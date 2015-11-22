<?php

/**
*
* @package phpBB Extension - Advanced Profile System
* @copyright (c) 2015 posey - http://www.godfathertalks.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace posey\aps\acp;

class main_module
{
	var $u_action;
	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		$user->add_lang('acp/common');
		$this->tpl_name = 'aps_body';
		$this->page_title = $user->lang('ACP_APS');
		add_form_key('posey/aps/acp_key');
		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('posey/aps/acp_key'))
			{
				trigger_error('FORM_INVALID');
			}
			$config->set('fl_enabled', $request->variable('fl_enabled', 1));
			$config->set('cp_enabled', $request->variable('cp_enabled', 1));
			$config->set('af_enabled', $request->variable('af_enabled', 1));
			$config->set('cp_panel_id', $request->variable('cp_panel_id', 1));
			trigger_error($user->lang('ACP_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}
		$template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,
			'FL_ENABLED'		=> $config['fl_enabled'],
			'CP_ENABLED'		=> $config['cp_enabled'],
			'AF_ENABLED'		=> $config['af_enabled'],
			'CP_panel_ID'		=> $config['cp_panel_id'],
		));
	}
}