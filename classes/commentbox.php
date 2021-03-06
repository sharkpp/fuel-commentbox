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

		$tags = array();

		foreach ($form->field() as $field)
		{
			$attributes = \Arr::merge(
				array('placeholder' => $field->label),
				$field->get_attribute());
			$tags[$field->name . '_field'] =
				'textarea' == \Arr::get($field->get_attribute(), 'type')
					? \Form::textarea($field->name, '', $attributes)
					: \Form::input   ($field->name, '', $attributes);
			
		}

		if (\Auth::check())
		{
			$tags['name_field'] = '';
			$tags['email_field'] = '';
		}

		$tags['comment_key'] = $comment_key;
		$tags['submit'] =   \Form::submit($form->field('submit')->name,
		                                  $form->field('submit')->get_attribute('value'),
		                                  \Arr::merge($form->field('submit')->get_attribute(),
		                                              array('id' => 'commentbox_submit_' . $comment_key)));
		$tags['open'] =     \Form::open(array('method' => 'post',
		                                      'action' => \Uri::create(\Uri::string(), array(), \Input::get())))
		                    . \Form::csrf()
		                    . \Form::hidden('action', 'post')
		                    . \Form::hidden('comment_key', $comment_key);
		$tags['close'] =    \Form::close();

		return $this->get_template('form', '', $tags);
	}

	/**
	 * Get the form and comments tree
	 *
	 * @return string HTML of forms and comments tree
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
	* Get form
	*
	* @return string HTML of form
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

		// reCAPTCHA を使用するか？
		$use_recaptcha
			= $this->get_config('recaptcha.enable', false) &&
			  ($this->get_config('recaptcha.always_use', false) ||
			   ! $authorized);

		// reCAPTCHA 用のスクリプトコードを取得
		$recaptcha_script =
			!$use_recaptcha
				? ''
				: $this->get_template('recaptcha_script', '', array(
						'recaptcha_site_key' => $this->get_config('recaptcha.site_key', '')
					));

		$html = $this->get_template('form_wrap', '', array(
					'recaptcha_script' => $recaptcha_script,
					'form' => $this->create_form($this->comment_key),
					'errors' => $errors
				));
		
		return $html;
	}

	/**
	* Get comment tree
	*
	* @return string HTML of comment tree
	*/
	public function comments()
	{
		$authorized = \Auth::check();

		// ゲストの書き込み許可
		$guest_comment = $authorized ||
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
		$avatar_deleted = Avatar::forge(
							\Arr::merge($this->get_config('avatar', array()),
							            array('service' => 'none' == $this->get_config('avatar.service') ? 'none' : 'blank'))
							);

		$comments_tmpl = $this->get_template('comments');

		$user_id = $authorized ? (int)\Auth::get('id') : -2;

		$use_delete            = $this->get_config('use_delete',            true);
		$delete_without_trace  = $this->get_config('delete_without_trace',  false);
		$delete_descendants    = $this->get_config('delete_descendants',    false);
		$delete_comment_avatar = $this->get_config('delete_comment_avatar', false);

		$depth = -1;

		$this->comment_num = 0;

		$tree2html = function($tree) use ($comments_tmpl, $user_page_empty, $authorized, $user_id,
		                                  $use_delete, $delete_without_trace, $delete_descendants, $delete_comment_avatar,
		                                  &$depth, &$avatar, &$avatar_deleted, &$tree2html, $guest_comment) {
				$html = '';
				$depth++;
				foreach ($tree as $item)
				{
					$deleted = null != $item['deleted_at'];
					$delete_with_avatar = $deleted && $delete_comment_avatar;

					if ($deleted && $delete_without_trace)
					{
						continue;
					}

					$this->comment_num++;
					$u = $this->get_user_info(
									$item['user_id'],
									array(
										'name' => $item['name'],
										'webpage' => '',
										'email' => $item['email']
									));
					$name       = $delete_with_avatar
									? '' :
									empty($u['name']) ? __('commentbox.anonymous') : $u['name'];
					$avatar_img = $delete_with_avatar
									? $avatar_deleted->get_html($u['name'], $u['email'], array('class' => 'img-rounded'))
									: $avatar        ->get_html($u['name'], $u['email'], array('class' => 'img-rounded'));
					$user_page  = $this->replace_tags($this->get_config('user_page', ''), array(
										'user_id' => -1 == $item['user_id'] ? '' : $item['user_id'],
										'user_name' => $u['name'],
									));
					$user_page  = $user_page == $user_page_empty ? '' : $user_page;
					$has_delete_perm = ! $authorized || ! $use_delete
											? false
											: $item['user_id'] == $user_id;

					$tmpl_name   = 0 < $depth ? 'comments_2nd' : 'comments';
					$tmpl_default= 0 < $depth ? $comments_tmpl : '';
					$html .= $this->get_template($tmpl_name, $tmpl_default, array(
							'body' => $deleted
								? $this->get_template('deleted_message', '', array('message' => __('commentbox.has_been_deleted')))
								: $item['body'],
							'name' => $name,
							'name_webpage' => $u['webpage'] ? \Html::anchor($u['webpage'], $name) : $name,
							'name_userpage' => $user_page ? \Html::anchor($user_page, $name) : $name,
							'name_email' => $u['email'] ? \Html::anchor('mailto:' . $u['email'], $name) : $name,
							'email' => $u['email'],
							'webpage' => $u['webpage'],
							'time' => $delete_with_avatar ? '' : \Date::time_ago($item['created_at']),
							'avatar' => $avatar_img,
							'avatar_webpage' => $u['webpage'] ? \Html::anchor($u['webpage'], $avatar_img) : $avatar_img,
							'avatar_userpage' => $user_page ? \Html::anchor($user_page, $avatar_img) : $avatar_img,
							'avatar_email' => $u['email'] ? \Html::anchor('mailto:' . $u['email'], $avatar_img) : $avatar_img,
							'reply_button' => $deleted || ! $guest_comment ? ''
							                  : $this->get_template('comment_reply_button', '', array(
							                        'comment_key' => $item['comment_key'],
							                        'reply_caption' => __('commentbox.reply'),
							                    )),
							'reply_form' => $deleted || ! $guest_comment 
							                ? '' : $this->create_form($item['comment_key']),
							'delete_button' => ! $has_delete_perm || $deleted || ! $guest_comment ? ''
							                   : $this->get_template('comment_delete_button', '', array(
							                         'comment_key' => $item['comment_key'],
							                         'delete_caption' => __('commentbox.delete'),
							                         'delete_message' => __('commentbox.delete_message'),
							                         'delete_form' => 
							    \Form::open(array('method' => 'post',
							                      'id' => 'commentbox_delete_form_' . $item['comment_key'],
							                      'action' => \Uri::create(\Uri::string(), array(), \Input::get()))) .
							    \Form::csrf() .
							    \Form::hidden('action', 'delete') .
							    \Form::hidden('comment_key', $item['comment_key']) .
							    \Form::submit('send', '', array('style' => 'display: none;')) .
							    \Form::close(),
							                     )),
							'comment_key' => $item['comment_key'],
							'child' => $deleted && $delete_descendants
											? ''
											: $tree2html($item['children']),
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
		$val = $form->validation();

		$delete_action = 'delete' == \Arr::get(\Arr::merge(\Input::post(), $input?$input:array()), 'action');

		// 削除アクション？
		if ($delete_action)
		{
			$val = \Validation::forge('commentbox-delete');
			$val->add('comment_key', '')
				->add_rule('required');
		}

		$val->run($input);

		if ( ! $val->error())
		{
			try
			{
				\DB::start_transaction();

				if ($delete_action)
				{ // 削除処理
					if (null != ($model = Model_Commentbox::get_item($val->validated('comment_key'))))
					{
						$has_delete_perm
							= ! $authorized || ! $this->get_config('use_delete')
								? false
								: $model->user_id == (int)\Auth::get('id');
						if ($has_delete_perm)
						{
							$model->deleted_at = time();
							$model->save();
						}
					}
				}
				else
				{ // 投稿処理
					// キーとなるハッシュを生成
					for ($comment_key = \Str::random('alnum', 32);
					     Model_Commentbox::query()
					     	->where('comment_key', $comment_key)
					     	->count();
					     $comment_key = \Str::random('alnum', 32))
						continue;
	
					// 親を捜す＆無ければ作成
					$parent = Model_Commentbox::get_parent($val->validated('comment_key', $this->comment_key), true);
	
					$model = new Model_Commentbox();
					$model->from_array($val->validated());
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
				}

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

	protected function get_user_info($user_id, $default)
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
			// Auth パッケージから名称を取得するときに fullname を使用するか？
			$use_fullname = $this->get_config('use_fullname', true);

			$result['name']  = $user->username;
			$result['email'] = $user->email;

			foreach ($user->metadata as $metadata)
			{
				switch ($metadata->key)
				{
				case 'fullname':
					if ($use_fullname &&
						!empty($metadata->value))
					{
						$result['name'] = $metadata->value;
					}
					break;
				}
			}
		}

		return $result;
	}

}
