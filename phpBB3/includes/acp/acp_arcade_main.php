<?php
/**
*
* @package acp
* @version $Id: acp_arcade_main.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_arcade_main
{
	var $u_action;
	var $tpl_name;
	var $page_title;

	function main($id, $mode)
	{
		global $db, $user, $config, $auth, $template, $phpbb_root_path, $phpEx;
		global $arcade, $auth_arcade, $arcade_cache, $arcade_config;

		include($phpbb_root_path . 'arcade/includes/common.' . $phpEx);
		define('IN_ARCADE_NO_LOAD_GAMES', true);
		// Initialize arcade auth
		$auth_arcade->acl($user->data);
		// Initialize arcade class
		$arcade = new arcade_admin();

		$this->tpl_name = 'acp_arcade_main';

		if (request_var('version_check', false))
		{
			$mode = 'version';
		}

		if ($user->data['user_type'] == USER_FOUNDER)
		{
			$this->maintenance_options($mode);
		}

		switch ($mode)
		{
			case 'main':
				$action = request_var('action', '');

				if($action)
				{
					if (!confirm_box(true))
					{
						switch ($action)
						{
							case 'reset_arcade':
							case 'reset_scores_all':
							case 'reset_users_data':
							case 'reset_users_settings':
							case 'reset_downloads':
							case 'purge_sessions':
							case 'reset_jackpot':
							case 'reset_points':
							case 'resync_totals_data':
								$confirm		= true;
								$confirm_lang	= 'ARCADE_' . strtoupper($action);
							break;

							default:
								$confirm		= false;
							break;
						}

						if ($confirm)
						{
							confirm_box(false, $confirm_lang, build_hidden_fields(array(
								'i'			=> $id,
								'mode'		=> $mode,
								'action'	=> $action,
							)));
						}
					}
					else
					{

						if (!$auth->acl_get('a_arcade'))
						{
							trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						switch ($action)
						{
							case 'reset_arcade':
							case 'reset_scores_all':
							case 'reset_users_data':
							case 'reset_users_settings':
							case 'reset_downloads':
							case 'purge_sessions':
							case 'reset_jackpot':
							case 'reset_points':
								if ($action == 'purge_sessions' && (int) $user->data['user_type'] !== USER_FOUNDER)
								{
									trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
								}

								$this->reset($action);
							break;

							case 'resync_totals_data':
								$arcade->sync('total_data', 'all');
								$arcade_cache->destroy('_arcade_games_filesize');
							break;

							default:
								trigger_error('NO_MODE', E_USER_ERROR);
							break;
						}

						add_log('admin', 'LOG_ARCADE_' . strtoupper($action));
						trigger_error($user->lang['ARCADE_' . strtoupper($action) . '_DONE'] . adm_back_link($this->u_action));
					}
				}

				$latest_version_info = $update_to_date = false;
				if (($latest_version_info = $this->obtain_latest_version_info(request_var('versioncheck_force', false))) === false)
				{
					$template->assign_var('S_VERSIONCHECK_FAIL', true);
				}
				else
				{
					$latest_version_info = explode("\n", $latest_version_info);
					$latest_version		 = str_replace('rc', 'RC', strtolower(trim($latest_version_info[0])));
					$current_version	 = str_replace('rc', 'RC', strtolower($arcade_config['version']));
					$update_to_date		 = version_compare(str_replace('rc', 'RC', strtolower($current_version)), str_replace('rc', 'RC', strtolower($latest_version)), '<') ? false : true;
				}

				$this->page_title = 'ACP_ARCADE_MAIN';

				$template->assign_vars(array(
					'S_VERSION_UP_TO_DATE'		=> $update_to_date,
					'S_USER_FOUNDER'			=> ($user->data['user_type'] == USER_FOUNDER) ? true : false,
					'S_ARCADE_MANAGE'			=> ($auth->acl_get('a_arcade')) ? true : false,
					'S_SHOW_POINTS'				=> ($arcade->points['installed']) ? true : false,

					'U_ACTION'					=> $this->u_action,

					'U_VERSIONCHECK'			=> $this->u_action . '&amp;version_check=1',
					'U_VERSIONCHECK_FORCE'		=> $this->u_action . '&amp;versioncheck_force=1',

					'ARCADE_START'				=> $user->format_date($arcade_config['arcade_startdate']),
					'STAT_INSTALL_GAMES'		=> $arcade->get_total('games'),
					'STAT_DOWNLOAD_GAMES'		=> $arcade_config['total_downloads'],
					'STAT_DOWNLOAD_GAMES_DAY'	=> $this->data_per_day($arcade_config['total_downloads']),
					'STAT_PLAYS_GAMES'			=> $arcade_config['total_plays'],
					'STAT_PLAYS_GAMES_DAY'		=> $this->data_per_day($arcade_config['total_plays']),
					'STAT_USERS_PLAYED'			=> $arcade->get_total('users_played'),
					'ARCADE_RESETDATE'			=> $arcade_config['arcade_resetdate'] ? $user->format_date($arcade_config['arcade_resetdate']) : $user->lang['ACP_ARCADE_RESET_NO_DATE'],
					'ARCADE_VERSION'			=> ($user->data['user_type'] == USER_FOUNDER) ? '<strong><a href="' . $this->u_action . '&amp;version_check=1' . '" style="color:#'. (($update_to_date) ? '228822' : 'BC2A4D') .';" title="' . $user->lang['MORE_INFORMATION'] . '">' . $arcade_config['version'] . '</a></strong>&nbsp;[&nbsp;<a id="version" href="' . $this->u_action . '&amp;versioncheck_force=1' . '">' . $user->lang['VERSIONCHECK_FORCE_UPDATE'] . '</a>&nbsp;]' : '<strong style="color:#'. (($update_to_date) ? '228822' : 'BC2A4D') .';">' . $arcade_config['version'] . '</strong>',
					'ARCADE_GAMES_FILESIZE'		=> get_formatted_filesize($arcade_cache->obtain_arcade_games_filesize())
				));

			break;

			case 'version':
				$this->page_title = 'ACP_VERSION_CHECK';
				$user->add_lang('install');
				$current_version = str_replace(' ', '.', $arcade_config['version']);

				// Get current and latest version
				$errstr = '';
				$errno = 0;

				$info = $this->obtain_latest_version_info(request_var('versioncheck_force', false), true);

				if ($info === false)
				{
					trigger_error('VERSIONCHECK_FAIL', E_USER_WARNING);
				}

				$info = explode("\n", $info);
				$latest_version   = trim($info[0]);
				$announcement_url = str_replace('&', '&amp;', trim($info[1]));
				$update_link = append_sid($phpbb_root_path . 'install/index.' . $phpEx);

				// next feature release
				$next_feature_version = $next_feature_announcement_url = false;
				if (isset($info[2]) && trim($info[2]) !== '')
				{
					$next_feature_version = trim($info[2]);
					$next_feature_announcement_url = trim($info[3]);
				}

				$up_to_date = (version_compare(str_replace('rc', 'RC', strtolower($current_version)), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;

				$template->assign_vars(array(
					'S_VERSION_CHECK'		=> true,
					'S_UP_TO_DATE'			=> $up_to_date,
					'U_VERSIONCHECK_FORCE'	=> $this->u_action . '&amp;version_check=1&amp;versioncheck_force=1',

					'LATEST_VERSION'		=> '<strong style="color:#228822">' . $latest_version . '</strong>',
					'CURRENT_VERSION'		=> '<strong style="color:#'. ($up_to_date ? '228822' : 'BC2A4D') .'">' . $current_version . '</strong>',
					'NEXT_FEATURE_VERSION'	=> $next_feature_version,

					'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['ARCADE_UPDATE_INSTRUCTIONS'], $announcement_url, $update_link),
					'UPGRADE_INSTRUCTIONS'	=> $next_feature_version ? sprintf($user->lang['ARCADE_UPGRADE_INSTRUCTIONS'], $next_feature_version, $next_feature_announcement_url) : false,
				));

			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}

	function reset($mode)
	{
		global $db, $user, $arcade_cache, $arcade, $arcade_config;

		switch ($mode)
		{
			case 'reset_arcade':
				$tables = array(ARCADE_FAVS_TABLE, ARCADE_RATING_TABLE, ARCADE_DOWNLOAD_TABLE);
				foreach ($tables as $table)
				{
					switch ($db->sql_layer)
					{
						case 'sqlite':
						case 'firebird':
							$db->sql_query('DELETE FROM ' . $table);
						break;

						default:
							$db->sql_query('TRUNCATE TABLE ' . $table);
						break;
					}
				}

				$sql_ary = array(
					'game_votetotal'		=> 0,
					'game_download_total'	=> 0,
					'game_votesum'			=> 0,
					'game_cost'				=> 0,
					'game_reward'			=> 0,
					'game_jackpot'			=> (float) $arcade_config['jackpot_minimum'],
					'game_use_jackpot'		=> 0,
				);

				$arcade->set_config('total_downloads', 0, true);

			case 'reset_scores_all':
				$tables = array(ARCADE_SCORES_TABLE, ARCADE_PLAYS_TABLE);
				foreach ($tables as $table)
				{
					switch ($db->sql_layer)
					{
						case 'sqlite':
						case 'firebird':
							$db->sql_query('DELETE FROM ' . $table);
						break;

						default:
							$db->sql_query('TRUNCATE TABLE ' . $table);
						break;
					}
				}

				$sql_ary['game_plays']		= 0;
				$sql_ary['game_highscore']	= 0;
				$sql_ary['game_highuser']	= 0;
				$sql_ary['game_highdate']	= 0;

				$sql = 'UPDATE ' . ARCADE_GAMES_TABLE. '
					SET ' . $db->sql_build_array('UPDATE', $sql_ary);
				$db->sql_query($sql);

				$sql_ary = array(
					'cat_plays'						=> 0,
					'cat_last_play_game_id'			=> 0,
					'cat_last_play_game_name'		=> '',
					'cat_last_play_user_id'			=> 0,
					'cat_last_play_score'			=> 0,
					'cat_last_play_time'			=> 0,
					'cat_last_play_username'		=> '',
					'cat_last_play_user_colour'		=> '',
				);

				if ($mode == 'reset_arcade')
				{
					$sql_ary['cat_cost'] = 0;
					$sql_ary['cat_reward'] = 0;
					$sql_ary['cat_use_jackpot'] = 0;
				}

				$sql = 'UPDATE ' . ARCADE_CATS_TABLE . '
					SET ' .	$db->sql_build_array('UPDATE', $sql_ary);
				$db->sql_query($sql);

				$arcade->sync('category');

				$arcade->set_config('arcade_resetdate', time());

				$arcade_cache->destroy('sql', ARCADE_CATS_TABLE);
				$arcade_cache->destroy('sql', ARCADE_GAMES_TABLE);
				$arcade_cache->destroy('sql', ARCADE_SCORES_TABLE);

				$arcade_cache->destroy('_arcade_leaders');
				$arcade_cache->destroy('_arcade_leaders_all');

				$arcade->set_config('total_plays', 0, true);
				$arcade->set_config('total_plays_time', 0, true);
			break;

			case 'reset_users_data':
				$tables = array(ARCADE_FAVS_TABLE, ARCADE_RATING_TABLE);
				foreach ($tables as $table)
				{
					switch ($db->sql_layer)
					{
						case 'sqlite':
						case 'firebird':
							$db->sql_query('DELETE FROM ' . $table);
						break;

						default:
							$db->sql_query('TRUNCATE TABLE ' . $table);
						break;
					}
				}

				$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
						SET game_votetotal = 0, game_votesum = 0';
				$db->sql_query($sql);
			break;

			case 'reset_users_settings':
				$sql_ary = array(
							'user_arcade_pm'	=> 1,
							'games_per_page'	=> 0,
							'games_sort_dir'	=> 'a',
							'games_sort_order'	=> 'n'
						);

				$sql = 'UPDATE ' . ARCADE_USERS_TABLE  . '
						SET ' . $db->sql_build_array('UPDATE', $sql_ary);
				$db->sql_query($sql);
			break;

			case 'reset_downloads':
				switch ($db->sql_layer)
				{
					case 'sqlite':
					case 'firebird':
						$db->sql_query('DELETE FROM ' . ARCADE_DOWNLOAD_TABLE);
					break;

					default:
						$db->sql_query('TRUNCATE TABLE ' . ARCADE_DOWNLOAD_TABLE);
					break;
				}

				$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
						SET game_download_total = 0';
				$db->sql_query($sql);

				$arcade->set_config('total_downloads', 0, true);
			break;

			case 'reset_points':
				$sql_ary = array(
					'cat_cost'			=> 0,
					'cat_reward'		=> 0,
					'cat_use_jackpot'	=> 0,
				);

				$sql = 'UPDATE ' . ARCADE_CATS_TABLE . '
					SET ' .	$db->sql_build_array('UPDATE', $sql_ary);
				$db->sql_query($sql);

				$sql_ary = array(
					'game_cost'			=> 0,
					'game_reward'		=> 0,
					'game_use_jackpot'	=> 0,
				);

				$arcade_cache->destroy('sql', ARCADE_CATS_TABLE);
			case 'reset_jackpot':
				$sql_ary['game_jackpot'] = (float) $arcade_config['jackpot_minimum'];

				$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
						SET ' .	$db->sql_build_array('UPDATE', $sql_ary);
				$db->sql_query($sql);

				$arcade_cache->destroy('sql', ARCADE_GAMES_TABLE);
			break;

			case 'purge_sessions':
				$tables = array(ARCADE_ACCESS_TABLE, ARCADE_SESSIONS_TABLE);

				foreach ($tables as $table)
				{
					switch ($db->sql_layer)
					{
						case 'sqlite':
						case 'firebird':
							$db->sql_query("DELETE FROM $table");
						break;

						default:
							$db->sql_query("TRUNCATE TABLE $table");
						break;
					}
				}
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}

	function data_per_day($data)
	{
		global $arcade, $arcade_config;

		$date = ($arcade_config['arcade_resetdate']) ? $arcade_config['arcade_resetdate'] : $arcade_config['arcade_startdate'];

		$arcadedays = (time() - $date) / 86400;
		$data_per_day = sprintf('%.2f', $data / $arcadedays);

		if ($data_per_day > $data)
		{
			$data_per_day = $data;
		}

		return $data_per_day;
	}

	function maintenance_options($mode = '')
	{
		global $phpbb_root_path, $phpEx, $arcade_config;

		$version_requirement = $arcade_action = '';

		if (file_exists($phpbb_root_path . 'install/config.' . $phpEx))
		{
			global $user, $config, $template, $arcade;

			define('IN_INSTALL', true);
			include($phpbb_root_path . 'install/config.' . $phpEx);

			if (isset($arcade_mod_config))
			{
				$phpbb_version = $version_oldest = true;

				if (version_compare(str_replace('rc', 'RC', strtolower($arcade_config['version'])), str_replace('rc', 'RC', strtolower($arcade_mod_config['version']['current'])), '<'))
				{
					if (version_compare(str_replace('rc', 'RC', strtolower($config['version'])), str_replace('rc', 'RC', strtolower($arcade_mod_config['version']['phpbb'])), '<'))
					{
						$phpbb_version = false;
						$version_requirement .= sprintf($user->lang['ACP_ARCADE_MAINTENANCE_UPDATE_PHPBB'], $arcade_mod_config['version']['phpbb'], $arcade_mod_config['version']['current']);
					}

					if (version_compare(str_replace('rc', 'RC', strtolower($arcade_config['version'])), str_replace('rc', 'RC', strtolower($arcade_mod_config['version']['oldest'])), '<'))
					{
						$version_oldest = false;
						$version_requirement .= ($phpbb_version == false) ? '<br />' : '';
						$version_requirement .= sprintf($user->lang['ACP_ARCADE_MAINTENANCE_UPDATE_ARCADE'], $arcade_mod_config['version']['oldest'], $arcade_mod_config['version']['oldest'], $arcade_mod_config['version']['current']);
					}

					if ($version_requirement)
					{
						$template->assign_var('ARCADE_REQUIREMENT', $version_requirement);
					}
				}

				if ($mode == 'main')
				{
					$user->add_lang('mods/arcade_install');

					if ((version_compare(str_replace('rc', 'RC', strtolower($arcade_config['version'])), str_replace('rc', 'RC', strtolower($arcade_mod_config['version']['current'])), '<')) && $phpbb_version && $version_oldest)
					{
						$update_link	= append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=update');

						$arcade_action .= ' - ';
						$arcade_action .= '<a style="color: #228822;" onclick="window.open(this.href); return false;" href="'. $update_link .'" title="'. $user->lang['ACP_ARCADE'] .' '. $user->lang['CAT_UPDATE'] .'"><b>'. $user->lang['CAT_UPDATE'] .'</b></a>';
					}

					if (version_compare(str_replace('rc', 'RC', strtolower($arcade_config['version'])), str_replace('rc', 'RC', strtolower($arcade_mod_config['version']['current'])), '=='))
					{
						$uninstall_link = append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=uninstall');
						$verify_link	= append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=verify');

						$arcade_action .= '<br />';
						$arcade_action .= '<a style="color: #BC2A4D;" onclick="window.open(this.href); return false;" href="'. $uninstall_link .'" title="'. $user->lang['ACP_ARCADE'] .' '. $user->lang['CAT_UNINSTALL'] .'"><b>'. $user->lang['CAT_UNINSTALL'] .'</b></a>';
						$arcade_action .= ' &bull; ';
						$arcade_action .= '<a style="color: #228822;" onclick="window.open(this.href); return false;" href="'. $verify_link .'" title="'. $user->lang['ACP_ARCADE'] .' '. $user->lang['CAT_VERIFY'] .'"><b>'. $user->lang['CAT_VERIFY'] .'</b></a>';
					}

					if ($arcade_action)
					{
						$template->assign_var('ARCADE_VERSION_ACTION', $arcade_action);
					}
				}
				unset($arcade_mod_config);
			}
		}
	}

	/**
	 * Obtains the latest version information
	 *
	 * @param bool $force_update Ignores cached data. Defaults to false.
	 * @param bool $warn_fail Trigger a warning if obtaining the latest version information fails. Defaults to false.
	 * @param int $ttl Cache version information for $ttl seconds. Defaults to 86400 (24 hours).
	 *
	 * @return string | false Version info on success, false on failure.
	 */
	function obtain_latest_version_info($force_update = false, $warn_fail = false, $ttl = 86400)
	{
		global $arcade_cache;

		$info = $arcade_cache->get('_arcade_versioncheck');

		if ($info === false || $force_update)
		{
			$errstr = '';
			$errno = 0;

			$info = get_remote_file('jatek-vilag.com', '/update', 'arcade_version.txt', $errstr, $errno);

			if ($info === false)
			{
				$arcade_cache->destroy('_arcade_versioncheck');
				if ($warn_fail)
				{
					trigger_error($errstr . adm_back_link($this->u_action), E_USER_WARNING);
				}
				return false;
			}

			$arcade_cache->put('_arcade_versioncheck', $info, $ttl);
		}

		return $info;
	}
}

?>