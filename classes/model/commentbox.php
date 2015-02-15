<?php

namespace Commentbox;

class Model_Commentbox extends \Orm\Model_Nestedset
{
	protected static $_properties = array(
		'id',
		'left_id',
		'right_id',
		'comment_key',
		'user_id',
		'name',
		'email',
		'website',
		'body',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_tree = array(
		'title_field' => 'comment_key',
	);

	protected static $_table_name = 'commentboxes';

	public static function get_root($comment_key, $create = false)
	{
		$root = Model_Commentbox::query()
					->where('comment_key', $comment_key)
					->get_one();

		if (null != $root ||
			! $create)
		{
			return $root;
		}

		$root = new Model_Commentbox();
		$root->comment_key = $comment_key;
		$root->user_id = -1;
		$root->name = '';
		$root->email = '';
		$root->website = '';
		$root->body = '';
		$root->save();

		return $root;
	}
}
