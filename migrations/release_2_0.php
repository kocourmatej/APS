<?php

/**
*
* @package phpBB Extension - Advanced Profile System
* @copyright (c) 2015 posey - http://www.godfathertalks.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace posey\aps\migrations;

class release_2_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('config.add', array('fl_enabled', 1)),
			array('config.add', array('cp_enabled', 1)),
			array('config.add', array('af_enabled', 1)),
			array('config.add', array('cp_panel_id', 1)),
			
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_APS')),
			array('module.add', array(
				'acp', 'ACP_APS',	array(
					'module_basename'	=> '\posey\aps\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
			// Add permission
			array('permission.add', array('u_wall_post', true)),
			array('permission.add', array('u_wall_read', true)),
			array('permission.add', array('u_wall_del', true)),
			array('permission.add', array('m_wall_del', true)),
			// Set permissions
			array('permission.permission_set', array('ROLE_USER_FULL', 'u_wall_post')),
			array('permission.permission_set', array('ROLE_USER_FULL', 'u_wall_read')),
			array('permission.permission_set', array('ROLE_USER_FULL', 'u_wall_del')),
			array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_wall_post')),
			array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_wall_read')),
			array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_wall_del')),
			array('permission.permission_set', array('ROLE_MOD_FULL', 'm_wall_post')),
			array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_wall_post')),
		);
	}
}
