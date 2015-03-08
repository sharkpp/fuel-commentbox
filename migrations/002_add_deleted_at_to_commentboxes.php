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

namespace Fuel\Migrations;

class Add_deleted_at_to_commentboxes
{
	public function up()
	{
		\DBUtil::add_fields('commentboxes', array(
			'deleted_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('commentboxes', array(
			'deleted_at'

		));
	}
}