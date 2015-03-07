<?php
/**
 * Part of the fuel-commentbox package.
 *
 * @package    fuel-commentbox
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2015 sharkpp
 * @link       https://github.com/sharkpp/fuel-commentbox
 */

namespace Commentbox;

class CommentboxException extends \FuelException {}

class Commentbox
{
	/**
	 * Commentbox class default config
	 * @var array
	 */
	protected static $_defaults = array();

	/**
	 * config
	 * @var array
	 */
	protected $config = array();

	/**
	 * Commentbox class config
	 * @var array
	 */
	protected $comment_key = '';

	/**
	 * Commentbox class config
	 * @var array
	 */
	protected $fieldset = null;

	/**
	 * Comment num
	 * @var array
	 */
	protected $comment_num = 0;

	/**
	 * Init
	 */
	public static function _init()
	{
		\Config::load('commentbox', true);
		\Lang::load('commentbox', true);
	}

	/**
	 * Commentbox driver forge.
	 *
	 * @param  string $comment_key comment key
	 * @param  array  $config      Config array
	 * @return Commentbox
	 */
	public static function forge($comment_key, $config = array())
	{
		$config = \Arr::merge(static::$_defaults, \Config::get('commentbox', array()), $config);

		$class = new static($comment_key, $config);

		return $class;
	}

	/**
	 * Commentbox driver forge.
	 *
	 * @param  array      $config Config array
	 * @return Commentbox
	 */
	public static function find($comment_key)
	{
		return new Commentbox();
	}

	/**
	 * Driver constructor
	 *
	 * @param string $comment_key comment key
	 * @param array  $config      driver config
	 */
	public function __construct($comment_key, array $config = array())
	{
		$this->comment_key = $comment_key;
		$this->config      = $config;
	}

	/**
	 * Get a config setting.
	 *
	 * @param  string $key the config key
	 * @param  mixed  $default the default value
	 * @return mixed           the config setting value
	 */
	public function get_config($key, $default = null)
	{
		return \Arr::get($this->config, $key, $default);
	}

	/**
	 * Set a config setting.
	 *
	 * @param  string $key   the config key
	 * @param  mixed  $value the new config value
	 * @return object        $this for chaining
	 */
	public function set_config($key, $value)
	{
		\Arr::set($this->config, $key, $value);

		return $this;
	}

	/**
	 * テンプレートの設定値を取得
	 *
	 * @param  string $key     設定値名
	 * @param  mixed  $default 初期値
	 * @param  mixed  $tags    置き換えタグ( array( 'xx' => 'yy', ... ) )
	 * @return 設定値
	 */
	protected function get_template($key, $default = null, $tags = array())
	{
		return $this->replace_tags(
					$this->get_config(
						$this->get_config('active', 'default') . '.' . $key,
						$default),
					$tags);
	}

	/**
	 * 文字列内のタグを置き換える
	 *
	 * @param  string $text    対象の文字列
	 * @param  mixed  $tags    置き換えタグ( array( 'xx' => 'yy', ... ) )
	 * @return 設定値
	 */
	protected function replace_tags($text, $tags = array())
	{
		$result = $text;

		foreach ($tags as $tag => $value)
		{
			$result = str_replace('{' . $tag . '}', $value, $result);
		}

		return $result;
	}

	protected function fieldset()
	{
		if ( ! $this->fieldset)
		{
			$use_recaptcha
				= $this->get_config('recaptcha.enable', false) &&
				  ($this->get_config('recaptcha.always_use', false) ||
				   ! \Auth::check());

			$this->fieldset = \Fieldset::forge('commentbox');
			$this->fieldset
				->add('comment_key', '')
				->add_rule('required');
			if ( ! \Auth::check())
			{
				$this->fieldset
					->add('name', __('commentbox.form.name'),
					      array('class' => 'form-control'))
					->add_rule('trim')
					->add_rule('max_length', 50);
				$this->fieldset
					->add('email', __('commentbox.form.email'),
					      array('type' => 'email', 'class' => 'form-control'))
					->add_rule('trim')
					->add_rule('valid_email')
					->add_rule('max_length', 255);
			}
			$this->fieldset
				->add('website', __('commentbox.form.website'),
				      array('type' => 'url', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('valid_url');
			$this->fieldset
				->add('body', __('commentbox.form.body'),
				      array('type' => 'textarea', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('required');
			$this->fieldset
				->add('submit', '', \Arr::merge(
				                        array('type'=>'submit', 'value' => __('commentbox.form.submit'),
				                              'class' => 'form-control'),
				                        $use_recaptcha
				                            ? array('disabled' => 'disabled')
				                            : array()));

			if ($use_recaptcha)
			{
				$this->fieldset
					->validation()
						->add_callable(Recaptcha::forge($this->get_config('recaptcha', array())));
				$this->fieldset
					->add('g-recaptcha-response', 'reCAPTCHA hash')
					->add_rule('recaptcha')
					->add_rule('required');
			}
		}

		return $this->fieldset;
	}

	protected function create_form($comment_key)
	{
		$form = $this->fieldset();

		$html = $this->get_template('form');

		foreach ($form->field() as $field)
		{
			$attributes = \Arr::merge(
				array('placeholder' => $field->label),
				$field->get_attribute());
			$html = str_replace(
						'{'.$field->name.'_field}',
						'textarea' == \Arr::get($field->get_attribute(), 'type')
							? \Form::textarea($field->name, '', $attributes)
							: \Form::input   ($field->name, '', $attributes),
						$html
					);
			
		}

		if (\Auth::check())
		{
			$html = str_replace('{name_field}',  '', $html);
			$html = str_replace('{email_field}', '', $html);
		}

		$html = str_replace('{comment_key}', $comment_key, $html);
		$html = str_replace('{submit}',
		                    \Form::submit($form->field('submit')->name,
		                                  $form->field('submit')->get_attribute('value'),
		                                  \Arr::merge($form->field('submit')->get_attribute(),
		                                              array('id' => 'commentbox_submit_' . $comment_key))),
		                    $html);
		$html = str_replace('{open}',
		                    \Form::open(array('method' => 'post',
		                                      'action' => \Uri::create(\Uri::string(), array(), \Input::get())))
		                    . \Form::csrf()
		                    . \Form::hidden('comment_key', $comment_key),
		                    $html);
		$html = str_replace('{close}',
		                    \Form::close(), $html);

		return $html;
	}

	/**
	* フォームとコメントツリーを取得
	*
	* @return string フォームとコメントツリーのHTML
	*/
	public function render()
	{
		$comments = $this->comments();
		return $this->get_template('commentbox', '', array(
					'form' => $this->form(),
					'comments' => $comments,
					'comment_num' =>
						__('commentbox.' .
						   (1 < $this->comment_num ? 'comments_num'
						                           : 'comment_num'),
						   array('num' => $this->comment_num))
				));
	}

	/**
	* フォームを取得
	*
	* @return string フォームのHTML
	*/
	public function form()
	{
		$authorized = \Auth::check();

		// ゲストの書き込み許可
		if (! $authorized &&
			! $this->get_config('guest', false))
		{
			return '';
		}

		$errors = '';
		if ($this->errors())
		{
			$error_template = $this->get_template('form_error_item');
	
			$errors = $this->get_template('form_errors_wrap');
			foreach ($this->errors() as $error)
			{
				$errors = str_replace('{errors}', str_replace('{error}', e($error), $error_template).'{errors}', $errors);
			}
			$errors = str_replace('{errors}', '', $errors);
		}

		$use_recaptcha
			= $this->get_config('recaptcha.enable', false) &&
			  ($this->get_config('recaptcha.always_use', false) ||
			   ! $authorized);

		$recaptcha_script = '';
		if ($use_recaptcha)
		{
			$recaptcha_script = $this->get_template('recaptcha_script');
			$recaptcha_script = str_replace('{recaptcha_site_key}', $this->get_config('recaptcha.site_key', ''), $recaptcha_script);
		}

		$html = $this->get_template('form_wrap');
		$html = str_replace('{recaptcha_script}', $recaptcha_script, $html);
		$html = str_replace('{form}', $this->create_form($this->comment_key), $html);
		$html = str_replace('{errors}', $errors, $html);
		
		return $html;
	}

	/**
	* コメントツリーを取得
	*
	* @return string コメントツリーのHTML
	*/
	public function comments()
	{
		// ゲストの書き込み許可
		$guest_comment = \Auth::check() ||
		                 $this->get_config('guest', false);

		$form = $this->fieldset();

		$root = Model_Commentbox::get_parent($this->comment_key);
		$tree = $root ? $root->dump_tree() : array();

		$user_page_empty
			= $this->replace_tags($this->get_config('user_page', ''), array(
					'user_id' => '',
					'user_name' => '',
				));

		$avatar = Avatar::forge($this->get_config('avatar', array()));

		$comments_tmpl = $this->get_template('comments');

		$depth = -1;

		$this->comment_num = 0;

		$tree2html = function($tree) use ($comments_tmpl, $user_page_empty,
		                                  &$depth, &$avatar, &$tree2html, $guest_comment) {
				$html = '';
				$depth++;
				foreach ($tree as $item)
				{
					$this->comment_num++;
					$u = self::get_user_info(
									$item['user_id'],
									array(
										'name' => $item['name'],
										'webpage' => '',
										'email' => $item['email']
									));
					$name       = empty($u['name']) ? __('commentbox.anonymous') : $u['name'];
					$avatar_img = $avatar->get_html($u['name'], $u['email'], array('class' => 'img-rounded'));
					$user_page  = $this->replace_tags($this->get_config('user_page', ''), array(
										'user_id' => -1 == $item['user_id'] ? '' : $item['user_id'],
										'user_name' => $u['name'],
									));
					$user_page  = $user_page == $user_page_empty ? '' : $user_page;

					$tmpl_name   = 0 < $depth ? 'comments_2nd' : 'comments';
					$tmpl_default= 0 < $depth ? $comments_tmpl : '';
					$html .= $this->get_template($tmpl_name, $tmpl_default, array(
							'body' => $item['body'],
							'name' => $name,
							'name_webpage' => $u['webpage'] ? \Html::anchor($u['webpage'], $name) : $name,
							'name_userpage' => $user_page ? \Html::anchor($user_page, $name) : $name,
							'name_email' => $u['email'] ? \Html::anchor('mailto:' . $u['email'], $name) : $name,
							'email' => $u['email'],
							'webpage' => $u['webpage'],
							'time' => \Date::time_ago($item['created_at']),
							'avatar' => $avatar_img,
							'avatar_webpage' => $u['webpage'] ? \Html::anchor($u['webpage'], $avatar_img) : $avatar_img,
							'avatar_userpage' => $user_page ? \Html::anchor($user_page, $avatar_img) : $avatar_img,
							'avatar_email' => $u['email'] ? \Html::anchor('mailto:' . $u['email'], $avatar_img) : $avatar_img,
							'reply_button' => ! $guest_comment ? ''
							                  : str_replace('{comment_key}', $item['comment_key'],
							                    str_replace('{reply_caption}', __('commentbox.reply'),
							                                $this->get_template('comment_reply_button'))),
							'reply_form' => ! $guest_comment 
							                ? '' : $this->create_form($item['comment_key']),
							'comment_key' => $item['comment_key'],
							'child' => $tree2html($item['children']),
						));
				}
				$depth--;
				return $html;
			};

		$html = '';

		foreach ($tree as $item)
		{
			$html .= $tree2html($item['children']);
		}

		$html = str_replace('{comments}', $html, $this->get_template('comments_wrap'));

		return $html;
	}

	/**
	* Get a config setting.
	*
	* @param string $key the config key
	* @param mixed  $default the default value
	* @return mixed the config setting value
	*/
	public function run($input = null)
	{
		$authorized = \Auth::check();

		// ゲストの書き込み許可
		if (! $authorized &&
			! $this->get_config('guest', false))
		{
			return '';
		}

		$form = $this->fieldset();

		$form->validation()->run($input);

		if ( ! $form->validation()->error())
		{
			try
			{
				\DB::start_transaction();

				// キーとなるハッシュを生成
				for ($comment_key = \Str::random('alnum', 32);
				     Model_Commentbox::query()
				     	->where('comment_key', $comment_key)
				     	->count();
				     $comment_key = \Str::random('alnum', 32))
					continue;

				$parent = Model_Commentbox::get_parent($form->validation()->validated('comment_key', $this->comment_key), true);

				$model = new Model_Commentbox();
				$model->from_array($form->validation()->validated());
				$model->comment_key = $comment_key;

				if ($authorized)
				{
					$model->name = '';
					$model->email = '';
					$model->user_id = (int)\Auth::get('id');
				}
				else
				{
					$model->user_id = -1;
				}

				$model->child($parent)->save();

				\DB::commit_transaction();
			}
			catch (\Exception $e)
			{
				// 未決のトランザクションクエリをロールバックする
				\DB::rollback_transaction();
				throw $e;
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	public function errors()
	{
		return $this->fieldset()->validation()->error();
	}

	protected static function get_user_info($user_id, $default)
	{
		$result = $default;
		$user = -1 == $user_id
				? null
				: \Auth\Model\Auth_User::query()
					->related('metadata')
					->where('id', $user_id)
					->where('metadata.key', 'fullname')
					->get_one();
		if ($user)
		{
			$result['name']
				= empty(current($user->metadata)->value)
					? $user->username
					: current($user->metadata)->value;
			$result['email'] = $user->email;
		}
		return $result;
	}

}
