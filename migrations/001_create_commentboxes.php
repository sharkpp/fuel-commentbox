<?php

namespace Fuel\Migrations;

class Create_commentboxes
{
	public function up()
	{
		\Config::load('commentbox', true);

		$table = \Config::get('commentbox.table_name', 'commentboxes');

		\DBUtil::create_table($table, array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'left_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'right_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'tree_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'comment_key' => array('constraint' => 50, 'type' => 'varchar'),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'email' => array('constraint' => 255, 'type' => 'varchar'),
			'website' => array('type' => 'text'),
			'body' => array('type' => 'text'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
		), array('id'));

		\DB::query('ALTER TABLE `commentboxes` ADD KEY(`left_id`)')->execute();
		\DB::query('ALTER TABLE `commentboxes` ADD KEY(`right_id`)')->execute();
	}

	public function down()
	{
		\Config::load('commentbox', true);

		$table = \Config::get('commentbox.table_name', 'commentboxes');

		\DBUtil::drop_table($table);
	}
}