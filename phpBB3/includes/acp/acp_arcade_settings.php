<?php
/**
*
* @package acp
* @version $Id: acp_arcade_settings.php 1663 2011-09-22 12:09:30Z killbill $
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
class acp_arcade_settings
{
	var $u_action;
	var $tpl_name;
	var $page_title;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $auth_arcade, $template, $arcade;
		global $config, $arcade_cache, $arcade_config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $file_functions;

		include($phpbb_root_path . 'arcade/includes/common.' . $phpEx);
		define('IN_ARCADE_NO_LOAD_GAMES', true);
		// Initialize arcade auth
		$auth_arcade->acl($user->data);
		// Initialize arcade class
		$arcade = new arcade_admin();

		$form_key = 'acp_arcade';
		add_form_key($form_key);

		$submit		= (isset($_POST['submit'])) ? true : false;

		switch ($mode)
		{
			case 'settings':
					$display_vars = array(
						'title'								=> 'ACP_ARCADE_SETTINGS_GENERAL',
						'vars'	=> array(
							'legend1'						=> 'ACP_ARCADE_SETTINGS_GENERAL',
							'arcade_disable'				=> array('lang' => 'DISABLE_ARCADE',					'validate' => 'bool',		'type' => 'custom',			'explain'	=> true,	'method' => 'arcade_disable'),
							'arcade_disable_msg'			=> false,
							'auto_disable'					=> array('lang' => 'ARCADE_AUTO_DISABLE',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'auto_disable_start'			=> array('lang' => 'ARCADE_AUTO_DISABLE_START',			'validate' => 'string',		'type' => 'text:5:5',		'explain'	=> true),
							'auto_disable_end'				=> array('lang' => 'ARCADE_AUTO_DISABLE_END',			'validate' => 'string',		'type' => 'text:5:5',		'explain'	=> true),
							'backup_limit'					=> array('lang' => 'ARCADE_BACKUP_LIMIT',				'validate' => 'int:2:500',	'type' => 'text:3:4',		'explain'	=> true),
							'load_list'						=> array('lang' => 'ARCADE_LOAD_LIST_LOADING',			'validate' => 'bool', 		'type' => 'custom',			'explain'	=> true,	'method' => 'load_list'),
							'download_list'					=> array('lang' => 'ARCADE_DOWNLOAD_LIST', 				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'download_list_message'			=> array('lang' => 'ARCADE_DOWNLOAD_LIST_MESSAGE',		'validate' => 'string',		'type' => 'textarea:8:25',	'explain'	=> true),
							'download_on_demand'			=> array('lang' => 'ARCADE_DOWNLOAD_ON_DEMAND', 		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'new_games_delay'				=> array('lang' => 'ARCADE_NEW_GAMES_DELAY',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true,	'append' => ' ' . $user->lang['DAYS']),
							'flash_version'					=> array('lang' => 'ARCADE_ACP_FLASH_VERSION',			'validate' => 'string:7:15','type' => 'text:16:15',		'explain'	=> true),
							'parse_bbcode'					=> array('lang' => 'ARCADE_COMMENTS_BBCODE', 			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'parse_smilies'					=> array('lang' => 'ARCADE_COMMENTS_SMILIES', 			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'parse_links'					=> array('lang' => 'ARCADE_COMMENTS_LINKS', 			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'played_colour'					=> array('lang' => 'ARCADE_PLAYED_GAMES_COLOUR',		'validate' => 'string',		'type' => 'custom',			'explain'	=> true,	'method' => 'arcade_colour_palette'),
							'cache_time'					=> array('lang' => 'ARCADE_CACHE_TIME',					'validate' => 'int:1',		'type' => 'text:4:5',		'explain'	=> true,	'append' => ' ' . $user->lang['HOURS']),
							'online_time'					=> array('lang' => 'ARCADE_ONLINE_TIME',				'validate' => 'int:0',		'type' => 'text:4:5',		'explain'	=> true,	'append' => ' ' . $user->lang['MINUTES']),
							'session_length'				=> array('lang' => 'ARCADE_SESSION_LENGTH',				'validate' => 'int:3600',	'type' => 'text:4:5',		'explain'	=> true,	'append' => ' ' . $user->lang['SECONDS']),

							'legend2'						=> 'ACP_ARCADE_SETTINGS_FLOOD',
							'play_anonymous_interval'		=> array('lang' => 'ARCADE_PLAY_INTERVAL_GUEST',		'validate' => 'int:0',		'type' => 'text:3:10',		'explain'	=> true,	'append' => ' ' . $user->lang['SECONDS']),
							'play_interval'					=> array('lang' => 'ARCADE_PLAY_INTERVAL',				'validate' => 'int:0',		'type' => 'text:3:10',		'explain'	=> true,	'append' => ' ' . $user->lang['SECONDS']),
							'download_anonymous_interval'	=> array('lang' => 'ARCADE_DOWNLOAD_INTERVAL_GUEST',	'validate' => 'int:0',		'type' => 'text:3:10',		'explain'	=> true,	'append' => ' ' . $user->lang['SECONDS']),
							'download_interval'				=> array('lang' => 'ARCADE_DOWNLOAD_INTERVAL',			'validate' => 'int:0',		'type' => 'text:3:10',		'explain'	=> true,	'append' => ' ' . $user->lang['SECONDS']),
							'search_anonymous_interval'		=> array('lang' => 'ARCADE_SEARCH_INTERVAL_GUEST',		'validate' => 'int:0',		'type' => 'text:3:10',		'explain'	=> true,	'append' => ' ' . $user->lang['SECONDS']),
							'search_interval'				=> array('lang' => 'ARCADE_SEARCH_INTERVAL',			'validate' => 'int:0',		'type' => 'text:3:10',		'explain' 	=> true,	'append' => ' ' . $user->lang['SECONDS']),
						));

				if ($user->data['user_type'] == USER_FOUNDER)
				{
						$display_vars['vars'] += array(
							'legend3'						=> 'ACP_ARCADE_SETTINGS_FOUNDER',
							'founder_exempt'				=> array('lang' => 'ARCADE_FOUNDER_EXEMPT',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain' 	=> true),
							'search_filter_age'				=> array('lang' => 'ARCADE_SEARCH_FILTER_AGE',			'validate' => 'int',		'type' => 'select',			'explain'	=> true,	'method'	=> 'search_check_select'),
							'search_filter_password'		=> array('lang' => 'ARCADE_SEARCH_FILTER_PASSWORD',		'validate' => 'int',		'type' => 'select',			'explain'	=> true,	'method'	=> 'search_check_select'),
						);
				}

				if ($arcade->points['installed'] && $auth->acl_get('a_arcade_points_settings'))
				{
						$display_vars['vars'] += array(
							'legend4'						=> 'ACP_ARCADE_POINTS',
							'use_points'					=> array('lang' => 'ARCADE_USE_POINTS',					'validate' => 'bool', 		'type' => 'custom',			'method'	=>	'points_detect',		'explain' => true),
						);

					if ($arcade->points['type'] == ARCADE_CASH_MOD)
					{
						$display_vars['vars'] += array(
							'cm_currency_id'				=> array('lang' => 'ARCADE_CM_CURRENCY',				'validate' => 'string',		'type' => 'select',			'method'	=>	'cm_currency_select',	'explain' => true),
						);
					}

						$display_vars['vars'] += array(
							'game_cost'						=> array('lang' => 'ARCADE_GLOBAL_GAME_COST', 			'validate' => 'string',		'type' => 'text:10:10', 	'explain'	=> true),
							'game_reward'					=> array('lang' => 'ARCADE_GLOBAL_GAME_REWARD',			'validate' => 'string',		'type' => 'text:10:10', 	'explain'	=> true),
							'use_jackpot'					=> array('lang' => 'ARCADE_GLOBAL_USE_JACKPOT',			'validate' => 'bool',		'type' => 'radio:yes_no', 	'explain'	=> true),
							'jackpot_maximum'				=> array('lang' => 'ARCADE_JACKPOT_MAXIMUM',			'validate' => 'string',		'type' => 'text:10:10', 	'explain'	=> true),
							'jackpot_minimum'				=> array('lang' => 'ARCADE_JACKPOT_MINIMUM',			'validate' => 'string',		'type' => 'text:10:10', 	'explain'	=> true),
							'playfree_reward'				=> array('lang' => 'ARCADE_PLAYFREE_REWARD',			'validate' => 'bool',		'type' => 'radio:yes_no', 	'explain'	=> true),
						);
				}

						$display_vars['vars'] += array(
							'legend5'						=> 'ACP_SUBMIT_CHANGES'
						);
			break;

			case 'game':
					$display_vars = array(
						'title'								=> 'ACP_ARCADE_SETTINGS_GAME',
						'vars'	=> array(
							'legend1'						=> 'ACP_ARCADE_SETTINGS_GAME',
							'game_popup_icon_enabled'		=> array('lang' => 'ARCADE_GAME_POPUP_ICON_ENABLE',		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'game_zero_negative_score'		=> array('lang' => 'ARCADE_GAME_ZERO_NEGATIVE_SCORE',	'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'game_autosize'					=> array('lang' => 'ARCADE_GLOBAL_GAME_AUTOSIZE',		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'game_width'					=> array('lang' => 'ARCADE_GLOBAL_GAME_WIDTH',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'game_height'					=> array('lang' => 'ARCADE_GLOBAL_GAME_HEIGHT',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'override_user_sort'			=> array('lang' => 'ARCADE_OVERRIDE_USER_SORT', 		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'games_sort_order'				=> array('lang' => 'ARCADE_GAMES_SORT_ORDER',  			'validate' => 'string',		'type' => 'select',			'explain'	=> true,	'method'	=> 'games_sort_order_select'),
							'games_sort_dir'				=> array('lang' => 'ARCADE_GAMES_SORT_DIR',  			'validate' => 'string',		'type' => 'select',			'explain'	=> true,	'method'	=> 'games_sort_dir_select'),
							'games_per_page'				=> array('lang' => 'ARCADE_GAMES_PER_PAGE',				'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'display_desc'					=> array('lang' => 'ARCADE_DISPLAY_DESC', 				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'display_game_type'				=> array('lang' => 'ARCADE_DISPLAY_GAME_TYPE', 			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'resolution_select'				=> array('lang' => 'ARCADE_RESOLUTION_SELECT', 			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),

							'legend2'						=> 'ACP_SUBMIT_CHANGES'
						));
			break;

			case 'feature':
					$display_vars = array(
						'title'								=> 'ACP_ARCADE_SETTINGS_FEATURE',
						'vars'	=> array(
							'legend1'						=> 'ACP_ARCADE_SETTINGS_PM',
							'send_arcade_pm'				=> array('lang' => 'ARCADE_SEND_PM',					'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),

							'legend2'						=> 'ACP_ARCADE_SETTINGS_ANNOUNCE',
							'announce_game'					=> array('lang' => 'ARCADE_ANNOUNCE_GAME',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'announce_forum'				=> array('lang' => 'ARCADE_ANNOUNCE_FORUM',				'validate' => 'string',		'type' => 'select', 		'explain'	=> true,	'method'	=> 'arcade_announce_game'),
							'announce_subject'				=> array('lang' => 'ARCADE_ANNOUNCE_SUBJECT',			'validate' => 'string',		'type' => 'textarea:5:25',	'explain'	=> true),
							'announce_message'				=> array('lang' => 'ARCADE_ANNOUNCE_MESSAGE',			'validate' => 'string',		'type' => 'textarea:8:25',	'explain'	=> true),

							'legend3'						=> 'ACP_ARCADE_SETTINGS_PLAY',
							'limit_play'					=> array('lang' => 'ARCADE_LIMIT_PLAY',					'validate' => 'int',		'type' => 'select',			'explain'	=> true,	'method'	=> 'arcade_limit_play_select'),
							'limit_play_total_posts'		=> array('lang' => 'ARCADE_LIMIT_PLAY_TOTAL_POSTS',		'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'limit_play_posts'				=> array('lang' => 'ARCADE_LIMIT_PLAY_POSTS',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'limit_play_days'				=> array('lang' => 'ARCADE_LIMIT_PLAY_DAYS',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),

							'legend4'						=> 'ARCADE_SETTINGS_FACEBOOK',
							'facebook_enable_like'			=> array('lang' => 'ARCADE_FACEBOOK_ENABLE_LIKE',		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'facebook_layout_style'			=> array('lang' => 'ARCADE_FACEBOOK_LAYOUT_STYLE',		'validate' => 'string',		'type' => 'select',			'explain'	=> true,	'method'	=> 'facebook_layout_style_select'),
							'facebook_show_faces'			=> array('lang' => 'ARCADE_FACEBOOK_SHOW_FACES',		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'facebook_color_scheme'			=> array('lang' => 'ARCADE_FACEBOOK_COLOR_SCHEME',		'validate' => 'string',		'type' => 'select',			'explain'	=> true,	'method'	=> 'facebook_color_scheme_select'),
							'facebook_font_type'			=> array('lang' => 'ARCADE_FACEBOOK_FONT_TYPE',			'validate' => 'string',		'type' => 'select',			'explain'	=> true,	'method'	=> 'facebook_font_type_select'),
							'facebook_background_color'		=> array('lang' => 'ARCADE_FACEBOOK_BACKGROUND_COLOR',	'validate' => 'string',		'type' => 'custom',			'explain'	=> true,	'method'	=> 'arcade_colour_palette'),
							'facebook_table_width'			=> array('lang' => 'ARCADE_FACEBOOK_TABLE_WIDTH',		'validate' => 'int:100:300','type' => 'text:2:3',		'explain'	=> true,	'append'	=> ' ' . $user->lang['PIXEL']),
							'facebook_table_height'			=> array('lang' => 'ARCADE_FACEBOOK_TABLE_HEIGHT',		'validate' => 'int::50',	'type' => 'text:2:3',		'explain'	=> true,	'append'	=> ' ' . $user->lang['PIXEL']),

							'legend5'						=> 'ACP_SUBMIT_CHANGES'
						));
			break;

			case 'page':
					$display_vars = array(
						'title'								=> 'ACP_ARCADE_SETTINGS_PAGE',
						'vars'	=> array(
							'legend1'						=> 'ACP_ARCADE_SETTINGS_PAGE',
							'display_viewtopic'				=> array('lang' => 'ARCADE_DISPLAY_VIEWTOPIC',			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'display_memberlist'			=> array('lang' => 'ARCADE_DISPLAY_MEMBERLIST',			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'acp_items_per_page'			=> array('lang' => 'ARCADE_ACP_ITEMS_PER_PAGE',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'download_list_per_page'		=> array('lang' => 'ARCADE_DOWNLOAD_LIST_PER_PAGE',		'validate' => 'int',		'type' => 'select',			'explain'	=> true,	'method'	=> 'download_list_per_page_select'),
							'arcade_leaders_header'			=> array('lang' => 'ARCADE_LEADERS_HEADER',				'validate' => 'int',		'type' => 'select',			'explain'	=> true,	'method'	=> 'arcade_leaders_header_select'),
							'newest_games'					=> array('lang' => 'ARCADE_NEWEST_GAMES',				'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'latest_highscores'				=> array('lang' => 'ARCADE_LATEST_HIGHSCORES',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'game_scores'					=> array('lang' => 'ARCADE_TOTAL_GAME_SCORES',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),

							'legend2'						=> 'ACP_ARCADE_SETTINGS_PAGE_INDEX',
							'welcome_index'					=> array('lang' => 'ARCADE_WELCOME_INDEX',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'search_index'					=> array('lang' => 'ARCADE_SEARCH_INDEX',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'links_index'					=> array('lang' => 'ARCADE_LINKS_INDEX',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),

							'legend3'						=> 'ACP_ARCADE_SETTINGS_PAGE_CATS',
							'welcome_cats'					=> array('lang' => 'ARCADE_WELCOME_CATS',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'search_cats'					=> array('lang' => 'ARCADE_SEARCH_CATS',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'links_cats'					=> array('lang' => 'ARCADE_LINKS_CATS',					'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),

							'legend4'						=> 'ACP_ARCADE_SETTINGS_PAGE_STATS',
							'welcome_stats'					=> array('lang' => 'ARCADE_WELCOME_STATS', 				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'search_stats'					=> array('lang' => 'ARCADE_SEARCH_STATS', 				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'links_stats'					=> array('lang' => 'ARCADE_LINKS_STATS', 				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'most_popular'					=> array('lang' => 'ARCADE_MOST_POPULAR',				'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'least_popular'					=> array('lang' => 'ARCADE_LEAST_POPULAR',				'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'most_downloaded'				=> array('lang' => 'ARCADE_MOST_DOWNLOADED',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'least_downloaded'				=> array('lang' => 'ARCADE_LEAST_DOWNLOADED',			'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'longest_held_scores'			=> array('lang' => 'ARCADE_LONGEST_HELD_SCORES',		'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'stat_items_per_page'			=> array('lang' => 'ARCADE_STAT_ITEMS_PER_PAGE',		'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),
							'arcade_leaders'				=> array('lang' => 'ARCADE_LEADERS',					'validate' => 'int',		'type' => 'text:3:4',		'explain'	=> true),

							'legend5'						=> 'ACP_SUBMIT_CHANGES'
						));
			break;

			case 'path':
					$display_vars = array(
						'title'								=> 'ACP_ARCADE_SETTINGS_PATH',
						'vars'	=> array(
							'legend1'						=> 'ACP_ARCADE_SETTINGS_PROTECT',
							'protect_amod'					=> array('lang' => 'ARCADE_PROTECT_AMOD',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'protect_ibpro'					=> array('lang' => 'ARCADE_PROTECT_IBPRO',				'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),
							'protect_v3arcade'				=> array('lang' => 'ARCADE_PROTECT_V3ARCADE',			'validate' => 'bool',		'type' => 'radio:yes_no',	'explain'	=> true),

							'legend2'						=> 'ACP_ARCADE_SETTINGS_PATH',
							'game_path'						=> array('lang' => 'ARCADE_GAME_PATH',					'validate' => 'wpath',		'type' => 'text:30:65',		'explain'	=> true),
							'unpack_game_path'				=> array('lang' => 'ARCADE_UNPACK_GAME_PATH',			'validate' => 'wpath',		'type' => 'text:30:65',		'explain'	=> true),
							'image_path'					=> array('lang' => 'ARCADE_IMAGE_PATH',					'validate' => 'path',		'type' => 'text:30:65',		'explain'	=> true),
							'cat_image_path'				=> array('lang' => 'ARCADE_CAT_IMAGE_PATH',				'validate' => 'path',		'type' => 'text:30:65',		'explain'	=> true),
							'cat_backup_path'				=> array('lang' => 'ARCADE_CAT_BACKUP_PATH',			'validate' => 'path',		'type' => 'text:30:65',		'explain'	=> true),

							'legend3'						=> 'ACP_SUBMIT_CHANGES'
						));
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $arcade_config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);
		$this->validate_time($cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				if ($config_name == 'session_length' && $config['session_length'] < $config_value)
				{
					$error[] = sprintf($user->lang['ARCADE_PHPBB_SESSION_LENGTH_ERROR'], '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", "i=board&amp;mode=load", true, $user->session_id) . '">', '</a>');
					$submit = false;
				}

				if ($config_name == 'download_list_per_page' && !in_array($config_value, array(50, 100, 200)))
				{
					$config_value = 50;
				}

				if ($config_name == 'arcade_leaders_header' && !in_array($config_value, array(3, 6, 9)))
				{
					$config_value = 3;
				}

				if (in_array($config_name, array('game_path', 'unpack_game_path', 'image_path', 'cat_image_path')))
				{
					$file_functions->append_slash($config_value);
				}

				$arcade->set_config($config_name, $config_value);

				if ($config_name == 'jackpot_minimum')
				{
					$arcade->reset('jackpot_min_max');
				}
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_ARCADE_' . strtoupper($mode));
			$arcade_cache->destroy('sql', ARCADE_CATS_TABLE);
			$arcade_cache->destroy('sql', ARCADE_GAMES_TABLE);
			$arcade_cache->destroy('_arcade_leaders');
			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'				=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'		=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'				=> (sizeof($error)) ? true : false,
			'ERROR_MSG'				=> implode('<br />', $error),

			'U_ACTION'				=> $this->u_action
		));

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars
				));

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content
			));

			unset($display_vars['vars'][$config_key]);
		}
	}

	function arcade_announce_game($value, $key = '')
	{
		return make_forum_select($value, false, true, true, true, false, false);
	}

	function arcade_limit_play_select($value, $key = '')
	{
		global $user;

		return '<option value="' . LIMIT_PLAY_TYPE_NONE  . '"' . (($value == LIMIT_PLAY_TYPE_NONE)  ? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_NONE'] . '</option>
				<option value="' . LIMIT_PLAY_TYPE_POSTS . '"' . (($value == LIMIT_PLAY_TYPE_POSTS) ? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_LIMIT_PLAY_TOTAL_POSTS'] . '</option>
				<option value="' . LIMIT_PLAY_TYPE_DAYS  . '"' . (($value == LIMIT_PLAY_TYPE_DAYS)  ? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_LIMIT_PLAY_POSTS'] . '</option>
				<option value="' . LIMIT_PLAY_TYPE_BOTH  . '"' . (($value == LIMIT_PLAY_TYPE_BOTH)  ? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_LIMIT_PLAY_BOTH'] . '</option>';
	}

	function games_sort_order_select($value, $key = '')
	{
		global $user;

		return '<option value="' . ARCADE_ORDER_FIXED .'"'		. (($value == ARCADE_ORDER_FIXED)		? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_GAMES_SORT_FIXED'] . '</option>
				<option value="' . ARCADE_ORDER_INSTALLDATE .'"'. (($value == ARCADE_ORDER_INSTALLDATE) ? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_GAMES_SORT_INSTALLDATE'] . '</option>
				<option value="' . ARCADE_ORDER_NAME . '"'		. (($value == ARCADE_ORDER_NAME)		? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_GAMES_SORT_NAME'] . '</option>
				<option value="' . ARCADE_ORDER_PLAYS . '"'		. (($value == ARCADE_ORDER_PLAYS)		? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_GAMES_SORT_PLAYS'] . '</option>
				<option value="' . ARCADE_ORDER_RATING . '"'	. (($value == ARCADE_ORDER_RATING)		? ' selected="selected"' : '') . '>' . $user->lang['ARCADE_GAMES_SORT_RATING'] . '</option>';
	}

	function games_sort_dir_select($value, $key = '')
	{
		global $user;

		return '<option value="' . ARCADE_ORDER_ASC . '"'  . (($value == ARCADE_ORDER_ASC)  ? ' selected="selected"' : '') . '>' . $user->lang['ASCENDING']  . '</option>
				<option value="' . ARCADE_ORDER_DESC . '"' . (($value == ARCADE_ORDER_DESC) ? ' selected="selected"' : '') . '>' . $user->lang['DESCENDING'] . '</option>';
	}

	function download_list_per_page_select($value, $key = '')
	{
		return '<option value="50"'  . (($value == 50)  ? ' selected="selected"' : '') . '>50</option>
				<option value="100"' . (($value == 100) ? ' selected="selected"' : '') . '>100</option>
				<option value="200"' . (($value == 200) ? ' selected="selected"' : '') . '>200</option>';
	}

	function arcade_leaders_header_select($value, $key = '')
	{
		return '<option value="3"' . (($value == 3) ? ' selected="selected"' : '') . '>3</option>
				<option value="6"' . (($value == 6) ? ' selected="selected"' : '') . '>6</option>
				<option value="9"' . (($value == 9) ? ' selected="selected"' : '') . '>9</option>';
	}

	function cm_currency_select($value, $key = '')
	{
		global $user, $cash;

		return $cash->get_currencies($value, true);
	}

	function points_detect($value, $key)
	{
		global $arcade, $user;

		$point_status = ($arcade->points['enabled']) ? 'ENABLED': 'DISABLED';
		$detect = '';
		switch($arcade->points['type'])
		{
			case ARCADE_CASH_MOD:
				$detect = sprintf($user->lang['ARCADE_POINTS_DETECT_' . $point_status], $user->lang['ARCADE_CASH_MOD']);
			break;

			case ARCADE_POINTS_SYSTEM:
				$detect = sprintf($user->lang['ARCADE_POINTS_DETECT_' . $point_status], $user->lang['ARCADE_POINTS_SYSTEM']);
			break;

			case ARCADE_SIMPLE_POINTS_SYSTEM:
				$detect = sprintf($user->lang['ARCADE_POINTS_DETECT_' . $point_status], $user->lang['ARCADE_SIMPLE_POINTS_SYSTEM']);
			break;

			case ARCADE_ULTIMATE_POINTS_SYSTEM:
				$detect = sprintf($user->lang['ARCADE_POINTS_DETECT_' . $point_status], $user->lang['ARCADE_ULTIMATE_POINTS_SYSTEM']);
			break;

			default:
			break;
		}

		$radio_ary = array(1 => 'YES', 0 => 'NO');
		return $detect . '<br />' . h_radio('config[use_points]', $radio_ary, $value, $key);
	}

	/**
	* Arcade disable option and message
	*/
	function arcade_disable($value, $key)
	{
		$radio_ary = array(1 => 'YES', 0 => 'NO');

		return h_radio('config[arcade_disable]', $radio_ary, $value) . '<br /><input id="' . $key . '" type="text" name="config[arcade_disable_msg]" maxlength="255" size="40" value="' . $this->new_config['arcade_disable_msg'] . '" />';
	}

	function load_list($value, $key)
	{
		$radio_ary = array(1 => 'ARCADE_LOAD_LIST_ALWAYS', 0 => 'ARCADE_LOAD_LIST_ONCLICK');

		return h_radio('config[load_list]', $radio_ary, $value, $key);
	}

	function arcade_colour_palette($value, $key)
	{
		global $user, $phpbb_admin_path, $phpEx;

		$bgc   = ($value) ? '&nbsp;<span style="background-color: #'.$value.'">&nbsp; &nbsp;</span>' : '<strong>&nbsp;&nbsp;'.$user->lang['DISABLED'].'</strong>';
		return '<input type="text" id="'.$key.'" name="config['.$key.']" value="'.$value.'" maxlength="6" size="7" />
		&nbsp;&nbsp;<span>[ <a href="'.append_sid("{$phpbb_admin_path}swatch.$phpEx", "form=acp_board&amp;name={$key}").'" onclick="popup(this.href, 636, 150, \'_swatch\'); return false">'.$user->lang['COLOUR_SWATCH'].'</a> ]</span>'.$bgc;
	}

	function facebook_layout_style_select($value)
	{
		return '<option value="standard"'		. (($value == 'standard')		? ' selected="selected"' : '') . '>standard</option>
				<option value="button_count"'	. (($value == 'button_count')	? ' selected="selected"' : '') . '>button count</option>
				<option value="box_count"'		. (($value == 'box_count')		? ' selected="selected"' : '') . '>box count</option>';
	}

	function facebook_color_scheme_select($value)
	{
		return '<option value="light"' . (($value == 'light') ? ' selected="selected"' : '') . '>light</option>
				<option value="dark"'  . (($value == 'dark')  ? ' selected="selected"' : '') . '>dark</option>';
	}

	function facebook_font_type_select($value)
	{
		return '<option value="arial"'			. (($value == 'arial')			? ' selected="selected"' : '') . '>Arial</option>
				<option value="lucida+grande"'	. (($value == 'lucida+grande')	? ' selected="selected"' : '') . '>Lucida grande</option>
				<option value="segoe+ui"'		. (($value == 'segoe+ui')		? ' selected="selected"' : '') . '>Segoe ui</option>
				<option value="tahoma"'			. (($value == 'tahoma')			? ' selected="selected"' : '') . '>Tahoma</option>
				<option value="trebuchet+ms"'	. (($value == 'trebuchet+ms')	? ' selected="selected"' : '') . '>Trebuchet ms</option>
				<option value="verdana"'		. (($value == 'verdana')		? ' selected="selected"' : '') . '>Verdana</option>';
	}

	function search_check_select($value)
	{
		global $user;

		return '<option value="' . ARCADE_CHECK_EVERYONE    . '"' . (($value == ARCADE_CHECK_EVERYONE)    ? ' selected="selected"' : '') . '>' . $user->lang['ACP_ARCADE_CHECK_EVERYONE'] . '</option>
				<option value="' . ARCADE_CHECK_USER_NORMAL . '"' . (($value == ARCADE_CHECK_USER_NORMAL) ? ' selected="selected"' : '') . '>' . $user->lang['ACP_ARCADE_CHECK_USER_NORMAL'] . '</option>
				<option value="' . ARCADE_CHECK_DISABLED    . '"' . (($value == ARCADE_CHECK_DISABLED)    ? ' selected="selected"' : '') . '>' . $user->lang['ACP_ARCADE_CHECK_DISABLED'] . '</option>';
	}

	function validate_time(&$cfg_array, &$error)
	{
		if (!isset($cfg_array['auto_disable_start']) && !isset($cfg_array['auto_disable_end']))
		{
			return;
		}

		global $arcade, $user;

		if ($cfg_array['auto_disable_start'] == '' && $cfg_array['auto_disable_end'] == '')
		{
			$cfg_array['auto_disable'] = 0;
			return;
		}

		if (!sizeof($start_ary = $arcade->validate_time($cfg_array['auto_disable_start'])))
		{
			$error[] = sprintf($user->lang['ARCADE_AUTO_DISABLE_START_ERROR'], $cfg_array['auto_disable_start']);
		}

		if (!sizeof($end_ary = $arcade->validate_time($cfg_array['auto_disable_end'])))
		{
			$error[] = sprintf($user->lang['ARCADE_AUTO_DISABLE_END_ERROR'], $cfg_array['auto_disable_end']);
		}

		if (sizeof($start_ary) && sizeof($end_ary))
		{
			if (($start_ary['hour'] > $end_ary['hour']) || ($start_ary['hour'] == $end_ary['hour'] && $start_ary['min'] >= $end_ary['min']))
			{
				$error[] = sprintf($user->lang['ARCADE_AUTO_DISABLE_START_END_ERROR']);
			}
		}

		return;
	}
}

?>