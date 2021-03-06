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

		\Config::set('language', \Input::get('lang', \Config::get('language')));

		\Config::load('commentbox', true);
		$config = array(
				'guest' => 'disable' != \Input::get('guest', \Config::get('commentbox.guest') ? 'enable' : 'disable'),
				'use_fullname' => 'no' != \Input::get('fullname', \Config::get('commentbox.use_fullname') ? 'yes' : 'no'),
				'use_delete' => 'false' != \Input::get('use_delete', \Config::get('commentbox.use_delete') ? 'true' : 'false'),
				'delete_without_trace' => 'false' != \Input::get('delete_without_trace', \Config::get('commentbox.delete_without_trace') ? 'true' : 'false'),
				'delete_descendants' => 'false' != \Input::get('delete_descendants', \Config::get('commentbox.delete_descendants') ? 'true' : 'false'),
				'delete_comment_avatar' => 'false' != \Input::get('delete_comment_avatar', \Config::get('commentbox.delete_comment_avatar') ? 'true' : 'false'),
				'active' => \Input::get('theme', \Config::get('commentbox.active')),
				'recaptcha' => array(
					'enable' => 'disable' != \Input::get('recaptcha', \Config::get('commentbox.recaptcha.enable') ? 'enable' : 'disable'),
					'always_use' => 'always' == \Input::get('recaptcha_always', \Config::get('commentbox.recaptcha.always_use') ? 'always' : 'none'),
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
					->set_safe('commentbox', $commentbox->render())
			);
	}
}
