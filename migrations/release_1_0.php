<?php

/**
*
* @package phpBB Extension - Advanced Profile System
* @copyright (c) 2015 posey - http://www.godfathertalks.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace posey\aps\migrations;

class release_1_0 extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_columns'        => array(
				$this->table_prefix . 'users'        => array(
					'user_profile_views'    => array('UINT', 0, 'after' => 'user_from'),
					'user_coverphoto'		=> array('VCHAR', '', 'after' => 'user_from'),
				),
			),
			'add_tables'		=> array(
				$this->table_prefix . 'wall'	=> array(
					'COLUMNS'			=> array(
						'msg_id'			=> array('UINT', NULL, 'auto_increment'),
						'user_id'			=> array('UINT', NULL),
						'poster_id'			=> array('UINT', NULL),
						'msg'				=> array('MTEXT_UNI', ''),
						'msg_time'			=> array('INT:11', NULL),
						'bbcode_bitfield' 	=> array('VCHAR', ''),
						'bbcode_uid' 		=> array('VCHAR:8', ''),
						'bbcode_options' 	=> array('USINT', NULL),
					),
					'PRIMARY_KEY'		=> 'msg_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'        => array(
				$this->table_prefix . 'users'        => array(
					'user_profile_views',
					'user_coverphoto',
				),
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'wall',
			),
		);
	}
}
