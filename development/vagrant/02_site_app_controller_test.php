<?php

class Controller_Test extends Controller
{
	public function before()
	{
		parent::before();

		// CSRFチェック
		if (Input::method() === 'POST' &&
			!\Security::check_token())
		{
			\Session::set_flash('error', 'session expired!');
			\Response::redirect(\Uri::create(\Uri::string(), array(), \Input::get()));
		}
	}

	public function action_index($id = null)
	{
		return $this->action_core('index', $id);
	}

	public function action_sub1($id = null)
	{
		return $this->action_core('sub1', $id);
	}

	public function action_sub2($id = null)
	{
		return $this->action_core('sub2', $id);
	}

	protected function action_core($page, $id)
	{
		if (!$id)
		{
			$id = 1;
		}

		$validation_signin = \Validation::forge();
		if (\Auth::check())
		{
			$validation_signin
				->add('signout', 'signout')
				->add_rule('required');
		}
		else
		{
			$validation_signin
				->add('username', 'Your username')
				->add_rule('required');
			$validation_signin
				->add('password', 'Your password')
				->add_rule('required');
		}

		\Config::load('commentbox', true);
\Log::error(print_r(\Input::post(), true));\Log::error(print_r(\Input::get(), true));
\Log::error(print_r($_POST, true));\Log::error(print_r($_GET, true));
		$config = array(
				'guest' => 'disable' != \Input::get('guest', \Config::get('commentbox.guest') ? 'enable' : 'disable'),
				'recaptcha' => array(
					'enable' => 'disable' != \Input::get('recaptcha', \Config::get('commentbox.recaptcha.enable') ? 'enable' : 'disable'),
				),
				'avatar' => array(
					'service' => \Input::get('avatar', \Config::get('commentbox.avatar.service')),
				),
			);
		$key = sprintf($page.'.'.$id);
		$commentbox = Commentbox::forge($key, $config);

		if (\Input::post())
		{
			if ($validation_signin->run())
			{
				if (\Auth::check())
				{
					\Auth::logout();
					\Response::redirect(\Uri::create(\Uri::string(), array(), \Input::get()));
				}
				else if (\Auth::login($validation_signin->validated('username'),
					                  $validation_signin->validated('password')))
				{
					\Response::redirect(\Uri::create(\Uri::string(), array(), \Input::get()));
				}
				else
				{
					\Session::set_flash('error', 'username or password mismatch');
				}
			}
			else if ($commentbox->run(\Input::post()))
			{
				\Response::redirect(\Uri::create(\Uri::string(), array(), \Input::get()));
			}
			else
			{
			}
		}

		return
			\Response::forge(
				\View::forge('test/index')
					->set_safe('commentbox_form', $commentbox->form())
					->set_safe('commentbox_comments', $commentbox->comments())
			);
	}
}
