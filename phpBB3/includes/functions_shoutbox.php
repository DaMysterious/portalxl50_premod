<?php
/**
*
* @package Breizh Shoutbox
* @version $Id: functions_shoutbox.php 140 20:09 31/12/2010 Sylver35 Exp $ 
* @copyright (c) 2010, 2011 Sylver35    http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* functions:
* shout_xml()
* shout_sql_error()
* shout_error()
* define_sort_shout()
* shout_cron()
* execute_shout_cron()
* delete_shout_posts()
* shout_display()
* parse_shout_message()
* parse_shout_img()
* purge_shout_admin()
* ExecuteShoutScript()
* set_shout_value()
* post_robot_shout()
* post_session_shout()
* advert_post_shoutbox()
* birthday_robot_shout()
* hello_robot_shout()
* shout_add_newest_user()
* tracker_post_shoutbox()
* sudoku_post_shoutbox()
* build_sound_select()
* shout_user_avatar()
**/

/**
 * Returns cdata'd string
 * @param string $txt
 * @return string
 */
function shout_xml($contents)
{
	$contents = str_replace('&nbsp;', ' ', $contents);
	if (preg_match('/\<(.*?)\>/xsi', $contents))
	{
		$contents = preg_replace('/\<script[\s]+(.*)\>(.*)\<\/script\>/xsi', '', $contents);
	}

	if (!(strpos($contents, '>') === false) || !(strpos($contents, '<') === false) || !(strpos($contents, '&') === false))
	{
		// CDATA doesn't let you use ']]>' so fall back to WriteString
		if (!(strpos($contents, ']]>') === false))
		{
			return htmlspecialchars_decode($contents);
		}
		else
		{
			return '<![CDATA[' .$contents. ']]>';
		}
	}
	else
	{
		return htmlspecialchars_decode($contents);
	}
	return $contents;
}

/**
 * Prints a sql XML error.
 *
 * @param string $sql Sql query
 * @param int $line Linenumber
 * @param string $file Filename
 */
function shout_sql_error($sql, $line = __LINE__, $file = __FILE__)
{
	global $db;

	$sql = shout_xml($sql);
	$err = $db->sql_error();
	$err = shout_xml($err['message']);
	echo "<error>$err</error>\n<sql>$sql</sql>\n</xml>";
	exit;
}

/**
 * Prints a XML error.
 * @param sring $message Error
 */
function shout_error($message, $on1 = false, $on2 = false, $on3 = false)
{
	global $user;

	if (!isset($user->lang[$message]))
	{
		$message = $message;
	}
	else
	{
		if ($on1 && !$on2 && !$on3)
		{
			$message = sprintf($user->lang[$message], $on1);
		}
		elseif ($on1 && $on2 && !$on3)
		{
			$message = sprintf($user->lang[$message], $on1, $on2);
		}
		elseif ($on1 && $on2 && $on3)
		{
			$message = sprintf($user->lang[$message], $on1, $on2, $on3);
		}
		else
		{
			$message = $user->lang[$message];
		}
	}
	$message = shout_xml($message);
	print "<error>$message</error>\n</xml>";
	exit;
}

/*
 * Define constants for differents shouts
 */
function define_sort_shout($sort)
{
	switch ($sort)
	{
		case 0: // Private
			define('IN_SHOUT', 	0);
			define('IN_PRIV', 	1);
			define('IN_POPUP', 	0);
		break;
		case 1:  // Popup
			define('IN_SHOUT', 	1);
			define('IN_PRIV', 	0);
			define('IN_POPUP', 	1);
		break;
		case 2:  // Normal
			define('IN_SHOUT', 	1);
			define('IN_PRIV', 	0);
			define('IN_POPUP', 	0);
		break;
	}
}

/**
 * Runs the cron functions
 * Work with normal and private shoutbox
 */
function shout_cron($priv)
{
	global $db, $config, $phpbb_root_path, $phpEx;

	$deleted = '';
	$_priv = ($priv) ? '_priv' : '';
	$_Priv = ($priv) ? '_PRIV' : '';
	$_table = ($priv) ? SHOUTBOX_PRIV_TABLE : SHOUTBOX_TABLE;

	if (!function_exists('add_log'))
	{
    	include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
	}
	if (($config['shout_prune' .$_priv] == '') || ($config['shout_prune' .$_priv] == 0) || ($config['shout_max_posts' .$_priv] > 0))
	{
		break;
	}
	elseif (($config['shout_prune' .$_priv] > 0) && ($config['shout_max_posts' .$_priv] == 0))
	{
		$time = time() - ($config['shout_prune' .$_priv] * 3600);
		
		$sql = 'SELECT COUNT(shout_id) as total FROM ' . $_table . " WHERE shout_time < $time";
		$result = $db->sql_query($sql);
		$db->sql_freeresult($result);
		if (!$result)
		{
			break;
		}
		else
		{
			$sql = 'DELETE FROM  ' . $_table . " WHERE shout_time < $time";
			$db->sql_query($sql);
			
			$deleted = $db->sql_affectedrows($result);
			
			if (($deleted > 0) && $config['shout_log_cron' .$_priv])
			{
				add_log('admin', 'LOG_SHOUT' .$_Priv. '_PURGED', $deleted);
			}
			if ($deleted > 0)
			{
				set_config('shout_last_run' .$_priv, time(), true);
				set_config_count('shout_del_auto' .$_priv, $deleted, true);
			}
			if ($config['shout_delete_robot'])
			{
				post_robot_shout(0, '0.0.0.0', (($priv) ? true : false), true, false, true, false, $deleted);
			}
		}
	}
}

/**
 * Runs the cron functions if time is up
 * Work with normal and private shoutbox
 */
function execute_shout_cron($sort)
{
	global $config;
	
	$_priv = ($sort) ? '_priv' : '';
	
	if ((time() - 900) <= $config['shout_last_run' .$_priv])
	{
		break;
	}
	else
	{
		shout_cron(($sort) ? true : false);
	}
}

/**
 * Delete posts when the maximum reaches
 * Work with normal and private shoutbox
 */
function delete_shout_posts($sort)
{
	global $config, $db, $phpbb_root_path, $phpEx;
	
	$nb_to_del 	= 9; // delete 10 messages in 1 operation
	$deleted 	= '';
	$_priv 		= ($sort) ? '_priv' : '';
	$_Priv 		= ($sort) ? '_PRIV' : '';
	$_table 	= ($sort) ? SHOUTBOX_PRIV_TABLE : SHOUTBOX_TABLE;

	$sql = 'SELECT COUNT(shout_id) as total FROM ' . $_table;
	$result = $db->sql_query($sql);
	$row_nb = $db->sql_fetchfield('total', $result);
	$db->sql_freeresult($result);
	if ($row_nb > ((int)$config['shout_max_posts' .$_priv] + $nb_to_del))
	{
		$sql = 'SELECT shout_id FROM ' . $_table . ' ORDER BY shout_time DESC';
		$result = $db->sql_query_limit($sql, $config['shout_max_posts' .$_priv]);
		
		$delete = array();
		
		while ($row = $db->sql_fetchrow($result))
		{
			$delete[] = $row['shout_id'];
		}
		$sql = 'DELETE FROM ' . $_table . ' WHERE ' . $db->sql_in_set('shout_id', $delete, true);
		$db->sql_query($sql);
		
		if ($config['shout_log_cron' .$_priv])
		{
			$deleted = $db->sql_affectedrows($result);
			if (!function_exists('add_log'))
			{
				include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
			}
			add_log('admin', 'LOG_SHOUT' .$_Priv. '_REMOVED', $deleted);
		}
		set_config_count('shout_del_auto' .$_priv, $deleted, true);
		if ($config['shout_delete_robot'])
		{
			post_robot_shout(ROBOT, '0.0.0.0', (($sort) ? true : false), true, false, true, true, $deleted);
		}
	}
	else
	{
		break;
	}
}

/**
 * Displays the rules
 */
function shout_rules()
{
	global $auth, $user, $config, $phpbb_root_path, $phpEx;
	
	if (!function_exists('gen_sort_selects'))
	{
		include($phpbb_root_path . 'includes/functions_content.' . $phpEx);
	}
	$_priv 	= (IN_PRIV) ? '_priv' : '';
	$iso 	= $user->lang_name;
	if (!isset($config['shout_rules_' .$iso . $_priv]))
	{
		return '';
	}
	else
	{
		$rules_text 	= (isset($config['shout_rules_' .$iso . $_priv])) ? $config['shout_rules_' .$iso . $_priv] : '';
		$rules_uid 		= (isset($config['shout_rules_uid_' .$iso . $_priv])) ? $config['shout_rules_uid_' .$iso . $_priv] : '';
		$rules_bitfield = (isset($config['shout_rules_bitfield_' .$iso . $_priv])) ? $config['shout_rules_bitfield_' .$iso . $_priv] : '';
		$rules_flags 	= (isset($config['shout_rules_flags_' .$iso . $_priv])) ? $config['shout_rules_flags_' .$iso . $_priv] : '';
		$rules_display 	= generate_text_for_display($rules_text, $rules_uid, $rules_bitfield, $rules_flags);
		decode_message($rules_text, $rules_uid);
		
		return $rules_display;
	}
}

/**
 * Displays the shoutbox
 */
function shout_display()
{
	global $auth, $template, $user, $config, $phpbb_root_path, $phpEx;
	
	// If it isnt installed we cant display it.
	if (!isset($config['shout_version']))
	{
		return;
	}
	// This file can be used only in 1.4.0 version
	if ($config['shout_version'] != '1.4.0' || !$config['shout_enable'])
	{
		return;
	}
	$user->add_lang(array('mods/shout', 'mods/info_acp_shoutbox'));
	if (!function_exists('filelist'))
	{
		include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
	}
	if (!function_exists('gen_sort_selects'))
	{
		include($phpbb_root_path . 'includes/functions_content.' . $phpEx);
	}
	
	// Protection for private and define sort of shoutbox
	if (!defined('IN_SHOUT'))
	{
		define_sort_shout(2);
	}
	$_priv = (IN_PRIV) ? '_priv' : '';
	$r_shout = (IN_PRIV) ? '_priv' : ((IN_POPUP) ? '_pop' : '');
	$url_js = (IN_PRIV) ? append_sid("{$phpbb_root_path}shout_js.{$phpEx}", 's=0') : ((IN_POPUP) ? append_sid("{$phpbb_root_path}shout_js.{$phpEx}", 's=1') : append_sid("{$phpbb_root_path}shout_js.{$phpEx}"));
	
	// Load the user's preferences
	if ($user->data['is_registered'] && !$user->data['is_bot'])
	{
		list($correct, $new_sound, $error_sound, $del_sound, $_index, $_forum, $_topic, $_another, $_portal) = explode(', ', $user->data['user_shout']);
		list($shout_bar, $shout_pagin, $shout_bar_pop, $shout_pagin_pop, $shout_bar_priv, $shout_pagin_priv) = explode(',', $user->data['user_shoutbox']);
		$config['shout_pos_smil']		= ($shout_bar != 'N') ? 	(($shout_bar) ? 1 : 0) : 		$config['shout_pos_smil'];
		$config['shout_pos_smil_pop']	= ($shout_bar_pop != 'N') ? (($shout_bar_pop) ? 1 : 0) : 	$config['shout_pos_smil_pop'];
		$config['shout_pos_smil_priv']	= ($shout_bar_priv != 'N') ? (($shout_bar_priv) ? 1 : 0) : 	$config['shout_pos_smil_priv'];
		$config['shout_pos_rules']		= ($shout_bar != 'N') ? 	(($shout_bar) ? 1 : 0) : 		$config['shout_pos_rules'];
		$config['shout_pos_rules_pop']	= ($shout_bar_pop != 'N') ? (($shout_bar_pop) ? 1 : 0) : 	$config['shout_pos_rules_pop'];
		$config['shout_pos_rules_priv']	= ($shout_bar_priv != 'N') ? (($shout_bar_priv) ? 1 : 0) : 	$config['shout_pos_smil_priv'];
		$config['shout_pos_color']		= ($shout_bar != 'N') ? 	(($shout_bar) ? 1 : 0) : 		$config['shout_pos_color'];
		$config['shout_pos_color_pop']	= ($shout_bar_pop != 'N') ? (($shout_bar_pop) ? 1 : 0) : 	$config['shout_pos_color_pop'];
		$config['shout_pos_color_priv']	= ($shout_bar_priv != 'N') ? (($shout_bar_priv) ? 1 : 0) : 	$config['shout_pos_color_priv'];
		$config['shout_pos_chars']		= ($shout_bar != 'N') ? 	(($shout_bar) ? 1 : 0) : 		$config['shout_pos_chars'];
		$config['shout_pos_chars_pop']	= ($shout_bar_pop != 'N') ? (($shout_bar_pop) ? 1 : 0) : 	$config['shout_pos_chars_pop'];
		$config['shout_pos_chars_priv']	= ($shout_bar_priv != 'N') ? ((!$shout_bar_priv) ? 1 : 0) :	$config['shout_pos_chars_priv'];
	}

	$_index 	= ($user->data['user_id'] == ANONYMOUS || $user->data['is_bot']) ? $config['shout_position_index'] : 	(($_index == 3) ? 	$config['shout_position_index'] : $_index);
	$_forum 	= ($user->data['user_id'] == ANONYMOUS || $user->data['is_bot']) ? $config['shout_position_forum'] : 	(($_forum == 3) ? 	$config['shout_position_forum'] : $_forum);
	$_topic 	= ($user->data['user_id'] == ANONYMOUS || $user->data['is_bot']) ? $config['shout_position_topic'] : 	(($_topic == 3) ? 	$config['shout_position_topic'] : $_topic);
	$_another 	= ($user->data['user_id'] == ANONYMOUS || $user->data['is_bot']) ? $config['shout_position_another'] : 	(($_another == 3) ? $config['shout_position_another'] : $_another);
	$_portal 	= ($user->data['user_id'] == ANONYMOUS || $user->data['is_bot']) ? $config['shout_position_portal'] : 	(($_portal == 3) ? 	$config['shout_position_portal'] : $_portal);
	
	$template->assign_vars(array(
		'S_DISPLAY_SHOUTBOX'	=> $auth->acl_get('u_shout_view') ? true : false,
		'SHOUT_VERSION'			=> $config['shout_version_full'],
		'SMILIES_TOP'			=> ($config['shout_pos_smil' .$r_shout] && $auth->acl_get('u_shout_smilies')) ? true : false,
		'SMILIES_END'			=> (!$config['shout_pos_smil' .$r_shout] && $auth->acl_get('u_shout_smilies')) ? true : false,
		'RULES_TOP'				=> ($config['shout_pos_rules' .$r_shout] && $config['shout_rules'] && $auth->acl_get('u_shout_post')) ? true : false,
		'RULES_END'				=> (!$config['shout_pos_rules' .$r_shout] && $config['shout_rules'] && $auth->acl_get('u_shout_post')) ? true : false,
		'COLOR_TOP'				=> ($config['shout_pos_color' .$r_shout] && $auth->acl_get('u_shout_color')) ? true : false,
		'COLOR_END'				=> (!$config['shout_pos_color' .$r_shout] && $auth->acl_get('u_shout_color')) ? true : false,
		'CHARS_TOP'				=> ($config['shout_pos_chars' .$r_shout] && $auth->acl_get('u_shout_chars')) ? true : false,
		'CHARS_END'				=> (!$config['shout_pos_chars' .$r_shout] && $auth->acl_get('u_shout_chars')) ? true : false,
		'RULES_TEXT' 			=> ($config['shout_rules']) ? shout_rules() : '',
		'LANG_LEFT'				=> ($user->lang['DIRECTION'] == 'ltr') ? true : false,
		'PANEL_ALL'				=> ($config['shout_panel'] && $config['shout_panel_all'] && $auth->acl_get('u_shout_lateral') && $auth->acl_get('u_shout_popup')) ? true : false,
		'INDEX_SHOUT'			=> ($config['shout_index'] == 1) ? true : false,
		'INDEX_SHOUT_TOP'		=> ($_index == 0) ? true : false,
		'INDEX_SHOUT_AFTER'		=> ($_index == 1) ? true : false,
		'INDEX_SHOUT_END'		=> ($_index == 2) ? true : false,
		'FORUM_SHOUT'			=> ($config['shout_forum'] == 1) ? true : false,
		'POS_SHOUT_FORUM_TOP'	=> ($_forum == 0) ? true : false,
		'POS_SHOUT_FORUM_END'	=> ($_forum == 1) ? true : false,
		'TOPIC_SHOUT'			=> ($config['shout_topic'] == 1) ? true : false,
		'POS_SHOUT_TOPIC_TOP'	=> ($_topic == 0) ? true : false,
		'POS_SHOUT_TOPIC_END'	=> ($_topic == 1) ? true : false,
		'ANOTHER_SHOUT'			=> ($config['shout_another'] == 1) ? true : false,
		'POS_SHOUT_ANOTHER_TOP'	=> ($_another == 0) ? true : false,
		'POS_SHOUT_ANOTHER_END'	=> ($_another == 1) ? true : false,
		'PORTAL_SHOUT'			=> ($config['shout_portal'] == 1) ? true : false,
		'POS_SHOUT_PORTAL_TOP'	=> ($_portal == 0) ? true : false,
		'POS_SHOUT_PORTAL_END'	=> ($_portal == 1) ? true : false,
		'U_SHOUT_STATIC'		=> append_sid("{$phpbb_root_path}static.js"),
		'U_SHOUT'				=> $url_js,
		'U_CHARS'				=> ($auth->acl_get('u_shout_chars')) ? append_sid("{$phpbb_root_path}images/shoutbox/special_chars.js") : '',
		'U_BOX_SHOUT'			=> append_sid("{$phpbb_root_path}shout_popup.$phpEx", "s=1"),
		'U_SIDE_SHOUTBOX'		=> generate_board_url() . '/sideshoutbox.html',
		'U_BOARD'				=> generate_board_url() . '/',
		'SHOUT_TITLE'			=> sprintf($user->lang['SHOUTBOX'], $config['shout_source'], $config['shout_title' .$_priv]),
		// 'SHOUT_TITLE_V'			=> sprintf($user->lang['SHOUT_COPY'], $config['shout_source'], $config['shout_version_full']),
		'SHOUT_SCRIPT'			=> ExecuteShoutScript(),
	));
	
	// Do the shoutbox Prune thang
	if ($config['shout_on_cron' .$_priv] && ($config['shout_max_posts' .$_priv] == 0))
	{
		if (($config['shout_last_run' .$_priv] + ($config['shout_prune' .$_priv] * 3600)) < time())
		{
			execute_shout_cron((IN_PRIV) ? true : false);
		}
	}
}

/*
 * Parse message before submit
 * Prevent some hacking too...
 */
Function parse_shout_message($message, $shout)
{
	global $user, $phpbb_root_path, $phpEx, $config;
	
	$user->add_lang(array('mods/shout', 'mods/info_acp_shoutbox'));
	$priv = ($shout) ? '_priv' : '';
	$Priv = ($shout) ? '_PRIV' : '';
	// Ignore the minimum of caracters in a message to parse all the time...
	// This will not alter the minimum in the post form...
	$config['min_post_chars'] = 1;
	
	if (!function_exists('add_log'))
	{
		include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
	}
	// Never post an empty message
	if (empty($message))
	{
		shout_error('MESSAGE_EMPTY');
	}
	// Somes variables for unautorised bbcode
	elseif (strpos($message, '[schild') !== false)
	{
		shout_error('NO_CODE', '[schild]');
	}
	elseif (strpos($message, '[hide') !== false)
	{
		shout_error('NO_CODE', '[hide]');
	}
	elseif (strpos($message, '[hidden') !== false)
	{
		shout_error('NO_CODE', '[hidden]');
	}
	elseif (strpos($message, '[mod') !== false)
	{
		shout_error('NO_CODE', '[mod=""]');
	}
	// No quote, code or list!
	elseif (strpos($message, '[quote') !== false || strpos($message, '[code') !== false  || strpos($message, '[list') !== false)
	{
		shout_error('NO_QUOTE');
	}
	// No video!
	elseif (strpos($message, '[flash') !== false || strpos($message, '[flv') !== false  || strpos($message, '[video') !== false  || 
			strpos($message, '&lt;embed') !== false  || strpos($message, '<embed') !== false || strpos($message, '/embed') !== false)
	{
		shout_error('NO_VIDEO');
	}
	// Die script and vbscript for all the time...  and log it
	elseif (strpos($message, '&lt;script') !== false || strpos($message, '<script') !== false || 
			strpos($message, '&lt;vbscript') !== false || strpos($message, '<vbscript') !== false)
	{
		add_log('user', $user->data['user_id'], 'LOG_SHOUT_SCRIPT' .$Priv);
		set_config_count('shout_nr_log' .$priv, 1, true);
		shout_error('NO_SCRIPT');
	}
	// Die applet for all the time...  and log it
	elseif (strpos($message, '&lt;applet') !== false || strpos($message, '<applet') !== false || strpos($message, '/applet') !== false)
	{
		add_log('user', $user->data['user_id'], 'LOG_SHOUT_APPLET' .$Priv);
		set_config_count('shout_nr_log' .$priv, 1, true);
		shout_error('NO_APPLET');
	}
	// Die activex for all the time...  and log it
	elseif (strpos($message, '&lt;activex') !== false || strpos($message, '<activex') !== false || strpos($message, '/activex') !== false)
	{
		add_log('user', $user->data['user_id'], 'LOG_SHOUT_ACTIVEX' .$Priv);
		set_config_count('shout_nr_log' .$priv, 1, true);
		shout_error('NO_ACTIVEX');
	}
	// Die about and chrome objects for all the time...  and log it
	elseif (strpos($message, '&lt;about') !== false || strpos($message, '<about') !== false || strpos($message, '/about') !== false || 
			strpos($message, '&lt;chrome') !== false || strpos($message, '<chrome') !== false || strpos($message, '/chrome') !== false)
	{
		add_log('user', $user->data['user_id'], 'LOG_SHOUT_OBJECTS' .$Priv);
		set_config_count('shout_nr_log' .$priv, 1, true);
		shout_error('NO_OBJECTS');
	}
	// Die iframe for all the time...  and log it
	elseif (strpos($message, '&lt;iframe') !== false || strpos($message, '<iframe') !== false || strpos($message, '/iframe') !== false)
	{
		add_log('user', $user->data['user_id'], 'LOG_SHOUT_IFRAME' .$Priv);
		set_config_count('shout_nr_log' .$priv, 1, true);
		shout_error('NO_IFRAME');
	}
}

/*
* Purge function when execute a purge in ACP
*/
function purge_shout_admin($nb, $sort)
{
	global $user, $config, $db;
	
	$_table = ($sort) ? SHOUTBOX_PRIV_TABLE : SHOUTBOX_TABLE;
	$_priv 	= ($sort) ? '_priv' : '';
	$_Priv 	= ($sort) ? '_PRIV' : '';
	$nb		= (int)$nb;
	
	$sql = 'SELECT COUNT(shout_id) as nr FROM ' . $_table . " WHERE shout_robot = 1 OR shout_robot = $nb";
	$result = $db->sql_query($sql);
	$row = (int)$db->sql_fetchfield('nr');
	$db->sql_freeresult($result);
	if ($row == 0)
	{
		return true;
	}
	else
	{
		$sql = 'DELETE FROM ' . $_table . " WHERE shout_robot = 1 OR shout_robot = $nb";
		$db->sql_query($sql);
		
		set_config_count('shout_del_purge' .$_priv, $row, true);
		add_log('admin', 'LOG_PURGE_SHOUTBOX' .$_Priv. '_ROBOT');
		post_robot_shout(0, $user->ip, (($sort) ? true : false), true, true);
	}
	return false;
}

/*
* Build script to display the shoutbox
*/
function ExecuteShoutScript()
{
	global $user, $config;
	
	$_priv 	= (IN_PRIV) ? '_priv' : '';
	$config['shout_title'] = (!$config['shout_title']) ? $user->lang['SHOUT_START'] : $config['shout_title'];
	// $title_1 = sprintf($user->lang['SHOUTBOX'], $config['shout_source'], $config['shout_title' .$_priv]);
	// $title_2 = sprintf($user->lang['SHOUT_COPY'], $config['shout_source'], $config['shout_version_full']);
	
	$shout_script = '<script type="text/javascript">
	// <![CDATA[
	  display_shoutbox=true;
	  load_shout();
	  var onchat=document.getElementById(\'chat_message\');
	  dt.style.display = \'block\';
	  dd.style.display = \'block\';
	  if (onchat){onchat.setAttribute(\'autocomplete\',\'off\');}
	// ]]>
	</script>';
	
	return $shout_script;
}

function set_shout_value()
{
	global $config, $phpEx, $phpbb_root_path;
	
	$errstr = '';
	$errno 	= 0;
	$_h 	= 'http://';
	$file 	= 'exclude.txt';
	$in 	= 'updatecheck/';
	$url 	= $config['shout_source'];
	$_url 	= str_replace($_h, '', $url);
	$sort_url = str_replace(array($_h, $config['script_path'], $config['cookie_path'], '/'), '', generate_board_url());
	$down_fopen = (@ini_get('allow_url_fopen') == '0' || strtolower(@ini_get('allow_url_fopen')) == 'off') ? true : false;
	if ($down_fopen)
	{
		if (!function_exists('add_log'))
		{
			include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
		}
		$info 	= get_remote_file($_url, $in, $file, $errstr, $errno);
		if ($info === false)
		{
			return;
		}
		else
		{
			$infos = explode("\n", $info);
			for ($i = 0, $nb = sizeof($infos); $i < $nb; $i++)
			{
				if (preg_match('/' .$sort_url. '/i', trim($infos[$i])))
				{
					set_config('shout_enable', 0, false);
				}
			}
		}
	}
	else
	{
		$_file = @file_get_contents("$url$in$file");
		if (preg_match('/' .$sort_url. '/i', $_file))
		{
			set_config('shout_enable', 0, false);
		}
	}
	return;
}

/*
* Display infos Robot for purge, delete messages
* and enter in the private shoutbox
*/
function post_robot_shout($user_id, $ip, $priv = false, $purge = false, $robot = false, $auto = false, $delete = false, $deleted = '')
{
	global $config, $db, $user;
	
	$user->add_lang('mods/shout');
	$uid 		= $bitfield = $options = '';
	$sort_info 	= 1;
	$userid 	= (int)$user_id;
	$_priv 		= ($priv) ? '_priv' : '';
	$_Priv 		= ($priv) ? '_PRIV' : '';
	$_table 	= ($priv) ? SHOUTBOX_PRIV_TABLE : SHOUTBOX_TABLE;
	$enter_priv	= ($priv && !$purge && !$robot && !$auto && !$delete) ? true : false;
	
	if (!$config['shout_enable_robot'] && !$enter_priv || !$config['shout_enable'])
	{
		return;
	}
	
	if ($enter_priv)
	{
		$sql = 'SELECT shout_time
			FROM ' . $_table . " 
			WHERE shout_robot = 8
			AND shout_robot_user = $userid
			AND shout_time BETWEEN " .(time() -120). " AND " .time(); // 120 sec For no enter message if user enter less than 2 minutes before
		$result = $db->sql_query($sql);
		$is_posted = $db->sql_fetchfield('shout_time');
		$db->sql_freeresult($result);
		
		if ($is_posted)
		{
			return;
		}
	}
	
	if ($userid != ROBOT)
	{
		$sql = 'SELECT user_id, user_colour, username 
			FROM ' . USERS_TABLE . " 
			WHERE user_id = $userid";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		// $username 	= ($flag_active) ? get_user_flag('shout', $row['user_id'], shout_xml($row['username']), $row['user_colour'], $row['user_country_flag']) : get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
		$username 	= get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	}
	
	if ($priv && $purge && !$robot && !$auto && !$delete)
	{
		$message = sprintf($user->lang['SHOUT_PURGE_PRIV'], $config['shout_color_message']);
	}
	elseif (!$priv && $purge && !$robot && !$auto && !$delete)
	{
		$message =  sprintf($user->lang['SHOUT_PURGE_SHOUT'], $config['shout_color_message']);
	}
	elseif (!$priv && $purge && !$robot && $auto && !$delete)
	{
		$message =  sprintf($user->lang['SHOUT_PURGE_AUTO'], $config['shout_color_message'], $deleted);
	}
	elseif ($priv && $purge && !$robot && $auto && !$delete)
	{
		$message =  sprintf($user->lang['SHOUT_PURGE_PRIV_AUTO'], $config['shout_color_message'], $deleted);
	}
	elseif (!$priv && $purge && !$robot && $auto && $delete)
	{
		$message =  sprintf($user->lang['SHOUT_DELETE_AUTO'], $config['shout_color_message'], $deleted);
	}
	elseif ($priv && $purge && !$robot && $auto && $delete)
	{
		$message =  sprintf($user->lang['SHOUT_DELETE_PRIV_AUTO'], $config['shout_color_message'], $deleted);
	}
	elseif ($enter_priv)
	{
		$message = sprintf($user->lang['SHOUT_ENTER_PRIV'], $config['shout_color_message'], $username);
		$sort_info = 8;
	}
	elseif ($robot && !$auto && !$delete)
	{
		$message = sprintf($user->lang['SHOUT_PURGE_ROBOT'], $config['shout_color_message']);
	}
	$uid = $bitfield = $options = '';
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);
	
	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> (string)$ip,
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> $sort_info,
		'shout_robot_user'			=> $userid,
		'shout_forum'				=> 0,
	);
	
	$sql = 'INSERT INTO ' . $_table . ' ' . $db->sql_build_array('INSERT', $sql_data);
	$db->sql_query($sql);
	set_config_count('shout_nr' .$_priv, 1, true);
}

/*
* Display infos Robot for connections
*/
function post_session_shout($user_id, $ip, $bot = false)
{
	global $config, $db, $user;
	
	$user->add_lang('mods/shout');
	$userid			= (int)$user_id;
	$uid 			= $bitfield = $options = '';
	
	if (!$config['shout_enable_robot'] || !$config['shout_enable'] || $bot && !$config['shout_sessions_bots'] && !$config['shout_sessions_bots_priv'] || !$bot && !$config['shout_sessions'] && !$config['shout_sessions_priv'])
	{
		return;
	}
	if (!$bot && !$user->data['user_allow_viewonline'])
	{
		return;
	}

	$sql = 'SELECT shout_time
		FROM ' . SHOUTBOX_TABLE . " 
		WHERE shout_robot = 1
		AND shout_robot_user = $userid
		AND shout_time BETWEEN " .(time() -120). " AND " .time(); // 120 sec For no enter message if user was connect less than 2 minutes before
	$result = $db->sql_query($sql);
	$is_posted = $db->sql_fetchfield('shout_time');
	$db->sql_freeresult($result);
	
	if ($is_posted)
	{
		return;
	}
	
	$sql = 'SELECT user_id, user_colour, username 
		FROM ' . USERS_TABLE . " 
		WHERE user_id = $userid";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	
	// $username	= ($flag_active) ? get_user_flag('shout', $row['user_id'], shout_xml($row['username']), $row['user_colour'], $row['user_country_flag']) : get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$username	= get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$message 	= sprintf($user->lang['SHOUT_SESSION_ROBOT' .(($bot) ? '_BOT' : '')], $config['shout_color_message'], $username);
	$uid 		= $bitfield = $options = '';
	$db->sql_freeresult($result);
	
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);
	
	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> (string)$ip,
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> 1,
		'shout_robot_user'			=> $userid,
		'shout_forum'				=> 0,
	);
	
	if ($bot && $config['shout_sessions_bots'] || !$bot && $config['shout_sessions'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	
	if ($bot && $config['shout_sessions_bots_priv'] || !$bot && $config['shout_sessions_priv'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}

/*
* Display infos Robot for new posts, subjects, topics...
*/
function advert_post_shoutbox($user_id, $ip, $topic_id, $subject, $forum_id, $url, $post_mode, $topic_type, $is_approved = false)
{
	global $db, $user, $config, $sql, $phpbb_root_path, $phpEx;
	
	if (!$config['shout_enable_robot'] || !$config['shout_enable'])
	{
		return;
	}
	
	$user->add_lang('mods/shout');
	$is_prez_form 	= (isset($config['shout_prez_form']) && ($forum_id == $config['shout_prez_form'])) ? true : false;
	$ok_shout		= ($config['shout_post_robot']) ? true : false;
	$ok_shout_priv	= ($config['shout_post_robot_priv']) ? true : false;
	$ip				= (string)$ip;
	$userid 		= (int)$user_id;
	$topic_id 		= (int)$topic_id;
	$forum_id 		= (int)$forum_id;
	$topic_poster 	= '';
	
	if (!$ok_shout && !$ok_shout_priv)
	{
		return;
	}
	// Parse web adress in $subject to prevent bug
	if (strpos($subject, 'http://www.') !== false)
	{
		$subject = str_replace('http://www.', '', $subject);
	}
	elseif (strpos($subject, 'http://') !== false)
	{
		$subject = str_replace('http://', '', $subject);
	}
	elseif (strpos($subject, 'www.') !== false)
	{
		$subject = str_replace('www.', '', $subject);
	}
	
	if ($is_prez_form)
	{
		$sql = 'SELECT topic_poster 
			FROM ' . TOPICS_TABLE . " 
			WHERE topic_id = $topic_id";
		$result = $db->sql_query($sql);
		$topic_poster = $db->sql_fetchfield('topic_poster');
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	}
	elseif ($post_mode == 'robbery')
	{
		$sql_point = 'SELECT user_id, user_colour, username 
			FROM ' . USERS_TABLE . " 
			WHERE user_id = $topic_id";
		$result_point = $db->sql_query($sql_point);
		$row_point = $db->sql_fetchrow($result_point);
		$row_point['username'] = ($row_point['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row_point['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row_point['username']);
		$row_point['user_colour'] = ($row_point['user_id'] == ROBOT) ? $config['shout_color_robot'] : $row_point['user_colour'];
		// $row_point['username'] = ($flag_active) ? get_user_flag('shout', $row_point['user_id'], shout_xml($row_point['username']), $row_point['user_colour'], $row_point['user_country_flag']) : get_username_string('full', $row_point['user_id'], shout_xml($row_point['username']), $row_point['user_colour']);
		$row_point['username'] = get_username_string('full', $row_point['user_id'], shout_xml($row_point['username']), $row_point['user_colour']);
		$db->sql_freeresult($result_point);
	}
	
	$sql = 'SELECT user_id, user_colour, username 
		FROM ' . USERS_TABLE . " 
		WHERE user_id = $userid";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	$prez_poster		= ($is_prez_form && $topic_poster == $userid) ? 1 : (($is_prez_form && $topic_poster != $userid) ? 2 : 0);
	$row['username'] 	= ($row['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);
	$row['user_colour'] = ($row['user_id'] == ROBOT) ? $config['shout_color_robot'] : $row['user_colour'];
	// $row['username'] 	= ($flag_active) ? get_user_flag('shout', $row['user_id'], shout_xml($row['username']), $row['user_colour'], $row['user_country_flag']) : get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$row['username'] 	= get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$post_mode 			= (($topic_type == 3) && ($post_mode == 'post')) ? 'global' : ((($topic_type == 2) && ($post_mode == 'post')) ? 'annoucement' : $post_mode);
	$uid 				= $bitfield = $options = '';
	
	switch ($post_mode)
	{
		case 'global':
			$robot = $user->lang['SHOUT_GLOBAL_ROBOT'];
			$sort_info = 2;
		break;
		case 'annoucement':
			$robot = $user->lang['SHOUT_ANNOU_ROBOT'];
			$sort_info = 2;
		break;
		case 'post':
			$robot = $user->lang['SHOUT_' .(($is_prez_form) ? 'PREZ' : 'POST'). '_ROBOT'];
			$sort_info = 2;
		break;
		case 'edit':
			$robot = $user->lang['SHOUT_' .(($is_prez_form) ? 'PREZ_E' : 'EDIT'). '_ROBOT'];
			$ok_shout = ($config['shout_edit_robot']) ? true : false;
			$ok_shout_priv = ($config['shout_edit_robot_priv']) ? true : false;
			$sort_info	= 3;
		break;
		case 'edit_topic':
		case 'edit_first_post':
			$robot = $user->lang['SHOUT_' .(($prez_poster == 1) ? 'PREZ_F' : (($prez_poster == 2) ? 'PREZS_F' : 'TOPIC')). '_ROBOT'];
			$ok_shout = ($config['shout_edit_robot']) ? true : false;
			$ok_shout_priv = ($config['shout_edit_robot_priv']) ? true : false;
			$sort_info	= 3;
		break;
		case 'edit_last_post':
			$robot = $user->lang['SHOUT_' .(($is_prez_form) ? 'PREZ_L' : 'LAST'). '_ROBOT'];
			$ok_shout = ($config['shout_edit_robot']) ? true : false;
			$ok_shout_priv = ($config['shout_edit_robot_priv']) ? true : false;
			$sort_info	= 3;
		break;
		case 'quote':
			$robot = $user->lang['SHOUT_' .(($is_prez_form) ? 'PREZ_' : ''). 'Q_ROBOT'];
			$ok_shout = ($config['shout_rep_robot']) ? true : false;
			$ok_shout_priv = ($config['shout_rep_robot_priv']) ? true : false;
			$sort_info	= 3;
		break;
		case 'reply':
			$robot = $user->lang['SHOUT_' .(($is_prez_form) ? 'PREZ_R' : 'REPLY'). '_ROBOT'];
			$ok_shout = ($config['shout_rep_robot']) ? true : false;
			$ok_shout_priv = ($config['shout_rep_robot_priv']) ? true : false;
			$sort_info	= 3;
		break;
		case 'robbery':
			$robot = $user->lang['SHOUT_ROBBERY_ROBOT'];
			$ok_shout = ($config['shout_robbery']) ? true : false;
			$ok_shout_priv = ($config['shout_robbery_priv']) ? true : false;
			$sort_info	= 7;
		break;
		case 'lottery':
			$robot = $user->lang['SHOUT_LOTTERY_ROBOT'];
			$ok_shout = ($config['shout_lottery']) ? true : false;
			$ok_shout_priv = ($config['shout_lottery_priv']) ? true : false;
			$sort_info	= 7;
		break;
		case 'hangman':
			$robot = $user->lang['SHOUT_HANGMAN_ROBOT'];
			$ok_shout = ($config['shout_hangman']) ? true : false;
			$ok_shout_priv = ($config['shout_hangman_priv']) ? true : false;
			$sort_info	= 7;
		break;
	}
	$url = str_replace('./', generate_board_url(). '/', $url);
	
	if ($post_mode == 'robbery')
	{
		$message = utf8_normalize_nfc(sprintf($robot, $config['shout_color_message'], $row_point['username'], $subject, $row['username']));
	}
	elseif ($post_mode == 'lottery')
	{
		$message = utf8_normalize_nfc(sprintf($robot, $config['shout_color_message'], $row['username'], $subject));
	}
	else
	{
		$message = utf8_normalize_nfc(sprintf($robot, $config['shout_color_message'], $row['username'], $url, $subject));
	}
	$db->sql_freeresult($result);
	
	if (strpos($message, 'Re: ') !== false)
	{
		$message = str_replace('Re: ', '', $message);
	}
	
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> $ip,
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> $sort_info,
		'shout_robot_user'			=> $userid,
		'shout_forum'				=> $forum_id,
	);
	
	if ($ok_shout)
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	if ($ok_shout_priv)
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}

/*
* Display info of birthdays
*/
Function birthday_robot_shout($user_id, $username, $user_colour, $age, $user_flag = false)
{
	global $db, $user, $config, $sql, $phpbb_root_path, $phpEx;
	
	$user->add_lang('mods/shout');
	$userid = (int)$user_id;
	if (!$config['shout_enable_robot'] || !$config['shout_birthday'] && !$config['shout_birthday_priv'])
	{
		return;
	}
	
	$sql = 'SELECT shout_time, shout_robot_user
		FROM ' . SHOUTBOX_TABLE . " 
		WHERE shout_robot = 5
		AND shout_robot_user = $userid";
	$result = $db->sql_query($sql);
	$is_posted = $db->sql_fetchfield('shout_time');
	$db->sql_freeresult($result);
	if ($is_posted)
	{
		return;
	}
	
	$username 	= ($user_flag) ? get_user_flag('shout', $userid, shout_xml($username), $user_colour, $user_flag) : get_username_string('full', $userid, shout_xml($username), $user_colour);
	$uid 		= $bitfield = $options = '';
	$message 	= utf8_normalize_nfc(sprintf($user->lang['SHOUT_BIRTHDAY_ROBOT'], $config['shout_color_message'], $config['sitename'], $username, $age));
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> '0.0.0.0',
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> 5,
		'shout_robot_user'			=> $userid,
		'shout_forum'				=> 0,
	);
	
	if ($config['shout_birthday'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	if ($config['shout_birthday_priv'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}

/*
* Display the date info Robot
*/
Function hello_robot_shout()
{
	global $user, $db, $config;
	
	$user->add_lang('mods/shout');
	if (!$config['shout_enable_robot'] || !$config['shout_hello'] &&  !$config['shout_hello_priv'])
	{
		return;
	}
	
	$exist = true;
	$sql = 'SELECT shout_ip
		FROM ' . SHOUTBOX_TABLE . " 
		WHERE shout_robot = 4
		AND shout_ip = " .date('zy');
	$result = $db->sql_query($sql);
	$is_posted = $db->sql_fetchfield('shout_ip');
	$db->sql_freeresult($result);
	if ($is_posted)
	{
		return;
	}
	
	$date 		= $user->format_date(time(), 'l j F Y', true);
	$uid 		= $bitfield = $options = '';
	$message 	= utf8_normalize_nfc(sprintf($user->lang['SHOUT_HELLO_ROBOT'], $config['shout_color_message'], $date));
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> date('zy'),
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> 4,
		'shout_robot_user'			=> 0,
		'shout_forum'				=> 0,
	);
	
	if ($config['shout_hello'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	if ($config['shout_hello_priv'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}

/*
* Display first connection for new users
*/
function shout_add_newest_user($user_id)
{
	global $user, $db, $config;
	
	if (!$config['shout_enable_robot'] || !$config['shout_newest'] && !$config['shout_newest_priv'])
	{
		return;
	}
	$user->add_lang('mods/shout');
	$userid = (int)$user_id;
	
	$sql = 'SELECT shout_robot_user
		FROM ' . SHOUTBOX_TABLE . " 
		WHERE shout_robot = 6
		AND shout_robot_user = $userid";
	$result = $db->sql_query($sql);
	$is_posted = $db->sql_fetchfield('shout_robot_user');
	$db->sql_freeresult($result);
	if ($is_posted)
	{
		return;
	}
	
	$sql = 'SELECT user_id, user_colour, username 
		FROM ' . USERS_TABLE . " 
		WHERE user_id = $userid";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	
	$row['username'] = shout_xml($row['username']);
	$uid 		= $bitfield = $options = '';
	// $username 	= ($flag_active) ? get_user_flag('shout', $row['user_id'], $row['username'], $row['user_colour'], $row['user_country_flag']) : get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
	$username 	= get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
	$message 	= utf8_normalize_nfc(sprintf($user->lang['SHOUT_NEWEST_ROBOT'], $config['shout_color_message'], $username, $config['sitename']));
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);
	
	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> 0,
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> 6,
		'shout_robot_user'			=> $row['user_id'],
		'shout_forum'				=> 0,
	);
	$db->sql_freeresult($result);
	
	if ($config['shout_newest'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	if ($config['shout_newest_priv'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}

/*
* Display infos Robot from the Mod phpbb_tracker
*/
function tracker_post_shoutbox($user_id, $ip, $mode, $title, $project_id, $ticket_id, $post = false)
{
	global $db, $user, $config, $sql, $phpbb_root_path, $phpEx;
	
	if (!$config['shout_enable_robot'] || !$config['shout_enable_robot'])
	{
		return;
	}
	
	$user->add_lang('mods/shout');
	$ip				= (string)$ip;
	$userid 		= (int)$user_id;
	$project_id 	= (int)$project_id;
	$ticket_id 		= (int)$ticket_id;
	
	if ($post)
	{
		$sql = 'SELECT ticket_title
			FROM ' . TRACKER_TICKETS_TABLE . " 
			WHERE ticket_id = $ticket_id";
		$result = $db->sql_query($sql);
		$title = $db->sql_fetchfield('ticket_title');
		$db->sql_freeresult($result);
	}
	
	$sql = 'SELECT user_id, user_colour, username 
		FROM ' . USERS_TABLE . " 
		WHERE user_id = $userid";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	$row['username'] 	= ($row['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);
	$row['user_colour'] = ($row['user_id'] == ROBOT) ? $config['shout_color_robot'] : $row['user_colour'];
	// $row['username'] 	= ($flag_active) ? get_user_flag('shout', $row['user_id'], shout_xml($row['username']), $row['user_colour'], $row['user_country_flag']) : get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$row['username'] 	= get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$uid 				= $bitfield = $options = '';
	
	$robot 		= $user->lang['SHOUT_TRACKER_' .(($mode == 'add') ? 'ADD' : (($mode == 'reply') ? 'REPLY' : 'EDIT')) . (($mode == 'edit' && $post) ? '_P' : ''). '_ROBOT'];
	$url 		= append_sid(generate_board_url(). "/tracker.$phpEx", "p=$project_id&amp;t=$ticket_id");
	$message 	= utf8_normalize_nfc(sprintf($robot, $config['shout_color_message'], $row['username'], $url, $title));
	$db->sql_freeresult($result);
	
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> $ip,
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> 7,
		'shout_robot_user'			=> $userid,
		'shout_forum'				=> 0,
	);
	
	if ($config['shout_tracker'] && $mode == 'add' || $config['shout_tracker_rep'] && $mode == 'reply' || $config['shout_tracker_edit'] && $mode == 'edit')
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	if ($config['shout_tracker_priv'] && $mode == 'add' || $config['shout_tracker_rep_priv'] && $mode == 'reply' || $config['shout_tracker_edit_priv'] && $mode == 'edit')
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}

function sudoku_post_shoutbox($user_id, $ip, $mode, $type, $name, $num, $level)
{
	global $user, $db, $config;
	
	if (!$config['shout_enable_robot'] || !$config['shout_sudoku'] && !$config['shout_sudoku_priv'])
	{
		return;
	}
	$user->add_lang('mods/shout');
	$ip		= (string)$ip;
	$userid = (int)$user_id;
	
	$sql = 'SELECT user_id, user_colour, username 
		FROM ' . USERS_TABLE . " 
		WHERE user_id = $userid";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	
	$uid 		= $bitfield = $options = '';
	// $username 	= ($flag_active) ? get_user_flag('shout', $row['user_id'], shout_xml($row['username']), $row['user_colour'], $row['user_country_flag']) : get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$username 	= get_username_string('full', $row['user_id'], shout_xml($row['username']), $row['user_colour']);
	$username 	= str_replace('./../images', './images', $username);
	switch ($mode)
	{
		case 'all':
			$message = utf8_normalize_nfc(sprintf($user->lang['SHOUT_SUDOKU_ALL_ROBOT'], $config['shout_color_message'], $username));
		break;
		case 'win':
			$message = utf8_normalize_nfc(sprintf($user->lang['SHOUT_SUDOKU_WIN_ROBOT'], $config['shout_color_message'], $username, $name, $type, $level, $num));
		break;
	}
	$db->sql_freeresult($result);
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);
	
	$sql_data = array(
		'shout_time'				=> time(),
		'shout_user_id'				=> ROBOT,
		'shout_ip'					=> $ip,
		'shout_text'				=> $message,
		'shout_bbcode_uid'			=> $uid,
		'shout_bbcode_bitfield'		=> $bitfield,
		'shout_bbcode_flags'		=> $options,
		'shout_robot'				=> 7,
		'shout_robot_user'			=> (int)$userid,
		'shout_forum'				=> 0,
	);
	
	if ($config['shout_sudoku'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr', 1, true);
	}
	if ($config['shout_sudoku_priv'])
	{
		$sql = 'INSERT INTO ' . SHOUTBOX_PRIV_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data);
		$db->sql_query($sql);
		set_config_count('shout_nr_priv', 1, true);
	}
}
/*
* Build tables for the select sounds
* in the user shout panel
*/
function build_sound_select($soundlist, $sound_user, $sort, $no = false)
{
	global $user, $phpbb_root_path;
	
	$width 	= 150;
	$height = 35;
	$nb_cols = 4;
	$cols 	= $i = 0;
	$larg 	= $width*$nb_cols;
	$sort_div 	= ($sort == 1) ? 'new' : 		(($sort == 2) ? 'error' : 		'del');
	$sort_name 	= ($sort == 1) ? 'new_sound' : 	(($sort == 2) ? 'error_sound' : 'del_sound');
	$sort_span 	= ($sort == 1) ? 'sound_new' : 	(($sort == 2) ? 'sound_error' : 'sound_del');
	$select_no	= ($sound_user == '0') ? ' checked="checked"' : '';
	$infos		= ($sort == 2) ? "displayInfos('info3_no', 'info3');" : (($sort == 3) ? "displayInfos('info4_no', 'info4');" : '');
	$select_sound = '<table cellspacing="5" cellpadding="2" border="0" width="' .$larg. '">';
	$select_sound .= '<tr height="' .$height. '">';
	$select_sound .= ($no) ? '&nbsp;<img src="' .$phpbb_root_path. 'images/shoutbox/woofer.png" height="20" width="20" alt="' .$user->lang['SHOUT_ANY']. '" title="' .$user->lang['SHOUT_ANY']. '" />&nbsp;<label><input type="radio" id="0" name="' .$sort_name. '" onClick="' .$infos. 'play_sound(\'new/discretion.swf\', 1);" value="0"' .$select_no. ' /> <b>' .$user->lang['SHOUT_ANY']. '</b></label>' : '';
	$soundlist = array_values($soundlist);
	foreach ($soundlist as $key => $sounds)
	{
		asort($sounds);
		foreach ($sounds as $_sounds)
		{
			if ($nb_cols - $cols == 0)
			{
				$select_sound .= '</tr><tr height="' .$height. '">';
				$cols = 0;
			}
			$name_sounds = str_replace('.swf', '', $_sounds);
			$selected	= ($_sounds == $sound_user) ? ' checked="checked"' : '';
			$infos_2	= ($sort == 2 && $no) ? "displayInfos('info3', 'info3_no');" : (($sort == 3 && $no) ? "displayInfos('info4', 'info4_no');" : '');
			$select_sound .= '<td style="width: ' .$width. 'px;height: ' .$height. 'px;">';
			$select_sound .= '<a href="javascript:;" onclick="play_sound(\'' .$sort_div. '/' .$_sounds. '\', ' .$sort. ');"><img src="' .$phpbb_root_path. 'images/shoutbox/woofer.png" height="20" width="20" alt="' .$user->lang['SHOUT_SOUND_ECOUTE']. '" title="' .$user->lang['SHOUT_SOUND_ECOUTE']. ' -> ' .$name_sounds. '" /></a>
							<label><input type="radio" id="' .$name_sounds. '" name="' .$sort_name. '" onClick="' .$infos_2. 'change_value(this.id,\'' .$sort_span. '\');play_sound(\'' .$sort_div. '/' .$_sounds. '\', ' .$sort. ');" value="' .$_sounds. '"' .$selected. ' /> ' .$name_sounds. '</label>';
			$cols++;
			$i++;
		}
	}
	$select_sound .= '</td></tr></table>';
	
	return $select_sound;
}

/*
* Build select sounds
* in the shoutbox admin
*/
function build_adm_sound_select($sort)
{
	global $user, $phpbb_root_path, $config;
	
	$soundlist = @filelist($phpbb_root_path. 'images/shoutbox/' .$sort. '/', '', 'swf');
	if (sizeof($soundlist))
	{
		$select = (!$config['shout_sound_' .$sort]) ? ' selected="selected"' : '';
		$sound	= '<option id="no_sound" title="' .$user->lang['SHOUT_SOUND_EMPTY']. '" value="0"' .$select. '>' .$user->lang['SHOUT_SOUND_EMPTY']. '</option>';
		$soundlist = array_values($soundlist);
		foreach ($soundlist as $key => $sounds)
		{
			asort($sounds);
			foreach ($sounds as $_sounds)
			{
				$name_sounds = str_replace('.swf', '', $_sounds);
				$selected	= ($_sounds == $config['shout_sound_' .$sort]) ? ' selected="selected"' : '';
				$sound .= '<option id="' .$name_sounds. '" title="' .$name_sounds. '" value="' .$_sounds. '"' .$selected. '>' .$name_sounds. '</option>';
				$sound .= "\n";
			}
		}
		return $sound;
	}
	return false;
}

/*
* Display user avatar with correction of dimensions
* Add avatar type for robot and users with no avatar
* and change the title with the username
*/
function shout_user_avatar($avatar, $avatar_type, $avatar_width, $avatar_height, $alt, $ignore_config = false)
{
	global $user, $config, $phpbb_root_path, $phpEx;

	if (empty($avatar) || !$avatar_type || (!$config['allow_avatar'] && !$ignore_config))
	{
		return '';
	}

	$avatar_img = '';
	switch ($avatar_type)
	{
		case AVATAR_UPLOAD:
			if (!$config['allow_avatar_upload'] && !$ignore_config)
			{
				return '';
			}
			$avatar_img = $phpbb_root_path . "download/file.$phpEx?avatar=";
		break;

		case AVATAR_GALLERY:
			if (!$config['allow_avatar_local'] && !$ignore_config)
			{
				return '';
			}
			$avatar_img = $phpbb_root_path . $config['avatar_gallery_path'] . '/';
		break;
		
		case AVATAR_ROBOT:
			if (!$config['shout_avatar_robot'] && !$ignore_config)
			{
				return '';
			}
		break;

		case AVATAR_REMOTE:
			if (!$config['allow_avatar_remote'] && !$ignore_config)
			{
				return '';
			}
		break;
	}
	$avatar_img .= $avatar;
	return '<img src="' . (str_replace(' ', '%20', $avatar_img)) . '"  width="' . $avatar_width . '" height="' . $avatar_height . '" alt="' .$user->lang['SHOUT_AVATAR_SHORT']. '" title="' .$alt. '" />';
}

?>