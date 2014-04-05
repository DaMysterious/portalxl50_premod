<?php
/**
*
* @package arcade
* @version $Id: config.php 1665 2011-09-22 16:46:56Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* Mod installation script created for phpbb Arcade
* by JRSweets. This can easily be modifed for
* use with any mod.
* @copyright (c) 2010 http://www.phpbbarcade.com
* @copyright (c) 2008 http://www.jeffrusso.net
* Some config values so the script can be used for different mods.
* EDIT VALUES BELOW
*/

if (!defined('IN_INSTALL') || !defined('IN_PHPBB'))
{
	exit;
}

$arcade_mod_config = array(
	'version'	=> array(
		'current' 	=> '2.0.RC1',
		'oldest'	=> '1.0.0',
		'phpbb'		=> '3.0.9',
	),
	'data_file'		=> array(
		'add'		=> '',
		'remove'	=> '',
	),
	'permission_options'		=> array(
		'arcade'	=> array(
			'local'		=> array('c_', 'c_list', 'c_view', 'c_play', 'c_popup', 'c_playfree', 'c_score', 'c_rate', 'c_re_rate', 'c_comment', 'c_report', 'c_download', 'c_ignorecontrol', 'c_ignoreflood_download', 'c_ignoreflood_play', 'c_resolution'),
			'global'	=> array('u_', 'u_arcade', 'u_favorites', 'u_ignoreflood_search', 'u_view_whoisplaying', 'u_viewstats'),
		),
		'phpbb'		=> array(
			'local'		=> array(),
			'global'	=> array('a_arcade', 'a_arcade_backup', 'a_cauth', 'a_arcade_settings', 'a_arcade_points_settings', 'a_arcade_game', 'a_arcade_delete_game', 'a_arcade_cat', 'a_arcade_delete_cat', 'a_arcade_scores', 'a_arcade_utilities'),
		),
		'update'	=> array(
			'2.0.RC1'=> array(
				'arcade'	=> array(
					'local'		=> array('c_ignoreflood_download', 'c_ignoreflood_play', 'c_re_rate'),
					'global'	=> array('u_', 'u_arcade', 'u_favorites', 'u_ignoreflood_search', 'u_view_whoisplaying', 'u_viewstats'),
				),
				'phpbb'	=> array(
					'local'		=> array(),
					'global'	=> array('a_arcade', 'a_arcade_points_settings', 'a_arcade_backup'),
				),
			),
		),
	),
	'roles' => array(
		'phpbb'		=> array(),
		'arcade'	=> array(
			array(
				'role_name'			=> 'ROLE_ARCADE_NOACCESS',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_NOACCESS',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> false,
										'options'	=> array('c_'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_FULL',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_FULL',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('c_', 'c_list', 'c_view', 'c_play', 'c_popup', 'c_playfree', 'c_score', 'c_rate', 'c_re_rate', 'c_comment', 'c_report', 'c_download', 'c_ignorecontrol', 'c_ignoreflood_download', 'c_ignoreflood_play', 'c_resolution'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_LIMITED',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_LIMITED',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('c_', 'c_list', 'c_view', 'c_play', 'c_popup', 'c_score'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_PLAYONLY',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_PLAYONLY',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('c_', 'c_list', 'c_view', 'c_play', 'c_popup'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_STANDARD',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_STANDARD',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('c_', 'c_list', 'c_view', 'c_play', 'c_popup', 'c_score', 'c_rate', 'c_comment', 'c_report', 'c_resolution'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_STANDARD_DOWNLOADS',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_STANDARD_DOWNLOADS',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('c_', 'c_list', 'c_view', 'c_play', 'c_popup', 'c_score', 'c_rate', 'c_comment', 'c_report', 'c_resolution', 'c_download'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_VIEWONLY',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_VIEWONLY',
				'role_type'			=> 'c_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('c_', 'c_list', 'c_view'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_USER_NOACCESS',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_USER_NOACCESS',
				'role_type'			=> 'u_',
				'data'				=> array(
										'access'	=> false,
										'options'	=> array('u_'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_USER_FULL',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_USER_FULL',
				'role_type'			=> 'u_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('u_', 'u_arcade', 'u_favorites', 'u_ignoreflood_search', 'u_view_whoisplaying', 'u_viewstats'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_USER_LIMITED',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_USER_LIMITED',
				'role_type'			=> 'u_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('u_', 'u_arcade'),
										),
			),
			array(
				'role_name'			=> 'ROLE_ARCADE_USER_STANDARD',
				'role_description'	=> 'ROLE_DESCRIPTION_ARCADE_USER_STANDARD',
				'role_type'			=> 'u_',
				'data'				=> array(
										'access'	=> true,
										'options'	=> array('u_', 'u_arcade', 'u_favorites', 'u_view_whoisplaying', 'u_viewstats'),
										),
			),
		),
	),
	'modules'				=> array(
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> 'arcade_main',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_MAIN_INDEX',
					'module_mode' 		=> 'main',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_SETTINGS',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_settings',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_SETTINGS_GENERAL',
					'module_mode' 		=> 'settings',
					'module_auth' 		=> 'acl_a_arcade_settings',
				),
				array(
					'module_basename'	=> 'arcade_settings',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_SETTINGS_GAME',
					'module_mode' 		=> 'game',
					'module_auth' 		=> 'acl_a_arcade_settings',
				),
				array(
					'module_basename'	=> 'arcade_settings',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_SETTINGS_FEATURE',
					'module_mode' 		=> 'feature',
					'module_auth' 		=> 'acl_a_arcade_settings',
				),
				array(
					'module_basename'	=> 'arcade_settings',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_SETTINGS_PAGE',
					'module_mode' 		=> 'page',
					'module_auth' 		=> 'acl_a_arcade_settings',
				),
				array(
					'module_basename'	=> 'arcade_settings',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_SETTINGS_PATH',
					'module_mode' 		=> 'path',
					'module_auth' 		=> 'acl_a_arcade_settings',
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_MANAGE',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_manage',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_MANAGE_CATEGORIES',
					'module_mode' 		=> 'manage',
					'module_auth' 		=> 'acl_a_arcade_cat'
				),
				array(
					'module_basename'	=> 'arcade_games',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_EDIT_SCORES',
					'module_mode' 		=> 'edit_scores',
					'module_auth' 		=> 'acl_a_arcade_scores'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_ERRORS',
					'module_mode' 		=> 'errors',
					'module_auth' 		=> 'acl_a_arcade_utilities'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_REPORTS',
					'module_mode' 		=> 'reports',
					'module_auth' 		=> 'acl_a_arcade_utilities'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_USER_GUIDE',
					'module_mode' 		=> 'user_guide',
					'module_auth' 		=> ''
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_GAMES',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_games',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_ADD_GAMES',
					'module_mode' 		=> 'add_games',
					'module_auth' 		=> 'acl_a_arcade_game'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_BACKUP',
					'module_mode' 		=> 'backup',
					'module_auth' 		=> 'acl_a_arcade_backup'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_CREATE_INSTALL',
					'module_mode' 		=> 'create_install',
					'module_auth' 		=> 'acl_a_arcade_utilities'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_DOWNLOADS',
					'module_mode' 		=> 'downloads',
					'module_auth' 		=> 'acl_a_arcade_utilities'
				),
				array(
					'module_basename'	=> 'arcade_utilities',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UTILITIES_DOWNLOAD_STATS',
					'module_mode' 		=> 'download_stats',
					'module_auth' 		=> 'acl_a_arcade_utilities'
				),
				array(
					'module_basename'	=> 'arcade_games',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_EDIT_GAMES',
					'module_mode' 		=> 'edit_games',
					'module_auth' 		=> 'acl_a_arcade_game'
				),
				array(
					'module_basename'	=> 'arcade_games',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_UNPACK_GAMES',
					'module_mode' 		=> 'unpack_games',
					'module_auth' 		=> 'acl_a_arcade_game'
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_PERMISSION_ROLES',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_permission_roles',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_CAT_ROLES',
					'module_mode' 		=> 'cat_roles',
					'module_auth' 		=> 'acl_a_cauth && acl_a_roles',
				),
				array(
					'module_basename'	=> 'arcade_permission_roles',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_USER_ROLES',
					'module_mode' 		=> 'user_roles',
					'module_auth' 		=> 'acl_a_cauth && acl_a_roles',
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_GLOBAL_PERMISSIONS',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_USERS_PERMISSIONS',
					'module_mode' 		=> 'setting_user_global',
					'module_auth' 		=> 'acl_a_authusers && acl_a_cauth',
				),
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_GROUPS_PERMISSIONS',
					'module_mode' 		=> 'setting_group_global',
					'module_auth' 		=> 'acl_a_authgroups && acl_a_cauth',
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_PERMISSIONS',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_PERMISSION_TRACE',
					'module_mode' 		=> 'trace',
					'module_auth' 		=> 'acl_a_viewauth',
				),
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_CATEGORY_PERMISSIONS',
					'module_mode' 		=> 'setting_category_local',
					'module_auth' 		=> 'acl_a_cauth && (acl_a_authusers || acl_a_authgroups)',
				),
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_USERS_CATEGORY_PERMISSIONS',
					'module_mode' 		=> 'setting_user_local',
					'module_auth' 		=> 'acl_a_authusers && acl_a_cauth',
				),
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_GROUPS_CATEGORY_PERMISSIONS',
					'module_mode' 		=> 'setting_group_local',
					'module_auth' 		=> 'acl_a_authgroups && acl_a_cauth',
				),
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_ARCADE_CATEGORY_PERMISSIONS_COPY',
					'module_mode' 		=> 'setting_category_copy',
					'module_auth' 		=> 'acl_a_cauth && acl_a_authusers && acl_a_authgroups',
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_CAT_ARCADE_PERMISSION_MASKS',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_VIEW_ARCADE_USERS_PERMISSIONS',
					'module_mode' 		=> 'view_user_global',
					'module_auth' 		=> 'acl_a_viewauth',
				),
				array(
					'module_basename'	=> 'arcade_permissions',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'acp',
					'module_langname' 	=> 'ACP_VIEW_ARCADE_CATEGORY_PERMISSIONS',
					'module_mode' 		=> 'view_category_local',
					'module_auth' 		=> 'acl_a_viewauth',
				),
			),
		),
		array(
			'parent_module_data'	=> array(
					'module_basename' 	=> '',
					'module_enabled'	=> '1',
					'module_display' 	=> '1',
					'parent_id' 		=> '0',
					'module_class' 		=> 'ucp',
					'module_langname' 	=> 'UCP_CAT_ARCADE',
					'module_mode' 		=> '',
					'module_auth' 		=> '',
			),
			'module_data'			=> array(
				array(
					'module_basename'	=> 'arcade',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'ucp',
					'module_langname' 	=> 'UCP_ARCADE_SETTINGS',
					'module_mode' 		=> 'settings',
					'module_auth' 		=> ''
				),
				array(
					'module_basename'	=> 'arcade',
					'module_enabled' 	=> '1',
					'module_display' 	=> '1',
					'module_class' 		=> 'ucp',
					'module_langname' 	=> 'UCP_ARCADE_FAVORITES',
					'module_mode' 		=> 'favorites',
					'module_auth' 		=> ''
				),
			),
		),
	),
	'modules_remove'	=> array(
		'modules'		=> array('arcade', 'arcade_settings', 'arcade_manage', 'arcade_games', 'arcade_utilities', 'arcade_permission_roles', 'arcade_permissions'),
		'parents'		=> array('ACP_ARCADE', 'ACP_CAT_ARCADE', 'ACP_CAT_ARCADE_MANAGE', 'ACP_ARCADE_MANAGE_ARCADE', 'ACP_ARCADE_MAIN_INDEX', 'ACP_ARCADE_INDEX', 'ACP_CAT_ARCADE_SETTINGS', 'ACP_CAT_ARCADE_CONFIGURATION', 'ACP_CAT_ARCADE_GAMES', 'ACP_CAT_ARCADE_UTILITIES', 'ACP_CAT_ARCADE_PERMISSION_ROLES', 'ACP_CAT_ARCADE_PERMISSIONS', 'ACP_CAT_ARCADE_PERMISSION_MASKS', 'ACP_CAT_ARCADE_GLOBAL_PERMISSIONS', 'UCP_ARCADE', 'UCP_CAT_ARCADE'),
	),
	'schema_changes'=> array(
		'add'		=> array(),
		'update'	=> array(
			'2.0.RC1' => array(
				'drop_columns'		=> array(
					USERS_TABLE				=> array('user_arcade_permissions', 'user_arcade_perm_from', 'user_arcade_pm', 'games_per_page', 'games_sort_dir', 'games_sort_order'),
					ARCADE_GAMES_TABLE		=> array('game_files'),
				),
				'add_columns'		=> array(
					ARCADE_CONFIG_TABLE		=> array('is_dynamic'	=> array('BOOL', 0)),
				),
				'add_index'			=> array(
					ARCADE_CONFIG_TABLE		=> array(
						'is_dynamic'		=> array('is_dynamic')
					),

					ARCADE_SESSIONS_TABLE	=> array(
						'game_id'			=> array('game_id'),
					),

					ARCADE_PLAYS_TABLE	=> array(
						'uid'			=> array('user_id'),
					),

					ARCADE_GAMES_TABLE	=> array(
						'highuser'		=> array('game_highuser'),
						'installdate'	=> array('game_installdate'),
						'highdate'		=> array('game_highdate'),
						'plays'			=> array('game_plays'),
						'download'		=> array('game_download_total')
					),
				),
				'change_columns'		=> array(
					ARCADE_SESSIONS_TABLE	=> array(
						'randchar1'			=> array('UINT', 0),
						'randchar2'			=> array('UINT', 0),
						'randgid1'			=> array('UINT', 0),
						'randgid2'			=> array('UINT', 0),
					),
				),
			),
		),
		'remove'	=> array(
			'drop_columns'		=> array(
				USERS_TABLE	=> array('user_arcade_permissions', 'user_arcade_perm_from', 'user_arcade_pm', 'user_arcade_last_download', 'user_arcade_last_play', 'user_arcade_last_search', 'user_arcade_last_search_term', 'games_per_page', 'games_sort_dir', 'games_sort_order'),
			),
		),
	),
	'schema_data'	=> array(
		ACL_ARCADE_GROUPS_TABLE				=> array(
			'COLUMNS'						=> array(
			'group_id'						=> array('UINT', 0),
			'cat_id'						=> array('UINT', 0),
			'auth_option_id'				=> array('UINT', 0),
			'auth_role_id'					=> array('UINT', 0),
			'auth_setting'					=> array('TINT:2', 0),
			),
			'KEYS'							=> array(
			'g_id'							=> array('INDEX', 'group_id'),
			'ao_id'							=> array('INDEX', 'auth_option_id'),
			'ar_id'							=> array('INDEX', 'auth_role_id'),
			),
		),
		ACL_ARCADE_OPTIONS_TABLE			=> array(
			'COLUMNS'						=> array(
			'auth_option_id'				=> array('UINT', NULL, 'auto_increment'),
			'auth_option'					=> array('VCHAR:50', ''),
			'is_global'						=> array('BOOL', 0),
			'is_local'						=> array('BOOL', 0),
			'founder_only'					=> array('BOOL', 0),
			),
			'PRIMARY_KEY'					=> 'auth_option_id',
			'KEYS'							=> array(
			'au_op'							=> array('UNIQUE', 'auth_option'),
			),
		),
		ACL_ARCADE_ROLES_TABLE				=> array(
			'COLUMNS'						=> array(
			'role_id'						=> array('UINT', NULL, 'auto_increment'),
			'role_name'						=> array('VCHAR_UNI', ''),
			'role_description'				=> array('TEXT_UNI', ''),
			'role_type'						=> array('VCHAR:10', ''),
			'role_order'					=> array('USINT', 0),
			),
			'PRIMARY_KEY'					=> 'role_id',
			'KEYS'							=> array(
			'r_type'						=> array('INDEX', 'role_type'),
			'r_order'						=> array('INDEX', 'role_order'),
			),
		),
		ACL_ARCADE_ROLES_DATA_TABLE			=> array(
			'COLUMNS'						=> array(
			'role_id'						=> array('UINT', 0),
			'auth_option_id'				=> array('UINT', 0),
			'auth_setting'					=> array('TINT:2', 0),
			),
			'PRIMARY_KEY'					=> array('role_id', 'auth_option_id'),
			'KEYS'							=> array(
			'aoi'							=> array('INDEX', 'auth_option_id'),
			),
		),
		ACL_ARCADE_USERS_TABLE				=> array(
			'COLUMNS'						=> array(
			'user_id'						=> array('UINT', 0),
			'cat_id'						=> array('UINT', 0),
			'auth_option_id'				=> array('UINT', 0),
			'auth_role_id'					=> array('UINT', 0),
			'auth_setting'					=> array('TINT:2', 0),
			),
			'KEYS'							=> array(
			'user_id'						=> array('INDEX', 'user_id'),
			'autho_id'						=> array('INDEX', 'auth_option_id'),
			'authr_id'						=> array('INDEX', 'auth_role_id'),
			),
		),
		ARCADE_ACCESS_TABLE					=> array(
			'COLUMNS'						=> array(
			'cat_id'						=> array('UINT', 0),
			'user_id'						=> array('UINT', 0),
			'session_id'					=> array('CHAR:32', ''),
			),
			'PRIMARY_KEY'					=> array('cat_id', 'user_id', 'session_id'),
		),
		ARCADE_CATS_TABLE					=> array(
			'COLUMNS'						=> array(
			'cat_id'						=> array('UINT', NULL, 'auto_increment'),
			'parent_id'						=> array('UINT', 0),
			'left_id'						=> array('UINT', 0),
			'right_id'						=> array('UINT', 0),
			'cat_parents'					=> array('MTEXT', ''),
			'cat_name'						=> array('STEXT_UNI', ''),
			'cat_desc'						=> array('TEXT_UNI', ''),
			'cat_desc_bitfield'				=> array('VCHAR:255', ''),
			'cat_desc_options'				=> array('UINT:11', 7),
			'cat_desc_uid'					=> array('VCHAR:8', ''),
			'cat_link'						=> array('VCHAR_UNI', ''),
			'cat_password'					=> array('VCHAR_UNI:40', ''),
			'cat_age'						=> array('TINT:2', 0),
			'cat_style'						=> array('USINT', 0),
			'cat_display'					=> array('USINT', 0),
			'cat_image'						=> array('VCHAR', ''),
			'cat_rules'						=> array('TEXT_UNI', ''),
			'cat_rules_link'				=> array('VCHAR_UNI', ''),
			'cat_rules_bitfield'			=> array('VCHAR:255', ''),
			'cat_rules_options'				=> array('UINT:11', 7),
			'cat_rules_uid'					=> array('VCHAR:8', ''),
			'cat_games_per_page'			=> array('TINT:4', 0),
			'cat_type'						=> array('TINT:4', 0),
			'cat_test'						=> array('BOOL', 0),
			'cat_status'					=> array('TINT:4', 0),
			'cat_games'						=> array('UINT', 0),
			'cat_plays'						=> array('UINT', 0),
			'cat_download'					=> array('TINT:1', 1),
			'cat_use_jackpot'				=> array('BOOL', 0),
			'cat_cost'						=> array('PDECIMAL:15', 0.00),
			'cat_reward'					=> array('PDECIMAL:15', 0.00),
			'cat_last_play_game_id'			=> array('UINT', 0),
			'cat_last_play_game_name'		=> array('VCHAR_UNI', ''),
			'cat_last_play_user_id'			=> array('UINT', 0),
			'cat_last_play_score'			=> array('PDECIMAL:25', 0.00),
			'cat_last_play_time'			=> array('TIMESTAMP', 0),
			'cat_last_game_installdate'		=> array('TIMESTAMP', 0),
			'cat_last_play_username'		=> array('VCHAR_UNI', ''),
			'cat_last_play_user_colour'		=> array('VCHAR:6', ''),
			'cat_flags'						=> array('TINT:4', 32),
			'display_subcat_list'			=> array('BOOL', 1),
			'display_on_index'				=> array('BOOL', 1),
			),
			'PRIMARY_KEY'					=> 'cat_id',
			'KEYS'							=> array(
			'lr_id'							=> array('INDEX', array('left_id', 'right_id')),
			'clg_id'						=> array('INDEX', 'cat_last_play_game_id'),
			),
		),
		ARCADE_CONFIG_TABLE					=> array(
			'COLUMNS'						=> array(
			'config_name'					=> array('VCHAR', ''),
			'config_value'					=> array('VCHAR_UNI', ''),
			'is_dynamic'					=> array('BOOL', 0),
			),
			'PRIMARY_KEY'					=> 'config_name',
			'KEYS'							=> array('is_dynamic' => array('INDEX' , 'is_dynamic')),
		),
		ARCADE_DOWNLOAD_TABLE				=> array(
			'COLUMNS'						=> array(
			'user_id'						=> array('UINT', 0),
			'game_id'						=> array('UINT', 0),
			'total'							=> array('UINT', 0),
			'download_time'					=> array('TIMESTAMP', 0),
			),
			'PRIMARY_KEY'					=> array('user_id', 'game_id'),
		),
		ARCADE_ERRORS_TABLE					=> array(
			'COLUMNS'						=> array(
			'error_id'						=> array('UINT', NULL, 'auto_increment'),
			'user_id'						=> array('UINT', 0),
			'game_id'						=> array('UINT', 0),
			'score'							=> array('PDECIMAL:25', 0.00),
			'error_date'					=> array('TIMESTAMP', 0),
			'error_type'					=> array('TINT:1', 0),
			'game_type'						=> array('TINT:1', 0),
			'submitted_game_type'			=> array('TINT:1', 0),
			'played_time'					=> array('INT:11', 0),
			'error_ip'						=> array('VCHAR:40', ''),
			),
			'PRIMARY_KEY'					=> 'error_id',
		),
		ARCADE_FAVS_TABLE					=> array(
			'COLUMNS'						=> array(
			'user_id'						=> array('UINT', 0),
			'game_id'						=> array('UINT', 0),
			),
			'PRIMARY_KEY'					=> array('user_id', 'game_id'),
		),
		ARCADE_GAMES_TABLE					=> array(
			'COLUMNS'						=> array(
			'game_id'						=> array('UINT', NULL, 'auto_increment'),
			'game_image'					=> array('VCHAR', ''),
			'game_desc'						=> array('TEXT', ''),
			'game_highscore'				=> array('PDECIMAL:25', 0.00),
			'game_highdate'					=> array('TIMESTAMP', 0),
			'game_highuser'					=> array('UINT', 0),
			'game_name'						=> array('VCHAR', ''),
			'game_name_clean'				=> array('VCHAR', ''),
			'game_swf'						=> array('VCHAR', ''),
			'game_scorevar'					=> array('VCHAR', ''),
			'game_type'						=> array('TINT:1', 0),
			'game_width'					=> array('UINT:5', 550),
			'game_height'					=> array('UINT:5', 380),
			'game_installdate'				=> array('TIMESTAMP', 0),
			'game_filesize'					=> array('UINT:20', 0),
			'game_scoretype'				=> array('TINT:1', 0),
			'game_order'					=> array('UINT', 0),
			'game_plays'					=> array('UINT', 0),
			'game_votetotal'				=> array('UINT', 0),
			'game_votesum'					=> array('UINT', 0),
			'game_download_total'			=> array('UINT', 0),
			'game_download'					=> array('TINT:1', 1),
			'game_cost'						=> array('PDECIMAL:15', 0.00),
			'game_reward'					=> array('PDECIMAL:15', 0.00),
			'game_use_jackpot'				=> array('BOOL', 0),
			'game_jackpot'					=> array('PDECIMAL:15', 0.00),
			'cat_id'						=> array('UINT', 0),
			),
			'PRIMARY_KEY'					=> 'game_id',
			'KEYS'							=> array(
													'highuser'		=> array('INDEX' , 'game_highuser'),
													'installdate'	=> array('INDEX' , 'game_installdate'),
													'highdate'		=> array('INDEX' , 'game_highdate'),
													'plays'			=> array('INDEX' , 'game_plays'),
													'download'		=> array('INDEX' , 'game_download_total')
												),
		),
		ARCADE_PLAYS_TABLE					=> array(
			'COLUMNS'						=> array(
			'game_id'						=> array('UINT', 0),
			'user_id'						=> array('UINT', 0),
			'total_time'					=> array('TIMESTAMP', 0),
			'total_plays'					=> array('UINT', 0),
			),
			'PRIMARY_KEY'					=> array('game_id', 'user_id'),
			'KEYS'							=> array(
													'uid' => array('INDEX' , 'user_id')
												),
		),
		ARCADE_RATING_TABLE					=> array(
			'COLUMNS'						=> array(
			'game_id'						=> array('UINT', 0),
			'user_id'						=> array('UINT', 0),
			'game_rating'					=> array('UINT', 0),
			'rating_date'					=> array('TIMESTAMP', 0),
			'user_ip'						=> array('VCHAR:40', ''),
			),
			'PRIMARY_KEY'					=> array('game_id', 'user_id'),
		),
		ARCADE_REPORTS_TABLE				=> array(
			'COLUMNS'						=> array(
			'report_id'						=> array('UINT', NULL, 'auto_increment'),
			'user_id'						=> array('UINT', 0),
			'game_id'						=> array('UINT', 0),
			'report_type'					=> array('TINT:1', 0),
			'report_desc'					=> array('TEXT_UNI', ''),
			'report_desc_bitfield'			=> array('VCHAR:255', ''),
			'report_desc_options'			=> array('UINT:11', 7),
			'report_desc_uid'				=> array('VCHAR:8', ''),
			'report_time'					=> array('INT:11', 0),
			'report_ip'						=> array('VCHAR:40', ''),
			'report_closed'					=> array('TINT:1', 0),
			),
			'PRIMARY_KEY'					=> 'report_id',
		),
		ARCADE_SCORES_TABLE					=> array(
			'COLUMNS'						=> array(
			'game_id'						=> array('UINT', 0),
			'user_id'						=> array('UINT', 0),
			'score'							=> array('PDECIMAL:25', 0.00),
			'comment_text'					=> array('MTEXT', ''),
			'comment_bitfield'				=> array('VCHAR:255', ''),
			'comment_options'				=> array('UINT:11', 7),
			'comment_uid'					=> array('VCHAR:8', ''),
			'score_date'					=> array('TIMESTAMP', 0),
			),
			'PRIMARY_KEY'					=> array('game_id', 'user_id'),
		),
		ARCADE_SESSIONS_TABLE				=> array(
			'COLUMNS'						=> array(
			'session_id'					=> array('CHAR:32', ''),
			'game_id'						=> array('UINT', 0),
			'user_id'						=> array('UINT', 0),
			'game_type'						=> array('TINT:1', 0),
			'randchar1'						=> array('UINT', 0),
			'randchar2'						=> array('UINT', 0),
			'randgid1'						=> array('UINT', 0),
			'randgid2'						=> array('UINT', 0),
			'start_time'					=> array('TIMESTAMP', 0),
			'phpbb_session_id'				=> array('CHAR:32', ''),
			),
			'PRIMARY_KEY'					=> 'session_id',
			'KEYS'							=> array('game_id' => array('INDEX' , 'game_id')),
		),
		ARCADE_USERS_TABLE					=> array(
			'COLUMNS'						=> array(
			'user_id'						=> array('UINT', 0),
			'user_arcade_permissions'		=> array('MTEXT', ''),
			'user_arcade_perm_from'			=> array('UINT', 0),
			'user_arcade_pm'				=> array('TINT:1', 1),
			'user_arcade_last_download'		=> array('TIMESTAMP', 0),
			'user_arcade_last_play'			=> array('TIMESTAMP', 0),
			'user_arcade_last_search'		=> array('TIMESTAMP', 0),
			'user_arcade_last_search_term'	=> array('VCHAR:255', ''),
			'games_per_page'				=> array('USINT', 0),
			'games_sort_dir'				=> array('VCHAR_UNI:1', 'a'),
			'games_sort_order'				=> array('VCHAR_UNI:1', 'n'),
			),
			'PRIMARY_KEY'					=> 'user_id',
		),
	)
);

$arcade_mod_config['verify'] = array(
	'tables'		=> array(ACL_ARCADE_GROUPS_TABLE, ACL_ARCADE_OPTIONS_TABLE, ACL_ARCADE_ROLES_DATA_TABLE, ACL_ARCADE_ROLES_TABLE, ACL_ARCADE_USERS_TABLE, ARCADE_ACCESS_TABLE, ARCADE_CATS_TABLE, ARCADE_CONFIG_TABLE, ARCADE_DOWNLOAD_TABLE, ARCADE_GAMES_TABLE, ARCADE_FAVS_TABLE, ARCADE_PLAYS_TABLE, ARCADE_RATING_TABLE, ARCADE_SESSIONS_TABLE, ARCADE_SCORES_TABLE, ARCADE_ERRORS_TABLE, ARCADE_REPORTS_TABLE, ARCADE_USERS_TABLE),
	'files' 		=> array(
	'new_html_key'		=> '{GAMETOP}',
		'core'			=> array(
			'arcade.php',
			'newscore.php',
			'viewgame.php',
			'adm/style/acp_arcade_main.html',
			'adm/style/acp_arcade_games.html',
			'adm/style/acp_arcade_manage.html',
			'adm/style/acp_arcade_permissions.html',
			'adm/style/acp_arcade_utilities.html',
			'adm/style/confirm_body_move_arcade_games.html',
			'adm/style/permission_arcade_category_copy.html',
			'arcade/arcadelib/arcadelogo.jpg',
			'arcade/arcadelib/getHiScores.php',
			'arcade/arcadelib/ibproArcadeLib.conf',
			'arcade/arcadelib/score2.swf',
			'arcade/arcadelib/index.htm',
			'arcade/gamedata/index.htm',
			'arcade/games/index.htm',
			'arcade/images/1st.gif',
			'arcade/images/2nd.gif',
			'arcade/images/3rd.gif',
			'arcade/images/trophy.gif',
			'arcade/images/add_favorite.png',
			'arcade/images/logo.png',
			'arcade/images/logo_transparent.png',
			'arcade/images/remove_favorite.png',
			'arcade/images/star.png',
			'arcade/images/popup.png',
			'arcade/images/loading1.gif',
			'arcade/images/loading2.gif',
			'arcade/images/index.htm',
			'arcade/images/cats/index.htm',
			'arcade/includes/.htaccess',
			'arcade/includes/ajax.php',
			'arcade/includes/auth.php',
			'arcade/includes/auth_admin.php',
			'arcade/includes/cache.php',
			'arcade/includes/class.php',
			'arcade/includes/class_admin.php',
			'arcade/includes/common.php',
			'arcade/includes/constants.php',
			'arcade/includes/download.php',
			'arcade/includes/functions.php',
			'arcade/includes/functions_files.php',
			'arcade/includes/games.php',
			'arcade/includes/index.htm',
			'arcade/includes/play.php',
			'arcade/includes/protect.php',
			'arcade/includes/reports.php',
			'arcade/includes/score.php',
			'arcade/includes/scoretype.php',
			'arcade/includes/session.php',
			'arcade/includes/stats.php',
			'arcade/includes/viewonline.php',
			'arcade/install/index.htm',
			'arcade/js/index.htm',
			'arcade/js/arcade.js',
			'arcade/js/swfobject.js',
			'arcade/swf/flash_player_update.swf',
			'arcade/swf/.htaccess',
			'arcade/swf/index.htm',
			'arcade/index.htm',
			'games/index.htm',
			'includes/acp/acp_arcade_settings.php',
			'includes/acp/acp_arcade_main.php',
			'includes/acp/acp_arcade_games.php',
			'includes/acp/acp_arcade_manage.php',
			'includes/acp/acp_arcade_permissions.php',
			'includes/acp/acp_arcade_permission_roles.php',
			'includes/acp/acp_arcade_utilities.php',
			'includes/acp/info/acp_arcade_settings.php',
			'includes/acp/info/acp_arcade_main.php',
			'includes/acp/info/acp_arcade_games.php',
			'includes/acp/info/acp_arcade_manage.php',
			'includes/acp/info/acp_arcade_permissions.php',
			'includes/acp/info/acp_arcade_permission_roles.php',
			'includes/acp/info/acp_arcade_utilities.php',
			'includes/ucp/ucp_arcade.php',
			'includes/ucp/info/ucp_arcade.php',
		),
		'langs'			=> array(
			'en'			=> array(
			'language/en/mods/arcade.php',
			'language/en/mods/help_arcade.php',
			'language/en/mods/info_acp_arcade.php',
			'language/en/mods/info_ucp_arcade.php',
			'language/en/mods/permissions_arcade.php',
			),
			'hu'			=> array(
			'language/hu/mods/arcade.php',
			'language/hu/mods/help_arcade.php',
			'language/hu/mods/info_acp_arcade.php',
			'language/hu/mods/info_ucp_arcade.php',
			'language/hu/mods/permissions_arcade.php',
			),
			'de_x_sie'		=> array(
			'language/de_x_sie/mods/arcade.php',
			'language/de_x_sie/mods/help_arcade.php',
			'language/de_x_sie/mods/info_acp_arcade.php',
			'language/de_x_siemods/info_ucp_arcade.php',
			'language/de_x_sie/mods/permissions_arcade.php',
			),
		),
		'styles'			=> array(
			'prosilver'			=> array(
				'styles/prosilver/template/ucp_arcade_settings.html',
				'styles/prosilver/template/ucp_arcade_favorites.html',
				'styles/prosilver/template/arcade/download_body.html',
				'styles/prosilver/template/arcade/header_body.html',
				'styles/prosilver/template/arcade/index_body.html',
				'styles/prosilver/template/arcade/info_body.html',
				'styles/prosilver/template/arcade/jumpbox.html',
				'styles/prosilver/template/arcade/arcadelist_body.html',
				'styles/prosilver/template/arcade/login_cat.html',
				'styles/prosilver/template/arcade/online_body.html',
				'styles/prosilver/template/arcade/play_body.html',
				'styles/prosilver/template/arcade/popup_body.html',
				'styles/prosilver/template/arcade/reports_body.html',
				'styles/prosilver/template/arcade/score_body.html',
				'styles/prosilver/template/arcade/script_body.html',
				'styles/prosilver/template/arcade/stats_body.html',
				'styles/prosilver/template/arcade/footer_body.html',
				'styles/prosilver/template/arcade/message_body.html',
				'styles/prosilver/template/arcade/index.htm',
				'styles/prosilver/theme/arcade.css',
				'styles/prosilver/theme/images/icon_arcade.gif',
				'styles/prosilver/theme/images/arcade/star.png',
				'styles/prosilver/theme/images/arcade/add_favorite.png',
				'styles/prosilver/theme/images/arcade/remove_favorite.png',
				'styles/prosilver/theme/images/arcade/loading1.gif',
				'styles/prosilver/theme/images/arcade/loading2.gif',
				'styles/prosilver/theme/images/arcade/open_close.png',
				'styles/prosilver/theme/images/arcade/popup.png',
				'styles/prosilver/theme/images/arcade/index.htm',
				'styles/prosilver/theme/images/arcade/cats/index.htm',
			),
			'subsilver2'		=> array(
				'styles/subsilver2/template/ucp_arcade_settings.html',
				'styles/subsilver2/template/ucp_arcade_favorites.html',
				'styles/subsilver2/template/arcade/download_body.html',
				'styles/subsilver2/template/arcade/header_body.html',
				'styles/subsilver2/template/arcade/index_body.html',
				'styles/subsilver2/template/arcade/info_body.html',
				'styles/subsilver2/template/arcade/jumpbox.html',
				'styles/subsilver2/template/arcade/arcadelist_body.html',
				'styles/subsilver2/template/arcade/login_cat.html',
				'styles/subsilver2/template/arcade/online_body.html',
				'styles/subsilver2/template/arcade/play_body.html',
				'styles/subsilver2/template/arcade/popup_body.html',
				'styles/subsilver2/template/arcade/reports_body.html',
				'styles/subsilver2/template/arcade/score_body.html',
				'styles/subsilver2/template/arcade/script_body.html',
				'styles/subsilver2/template/arcade/stats_body.html',
				'styles/subsilver2/template/arcade/footer_body.html',
				'styles/subsilver2/template/arcade/message_body.html',
				'styles/subsilver2/template/arcade/index.htm',
				'styles/subsilver2/theme/arcade.css',
				'styles/subsilver2/theme/images/icon_mini_arcade.gif',
				'styles/subsilver2/theme/images/arcade/star.png',
				'styles/subsilver2/theme/images/arcade/add_favorite.png',
				'styles/subsilver2/theme/images/arcade/remove_favorite.png',
				'styles/subsilver2/theme/images/arcade/loading1.gif',
				'styles/subsilver2/theme/images/arcade/loading2.gif',
				'styles/subsilver2/theme/images/arcade/open_close.png',
				'styles/subsilver2/theme/images/arcade/popup.png',
				'styles/subsilver2/theme/images/arcade/index.htm',
				'styles/subsilver2/theme/images/arcade/cats/index.htm',
			),
			'prosilver Special Edition'		=> array(
				'styles/prosilver_se/template/arcade/header_body.html',
				'styles/prosilver_se/template/arcade/stats_body.html',
				'styles/prosilver_se/template/arcade/index.htm',
				'styles/prosilver_se/theme/arcade.css',
				'styles/prosilver_se/theme/images/icon_arcade.gif',
				'styles/prosilver_se/theme/images/arcade/star.png',
				'styles/prosilver_se/theme/images/arcade/add_favorite.png',
				'styles/prosilver_se/theme/images/arcade/remove_favorite.png',
				'styles/prosilver_se/theme/images/arcade/loading1.gif',
				'styles/prosilver_se/theme/images/arcade/loading2.gif',
				'styles/prosilver_se/theme/images/arcade/open_close.png',
				'styles/prosilver_se/theme/images/arcade/popup.png',
				'styles/prosilver_se/theme/images/arcade/index.htm',
				'styles/prosilver_se/theme/images/arcade/cats/index.htm',
			),
		),
	),
	'old_files'		=> array(
		'core'	=> array(
			'adm/style/acp_arcade.html',
			'arcade/images/star1.gif',
			'arcade/images/star2.gif',
			'arcade/images/favorite.gif',
			'arcade/images/remove_favorite.gif',
			'includes/auth_arcade.php',
			'includes/acp/auth_arcade.php',
			'includes/acp/acp_arcade.php',
			'includes/acp/info/acp_arcade.php',
			'includes/arcade/arcade_admin_class.php',
			'includes/arcade/arcade_cache.php',
			'includes/arcade/arcade_class.php',
			'includes/arcade/arcade_common.php',
			'includes/arcade/arcade_constants.php',
			'includes/arcade/arcade_download.php',
			'includes/arcade/arcade_games.php',
			'includes/arcade/arcade_play.php',
			'includes/arcade/arcade_protect.php',
			'includes/arcade/arcade_reports.php',
			'includes/arcade/arcade_score.php',
			'includes/arcade/arcade_scoretype.php',
			'includes/arcade/arcade_stats.php',
			'includes/arcade/arcade_viewonline.php',
			'includes/arcade/functions_files.php',
			'includes/arcade/functions_arcade.php',
			'includes/arcade/swfobject.js',
			'includes/arcade/scoretype/ibpro.php',
			'includes/arcade/scoretype/ibprov3.php',
			'includes/arcade/scoretype/v3arcade.php',
		),
		'styles'			=> array(
			'prosilver'						=> array(
				'styles/prosilver/template/arcade/arcade_version_body.html',
				'styles/prosilver/template/arcade/arcade_download_body.html',
				'styles/prosilver/template/arcade/arcade_header_body.html',
				'styles/prosilver/template/arcade/arcade_index_body.html',
				'styles/prosilver/template/arcade/arcade_info_body.html',
				'styles/prosilver/template/arcade/arcade_jumpbox.html',
				'styles/prosilver/template/arcade/arcade_list_body.html',
				'styles/prosilver/template/arcade/arcade_login_cat.html',
				'styles/prosilver/template/arcade/arcade_online_body.html',
				'styles/prosilver/template/arcade/arcade_play_body.html',
				'styles/prosilver/template/arcade/arcade_reports_body.html',
				'styles/prosilver/template/arcade/arcade_score_body.html',
				'styles/prosilver/template/arcade/arcade_stats_body.html',
			),
			'subsilver2'					=> array(
				'styles/subsilver2/template/arcade/arcade_version_body.html',
				'styles/subsilver2/template/arcade/arcade_download_body.html',
				'styles/subsilver2/template/arcade/arcade_header_body.html',
				'styles/subsilver2/template/arcade/arcade_index_body.html',
				'styles/subsilver2/template/arcade/arcade_info_body.html',
				'styles/subsilver2/template/arcade/arcade_jumpbox.html',
				'styles/subsilver2/template/arcade/arcade_list_body.html',
				'styles/subsilver2/template/arcade/arcade_login_cat.html',
				'styles/subsilver2/template/arcade/arcade_online_body.html',
				'styles/subsilver2/template/arcade/arcade_play_body.html',
				'styles/subsilver2/template/arcade/arcade_reports_body.html',
				'styles/subsilver2/template/arcade/arcade_score_body.html',
				'styles/subsilver2/template/arcade/arcade_stats_body.html',
			),
			'prosilver Special Edition'		=> array(
				'styles/prosilver_se/template/arcade/arcade_header_body.html',
			),
		),
	),

// files edits
	'edits'	=> array(
		'core'		=> array(

// index.php edit
			'index.php' => array(
				'$scoretype = (strtolower(request_var(\'act\', \'\')) == \'arcade\' && strtolower(request_var(\'do\', \'\')) == \'newscore\') ? \'IBPRO_GAME\' : ((strtolower(request_var(\'autocom\', \'\')) == \'arcade\') ? \'IBPROV3_GAME\' : false);
if ($scoretype)
{
	include($phpbb_root_path . \'arcade/includes/scoretype.\' . $phpEx);
}'),

// memberlist.php edit
			'memberlist.php' => array(
				'		include($phpbb_root_path . \'arcade/includes/cache.\' . $phpEx);
		include($phpbb_root_path . \'arcade/includes/arcade.\' . $phpEx);
		$user->add_lang(\'mods/arcade\');

		$arcade_data = display_arcade_highscores($user_id, basename(__FILE__, \'.\' . $phpEx));
		if (sizeof($arcade_data))
		{
			$template->assign_vars($arcade_data);
		}'),

// ucp.php edits
			'ucp.php' => array(
				'		include($phpbb_root_path . \'arcade/includes/auth.\' . $phpEx);
		include($phpbb_root_path . \'arcade/includes/auth_admin.\' . $phpEx);

		$auth_arcade_admin = new auth_arcade_admin();
		$user->add_lang(\'mods/arcade\');

		if (!$auth_arcade_admin->ghost_permissions($user_id, $user->data[\'user_id\']))
		{
			$arcade_message = \'ARCADE_SWITCH_NO_PERMISSION\';
		}
		else
		{
			$arcade_message = \'ARCADE_SWITCH_PERMISSION\';
		}

		$arcade_message = sprintf($user->lang[$arcade_message], $user_row[\'username\']);',

				' . $arcade_message',
				'		include($phpbb_root_path . \'arcade/includes/common.\' . $phpEx);
		$auth_arcade->acl($user->data);
		$auth_arcade->acl_cache($user->data);

		$sql = \'UPDATE \' . ARCADE_USERS_TABLE . "
			SET user_arcade_perm_from = 0
			WHERE user_id = " . (int) $user->data[\'user_id\'];
		$db->sql_query($sql);'),

// viewonline.php edit
			'viewonline.php' => array(
				'		case \'arcade\':
			include($phpbb_root_path . \'arcade/includes/viewonline.\' . $phpEx);
		break;'),

// viewtopic.php edits
			'viewtopic.php' => array(
				'include($phpbb_root_path . \'arcade/includes/cache.\' . $phpEx);
include($phpbb_root_path . \'arcade/includes/arcade.\' . $phpEx);
$user->add_lang(\'mods/arcade\');',

			'	$arcade_data = display_arcade_highscores($poster_id, basename(__FILE__, \'.\' . $phpEx));
	if (sizeof($arcade_data))
	{
		$postrow = array_merge($postrow, $arcade_data);
	}'),

// adm/style/admin.css edit
			'adm/style/admin.css' => array(
				'.arcade-userguide {
	font-size: 0.85em;
	padding-bottom: 5px;
	margin-bottom: 0.7em;
	line-height: 1.40em;
}

.arcade-userguide ul{
	margin-bottom: 0;
	padding-bottom: 0;
}

.arcade-userguide li{
	font-size: 1em;
}'),

// includes/auth.php edit
			'includes/auth.php' => array(
				'		global $phpbb_root_path, $phpEx;
		if (!class_exists(\'auth_arcade\'))
		{
			include($phpbb_root_path . \'arcade/includes/auth.\' . $phpEx);
		}
		$auth_arcade = new auth_arcade();
		$auth_arcade->acl_clear_prefetch($user_id);'),

// includes/constants.php edit
			'includes/constants.php' => array(
				'include($phpbb_root_path . \'arcade/includes/constants.\' . $phpEx);'),

// includes/functions.php edit
			'includes/functions.php' => array(
			'	global $arcade;
	if (!isset($arcade))
	{
		$user->add_lang(\'mods/arcade\');
		$template->assign_var(\'U_ARCADE\', append_sid("{$phpbb_root_path}arcade.$phpEx"));
	}'),

// includes/functions_user.php edits
			'includes/functions_user.php' => array(
				'	global $arcade, $auth_arcade, $arcade_cache, $arcade_config;
	if (!class_exists(\'arcade_admin\'))
	{
		define(\'USE_ARCADE_ADMIN\', true);
		include($phpbb_root_path . \'arcade/includes/common.\' . $phpEx);
	}

	if (!isset($arcade))
	{
		$arcade = new arcade_admin();
	}

	$arcade->delete_user($user_id, $user_row[\'username\']);',
				'		$sql = \'UPDATE \' . ARCADE_CATS_TABLE . " SET cat_last_play_user_colour = \'" . $db->sql_escape($sql_ary[\'user_colour\']) . "\'
			WHERE " . $db->sql_in_set(\'cat_last_play_user_id\', $user_id_ary);
		$db->sql_query($sql);

		$cache->destroy(\'sql\', ARCADE_CATS_TABLE);
		$cache->destroy(\'sql\', ARCADE_GAMES_TABLE);
		$cache->destroy(\'sql\', ARCADE_SCORES_TABLE);
		$cache->destroy(\'_arcade_leaders\');'),

// includes/session.php edits
			'includes/session.php' => array(
				'			global $arcade_config;

			define(\'ARCADE_DIMINISHED_DATA\', true);
			include($phpbb_root_path . \'arcade/includes/common.\' . $phpEx);

			arcade_session::delete_session(\'gc\');'),

// includes/acp/acp_styles.php edit
			'includes/acp/acp_styles.php' => array(
				'				$sql = \'UPDATE \' . ARCADE_CATS_TABLE . "
					SET cat_style = $new_id
					WHERE cat_style = $style_id";
				$db->sql_query($sql);
				$cache->destroy(\'sql\', ARCADE_CATS_TABLE);
				$cache->destroy(\'_arcade_cats\');')),

// styles files edits
		'styles'		=> array(
			'prosilver'		=> array(

// styles/prosilver/template/memberlist_view.html edit
				'styles/prosilver/template/memberlist_view.html' => array(
					'<!-- IF S_HAS_HIGHSCORES --><dt>{L_ARCADE_HIGHSCORES}:</dt> <dd>{TOTAL_HIGHSCORES} | <strong><a href="{U_ARCADE_STATS}">{L_ARCADE_VIEW_USERS_STATS}</a></strong></dd><!-- ENDIF -->'),

// styles/prosilver/template/overall_header.html edits
				'styles/prosilver/template/overall_header.html' => array(
					'<!-- IF S_ARCADE_FB -->
<meta property="og:site_name" content="{SITENAME}" />
<meta property="og:title"	  content="{ARCADE_FB_GN}" />
<meta property="og:image"	  content="{ARCADE_FB_GI}" />
<!-- ENDIF -->',
					'<!-- IF S_IN_ARCADE -->
	<link href="{T_THEME_PATH}/arcade.css" rel="stylesheet" type="text/css" media="screen, projection" />
<!-- ENDIF -->',
					'<li class="icon-arcade"><a href="{U_ARCADE}" title="{L_ARCADE_EXPLAIN}">{L_ARCADE}</a></li>',
					'<!-- INCLUDE arcade/info_body.html -->'),

// styles/prosilver/template/simple_header.html edit
				'styles/prosilver/template/simple_header.html' => array(
					'<!-- IF S_IN_ARCADE -->
	<link href="{T_THEME_PATH}/arcade.css" rel="stylesheet" type="text/css" media="screen, projection" />
<!-- ENDIF -->'),

// styles/prosilver/template/ucp_pm_message_header.html edit
				'styles/prosilver/template/ucp_pm_message_header.html' => array(
					'<script type="text/javascript" src="{ROOT_PATH}arcade/js/arcade.js"></script>'),

// styles/prosilver/template/viewtopic_body.html edit
				'styles/prosilver/template/viewtopic_body.html' => array(
					'<!-- IF postrow.S_HAS_HIGHSCORES --><dd><strong>{L_ARCADE_HIGHSCORES}:</strong> <a href="{postrow.U_ARCADE_STATS}">{postrow.TOTAL_HIGHSCORES}</a></dd><!-- ENDIF -->'),

// styles/prosilver/theme/bidi.css edit
				'styles/prosilver/theme/bidi.css' => array(
					'.rtl .icon-arcade,'),

// styles/prosilver/theme/buttons.css edit
				'styles/prosilver/theme/buttons.css' => array(
					'.icon-arcade,'),

// styles/prosilver/theme/colours.css edit
				'styles/prosilver/theme/colours.css' => array(
					'.icon-arcade					{ background-image: url("{T_THEME_PATH}/images/icon_arcade.gif"); }')),

			'subsilver2'	=> array(

// styles/subsilver2/template/memberlist_view.html edit
				'styles/subsilver2/template/memberlist_view.html' => array(
					'			<!-- IF S_HAS_HIGHSCORES -->
			<tr>
				<td class="gen" align="{S_CONTENT_FLOW_END}" valign="top" nowrap="nowrap">{L_ARCADE_HIGHSCORES}: </td>
				<td><b class="gen">{TOTAL_HIGHSCORES}</b><span class="genmed"><br /><a href="{U_ARCADE_STATS}">{L_ARCADE_VIEW_USERS_STATS}</a></span></td>
			</tr>
			<!-- ENDIF -->'),

// styles/subsilver2/template/overall_header.html edits
				'styles/subsilver2/template/overall_header.html' => array(
					'<!-- IF S_ARCADE_FB -->
<meta property="og:site_name" content="{SITENAME}" />
<meta property="og:title"	  content="{ARCADE_FB_GN}" />
<meta property="og:image"	  content="{ARCADE_FB_GI}" />
<!-- ENDIF -->',
					'<!-- IF S_IN_ARCADE -->
	<link href="{T_THEME_PATH}/arcade.css" rel="stylesheet" type="text/css" media="screen, projection" />
<!-- ENDIF -->',
					'<a href="{U_ARCADE}"><img src="{T_THEME_PATH}/images/icon_mini_arcade.gif" width="12" height="13" alt="*" /> {L_ARCADE}</a>&nbsp; &nbsp;',
					'<!-- INCLUDE arcade/info_body.html -->'),

// styles/subsilver2/template/simple_header.html edit
				'styles/subsilver2/template/simple_header.html' => array(
					'<!-- IF S_IN_ARCADE -->
	<link href="{T_THEME_PATH}/arcade.css" rel="stylesheet" type="text/css" media="screen, projection" />
<!-- ENDIF -->'),

// styles/subsilver2/template/ucp_pm_message_header.html edit
				'styles/subsilver2/template/ucp_pm_message_header.html' => array(
					'<script type="text/javascript" src="{ROOT_PATH}arcade/js/arcade.js"></script>'),

// styles/subsilver2/template/viewtopic_body.html edit
				'styles/subsilver2/template/viewtopic_body.html' => array(
					'<!-- IF postrow.S_HAS_HIGHSCORES --><br /><b>{L_ARCADE_HIGHSCORES}:</b> <a href="{postrow.U_ARCADE_STATS}">{postrow.TOTAL_HIGHSCORES}</a><!-- ENDIF -->')),

			'prosilver Special Edition'	=> array(

// styles/prosilver_se/template/overall_header.html edits
				'styles/prosilver_se/template/overall_header.html' => array(
					'<!-- IF S_ARCADE_FB -->
<meta property="og:site_name" content="{SITENAME}" />
<meta property="og:title"	  content="{ARCADE_FB_GN}" />
<meta property="og:image"	  content="{ARCADE_FB_GI}" />
<!-- ENDIF -->',
					'<!-- IF S_IN_ARCADE -->
	<link href="{T_THEME_PATH}/arcade.css" rel="stylesheet" type="text/css" media="screen, projection" />
<!-- ENDIF -->',
					'<li class="icon-arcade"><a href="{U_ARCADE}" title="{L_ARCADE_EXPLAIN}">{L_ARCADE}</a></li>',
					'<!-- INCLUDE arcade/info_body.html -->'),

// styles/prosilver_se/template/simple_header.html edit
				'styles/prosilver_se/template/simple_header.html' => array(
					'<!-- IF S_IN_ARCADE -->
	<link href="{T_THEME_PATH}/arcade.css" rel="stylesheet" type="text/css" media="screen, projection" />
<!-- ENDIF -->'),

// styles/prosilver_se/theme/bidi.css edit
				'styles/prosilver_se/theme/bidi.css' => array(
					'.rtl .icon-arcade,'),

// styles/prosilver_se/theme/buttons.css edit
				'styles/prosilver_se/theme/buttons.css' => array(
					'.icon-arcade,'),

// styles/prosilver_se/theme/colours.css edit
				'styles/prosilver_se/theme/colours.css' => array(
					'.icon-arcade					{ background-image: url("{T_THEME_PATH}/images/icon_arcade.gif"); }'),
			),
		),
	),
	'modules'		=> array(
		'acp' => array(
			'arcade_utilities' 			=> array('backup', 'downloads', 'errors', 'create_install', 'download_stats', 'reports', 'user_guide'),
			'arcade_games'				=> array('edit_games', 'edit_scores', 'add_games', 'unpack_games'),
			'arcade_settings'			=> array('settings', 'game', 'feature', 'page', 'path'),
			'arcade_main'				=> array('main'),
			'arcade_manage'				=> array('manage'),
			'arcade_permission_roles'	=> array('cat_roles', 'user_roles'),
			'arcade_permissions'		=> array('trace', 'setting_user_global', 'setting_group_global', 'setting_category_local', 'setting_user_local', 'setting_group_local', 'setting_category_copy', 'view_user_global', 'view_category_local'),
		),
		'ucp' => array(
			'arcade' => array('settings', 'favorites'),
		),
	),
	'alter_db' 		=> array(
		'current'	=> array(
		),
		'old'		=> array(
			USERS_TABLE		=> array('user_arcade_permissions', 'user_arcade_perm_from', 'user_arcade_pm', 'games_per_page', 'games_sort_dir', 'games_sort_order'),
		),
	),
);

?>