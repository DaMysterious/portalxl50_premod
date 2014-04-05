<?php
/**
*
* @name ajax_userinfo.php
* @package phpBB3 Portal XL 5.0
* @version $Id: ajax_userinfo.php,v 0.1.1 2010/12/30 14:14:05
*
* @copyright (c) 2007, 2013 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

// AJAX Userinfo by Tobi (http://www.seo-phpbb.org)


// Check that the $_GET['mode'] variable has been set and is a number
$ajax_userid = $_GET['userid'];

if (!is_numeric($ajax_userid))
{
	die('');
}

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$user->setup();

// Select some Userdata from DB
$sql = 'SELECT *
	FROM ' . USERS_TABLE . ' 
	WHERE user_id = '. (int) $ajax_userid;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

// Get user from
if ( $row['user_from'] != NULL ) { 
	$user_from = $row['user_from'];
} else {
	$user_from = $user->lang['GENDER_X'] . '!';
}
// Get user from

// Get user website
if ( $row['user_website'] != NULL ) { 
	$user_website = $row['user_website'];
} else {
	$user_website = $user->lang['GENDER_X'] . '!';
}
// Get user website

// Get user posts
if ( $row['user_posts'] != '0' ) {
	$user_posts = $row['user_posts'];
} else {
	$user_posts = $user->lang['GENDER_X'] . '!';
}
// Get user website

// Get the Avatar for display
$avatar = '';
$avatar_img = '';
switch ($row['user_avatar_type'])
{
	case AVATAR_UPLOAD:
		$avatar = "download/file.$phpEx?avatar=";
    	$avatar .= $row['user_avatar'];
    	$avatar_img = $avatar;
    break;

    case AVATAR_GALLERY:
		$avatar = $config['avatar_gallery_path'] . '/';
        $avatar .= $row['user_avatar'];
        $avatar_img = $avatar;
    break;
}

$avatar = str_replace('<img', '', $avatar_img);
$avatar = str_replace(' width="80" height="80" alt="" />', '', $avatar_img);

if ( !$avatar )
{
	$avatar = 'styles/' . $user->theme['theme_path'] . '/theme/images/no_avatar.gif';
}
// Get the Avatar for display

// Get rank for display
if ( $row['user_rank'] != '0' ) { 
	$rank_title = $rank_img = $rank_img_src = '';
	get_user_rank($row['user_rank'], (($row['user_id'] == ANONYMOUS) ? false : $row['user_posts']), $rank_title, $rank_img, $rank_img_src);
} else {
	$rank_title = $user->lang['GENDER_X'] . '!';
}
// Get rank for display

// Get country flag for display
if ( $row['user_country_flag'] != '0' ) { 
	$flag_title = $row['user_country_flag'];
} else {
	$flag_title = $user->lang['GENDER_X'] . '!';
}
// Get country flag for display

// Get gender for display
if ( $row['user_gender'] != '0' ) {
	
  if ( $row['user_gender'] != '2' ) { 
	  $gender_title = sprintf($user->lang['GENDER_M']);
  } else {
	  $gender_title = sprintf($user->lang['GENDER_F']);
  }
  
} else {
	$gender_title = $user->lang['GENDER_X'] . '!'; 
}
// Get gender for display

// Get IP for display
if ( $row['user_ip'] != NULL ) { 
	$user_ip = $row['user_ip'];
} else {
	$user_ip = $user->lang['GENDER_X'] . '!'; 
}
// Get IP for display

// Get user regdate
if ( $row['user_regdate'] != NULL ) { 
	$user_regdate = $user->format_date($row['user_regdate']);
} else {
	$user_regdate = $user->lang['GENDER_X'] . '!'; 
}
// Get IP for display

// Send to XML File
echo header('Content-Type: text/xml; charset=utf-8');
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
echo '<userdata>';
echo '<username>' . get_username_string('username', $row['user_id'], $row['username'], $row['user_colour']) . '</username>';
echo '<colour>' . trim((empty($row['user_colour'])) ? '000000' : $row['user_colour']  ) . '</colour>';
echo '<regdate>' . $user_regdate . '</regdate>';
echo '<posts>' . $user_posts . '</posts>';
echo '<from>' . $user_from . '</from>';
echo '<lastvisit>' . $user->format_date($row['user_lastvisit']) . '</lastvisit>';
echo '<website>' . $user_website . '</website>';
// Get the Avatar for display
echo '<avatar>' . $avatar . '</avatar>';
// Get the Avatar for display
// Get rank for display
echo '<rank>' . $rank_title . '</rank>';
// Get rank for display
// Get country flag for display
echo '<flag>' . $flag_title . '</flag>';
// Get country flag for display
// Get gender for display
echo '<gender>' . $gender_title . '</gender>';
// Get gender for display
// Get IP for display
echo '<userip>' . $user_ip . '</userip>';
// Get IP for display
echo'</userdata>';

?>