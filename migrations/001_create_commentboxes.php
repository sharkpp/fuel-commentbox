<?php

namespace Fuel\Migrations;

class Create_commentboxes
{
	public function up()
	{
		\DBUtil::create_table('commentboxes', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'left_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'right_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'comment_key' => array('constraint' => 50, 'type' => 'varchar'),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'email' => array('constraint' => 255, 'type' => 'varchar'),
			'website' => array('type' => 'text'),
			'body' => array('type' => 'text'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('commentboxes');
	}
}