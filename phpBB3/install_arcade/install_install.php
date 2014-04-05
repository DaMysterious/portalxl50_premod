<?php
/**
*
* @package install
* @version $Id: install_install.php 1663 2011-09-22 12:09:30Z killbill $
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
	if ($this->installed_version)
	{
		return;
	}

	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'INSTALL',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 10,
		'module_subs'		=> '',
		'module_stages'		=> array('INTRO', 'REQUIREMENTS', 'INSTALL'),
		'module_reqs'		=> ''
	);
}

/**
* Installation
* @package install
*/
class install_install extends module
{
	function install_install(&$p_master)
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
					'TITLE'			=> $user->lang['INSTALL_INTRO'],
					'BODY'			=> $user->lang['INSTALL_INTRO_BODY'],
					'L_SUBMIT'		=> $user->lang['NEXT_STEP'],
					'U_ACTION'		=> $this->p_master->module_url . "?mode=$mode&amp;sub=requirements",
				));

			break;

			case 'requirements':
				$this->page_title = $user->lang['STAGE_REQUIREMENTS'];

				$this->p_master->requirements($mode, $sub);

			break;

			case 'install':
				$this->install($mode, $sub);

			break;
		}

		$this->tpl_name = 'install_install';
	}

	/**
	* Obtain the information required to connect to the database
	*/
	function install($mode, $sub)
	{
		global $table_prefix, $user, $template, $cache, $phpEx, $phpbb_root_path, $phpbb_db_tools, $arcade_mod_config;

		$this->page_title = $user->lang['STAGE_INSTALL_ARCADE'];

		foreach ($arcade_mod_config['schema_data'] as $table_name => $table_data)
		{
			$phpbb_db_tools->sql_create_table($table_name, $table_data);
		}

		// Load all the arcade data
		if (!empty($arcade_mod_config['data_file']['add']))
		{
			$this->p_master->load_data($arcade_mod_config['data_file']['add']);
		}

		$arcade_configs = array(array('arcade_resetdate', '0'),
								array('arcade_startdate', time()),
								array('reports_open', '0'),
								array('total_downloads', 0, true),
								array('total_plays', 0, true),
								array('total_plays_time', 0, true),
								array('acp_items_per_page', '10'),
								array('announce_forum', '0'),
								array('announce_game', '0'),
								array('announce_message', '[game_image]\n[b]Game Name:[/b] [game_name]\n[b]Game Description:[/b] [game_desc]\n\n[game_link]\n[download_link]\n[stats_link]'),
								array('announce_subject', '[GAME RELEASE] [game_name]'),
								array('arcade_disable', '0'),
								array('arcade_disable_msg', ''),
								array('arcade_leaders', '5'),
								array('arcade_leaders_header', '3'),
								array('auto_disable', '0'),
								array('auto_disable_end', ''),
								array('auto_disable_start', ''),
								array('backup_limit', '75'),
								array('cache_time', '4'),
								array('cat_backup_path', 'arcade/backup/'),
								array('cat_image_path', 'arcade/images/cats/'),
								array('cm_currency_id', '0'),
								array('copyright', 'Powered by <a href="http://phpbbarcade.origon.dk">phpBB Arcade</a> &copy 2011'),
								array('display_desc', '1'),
								array('display_game_type', '0'),
								array('display_memberlist', '1'),
								array('display_viewtopic', '1'),
								array('download_anonymous_interval', '0'),
								array('download_interval', '0'),
								array('download_list', '0'),
								array('download_list_message', ''),
								array('download_list_per_page', '50'),
								array('download_on_demand', '1'),
								array('facebook_background_color', ''),
								array('facebook_color_scheme', 'light'),
								array('facebook_enable_like', '0'),
								array('facebook_font_type', 'arial'),
								array('facebook_layout_style', 'standard'),
								array('facebook_show_faces', '0'),
								array('facebook_table_height', '0'),
								array('facebook_table_width', '250'),
								array('flash_version', '10.0.0.0'),
								array('founder_cat_login', '0'),
								array('game_autosize', '0'),
								array('game_cost', '5'),
								array('game_height', '0'),
								array('game_path', 'arcade/games/'),
								array('game_popup_icon_enabled', '1'),
								array('game_reward', '10'),
								array('game_scores', '5'),
								array('game_width', '0'),
								array('game_zero_negative_score', '0'),
								array('games_per_page', '10'),
								array('games_sort_dir', 'a'),
								array('games_sort_order', 'n'),
								array('image_path', 'arcade/images/'),
								array('jackpot_maximum', '0'),
								array('jackpot_minimum', '0'),
								array('latest_highscores', '5'),
								array('least_downloaded', '5'),
								array('least_popular', '5'),
								array('limit_play', '0'),
								array('limit_play_days', '7'),
								array('limit_play_posts', '10'),
								array('limit_play_total_posts', '10'),
								array('links_cats', '1'),
								array('links_index', '1'),
								array('links_stats', '1'),
								array('load_list', '1'),
								array('longest_held_scores', '5'),
								array('most_downloaded', '5'),
								array('most_popular', '5'),
								array('new_games_delay', '5'),
								array('newest_games', '10'),
								array('online_time', '0'),
								array('override_user_sort', '0'),
								array('parse_bbcode', '1'),
								array('parse_links', '1'),
								array('parse_smilies', '1'),
								array('play_anonymous_interval', '0'),
								array('playfree_reward', '0'),
								array('play_interval', '0'),
								array('played_colour', 'cdcdcd'),
								array('protect_amod', '1'),
								array('protect_ibpro', '1'),
								array('protect_v3arcade', '1'),
								array('resolution_select', '1'),
								array('search_anonymous_interval', '0'),
								array('search_cats', '1'),
								array('search_filter_age', '0'),
								array('search_filter_password', '0'),
								array('search_index', '1'),
								array('search_interval', '0'),
								array('search_stats', '1'),
								array('send_arcade_pm', '1'),
								array('session_length', '72000'),
								array('stat_items_per_page', '10'),
								array('unpack_game_path', 'arcade/install/'),
								array('use_jackpot', '0'),
								array('use_points', '0'),
								array('version', $arcade_mod_config['version']['current']),
								array('welcome_cats', '1'),
								array('welcome_index', '1'),
								array('welcome_stats', '1')
							);

		$this->p_master->arcade_config_add($arcade_configs);

		// Alter some existing tables
		if (!empty($arcade_mod_config['schema_changes']))
		{
			$phpbb_db_tools->perform_schema_changes($arcade_mod_config['schema_changes']);
		}

		$this->p_master->add_permissions($arcade_mod_config['permission_options']['arcade'], 'arcade');
		$this->p_master->add_roles($arcade_mod_config['roles']['arcade'], 'arcade');

		// Add the admin permissions for the arcade acp modules
		$this->p_master->add_permissions($arcade_mod_config['permission_options']['phpbb']);
		// Add the admin permissions for the arcade acp modules to the correct roles
		$this->p_master->update_roles(array('ROLE_ADMIN_STANDARD', 'ROLE_ADMIN_FULL'), $arcade_mod_config['permission_options']['phpbb']['global']);

		// Add the modules
		foreach ($arcade_mod_config['modules'] as $modules)
		{
			$this->p_master->create_modules($modules['parent_module_data'], $modules['module_data']);
		}

		// Purge the cache
		$this->p_master->cache_purge(array('auth', 'imageset', 'theme', 'template', ''));

		add_log('admin', 'LOG_ARCADE_INSTALL', $arcade_mod_config['version']['current']);

		$perm_msg = '</p><div class="errorbox"><h3>' . $user->lang['WARNING'] . '</h3><p>' . $user->lang['PERMISSIONS_WARNING'] . '</p></div>';

		$template->assign_vars(array(
			'TITLE'		=> $user->lang['INSTALL_CONGRATS'],
			'BODY'		=> $user->lang['STAGE_INSTALL_ARCADE_EXPLAIN'] . '<br /><br />' . $perm_msg . sprintf($user->lang['INSTALL_CONGRATS_EXPLAIN'], $arcade_mod_config['version']['current']),
			'L_SUBMIT'	=> $user->lang['CAT_VERIFY'],
			'U_ACTION'	=> $this->p_master->module_url . '?mode=verify&amp;sub=verify',
		));
	}
}

?>