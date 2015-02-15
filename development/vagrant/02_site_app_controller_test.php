<?php

class Controller_Test extends Controller
{
	public function action_index()
	{
	//	Comments::forge();

		$commentbox_form = '<b>aaa</b>';

		return
			Response::forge(
				View::forge('test/index')
					->set_safe('commentbox_form', $commentbox_form)
			);
	}
}
