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

	protected function fieldset()
	{
		if ( ! $this->fieldset)
		{
			$this->fieldset = \Fieldset::forge('commentbox');
			$this->fieldset
				->add('comment_key', '')
				->add_rule('match_value', $this->comment_key, true);
			$this->fieldset
				->add('name', '名前', array('class' => 'form-control'))
				->add_rule('trim')
				->add_rule('max_length', 50);
			$this->fieldset
				->add('email', 'メールアドレス', array('type' => 'email', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('valid_email')
				->add_rule('max_length', 255);
			$this->fieldset
				->add('website', 'ウェブサイト', array('type' => 'url', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('valid_url');
			$this->fieldset
				->add('body', '本文', array('type' => 'textarea', 'class' => 'form-control'))
				->add_rule('trim')
				->add_rule('required');
			$this->fieldset
				->add('submit', '', array('type'=>'submit', 'value' => '確認', 'class' => 'form-control'));
		}

		return $this->fieldset;
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
		$form = $this->fieldset();

		$template = <<<EOD
{open}
<div class="form-group">
	{body_field}
</div>
<div class="form-inline">
<div class="form-group">
	{name_field}
</div>
<div class="form-group">
	{email_field}
</div>
<div class="form-group">
	{website_field}
</div>
<div class="form-group">
	{submit}
</div>
</div>
{close}
EOD;

		$html = $template;

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

		$html = str_replace('{submit}',
		                    \Form::submit($form->field('submit')->name,
		                                  $form->field('submit')->get_attribute('value')),
		                    $html);
		$html = str_replace('{open}',
		                    \Form::open(array('method' => 'post'))
		                    . \Form::csrf()
		                    . \Form::hidden('comment_key', $this->comment_key),
		                    $html);
		$html = str_replace('{close}',
		                    \Form::close(), $html);

		return $html;
	}

	public function comments()
	{
		$form = $this->fieldset();

		$root = Model_Commentbox::get_root($this->comment_key);
		$tree = $root ? $root->dump_tree() : array();

		$template = <<<EOD
<hr>
<div class="media">
	<div class="media-left">
		<span class="media-object img-thumbnail" style="width: 64px; height: 64px; background-color: #eee"></span>
	</div>
	<div class="media-body">
		<h4 class="media-heading">{name} <small>{time}</small></h4>
		{body}
	{email}<br>
{child}
	</div>
</div>
EOD;

		$tree2html = function($tree) use ($template, &$tree2html) {
				$html = '';
				foreach ($tree as $item)
				{
					$tmp = $template;
					$tmp = str_replace('{body}', $item['body'], $tmp);
					$tmp = str_replace('{name}', empty($item['name']) ? '匿名' : $item['name'], $tmp);
					$tmp = str_replace('{email}', $item['email'], $tmp);
					$tmp = str_replace('{time}', \Date::time_ago($item['created_at']), $tmp);
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

				$root = Model_Commentbox::get_root($this->comment_key, true);

				$model = new Model_Commentbox();
				$model->from_array($form->validation()->validated());
				$model->comment_key = $comment_key;
				$model->user_id = -1;
				$model->child($root)->save();

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
		}

		return true;
	}

	public function error()
	{
		return $this->fieldset()->validation()->error();
	}

}
