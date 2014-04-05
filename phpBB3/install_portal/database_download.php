<?php
/**
*
* @author oxpus (Karsten Ude) webmaster@oxpus.net
* @package download mod installation package based on umil (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'DOWNLOAD_MOD';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
if (!isset($config['dl_mod_version']))
{
	$db->return_on_error = true;

	$sql = 'SELECT config_value FROM ' . $table_prefix . "dl_config
		WHERE config_name = 'dl_mod_version'";
	$result = $db->sql_query($sql);
	$value = $db->sql_fetchfield('config_value');
	$db->sql_freeresult($result);

	if ($value)
	{
		$config['dl_mod_version'] = $value;

		$sql = 'INSERT INTO ' . CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'config_name'	=> 'dl_mod_version',
			'config_value'	=> $value,
		));
		$db->sql_query($sql); 
	}
}
	
$version_config_name = 'dl_mod_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
* $mod_name
* 'INSTALL_' . $mod_name
* 'INSTALL_' . $mod_name . '_CONFIRM'
* 'UPDATE_' . $mod_name
* 'UPDATE_' . $mod_name . '_CONFIRM'
* 'UNINSTALL_' . $mod_name
* 'UNINSTALL_' . $mod_name . '_CONFIRM'
*/
$language_file = 'mods/dl_install';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	'5.0.0'	=> array(
		'table_add' => array(
			array('phpbb_downloads', array(
				'COLUMNS'		=> array(
					'id'					=> array('UINT:11', NULL, 'auto_increment'),
					'description'			=> array('MTEXT_UNI', ''),
					'file_name'				=> array('VCHAR', ''),
					'klicks'				=> array('INT:11', 0),
					'free'					=> array('BOOL', 0),
					'extern'				=> array('BOOL', 0),
					'long_desc'				=> array('MTEXT_UNI', ''),
					'sort'					=> array('INT:11', 0),
					'cat'					=> array('INT:11', 0),
					'hacklist'				=> array('BOOL', 0),
					'hack_author'			=> array('VCHAR', ''),
					'hack_author_email'		=> array('VCHAR', ''),
					'hack_author_website'	=> array('TEXT_UNI', ''),
					'hack_version'			=> array('VCHAR:32', ''),
					'hack_dl_url'			=> array('TEXT_UNI', ''),
					'test'					=> array('VCHAR:50', ''),
					'req'					=> array('MTEXT_UNI', ''),
					'todo'					=> array('MTEXT_UNI', ''),
					'warning'				=> array('MTEXT_UNI', ''),
					'mod_desc'				=> array('MTEXT_UNI', ''),
					'mod_list'				=> array('BOOL', 0),
					'file_size'				=> array('BINT', 0),
					'change_time'			=> array('TIMESTAMP', 0),
					'add_time'				=> array('TIMESTAMP', 0),
					'rating'				=> array('INT:5', 0),
					'file_traffic'			=> array('BINT', 0),
					'overall_klicks'		=> array('INT:11', 0),
					'approve'				=> array('BOOL', 0),
					'add_user'				=> array('UINT', 0),
					'change_user'			=> array('UINT', 0),
					'last_time'				=> array('TIMESTAMP', 0),
					'down_user'				=> array('UINT', 0),
					'thumbnail'				=> array('VCHAR', ''),
					'mod_desc_uid'			=> array('CHAR:8', ''),
					'mod_desc_bitfield'		=> array('VCHAR', ''),
					'mod_desc_flags'		=> array('UINT:11', 0),
					'long_desc_uid'			=> array('CHAR:8', ''),
					'long_desc_bitfield'	=> array('VCHAR', ''),
					'long_desc_flags'		=> array('UINT:11', 0),
					'desc_uid'				=> array('CHAR:8', ''),
					'desc_bitfield'			=> array('VCHAR', ''),
					'desc_flags'			=> array('UINT:11', 0),
					'warn_uid'				=> array('CHAR:8', ''),
					'warn_bitfield'			=> array('VCHAR', ''),
					'warn_flags'			=> array('UINT:11', 0),
				),
				'PRIMARY_KEY'	=> 'id'
				),
			),
				
			array('phpbb_downloads_cat', array(
				'COLUMNS'		=> array(
					'id'				=> array('UINT:11', NULL, 'auto_increment'),
					'parent'			=> array('INT:11', 0),
					'path'				=> array('VCHAR', ''),
					'cat_name'			=> array('VCHAR', ''),
					'sort'				=> array('INT:11', 0),
					'description'		=> array('MTEXT_UNI', ''),
					'auth_view'			=> array('BOOL', 1),
					'auth_dl'			=> array('BOOL', 1),
					'auth_up'			=> array('BOOL', 0),
					'auth_mod'			=> array('BOOL', 0),
					'must_approve'		=> array('BOOL', 0),
					'allow_mod_desc'	=> array('BOOL', 0),
					'statistics'		=> array('BOOL', 1),
					'stats_prune'		=> array('UINT', 0),
					'comments'			=> array('BOOL', 1),
					'cat_traffic'		=> array('BINT', 0),
					'cat_traffic_use'	=> array('BINT', 0),
					'allow_thumbs'		=> array('BOOL', 0),
					'auth_cread'		=> array('BOOL', 0),
					'auth_cpost'		=> array('BOOL', 1),
					'approve_comments'	=> array('BOOL', 1),
					'desc_uid'			=> array('CHAR:8', ''),
					'desc_bitfield'		=> array('VCHAR', ''),
					'desc_flags'		=> array('UINT:11', 0),
				),
				'PRIMARY_KEY'	=> 'id'
				),
			),
				
			array('phpbb_dl_auth', array(
				'COLUMNS'		=> array(
					'cat_id'	=> array('INT:11', 0),
					'group_id'	=> array('INT:11', 0),
					'auth_view'	=> array('BOOL', 1),
					'auth_dl'	=> array('BOOL', 1),
					'auth_up'	=> array('BOOL', 1),
					'auth_mod'	=> array('BOOL', 0),
					),
				),
			),
				
			array('phpbb_dl_comments', array(
				'COLUMNS'		=> array(
					'dl_id'				=> array('BINT', NULL, 'auto_increment'),
					'id'				=> array('INT:11', 0),
					'cat_id'			=> array('INT:11', 0),
					'user_id'			=> array('UINT', 0),
					'username'			=> array('VCHAR:32', ''),
					'comment_time'		=> array('TIMESTAMP', 0),
					'comment_edit_time'	=> array('TIMESTAMP', 0),
					'comment_text'		=> array('MTEXT_UNI', ''),
					'approve'			=> array('BOOL', 0),
					'com_uid'			=> array('CHAR:8', ''),
					'com_bitfield'		=> array('VCHAR', ''),
					'com_flags'			=> array('UINT:11', 0),
				),
				'PRIMARY_KEY'	=> 'dl_id'
				),
			),
				
			array('phpbb_dl_ratings', array(
				'COLUMNS'		=> array(
					'dl_id'			=> array('INT:11', 0),
					'user_id'		=> array('UINT', 0),
					'rate_point'	=> array('CHAR:10', ''),
					),
				),
			),
				
			array('phpbb_dl_stats', array(
				'COLUMNS'		=> array(
					'dl_id'			=> array('BINT', NULL, 'auto_increment'),
					'id'			=> array('INT:11', 0),
					'cat_id'		=> array('INT:11', 0),
					'user_id'		=> array('UINT', 0),
					'username'		=> array('VCHAR:32', ''),
					'traffic'		=> array('BINT', 0),
					'direction'		=> array('BOOL', 0),
					'user_ip'		=> array('VCHAR:40', ''),
					'browser'		=> array('VCHAR:20', ''),
					'time_stamp'	=> array('INT:11', 0),
				),
				'PRIMARY_KEY'	=> 'dl_id'
				),
			),
				
			array('phpbb_dl_config', array(
				'COLUMNS'		=> array(
					'config_name'	=> array('VCHAR', ''),
					'config_value'	=> array('VCHAR', ''),
				),
				'PRIMARY_KEY'	=> 'config_name'
				),
			),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'delay_auto_traffic', 'config_value' => '30'),
				array('config_name' => 'delay_post_traffic', 'config_value' => '30'),
				array('config_name' => 'disable_email', 'config_value' => '1'),
				array('config_name' => 'disable_popup', 'config_value' => '0'),
				array('config_name' => 'dl_click_reset_time', 'config_value' => '0'),
				array('config_name' => 'dl_edit_time', 'config_value' => '3'),
				array('config_name' => 'dl_links_per_page', 'config_value' => '10'),
				array('config_name' => 'dl_method', 'config_value' => '2'),
				array('config_name' => 'dl_method_quota', 'config_value' => '2097152'),
				array('config_name' => 'dl_new_time', 'config_value' => '3'),
				array('config_name' => 'dl_posts', 'config_value' => '25'),
				array('config_name' => 'dl_stats_perm', 'config_value' => '0'),
				array('config_name' => 'download_dir', 'config_value' => 'dl_mod/downloads/'),
				array('config_name' => 'enable_post_dl_traffic', 'config_value' => '1'),
				array('config_name' => 'guest_stats_show', 'config_value' => '1'),
				array('config_name' => 'newtopic_traffic', 'config_value' => '524288'),
				array('config_name' => 'overall_traffic', 'config_value' => '104857600'),
				array('config_name' => 'physical_quota', 'config_value' => '524288000'),
				array('config_name' => 'recent_downloads', 'config_value' => '10'),
				array('config_name' => 'remain_traffic', 'config_value' => '0'),
				array('config_name' => 'reply_traffic', 'config_value' => '262144'),
				array('config_name' => 'stop_uploads', 'config_value' => '0'),
				array('config_name' => 'thumb_fsize', 'config_value' => '0'),
				array('config_name' => 'thumb_xsize', 'config_value' => '200'),
				array('config_name' => 'thumb_ysize', 'config_value' => '150'),
				array('config_name' => 'traffic_retime', 'config_value' => '0'),
				array('config_name' => 'upload_traffic_count', 'config_value' => '1'),
				array('config_name' => 'use_hacklist', 'config_value' => '1'),
				array('config_name' => 'user_dl_auto_traffic', 'config_value' => '0'),
			)),
		),

		'table_column_add' => array(
			array('phpbb_groups', 'group_dl_auto_traffic', array('BINT', 0)),
			array('phpbb_users', 'user_allow_new_download_email', array('BOOL', 0)),
			array('phpbb_users', 'user_allow_new_download_popup', array('BOOL', 1)),
			array('phpbb_users', 'user_dl_update_time', array('TIMESTAMP', 0)),
			array('phpbb_users', 'user_new_download', array('BOOL', 0)),
			array('phpbb_users', 'user_traffic', array('BINT', 0)),
		),

	),

	'5.0.1' => array(),
	'5.0.2' => array(),
	'5.0.3' => array(),
	'5.0.4' => array(),

	'5.0.5' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'limit_desc_on_index', 'config_value' => '0'),
				array('config_name' => 'show_footer_legend', 'config_value' => '1'),
				array('config_name' => 'show_footer_stat', 'config_value' => '1'),
			)),
		),
	),

	'5.0.6' => array(),
	'5.0.7' => array(),
	'5.0.8' => array(),

	'5.0.9' => array(
		'table_add' => array(
			array('phpbb_dl_ext_blacklist', array(
				'COLUMNS'		=> array(
					'extention'	=> array('CHAR:10', ''),
					),
				),
			),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'use_ext_blacklist', 'config_value' => '1'),
			)),
			array('phpbb_dl_ext_blacklist', array(
				array('extention'	=> 'asp'),
				array('extention'	=> 'cgi'),
				array('extention'	=> 'dhtm'),
				array('extention'	=> 'dhtml'),
				array('extention'	=> 'exe'),
				array('extention'	=> 'htm'),
				array('extention'	=> 'html'),
				array('extention'	=> 'jar'),
				array('extention'	=> 'js'),
				array('extention'	=> 'php'),
				array('extention'	=> 'php3'),
				array('extention'	=> 'pl'),
				array('extention'	=> 'sh'),
				array('extention'	=> 'shtm'),
				array('extention'	=> 'shtml'),
			)),
		),
	),

	'5.0.10' => array(),
	'5.0.11' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'disable_popup_notify', 'config_value' => '0'),
			)),
		),
	),

	'5.0.12' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'show_real_filetime', 'config_value' => '1'),
			)),
		),
	),

	'5.0.13' => array(),
	'5.0.14' => array(
		'table_add' => array(
			array('phpbb_dl_banlist', array(
				'COLUMNS'		=> array(
					'ban_id'		=> array('UINT:11', NULL, 'auto_increment'),
					'user_id'		=> array('UINT', 0),
					'user_ip'		=> array('VCHAR:40', ''),
					'user_agent'	=> array('VCHAR:50', ''),
					'username'		=> array('VCHAR:25', ''),
					'guests'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'ban_id'
				),
			),

			array('phpbb_dl_favorites', array(
				'COLUMNS'		=> array(
					'fav_id'		=> array('UINT:11', NULL, 'auto_increment'),
					'fav_dl_id'		=> array('INT:11', 0),
					'fav_dl_cat'	=> array('INT:11', 0),
					'fav_user_id'	=> array('UINT', 0),
				),
				'PRIMARY_KEY'	=> 'fav_id'
				),
			),
		),
			
		'table_column_add' => array(
			array('phpbb_downloads', 'broken', array('BOOL', 0)),
		),
		
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'guest_report_broken', 'config_value' => '1'),
			)),
			array('phpbb_dl_banlist', array(
				array('user_agent'	=> 'n/a'),
			)),
		),
	),

	'5.0.15' => array(
		'table_add' => array(
			array('phpbb_dl_notraf', array(
				'COLUMNS'		=> array(
					'user_id'	=> array('UINT', 0),
					'dl_id'		=> array('INT:11', 0),
					),
				),
			),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'user_traffic_once', 'config_value' => '0'),
			)),
		),

	),

	'5.0.16' => array(
		'table_add' => array(
			array('phpbb_dl_hotlink', array(
				'COLUMNS'		=> array(
					'user_id'		=> array('UINT', 0),
					'session_id'	=> array('VCHAR:32', ''),
					'hotlink_id'	=> array('VCHAR:32', ''),
					'code'			=> array('CHAR:5', ''),
					),
				),
			),
		),

		'table_column_add' => array(
			array('phpbb_users', 'user_allow_fav_download_email', array('BOOL', 1)),
			array('phpbb_users', 'user_allow_fav_download_popup', array('BOOL', 1)),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'prevent_hotlink', 'config_value' => '1'),
				array('config_name' => 'hotlink_action', 'config_value' => '1'),
			)),
		),
	),

	'5.0.17' => array(
		'table_column_update' => array(
			array('phpbb_downloads', 'todo', array('MTEXT_UNI', '')),
		),

		'table_column_add' => array(
			array('phpbb_downloads_cat', 'rules', array('MTEXT_UNI', '')),
			array('phpbb_downloads_cat', 'rules_uid', array('CHAR:8', '')),
			array('phpbb_downloads_cat', 'rules_bitfield', array('VCHAR', '')),
			array('phpbb_downloads_cat', 'rules_flags', array('UINT:11', 0)),
		),

		'table_row_update'	=> array(
			array('phpbb_dl_config', array(
					'config_name'		=> 'guest_report_broken',
					'config_value'		=> '1',
				),
				array(
					'config_name'		=> 'report_broken',
					'config_value'		=> '1',
				),
			),
		),
					
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name'	=> 'report_broken_lock', 'config_value' => '1'),
				array('config_name'	=> 'report_broken_message', 'config_value' => '1'),
				array('config_name'	=> 'report_broken_vc', 'config_value' => '1'),
				array('config_name'	=> 'download_vc', 'config_value' => '1'),
				array('config_name'	=> 'edit_own_downloads', 'config_value' => '1'),
				array('config_name'	=> 'shorten_extern_links', 'config_value' => '10'),
			)),
		),
	),

	'5.1.0' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name'	=> 'icon_free_for_reg', 'config_value' => '0'),
			)),
		),
	),

	'5.1.1' => array(),
	'5.1.2' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name'	=> 'latest_comments', 'config_value' => '1'),
			)),
		),
	),

	'5.1.3' => array(
		'table_column_add' => array(
			array('phpbb_users', 'user_dl_note_type', array('BOOL', 1)),
			array('phpbb_users', 'user_dl_sort_fix', array('BOOL', 0)),
			array('phpbb_users', 'user_dl_sort_opt', array('BOOL', 0)),
			array('phpbb_users', 'user_dl_sort_dir', array('BOOL', 0)),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name'	=> 'sort_preform', 'config_value' => '0'),
			)),
		),
	),

	'5.1.4' => array(),
	'5.1.5' => array(
		'table_column_add' => array(
			array('phpbb_downloads_cat', 'bug_tracker', array('BOOL', 0)),
		),
		
		'table_add' => array(
			array('phpbb_dl_bug_tracker', array(
				'COLUMNS'		=> array(
					'report_id'				=> array('UINT:11', NULL, 'auto_increment'),
					'df_id'					=> array('INT:11', 0),
					'report_title'			=> array('VCHAR', ''),
					'report_text'			=> array('MTEXT_UNI', ''),
					'report_file_ver'		=> array('VCHAR:50', ''),
					'report_date'			=> array('TIMESTAMP', 0),
					'report_author_id'		=> array('UINT', 0),
					'report_assign_id'		=> array('UINT', 0),
					'report_assign_date'	=> array('TIMESTAMP', 0),
					'report_status'			=> array('BOOL', 0),
					'report_status_date'	=> array('TIMESTAMP', 0),
					'report_php'			=> array('VCHAR:50', ''),
					'report_db'				=> array('VCHAR:50', ''),
					'report_forum'			=> array('VCHAR:50', ''),
					'bug_uid'				=> array('CHAR:8', ''),
					'bug_bitfield'			=> array('VCHAR', ''),
					'bug_flags'				=> array('UINT:11', 0),
				),
				'PRIMARY_KEY'	=> 'report_id'
				),
			),

			array('phpbb_dl_bug_history', array(
				'COLUMNS'		=> array(
					'report_his_id'		=> array('UINT:11', NULL, 'auto_increment'),
					'df_id'				=> array('INT:11', 0),
					'report_id'			=> array('INT:11', 0),
					'report_his_type'	=> array('CHAR:10', ''),
					'report_his_date'	=> array('TIMESTAMP', 0),
					'report_his_value'	=> array('VCHAR', ''),
				),
				'PRIMARY_KEY'	=> 'report_his_id'
				)
			),
		),
	),

	'5.2.0' => array(),
	'5.2.1' => array(),
	'5.2.2' => array(),
	'5.3.0' => array(),
	'5.3.1' => array(
		'table_column_add' => array(
			array('phpbb_users', 'user_dl_sub_on_index', array('BOOL', 1)),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'drop_traffic_postdel', 'config_value' => '0'),
			)),
		),
	),

	'5.3.2' => array(),
	'6.0.0 RC1' => array(),
	'6.0.0 RC2' => array(),
	'6.0.0 RC3' => array(),
	'6.0.0 RC4' => array(),
	'6.0.0' => array(),
	'6.0.1' => array(),
	'6.0.2' => array(),
	'6.0.3' => array(),
	'6.0.4' => array(),
	'6.0.5' => array(),
	'6.0.6' => array(),
	'6.0.7' => array(),
	'6.0.8' => array(),
	'6.0.9' => array(),
	'6.0.10' => array(),
	'6.0.11' => array(),
	'6.0.12' => array(),
	'6.0.13' => array(),
	'6.0.14' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'enable_dl_topic', 'config_value' => '0'),
				array('config_name' => 'dl_topic_forum', 'config_value' => ''),
				array('config_name' => 'dl_topic_text', 'config_value' => ''),
			)),
		),

		'table_column_add' => array(
			array('phpbb_downloads', 'dl_topic', array('UINT:11', 0))
		),
	),

	'6.1.0' => array(),
	'6.1.1' => array(),
	'6.1.2' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'overall_guest_traffic', 'config_value' => '0'),
				array('config_name' => 'remain_guest_traffic', 'config_value' => '0'),
			)),
		),

		'table_column_update' => array(
			array('phpbb_dl_stats', 'user_ip', array('VCHAR:40', '')),
		),

		'custom'	=> 'stats_ip_convert',

		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_DOWNLOADS'),

			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'ACP_USER_OVERVIEW',
				'module_mode' => 'overview',
				'module_auth' => 'acl_a_dl_overview')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_CONFIG_MANAGEMENT',
				'module_mode' => 'config',
				'module_auth' => 'acl_a_dl_config')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_TRAFFIC_MANAGEMENT',
				'module_mode' => 'traffic',
				'module_auth' => 'acl_a_dl_traffic')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname'=> 'DL_ACP_CATEGORIES_MANAGEMENT',
				'module_mode' => 'categories',
				'module_auth' => 'acl_a_dl_categories')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_FILES_MANAGEMENT',
				'module_mode' => 'files',
				'module_auth' => 'acl_a_dl_files')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_PERMISSIONS',
				'module_mode' => 'permissions',
				'module_auth' => 'acl_a_dl_permissions')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_STATS_MANAGEMENT',
				'module_mode' => 'stats',
				'module_auth' => 'acl_a_dl_stats')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_BANLIST',
				'module_mode' => 'banlist',
				'module_auth' => 'acl_a_dl_banlist')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_EXT_BLACKLIST',
				'module_mode' => 'ext_blacklist',
				'module_auth' => 'acl_a_dl_blacklist')
			),
	
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_MANAGE',
				'module_mode' => 'toolbox',
				'module_auth' => 'acl_a_dl_toolbox')
			),
		),

		'permission_add' => array(
			array('a_dl_overview', true),
			array('a_dl_config', true),
			array('a_dl_traffic', true),
			array('a_dl_categories', true),
			array('a_dl_files', true),
			array('a_dl_permissions', true),
			array('a_dl_stats', true),
			array('a_dl_banlist', true),
			array('a_dl_blacklist', true),
			array('a_dl_toolbox', true),
		),

		'permission_set' => array(
			array('ADMINISTRATORS', 'a_dl_overview', 'group'),
			array('ADMINISTRATORS', 'a_dl_config', 'group'),
			array('ADMINISTRATORS', 'a_dl_traffic', 'group'),
			array('ADMINISTRATORS', 'a_dl_categories', 'group'),
			array('ADMINISTRATORS', 'a_dl_files', 'group'),
			array('ADMINISTRATORS', 'a_dl_permissions', 'group'),
			array('ADMINISTRATORS', 'a_dl_stats', 'group'),
			array('ADMINISTRATORS', 'a_dl_banlist', 'group'),
			array('ADMINISTRATORS', 'a_dl_blacklist', 'group'),
			array('ADMINISTRATORS', 'a_dl_toolbox', 'group'),
		),
	),

	'6.2.0' => array(),
	'6.2.1' => array(),
	'6.2.2' => array(),
	'6.2.3' => array(),
	'6.2.4' => array(
		'module_add' => array(
			array('ucp', false, 'ACP_DOWNLOADS'),
	
			array('ucp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_CONFIG',
				'module_mode' => 'config')
			),
	
			array('ucp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_FAVORITE',
				'module_mode' => 'favorite')
			),
		),
	),

	'6.2.5' => array(),
	'6.2.6' => array(
		'table_column_update' => array(
			array('phpbb_dl_banlist', 'user_ip', array('VCHAR:40', '')),
		),

		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'ext_new_window', 'config_value' => '0'),
			)),
		),
	),

	'6.2.7' => array(
		'table_column_add' => array(
			array('phpbb_downloads', 'real_file', array('VCHAR', '')),
		),

		'custom'	=> 'add_real_file',
	),

	'6.2.8' => array(),
	'6.2.9' => array(),
	'6.2.10' => array(),
	'6.2.11' => array(),
	'6.2.12' => array(
		'table_row_insert' => array(
			array('phpbb_dl_config', array(
				array('config_name' => 'dl_cap_char_trans', 'config_value' => '0'),
				array('config_name' => 'dl_cap_back_color', 'config_value' => '969696'),
				array('config_name' => 'dl_cap_chess', 'config_value' => '1'),
				array('config_name' => 'dl_cap_lines', 'config_value' => '1'),
				array('config_name' => 'dl_cap_carree_x', 'config_value' => '20'),
				array('config_name' => 'dl_cap_carree_y', 'config_value' => '20'),
				array('config_name' => 'dl_cap_carree_color', 'config_value' 	=> 'FFFFFF'),
				array('config_name' => 'dl_cap_contrast', 'config_value' => '0.8'),
				array('config_name' => 'dl_cap_type', 'config_value' => 'j'),
				array('config_name' => 'dl_cap_jpeg_qual', 'config_value' => '75'),
			)),
		),
	),

	'6.2.13' => array(),
	'6.2.14' => array(),
	'6.2.15 RC1' => array(),
	'6.2.15' => array(),
	'6.2.16' => array(),
	'6.2.17' => array(),
	'6.2.18' => array(),
	'6.2.19' => array(),
	'6.2.20' => array(),
	'6.2.21' => array(
		'custom'	=> 'drop_captcha_values',

		'table_column_add'	=> array(
			array('phpbb_downloads_cat', 'dl_topic_forum', array('INT:11', 0)),	
			array('phpbb_downloads_cat', 'dl_topic_text', array('MTEXT_UNI', ''))
		),
	),

	'6.2.22' => array(
		'table_column_add'	=> array(
			array('phpbb_downloads_cat', 'cat_icon', array('VCHAR', '')),
		),
	),

	'6.2.23' => array(),
	'6.2.24' => array(),
	'6.2.25' => array(),
	'6.2.26' => array(),

	'6.2.27' => array(
		'custom'	=> 'drop_old_mod_version',
	),

	'6.2.28' => array(),
	'6.2.29' => array(),
	'6.2.30' => array(),
	'6.2.31' => array(),
	'6.2.32' => array(),

	'6.3.0' => array(
		'table_column_update' => array(
			array('phpbb_dl_hotlink', 'code', array('VCHAR:10', '-')),
		),
	),

	'6.3.1' => array(
		'table_column_update' => array(
			array('phpbb_dl_hotlink', 'code', array('VCHAR:10', '-')),
		),
	),

	'6.3.2' => array(),

	'6.3.3' => array(
		'table_add' => array(
			array('phpbb_dl_rem_traf', array(
				'COLUMNS'		=> array(
					'config_name'	=> array('VCHAR', ''),
					'config_value'	=> array('VCHAR', ''),
				),
				'PRIMARY_KEY'	=> 'config_name'
				),
			),

			array('phpbb_dl_cat_traf', array(
				'COLUMNS'		=> array(
					'cat_id'			=> array('UINT:11', 0),
					'cat_traffic_use'	=> array('BINT', 0),
				),
				'PRIMARY_KEY'	=> 'cat_id'
				),
			),
		),

		'custom'	=> 'move_traffics',

		'table_column_remove' => array(
			array('phpbb_downloads_cat', 'cat_traffic_use'),
		),
	),

	'6.3.4.RC1' => array(
		'custom'	=> 'move_config',

		'table_remove' => array(
			array('phpbb_dl_config'),
		),

		'table_row_update'	=> array(
			array('phpbb_dl_rem_traf', array(
					'config_name'		=> 'remain_traffic',
				),
				array(
					'config_name'		=> 'dl_remain_traffic',
				),
			),

			array('phpbb_dl_rem_traf', array(
					'config_name'		=> 'remain_guest_traffic',
				),
				array(
					'config_name'		=> 'dl_remain_guest_traffic',
				),
			),
		),
					
	),

	'6.3.4.RC2' => array(),
	'6.3.4' => array(),
	'6.3.5' => array(),
	'6.3.6' => array(),

	'6.3.7' => array(
		'table_add' => array(
			array('phpbb_dl_versions', array(
				'COLUMNS'		=> array(
					'ver_id'			=> array('UINT:11', NULL, 'auto_increment'),
					'dl_id'				=> array('UINT:11', 0),
					'ver_file_name'		=> array('VCHAR', ''),
					'ver_real_file'		=> array('VCHAR', ''),
					'ver_file_size'		=> array('BINT', 0),
					'ver_version'		=> array('VCHAR:32', ''),
					'ver_change_time'	=> array('TIMESTAMP', 0),
					'ver_add_time'		=> array('TIMESTAMP', 0),
					'ver_add_user'		=> array('UINT', 0),
					'ver_change_user'	=> array('UINT', 0),
				),
				'PRIMARY_KEY'	=> 'ver_id'
				),
			),
		),

		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_antispam_posts', 'config_value' => '50'),
				array('config_name' => 'dl_antispam_hours', 'config_value' => '24'),
			)),
		),
	),

	'6.3.8' => array(
		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_traffic_off', 'config_value' => '0'),
			)),
		),
	),

	'6.4.0' => array(
		'table_column_add'	=> array(
			array('phpbb_downloads', 'todo_uid', array('CHAR:8', '')),
			array('phpbb_downloads', 'todo_bitfield', array('VCHAR', '')),
			array('phpbb_downloads', 'todo_flags', array('UINT:11', 0)),
			array('phpbb_downloads_cat', 'diff_topic_user', array('BOOL', 0)),
			array('phpbb_downloads_cat', 'topic_user', array('UINT:11', 0)),
		),

		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_diff_topic_user', 'config_value' => '0'),
				array('config_name' => 'dl_topic_user', 'config_value' => '0'),
			)),
		),

		'table_add' => array(
			array('phpbb_dl_fields', array(
				'COLUMNS'		=> array(
					'field_id'				=> array('UINT:8', NULL, 'auto_increment'),
					'field_name'			=> array('MTEXT_UNI', ''),
					'field_type'			=> array('INT:4', 0),
					'field_ident'			=> array('VCHAR:20', ''),
					'field_length'			=> array('VCHAR:20', ''),
					'field_minlen'			=> array('VCHAR', ''),
					'field_maxlen'			=> array('VCHAR', ''),
					'field_novalue'			=> array('MTEXT_UNI', ''),
					'field_default_value'	=> array('MTEXT_UNI', ''),
					'field_validation'		=> array('VCHAR:60', ''),
					'field_required'		=> array('BOOL', 0),
					'field_active'			=> array('BOOL', 0),
					'field_order'			=> array('UINT:8', 0),
				),
				'PRIMARY_KEY'	=> 'field_id'
				),
			),

			array('phpbb_dl_fields_data', array(
				'COLUMNS'		=> array(
					'df_id'			=> array('UINT:11', 0),
				),
				'PRIMARY_KEY'	=> 'df_id'
				),
			),

			array('phpbb_dl_fields_lang', array(
				'COLUMNS'		=> array(
					'field_id'		=> array('UINT:8', 0),
					'lang_id'		=> array('UINT:8', 0),
					'option_id'		=> array('UINT:8', 0),
					'field_type'	=> array('INT:4', 0),
					'lang_value'	=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> array('field_id', 'lang_id', 'option_id'),
				),
			),

			array('phpbb_dl_lang', array(
				'COLUMNS'		=> array(
					'field_id'				=> array('UINT:8', 0),
					'lang_id'				=> array('UINT:8', 0),
					'lang_name'				=> array('MTEXT_UNI', ''),
					'lang_explain'			=> array('MTEXT_UNI', ''),
					'lang_default_value'	=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> array('field_id', 'lang_id'),
				),
			),
		),

		'module_add' => array(
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_FIELDS',
				'module_mode' => 'fields',
				'module_auth' => 'acl_a_dl_fields')
			),
		),

		'permission_add' => array(
			array('a_dl_fields', true),
		),

		'permission_set' => array(
			array('ADMINISTRATORS', 'a_dl_fields', 'group'),
		),
	),

	'6.4.1' => array(
		'table_column_add'	=> array(
			array('phpbb_downloads_cat', 'topic_more_details', array('BOOL', 1)),
		),

		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_todo_link_onoff', 'config_value' => '1'),
				array('config_name' => 'dl_uconf_link_onoff', 'config_value' => '1'),
				array('config_name' => 'dl_topic_more_details', 'config_value' => '1'),
			)),
		),
	),

	'6.4.2' => array(),
	'6.4.3' => array(),
	'6.4.4' => array(),
	'6.4.5' => array(),

	'6.4.6' => array(
		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_active', 'config_value' => '1'),
				array('config_name' => 'dl_off_hide', 'config_value' => '1'),
				array('config_name' => 'dl_off_now_time', 'config_value' => '0'),
				array('config_name' => 'dl_off_from', 'config_value' => '00:00'),
				array('config_name' => 'dl_off_till', 'config_value' => '23:59'),
				array('config_name' => 'dl_on_admins', 'config_value' => '1'),
			)),
		),
	),

	'6.4.7' => array(),

	'6.4.8' => array(
		'module_add' => array(
			array('acp', 'ACP_DOWNLOADS', array(
				'module_basename' => 'downloads',
				'module_langname' => 'DL_ACP_BROWSER',
				'module_mode' => 'browser',
				'module_auth' => 'acl_a_dl_browser')
			),
		),

		'permission_add' => array(
			array('a_dl_browser', true),
		),

		'permission_set' => array(
			array('ADMINISTRATORS', 'a_dl_browser', 'group'),
		),
	),

	'6.4.9' => array(),

	'6.4.10' => array(
		'table_add' => array(
			array('phpbb_dl_images', array(
				'COLUMNS'		=> array(
					'img_id'				=> array('UINT:8', NULL, 'auto_increment'),
					'dl_id'					=> array('UINT:11', 0),
					'img_name'				=> array('VCHAR:255', ''),
					'img_title'				=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> 'img_id'
				),
			),
		),
	),

	'6.4.11' => array(),
	'6.4.11.1' => array(),
	'6.4.12' => array(),

	'6.4.13' => array(
		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_enable_rate', 'config_value' => '1'),
				array('config_name' => 'dl_rate_points', 'config_value' => '10'),
				array('config_name' => 'dl_enable_jumpbox', 'config_value' => '1'),
			)),
		),
	),

	'6.4.14' => array(
		'table_column_update' => array(
			array('phpbb_dl_stats', 'browser', array('VCHAR:255', '')),
		),

		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_rss_enable', 'config_value' => '0'),
				array('config_name' => 'dl_rss_off_action', 'config_value' => '0'),
				array('config_name' => 'dl_rss_off_text', 'config_value' => 'Dieser Feed ist aktuell offline. / This feed is currently offline.'),
				array('config_name' => 'dl_rss_cats', 'config_value' => '0'),
				array('config_name' => 'dl_rss_cats_select', 'config_value' => '-'),
				array('config_name' => 'dl_rss_perms', 'config_value' => '1'),
				array('config_name' => 'dl_rss_number', 'config_value' => '10'),
				array('config_name' => 'dl_rss_select', 'config_value' => '0'),
				array('config_name' => 'dl_rss_desc_length', 'config_value' => '0'),
			)),
		),
	),

	'6.4.14.1' => array(),

	'6.4.15' => array(
		'table_row_insert' => array(
			array('phpbb_config', array(
				array('config_name' => 'dl_rss_desc_shorten', 'config_value' => '150'),
				array('config_name' => 'dl_rss_new_update', 'config_value' => '0'),
			)),
		),
	),

	'6.4.16' => array(),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

$cache->purge();

function move_config($action, $version)
{
	global $db, $table_prefix, $phpbb_root_path, $phpEx, $user;

	$sql = 'SELECT * FROM ' . $table_prefix . 'dl_config';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];

		if (substr($config_name, 0, 3) != 'dl_')
		{
			$config_name = 'dl_' . $config_name;
		}

		set_config($config_name, $config_value, true);
	}

	$db->sql_freeresult($result);

	@unlink($phpbb_root_path . 'cache/data_dl_config.' . $phpEx);

	return $user->lang['DL_MOVE_CONFIGURATION'];
}

function move_traffics($action, $version)
{
	global $db, $table_prefix, $user;

	$sql = 'SELECT config_value FROM ' . $table_prefix . "dl_config
		WHERE config_name = 'remain_traffic'";
	$result = $db->sql_query($sql);
	$remain_traffic = $db->sql_fetchfield('config_value');
	$db->sql_freeresult($result);

	$sql = 'INSERT INTO ' . $table_prefix . 'dl_rem_traf ' . $db->sql_build_array('INSERT', array(
		'config_name'	=> 'remain_traffic',
		'config_value'	=> $remain_traffic,
	));
	$db->sql_query($sql);

	$sql = 'SELECT config_value FROM ' . $table_prefix . "dl_config
		WHERE config_name = 'remain_guest_traffic'";
	$result = $db->sql_query($sql);
	$remain_guest_traffic = $db->sql_fetchfield('config_value');
	$db->sql_freeresult($result);

	$sql = 'INSERT INTO ' . $table_prefix . 'dl_rem_traf ' . $db->sql_build_array('INSERT', array(
		'config_name'	=> 'remain_guest_traffic',
		'config_value'	=> $remain_guest_traffic,
	));
	$db->sql_query($sql);

	$sql = 'DELETE FROM ' . $table_prefix . 'dl_config
		WHERE ' . $db->sql_in_set('config_name', array('remain_traffic', 'remain_guest_traffic'));
	$db->sql_query($sql);

	$sql = 'SELECT id, cat_traffic_use FROM ' . $table_prefix . 'downloads_cat';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$cat_id = $row['id'];
		$cat_traf = $row['cat_traffic_use'];

		$sql = 'INSERT INTO ' . $table_prefix . 'dl_cat_traf ' . $db->sql_build_array('INSERT', array(
			'cat_id'			=> $cat_id,
			'cat_traffic_use'	=> $cat_traf,
		));

		$db->sql_query($sql);
	}

	$db->sql_freeresult($result);

	return $user->lang['DL_MOVE_TRAFFIC_VALUES'];
}

function drop_old_mod_version($action, $version)
{
	global $db, $table_prefix, $user;

	$sql = 'DELETE FROM ' . $table_prefix . "dl_config
		WHERE config_name =  'dl_mod_version'";
	$db->sql_query($sql);

	return $user->lang['DL_DROP_OLD_VERSION'];
}

function drop_captcha_values($action, $version)
{
	global $db, $table_prefix, $user;

	$sql = 'DELETE FROM ' . $table_prefix . "dl_config
		WHERE config_name LIKE ('dl_cap_%')";
	$db->sql_query($sql);

	return $user->lang['DL_DROP_OLD_CAPTCHA'];
}

function stats_ip_convert($action, $version)
{
	global $db, $table_prefix, $user;
	
	$sql = 'SELECT user_ip FROM ' . $table_prefix . 'dl_stats
		GROUP BY user_ip';
	$result = $db->sql_query($sql);

	if ($db->sql_affectedrows($result) <= 100)
	{
		while($row = $db->sql_fetchrow($result))
		{
			$raw_ip = $row['user_ip'];
			$user_ip = decode_ip($raw_ip);
		
			$sql = 'UPDATE ' . $table_prefix . 'dl_stats SET ' . $db->sql_build_array('UPDATE', array(
				'user_ip' => $user_ip)) . "	WHERE user_ip = '" . $db->sql_escape($raw_ip) . "'";
			$db->sql_query($sql);
		}
	}
	
	$db->sql_freeresult($result);

	return $user->lang['DL_CONVERT_STATISTICAL_DATA'];
}

function add_real_file($action, $version)
{
	global $db, $table_prefix, $user;

	$sql = 'UPDATE ' . $table_prefix . 'downloads SET real_file = file_name WHERE extern = 0';
	$db->sql_query($sql);

	return $user->lang['DL_ADD_REAL_FILE'];
}

function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

?>