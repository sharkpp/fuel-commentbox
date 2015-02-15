<?php

class Controller_Test extends Controller
{
	public function action_index($id = null)
	{
		if (!$id)
		{
			$id = 1;
		}

		$key = sprintf('index.'.$id);
		$commentbox = Commentbox::forge($key);

		if (Input::post())
		{
			if ($commentbox->run(Input::post()))
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
