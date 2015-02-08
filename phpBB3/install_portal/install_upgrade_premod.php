<?php
/**
*
* @package install_upgrade_premod.php
* @package Modification Installer for phpBB3 Portal XL
* @version $Id: install_upgrade_premod.php,v 1.2 2009/10/20 portalxl group Exp $
*
* @copyright (c) 2007, 2013 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
* @some code borrowed from phpBB's installer
* @copyright (c) 2005 phpBB Group
*/

/**
*/
if (!defined('IN_INSTALL'))
{
	// Someone has tried to access the file direct. This is not a good idea, so exit
	exit;
}

if (!empty($setmodules))
{
	// If phpBB is already installed we do not include this module
	if (@file_exists($phpbb_root_path . 'config.' . $phpEx) && !file_exists($phpbb_root_path . 'cache/install_lock'))
	{
		include_once($phpbb_root_path . 'config.' . $phpEx);

		if (!defined('PORTAL'))
		{
			return;
		}
	}

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'UPGRADE_PREMOD',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 20,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'UPDATE_DB', 'INSERT_MODULES', 'FINAL', 'ADDITIONAL'),
		'module_reqs'		=> ''
	);
}

/**
* Class holding all specific details.
* @package install
*/
class upgrade_premod
{
	var $cur_release = 'Premod';

	var $p_master;

	function upgrade_premod(&$p_master)
	{
		$this->p_master = &$p_master;
	}
}

/**
* Convert class for conversions
* @package install
*/
class install_upgrade_premod extends module
{
	/**
	* Variables used while converting, they are accessible from the global variable $convert
	*/
	function install_upgrade_premod(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $lang, $template, $phpbb_root_path, $phpEx, $cache, $config, $language, $table_prefix;

		$this->mode = $mode;

		$upgrade_premod = new upgrade_premod($this->p_master);

		switch ($sub)
		{
			case 'intro':
				$this->page_title = $lang['PORTAL_UPGRADE'];

				// Try opening config file
				// @todo If phpBB is not installed, we need to do a cut-down installation here
				// For now, we redirect to the installation script instead
				if (@file_exists($phpbb_root_path . 'config.' . $phpEx))
				{
					include($phpbb_root_path . 'config.' . $phpEx);
				}

				if (!defined('PHPBB_INSTALLED'))
				{
					$template->assign_vars(array(
						'S_NOT_INSTALLED'		=> true,
						'TITLE'					=> $lang['BOARD_NOT_INSTALLED'],
						'BODY'					=> sprintf($lang['BOARD_NOT_INSTALLED_EXPLAIN'], append_sid($phpbb_root_path . 'install_portal/index.' . $phpEx, 'mode=install&amp;language=' . $language)),
						
						'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
					));

					return;
				}
				
				if (!defined('PORTAL'))
				{
					$template->assign_vars(array(
						'S_NOT_INSTALLED'		=> true,
						'TITLE'					=> $lang['PORTAL_NOT_INSTALLED'],
						'BODY'					=> sprintf($lang['PORTAL_UPGRADE_NOT_INSTALLED_EXPLAIN'], append_sid($phpbb_root_path . 'install_portal/index.' . $phpEx, 'mode=install&amp;language=' . $language)),
						
						'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
					));

					return;
				}

				require($phpbb_root_path . 'config.' . $phpEx);
				require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
		
				$db = new $sql_db();
				$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, true);
				unset($dbpasswd);

				if (!defined('PORTAL_CONFIG_TABLE'))
				{
					require($phpbb_root_path . 'includes/constants.' . $phpEx);
				}

				$sql = 'SELECT config_value
					FROM ' . PORTAL_CONFIG_TABLE . "
					WHERE config_name = 'portal_version'";
				$result = $db->sql_query($sql);
		
				while ($row = $db->sql_fetchrow($result))
				{
					$portal_version = $row['config_value'];
				}
				$db->sql_freeresult($result);
		
				if ((!defined('PORTAL') && $portal_version >= 'Plain') || !$portal_version || $portal_version < '0.0.0' || ($portal_version >= $upgrade_premod->cur_release && !in_array($portal_version, array('RC4 - Plain', 'Plain', 'Plain 0.1', 'Plain 0.2'))))
				{
					$template->assign_vars(array(
						'S_NOT_INSTALLED'		=> true,
						'TITLE'					=> $lang['PORTAL_UPDATE'],
						'BODY'					=> sprintf($lang['PORTAL_UPDATE_NOT_POSSIBLE'], $portal_version, $upgrade_premod->cur_release),
						
						'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
					));

					return;
				}

				$s_hidden_fields = '<input type="hidden" name="mode" value="upgrade_premod" />';
				$s_hidden_fields .= '<input type="hidden" name="sub" value="update_db" />';

				$template->assign_vars(array(
					'L_SUBMIT'				=> $lang['UPDATE_DATABASE'],
					'TITLE'					=> $lang['PORTAL_UPGRADE'],
					'BODY'					=> sprintf($lang['PORTAL_UPGRADE_TODO'], $portal_version, $upgrade_premod->cur_release),
					'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
					'U_ACTION'				=> append_sid($phpbb_root_path . 'install_portal/index.' . $phpEx),
						
					'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
				));

			break;

			case 'update_db':
				$this->update_data($sub);

			break;

			case 'insert_modules':
				$this->page_title = $lang['ACP_MODULE_MANAGEMENT'];

				require($phpbb_root_path . 'config.' . $phpEx);
				require($phpbb_root_path . 'includes/constants.' . $phpEx);
				require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
		
				$db = new $sql_db();
				$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, true);
				unset($dbpasswd);

				// Check existing shoubox tab on acp 
				$sql = "SELECT module_id FROM " . MODULES_TABLE . "
					WHERE module_langname = '" . $db->sql_escape('ACP_SHOUTBOX') . "'
						AND module_class = 'acp'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				$choosen_acp_module = (int) $db->sql_fetchfield('module_id');
				
				// Create shoubox tab, if not found
				if ($choosen_acp_module <= 0)
				{
					$acp_shoubox_tab = array('module_basename' => '',	'module_enabled' => 1,	'module_display' => 1,	'parent_id' => 0,	'module_class' => 'acp',	'module_langname' => 'ACP_SHOUTBOX',	'module_mode' => '',	'module_auth' => '');
					$this->add_module($acp_shoubox_tab, $db);
					$choosen_acp_module = $db->sql_nextid();
				}
				else
				{
					$sql = "UPDATE " . MODULES_TABLE . " SET
						module_enabled = 1, module_display = 1
						WHERE module_class = 'ACP'
							AND module_langname = 'ACP_SHOUTBOX'";
					$db->sql_query($sql);
				}

				// Add new Breits Shout box MOD modules
				$shout_general_cat = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $choosen_acp_module,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_GENERAL_CAT',
					'module_mode'		=> '',
					'module_auth'		=> '');
				$this->add_module($shout_general_cat, $db);
				$acp_module_id = $db->sql_nextid();

				$shout_version = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 0,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_VERSION',
					'module_mode'		=> 'version',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_version, $db);

				$shout_configs = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_CONFIGS',
					'module_mode'		=> 'configs',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_configs, $db);

				$shout_rules = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_RULES',
					'module_mode'		=> 'rules',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_rules, $db);

				$shout_pricipal_cat = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $choosen_acp_module,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_PRINCIPAL_CAT',
					'module_mode'		=> '',
					'module_auth'		=> '');
				$this->add_module($shout_pricipal_cat, $db);
				$acp_module_id = $db->sql_nextid();

				$shout_overview = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_OVERVIEW',
					'module_mode'		=> 'overview',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_overview, $db);

				$shout_config_gen = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_CONFIG_GEN',
					'module_mode'		=> 'config_gen',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_config_gen, $db);

				$shout_private_cat = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $choosen_acp_module,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_PRIVATE_CAT',
					'module_mode'		=> '',
					'module_auth'		=> 'acl_a_shout_priv');
				$this->add_module($shout_private_cat, $db);
				$acp_module_id = $db->sql_nextid();

				$shout_private = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_PRIVATE',
					'module_mode'		=> 'private',
					'module_auth'		=> 'acl_a_shout_priv');
				$this->add_module($shout_private, $db);

				$shout_config_priv = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_CONFIG_PRIV',
					'module_mode'		=> 'config_priv',
					'module_auth'		=> 'acl_a_shout_priv');
				$this->add_module($shout_config_priv, $db);

				$shout_popup_cat = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $choosen_acp_module,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_POPUP_CAT',
					'module_mode'		=> '',
					'module_auth'		=> '');
				$this->add_module($shout_popup_cat, $db);
				$acp_module_id = $db->sql_nextid();

				$shout_popup = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_POPUP',
					'module_mode'		=> 'popup',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_popup, $db);

				$shout_smiles_cat = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $choosen_acp_module,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_SMILIES_CAT',
					'module_mode'		=> '',
					'module_auth'		=> '');
				$this->add_module($shout_smiles_cat, $db);
				$acp_module_id = $db->sql_nextid();

				$shout_smilies = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_SMILIES',
					'module_mode'		=> 'smilies',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_smilies, $db);

				$shout_robot_cat = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $choosen_acp_module,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_ROBOT_CAT',
					'module_mode'		=> '',
					'module_auth'		=> '');
				$this->add_module($shout_robot_cat, $db);
				$acp_module_id = $db->sql_nextid();

				$shout_robot = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_ROBOT',
					'module_mode'		=> 'robot',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_robot, $db);

				$shout_robot_mod = array(
					'module_basename'	=> 'shoutbox',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'			=> $acp_module_id,
					'module_class'		=> 'acp',
					'module_langname'	=> 'ACP_SHOUT_ROBOT_MOD',
					'module_mode'		=> 'robot_mod',
					'module_auth'		=> 'acl_a_shout_manage');
				$this->add_module($shout_robot_mod, $db);


				// Check existing mods tab on acp 
				$sql = "SELECT module_id FROM " . MODULES_TABLE . "
					WHERE module_langname = 'ACP_CAT_DOT_MODS'
						AND module_class = 'acp'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				$choosen_acp_module = intval($row['module_id']);
				
				// Create mods tab, if not found
				if ($choosen_acp_module <= 0)
				{
					$acp_mods_tab = array('module_basename' => '',	'module_enabled' => 1,	'module_display' => 1,	'parent_id' => 0,	'module_class' => 'acp',	'module_langname'=> 'ACP_CAT_DOT_MODS',	'module_mode' => '',	'module_auth' => '');
					$this->add_module($acp_mods_tab, $db);
					$choosen_acp_module = $db->sql_nextid();
				}
				else
				{
					$sql = "UPDATE " . MODULES_TABLE . " SET
						module_enabled = 1, module_display = 1
						WHERE module_class = 'ACP'
							AND module_langname = 'ACP_CAT_DOT_MODS'";
					$db->sql_query($sql);
				}

				// Add new Download MOD modules
				$dl_mod = array(
					'module_basename'   => '', // must be blank for category
					'module_mode'      	=> '', // must be blank for category/tab
					'module_auth'      	=> '', // must be blank for category/tab
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module, // If you wanted to add this to an existing tab, you could have obtained the parent_id here using module_exists('TAB_NAME', 0);
					'module_langname'   => 'ACP_DOWNLOADS', //language key or just a string for the name -- must include
					'module_class'      => 'acp');
				$this->add_module($dl_mod, $db);
				$acp_module_id = $db->sql_nextid();
				
				$dl_mod_overview = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'overview',	
					'module_auth' 		=> 'acl_a_dl_overview',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'ACP_USER_OVERVIEW',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_overview, $db);
				
				$dl_mod_config = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'config',	
					'module_auth' 		=> 'acl_a_dl_config',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'DL_ACP_CONFIG_MANAGEMENT',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_config, $db);
				
				$dl_mod_traffic 		= array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'traffic',	
					'module_auth' 		=> 'acl_a_dl_traffic',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'DL_ACP_TRAFFIC_MANAGEMENT',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_traffic, $db);
				
				$dl_mod_categories = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'categories',	
					'module_auth' 		=> 'acl_a_dl_categories',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'DL_ACP_CATEGORIES_MANAGEMENT',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_categories, $db);
				
				$dl_mod_files = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'files',	
					'module_auth' 		=> 'acl_a_dl_files',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'DL_ACP_FILES_MANAGEMENT',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_files, $db);
				
				$dl_mod_permissions = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'permissions',	
					'module_auth' 		=> 'acl_a_dl_permissions',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'DL_ACP_PERMISSIONS',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_permissions, $db);
				
				$dl_mod_stats = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'stats',	
					'module_auth' 		=> 'acl_a_dl_stats',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname'	=> 'DL_ACP_STATS_MANAGEMENT',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_stats, $db);
				
				$dl_mod_banlist = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'banlist',	
					'module_auth' 		=> 'acl_a_dl_banlist',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname' 	=> 'DL_ACP_BANLIST',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_banlist, $db);
				
				$dl_mod_blacklist = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'ext_blacklist',	
					'module_auth' 		=> 'acl_a_dl_blacklist',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname' 	=> 'DL_EXT_BLACKLIST',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_blacklist, $db);
				
				$dl_mod_toolbox = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'toolbox',	
					'module_auth' 		=> 'acl_a_dl_toolbox',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname' 	=> 'DL_MANAGE',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_toolbox, $db);
				
				// -- New on 6.4.0 
				$dl_mod_fields = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'fields',	
					'module_auth' 		=> 'acl_a_dl_fields',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname' 	=> 'DL_ACP_FIELDS',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_mod_fields, $db);
				
				// -- New on 6.4.8 
				$dl_browser = array(
					'module_basename' 	=> 'downloads',	
					'module_mode' 		=> 'browser',	
					'module_auth' 		=> 'acl_a_dl_browser',
					'module_enabled' 	=> 1,	
					'module_display' 	=> 1,	
					'parent_id' 		=> $acp_module_id,	
					'module_langname' 	=> 'DL_ACP_BROWSER',	
					'module_class' 		=> 'acp');	
				$this->add_module($dl_browser, $db);


				// Add new Calendar MOD modules
				$cal_mod = array(
					'module_basename'   => '', // must be blank for category
					'module_mode'      	=> '', // must be blank for category/tab
					'module_auth'      	=> '', // must be blank for category/tab
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module, // If you wanted to add this to an existing tab, you could have obtained the parent_id here using module_exists('TAB_NAME', 0);
					'module_langname'   => 'ACP_CALENDAR', //language key or just a string for the name -- must include
					'module_class'      => 'acp');
				$this->add_module($cal_mod, $db);
				$acp_module_id = $db->sql_nextid();

				$cal_settings = array(
					'module_basename'   => 'calendar',
					'module_mode'      	=> 'calsettings',
					'module_auth'      	=> 'acl_a_calendar',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_CALENDAR_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($cal_settings, $db);
				
				$cal_etypes = array(
					'module_basename'   => 'calendar',
					'module_mode'      	=> 'caletypes',
					'module_auth'      	=> 'acl_a_calendar',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_CALENDAR_ETYPES',
					'module_class'      => 'acp');
				$this->add_module($cal_etypes, $db);


				// Add new log_connections MOD modules
				$lc_mod = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_LC',
					'module_class'      => 'acp');
				$this->add_module($lc_mod, $db);
				$acp_module_id = $db->sql_nextid();

				$lc_log_connections = array(
					'module_basename'   => 'lc',
					'module_mode'      	=> 'log_connections',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_CONNECTIONS_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($lc_log_connections, $db);
				$lc_connections = array(
					'module_basename'   => 'lc',
					'module_mode'      	=> 'connections',
					'module_auth'      	=> 'acl_a_viewlogs',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_CONNECTIONS_LOGS',
					'module_class'      => 'acp');
				$this->add_module($lc_connections, $db);


				// Add new add_user MOD modules
				$add_user_mod = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_CAT_USERS',
					'module_class'      => 'acp');
				$this->add_module($add_user_mod, $db);
				$acp_module_id = $db->sql_nextid();

				$add_user = array(
					'module_basename'   => 'add_user',
					'module_mode'      	=> 'add_user',
					'module_auth'      	=> 'acl_a_user',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_ADD_USER_ACCOUNT',
					'module_class'      => 'acp');
				$this->add_module($add_user, $db);
				$pm_spy = array(
					'module_basename'   => 'pm_spy',
					'module_mode'      	=> 'main',
					'module_auth'      	=> 'acl_a_pm_spy',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_PM_SPY',
					'module_class'      => 'acp');
				$this->add_module($pm_spy, $db);


				// Add new faq_manager MOD modules
				$faq_mod = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_FAQ_MANAGER',
					'module_class'      => 'acp');
				$this->add_module($faq_mod, $db);
				$acp_module_id = $db->sql_nextid();

				$faq_manager = array(
					'module_basename'   => 'faq_manager',
					'module_mode'      	=> 'default',
					'module_auth'      	=> 'acl_a_language',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_FAQ_MANAGER',
					'module_class'      => 'acp');
				$this->add_module($faq_manager, $db);


				// Add new announcemet centre MOD modules
				$announcements_centre = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_ANNOUNCEMENTS_CENTRE',
					'module_class'      => 'acp');
				$this->add_module($announcements_centre, $db);
				$acp_module_id = $db->sql_nextid();

				$announcements_centre_default = array(
					'module_basename'   => 'announcements_centre',
					'module_mode'      	=> 'default',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_ANNOUNCEMENTS_CENTRE',
					'module_class'      => 'acp');
				$this->add_module($announcements_centre_default, $db);


				// Add new Imprint modules
				$impressum = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'IMPRESSUM',
					'module_class'      => 'acp');
				$this->add_module($impressum, $db);
				$acp_module_id = $db->sql_nextid();

				$edit_impressum = array(
					'module_basename'   => 'impressum',
					'module_mode'      	=> 'edit_impressum',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'IMPRESSUM',
					'module_class'      => 'acp');
				$this->add_module($edit_impressum, $db);


				// Add new SEO modules
				$acp_portal_seo = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_CAT_PHPBB_SEO',
					'module_class'      => 'acp',
				);
				$this->add_module($acp_portal_seo, $db);
				$acp_module_id = $db->sql_nextid();

				$acp_portal_seo_settings = array(
					'module_basename'   => 'phpbb_seo',
					'module_mode'       => 'settings',
					'module_auth'       => 'acl_a_board',
					'module_enabled'    => 1,
					'module_display'    => 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_PHPBB_SEO_CLASS',
					'module_class'      => 'acp',
				);
				$this->add_module($acp_portal_seo_settings, $db);
				$acp_portal_seo_forum_url = array(
					'module_basename'   => 'phpbb_seo',
					'module_mode'       => 'forum_url',
					'module_auth'       => 'acl_a_board',
					'module_enabled'    => 1,
					'module_display'    => 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_FORUM_URL',
					'module_class'      => 'acp',
				);
				$this->add_module($acp_portal_seo_forum_url, $db);
				$acp_portal_seo_htaccess = array(
					'module_basename'   => 'phpbb_seo',
					'module_mode'       => 'htaccess',
					'module_auth'       => 'acl_a_board',
					'module_enabled'    => 1,
					'module_display'    => 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_HTACCESS',
					'module_class'      => 'acp',
				);
				$this->add_module($acp_portal_seo_htaccess, $db);
				$acp_portal_seo_extended = array(
					'module_basename'   => 'phpbb_seo',
					'module_mode'       => 'extended',
					'module_auth'       => 'acl_a_board',
					'module_enabled'    => 1,
					'module_display'    => 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_SEO_EXTENDED',
					'module_class'      => 'acp',
				);
				$this->add_module($acp_portal_seo_extended, $db);


				// Add new Welcome PM modules
				$wpm = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_WELCOME_PM',
					'module_class'      => 'acp');
				$this->add_module($wpm, $db);
				$acp_module_id = $db->sql_nextid();

				$settings_wpm = array(
					'module_basename'   => 'wpm',
					'module_mode'      	=> 'settings',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_WPM_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($settings_wpm, $db);


				// Friend list on member view
				$fom = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_FRIEND_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($fom, $db);
				$acp_module_id = $db->sql_nextid();

				$settings_fom = array(
					'module_basename'   => 'board',
					'module_mode'      	=> 'profilefriends',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_PROFILE_FRIENDS',
					'module_class'      => 'acp');
				$this->add_module($settings_fom, $db);


				// Contact Admin version 1.0.7
				$contact_admin = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_CAT_CONTACT',
					'module_class'      => 'acp');
				$this->add_module($contact_admin, $db);
				$acp_module_id = $db->sql_nextid();

				$settings_contact_admin = array(
					'module_basename'   => 'contact',
					'module_mode'      	=> 'configuration',
					'module_auth'      	=> 'acl_a_contact',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_CONTACT_CONFIG',
					'module_class'      => 'acp');
				$this->add_module($settings_contact_admin, $db);


				// User reminder version 1.0.5
				$user_reminder = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'USER_REMINDER',
					'module_class'      => 'acp');
				$this->add_module($user_reminder, $db);
				$acp_module_id = $db->sql_nextid();

				$user_reminder_configuration = array(
					'module_basename'   => 'user_reminder',
					'module_mode'      	=> 'configuration',
					'module_auth'      	=> 'acl_a_user || acl_a_userdel',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_USER_REMINDER_CONFIGURATION',
					'module_class'      => 'acp');
				$this->add_module($user_reminder_configuration, $db);
				
				$user_reminder_zero_poster = array(
					'module_basename'   => 'user_reminder',
					'module_mode'      	=> 'zero_poster',
					'module_auth'      	=> 'acl_a_user || acl_a_userdel',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_USER_REMINDER_ZERO_POSTER',
					'module_class'      => 'acp');
				$this->add_module($user_reminder_zero_poster, $db);
				
				$user_reminder_inactive = array(
					'module_basename'   => 'user_reminder',
					'module_mode'      	=> 'inactive',
					'module_auth'      	=> 'acl_a_user || acl_a_userdel',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_USER_REMINDER_INACTIVE',
					'module_class'      => 'acp');
				$this->add_module($user_reminder_inactive, $db);
				
				$user_reminder_not_logged_in = array(
					'module_basename'   => 'user_reminder',
					'module_mode'      	=> 'not_logged_in',
					'module_auth'      	=> 'acl_a_user || acl_a_userdel',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_USER_REMINDER_NOT_LOGGED_IN',
					'module_class'      => 'acp');
				$this->add_module($user_reminder_not_logged_in, $db);
				
				$user_reminder_inactive_still = array(
					'module_basename'   => 'user_reminder',
					'module_mode'      	=> 'inactive_still',
					'module_auth'      	=> 'acl_a_user || acl_a_userdel',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_USER_REMINDER_INACTIVE_STILL',
					'module_class'      => 'acp');
				$this->add_module($user_reminder_inactive_still, $db);
				
				$user_reminder_protected_users = array(
					'module_basename'   => 'user_reminder',
					'module_mode'      	=> 'protected_users',
					'module_auth'      	=> 'acl_a_user || acl_a_userdel',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_USER_REMINDER_PROTECTED_USERS',
					'module_class'      => 'acp');
				$this->add_module($user_reminder_protected_users, $db);
				

				// Advanced Block Mod 1.0.6
				$user_dnsbl_manage = array(
					'module_basename'   => 'dnsbl',
					'module_mode'      	=> 'manage',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => 5,
					'module_langname'   => 'ACP_DNSBL',
					'module_class'      => 'acp');
				$this->add_module($user_dnsbl_manage, $db);

				$user_dnsbl_manage = array(
					'module_basename'   => 'logs',
					'module_mode'      	=> 'block',
					'module_auth'      	=> 'acl_a_viewlogs',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => 25,
					'module_langname'   => 'ACP_BLOCK_LOGS',
					'module_class'      => 'acp');
				$this->add_module($user_dnsbl_manage, $db);
				

				// Mod_Share_On by _Vinny_
				$shareon = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_SHARE_ON',
					'module_class'      => 'acp');
				$this->add_module($shareon, $db);
				$acp_module_id = $db->sql_nextid();

				$shareon_configuration = array(
					'module_basename'   => 'shareon',
					'module_mode'      	=> 'settings',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_SHARE_ON_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($shareon_configuration, $db);
				

				// Dm Video
				$dm_video = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_DMV_DM_VIDEO',
					'module_class'      => 'acp');
				$this->add_module($dm_video, $db);
				$acp_module_id = $db->sql_nextid();

				$dm_video_video_config = array(
					'module_basename'   => 'dm_video',
					'module_mode'      	=> 'video_config',
					'module_auth'      	=> 'acl_a_dm_video_config',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DMV_CONFIG',
					'module_class'      => 'acp');
				$this->add_module($dm_video_video_config, $db);

				$dm_video_manage_categories = array(
					'module_basename'   => 'dm_video',
					'module_mode'      	=> 'manage_categories',
					'module_auth'      	=> 'acl_a_dm_video_cats',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DMV_MANAGE_CATEGORIES',
					'module_class'      => 'acp');
				$this->add_module($dm_video_manage_categories, $db);

				$dm_video_edit_videos = array(
					'module_basename'   => 'dm_video',
					'module_mode'      	=> 'edit_videos',
					'module_auth'      	=> 'acl_a_dm_video_edit',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DMV_EDIT',
					'module_class'      => 'acp');
				$this->add_module($dm_video_edit_videos, $db);

				$dm_video_release_videos = array(
					'module_basename'   => 'dm_video',
					'module_mode'      	=> 'release_videos',
					'module_auth'      	=> 'acl_a_dm_video_release',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DMV_RELEASE',
					'module_class'      => 'acp');
				$this->add_module($dm_video_release_videos, $db);

				$dm_video_reported_videos = array(
					'module_basename'   => 'dm_video',
					'module_mode'      	=> 'reported_videos',
					'module_auth'      	=> 'acl_a_dm_video_report',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DMV_REPORTED',
					'module_class'      => 'acp');
				$this->add_module($dm_video_reported_videos, $db);
				

				// DM Music Charts 1.0.2
				$dm_music = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'DM_MC_TITLE',
					'module_class'      => 'acp');
				$this->add_module($dm_music, $db);
				$acp_module_id = $db->sql_nextid();

				$dm_music_config = array(
					'module_basename'   => 'dm_music_charts',
					'module_mode'      	=> 'config',
					'module_auth'      	=> 'acl_a_dm_mc_manage',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'DM_MC_CONFIG',
					'module_class'      => 'acp');
				$this->add_module($dm_music_config, $db);

				$dm_music_manage_charts = array(
					'module_basename'   => 'dm_music_charts',
					'module_mode'      	=> 'manage_charts',
					'module_auth'      	=> 'acl_a_dm_mc_manage',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'DM_MC_MANAGE',
					'module_class'      => 'acp');
				$this->add_module($dm_music_manage_charts, $db);
				

				// PayPal IPN Donation 1.1.0
				$paypal_ipn = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_DONORS_MOD',
					'module_class'      => 'acp');
				$this->add_module($paypal_ipn, $db);
				$acp_module_id = $db->sql_nextid();

				$paypal_ipn_donors_config = array(
					'module_basename'   => 'donors_config',
					'module_mode'      	=> 'default',
					'module_auth'      	=> 'acl_a_dm_mc_manage',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DONORS_CONFIG',
					'module_class'      => 'acp');
				$this->add_module($paypal_ipn_donors_config, $db);

				$paypal_ipn_donate_mod_settings = array(
					'module_basename'   => 'donate_mod_settings',
					'module_mode'      	=> 'default',
					'module_auth'      	=> 'acl_a_dm_mc_manage',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DONATE_MOD_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($paypal_ipn_donate_mod_settings, $db);
				

				// Post Links 1.0.1
				$post_links = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_POST_LINKS_TITLE',
					'module_class'      => 'acp');
				$this->add_module($post_links, $db);
				$acp_module_id = $db->sql_nextid();

				$post_links_config = array(
					'module_basename'   => 'post_links',
					'module_mode'      	=> 'default',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_POST_LINKS_CONFIGURE',
					'module_class'      => 'acp');
				$this->add_module($post_links_config, $db);
				

				// Tapatalk 3.6.0
				$tapatalk = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_MOBIQUO',
					'module_class'      => 'acp');
				$this->add_module($tapatalk, $db);
				$acp_module_id = $db->sql_nextid();

				$tapatalk_config = array(
					'module_basename'   => 'mobiquo',
					'module_mode'      	=> 'mobiquo',
					'module_auth'      	=> 'acl_a_mobiquo',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_MOBIQUO_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($tapatalk_config, $db);

				$tapatalk_rebranding = array(
					'module_basename'   => 'mobiquo',
					'module_mode'      	=> 'rebranding',
					'module_auth'      	=> 'acl_a_mobiquo',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_TAPATALK_REBRANDING',
					'module_class'      => 'acp');
				$this->add_module($tapatalk_rebranding, $db);
				
				$tapatalk_register = array(
					'module_basename'   => 'mobiquo',
					'module_mode'      	=> 'register',
					'module_auth'      	=> 'acl_a_mobiquo',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_MOBIQUO_REGISTER_SETTINGS',
					'module_class'      => 'acp');
				$this->add_module($tapatalk_register, $db);

				// Database Optimize & Repair Tool 1.0.2
				$database_or = array(
					'module_basename'   => '',
					'module_mode'      	=> '',
					'module_auth'      	=> '',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $choosen_acp_module,
					'module_langname'   => 'ACP_CAT_DATABASE_OR',
					'module_class'      => 'acp');
				$this->add_module($database_or, $db);
				$acp_module_id = $db->sql_nextid();

				$databaseor_config = array(
					'module_basename'   => 'database_or',
					'module_mode'      	=> 'view',
					'module_auth'      	=> 'acl_a_backup',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_DATABASE_OR',
					'module_class'      => 'acp');
				$this->add_module($databaseor_config, $db);

				$databasebackup_config = array(
					'module_basename'   => 'auto_backup',
					'module_mode'      	=> 'index',
					'module_auth'      	=> 'acl_a_board',
					'module_enabled'   	=> 1,
					'module_display'   	=> 1, 
					'parent_id'         => $acp_module_id,
					'module_langname'   => 'ACP_AUTO_BACKUP_INDEX_TITLE',
					'module_class'      => 'acp');
				$this->add_module($databasebackup_config, $db);


				$s_hidden_fields = '<input type="hidden" name="mode" value="upgrade_premod" />';
				if ($sub == 'create_table')
				{
					$s_hidden_fields .= '<input type="hidden" name="sub" value="final" />';
					$l_submit = $lang['INSTALL_NEXT'];
					$body = $lang['PORTAL_FINAL_MODULE_STEP'];
				}
				else
				{
					$s_hidden_fields .= '<input type="hidden" name="sub" value="final" />';
					$l_submit = $lang['INSTALL_NEXT'];
					$body = $lang['PORTAL_FINAL_MODULE_STEP'];
				}		
				$s_hidden_fields .= '<input type="hidden" name="language" value="' . $language . '" />';
	
				$template->assign_vars(array(
					'TITLE'				=> $lang['STAGE_INSERT_MODULES'],
					'BODY'				=> $lang['PORTAL_FINAL_MODULE_STEP'],
					'L_SUBMIT'			=> $l_submit,
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
					'U_ACTION'			=> $this->p_master->module_url,
						
					'S_LANG_SELECT'		=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
				));

			break;

			case 'final':
				$this->page_title = $lang['UPDATE_COMPLETED'];

				// Clear cache
				$cache->purge();
				
				$url = $this->p_master->module_url . "?mode=upgrade_premod&amp;sub=additional";

				$template->assign_vars(array(
					'TITLE'					=> $lang['UPDATE_COMPLETED'],
					'BODY'					=> $lang['PORTAL_UPGRADE_SUCCESS'],
//					'L_SUBMIT'				=> $lang['INSTALL_LOGIN'],
//					'U_ACTION'				=> append_sid($phpbb_root_path . 'ucp.' . $phpEx .'?mode=login'),
					'L_SUBMIT'				=> $lang['PORTAL_ADDITIONAL_THIRD_PARTY_MODS'],
					'U_ACTION'				=> $url,
						
					'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
				));

			break;

			case 'additional' :
				$this->page_title = $lang['PORTAL_ADDITIONAL_THIRD_PARTY_MODS'];

				$template->assign_vars(array(
					'TITLE'					=> $lang['PORTAL_ADDITIONAL_THIRD_PARTY_MODS'],
					'BODY'					=> $lang['PORTAL_ADDITIONAL_THIRD_PARTY_MODS_BODY'],
						
					'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
				));
			break;
		}
	}

	/**
	* The function which does the actual work (or dispatches it to the relevant places)
	*/
	function update_data($sub)
	{
		global $template, $user, $phpbb_root_path, $phpEx, $db, $lang, $config, $cache;

		require($phpbb_root_path . 'config.' . $phpEx);
		require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
		require($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);

		$module = new module($this->p_master);

		$db = new $sql_db();
		$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, true);
		unset($dbpasswd);

		if (!defined('PORTAL_CONFIG_TABLE'))
		{
			require($phpbb_root_path . 'includes/constants.' . $phpEx);
		}

		$sql = 'SELECT config_value
			FROM ' . PORTAL_CONFIG_TABLE . "
			WHERE config_name = 'portal_version'";
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$portal_version = $row['config_value'];
		}
		$db->sql_freeresult($result);

		if ((!defined('PORTAL') && $portal_version >= 'Plain') || !$portal_version || $portal_version < '0.0.0' || ($portal_version >= $upgrade_premod->cur_release && !in_array($portal_version, array('RC4 - Plain', 'Plain', 'Plain 0.1', 'Plain 0.2'))))
		{
			return;
		}

		$this->page_title = $lang['STAGE_UPDATE_DB'];

		// Now do the real work from here
		$sql = array();

		$old_upgrade_premod = false;

		switch($portal_version)
		{
			case '0.0.0':
			case 'RC4 - Plain':
			case 'Plain':
			case 'Plain 0.1':
			case 'Plain 0.2':

				$sql[] = "UPDATE " . PORTAL_CONFIG_TABLE . " SET config_value = 'Premod 0.4' WHERE config_name = 'portal_version'";

				$sql[] = "INSERT INTO " . PORTAL_CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('portal_show_ajax_userinfo', '1', 0)";
				$sql[] = "INSERT INTO " . PORTAL_CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('portal_show_topic_hover_preview', '1', 0)";

				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (21, 'Download Mod for phpBB 3', '6.5.32', '', 'This mod generates a page of downloads for your phpBB 3 forum.', 'http://phpbb3.oxpus.net/index.php', 'OXPUS', 'http://phpbb3.oxpus.net/downloads.php?view=detail&amp;df_id=1')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (22, 'Prime Links', '1.2.6', '', 'Modifies links within posts so that local links are correctly classified as such. It can also apply a target to external links (e.g. to open in a new window) and prepend links (e.g. to apply an anonymizer).', 'http://www.absoluteanime.com/admin/mods.htm', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (23, 'Prime Links Supplement: Forum Links', '1.2.6', '', 'Applies a target to forums that are set up to be links so that they may open in a new browser window. The target is defined by EXTERNAL_LINK_TARGET in the &quot;includes/prime_links.php&quot; file.', 'http://www.absoluteanime.com/admin/mods.htm', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (24, 'Prime Links Supplement: Style Links', '1.2.6', '', 'Adds a stylesheet for convenience in styling links.', 'http://www.absoluteanime.com/admin/mods.htm', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (25, 'Prime Links Supplement: User Website Links', '1.2.6', '', 'Applies a target to user website links so that they may open in a new browser window.', 'http://www.absoluteanime.com/admin/mods.htm', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (26, 'Prime Quick Reply', '1.0.7', '', 'Adds a quick-reply form to topic pages, making available most features found on the standard reply page. Can be configured from within the Administration Control Panel, and will safely work in conjunction with my other MODs, such as Prime Anti-bot, Prime Multi-Quote, and Prime Trash Bin.', 'http://www.absoluteanime.com/admin/mods.htm', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (27, 'Log connections', '1.0.3', '', 'This MOD allows to log forum''s connections in success or failure. In ACP, many options are available to limit or maximize the number of logs in your database.', 'http://www.phpbb-services.com', 'Elglobo', 'http://www.phpbb-services.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (28, 'ACP Add User MOD', '1.0.1', '', 'Enables an Administrator to create a new user account through the Administration Control Panel. Adds an extra permission to allow administrator to create a new user account. Gives the administrator the ability to instantly approve a new member after creation.', 'http://phpBBAcademy.com', 'Highway of Life', 'http://phpBBAcademy.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (29, 'PhpBB3 Knowledge Base Mod', '1.0.2', '', 'This mod adds a Knowledge Base to your forum, which is fully integrated with phpBB''s core system. It is fully manageable through a ACP system found under the .MODS tab.', 'http://kb.softphp.dk/', 'Imladris', 'http://kb.softphp.dk/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (30, 'Anti Bot Question', '1.1.0', '', 'Add an Admin controlled anti-bot question to the registration page and ACP.', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=645075', 'CoC', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=645075')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (31, 'phpbb Calendar', '0.1.0', '', 'A calendar MOD for phpBB to allow users to post events for public or private view within phpBB.\n\nCompleted Tasks in Detail:\n* Includes both All Day and Timed Events\n\n* Event View - filled with all event details (who''s invited, who created the event,\nBBCode, Smilies, start and end times, edit [amp] delete buttons if applicable etc)\n\n* Month View - can jump to any month via next and prev links, or jump randomly via\npulldown menus. Lists birthdays, event types and event names only. Click on the\nday''s number to add a new event on that day\n\n* Week View - can jump to any week via next and prev links, or jump randomly via\npulldown menus. Lists birthdays, event types, names, and times. Click on the day''s\nnumber to add a new event on that day.\n\n* Day View - can jump to any day via next and prev links, or jump randomly via\npulldown menus. Includes a Graphical display of events on a timeline - lets you\nquickly see schedule conflicts etc. Lists birthdays, event types, names, and times.\nClick on the day''s number to add a new event on that day.\n\n* Support for BBCode and Smilies\n\n* Birthday Support - Using phpBB3 Birthdays\n\n* Personal Events\n\n* Group Events\n\n* Public Events\n\n* Make events bold if you are event creator - Let''s you quickly see events most\nimportant to you.\n\n* List of Upcoming Events on index - in the ACP you can specify whether or not you\nwant to display the upcoming events on the index (and if so how many events to list).\nYou also have the option to list the current week view on the index.\n\n* Detailed Permissions- You can control who has permission to view, create, edit,\ndelete, and moderate events.\n\n* Auto pruning of past events - From the ACP you control how often events are pruned,\nand how old they have to be before they are added to the delete list.', 'http://phpbbcalendarmod.com', 'alightner', 'http://phpbbcalendarmod.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (32, 'Breizh Shoutbox', '1.4.0', '', 'Breizh Shoutbox is a mod for phpbb3, which allows to chat live on a forum. Being in ajax, this shoutbox is charging discreetly without reloading the page. It adapts perfectly with styles prosilver and subsilver2. The configuration allows high degree of customizing to the extreme. Can also display on a popup and on a very discreet lateral panel. This lateral panel with an iframe may be called in all the pages of a site, regardless of the employee cms, while respecting the user''s permissions. Warning: This mod will only work on a forum phpbb3 version 3.0.8 minimum and later.', 'http://breizh-portal.com/', 'Sylver35', 'http://breizh-portal.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (33, 'Thank Post MOD', '0.4.0', '', 'Thank other user''s posts! Allow other users to thank your posts! This version is reworked for portal use.', 'https://www.phpbb.com/community/viewtopic.php?f=70&amp;t=543797&amp;start=0', 'geoffreak', 'https://www.phpbb.com/community/viewtopic.php?f=70&amp;t=543797&amp;start=0')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (34, 'FAQ Manager', '1.2.5', '', 'Adds an easy to use FAQ Manager to the ACP.', 'http://www.lithiumstudios.org/forum/viewtopic.php?f=31&t=464', 'EXreaction', 'http://www.lithiumstudios.org/forum/viewtopic.php?f=31&t=464')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (35, 'Categorize Announcements and Stickies', '1.0.0', '', 'This MOD will separate the topics in viewforum into Global Announcements, Announcements, Stickies and Normal Topics. Global Announcements are displayed first, followed by Announcements, Stickies, and Topics. Global Announcements / Announcements will be sorted by last post date, instead of topic start date.', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=1101445', 'Ash Hi Fi Zone', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=1101445')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (36, 'Posts per day', '1.0.0', '', 'Adds a little extra info to the statistic box on the index page: Average posts per day since the board came into existence.', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=1214815', 'MartectX', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=1214815')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (37, 'PM Spy', '0.0.1', '', 'Adds an Admin option to list/read/sort all of the board''s PM''s with the ability to delete PM''s.', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1074285', 'david63', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1074285')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (38, 'Avatar on Memberlist', '1.0.0', '', 'Displays a small thumbnail of users avatars on the member list. -- Rolling your mouse over the thumbnail will show the full size avatar.', 'https://www.phpbb.com/community/viewtopic.php?f=69&amp;t=583545', 'Highway of Life', 'https://www.phpbb.com/community/viewtopic.php?f=69&amp;t=583545')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (39, 'MCP info on index', '1.0.2', '', 'Adds a red box to the index when there are reported or unapproved posts (and how many). So you don''t have to check the MCP every time and you will see reports and unapproved posts faster. This is only visible for moderators or admins with moderator permissions.', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=1214675', 'Derky', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=1214675')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (40, 'Prime Post Revisions', '1.2.3', '', 'Stores each revision of a post every time a post is edited. It will only make these past revisions viewable to those users who have the necessary permissions.', 'http://www.absoluteanime.com/admin/mods.htm#post_revisions', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm#post_revisions')";
			    $sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (41, 'Highslide Attatchment Mod', '4.0.10', '', 'Makes attached images open in a nice popup layer. Large images fits screen (resized) with option to view fullsize. Commercial users need to register their version', 'http://www.phpbb3bbcodes.com', 'Stoker', 'http://www.phpbb3bbcodes.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (42, 'ACP Announcement Centre', '1.0.3', 'a', 'Adds an Announcement Box to the Index Page.\nFeatures:\n- Enable/Disable Announcement\n- (Guest) Announcement Preview\n- BBCode/Smilie enabled Announcements\n- Possibility to show different Announcements to Guest users\n- Possibility to show birthdays as announcements (display of avatars possible)\n- Option to show Announcements to Guests only, group(s) only or everyone\n- (Guest) Announcement Title configurable\n- Option to show pages on all pages or on index page only', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=981855&amp;start=0', 'lefty74', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=981855&amp;start=0')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (43, 'Rules page', '1.0.0', 'b', 'This MOD will add a Rules page to your board (using faq.php).', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=753535&amp;start=0', 'eviL&lt;3', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=753535&amp;start=0')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (44, 'Imprint', '0.1.6', 'b', 'Add an Impressum with config per ACP to the forum', 'http://www.phpbb-seo.de/downloads/mod-impressum.html', 'tas2580', 'http://www.phpbb-seo.de/downloads/mod-impressum.html')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (45, 'Similar Topics', '1.0.0', '', 'Shows a list with similar topics at the end of a topic.', 'http://www.phpbb-seo.de/downloads/mod-similar-topics.html', 'tas2580', 'http://www.phpbb-seo.de/downloads/mod-similar-topics.html')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (46, 'phpBB SEO Ultimate SEO URL', '0.7.0', '', 'This mod will URL rewrite phpBB URLs in a lot of different ways. You will be able to run the mod in Advanced, Mixed and Simple mode.', 'http://www.phpbb-seo.com/', 'dcz', 'http://www.phpbb-seo.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (47, 'SupportTicket Assistant for phpBB Support Sites', '1.0.2', '', 'This MOD adds an Assistant to your phpBB Supportforum wich asks the user to important information supporters need to help Users with support questions. You simply have to go in the ACP and aktivate the Support Ticket System for the Forum you wish. When the Support Ticket System not aktive is, you have the normal phpBB Post System.', 'http://www.flying-bits.org/', 'nickvergessen', 'http://www.flying-bits.org/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (48, 'Prime Trash Bin', '1.0.8', 'a', 'Allows topics and posts to be kept when they are deleted so that they can be reviewed before being permanently deleted (or undeleted). Also, provides the ability to enter a reason for the deletion, and allows deleted topics to be moved to a specified Trash Bin forum. These deleted topics and posts can be viewed, undeleted, or permanently deleted by those with the proper permissions. Permanent deletion will occur when deleting topics in the Trash Bin forum or topics and posts that have already been marked as deleted.', 'http://www.absoluteanime.com/admin/mods.htm#trash_bin', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm#trash_bin')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (49, 'Auto Groups MOD', '1.0.1', '', 'Gives administrators options to place users in groups based on post count, membership days, and warning points.', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=770205', 'A_Jelly_Doughnut', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=770205')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (50, 'Welcome PM on First Login', '2.2.5', '', 'Lets the admin configure a welcome private message that will be sent out to newly registered users on their first login.', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=573016', '..::Frans::..', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=573016')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (51, 'Contact board administration', '1.0.10', '', 'Allows guests and/or registered users to either send an email to admins or to either send a PM or make a Post in a designated forum. Allows an admin to choose to have attachments for forum post or PMs. Also allows an admin to allow checking against forum email and/or user names.', 'http://www.phpbb.com/community/viewtopic.php?p=11823035#p11823035', 'RMcGirr83', 'http://www.phpbb.com/community/viewtopic.php?p=11823035#p11823035')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (52, 'AJAX Userinfo', '0.1.0', '', 'Shows a small popup with the avatar and infos about the user by mouseover on a username with link to profile. Rewritten for Portal XL use by DaMysterious.', 'http://www.phpbb.com/community/viewtopic.php?p=3009017#p3009017', 'tas2580', 'http://www.phpbb.com/community/viewtopic.php?p=3009017#p3009017')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (53, 'Topic Hover Preview', '0.3.1', '', 'Hovering over topic links will show text from the first post of that topic.', '', 'raptor5001', '')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (54, 'View or mark unread posts', '1.0.4', '', 'Adds a ''View unread posts'' or ''You have no unread posts'' link\non the index page (and, for subsilver2, each other page). Also adds a ''Mark post as unread'' link at the bottom of each post and a mark pm as unread link at the bottom of each private message as well as a folder in the private messages part of the UCP for unread pms.', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=788695', 'asinshesq', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=788695')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (55, 'AJAX User Registration Checks', '1.0.0', '', 'During user registration, this MOD makes a number of checks to see if it can find errors with the information given by the user. These checks are;\n* Checks if the username is available or whether it has already been taken.\n* Checks that the two passwords entered are the identical (if both have been entered).\n* Checks if the first email address is in the correct format. If it is then it does the next check:\n* Checks that the two email addresses entered are the same (if both have been entered).', 'http://www.phpbb.com/community/viewtopic.php?f=70&amp;t=1311855&amp;start=0', 'andy2295', 'http://www.phpbb.com/community/viewtopic.php?f=70&amp;t=1311855&amp;start=0')";
				// $sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (56, 'Automatic DST', '2.0.1', '', 'Allows users to choose Daylight Savings Time (DST) automatically instead of having to set it twice a year. The basis for this adjustment are the web server''s time settings. Also, DST adjustments are now determined for each date instead of just being globally applied.\n\nVisitors will be affected by the setup of the guest account. Newly registering users will inherit the &quot;Automatic&quot; option should they live in the board''s native time zone.', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1496415', 'MartectX', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1496415')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (57, 'Smilie Creator', '1.0.6', '', 'With this mod you can add some smilie signs to your forum.\nUse this MOD as a custom bbcode or with a smilie creator popup.', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=1069695', 'Dr.Death', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=1069695')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (58, 'Quickly Change Your Language', '0.1.0', '', 'Allows users to quickly change their language by adding ?lang= to the URL.', NULL, 'LEW21', NULL)";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (59, 'phpBB Gallery', '1.1.6', '', 'An image-gallery integrated into your phpbb-board.', 'http://www.flying-bits.org/', 'nickvergessen', 'http://www.flying-bits.org/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (60, 'jQuery JavaScript Library', '1.7.2', '', 'jQuery is a fast and concise JavaScript Library that simplifies HTML document traversing, event handling, animating, and Ajax interactions for rapid web development. jQuery is designed to change the way that you write JavaScript.', 'http://jquery.com/', 'John Resig', 'http://jquery.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (61, 'Easy Widgets jQuery plugin', '2.0', '', 'A very easy way to use Widgets in your site.', 'http://bb.magudas.com/jq/easywidgets/index.html', 'David Esperalta', 'http://bb.magudas.com/jq/easywidgets/index.html')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (62, 'Character Countdown', '0.0.3', '', 'Displays the amount of characters the user has typed while posting a message.', 'http://www.phpbb.com/community/viewtopic.php?p=10312655#p10312655', 'Xtracker!', 'http://www.phpbb.com/community/viewtopic.php?p=10312655#p10312655')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (63, 'Anti-Spam ACP', '1.0.5', '', 'Prevents spam on your phpBB3 forum.', 'http://www.lithiumstudios.org/', 'EXreaction', 'http://www.lithiumstudios.org/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (64, 'Sortables CAPTCHA Plugin', '1.0.1', '', 'This CAPTCHA plugin adds two columns, you can add options to each column. All the options will be displayed into one column, then the user has to sort the options from one to the other column, by dragging them with the mouse. If the options are dragged to the correct columns the CAPTCHA is solved. Because this is a plugin you don''t have to edit any file, just upload the files and it works!', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1714415', 'Derky', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1714415')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (65, 'Country Flags', '3.0.6', '', 'Are you a true patriot? This MOD allows your registered users to select the flag of their country.\r\nTheir country flags will then display thoughout the phpBB system. You can select a default country flag for usergroups.\r\nYou also can manage these flags (edit/delete/add), change some settings, set country flag for users/groups...\r\nin ACP, and in UCP for usergroups.', 'http://www.vinabb.com/', 'nedka', 'http://vinabb.com/viewtopic.php?p=513#p513')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (66, 'Genders', '1.0.1', '', 'This MOD will allow your members to specify their Gender. They can chose between &quot;Male&quot;, &quot;Female&quot; and &quot;None specified&quot;.', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=736135&amp;start=0', 'eviL<3', 'http://www.phpbb.com/community/viewtopic.php?f=69&amp;t=736135&amp;start=0')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (67, 'User achievements', '0.0.2', '', 'Adds goals to user profiles', 'http://itmods.com/viewtopic.php?p=605#p605', 'platinum_2007', 'http://itmods.com/viewtopic.php?p=605#p605')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (68, 'Ranks page', '1.0', '', 'Adds a ranks page to Portal XL showing all installed ranks on board. Page will be paginated if applicable.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (69, 'Smiles page', '1.0', '', 'Adds a smiles page to Portal XL showing all installed smiles on board. Page will be paginated if applicable.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (70, 'Google Search', '1.0.0', '', 'Google search check-box with standard search.', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=1659335', 'AllCity', 'http://www.phpbb.com/community/viewtopic.php?f=69&t=1659335')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (71, 'Guest Hide BBCode MOD', '1.4.0', '', 'With the [hide] BBCode, members can hide their message content from guest!', 'http://www.phpbb.com/community/viewtopic.php?p=8076165', 'AllCity', 'http://www.phpbb.com/community/viewtopic.php?p=8076165')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (72, 'Friends on member view', '1.0.0', '', 'Adds a friend list to user profiles.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (73, 'phpBB Arcade', '2.0', 'RC1', 'Full arcade add-on for phpBB 3.0.9 or later.', 'http://phpbbarcade.origon.dk/index.php', 'KillBill', 'http://phpbbarcade.origon.dk/index.php')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (74, 'Flag list page', '1.0', '', 'Adds a flag list to Portal XL showing all installed flags on board.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (75, 'Topic solved', '1.4.5', '', 'Allows users to set topics as solved, and then set them back as unsolved. The option is found grouped with the profile icon buttons in the message areas of a topic (i.e. Edit, Report, Quote icons etc). Gives the ability to search in solved and unsolved topics including your own. Configurable in ACP > Forums .. Edit forum.', 'http://www.phpbb.com/customise/db/mod/topic_solved/', 'tumba25', 'http://skripter.se/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (76, 'Groups list page', '1.0', '', 'Adds a groups list to Portal XL showing all available groups on board.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (77, 'Prime Notify', '1.0.9', '', 'Inserts the content of a post or private messages into the notification e-mail.', 'http://www.absoluteanime.com/admin/mods.htm#notify', 'primehalo', 'http://www.absoluteanime.com/admin/mods.htm#notify')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (78, 'User Reminder', '1.0.5', '', 'Will remind users that they are members of a community by sending out emails when not having logged in for a while, not posted yet, registered, activated but not logged in since, not having acted on above for a while (after first reminder)... The you better visit soon or ... email.', 'http://www.lefty74.com', 'lefty74', 'http://www.lefty74.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (79, 'Advanced Block Mod', '1.0.6', '', 'Adds more DNS blacklists to phpBB3. Blacklists can be managed from ACP. Blacklists can be weighted from 0 to 5 to reach a threshold value of 5 to bring several blacklists in combination for blocking. Adds a new log for Block actions. Adds logging to validate_email and check_dnsbl. Adds WHOIS to logs.', 'http://www.martin-truckenbrodt.com', 'Martin Truckenbrodt', 'http://www.martin-truckenbrodt.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (80, 'Email on Birthday', '1.0.1', 'b', 'If Birthdays are enabled this will send an email to the members on their birthday, can be turned off via ACP.', 'http://www.lefty74.com', 'lefty74', 'http://www.lefty74.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (81, 'Portal XL Custom bbCode Box', '1.0.0', '', 'This mod adds a Office like tool-bar for formatting and multimedia-contents to the phpBB3-Postbox and eliminates the standard phpBB3 bbCode Buttons.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (82, 'Share On', '2.1.0', '', 'After installing this mod, user can share his posts on Facebook, Twitter, Orkut, Digg, MySpace, Delicious, Technorati or Google.', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1844865', '_Vinny_', 'http://www.phpbb.com/community/viewtopic.php?f=70&t=1844865')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (83, 'DM Multi Zodiacs', '1.0.0', '', 'This mod will add different zodias to your forum. You will see them in view topic view, as well as in the users profile. Images created by darko, will be found for European, American Indian and Chinese zodiacs.', 'http://die-muellers.org', 'Felix Mueller', 'http://die-muellers.org')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (84, 'DM Video', '1.0.5', '', 'This mod will add a video page to your forum, where you can show videos as provided by YouTube, MyVideo or other providers, which offer embedded codes to copy. Videos can be added by the users, but need approval by the administrator. Also they can enter a description or whatever they like, if they want. You can have categories and subcategories to sort the videos.', 'http://die-muellers.org', 'Felix Mueller', 'http://die-muellers.org')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (85, 'RSS syndication downloads', '1.0', '', 'RSS (which, in its most recent format, stands for &quot;Really Simple Syndication&quot;). RSS content can be read using software called a &quot;feed reader&quot; or an &quot;aggregator.&quot; The user subscribes to a feed by entering the feed''s link into the reader or by clicking an RSS icon in a browser that initiates the subscription process. The reader checks the user''s subscribed feeds regularly for new content, downloading any updates that it finds. ', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (86, 'RSS syndication knowledgebase', '1.0', '', 'RSS (which, in its most recent format, stands for &quot;Really Simple Syndication&quot;). RSS content can be read using software called a &quot;feed reader&quot; or an &quot;aggregator.&quot; The user subscribes to a feed by entering the feed''s link into the reader or by clicking an RSS icon in a browser that initiates the subscription process. The reader checks the user''s subscribed feeds regularly for new content, downloading any updates that it finds. ', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (87, 'RSS syndication gallery', '1.0', '', 'RSS (which, in its most recent format, stands for &quot;Really Simple Syndication&quot;). RSS content can be read using software called a &quot;feed reader&quot; or an &quot;aggregator.&quot; The user subscribes to a feed by entering the feed''s link into the reader or by clicking an RSS icon in a browser that initiates the subscription process. The reader checks the user''s subscribed feeds regularly for new content, downloading any updates that it finds. ', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (88, 'RSS syndication arcade', '1.0', '', 'RSS (which, in its most recent format, stands for &quot;Really Simple Syndication&quot;). RSS content can be read using software called a &quot;feed reader&quot; or an &quot;aggregator.&quot; The user subscribes to a feed by entering the feed''s link into the reader or by clicking an RSS icon in a browser that initiates the subscription process. The reader checks the user''s subscribed feeds regularly for new content, downloading any updates that it finds. ', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (89, 'RSS syndication video', '1.0', '', 'RSS (which, in its most recent format, stands for &quot;Really Simple Syndication&quot;). RSS content can be read using software called a &quot;feed reader&quot; or an &quot;aggregator.&quot; The user subscribes to a feed by entering the feed''s link into the reader or by clicking an RSS icon in a browser that initiates the subscription process. The reader checks the user''s subscribed feeds regularly for new content, downloading any updates that it finds. ', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (90, 'First Topic [pic] on Forum Index', '0.0.6', '', 'This Mod shows the first [pic] in a Topic on Forum Index, no matter if it''s an [img] or [attachment).', 'http://4seven.kilu.de/forum/phpbb3/', '4_seven', 'http://4seven.kilu.de/forum/phpbb3/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (91, 'jQuery Topic Short Description', '0.0.2', '', 'This Mod shows a jQuery topic short description teaser with ''Read More Link'' in Topic Overview.', 'http://4seven.kilu.de/forum/phpbb3/', '4_seven', 'http://4seven.kilu.de/forum/phpbb3/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (92, 'DM Music Charts', '1.0.2', '', 'With this mod, you can offer your users a music charts table, which they can build by their own. All registered users can add new songs, which then will take part in the election. You will set a election period, where the registered users then can vote once for every song. This way you will receive a dynamic charts table.', 'http://die-muellers.org/', 'Felix Mueller', 'http://die-muellers.org/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (93, 'PayPal IPN Donation', '1.1.0', '', 'Allow phpbb3 forum to accept donations from your community members.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (94, 'Index Tabbed', '1.1.0', '', 'Login, Who is online, Birthdays, Statistics in tabbed mode.', 'http://www.mssti.com/phpbb3/', 'Leviatan21', 'http://www.mssti.com/phpbb3/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (95, 'Browser, Os and Screen', '0.3.0', '', 'This mod will display additional informations of the poster in view message.', 'http://breizh-portal.com/', 'Sylver35', 'http://breizh-portal.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (96, 'Visit counter by forum', '1.0.0', '', 'This mod adds a counter next to each forum name, displaying the number of visitors navigate into this forum. Users in sub-forums are not recorded.', 'http://www.phpbb-services.com/', 'ErnadoO', 'http://www.phpbb-services.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (97, 'Users and Bots on Seperate Lines', '1.0.0', '', 'With this mod your users and bots will display on separate lines within the stats section of your forum.', 'http://www.phpbbmodders.net/', 'Tumba25', 'http://www.phpbbmodders.net/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (98, 'Users Profile Tabbed', '1.0.0', '', 'Divides the members profile into separate tabs for cleaner overview.', 'http://www.portalxl.nl/forum/', 'DaMysterious', 'http://www.portalxl.nl/forum/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (99, 'View Poster IP in viewtopic', '1.0.0', '', 'This MOD allows Moderator and Administrator to View the IP of the Poster on the Mini-Profile.', 'http://breizh-portal.com/', 'Sylver35', 'http://breizh-portal.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (100, 'uAttachment', '1.0.1', '', 'This mod adds an -Update attachment- button to upload attachments form.', 'http://allcity.net.ru/', 'AllCity', 'http://allcity.net.ru/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (101, 'Default Random No Avatar', '1.0.4', 'a', 'Random Avatars are displayed by default for users who have not selected an Avatar.', 'http://www.boardtalk.net/', 'Boardtalk.net', 'http://www.boardtalk.net/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (102, 'Post links', '1.0.1', '', 'This MOD will add links to all your posts. You can choose to display link, bb code and html format of post. Those links are hidden by default and user can show them by single click, so they do not make posts huge showing each 3 links after each post on page.', 'http://phpbb3hacks.com/', 'Senky', 'http://phpbb3hacks.com/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (103, 'Collapse categories', '1.1.1', '', 'This MOD allows users to collapse categories on index.', 'http://www.phpbb.com/community/memberlist.php?mode=viewprofile&un=doktornotor', 'doktornotor', 'http://www.phpbb.com/community/memberlist.php?mode=viewprofile&un=doktornotor')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (104, 'Tapatalk', '4.4.1', '', 'Tapatalk for the forum index and seperate page.', 'http://www.tapatalk.com', 'tapatalk', 'http://www.tapatalk.com')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (105, 'Database Optimize and Repair Tool', '1.0.2', '', 'This MOD will allow you to check, optimize and repair phpBB''s database tables from a phpMyAdmin-like interface in the ACP. Requirements: Your phpBB database must be using MySQL with MyISAM, InnoDB or Archive table types. Note: InnoDB table types do not support Repair.', 'http://www.phpbb.com/customise/db/mod/database_optimize_repair_tool/', 'VSE', 'http://www.phpbb.com/customise/db/mod/database_optimize_repair_tool/')";
				$sql[] = "INSERT INTO " . PORTAL_MODS_TABLE . " (mod_id, mod_title, mod_version, mod_version_type, mod_desc, mod_url, mod_author, mod_download) VALUES (106, 'Auto Backup', '1.0.3', '', 'Automatically backup your database using the phpBB3 Cron.', 'http://www.phpbb.com/community/memberlist.php?mode=viewprofile&un=Pico88', 'Pico88', 'http://www.phpbb.com/community/memberlist.php?mode=viewprofile&un=Pico88')";


				// Table: 'phpbb_log_lc_exclude_ip'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . LOG_LC_EXCLUDE_IP_TABLE . " (
					exclude_id mediumint(8) NOT NULL auto_increment,
					exclude_ip varchar(40) NOT NULL default '',
					PRIMARY KEY (exclude_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_config'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_CONFIG_TABLE . " (
				  config_name varchar(255) binary NOT NULL,
				  config_value varchar(255) binary NOT NULL
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_events'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_EVENTS_TABLE . " (
				  event_id mediumint(8) unsigned NOT NULL auto_increment,
				  etype_id tinyint(4) NOT NULL,
				  sort_timestamp bigint(20) unsigned NOT NULL,
				  event_start_time bigint(20) unsigned NOT NULL,
				  event_end_time bigint(20) unsigned NOT NULL,
				  event_all_day tinyint(2) NOT NULL default '0',
				  event_day varchar(10) binary NOT NULL default '',
				  event_subject varchar(255) NOT NULL default '',
				  event_body longblob NOT NULL,
				  poster_id mediumint(8) unsigned NOT NULL default '0',
				  event_access_level tinyint(1) NOT NULL default '0',
				  group_id mediumint(8) unsigned NOT NULL default '0',
				  group_id_list varchar(255) NOT NULL default ',',
				  enable_bbcode tinyint(1) unsigned NOT NULL default '1',
				  enable_smilies tinyint(1) unsigned NOT NULL default '1',
				  enable_magic_url tinyint(1) unsigned NOT NULL default '1',
				  bbcode_bitfield varchar(255) binary NOT NULL default '',
				  bbcode_uid varchar(8) binary NOT NULL,
				  track_rsvps tinyint(1) unsigned NOT NULL default '0',
				  allow_guests tinyint(1) unsigned NOT NULL default '0',
				  rsvp_yes mediumint(8) unsigned NOT NULL default '0',
				  rsvp_no mediumint(8) unsigned NOT NULL default '0',
				  rsvp_maybe mediumint(8) unsigned NOT NULL default '0',
				  recurr_id mediumint(8) unsigned NOT NULL default '0',
				  PRIMARY KEY  (event_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_event_types'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_EVENT_TYPES_TABLE . " (
				  etype_id tinyint(3) unsigned NOT NULL auto_increment,
				  etype_index tinyint(3) unsigned NOT NULL default '0',
				  etype_full_name varchar(255) NOT NULL default '',
				  etype_display_name varchar(255) NOT NULL default '',
				  etype_color varchar(6) binary NOT NULL default '',
				  etype_image varchar(255) binary NOT NULL,
				  PRIMARY KEY  (etype_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_event_types'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_EVENTS_WATCH . " (
				  event_id mediumint(8) unsigned NOT NULL default '0',
				  user_id mediumint(8) unsigned NOT NULL default '0',
				  notify_status tinyint(1) unsigned NOT NULL default '0',
				  track_replies tinyint(1) unsigned NOT NULL default '0',
				  KEY event_id (event_id),
				  KEY user_id (user_id),
				  KEY notify_stat (notify_status)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_event_types'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_RECURRING_EVENTS_TABLE . " (
				  recurr_id mediumint(8) unsigned NOT NULL auto_increment,
				  etype_id tinyint(4) NOT NULL,
				  frequency tinyint(4) NOT NULL default '1',
				  frequency_type tinyint(4) NOT NULL default '0',
				  first_occ_time bigint(20) unsigned NOT NULL,
				  final_occ_time bigint(20) unsigned NOT NULL,
				  event_all_day tinyint(2) NOT NULL default '0',
				  event_duration bigint(20) unsigned NOT NULL,
				  week_index tinyint(2) NOT NULL default '0',
				  first_day_of_week tinyint(1) unsigned NOT NULL default '0',
				  last_calc_time bigint(20) unsigned NOT NULL,
				  next_calc_time bigint(20) unsigned NOT NULL,
				  event_subject varchar(255) NOT NULL default '',
				  event_body longblob NOT NULL,
				  poster_id mediumint(8) unsigned NOT NULL default '0',
				  poster_timezone decimal(5,2) NOT NULL default '0.00',
				  poster_dst tinyint(1) unsigned NOT NULL default '0',
				  event_access_level tinyint(1) NOT NULL default '0',
				  group_id mediumint(8) unsigned NOT NULL default '0',
				  group_id_list varchar(255) NOT NULL default ',',
				  enable_bbcode tinyint(1) unsigned NOT NULL default '1',
				  enable_smilies tinyint(1) unsigned NOT NULL default '1',
				  enable_magic_url tinyint(1) unsigned NOT NULL default '1',
				  bbcode_bitfield varchar(255) binary NOT NULL default '',
				  bbcode_uid varchar(8) binary NOT NULL,
				  track_rsvps tinyint(1) unsigned NOT NULL default '0',
				  allow_guests tinyint(1) unsigned NOT NULL default '0',
				  PRIMARY KEY  (recurr_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_event_types'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_RSVP_TABLE . " (
				  rsvp_id mediumint(8) unsigned NOT NULL auto_increment,
				  event_id mediumint(8) unsigned NOT NULL default '0',
				  poster_id mediumint(8) unsigned NOT NULL default '0',
				  poster_name varchar(255) binary NOT NULL default '',
				  poster_colour varchar(6) binary NOT NULL default '',
				  poster_ip varchar(40) binary NOT NULL default '',
				  post_time int(11) unsigned NOT NULL default '0',
				  rsvp_val tinyint(1) unsigned NOT NULL default '0',
				  rsvp_count smallint(4) unsigned NOT NULL default '1',
				  rsvp_detail mediumtext NOT NULL,
				  bbcode_bitfield varchar(255) binary NOT NULL default '',
				  bbcode_uid varchar(8) binary NOT NULL,
				  bbcode_options tinyint(1) unsigned NOT NULL default '7',
				  PRIMARY KEY  (rsvp_id),
				  KEY event_id (event_id),
				  KEY poster_id (poster_id),
				  KEY eid_post_time (event_id,post_time)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_calendar_event_types'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CALENDAR_WATCH . " (
				  user_id mediumint(8) unsigned NOT NULL default '0',
				  notify_status tinyint(1) unsigned NOT NULL default '0',
				  KEY user_id (user_id),
				  KEY notify_stat (notify_status)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				// Table: 'portal_thanks'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . PORTAL_THANKS_TABLE . " (
				  post_id mediumint(8) NOT NULL default '0',
				  user_id mediumint(8) NOT NULL default '0',
				  KEY post_id (post_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_downloads'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DOWNLOADS_TABLE . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					description blob,
					file_name varchar(255) binary DEFAULT '',
					klicks int(11) DEFAULT '0',
					free tinyint(1) DEFAULT '0',
					extern tinyint(1) DEFAULT '0',
					long_desc blob,
					sort int(11) DEFAULT '0',
					cat int(11) DEFAULT '0',
					hacklist tinyint(1) DEFAULT '0',
					hack_author varchar(255) binary DEFAULT '',
					hack_author_email varchar(255) binary DEFAULT '',
					hack_author_website tinytext,
					hack_version varchar(32) binary DEFAULT '',
					hack_dl_url tinytext,
					test varchar(50) binary DEFAULT '',
					req blob,
					todo blob,
					warning blob,
					mod_desc blob,
					mod_list tinyint(1) DEFAULT '0',
					file_size bigint(20) NOT NULL DEFAULT '0',
					change_time int(11) DEFAULT '0',
					add_time int(11) DEFAULT '0',
					rating smallint(5) NOT NULL DEFAULT '0',
					file_traffic bigint(20) NOT NULL DEFAULT '0',
					overall_klicks int(11) DEFAULT '0',
					approve tinyint(1) DEFAULT '0',
					add_user mediumint(8) DEFAULT '0',
					change_user mediumint(8) DEFAULT '0',
					last_time int(11) DEFAULT '0',
					down_user mediumint(8) NOT NULL DEFAULT '0',
					thumbnail varchar(255) binary NOT NULL DEFAULT '',
					broken tinyint(1) NOT NULL DEFAULT '0',
					mod_desc_uid varchar(8) binary NOT NULL DEFAULT '',
					mod_desc_bitfield varchar(255) binary NOT NULL DEFAULT '',
					mod_desc_flags int(11) unsigned NOT NULL DEFAULT '0',
					long_desc_uid varchar(8) binary NOT NULL DEFAULT '',
					long_desc_bitfield varchar(255) binary NOT NULL DEFAULT '',
					long_desc_flags int(11) unsigned NOT NULL DEFAULT '0',
					desc_uid varchar(8) binary NOT NULL DEFAULT '',
					desc_bitfield varchar(255) binary NOT NULL DEFAULT '',
					desc_flags int(11) unsigned NOT NULL DEFAULT '0',
					warn_uid varchar(8) binary NOT NULL DEFAULT '',
					warn_bitfield varchar(255) binary NOT NULL DEFAULT '',
					warn_flags int(11) unsigned NOT NULL DEFAULT '0',
					dl_topic int(11) NOT NULL DEFAULT '0',
					real_file varchar(255) binary DEFAULT '',
					todo_uid char(8) binary NOT NULL DEFAULT '',
					todo_bitfield varchar(255) binary NOT NULL DEFAULT '',
					todo_flags int(11) unsigned NOT NULL DEFAULT '0',
					file_hash varchar(255) binary NOT NULL DEFAULT '',
					PRIMARY KEY (id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_downloads_cat'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_CAT_TABLE . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					parent int(11) DEFAULT '0',
					path varchar(255) binary DEFAULT '',
					cat_name varchar(255) binary DEFAULT '',
					sort int(11) DEFAULT '0',
					description blob,
					rules blob,
					auth_view tinyint(1) NOT NULL DEFAULT '1',
					auth_dl tinyint(1) NOT NULL DEFAULT '1',
					auth_up tinyint(1) NOT NULL DEFAULT '0',
					auth_mod tinyint(1) NOT NULL DEFAULT '0',
					must_approve tinyint(1) NOT NULL DEFAULT '0',
					allow_mod_desc tinyint(1) NOT NULL DEFAULT '0',
					statistics tinyint(1) NOT NULL DEFAULT '1',
					stats_prune mediumint(8) NOT NULL DEFAULT '0',
					comments tinyint(1) NOT NULL DEFAULT '1',
					cat_traffic bigint(20) NOT NULL DEFAULT '0',
					allow_thumbs tinyint(1) NOT NULL DEFAULT '0',
					auth_cread tinyint(1) NOT NULL DEFAULT '0',
					auth_cpost tinyint(1) NOT NULL DEFAULT '1',
					approve_comments tinyint(1) NOT NULL DEFAULT '1',
					bug_tracker tinyint(1) NOT NULL DEFAULT '0',
					desc_uid varchar(8) binary NOT NULL DEFAULT '',
					desc_bitfield varchar(255) binary NOT NULL DEFAULT '',
					desc_flags int(11) unsigned NOT NULL DEFAULT '0',
					rules_uid varchar(8) binary NOT NULL DEFAULT '',
					rules_bitfield varchar(255) binary NOT NULL DEFAULT '',
					rules_flags int(11) unsigned NOT NULL DEFAULT '0',
					dl_topic_forum int(11) NOT NULL DEFAULT '0',
					dl_topic_text mediumtext NOT NULL,
					cat_icon varchar(255) binary NOT NULL DEFAULT '',
					diff_topic_user tinyint(1) unsigned NOT NULL DEFAULT '0',
					topic_user int(11) unsigned NOT NULL DEFAULT '0',
					topic_more_details tinyint(1) unsigned NOT NULL DEFAULT '1',
					show_file_hash tinyint(1) unsigned NOT NULL DEFAULT '1',
					PRIMARY KEY (id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_auth'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_AUTH_TABLE . " (
					cat_id int(11) NOT NULL,
					group_id int(11) NOT NULL,
					auth_view tinyint(1) NOT NULL DEFAULT '1',
					auth_dl tinyint(1) NOT NULL DEFAULT '1',
					auth_up tinyint(1) NOT NULL DEFAULT '1',
					auth_mod tinyint(1) NOT NULL DEFAULT '0'
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_comments'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_COMMENTS_TABLE . " (
					dl_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					id int(11) NOT NULL DEFAULT '0',
					cat_id int(11) NOT NULL DEFAULT '0',
					user_id mediumint(8) NOT NULL DEFAULT '0',
					username varchar(32) binary NOT NULL DEFAULT '',
					comment_time int(11) NOT NULL DEFAULT '0',
					comment_edit_time int(11) NOT NULL DEFAULT '0',
					comment_text blob,
					approve tinyint(1) NOT NULL DEFAULT '0',
					com_uid varchar(8) binary NOT NULL DEFAULT '',
					com_bitfield varchar(255) binary NOT NULL DEFAULT '',
					com_flags int(11) unsigned NOT NULL DEFAULT '0',
					PRIMARY KEY (dl_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_ratings'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_RATING_TABLE . " (
					dl_id int(11) DEFAULT '0',
					user_id mediumint(8) DEFAULT '0',
					rate_point varchar(10) binary DEFAULT '0'
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_stats'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_STATS_TABLE . " (
					dl_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					id int(11) NOT NULL DEFAULT '0',
					cat_id int(11) NOT NULL DEFAULT '0',
					user_id mediumint(8) NOT NULL DEFAULT '0',
					username varchar(32) binary NOT NULL DEFAULT '',
					traffic bigint(20) NOT NULL DEFAULT '0',
					direction tinyint(1) NOT NULL DEFAULT '0',
					user_ip varchar(40) binary NOT NULL,
					browser varchar(255) binary NOT NULL DEFAULT '',
					time_stamp int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (dl_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_ext_blacklist'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_EXT_BLACKLIST . " (
					extention varchar(10) binary NOT NULL DEFAULT ''
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_banlist'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_BANLIST_TABLE . "(
					ban_id int(11) NOT NULL AUTO_INCREMENT,
					user_id mediumint(8) NOT NULL DEFAULT '0',
					user_ip varchar(40) binary DEFAULT NULL,
					user_agent varchar(50) binary NOT NULL DEFAULT '',
					username varchar(25) binary NOT NULL DEFAULT '',
					guests tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY (ban_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_favorites'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_FAVORITES_TABLE . " (
					fav_id int(11) NOT NULL AUTO_INCREMENT,
					fav_dl_id int(11) NOT NULL DEFAULT '0',
					fav_dl_cat int(11) NOT NULL DEFAULT '0',
					fav_user_id mediumint(8) NOT NULL DEFAULT '0',
					PRIMARY KEY (fav_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_notraf'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_NOTRAF_TABLE . " (
					user_id mediumint(8) NOT NULL DEFAULT '0',
					dl_id int(11) NOT NULL DEFAULT '0'
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_hotlink'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_HOTLINK_TABLE . " (
					user_id mediumint(8) NOT NULL DEFAULT '0',
					session_id varchar(32) binary NOT NULL DEFAULT '',
					hotlink_id varchar(32) binary NOT NULL DEFAULT '',
					`code` varchar(10) binary NOT NULL DEFAULT '-'
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_bug_tracker'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_BUGS_TABLE . " (
					report_id int(11) NOT NULL AUTO_INCREMENT,
					df_id int(11) NOT NULL DEFAULT '0',
					report_title varchar(255) binary DEFAULT '',
					report_text blob,
					report_file_ver varchar(50) binary DEFAULT '',
					report_date int(11) DEFAULT '0',
					report_author_id mediumint(8) NOT NULL DEFAULT '0',
					report_assign_id mediumint(8) NOT NULL DEFAULT '0',
					report_assign_date int(11) DEFAULT '0',
					report_status tinyint(1) NOT NULL DEFAULT '0',
					report_status_date int(11) DEFAULT '0',
					report_php varchar(50) binary DEFAULT '',
					report_db varchar(50) binary DEFAULT '',
					report_forum varchar(50) binary DEFAULT '',
					bug_uid varchar(8) binary NOT NULL DEFAULT '',
					bug_bitfield varchar(255) binary NOT NULL DEFAULT '',
					bug_flags int(11) unsigned NOT NULL DEFAULT '0',
					PRIMARY KEY (report_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_bug_history'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_BUG_HISTORY_TABLE . " (
					report_his_id int(11) NOT NULL AUTO_INCREMENT,
					df_id int(11) NOT NULL DEFAULT '0',
					report_id int(11) NOT NULL,
					report_his_type varchar(10) binary DEFAULT '',
					report_his_date int(11) DEFAULT '0',
					report_his_value mediumtext NOT NULL,
					PRIMARY KEY (report_his_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// -- New on 6.3.3 
				// Table: 'phpbb_dl_rem_traf'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_REM_TRAF_TABLE . " (
					config_name varchar(255) binary NOT NULL DEFAULT '',
					config_value varchar(255) binary NOT NULL DEFAULT '',
					PRIMARY KEY (config_name)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_cat_traf'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_CAT_TRAF_TABLE . " (
					cat_id int(11) unsigned NOT NULL DEFAULT '0',
					cat_traffic_use bigint(20) NOT NULL DEFAULT '0',
					PRIMARY KEY (cat_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// -- New on 6.3.7 
				// Table: 'phpbb_dl_versions'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_VERSIONS_TABLE . " (
					ver_id int(11) unsigned NOT NULL AUTO_INCREMENT,
					dl_id int(11) unsigned NOT NULL DEFAULT '0',
					ver_file_name varchar(255) binary NOT NULL DEFAULT '',
					ver_real_file varchar(255) binary NOT NULL DEFAULT '',
					ver_file_size bigint(20) NOT NULL DEFAULT '0',
					ver_version varchar(32) binary NOT NULL DEFAULT '',
					ver_change_time int(11) unsigned NOT NULL DEFAULT '0',
					ver_add_time int(11) unsigned NOT NULL DEFAULT '0',
					ver_add_user mediumint(8) unsigned NOT NULL DEFAULT '0',
					ver_change_user mediumint(8) unsigned NOT NULL DEFAULT '0',
					ver_file_hash varchar(255) binary NOT NULL DEFAULT '',
					PRIMARY KEY (ver_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// -- New on 6.4.0 
				// Table: 'phpbb_dl_fields'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_FIELDS_TABLE . " (
					field_id int(8) unsigned NOT NULL AUTO_INCREMENT,
					field_name mediumtext NOT NULL,
					field_type int(4) NOT NULL DEFAULT '0',
					field_ident varchar(20) binary NOT NULL DEFAULT '',
					field_length varchar(20) binary NOT NULL DEFAULT '',
					field_minlen varchar(255) binary NOT NULL DEFAULT '',
					field_maxlen varchar(255) binary NOT NULL DEFAULT '',
					field_novalue mediumtext NOT NULL,
					field_default_value mediumtext NOT NULL,
					field_validation varchar(60) binary NOT NULL DEFAULT '',
					field_required tinyint(1) unsigned NOT NULL DEFAULT '0',
					field_active tinyint(1) unsigned NOT NULL DEFAULT '0',
					field_order int(8) unsigned NOT NULL DEFAULT '0',
					PRIMARY KEY (field_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
                // Table: 'phpbb_dl_fields_data'
                $sql[] = "CREATE TABLE IF NOT EXISTS " . DL_FIELDS_DATA_TABLE . " (
                   df_id int(11) unsigned NOT NULL DEFAULT '0',
                   PRIMARY KEY (df_id)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";				
				
				// Table: 'phpbb_dl_fields_lang'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_FIELDS_LANG_TABLE . " (
					field_id int(8) unsigned NOT NULL DEFAULT '0',
					lang_id int(8) unsigned NOT NULL DEFAULT '0',
					option_id int(8) unsigned NOT NULL DEFAULT '0',
					field_type int(4) NOT NULL DEFAULT '0',
					lang_value mediumtext NOT NULL,
					PRIMARY KEY (field_id,lang_id,option_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_dl_lang'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_LANG_TABLE . " (
					field_id int(8) unsigned NOT NULL DEFAULT '0',
					lang_id int(8) unsigned NOT NULL DEFAULT '0',
					lang_name mediumtext NOT NULL,
					lang_explain mediumtext NOT NULL,
					lang_default_value mediumtext NOT NULL,
					PRIMARY KEY (field_id,lang_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// -- New on 6.4.14 
				// Table: 'phpbb_dl_images'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DL_IMAGES_TABLE . " (
					img_id int(8) unsigned NOT NULL AUTO_INCREMENT,
					dl_id int(11) unsigned NOT NULL DEFAULT '0',
					img_name varchar(255) binary NOT NULL DEFAULT '',
					img_title mediumtext NOT NULL,
					PRIMARY KEY (img_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Tapatalk 3.5.0
				// Table: 'phpbb_tapatalk_push_data'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . TAPATALK_PUSH_DATA_TABLE . " (
				  `push_id` int(10) NOT NULL auto_increment,
				  `author` varchar(100) collate utf8_bin NOT NULL default '',
				  `user_id` int(10) NOT NULL default '0',
				  `data_type` char(20) collate utf8_bin NOT NULL default '',
				  `title` varchar(200) collate utf8_bin NOT NULL default '',
				  `data_id` int(10) NOT NULL default '0',
				  `create_time` int(11) unsigned NOT NULL default '0',
				  PRIMARY KEY  (`push_id`),
				  KEY `user_id` (`user_id`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Table: 'phpbb_tapatalk_users'
				$sql[] = "CREATE TABLE IF NOT EXISTS " . TAPATALK_USERS_TABLE . " (
				  `userid` int(10) NOT NULL default '0',
				  `announcement` int(5) NOT NULL default '1',
				  `pm` int(5) NOT NULL default '1',
				  `subscribe` int(5) NOT NULL default '1',
				  `quote` int(5) NOT NULL default '1',
				  `newtopic` int(5) NOT NULL default '1',
				  `tag` int(5) NOT NULL default '1',
				  `updated` int(11) unsigned NOT NULL default '0',
				  PRIMARY KEY  (`userid`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				// Tapatalk 3.5.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('mobiquo_push', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('mobiquo_hide_forum_id', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('mobiquo_guest_okay', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('mobiquo_reg_url', 'ucp.php?mode=register', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalkdir', 'mobiquo', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('mobiquo_is_chrome', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_push_key', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('mobiquo_version', '4.4.1', 0)";
				
				// Tapatalk 3.6.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_android_msg', 'This forum has an app for Android! Click OK to learn more about Tapatalk.', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_android_url', 'market://details?id=com.quoord.tapatalkpro.activity', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_iphone_msg', 'This forum has an app for iPhone! Click OK to learn more about Tapatalk.', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_iphone_url', 'http://itunes.apple.com/us/app/tapatalk-forum-app/id307880732?mt=8', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_ipad_msg', 'This forum has an app for iPad! Click OK to learn more about Tapatalk.', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_ipad_url', 'http://itunes.apple.com/us/app/tapatalk-hd-for-ipad/id481579541?mt=8', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_kindle_msg', 'This forum has an app for Kindle Fire! Click OK to learn more about Tapatalk.', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_kindle_url', 'http://www.amazon.com/gp/mas/dl/android?p=com.quoord.tapatalkpro.activity', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_forum_read_only', '', 0)";
				
				// Tapatalk 3.7.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_allow_register', '', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_iphone_app_id', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_kindle_hd_msg', 'This forum has an app for Kindle Fire HD! Click OK to learn more about Tapatalk.', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_kindle_hd_url', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_android_hd_msg', 'This forum has an app for Android HD! Click OK to learn more about Tapatalk.', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_android_hd_url', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_app_icon_url','mobiquo/smartbanner/tapatalk2.png', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_custom_replace', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_app_desc', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_app_name', '', 0)";

				// Tapatalk 3.8.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_app_banner_msg', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_app_ios_id', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_android_url', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_kindle_url', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_push_slug'', '', 0)";

				// Tapatalk 4.1.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_app_ads_enable', '', 1)";

				// Tapatalk 4.3.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_register_status', '', 2)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_register_group', 'get_group_id('REGISTERED')', 0)";

				// Tapatalk 4.4.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('tapatalk_spam_status', '', 1)";
				
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'a_mobiquo', 1, 0, 0)";
				
				// $sql[] = "ALTER TABLE " . POSTS_TABLE . " CHANGE `post_ua` `post_ua` VARCHAR( 300 ) NULL DEFAULT ''";
				// $sql[] = "ALTER TABLE " . POSTS_TABLE . " CHANGE `screen` `screen` VARCHAR( 12 ) NULL DEFAULT ''";			
				
				// -- Log connections 1.0.3
				$sql[] = "ALTER TABLE " . LOG_TABLE . " ADD log_number MEDIUMINT( 8 ) NOT NULL DEFAULT '1'";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_mod_enable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_disable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_acp_disable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_founder_disable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_admin_disable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_prune_entries', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_prune_day', '7', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " VALUES ('lc_interval', '60', 0)";
				
				// -- Anti Bot Question 1.1.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('enable_abquestion', '0', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('abquestion','','0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('abanswer','','0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('abanswer2','','0')";
				
				// -- phpBB Calendar 0.0.8
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('first_day_of_week', '1')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('index_display_week', '1')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('index_display_next_events', '0')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('hour_mode', '24')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('display_truncated_name', '0')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('prune_frequency', '864000')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('last_prune', '0')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('prune_limit', '2592000')";
				// New entries version 0.1.0
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('display_hidden_groups', '0')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('time_format', 'h:i a')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('date_format', 'M d, Y')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('date_time_format', 'M d, Y h:i a')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('disp_events_only_on_start', '0')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('populate_frequency', '86400')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('last_populate', '0')";
				$sql[] = "INSERT INTO " . CALENDAR_CONFIG_TABLE . " (config_name, config_value) VALUES ('populate_limit', '2592000')";

				$sql[] = "INSERT INTO " . CALENDAR_EVENT_TYPES_TABLE . " (etype_id, etype_index, etype_full_name, etype_display_name, etype_color, etype_image) VALUES (1, 1, 'Generic Event', 'All events', '993333', '')";
				
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'a_calendar', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'm_calendar_edit_other_users_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'm_calendar_delete_other_users_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_view_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_create_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_edit_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_delete_events', 1, 0, 0)";
				// New entries version 0.1.0
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'm_calendar_edit_other_users_rsvps', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_create_recurring_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_create_public_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_create_group_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_create_private_events', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_nonmember_groups', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_track_rsvps', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_allow_guests', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_view_headcount', 1, 0, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'u_calendar_view_detailed_rsvps', 1, 0, 0)";
				
				// -- Thank Post MOD
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_thanked int(11) NOT NULL default '0'";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_thanked_others int(11) NOT NULL default '0'";
				
				// -- Alter table groups
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD COLUMN group_dl_auto_traffic BIGINT(20) DEFAULT '0' NOT NULL";
				
				// -- Alter table users
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_allow_new_download_email TINYINT(1) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_allow_fav_download_email TINYINT(1) DEFAULT '1' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_allow_new_download_popup TINYINT(1) DEFAULT '1' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_allow_fav_download_popup TINYINT(1) DEFAULT '1' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_dl_update_time INT( 11 ) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_new_download TINYINT(1) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_traffic BIGINT(20) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_dl_note_type TINYINT(1) DEFAULT '1' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_dl_sort_fix TINYINT(1) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_dl_sort_opt TINYINT(1) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_dl_sort_dir TINYINT(1) DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_dl_sub_on_index TINYINT(1) DEFAULT 1 NOT NULL";
				
				// -- Configuration values moved into phpbb_config table on 6.3.4.RC1 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_cap_carree_x', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_cap_carree_y', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_cap_char_trans', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_cap_lines', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_click_reset_time', '1260058009', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_delay_auto_traffic', '30', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_delay_post_traffic', '30', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_disable_email', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_disable_popup', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_disable_popup_notify', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_download_dir', 'dl_mod/downloads/', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_download_vc', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_drop_traffic_postdel', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_edit_own_downloads', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_edit_time', '3', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_enable_dl_topic', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_enable_post_dl_traffic', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_ext_new_window', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_guest_stats_show', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_hotlink_action', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_icon_free_for_reg', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_latest_comments', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_limit_desc_on_index', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_links_per_page', '10', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_method', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_method_quota', '20971520', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_mod_version', '6.5.32', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_new_time', '3', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_newtopic_traffic', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_overall_guest_traffic', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_overall_traffic', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_physical_quota', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_posts', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_prevent_hotlink', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_recent_downloads', '10', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_reply_traffic', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_report_broken', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_report_broken_lock', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_report_broken_message', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_report_broken_vc', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_shorten_extern_links', '10', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_show_footer_legend', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_show_footer_stat', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_show_real_filetime', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_sort_preform', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_stats_perm', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_stop_uploads', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_thumb_fsize', '512000', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_thumb_xsize', '800', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_thumb_ysize', '600', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_topic_forum', '2', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_topic_text', 'New arrived file downloads!', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffic_retime', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_upload_traffic_count', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_use_ext_blacklist', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_use_hacklist', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_user_dl_auto_traffic', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_user_traffic_once', '0', 1)";

				// -- New on 6.3.7 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_antispam_posts', '50', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_antispam_hours', '24', 1)";
				
				// -- New on 6.3.8 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffic_off', '0', 1)";
				
				// -- New on 6.4.0 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_diff_topic_user', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_topic_user', '0', 1)";
				
				// -- New on 6.4.1 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_todo_link_onoff', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_uconf_link_onoff', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_topic_more_details', '0', 1)";
				
				// -- New on 6.4.6 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_active', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_off_hide', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_off_now_time', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_off_from', '00:00', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_off_till', '23:59', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_on_admins', '1', 1)";
				
				// -- New on 6.4.13 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_enable_rate', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rate_points', '10', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_enable_jumpbox', '1', 1)";
				
				// -- New on 6.4.14 
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_enable', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_off_action', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_off_text', 'This feed is currently offline.', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_cats', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_cats_select', '-', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_perms', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_number', '10', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_select', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_desc_length', '0', 1)";

				// -- New on 6.4.15
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_desc_shorten', '150', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_rss_new_update', '0', 1)";

				// -- New on 6.5.0
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD COLUMN user_allow_fav_comment_email TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'";

				// -- New on 6.5.6
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffics_overall', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffics_users', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffics_guests', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffics_founder', '1', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffics_overall_groups', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_traffics_users_groups', '0', 1)";

				// -- New on 6.5.11
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_cat_edit', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_overview_link_onoff', '1', 0)";

				// -- New on 6.5.12
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_file_hash_algo', 'md5', 0)";

				// -- New on 6.5.17
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_similar_dl', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_similar_limit', '10', 0)";

				// -- New on 6.5.18
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dl_todo_onoff', '1', 0)";
				
				
				
				// -- Default file extention blacklist values
				$sql[] = "INSERT INTO " . DL_EXT_BLACKLIST . " (extention) VALUES
					('asp'), ('cgi'), ('dhtm'), ('dhtml'), ('exe'), ('htm'), ('html'), ('jar'), ('js'), ('php'), ('php3'), ('pl'), ('sh'), ('shtm'), ('shtml')";
				
				// -- Default banlist values
				$sql[] = "INSERT INTO " . DL_BANLIST_TABLE . " (user_agent) VALUES ('n/a')";
			
				// -- New on 6.3.3 
				$sql[] = "INSERT INTO " . DL_REM_TRAF_TABLE . " (config_name, config_value) VALUES ('dl_remain_traffic', '0')";
				$sql[] = "INSERT INTO " . DL_REM_TRAF_TABLE . " (config_name, config_value) VALUES ('dl_remain_guest_traffic', '0')";


				// -- PM Spy
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'a_pm_spy', 1, 0, 0)";

				// -- Post revisions
				$sql[] = "CREATE TABLE IF NOT EXISTS " . POST_REVISIONS_TABLE . " (
					post_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
					post_subject varchar(100) DEFAULT '' NOT NULL,
					post_text mediumblob NOT NULL,
					bbcode_uid varchar(8) DEFAULT '' NOT NULL,
					post_edit_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
					post_edit_user mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
					post_edit_reason varchar(255) DEFAULT '' NOT NULL,
					KEY post_id (post_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				// -- ACP Announcement Centre
				$sql[] = "CREATE TABLE IF NOT EXISTS " . ANNOUNCEMENTS_CENTRE_TABLE . " (
					announcement_show tinyint (1) NOT NULL,
					announcement_enable_guests tinyint (1) NOT NULL,
					announcement_show_birthdays tinyint (1) NOT NULL,
					announcement_birthday_avatar tinyint (1) NOT NULL,
					announcement_draft text NOT NULL,
					announcement_draft_bbcode_uid varchar(8) DEFAULT '' NOT NULL,
					announcement_draft_bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
					announcement_draft_bbcode_options mediumint(4) DEFAULT 0 NOT NULL,
					announcement_text text NOT NULL,
					announcement_text_bbcode_uid varchar(8) DEFAULT '' NOT NULL,
					announcement_text_bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
					announcement_text_bbcode_options mediumint(4) DEFAULT 0 NOT NULL,
					announcement_text_guests text NOT NULL,
					announcement_text_guests_bbcode_uid varchar(8) DEFAULT 0 NOT NULL,
					announcement_text_guests_bbcode_bitfield varchar(255) DEFAULT 0 NOT NULL,
					announcement_text_guests_bbcode_options mediumint(4) DEFAULT 0 NOT NULL,
					announcement_title varchar(255) NOT NULL default '',
					announcement_title_guests varchar(255) NOT NULL default '',
					announcement_show_group varchar(255) NOT NULL default ''
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . ANNOUNCEMENTS_CENTRE_TABLE  . " (announcement_show, announcement_enable_guests, announcement_show_birthdays, announcement_birthday_avatar, announcement_title, announcement_text, announcement_draft, announcement_title_guests, announcement_text_guests, announcement_show_group) VALUES ('0', '1', '0', '0', 'Site Announcements', 'Site Announcements can be seen here!', 'Draft Announcements can be seen here!', 'Guest Announcements', 'Guest Announcements can be seen here!', '2')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('announcement_show_index', '0', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('announcement_enable', '1', '0')";

				// -- Imprint 0.1.6
				$sql[] = "CREATE TABLE IF NOT EXISTS " . IMPRESSUM_TABLE  . " (
				  name smallint(2) NOT NULL default '0',
				  value varchar(255) binary NOT NULL default '',
				  aktiv smallint(1) NOT NULL default '0'
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (1, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (2, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (3, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (4, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (5, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (6, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (7, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (8, '', 0)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (9, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (10, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (11, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (12, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (13, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (14, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (15, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (16, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (17, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (18, '', 1)";
				$sql[] = "INSERT INTO " . IMPRESSUM_TABLE  . " (name, value, aktiv) VALUES (19, '', 1)";

				// -- SupportTicket Assistant for phpBB Support Sites 1 0 2
				$sql[] = "ALTER TABLE " . FORUMS_TABLE . " ADD COLUMN enable_sts TINYINT(1) DEFAULT '0' NOT NULL";

				// -- Prime Trash Bin 1.0.6
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('f_delete_forever', 0, 1, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('f_undelete', 0, 1, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('m_delete_forever', 1, 1, 0)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('m_undelete', 1, 1, 0)";
				
				$sql[] = "ALTER TABLE " . TOPICS_TABLE . " ADD topic_deleted_from mediumint(8) UNSIGNED DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . TOPICS_TABLE . " ADD topic_deleted_user mediumint(8) UNSIGNED DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . TOPICS_TABLE . " ADD topic_deleted_time int(11) UNSIGNED DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . TOPICS_TABLE . " ADD topic_deleted_reason varchar(255) DEFAULT '' NOT NULL";

				$sql[] = "ALTER TABLE " . POSTS_TABLE . " ADD post_deleted_from mediumint(8) UNSIGNED DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . POSTS_TABLE . " ADD post_deleted_user mediumint(8) UNSIGNED DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . POSTS_TABLE . " ADD post_deleted_time int(11) UNSIGNED DEFAULT '0' NOT NULL";
				$sql[] = "ALTER TABLE " . POSTS_TABLE . " ADD post_deleted_reason varchar(255) DEFAULT '' NOT NULL";

				// -- Auto Groups MOD
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_min_posts MEDIUMINT(8) DEFAULT 0";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_max_posts MEDIUMINT(8) DEFAULT 0";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_min_warnings MEDIUMINT(8) DEFAULT 0";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_max_warnings MEDIUMINT(8) DEFAULT 0";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_min_days MEDIUMINT(8) DEFAULT 0";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_max_days MEDIUMINT(8) DEFAULT 0";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_auto_default TINYINT(1) DEFAULT 0";
				$sql[] = "ALTER TABLE " . USER_GROUP_TABLE . " ADD auto_group TINYINT(1) DEFAULT '0'";

				// -- Quickly Change Your Language Version 0.1.0
				$sql[] = "ALTER TABLE " . SESSIONS_TABLE . " ADD session_lang varchar(30) DEFAULT 'en' NOT NULL";

				// -- phpbb_seo
				$sql[] = "ALTER TABLE " . TOPICS_TABLE . " ADD topic_url VARCHAR(255) binary NOT NULL default '' AFTER topic_deleted_reason";

				// -- Removing old Welcome PM on First Login
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'wpm_enable'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'wpm_send_id'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'wpm_subject'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'wpm_message'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'wpm_preview'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'wpm_variables'";
				
				// -- New Welcome PM on First Login 2.2.5
				$sql[] = "CREATE TABLE IF NOT EXISTS " . WPM_TABLE . " (
					wpm_config_id int(3) NOT NULL,
					wpm_enable tinyint(1) unsigned NOT NULL,
					wpm_send_id mediumint(8) NOT NULL,
					wpm_preview tinyint(1) unsigned NOT NULL,
					wpm_variables varchar(255) NOT NULL,
					wpm_subject varchar(100) NOT NULL,
					wpm_message mediumtext NOT NULL,
					wpm_version varchar(255) NOT NULL,
					PRIMARY KEY	(wpm_config_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . WPM_TABLE . " (wpm_config_id, wpm_enable, wpm_send_id, wpm_preview, wpm_variables, wpm_subject, wpm_message, wpm_version) VALUES (1, 1, 2, 0, '', 'Welcome to {SITE_NAME}!', 'Hello, [b]{USERNAME}[/b]!\n\nWelcome to {SITE_NAME}	({SITE_DESC})\n\nYou registered on [b]{USER_REGDATE}[/b]. According to your input, your email is [b]{USER_EMAIL}[/b] and you live in timezone [b]{USER_TZ}[/b]. It is nice to know that you speak {USER_LANG_LOCAL}.\n\nYou can contact us here: {BOARD_CONTACT} or here: {BOARD_EMAIL}, whichever you prefer, at anytime. Thank you for choosing us.\n\n-Thank you for registering at {SITE_NAME}!\n\nThanks, {SENDER}', '2.2.5')";

				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('country_flags_require', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('country_flags_path', 'images/flags', 0)";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_country_flag varchar(30) binary NOT NULL DEFAULT '0'";
				$sql[] = "ALTER TABLE " . GROUPS_TABLE . " ADD group_country_flag varchar(30) binary NOT NULL DEFAULT '0'";

				$sql[] = "CREATE TABLE IF NOT EXISTS " . COUNTRY_FLAGS_TABLE . " (
					flag_id mediumint(8) unsigned NOT NULL auto_increment,
					flag_country blob NOT NULL,
					flag_code blob NOT NULL,
					flag_image varbinary(255) NOT NULL default '',
					PRIMARY KEY  (flag_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (1, 'Afghanistan', 'af', 'af.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (2, 'Aland Islands', 'ax', 'ax.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (3, 'Albania', 'al', 'al.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (4, 'Algeria', 'dz', 'dz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (5, 'American Samoa', 'as', 'as.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (6, 'Andorra', 'ad', 'ad.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (7, 'Angola', 'ao', 'ao.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (8, 'Anguilla', 'ai', 'ai.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (9, 'Antarctica', 'aq', 'aq.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (10, 'Antigua and Barbuda', 'ag', 'ag.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (11, 'Argentina', 'ar', 'ar.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (12, 'Armenia', 'am', 'am.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (13, 'Aruba', 'aw', 'aw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (14, 'Ascension Island', 'ac', 'ac.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (15, 'Australia', 'au', 'au.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (16, 'Austria', 'at', 'at.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (17, 'Azerbaijan', 'az', 'az.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (18, 'Bahamas', 'bs', 'bs.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (19, 'Bahrain', 'bh', 'bh.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (20, 'Bangladesh', 'bd', 'bd.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (21, 'Barbados', 'bb', 'bb.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (22, 'Belarus', 'by', 'by.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (23, 'Belgium', 'be', 'be.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (24, 'Belize', 'bz', 'bz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (25, 'Benin', 'bj', 'bj.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (26, 'Bermuda', 'bm', 'bm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (27, 'Bhutan', 'bt', 'bt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (28, 'Bolivia', 'bo', 'bo.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (29, 'Bosnia and Herzegowina', 'ba', 'ba.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (30, 'Botswana', 'bw', 'bw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (31, 'Bouvet Island', 'bv', 'bv.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (32, 'Brazil', 'br', 'br.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (33, 'British Indian Ocean Territory', 'io', 'io.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (34, 'Brunei', 'bn', 'bn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (35, 'Bulgaria', 'bg', 'bg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (36, 'Burkina Faso', 'bf', 'bf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (37, 'Burundi', 'bi', 'bi.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (38, 'Cambodia', 'kh', 'kh.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (39, 'Cameroon', 'cm', 'cm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (40, 'Canada', 'ca', 'ca.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (41, 'Cape Verde', 'cv', 'cv.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (42, 'Cayman Islands', 'ky', 'ky.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (43, 'Central African Republic', 'cf', 'cf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (44, 'Chad', 'td', 'td.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (45, 'Chile', 'cl', 'cl.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (46, 'China', 'cn', 'cn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (47, 'Christmas Island', 'cx', 'cx.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (48, 'Cocos (Keeling) Islands', 'cc', 'cc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (49, 'Colombia', 'co', 'co.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (50, 'Comoros', 'km', 'km.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (51, 'Congo', 'cg', 'cg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (52, 'Congo, Democratic Republic of', 'cd', 'cd.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (53, 'Cook Islands', 'ck', 'ck.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (54, 'Costa Rica', 'cr', 'cr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (55, 'Cote d''Ivoire', 'ci', 'ci.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (56, 'Croatia', 'hr', 'hr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (57, 'Cuba', 'cu', 'cu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (58, 'Cyprus', 'cy', 'cy.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (59, 'Czech Republic', 'cz', 'cz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (60, 'Denmark', 'dk', 'dk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (61, 'Djibouti', 'dj', 'dj.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (62, 'Dominica', 'dm', 'dm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (63, 'Dominican Republic', 'do', 'do.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (64, 'Ecuador', 'ec', 'ec.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (65, 'Egypt', 'eg', 'eg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (66, 'El Salvador', 'sv', 'sv.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (67, 'Equatorial Guinea', 'gq', 'gq.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (68, 'Eritrea', 'er', 'er.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (69, 'Estonia', 'ee', 'ee.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (70, 'Ethiopia', 'et', 'et.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (71, 'Falkland Islands', 'fk', 'fk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (72, 'Faroe Islands', 'fo', 'fo.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (73, 'Fiji', 'fj', 'fj.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (74, 'Finland', 'fi', 'fi.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (75, 'France', 'fr', 'fr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (76, 'French Guiana', 'gf', 'gf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (77, 'French Polynesia', 'pf', 'pf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (78, 'French Southern Territories', 'tf', 'tf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (79, 'Gabon', 'ga', 'ga.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (80, 'Gambia', 'gm', 'gm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (81, 'Georgia', 'ge', 'ge.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (82, 'Germany', 'de', 'de.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (83, 'Ghana', 'gh', 'gh.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (84, 'Gibraltar', 'gi', 'gi.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (85, 'Greece', 'gr', 'gr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (86, 'Greenland', 'gl', 'gl.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (87, 'Grenada', 'gd', 'gd.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (88, 'Guadeloupe', 'gp', 'gp.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (89, 'Guam', 'gu', 'gu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (90, 'Guatemala', 'gt', 'gt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (91, 'Guernsey', 'gg', 'gg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (92, 'Guinea', 'gn', 'gn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (93, 'Guinea-Bissau', 'gw', 'gw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (94, 'Guyana', 'gy', 'gy.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (95, 'Haiti', 'ht', 'ht.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (96, 'Heard Island and McDonald Islands', 'hm', 'hm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (97, 'Holy See (Vatican City State)', 'va', 'va.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (98, 'Honduras', 'hn', 'hn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (99, 'Hong Kong', 'hk', 'hk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (100, 'Hungary', 'hu', 'hu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (101, 'Iceland', 'is', 'is.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (102, 'India', 'in', 'in.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (103, 'Indonesia', 'id', 'id.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (104, 'Iran', 'ir', 'ir.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (105, 'Iraq', 'iq', 'iq.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (106, 'Ireland', 'ie', 'ie.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (107, 'Isle of Man', 'im', 'im.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (108, 'Israel', 'il', 'il.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (109, 'Italy', 'it', 'it.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (110, 'Jamaica', 'jm', 'jm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (111, 'Japan', 'jp', 'jp.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (112, 'Jersey', 'je', 'je.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (113, 'Jordan', 'jo', 'jo.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (114, 'Kazakhstan', 'kz', 'kz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (115, 'Kenya', 'ke', 'ke.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (116, 'Kiribati', 'ki', 'ki.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (117, 'Korea, Democratic People''s Republic of', 'kp', 'kp.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (118, 'Korea, Republic of', 'kr', 'kr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (119, 'Kuwait', 'kw', 'kw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (120, 'Kyrgyzstan', 'kg', 'kg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (121, 'Laos', 'la', 'la.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (122, 'Latvia', 'lv', 'lv.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (123, 'Lebanon', 'lb', 'lb.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (124, 'Lesotho', 'ls', 'ls.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (125, 'Liberia', 'lr', 'lr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (126, 'Libyan Arab Jamahiriya', 'ly', 'ly.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (127, 'Liechtenstein', 'li', 'li.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (128, 'Lithuania', 'lt', 'lt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (129, 'Luxembourg', 'lu', 'lu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (130, 'Macao', 'mo', 'mo.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (131, 'Macedonia', 'mk', 'mk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (132, 'Madagascar', 'mg', 'mg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (133, 'Malawi', 'mw', 'mw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (134, 'Malaysia', 'my', 'my.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (135, 'Maldives', 'mv', 'mv.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (136, 'Mali', 'ml', 'ml.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (137, 'Malta', 'mt', 'mt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (138, 'Marshall Island', 'mh', 'mh.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (139, 'Martinique', 'mq', 'mq.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (140, 'Mauritania', 'mr', 'mr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (141, 'Mauritius', 'mu', 'mu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (142, 'Mayotte', 'yt', 'yt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (143, 'Mexico', 'mx', 'mx.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (144, 'Micronesia', 'fm', 'fm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (145, 'Moldova', 'md', 'md.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (146, 'Monaco', 'mc', 'mc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (147, 'Mongolia', 'mn', 'mn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (148, 'Montenegro', 'me', 'me.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (149, 'Montserrat', 'ms', 'ms.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (150, 'Morocco', 'ma', 'ma.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (151, 'Mozambique', 'mz', 'mz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (152, 'Myanmar', 'mm', 'mm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (153, 'Namibia', 'na', 'na.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (154, 'Nauru', 'nr', 'nr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (155, 'Nepal', 'np', 'np.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (156, 'Netherlands', 'nl', 'nl.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (157, 'Netherlands Antilles', 'an', 'an.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (158, 'New Caledonia', 'nc', 'nc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (159, 'New Zealand', 'nz', 'nz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (160, 'Nicaragua', 'ni', 'ni.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (161, 'Niger', 'ne', 'ne.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (162, 'Nigeria', 'ng', 'ng.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (163, 'Niue', 'nu', 'nu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (164, 'Norfolk Island', 'nf', 'nf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (165, 'Northern Mariana Islands', 'mp', 'mp.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (166, 'Norway', 'no', 'no.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (167, 'Oman', 'om', 'om.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (168, 'Pakistan', 'pk', 'pk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (169, 'Palau', 'pw', 'pw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (170, 'Palestine', 'ps', 'ps.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (171, 'Panama', 'pa', 'pa.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (172, 'Papua New Guinea', 'pg', 'pg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (173, 'Paraguay', 'py', 'py.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (174, 'Peru', 'pe', 'pe.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (175, 'Philippines', 'ph', 'ph.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (176, 'Pitcairn', 'pn', 'pn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (177, 'Poland', 'pl', 'pl.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (178, 'Portugal', 'pt', 'pt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (179, 'Puerto Rico', 'pr', 'pr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (180, 'Qatar', 'qa', 'qa.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (181, 'Reunion', 're', 're.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (182, 'Romania', 'ro', 'ro.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (183, 'Russia', 'ru', 'ru.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (184, 'Rwanda', 'rw', 'rw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (185, 'Saint Helena', 'sh', 'sh.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (186, 'Saint Kitts and Nevis', 'kn', 'kn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (187, 'Saint Lucia', 'lc', 'lc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (188, 'Saint Pierre and Miquelon', 'pm', 'pm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (189, 'Saint Vincent and the Grenadines', 'vc', 'vc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (190, 'Samoa', 'ws', 'ws.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (191, 'San Marino', 'sm', 'sm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (192, 'Sao Tome and Principe', 'st', 'st.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (193, 'Saudi Arabia', 'sa', 'sa.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (194, 'Senegal', 'sn', 'sn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (195, 'Serbia', 'rs', 'rs.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (196, 'Seychelles', 'sc', 'sc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (197, 'Sierra Leone', 'sl', 'sl.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (198, 'Singapore', 'sg', 'sg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (199, 'Slovakia', 'sk', 'sk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (200, 'Slovenia', 'si', 'si.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (201, 'Slomon Islands', 'sb', 'sb.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (202, 'Somalia', 'so', 'so.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (203, 'South Africa', 'za', 'za.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (204, 'South Georgia and the South Sandwich Islands', 'gs', 'gs.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (205, 'Spain', 'es', 'es.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (206, 'Sri Lanka', 'lk', 'lk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (207, 'Sudan', 'sd', 'sd.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (208, 'Suriname', 'sr', 'sr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (209, 'Svalbard and Jan Mayen', 'sj', 'sj.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (210, 'Swaziland', 'sz', 'sz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (211, 'Sweden', 'se', 'se.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (212, 'Switzerland', 'ch', 'ch.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (213, 'Syria', 'sy', 'sy.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (214, 'Taiwan', 'tw', 'tw.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (215, 'Tajikistan', 'tj', 'tj.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (216, 'Tanzania', 'tz', 'tz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (217, 'Thailand', 'th', 'th.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (218, 'Timor-Leste', 'tl', 'tl.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (219, 'Togo', 'tg', 'tg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (220, 'Tokelau', 'tk', 'tk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (221, 'Tonga', 'to', 'to.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (222, 'Trinidad and Tobago', 'tt', 'tt.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (223, 'Tunisia', 'tn', 'tn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (224, 'Turkey', 'tr', 'tr.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (225, 'Turkmenistan', 'tm', 'tm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (226, 'Turks and Caicos Islands', 'tc', 'tc.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (227, 'Tuvalu', 'tv', 'tv.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (228, 'Uganda', 'ug', 'ug.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (229, 'Ukraine', 'ua', 'ua.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (230, 'United Arab Emirates', 'ae', 'ae.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (231, 'United Kingdom', 'uk', 'uk.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (232, 'United States', 'us', 'us.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (233, 'United States Minor Outlying Islands', 'um', 'um.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (234, 'Uruguay', 'uy', 'uy.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (235, 'Uzbekistan', 'uz', 'uz.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (236, 'Vanuatu', 'vu', 'vu.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (237, 'Venezuela', 've', 've.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (238, 'Vietnam', 'vn', 'vn.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (239, 'Virgin Islands (British)', 'vg', 'vg.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (240, 'Virgin Islands (US)', 'vi', 'vi.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (241, 'Wallis and Futuna', 'wf', 'wf.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (242, 'Western Sahara', 'eh', 'eh.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (243, 'Yemen', 'ye', 'ye.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (244, 'Zambia', 'zm', 'zm.png')";
				$sql[] = "INSERT INTO " . COUNTRY_FLAGS_TABLE . " (flag_id, flag_country, flag_code, flag_image) VALUES (245, 'Zimbabwe', 'zw', 'zw.png')";

				// -- Gender MOD 1.0.1
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_gender TINYINT(1) UNSIGNED NOT NULL DEFAULT 0";

				// -- Friend list on member view
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('number_friends', '5', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('friend_avatar_size', '80', 0)";
				
				// Contact Admin version 1.0.10, module created above already
				// To be on the save site we remove the former contact 1.0.4 entries first if there are
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_bot_forum'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_bot_user'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_confirm'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_confirm_guests'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_enable'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_max_attempts'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_method'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_reasons'";
				$sql[] = "DELETE FROM " . CONFIG_TABLE . " WHERE (" . CONFIG_TABLE . " . config_name ) = 'contact_version'";
				
				$sql[] = "CREATE TABLE IF NOT EXISTS " . CONTACT_CONFIG_TABLE . " (
					contact_confirm tinyint(1) unsigned NOT NULL DEFAULT '1',
					contact_confirm_guests tinyint(1) unsigned NOT NULL DEFAULT '1',
					contact_max_attempts int(3) unsigned NOT NULL DEFAULT '0',
					contact_method tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_bot_user mediumint(8) unsigned NOT NULL DEFAULT '0',
					contact_bot_forum mediumint(8) unsigned NOT NULL DEFAULT '0',
					contact_reasons mediumtext NOT NULL,
					contact_founder_only tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_bbcodes_allowed tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_smilies_allowed tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_bot_poster tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_attach_allowed tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_urls_allowed tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_username_chk tinyint(1) unsigned NOT NULL DEFAULT '0',
					contact_email_chk tinyint(1) unsigned NOT NULL DEFAULT '0'					
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option_id, auth_option, is_global, is_local, founder_only) VALUES (NULL, 'a_contact', 1, 0, 0)";
				$sql[] = "INSERT INTO " . CONTACT_CONFIG_TABLE . " (contact_confirm, contact_confirm_guests, contact_max_attempts, contact_method, contact_bot_user, contact_bot_forum, contact_reasons, contact_founder_only, contact_bbcodes_allowed, contact_smilies_allowed, contact_bot_poster, contact_attach_allowed, contact_urls_allowed, contact_username_chk, contact_email_chk) VALUES (1, 1, 0, 0, 2, 2, '', 1, 0, 0, 0, 0, 0, 1, 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('contact_version', '1.0.10', 0)";
	
				// User reminder version 1.0.5, module created above already
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_last_auto_run', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_ignore_no_email', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_delete_choice', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_zero_poster_enable', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_zero_poster_days', '15', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_enable', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_days', '60', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_still_enable', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_still_days', '30', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_not_logged_in_enable', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_not_logged_in_days', '20', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_enable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_users_per_page', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_still_opt_zero', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_still_opt_inactive', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_inactive_still_opt_not_logged_in', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_log_opt_script', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_log_opt_users_react', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_log_opt_auto_emails', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_protected_users', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_protected_group', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('user_reminder_bcc_email', '', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('urmod_version', '1.0.5', 0)";

				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_reminder_inactive int(11) NOT NULL DEFAULT '0'";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_reminder_zero_poster int(11) NOT NULL DEFAULT '0'";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_reminder_inactive_still int(11) NOT NULL DEFAULT '0'";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_reminder_not_logged_in int(11) NOT NULL DEFAULT '0'";

				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD INDEX ( `user_reminder_inactive` )"; 
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD INDEX ( `user_reminder_zero_poster` )"; 
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD INDEX ( `user_reminder_inactive_still` )"; 
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD INDEX ( `user_reminder_not_logged_in` )"; 

				// Advanced Block Mod 1.0.6, module created above already
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DNSBL_TABLE . " (
					dnsbl_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
					left_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					right_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					dnsbl_fqdn varchar(255) binary NOT NULL DEFAULT '',
					dnsbl_lookup varchar(255) binary NOT NULL DEFAULT '',
					dnsbl_register tinyint(1) NOT NULL DEFAULT '0',
					dnsbl_weight tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY (dnsbl_id),
					KEY left_right_id (left_id,right_id),
					KEY dnsbl_fqdn (dnsbl_fqdn)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (1, 1, 2, 'sbl-xbl.spamhaus.org', 'http://www.spamhaus.org/query/bl?ip=', 0, 4)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (2, 19, 20, 'bl.spamcop.net', 'http://spamcop.net/bl.shtml?', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (3, 21, 22, 'no-more-funn.moensted.dk', 'http://moensted.dk/spam/no-more-funn/?Submit=Submit&addr=', 1, 0)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (4, 27, 28, 'blackholes.five-ten-sg.com', 'http://www.five-ten-sg.com/blackhole.php?Search=Search&ip=', 1, 0)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (5, 13, 14, 'cbl.abuseat.org', 'http://cbl.abuseat.org/lookup.cgi?.submit=Lookup&ip=', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (6, 15, 16, 'bl.spamcannibal.org', '', 1, 4)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (7, 17, 18, 'dnsbl-1.uceprotect.net', 'http://www.uceprotect.net/en/rblcheck.php?ipr=', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (8, 25, 26, 'dnsbl-2.uceprotect.net', 'http://www.uceprotect.net/en/rblcheck.php?ipr=', 1, 2)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (9, 23, 24, 'dnsbl-3.uceprotect.net', 'http://www.uceprotect.net/en/rblcheck.php?ipr=', 1, 1)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (13, 5, 6, 'spam.dnsbl.sorbs.net', 'http://www.sorbs.net/lookup.shtml?', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (12, 3, 4, 'opm.tornevall.org', 'http://www.stopforumspam.com/api?ip=', 0, 4)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (14, 7, 8, 'smtp.dnsbl.sorbs.net', 'http://www.sorbs.net/lookup.shtml?', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (15, 9, 10, 'socks.dnsbl.sorbs.net', 'http://www.sorbs.net/lookup.shtml?', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (16, 11, 12, 'escalations.dnsbl.sorbs.net', 'http://www.sorbs.net/lookup.shtml?', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (17, 29, 30, 'b.barracudacentral.org', '', 1, 1)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (18, 31, 32, 'rbl.atlbl.net', 'http://search.atlbl.com/search.php?q=', 1, 5)";
				$sql[] = "INSERT INTO " . DNSBL_TABLE . " (dnsbl_id, left_id, right_id, dnsbl_fqdn, dnsbl_lookup, dnsbl_register, dnsbl_weight) VALUES (19, 33, 34, 'access.atlbl.net', 'http://search.atlbl.com/search.php?q=', 1, 5)";

				$sql[] = "ALTER TABLE " . LOG_TABLE . " ADD dnsbl_id mediumint(8) unsigned NOT NULL DEFAULT '0'";

				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('log_check_dnsbl', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('log_email_check_mx', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('check_tz', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('log_check_tz', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('dnsbl_version', '1.0.6', 0)";

				// Email on Birthday version 1.0.1b
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('birthday_emails', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('birthday_run', '')";

				// Mod_Share_On by _Vinny_ 2.1.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_status', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_facebook', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_twitter', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_tuenti', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_sonico', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_friendfeed', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_orkut', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_digg', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_myspace', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_delicious', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_technorati', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_tumblr', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_google', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('so_position', '1')";

				// DM Video
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_VIDEO_TABLE . " (
					video_id int(10) unsigned NOT NULL AUTO_INCREMENT,
					video_title varchar(255) binary NOT NULL DEFAULT '',
					video_url mediumtext NOT NULL,
					video_songtext mediumtext NOT NULL,
					video_singer varchar(255) binary NOT NULL DEFAULT '',
					video_duration varchar(255) binary NOT NULL DEFAULT '',
					video_user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					video_username varchar(32) binary NOT NULL DEFAULT '',
					video_time varchar(15) binary NOT NULL DEFAULT '',
					video_cat_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					video_approval tinyint(3) NOT NULL DEFAULT '0',
					video_counter int(11) unsigned NOT NULL DEFAULT '0',
					video_votetotal int(8) unsigned NOT NULL DEFAULT '0',
					video_votesum int(8) unsigned NOT NULL DEFAULT '0',
					last_poster_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					last_poster_name varchar(255) binary NOT NULL DEFAULT '',
					last_poster_colour varchar(6) binary NOT NULL DEFAULT '',
					last_poster_time int(11) unsigned NOT NULL DEFAULT '0',
					last_poster_cat_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					video_image tinytext NOT NULL,
					bbcode_bitfield varchar(255) binary NOT NULL DEFAULT '',
					bbcode_uid varchar(8) binary NOT NULL DEFAULT '',
					bbcode_options mediumint(4) NOT NULL DEFAULT '0',
					video_reported tinyint(1) NOT NULL DEFAULT '1',
					enable_magic_url tinyint(1) NOT NULL DEFAULT '0',
					enable_smilies tinyint(1) NOT NULL DEFAULT '0',
					enable_bbcode tinyint(1) NOT NULL DEFAULT '0',
					video_announced tinyint(1) NOT NULL DEFAULT '0',
					video_points int(4) NOT NULL DEFAULT '0',
					PRIMARY KEY (video_id),
					KEY video_cat_id (video_cat_id),
					KEY video_user_id (video_user_id),
					KEY video_time (video_time)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_VIDEO_CATS_TABLE . " (
					cat_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
					parent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
					left_id mediumint(8) unsigned NOT NULL DEFAULT '1',
					right_id mediumint(8) unsigned NOT NULL DEFAULT '2',
					cat_parents mediumtext NOT NULL,
					cat_name varchar(255) binary NOT NULL DEFAULT '',
					cat_desc mediumtext NOT NULL,
					cat_desc_uid varchar(8) binary NOT NULL DEFAULT '',
					cat_desc_bitfield varchar(255) binary NOT NULL DEFAULT '',
					cat_desc_options mediumint(8) unsigned NOT NULL DEFAULT '0',
					PRIMARY KEY (cat_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_VIDEO_COMMENT_TABLE . " (
					comment_id int(11) unsigned NOT NULL AUTO_INCREMENT,
					video_id int(11) unsigned NOT NULL DEFAULT '0',
					video_user_id int(8) unsigned NOT NULL DEFAULT '0',
					video_username varchar(255) binary NOT NULL DEFAULT '',
					video_user_colour varchar(6) binary NOT NULL DEFAULT '',
					video_time int(11) unsigned NOT NULL DEFAULT '0',
					video_comment mediumtext NOT NULL,
					comment_bbcode_bitfield varchar(255) binary NOT NULL DEFAULT '',
					comment_bbcode_options int(4) unsigned NOT NULL DEFAULT '0',
					comment_bbcode_uid varchar(8) binary NOT NULL DEFAULT '',
					enable_magic_url tinyint(1) NOT NULL DEFAULT '1',
					enable_smilies tinyint(1) NOT NULL DEFAULT '1',
					enable_bbcode tinyint(1) NOT NULL DEFAULT '1',
					PRIMARY KEY (comment_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
				
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_VIDEO_CONFIG_TABLE . " (
					config_name varchar(255) binary NOT NULL DEFAULT '',
					config_value varchar(255) binary NOT NULL DEFAULT '',
					PRIMARY KEY (config_name)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_page_user', '15')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_page_acp', '15')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('top_views', '10')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('top_ratings', '10')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('newest_videos', '5')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_page_comment', '15')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('copyright_email', 'sample@yourdomain.com')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('copyright_show', 'Sample At Yourdomain Dot Com')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_announce_forum_id', '0')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_announce_enable', '0')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('new_video_pm_from', '2')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('new_video_pm_to', '2')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_points_enable', '0')";
				$sql[] = "INSERT INTO " . DM_VIDEO_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_points_value', '0')";
				
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_VIDEO_RATE_TABLE . " (
					video_id int(8) unsigned NOT NULL DEFAULT '0',
					user_id int(8) unsigned NOT NULL DEFAULT '0',
					video_rating int(8) unsigned NOT NULL DEFAULT '0',
					rating_date int(11) unsigned NOT NULL DEFAULT '0',
					user_ip varchar(16) binary NOT NULL DEFAULT '',
					PRIMARY KEY (video_id,user_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('dm_video_version', '1.0.5')";

				// Topic solved 1.4.5
				$sql[] = "ALTER TABLE " . FORUMS_TABLE . " ADD forum_allow_solve tinyint(1) UNSIGNED NOT NULL DEFAULT 0";
				$sql[] = "ALTER TABLE " . FORUMS_TABLE . " ADD forum_allow_unsolve tinyint(1) UNSIGNED NOT NULL DEFAULT 0";
				$sql[] = "ALTER TABLE " . FORUMS_TABLE . " ADD forum_lock_solved tinyint(1) UNSIGNED NOT NULL DEFAULT 0";
				$sql[] = "ALTER TABLE " . FORUMS_TABLE . " ADD forum_solve_text varchar(25) NULL";
				$sql[] = "ALTER TABLE " . FORUMS_TABLE . " ADD forum_solve_color varchar(7) NOT NULL DEFAULT ''";
				$sql[] = "ALTER TABLE " . TOPICS_TABLE . " ADD topic_solved mediumint(8) UNSIGNED NOT NULL DEFAULT 0";

				// PortalXL Premod 0.4 new block portal referers
				$sql[] = "CREATE TABLE IF NOT EXISTS " . PORTAL_REFERER_TABLE . " (
					referer_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
					referer_ip varchar(40) binary NOT NULL DEFAULT '',
					referer_proxy varchar(40) binary NOT NULL DEFAULT '',
					referer_host varchar(255) binary NOT NULL DEFAULT '',
					referer_hits int(10) NOT NULL DEFAULT '1',
					referer_firstvisit int(11) NOT NULL DEFAULT '0',
					referer_lastvisit int(11) NOT NULL DEFAULT '0',
					referer_enabled tinyint(1) DEFAULT NULL,
					PRIMARY KEY (referer_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				// DM Music Charts 1.0.2
				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_MUSIC_CHARTS_TABLE . " (
					chart_id int(10) unsigned NOT NULL AUTO_INCREMENT,
					chart_song_name varchar(255) binary NOT NULL DEFAULT '',
					chart_artist varchar(255) binary NOT NULL DEFAULT '',
					chart_album varchar(255) binary NOT NULL DEFAULT '',
					chart_picture text NOT NULL,
					chart_year varchar(4) binary NOT NULL DEFAULT '',
					chart_website text NOT NULL,
					chart_video text NOT NULL,
					chart_video_no int(10) unsigned NOT NULL DEFAULT '0',
					chart_poster_id int(10) unsigned NOT NULL DEFAULT '0',
					chart_user_points int(10) unsigned NOT NULL DEFAULT '0',
					chart_cur_pos int(8) unsigned NOT NULL DEFAULT '0',
					chart_hot int(8) unsigned NOT NULL DEFAULT '0',
					chart_not int(8) unsigned NOT NULL DEFAULT '0',
					chart_last_pos int(8) unsigned NOT NULL DEFAULT '0',
					chart_best_pos int(8) unsigned NOT NULL DEFAULT '0',
					bbcode_bitfield varchar(255) binary NOT NULL DEFAULT '',
					bbcode_uid varchar(8) binary NOT NULL DEFAULT '',
					bbcode_options int(8) unsigned NOT NULL DEFAULT '0',
					chart_add_time int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (chart_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (
					config_name varchar(255) binary NOT NULL DEFAULT '',
					config_value varchar(255) binary NOT NULL DEFAULT '',
					PRIMARY KEY (config_name)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('video_page_user', '15')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_max_entries', '100')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_num_top', '10')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_num_last', '5')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_acp_page', '10')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_user_page', '10')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_ups_points', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_check_1', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_check_2', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_check_time', '24')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_1st_place', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_2nd_place', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_3rd_place', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('required_1', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('required_2', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('required_3', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('required_4', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('required_5', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('pm_user', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('pm_enable', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('announce_enable', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('announce_forum', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('points_per_vote', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('voters_points', '0')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('winners_per_page', '10')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('last_voters_winner_id', '')";
				$sql[] = "INSERT INTO " . DM_MUSIC_CHARTS_CONFIG_TABLE . " (config_name, config_value) VALUES ('default_sort', '1')";

				$sql[] = "CREATE TABLE IF NOT EXISTS " . DM_MUSIC_CHARTS_VOTERS_TABLE . " (
					vote_id int(10) unsigned NOT NULL AUTO_INCREMENT,
					vote_user_id int(8) unsigned NOT NULL DEFAULT '0',
					vote_chart_id int(8) unsigned NOT NULL DEFAULT '0',
					vote_rate int(11) unsigned NOT NULL DEFAULT '0',
					PRIMARY KEY (vote_id)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD dm_mc_check_1 tinyint(1) NOT NULL DEFAULT '0'";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD dm_mc_check_2 TINYINT(1) NOT NULL DEFAULT '0'";
				
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('dm_music_charts_version', '1.0.1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_server_time', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_period', '604800')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('chart_start_time', '1262300400')";

				// PayPal IPN Donation 1.1.0
				$sql[] = "CREATE TABLE IF NOT EXISTS " . ACCT_HIST_TABLE . " (
					user_id mediumint(8) DEFAULT '0',
					post_id mediumint(8) DEFAULT '0',
					money float DEFAULT '0',
					plus_minus smallint(5) DEFAULT '0',
					currency varchar(16) binary DEFAULT '',
					`date` int(11) DEFAULT '0',
					`comment` varchar(255) binary DEFAULT '',
					`status` varchar(64) binary DEFAULT '',
					txn_id varchar(64) binary DEFAULT '',
					site varchar(255) binary DEFAULT NULL
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_mod', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('dislay_x_donors', '10')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_start_time', '')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_end_time', '')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_cur_goal', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_description', '')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_to_points', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('paypal_p_acct', '')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('paypal_b_acct', '')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('paypal_currency_code', 'EUR')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_to_posts', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('list_top_donors', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_to_grp_one', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('to_grp_one_amount', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_to_grp_two', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('to_grp_two_amount', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donor_rank_id', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('explanation_postid', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donate_currencies', '')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('usd_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('eur_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('gbp_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('cad_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('jpy_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('aud_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('paypal_support_currency', 'EUR;')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_paypal', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('donors_only', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('czk_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('dkk_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('hkd_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('huf_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('nzd_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('nok_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('pln_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('sgd_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('sek_to_primary', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('chf_to_primary', '0')";

				$sql[] = "ALTER TABLE " . PROFILE_FIELDS_DATA_TABLE . " ADD pf_user_donation varchar(10) DEFAULT NULL";
				$sql[] = "INSERT INTO " . PROFILE_FIELDS_TABLE . " (`field_name`, `field_type`, `field_ident`, `field_length`, `field_minlen`, `field_maxlen`, `field_novalue`, `field_default_value`, `field_validation`, `field_required`, `field_show_on_reg`, `field_show_on_vt`, `field_show_profile`, `field_hide`, `field_no_view`, `field_active`, `field_order`) VALUES ('user_donation', 6, 'user_donation', '10', '10', '10', ' 0- 0-   0', '0- 0-   0', '', 0, 0, 1, 0, 1, 0, 1, 1)";
				$sql[] = "INSERT INTO " . PROFILE_LANG_TABLE . " (`lang_id`, `lang_name`, `lang_explain`, `lang_default_value`) VALUES (1, 'Donation period', 'Donation Period ends on:', '')";
				
				// Scheduled Group Membership 0.0.2
				$sql[] = "ALTER TABLE " . USER_GROUP_TABLE . " ADD group_schedule_days varchar(100) binary DEFAULT NULL";
				$sql[] = "ALTER TABLE " . USER_GROUP_TABLE . " ADD group_schedule_start int(4) DEFAULT NULL";
				$sql[] = "ALTER TABLE " . USER_GROUP_TABLE . " ADD group_schedule_end int(4) DEFAULT NULL";
				$sql[] = "ALTER TABLE " . USER_GROUP_TABLE . " ADD group_schedule_start_date int(11) DEFAULT '0'";
				$sql[] = "ALTER TABLE " . USER_GROUP_TABLE . " ADD group_schedule_end_date int(11) DEFAULT '0'";

				// Browser, Os & Screen 0.3.0
				$sql[] = "ALTER TABLE " . POSTS_TABLE . " ADD post_ua varchar(300) binary NULL DEFAULT ''";
				$sql[] = "ALTER TABLE " . POSTS_TABLE . " ADD screen varchar(12) binary NULL DEFAULT ''";

				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('browser_os_version', '0.3.0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('browser_os_enable', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('browser_os_position', '1')";

				// Colorized Unread Links 1.0.0
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('colorized_links', '#FF0000')";

				// Post links 1.0.1
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('pl_enable', '0')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('pl_link', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('pl_bbcode', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('pl_html', '1')";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('post_links_version', '1.0.1')";

				// Collapse categories 1.1.1
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_category_collapse VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT ''";

				// Auto Backup 1.0.3
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('auto_backup_enable', '0', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('auto_backup_copies', '5', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('auto_backup_gc', '1', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('auto_backup_last_gc', '0', 1)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('auto_backup_filetype', 'text', 0)";
				$sql[] = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('auto_backup_optimize', '0', 0)";

				// Breizh Shout box 1.4.0
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_shout varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '0, , , , 3, 3, 3, 3, 3'";
				$sql[] = "ALTER TABLE " . USERS_TABLE . " ADD user_shoutbox varchar(100) COLLATE utf8_bin NOT NULL DEFAULT 'N,N,N,N,N,N'";
				$sql[] = "ALTER TABLE " . SMILIES_TABLE . " ADD display_on_shout tinyint(1) unsigned NOT NULL DEFAULT '1'";

				$sql[] = "CREATE TABLE IF NOT EXISTS " . SHOUTBOX_TABLE . " (
				  `shout_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				  `shout_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  `shout_time` int(11) unsigned NOT NULL DEFAULT '0',
				  `shout_ip` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `shout_text` mediumtext COLLATE utf8_bin NOT NULL,
				  `shout_bbcode_bitfield` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `shout_bbcode_uid` varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `shout_bbcode_flags` int(11) unsigned NOT NULL DEFAULT '7',
				  `shout_robot` int(1) unsigned NOT NULL DEFAULT '0',
				  `shout_robot_user` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  `shout_forum` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`shout_id`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . SHOUTBOX_TABLE . " (`shout_id`, `shout_user_id`, `shout_time`, `shout_ip`, `shout_text`, `shout_bbcode_bitfield`, `shout_bbcode_uid`, `shout_bbcode_flags`, `shout_robot`, `shout_robot_user`, `shout_forum`) VALUES (1, 0, " . time() . ", '127.0.0.0', 'Mounting of the Mod Breizh Shoutbox 1.4.0 successful!', '', '', 0, 0, 2, 0)";

				$sql[] = "CREATE TABLE IF NOT EXISTS " . SHOUTBOX_PRIV_TABLE . " (
				  `shout_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				  `shout_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  `shout_time` int(11) unsigned NOT NULL DEFAULT '0',
				  `shout_ip` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `shout_text` mediumtext COLLATE utf8_bin NOT NULL,
				  `shout_bbcode_bitfield` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `shout_bbcode_uid` varchar(8) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `shout_bbcode_flags` int(11) unsigned NOT NULL DEFAULT '7',
				  `shout_robot` int(1) unsigned NOT NULL DEFAULT '0',
				  `shout_robot_user` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  `shout_forum` mediumint(8) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`shout_id`)
				) DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

				$sql[] = "INSERT INTO " . SHOUTBOX_PRIV_TABLE . " (`shout_id`, `shout_user_id`, `shout_time`, `shout_ip`, `shout_text`, `shout_bbcode_bitfield`, `shout_bbcode_uid`, `shout_bbcode_flags`, `shout_robot`, `shout_robot_user`, `shout_forum`) VALUES (1, 0, " . time() . ", '127.0.0.0', 'Mounting of the Mod Breizh Shoutbox 1.4.0 successful!', '', '', 0, 0, 2, 0)";

				$shout_config_data = array(
					'shout_another'						=> array('0', 0),
					'shout_color_background'			=> array('transparent', 0),
					'shout_color_background_pop'		=> array('transparent', 0),
					'shout_color_background_priv'		=> array('transparent', 0),
					'shout_color_message'				=> array('008000', 0),
					'shout_color_robot'					=> array('990033', 0),
					'shout_del_acp'						=> array('0', 1),
					'shout_del_acp_priv'				=> array('0', 1),
					'shout_del_auto'					=> array('0', 1),
					'shout_del_auto_priv'				=> array('0', 1),
					'shout_del_purge'					=> array('0', 1),
					'shout_del_purge_priv'				=> array('0', 1),
					'shout_del_user'					=> array('0', 1),
					'shout_del_user_priv'				=> array('0', 1),
					'shout_enable_robot'				=> array('1', 0),
					'shout_exclude_forums'				=> array('0', 0),
					'shout_flood_interval'				=> array('5', 0),
					'shout_forum'						=> array('1', 0),
					'shout_height'						=> array('160', 0),
					'shout_ie_nr'						=> array('5', 0),
					'shout_ie_nr_pop'					=> array('15', 0),
					'shout_ie_nr_priv'					=> array('15', 0),
					'shout_index'						=> array('1', 0),
					'shout_interval'					=> array('3600', 0),
					'shout_last_run'					=> array(time(), 1),
					'shout_last_run_priv'				=> array(time(), 1),
					'shout_log_cron'					=> array('0', 0),
					'shout_log_cron_priv'				=> array('0', 0),
					'shout_lottery'						=> array('0', 0),
					'shout_max_posts'					=> array('450', 0),
					'shout_max_posts_on'				=> array('200', 0),
					'shout_max_posts_on_priv'			=> array('200', 0),
					'shout_max_posts_priv'				=> array('450', 0),
					'shout_non_ie_height_pop'			=> array('415', 0),
					'shout_non_ie_height_priv'			=> array('460', 0),
					'shout_non_ie_nr'					=> array('20', 0),
					'shout_non_ie_nr_pop'				=> array('22', 0),
					'shout_non_ie_nr_priv'				=> array('20', 0),
					'shout_nr'							=> array('2', 1),
					'shout_nr_acp'						=> array('20', 0),
					'shout_nr_log'						=> array('0', 1),
					'shout_nr_log_priv'					=> array('0', 1),
					'shout_nr_priv'						=> array('2', 1),
					'shout_on_cron'						=> array('1', 0),
					'shout_on_cron_priv'				=> array('1', 0),
					'shout_popup_height'				=> array('510', 0),
					'shout_popup_width'					=> array('850', 0),
					'shout_portal'						=> array('1', 0),
					'shout_position_another'			=> array('0', 0),
					'shout_position_index'				=> array('0', 0),
					'shout_position_portal'				=> array('0', 0),
					'shout_post_robot'					=> array('1', 0),
					'shout_post_robot_priv'				=> array('1', 0),
					'shout_prez_form'					=> array('0', 0),
					'shout_prune'						=> array('0', 0),
					'shout_prune_priv'					=> array('0', 0),
					'shout_robbery'						=> array('0', 0),
					'shout_see_buttons'					=> array('0', 0),
					'shout_see_buttons_left'			=> array('0', 0),
					'shout_sessions'					=> array('1', 0),
					'shout_sessions_priv'				=> array('0', 0),
					'shout_sessions_bots'				=> array('0', 0),
					'shout_smilies_height'				=> array('430', 0),
					'shout_smilies_width'				=> array('600', 0),
					'shout_time'						=> array(time(), 0),
					'shout_time_priv'					=> array(time(), 0),
					'shout_title'						=> array('ShoutBox', 0),
					'shout_title_priv'					=> array('Private ShoutBox', 0),
					'shout_topic'						=> array('1', 0),
					'shout_avatar'						=> array('1', 0),
					'shout_avatar_height'				=> array('18', 0),
					'shout_avatar_robot'				=> array('1', 0),
					'shout_bar_option'					=> array('1', 0),
					'shout_bar_option_pop'				=> array('1', 0),
					'shout_bar_option_priv'				=> array('1', 0),
					'shout_birthday'					=> array('1', 0),
					'shout_birthday_hour'				=> array('09', 0),
					'shout_birthday_priv'				=> array('1', 0),
					'shout_button_background'			=> array('1', 0),
					'shout_button_background_pop'		=> array('1', 0),
					'shout_button_background_priv'		=> array('0', 0),
					'shout_color_background_sub'		=> array('grey', 0),
					'shout_color_background_sub_pop'	=> array('grey', 0),
					'shout_color_background_sub_priv'	=> array('grey', 0),
					'shout_correct_minutes'				=> array('1', 0),
					'shout_delete_robot'				=> array('0', 0),
					'shout_edit_robot'					=> array('0', 0),
					'shout_edit_robot_priv'				=> array('0', 0),
					'shout_enable'						=> array('1', 0),
					'shout_hangman'						=> array('0', 0),
					'shout_hangman_priv'				=> array('0', 0),
					'shout_hello'						=> array('1', 0),
					'shout_hello_hour'					=> array('01', 0),
					'shout_hello_priv'					=> array('1', 0),
					'shout_newest'						=> array('1', 0),
					'shout_newest_priv'					=> array('1', 0),
					'shout_pagin_option'				=> array('0', 0),
					'shout_pagin_option_pop'			=> array('0', 0),
					'shout_pagin_option_priv'			=> array('0', 0),
					'shout_panel'						=> array('1', 0),
					'shout_panel_all'					=> array('0', 0),
					'shout_pos_chars'					=> array('1', 0),
					'shout_pos_chars_pop'				=> array('1', 0),
					'shout_pos_chars_priv'				=> array('1', 0),
					'shout_pos_color'					=> array('1', 0),
					'shout_pos_color_pop'				=> array('1', 0),
					'shout_pos_color_priv'				=> array('1', 0),
					'shout_pos_rules'					=> array('1', 0),
					'shout_pos_rules_pop'				=> array('1', 0),
					'shout_pos_rules_priv'				=> array('1', 0),
					'shout_pos_smil'					=> array('1', 0),
					'shout_pos_smil_pop'				=> array('1', 0),
					'shout_pos_smil_priv'				=> array('1', 0),
					'shout_position_forum'				=> array('0', 0),
					'shout_position_topic'				=> array('0', 0),
					'shout_rep_robot'					=> array('1', 0),
					'shout_rep_robot_priv'				=> array('0', 0),
					'shout_robbery_priv'				=> array('0', 0),
					'shout_robot_choice'				=> array('1, 3, 8, 7', 0),
					'shout_rules'						=> array('1', 0),
					'shout_sessions_bots_priv'			=> array('0', 0),
					'shout_sound_del'					=> array('cartoon1.swf', 0),
					'shout_sound_error'					=> array('failure.swf', 0),
					'shout_sound_new'					=> array('elegance.swf', 0),
					'shout_source'						=> array('http://breizh-portal.com/', 0),
					'shout_tracker'						=> array('0', 0),
					'shout_tracker_edit'				=> array('0', 0),
					'shout_tracker_edit_priv'			=> array('0', 0),
					'shout_tracker_priv'				=> array('0', 0),
					'shout_tracker_rep'					=> array('0', 0),
					'shout_tracker_rep_priv'			=> array('0', 0),
					'shout_width_post'					=> array('325', 0),
					'shout_width_post_pop'				=> array('325', 0),
					'shout_width_post_priv'				=> array('325', 0),
					'shout_avatar_none'					=> array('1', 0),
					'shout_avatar_user'					=> array('1', 0),
					'shout_avatar_img'					=> array('no_avatar.gif', 0),
					'shout_avatar_img_robot'			=> array('avatar_robot.png', 0),
					'shout_max_post_chars'				=> array('300', 0),
					'shout_sound_on'					=> array('1', 0),
					'shout_version'				    	=> array('1.4.0', 0),
					'shout_version_full'				=> array('Breizh Shoutbox 1.4.0', 0),
				);
				$this->add_config($shout_config_data);


				// Authorizations
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_overview', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_config', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_traffic', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_categories', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_files', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_permissions', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_stats', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_banlist', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_blacklist', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_toolbox', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_fields', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dl_browser', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_country_flags', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_flags', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_view', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_rate', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video_edit', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_add', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_edit', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_del', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_report', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_comment_view', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_comment_add', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_comment_edit', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_video_comment_del', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video_report', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video_release', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video_cats', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video_config', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video_auto_announce', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_video', 1, 0, 0)";
				
				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_mc_view', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_mc_vote', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_mc_add', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_dm_mc_edit', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_dm_mc_manage', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_shout_manage', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('a_shout_priv', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_post', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_info_s', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_info', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_delete_s', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_delete', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_edit', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_edit_mod', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_smilies', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_color', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_bbcode', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_ignore_flood', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_popup', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ( 'u_shout_view', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_priv', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_purge', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_hide', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_chars', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_image', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_lateral', 1, 0, 0)";

				$auth_option_id = $db->sql_nextid();
				$sql[] = "INSERT INTO " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (5, 0, $auth_option_id, 0, 1)";
				$sql[] = "INSERT INTO " . ACL_OPTIONS_TABLE . " (auth_option, is_global, is_local, founder_only) VALUES ('u_shout_limit_post', 1, 0, 0)";



				// Run the sql statements
				for($i = 0; $i < sizeof($sql); $i++)
				{
					if (!$db->sql_query($sql[$i]))
					{
						$error = $db->sql_error();
						$this->db_error($error['message'], $sql, __LINE__, __FILE__, true);
					}
					else
					{
						$sql_results .= preg_replace('/\t(AND|OR)(\W)/', "\$1\$2", htmlspecialchars(preg_replace('/[\s]*[\n\r\t]+[\n\r\s\t]*/', "\n", $sql[$i]))) . "\n\n";
					}
		
				}
		
				$template->assign_block_vars('checks', array(
					'S_LEGEND'	=> true,
				));
		
				$template->assign_block_vars('checks', array(
					'TITLE'		=> $lang['PORTAL_SQL_UPDATE_DONE'],
					'RESULT'	=> '<textarea rows="10" cols="15">' . trim($sql_results) . '</textarea>',
				));
		
				unset($sql);

				/**
				* We try to set the right CHMOD (write access) for *NIX systems in case config.php is write protected.
				* If not successful, or not allowed by hosting company the user must do this manually before using this installer!
				*/
				@chmod($phpbb_root_path . 'config.' . $phpEx,  0777);

				// Create a lock file to indicate that there is an install in progress
				$fp = @fopen($phpbb_root_path . 'cache/install_lock', 'wb');
				if ($fp === false)
				{
					// We were unable to create the lock file - abort
					$this->p_master->error($lang['UNABLE_WRITE_LOCK'], __LINE__, __FILE__);
				}
				@fclose($fp);
	
				@chmod($phpbb_root_path . 'cache/install_lock', 0666);
	
				// Open, rewrite config.php and chmod all files and directories
				$config_file = $phpbb_root_path . 'config.' . $phpEx;
				
				// Open, retrieve and unset all config.php variables here
				require($config_file);
				$config_data['dbms'] = $dbms;
				$config_data['dbhost'] = $dbhost;
				$config_data['dbport'] = $dbport;
				$config_data['dbname'] = $dbname;
				$config_data['dbuser'] = $dbuser;
				$config_data['dbpasswd'] = $dbpasswd;
				$config_data['table_prefix'] = $table_prefix;
				$config_data['acm_type'] = $acm_type;
				$config_data['load_extensions'] = $load_extensions;
			
				unset($dbms);
				unset($dbhost);
				unset($dbname);
				unset($dbuser);
				unset($dbpasswd);
				unset($table_prefix);
				unset($acm_type);
				unset($load_extensions);
			
				$fp = @fopen($config_file, 'wb');
				if ($fp !== false)
				{
					// Construct config data
					$new_config_data = "<?php\n";
					$new_config_data .= "// phpBB 3.0. Portal XL auto-generated configuration file\n// Do not change anything in this file!\n";
					$new_config_data .= "\$dbms = '" . $config_data['dbms'] . "';\n";
					$new_config_data .= "\$dbhost = '" . $config_data['dbhost'] . "';\n";
					$new_config_data .= "\$dbport = '" . $config_data['dbport'] . "';\n";
					$new_config_data .= "\$dbname = '" . $config_data['dbname'] . "';\n";
					$new_config_data .= "\$dbuser = '" . $config_data['dbuser'] . "';\n";
					$new_config_data .= "\$dbpasswd = '" . $config_data['dbpasswd'] . "';\n\n";
					$new_config_data .= "\$table_prefix = '" . $config_data['table_prefix'] . "';\n";
					$new_config_data .= "\$acm_type = 'file';\n";
					$new_config_data .= "\$load_extensions = '" . $config_data['load_extensions'] . "';\n\n";
					$new_config_data .= "@define('PHPBB_INSTALLED', true);\n";
					$new_config_data .= "// @define('DEBUG', true);\n";
					$new_config_data .= "// @define('DEBUG_EXTRA', true);\n";
					$new_config_data .= "@define('PORTAL', true); // remove this line to pass the portal (remove portal.php from .htaccess)\n";
					$new_config_data .= "@define('PORTAL_INDEX_PAGE', true); // remove above and this line to have a plain phpBB3\n";
					$new_config_data .= '?' . '>'; // Done this to prevent highlighting editors getting confused!
	
					if(!(@fwrite($fp, $new_config_data)))
					{
						trigger_error('Could not write new config.php for unknown reason...');
					}
				
					/**
					* We try to set the right CHMOD (write protected) for *NIX systems.
					* If not successful, or not allowed by hosting company the user must do this manually after installation!
					*/
					@chmod($phpbb_root_path . 'config.' . $phpEx, 0644);
				}
			
				// Remove the lock file
				@unlink($phpbb_root_path . 'cache/install_lock');
				
				// Remove info from prying eyes
				unset($new_config_data);
				unset($config_data);
				add_log('admin', 'Portal XL 5.0 Premod config.php rewritten after install');
				
				// clear cache and log what we did
				$cache->purge();
				add_log('admin', 'Portal XL 5.0 Premod completely installed!');
				add_log('admin', 'LOG_PURGE_CACHE');

			case 'Plain 0.3':
			case 'Plain 0.4':
			case 'Plain 0.5':
			
				$old_upgrade_premod = true;

			break;
		}
	
		$s_hidden_fields = '<input type="hidden" name="mode" value="upgrade_premod" />';
		if ($sub == 'create_table')
		{
			$s_hidden_fields .= '<input type="hidden" name="sub" value="insert_modules" />';
			$l_submit = $lang['INSTALL_NEXT'];
			$body = $lang['PORTAL_FINAL_CONFIGFILE_STEP'];
		}
		else
		{
			$s_hidden_fields .= '<input type="hidden" name="sub" value="insert_modules" />';
			$l_submit = $lang['INSTALL_NEXT'];
			$body = $lang['PORTAL_FINAL_CONFIGFILE_STEP'];
		}		

		$template->assign_vars(array(
			'TITLE'					=> $lang['PORTAL_INSTALL'],
			'BODY'					=> $body,
			'L_SUBMIT'				=> $l_submit,
			'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
			'U_ACTION'				=> $this->p_master->module_url,
						
			'S_LANG_SELECT'			=> '<select id="language" name="language">' . $this->p_master->inst_language_select($language) . '</select>',
		));

		// $this->meta_refresh($url);

		return;
	}
	
	function module_exists($class, $module)
	{
		global $db;
		
		if (!$module || !$class)
		{
			return true;
		}
		$class = $db->sql_escape($class);
		$module = $db->sql_escape($module);
		$sql = 'SELECT module_id 
			FROM ' . MODULES_TABLE . "
			WHERE module_langname = '$module'
			AND module_class = '$class'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		if ($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function add_module($module_data, $db)
	{
		// no module_id means we're creating a new category/module
		if ($module_data['parent_id'])
		{
			$sql = 'SELECT left_id, right_id
				FROM ' . MODULES_TABLE . "
				WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
					AND module_id = " . (int) $module_data['parent_id'];
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
	
			if (!$row)
			{
				return 'PARENT_NO_EXIST';
			}
	
			// Workaround
			$row['left_id'] = (int) $row['left_id'];
			$row['right_id'] = (int) $row['right_id'];
	
			$sql = 'UPDATE ' . MODULES_TABLE . "
				SET left_id = left_id + 2, right_id = right_id + 2
				WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
					AND left_id > {$row['right_id']}";
			$db->sql_query($sql);
	
			$sql = 'UPDATE ' . MODULES_TABLE . "
				SET right_id = right_id + 2
				WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
					AND {$row['left_id']} BETWEEN left_id AND right_id";
			$db->sql_query($sql);
	
			$module_data['left_id'] = (int) $row['right_id'];
			$module_data['right_id'] = (int) $row['right_id'] + 1;
		}
		else
		{
			$sql = 'SELECT MAX(right_id) AS right_id
				FROM ' . MODULES_TABLE . "
				WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
	
			$module_data['left_id'] = (int) $row['right_id'] + 1;
			$module_data['right_id'] = (int) $row['right_id'] + 2;
		}
	
		$sql = 'INSERT INTO ' . MODULES_TABLE . ' ' . $db->sql_build_array('INSERT', $module_data);
		$db->sql_query($sql);
	
		$module_data['module_id'] = $db->sql_nextid();

		// $this->meta_refresh($url);

		return array();
	}
	
	function update_module(&$module_data)
	{
		global $db, $user, $modules;

		if (!isset($module_data['module_id']))
		{
			// no module_id means we're creating a new category/module
			if ($module_data['parent_id'])
			{
				$sql = 'SELECT left_id, right_id
					FROM ' . MODULES_TABLE . "
					WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
						AND module_id = " . (int) $module_data['parent_id'];
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					return 'PARENT_NO_EXIST';
				}

				// Workaround
				$row['left_id'] = (int) $row['left_id'];
				$row['right_id'] = (int) $row['right_id'];

				$sql = 'UPDATE ' . MODULES_TABLE . "
					SET left_id = left_id + 2, right_id = right_id + 2
					WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
						AND left_id > {$row['right_id']}";
				$db->sql_query($sql);

				$sql = 'UPDATE ' . MODULES_TABLE . "
					SET right_id = right_id + 2
					WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
						AND {$row['left_id']} BETWEEN left_id AND right_id";
				$db->sql_query($sql);

				$module_data['left_id'] = (int) $row['right_id'];
				$module_data['right_id'] = (int) $row['right_id'] + 1;
			}
			else
			{
				$sql = 'SELECT MAX(right_id) AS right_id
					FROM ' . MODULES_TABLE . "
					WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$module_data['left_id'] = (int) $row['right_id'] + 1;
				$module_data['right_id'] = (int) $row['right_id'] + 2;
			}

			$sql = 'INSERT INTO ' . MODULES_TABLE . ' ' . $db->sql_build_array('INSERT', $module_data);
			$db->sql_query($sql);

			$module_data['module_id'] = $db->sql_nextid();

			add_log('admin', 'LOG_INSTALL_MODULE_ADD', $module_data['module_class'], $this->lang_install($module_data['module_langname']));
		}
		else
		{
			$row = $modules->get_module_row($module_data['module_id']);

			if ($module_data['module_basename'] && !$row['module_basename'])
			{
				// we're turning a category into a module
				$branch = $modules->get_module_branch($module_data['module_id'], 'children', 'descending', false);

				if (sizeof($branch))
				{
					return array($user->lang['NO_CATEGORY_TO_MODULE']);
				}
			}

			if ($row['parent_id'] != $module_data['parent_id'])
			{
				$modules->move_module($module_data['module_id'], $module_data['parent_id']);
			}

			$update_ary = $module_data;
			unset($update_ary['module_id']);

			$sql = 'UPDATE ' . MODULES_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $update_ary) . "
				WHERE module_class = '" . $db->sql_escape($module_data['module_class']) . "'
					AND module_id = " . (int) $module_data['module_id'];
			$db->sql_query($sql);

			add_log('admin', 'LOG_INSTALL_MODULE_EDIT', $module_data['module_class'], $this->lang_install($module_data['module_langname']));
		}

		return array();
	}
	
	/**
	* Add new items to configuration table
	*/
	function add_config($config_data)
	{
		global $config, $db, $cache, $user;
		
		foreach ($config_data as $config_name => $config_array)
		{
			if (isset($config[$config_name]))
			{
				add_log('admin', 'LOG_INSTALL_CONFIG_UDP', $config_name);
			}
			else
			{
				add_log('admin', 'LOG_INSTALL_CONFIG_ADD', $config_name);
			}
			set_config("{$config_name}", "{$config_array[0]}", "{$config_array[1]}");
		}
		$cache->purge();
		return true;
	}

	/**
	* Meta refresh function to be able to change the global time used
	*/
	function meta_refresh($url)
	{
		global $template;

		$template->assign_vars(array(
			'S_REFRESH'	=> true,
			'META'		=> '<meta http-equiv="refresh" content="15;url=' . $url . '" />')
		);
	}
}

?>
