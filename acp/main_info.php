<?php

/**
*
* @package phpBB Extension - Advanced Profile System
* @copyright (c) 2015 posey - http://www.godfathertalks.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace posey\aps\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\posey\aps\acp\main_module',
			'title'		=> 'ACP_APS',
			'modes'		=> array(
				'settings' => array(
					'title'	=> 'ACP_SETTINGS', 
					'auth'	=> 'ext_posey/aps && acl_a_board', 
					'cat'	=> array('ACP_APS')),
			),
		);
	}
}