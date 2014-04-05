<?php
/**
*
* @package install
* @version $Id: install_update.php 1663 2011-09-22 12:09:30Z killbill $
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
	if (!$this->installed_version || ($this->installed_version && version_compare(str_replace('rc', 'RC', strtolower($this->installed_version)), str_replace('rc', 'RC', strtolower($arcade_mod_config['version']['current'])), '>=')))
	{
		return;
	}

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'UPDATE',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 20,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'REQUIREMENTS', 'UPDATE'),
		'module_reqs'		=> ''
	);
}

/**
* Installation
* @package install
*/
class install_update extends module
{
	function install_update(&$p_master)
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
					'TITLE'		=> $user->lang['UPDATE_INTRO'],
					'BODY'		=> $user->lang['UPDATE_INTRO_BODY'],
					'L_SUBMIT'	=> $user->lang['NEXT_STEP'],
					'U_ACTION'	=> $this->p_master->module_url . "?mode=$mode&amp;sub=requirements",
				));

			break;

			case 'requirements':
				$this->page_title = $user->lang['STAGE_REQUIREMENTS'];
				$this->p_master->requirements($mode, $sub);
			break;

			case 'update':
				$this->update($mode, $sub);
			break;
		}

		$this->tpl_name = 'install_install';
	}

	/**
	* Obtain the information required to connect to the database
	*/
	function update($mode, $sub)
	{
		global $user, $template, $cache, $phpEx, $phpbb_root_path, $file_functions, $phpbb_db_tools, $db, $arcade_mod_config;

		$this->page_title = $user->lang['STAGE_UPDATE_ARCADE'];

		switch ($this->p_master->installed_version)
		{
			case '1.0.0':
				// The last thing we do is update the game install files.  This is only necessary to stop the
				// error message in the acp create install file module about the install file changing.  The
				// old install files will still install in the new arcade (1.1.x or higher).  This could also
				// take some time of boards with large amounts of games, so it may have to be tweaked.
				$this->p_master->convert_install_file();

				$sql = 'SELECT game_installdate
						FROM ' . ARCADE_GAMES_TABLE . '
						ORDER BY game_installdate ASC';
				$result = $db->sql_query_limit($sql, 1);
				$startdate = (int) $db->sql_fetchfield('game_installdate');
				$db->sql_freeresult($result);

				$startdate = ($startdate) ? $startdate : time();

				$sql = 'SELECT SUM(total_plays) AS games_played, SUM(total_time) AS games_time
						FROM ' . ARCADE_PLAYS_TABLE;
				$result = $db->sql_query($sql);
				$play_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$sql = 'SELECT SUM(total) as downloads
						FROM ' . ARCADE_DOWNLOAD_TABLE;
				$result = $db->sql_query($sql);
				$total_downloads = (int) $db->sql_fetchfield('downloads');
				$db->sql_freeresult($result);

				if ($db->sql_layer === 'sqlite')
				{
					$sql = 'SELECT COUNT(user_id) as total_users_played
							FROM (
								SELECT DISTINCT user_id
								FROM ' . ARCADE_SCORES_TABLE . '
							)';
				}
				else
				{
					$sql = 'SELECT COUNT(DISTINCT user_id) as total_users_played
							FROM ' . ARCADE_SCORES_TABLE;
				}

				$result = $db->sql_query($sql);
				$total_users_played = (int) $db->sql_fetchfield('total_users_played');
				$db->sql_freeresult($result);

				$sql = 'SELECT COUNT(report_id) as reports_open
						FROM ' . ARCADE_REPORTS_TABLE . '
						WHERE report_closed = ' . ARCADE_REPORT_OPEN;
				$result = $db->sql_query($sql);
				$reports_open = (int) $db->sql_fetchfield('reports_open');
				$db->sql_freeresult($result);

				$total_plays = (int) $play_data['games_played'];
				$total_times = (int) $play_data['games_time'];

				// Now create the table
				$phpbb_db_tools->sql_create_table(ARCADE_USERS_TABLE, $arcade_mod_config['schema_data'][ARCADE_USERS_TABLE]);

				// Lets move the old data over...
				$sql = 'SELECT user_id, user_arcade_permissions, user_arcade_perm_from, user_arcade_pm, games_per_page, games_sort_dir, games_sort_order
						FROM ' . USERS_TABLE . '
						ORDER BY user_id';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					// We will only move the data over if the user changed the default values.
					// The other users will be created when they first visit the arcade.
					if ($row['user_arcade_pm'] != true || $row['games_per_page'] != 0 || $row['games_sort_dir'] != 'a' || $row['games_sort_order'] != 'n')
					{
						$db->sql_query('INSERT INTO ' . ARCADE_USERS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
								'user_id'						=> $row['user_id'],
								'user_arcade_permissions'		=> $row['user_arcade_permissions'],
								'user_arcade_perm_from'			=> $row['user_arcade_perm_from'],
								'user_arcade_pm'				=> $row['user_arcade_pm'],
								'games_per_page'				=> $row['games_per_page'],
								'games_sort_dir'				=> $row['games_sort_dir'],
								'games_sort_order'				=> $row['games_sort_order'],
							)
						));
					}
				}
				$db->sql_freeresult($result);

				switch ($db->sql_layer)
				{
					case 'sqlite':
					case 'firebird':
						$db->sql_query('DELETE FROM ' . ARCADE_SESSIONS_TABLE);
					break;

					default:
						$db->sql_query('TRUNCATE TABLE ' . ARCADE_SESSIONS_TABLE);
					break;
				}

				// Process schema changes
				$phpbb_db_tools->perform_schema_changes($arcade_mod_config['schema_changes']['update']['2.0.RC1']);

				$arcade_configs = array(array('arcade_startdate', $startdate),
										array('arcade_resetdate', '0'),
										array('backup_limit', '75'),
										array('cat_backup_path', 'arcade/backup/'),
										array('download_anonymous_interval', '0'),
										array('download_interval', '0'),
										array('download_on_demand', '1'),
										array('facebook_font_type', 'arial'),
										array('founder_exempt', '1'),
										array('game_popup_icon_enabled', '1'),
										array('load_list', '1'),
										array('playfree_reward', '0'),
										array('play_anonymous_interval', '0'),
										array('play_interval', '0'),
										array('reports_open', $reports_open),
										array('search_anonymous_interval', '0'),
										array('search_filter_age', '1'),
										array('search_filter_password', '1'),
										array('search_interval', '0'),
										array('session_length', '72000'),
										array('total_downloads', $total_downloads, true),
										array('total_plays', $total_plays, true),
										array('total_plays_time', $total_times, true),
				);

				$this->p_master->arcade_config_add($arcade_configs);
				$this->p_master->arcade_set_config('flash_version', '10.0.0.0');
				$this->p_master->arcade_set_config('copyright', 'Powered by <a href="http://phpbbarcade.origon.dk">phpBB Arcade</a> &copy 2011');

				// Remove unneeded config values
				$this->p_master->arcade_remove_config('pm_subject');
				$this->p_master->arcade_remove_config('pm_message');

				// Add new permission options
				$this->p_master->add_permissions($arcade_mod_config['permission_options']['update']['2.0.RC1']['phpbb']);
				$this->p_master->add_permissions($arcade_mod_config['permission_options']['update']['2.0.RC1']['arcade'], 'arcade');

				// Add new permission options to correct roles
				$this->p_master->update_roles(array('ROLE_ARCADE_STANDARD'), array('c_re_rate'), 'arcade');
				$this->p_master->update_roles(array('ROLE_ARCADE_FULL'), $arcade_mod_config['permission_options']['update']['2.0.RC1']['arcade']['local'], 'arcade');

				// Add new roles
				$roles_ary = array();
				foreach ($arcade_mod_config['roles']['arcade'] as $role)
				{
					if ($role['role_type'] != 'c_')
					{
						$roles_ary[] = $role;
					}
				}

				$this->p_master->add_roles($roles_ary, 'arcade');

				// Remove and then re-add the modules
				$this->p_master->remove_modules($arcade_mod_config['modules_remove']);
				foreach ($arcade_mod_config['modules'] as $modules)
				{
					$this->p_master->create_modules($modules['parent_module_data'], $modules['module_data']);
				}
			break;

			default:
			break;
		}

		// Set arcade version config value to latest version
		$this->p_master->arcade_set_config('version', $arcade_mod_config['version']['current']);

		// Purge the cache
		$this->p_master->cache_purge(array('auth', 'imageset', 'theme', 'template', ''));

		add_log('admin', 'LOG_ARCADE_UPDATE', $this->p_master->installed_version, $arcade_mod_config['version']['current']);

		$perm_msg = '</p><div class="errorbox"><h3>' . $user->lang['WARNING'] . '</h3><p>' . $user->lang['PERMISSIONS_WARNING'] . '</p></div>';

		$template->assign_vars(array(
			'TITLE'		=> $user->lang['INSTALL_CONGRATS'],
			'BODY'		=> $user->lang['STAGE_UPDATE_ARCADE_EXPLAIN'] . '<br /><br />' . $perm_msg . sprintf($user->lang['UPDATE_CONGRATS_EXPLAIN'], $arcade_mod_config['version']['current']),
			'L_SUBMIT'	=> $user->lang['CAT_VERIFY'],
			'U_ACTION'	=> $this->p_master->module_url . '?mode=verify&amp;sub=verify',
		));
	}
}

?>