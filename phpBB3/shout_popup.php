<?php
/**
*
* @package breizh Shoutbox
* @version $Id: shout_popup.php 140 20:07 31/12/2010 Sylver35 Exp $ 
* @copyright (c) 2010, 2011 Sylver35  http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/shout');

// $shout define the sort of shout (1=popup  0=private)
$shout 		= request_var('s', -1);
$mode 		= request_var('m', '');
$submit 	= (isset($_POST['submit'])) ? true : false;
$accept 	= (isset($_POST['accept'])) ? true : false;

// Protections
if (!isset($config['shout_version']))
{
	exit;
}
if ($shout != 1 && $shout != 0 && $mode == '' || !$config['shout_enable'])
{
	redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
}

// Define contants
if ($shout == 0)
{
	define_sort_shout(0);
}
elseif ($shout == 1)
{
	define_sort_shout(1);
}
shout_display();
$title = ((IN_POPUP) ? $user->lang['SHOUTBOX_POPUP']. ' &bull; ' : '') . $config['shout_title' .((IN_PRIV) ? '_priv' : '')]. ' &bull; ' .$user->data['username']. ' &bull; ' .$config['shout_version_full'];

// Display the shoutbox in a popup
if ($shout == 1 && $mode == '')
{
	if (!$auth->acl_get('u_shout_popup'))
	{
		redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
	}
	else
	{
		$template->assign_vars(array(
			'S_IN_SHOUT_POP'	=> true,
			'S_IN_PRIV'			=> false,
			'S_IN_SHOUT_TEMP'	=> true,
			'SHOUTBOX_VERSION'	=> sprintf($user->lang['SHOUTBOX_VERSION_ACP_COPY'], $config['shout_version']),
		));
	}
}
// Display the private shoutbox
elseif ($shout == 0 && $mode == '')
{
	if (!$auth->acl_get('u_shout_priv'))
	{
		redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
	}
	else
	{
		$template->assign_vars(array(
			'S_IN_PRIV'			=> true,
			'S_IN_SHOUT_POP'	=> false,
			'S_IN_SHOUT_TEMP'	=> true,
		));
		
		// Always post enter info 
		post_robot_shout($user->data['user_id'], $user->ip, true, false, false, false, false);
	}
}
// Display the popup config
elseif ($mode)
{
	switch ($mode)
	{
		case 'config':
			if (!function_exists('set_shout_value'))
			{
				set_config('shout_enable', 0, false);
				redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
			}
			if ($user->data['user_id'] == ANONYMOUS || !$auth->acl_get('u_shout_post'))
			{
				redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
			}
			$retour = (isset($_POST['retour'])) ? true : false;
			set_shout_value();
			$config_url = append_sid("{$phpbb_root_path}shout_popup.$phpEx", 'm=config');
			
			if ($submit)
			{
				$user_correct 		= request_var('user_correct', 0);
				$new_sound 			= utf8_normalize_nfc(request_var('new_sound', $config['shout_sound_new'], true));
				$error_sound 		= utf8_normalize_nfc(request_var('error_sound', $config['shout_sound_error'], true));
				$del_sound 			= utf8_normalize_nfc(request_var('del_sound', $config['shout_sound_del'], true));
				$out_index 			= request_var('pos_index', 		$config['shout_position_index']);
				$out_forum 			= request_var('pos_forum', 		$config['shout_position_forum']);
				$out_topic 			= request_var('pos_topic', 		$config['shout_position_topic']);
				$out_another 		= request_var('pos_another', 	$config['shout_position_another']);
				$out_portal 		= request_var('pos_portal', 	$config['shout_position_portal']);
				$shout_bar			= request_var('shout_bar', 		$config['shout_bar_option']);
				$shout_pagin		= request_var('shout_pagin', 	$config['shout_pagin_option']);
				$shout_bar_pop		= request_var('shout_bar_pop', 	$config['shout_bar_option_pop']);
				$shout_pagin_pop	= request_var('shout_pagin_pop',$config['shout_pagin_option_pop']);
				$shout_bar_priv		= request_var('shout_bar_priv', $config['shout_bar_option_priv']);
				$shout_pagin_priv	= request_var('shout_pagin_priv',$config['shout_pagin_option_priv']);
				
				$config_user_shout = implode(', ', array($user_correct, $new_sound, $error_sound, $del_sound, $out_index, $out_forum, $out_topic, $out_another, $out_portal));
				$config_user_shoutbox = implode(',', array($shout_bar, $shout_pagin, $shout_bar_pop, $shout_pagin_pop, $shout_bar_priv, $shout_pagin_priv));
				
				$sql = 'UPDATE ' . USERS_TABLE . " SET user_shout = '" .$db->sql_escape($config_user_shout). "' WHERE user_id = " .(int)$user->data['user_id'];
				$db->sql_query($sql);
				
				$sql = 'UPDATE ' . USERS_TABLE . " SET user_shoutbox = '" .$db->sql_escape($config_user_shoutbox). "' WHERE user_id = " .(int)$user->data['user_id'];
				$db->sql_query($sql);
				
				meta_refresh(1, $config_url);
				$message = $user->lang['SHOUT_PREF_UPDATED']. '<br /><br />' .sprintf($user->lang['RETURN_SHOUT_PREF'], '<a href="' . $config_url . '">', '</a>'). '<br /><br />' .$user->lang['SHOUT_NOTE'];
				trigger_error($message);
			}
			elseif ($retour)
			{
				$on_sound = (!$config['shout_sound_on']) ? 1 : 0;
				$active = (!$config['shout_sound_on']) ? 0 : '';
				
				$sql = 'UPDATE ' . USERS_TABLE . " SET user_shout = '$on_sound, $active, $active, $active, 3, 3, 3, 3, 3' WHERE user_id = " .(int)$user->data['user_id'];
				$db->sql_query($sql);
				
				$sql = 'UPDATE ' . USERS_TABLE . " SET user_shoutbox = 'N,N,N,N,N,N' WHERE user_id = " .(int)$user->data['user_id'];
				$db->sql_query($sql);
				
				meta_refresh(1, $config_url);
				$message = $user->lang['SHOUT_PREF_UPDATED']. '<br /><br />' .sprintf($user->lang['RETURN_SHOUT_PREF'], '<a href="' . $config_url . '">', '</a>'). '<br /><br />' .$user->lang['SHOUT_NOTE'];
				trigger_error($message);
			}
			
			list($correct, $sound_new, $sound_error, $sound_del, $_index, $_forum, $_topic, $_another, $_portal) = explode(', ', $user->data['user_shout']);
			list($shout_bar, $shout_pagin, $shout_bar_pop, $shout_pagin_pop, $shout_bar_priv, $shout_pagin_priv) = explode(',', $user->data['user_shoutbox']);
			
			$sound_new 	= ($sound_new == '') ? 	(($config['shout_sound_on']) ? (string)$config['shout_sound_new'] : '') : 	(string)$sound_new;
			$sound_error= ($sound_error == '') ? (($config['shout_sound_on']) ? (string)$config['shout_sound_error'] : '') : (string)$sound_error;
			$sound_del 	= ($sound_del == '') ? 	(($config['shout_sound_on']) ? (string)$config['shout_sound_del'] : '') : 	(string)$sound_del;
			$_index 	= ($_index == 3) ? 		(int)$config['shout_position_index'] : 	(int)$_index;
			$_forum 	= ($_forum == 3) ? 		(int)$config['shout_position_forum'] : 	(int)$_forum;
			$_topic 	= ($_topic == 3) ? 		(int)$config['shout_position_topic'] : 	(int)$_topic;
			$_another 	= ($_another == 3) ? 	(int)$config['shout_position_another'] : (int)$_another;
			$_portal 	= ($_portal == 3) ? 	(int)$config['shout_position_portal'] : (int)$_portal;
			$shout_bar			= ($shout_bar != 'N') ? $shout_bar : $config['shout_bar_option'];
			$shout_pagin		= ($shout_pagin != 'N') ? $shout_pagin : $config['shout_pagin_option'];
			$shout_bar_pop		= ($shout_bar_pop != 'N') ? $shout_bar_pop : $config['shout_bar_option_pop'];
			$shout_pagin_pop	= ($shout_pagin_pop != 'N') ? $shout_pagin_pop : $config['shout_pagin_option_pop'];
			$shout_bar_priv		= ($shout_bar_priv != 'N') ? $shout_bar_priv : $config['shout_bar_option_priv'];
			$shout_pagin_priv	= ($shout_pagin_priv != 'N') ? $shout_pagin_priv : $config['shout_pagin_option_priv'];
			
			
			$sort_date = array('|M Y|', '|F Y|', '|F Y|', '|d F Y|', '|d M Y|, H:i', '|F j, Y|, g:i a', '|D d M y|, H:i');
			if (in_array($user->data['user_dateformat'], $sort_date))
			{
				$enable_sound = sprintf($user->lang['MINUTES_CHOICE'], $user->format_date(time()));
				$is_minutes = true;
			}
			else
			{
				$enable_sound = $user->lang['DISPLAY_SOUND_CHOICE'];
				$is_minutes = false;
			}
			
			$new_sound 	= $error_sound = $del_sound = '';
			$shout_path = $phpbb_root_path. 'images/shoutbox/';
			$soundlist1 = @filelist($shout_path. 'new/', '', 'swf');
			$soundlist2 = @filelist($shout_path. 'error/', '', 'swf');
			$soundlist3 = @filelist($shout_path. 'del/', '', 'swf');
			
			// Sounds for new messages
			if (sizeof($soundlist1))
			{
				$new_sound = build_sound_select($soundlist1, $sound_new, 1, false);
			}
			
			// Sounds for error
			if (sizeof($soundlist2))
			{
				$error_sound = build_sound_select($soundlist2, $sound_error, 2, true);
			}
			
			// Sounds for delete
			if (sizeof($soundlist3))
			{
				$del_sound = build_sound_select($soundlist3, $sound_del, 3, true);
			}
			
			$s0 = $user->lang['SHOUT_POSITION_TOP'];
			$s1 = $user->lang['SHOUT_POSITION_AFTER'];
			$s2 = $user->lang['SHOUT_POSITION_END'];
			$is_index 	= ($config['shout_index']) ? true : false;
			$is_forum 	= ($config['shout_forum']) ? true : false;
			$is_topic 	= ($config['shout_topic']) ? true : false;
			$is_another	= ($config['shout_another']) ? true : false;
			$is_portal 	= ($config['shout_portal'] && file_exists($phpbb_root_path. 'portal.' .$phpEx)) ? true : false;
			$w_index 	= (!$_index) ? $s0 : (($_index == 1) ? $s1 : $s2);
			$w_forum 	= (!$_forum) ? $s0 : $s2;
			$w_topic 	= (!$_topic) ? $s0 : $s2;
			$w_another 	= (!$_another) ? $s0 : $s2;
			$w_portal 	= (!$_portal) ? $s0 : $s2;
			
			$template->assign_vars(array(
				'S_IN_CONFIG'			=> true,
				'S_IN_SHOUT_TEMP'		=> true,
				'EXPLAIN_DATE'			=> $enable_sound,
				'USER_CORRECT'			=> $correct,
				'USER_CORRECT_DISP'		=> ($correct) ? 'none' : 'block',
				'USER_CORRECT_DISP2'	=> ($correct) ? 'block' : 'none',
				'DATE_CORRECT'			=> $is_minutes,
				'LANG_CORRECT'			=> ($is_minutes) ? $user->lang['SHOUT_SOUND_MINUTES'] : $user->lang['SHOUT_SOUND_NO'],
				'NO_SOUND_DEL'			=> ($sound_del) ? false : true,
				'SOUND_DEL_DISP2'		=> ($sound_del) ? 'none' : 'block',
				'SOUND_DEL_DISP'		=> ($sound_del) ? 'block' : 'none',
				'NO_SOUND_ERROR'		=> ($sound_error) ? false : true,
				'SOUND_ERROR_DISP2'		=> ($sound_error) ? 'none' : 'block',
				'SOUND_ERROR_DISP'		=> ($sound_error) ? 'block' : 'none',
				'NEW_SOUND'				=> $new_sound,
				'ERROR_SOUND'			=> $error_sound,
				'DEL_SOUND'				=> $del_sound,
				'USER_SOUND'			=> $sound_new,
				'USER_SOUND_E'			=> $sound_error,
				'USER_SOUND_D'			=> $sound_del,
				'USER_SOUND_INFO'		=> str_replace('.swf', '', $sound_new),
				'USER_SOUND_INFO_E'		=> str_replace('.swf', '', $sound_error),
				'USER_SOUND_INFO_D'		=> str_replace('.swf', '', $sound_del),
				'SHOUT_BAR'				=> $shout_bar,
				'SHOUT_PAGIN'			=> $shout_pagin,
				'SHOUT_BAR_POP'			=> $shout_bar_pop,
				'SHOUT_PAGIN_POP'		=> $shout_pagin_pop,
				'SHOUT_BAR_PRIV'		=> $shout_bar_priv,
				'SHOUT_PAGIN_PRIV'		=> $shout_pagin_priv,
				'IS_INDEX'				=> $is_index,
				'IS_FORUM'				=> $is_forum,
				'IS_TOPIC'				=> $is_topic,
				'IS_ANOTHER'			=> $is_another,
				'IS_PORTAL'				=> $is_portal,
				'ON_INDEX'				=> $_index,
				'ON_FORUM'				=> $_forum,
				'ON_TOPIC'				=> $_topic,
				'ON_ANOTHER'			=> $_another,
				'ON_PORTAL'				=> $_portal,
				'W_INDEX'				=> $w_index,
				'W_FORUM'				=> $w_forum,
				'W_TOPIC'				=> $w_topic,
				'W_ANOTHER'				=> $w_another,
				'W_PORTAL'				=> $w_portal,
				'PERM_POP'				=> $auth->acl_get('u_shout_popup'),
				'PERM_PRIV'				=> $auth->acl_get('u_shout_priv'),
				'U_SHOUT_ACTION'		=> $config_url,
				'SHOUTBOX_VERSION'		=> sprintf($user->lang['SHOUTBOX_VERSION_ACP_COPY'], $config['shout_version']),
			));
		break;
	}
}
else
{
	exit;
}

// Output page
page_header($title);

$template->set_filenames(array(
	'body' => 'shout_template.html')
);

page_footer();

?>