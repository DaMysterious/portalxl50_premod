<?php
/**
*
* @package install
* @version $Id: install_uninstall.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/
if (!defined('IN_INSTALL') || !defined('IN_PHPBB'))
{
	exit;
}

if (!empty($setmodules))
{
	if (!$this->installed_version)
	{
		return;
	}

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'UNINSTALL',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 30,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'UNINSTALL'),
		'module_reqs'		=> ''
	);
}

/**
* Installation
* @package install
*/
class install_uninstall extends module
{
	function install_uninstall(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $user, $template, $phpbb_root_path;

		switch ($sub)
		{
			case 'intro':
				$this->page_title = $user->lang['SUB_INTRO'];

				$template->assign_vars(array(
					'TITLE'			=> $user->lang['UNINSTALL_INTRO'],
					'BODY'			=> $user->lang['UNINSTALL_INTRO_BODY'],
					'L_SUBMIT'		=> $user->lang['UNINSTALL_START'],
					'U_ACTION'		=> $this->p_master->module_url . "?mode=$mode&amp;sub=uninstall",
				));

			break;

			case 'uninstall':
				$this->uninstall($mode, $sub);

			break;
		}

		$this->tpl_name = 'install_install';
	}

	/**
	* Obtain the information required to connect to the database
	*/
	function uninstall($mode, $sub)
	{
		global $user, $template, $cache, $phpEx, $phpbb_root_path, $phpbb_db_tools, $db, $arcade_mod_config;

		$this->page_title = $user->lang['STAGE_UNINSTALL_ARCADE'];

		// Remove all the arcade tables.
		foreach ($arcade_mod_config['verify']['tables'] as $table_name)
		{
			$phpbb_db_tools->sql_table_drop($table_name);
		}

		if (!empty($arcade_mod_config['data_file']['remove']))
		{
			$this->p_master->load_data($arcade_mod_config['data_file']['remove']);
		}

		if (!empty($arcade_mod_config['schema_changes']['remove']))
		{
			// Remove any changes made to existing tables
			$phpbb_db_tools->perform_schema_changes($arcade_mod_config['schema_changes']['remove']);
		}

		// Remove all permission options
		$this->p_master->remove_permissions($arcade_mod_config['permission_options']['phpbb']);

		// Remove modules
		$this->p_master->remove_modules($arcade_mod_config['modules_remove']);

		// Purge the cache
		$db->sql_return_on_error(true);
		$this->p_master->cache_purge(array('auth', 'imageset', 'theme', 'template', ''));
		$db->sql_return_on_error(false);

		add_log('admin', 'LOG_ARCADE_UNINSTALL', $arcade_mod_config['version']['current']);

		$template->assign_vars(array(
			'BODY'		=> $user->lang['STAGE_UNINSTALL_ARCADE_EXPLAIN'] . '<br /><br />' . sprintf($user->lang['UNINSTALL_CONGRATS_EXPLAIN'], $arcade_mod_config['version']['current']),
			'L_SUBMIT'	=> $user->lang['INSTALL_LOGIN'],
			'U_ACTION'	=> append_sid("{$phpbb_root_path}adm/index.$phpEx", false, true, $user->session_id),
		));
	}
}

?>