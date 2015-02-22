<?php

namespace Commentbox;

class CommentboxException extends \FuelException {}

class Commentbox
{
	/**
	 * Default config
	 * @var array
	 */
	protected static $_defaults = array();

	/**
	 * config
	 * @var array
	 */
	protected $config = array();

	/**
	 * Driver config
	 * @var array
	 */
	protected $comment_key = '';

	/**
	 * Driver config
	 * @var array
	 */
	protected $fieldset = null;

	/**
	 * Init
	 */
	public static function _init()
	{
		\Config::load('commentbox', true);
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
	 * Commentbox driver forge.
	 *
	 * @param	array			$config		Config array
	 * @return  Commentbox
	 */
	public static function find($comment_key)
	{
		return new Commentbox();
	}

	protected function get_template($name)
	{
		$active_template = \Config::get('commentbox.active', 'default');

		return \Config::get('commentbox.'.$active_template.'.'.$name, '');
	}

	protected function fieldset()
	{
		if ( ! $this->fieldset)
		{
			$this->fieldset = \Fieldset::forge('commentbox');
			$this->fieldset
				->add('comment_key', '')
				->add_rule('required');
			if ( ! \Auth::check())
			{
				$this->fieldset
					->add('name', '名前', array('class' => 'form-control'))
					->add_rule('trim')
					->add_rule('max_length', 50);
				$this->fieldset
					->add('email', 'メールアドレス', array('type' => 'email', 'class' => 'form-control'))
					->add_rule('trim')
					->add_rule('valid_email')
					->add_rule('max_length', 255);
			}
			$this->fieldset
				->add('website', 'ウェブサイト', array('type' => 'url', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('valid_url');
			$this->fieldset
				->add('body', '本文', array('type' => 'textarea', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('required')
				->add_rule('required');
			$this->fieldset
				->add('submit', '', array('type'=>'submit', 'value' => '送信', 'class' => 'form-control'));
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

		$html = str_replace('{submit}',
		                    \Form::submit($form->field('submit')->name,
		                                  $form->field('submit')->get_attribute('value')),
		                    $html);
		$html = str_replace('{open}',
		                    \Form::open(array('method' => 'post'))
		                    . \Form::csrf()
		                    . \Form::hidden('comment_key', $comment_key),
		                    $html);
		$html = str_replace('{close}',
		                    \Form::close(), $html);

		return $html;
	}

	/**
	* Get a config setting.
	*
	* @param string $key the config key
	* @param mixed  $default the default value
	* @return mixed the config setting value
	*/
	public function form()
	{
		return $this->create_form($this->comment_key);
	}

	public function comments()
	{
		$form = $this->fieldset();

		$root = Model_Commentbox::get_parent($this->comment_key);
		$tree = $root ? $root->dump_tree() : array();

		$template = $this->get_template('comments');

		$tree2html = function($tree) use ($template, &$tree2html) {
				$html = '';
				foreach ($tree as $item)
				{
					$user_info = self::get_user_info(
									$item['user_id'],
									array(
										'name' => $item['name'],
										'email' => $item['email']
									));
					$tmp = $template;
					$tmp = str_replace('{body}', $item['body'], $tmp);
					$tmp = str_replace('{name}', empty($user_info['name']) ? '匿名' : $user_info['name'], $tmp);
					$tmp = str_replace('{email}', $user_info['email'], $tmp);
					$tmp = str_replace('{time}', \Date::time_ago($item['created_at']), $tmp);
					$tmp = str_replace('{icon}', self::gravatar($user_info['email'], array('class' => 'img-rounded'), array('size' => 48)), $tmp);
					$tmp = str_replace('{reply_toggle}', '<a href="#" onclick="$(this).next().toggleClass(\'hidden\');return false;">Reply</a>', $tmp);
					$tmp = str_replace('{reply_form}', $this->create_form($item['comment_key']), $tmp);
					$tmp = str_replace('{child}', $tree2html($item['children']), $tmp);
					$html .= $tmp;
				}
				return $html;
			};

		$html = '';

		foreach ($tree as $item)
		{
			$html .= $tree2html($item['children']);
		}

		return $html;
		return '<pre>'.print_r($root ? $root->dump_tree() : false, true).'</pre>';
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
		$form = $this->fieldset();

		$form->validation()->run($input);
\Log::error(print_r($input,true));
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
\Log::error(print_r($parent,true));
				$model = new Model_Commentbox();
				$model->from_array($form->validation()->validated());
				$model->comment_key = $comment_key;

				if (\Auth::check())
				{
					$model->name = '';
					$model->email = '';
					$model->user_id = (int)\Auth::get('id');
				}
				else
				{
					$model->user_id = -1;
				}
\Log::error(print_r($model,true));
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

	public function error()
	{
		return $this->fieldset()->validation()->error();
	}

	// GravatarのイメージURLを取得
	protected static function gravatar($email, Array $attr = array(), Array $options = array())
	{
		// http://ja.gravatar.com/site/implement/hash/
		$hash = md5(strtolower(trim($email)));

		$type = \Arr::get($options, 'type', '');
		$type = $type ? '.' . $type : $type;
		\Arr::delete($options, 'type');

		if (\Arr::get($options, 'size'))
		{
			$attr['width']  = \Arr::get($options, 'size');
			$attr['height'] = \Arr::get($options, 'size');
		}

		$query= http_build_query($options);
		$query= $query ? '?' . $query : $query;
		$url  = strtolower(\Input::protocol()) . '://www.gravatar.com/avatar/' . $hash . $type . $query;
		return \Html::img($url, $attr);
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
