<?php

namespace Commentbox;

class Avatar
{
	/**
	 * Avatar class defualt config
	 * @var array
	 */
	protected static $_defaults = array();

	/**
	 * config
	 * @var array
	 */
	protected $config = array();

	/**
	 * Init
	 */
	public static function _init()
	{
	}

	/**
	 * Avatar class forge.
	 *
	 * @param  array  $config Config array
	 * @return Commentbox
	 */
	public static function forge($config = array())
	{
		$config = \Arr::merge(static::$_defaults, \Config::get('commentbox.avatar', array()), $config);

		$class = new static($config);

		return $class;
	}

	/**
	 * Avatar class constructor
	 *
	 * @param array  $config driver config
	 */
	public function __construct(array $config = array())
	{
		$this->config = $config;
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

	protected static function array_filter_null($arr)
	{
		return array_filter($arr, 
					function($v){ return !is_null($v); }
				);
	}

	// get html tag with Gravatar
	protected function gravatar($email, Array $attr = array(), Array $options = array())
	{
		// http://ja.gravatar.com/site/implement/hash/
		$hash = md5(strtolower(trim($email)));

		$type = \Arr::get($options, 'type', '');
		$type = $type ? '.' . $type : $type;
		\Arr::delete($options, 'type');

		$size = \Arr::get($options, 's', \Arr::get($options, 'size'));
		if ($size)
		{
			$attr['width']  = $size;
			$attr['height'] = $size;
		}

		$query= http_build_query($options);
		$query= $query ? '?' . $query : $query;
		$url  = strtolower(\Input::protocol()) . '://www.gravatar.com/avatar/' . $hash . $type . $query;
		return \Html::img($url, $attr);
	}

	// get html tag with robohash
	protected function robohash($email, Array $attr = array(), Array $options = array())
	{
		$hash = md5(strtolower(trim($email)));

		$type = \Arr::get($options, 'ext', '');
		$type = $type ? '.' . $type : $type;
		\Arr::delete($options, 'ext');

		$size = \Arr::get($options, 'size');
		if ($size)
		{
			$attr['width']  = (int)$size;
			$attr['height'] = (int)$size;
			\Arr::set($options, 'size', "${size}x${size}");
		}

		$query= http_build_query($options);
		$query= $query ? '?' . $query : $query;
		$url  = strtolower(\Input::protocol()) . '://robohash.org/' . $hash . $type . $query;
		return \Html::img($url, $attr);
	}

	// get html tag with Adorable Avatars
	protected function adorable($email, Array $attr = array(), Array $options = array())
	{
		$hash = md5(strtolower(trim($email)));

		$size = \Arr::get($options, 'size');
		if ($size)
		{
			$attr['width']  = (int)$size;
			$attr['height'] = (int)$size;
		}
		\Arr::delete($options, 'size');

		$url  = strtolower(\Input::protocol()) . '://api.adorable.io/avatars/' . $size . '/' . $hash;
		return \Html::img($url, $attr);
	}

	public function get_html($username, $email, Array $attr = array())
	{
		switch ($this->get_config('service'))
		{

		case 'none';
			return '';

		case 'gravatar':
			return $this->gravatar(
						$email, $attr,
						self::array_filter_null(\Arr::merge(
							array( 'size' => $this->get_config('size', 48) ),
							$this->get_config('gravatar', array()))
						));

		case 'robohash':
			return $this->robohash(
						$email, $attr,
						self::array_filter_null(\Arr::merge(
							array( 'size' => $this->get_config('size', 48) ),
							$this->get_config('robohash', array()))
						));

		case 'adorable':
			return $this->adorable(
						$email, $attr,
						self::array_filter_null(\Arr::merge(
							array( 'size' => $this->get_config('size', 48) ),
							$this->get_config('adorable', array()))
						));

		}

		return html_tag('span',
		                \Arr::merge($attr,
		                            array('style' =>
		                                     'width: 64px; ' .
		                                     'height: 64px; ' .
		                                     'background-color: #eee; ' .
		                                     'display: block;')),
		                            '');
	}

}
