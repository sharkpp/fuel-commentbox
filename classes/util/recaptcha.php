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

class Recaptcha
{
	/**
	 * reCAPTCHA class defualt config
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
	 * reCAPTCHA class forge.
	 *
	 * @param  array  $config Config array
	 * @return Commentbox
	 */
	public static function forge($config = array())
	{
		$config = \Arr::merge(static::$_defaults, \Config::get('commentbox.recaptcha', array()), $config);

		$class = new static($config);

		return $class;
	}

	/**
	 * reCAPTCHA class constructor
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

	public static function js()
	{
	}

	public function verify($response)
	{
		$this->last_error = false;

		$curl = \Request::forge('https://www.google.com/recaptcha/api/siteverify', 'curl');
		$curl->set_params(array(
				'secret' => $this->get_config('secret_key', ''),
				'response' => $response
			));
		$curl->execute();
		$result = $curl->response();

		$result = $result ? \Format::forge($result->body(), 'json')->to_array()
		                  : array();

		if (\Arr::get($result, 'success', false))
		{
			return true;
		}

		$this->last_error = implode(PHP_EOL, \Arr::get($result, 'error-codes', array()));

		return false;
	}

	public function last_error()
	{
		return $this->last_error;
	}

	public function _validation_recaptcha($val, $options = array())
	{
		$result = $this->verify($val);
		\Validation::active()
			->active_field()
			->set_error_message('recaptcha', $this->last_error());
		return $result;
	}
}
