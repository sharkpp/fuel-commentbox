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
			return Response::redirect(Uri::current());
		}
	}

	public function action_index($id = null)
	{
		if (!$id)
		{
			$id = 1;
		}

		$validation_signin = Validation::forge();
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

		$key = sprintf('index.'.$id);
		$commentbox = Commentbox::forge($key);

		if (Input::post())
		{
			if ($validation_signin->run())
			{
				if (\Auth::check())
				{
					\Auth::logout();
					Response::redirect(Uri::current());
				}
				else if (\Auth::login($validation_signin->validated('username'),
					                  $validation_signin->validated('password')))
				{
					Response::redirect(Uri::current());
				}
				else
				{
					\Session::set_flash('error', 'username or password mismatch');
				}
			}
			else if ($commentbox->run(Input::post()))
			{
				Response::redirect(Uri::current());
			}
			else
			{
			}
		}

//		$commentbox_form = $comment->;

		return
			Response::forge(
				View::forge('test/index')
					->set_safe('commentbox_form', $commentbox->form())
					->set_safe('commentbox_error', sprintf('<ul><li>%s</li></ul>', implode('</li><li>', $commentbox->error())))
					->set_safe('commentbox_comments', $commentbox->comments())
			);
	}
}
