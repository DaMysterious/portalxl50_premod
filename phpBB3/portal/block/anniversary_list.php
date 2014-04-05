<?php
/*
*
* @name anniversary_list.php
* @package phpBB3 Portal XL 5.0
* @version $Id: anniversary_list.php,v 1.0 2010/10/22 portalxl group Exp $
*
* @copyright (c) 2007, 2010  Portal XL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
* Original author EXreaction, http://www.lithiumstudios.org/ 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

/*
* Start session management
*/
global $auth, $cache, $config, $db, $portal_config, $template, $user;

/*
* Begin block script here
*/
$anniversary_list = $cache->get('anniversary_list');

if ($anniversary_list === false)
{
	$anniversary_list = '';
	$current_date = date('m-d');
	$current_year = date('Y');
	$leap_year = date('L');
	
	$sql = 'SELECT user_id, username, user_colour, user_regdate, user_country_flag
		FROM ' . USERS_TABLE . "
		WHERE user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		// Country Flags Version 3.0.6
		if ($user->data['user_id'] != ANONYMOUS)
		{
			$flag_title = $flag_img = $flag_img_src = '';
			get_user_flag($row['user_country_flag'], $row['user_country'], $flag_title, $flag_img, $flag_img_src);
		}
		// Country Flags Version 3.0.6

		// We are compensating for leap year here.  If the year is not a leap year, the current date is Feb 28, and they joined Feb 29 we will list their names.
		if (date('m-d', $row['user_regdate']) == $current_date || (!$leap_year && $current_date == '02-28' && date('m-d', $row['user_regdate']) == '02-29'))
		{
			if (($current_year - date('Y', $row['user_regdate'])) > 0)
			{
				$anniversary_list .= (($anniversary_list != '') ? ', ' : '') . get_username_string('full', $row['user_id'], ($row['user_id'] <> ANONYMOUS) ? $row['username'] : $user->lang['GUEST'], $row['user_colour']) . ' ' . $flag_img;
				$anniversary_list .= ' (' . ($current_year - date('Y', $row['user_regdate'])) . ')';
			}
		}
	}
	$db->sql_freeresult($result);

	//Figure out what tomorrow's beginning time is based on the board timezone settings and have the cache expire then.
	$till_tomorrow = gmmktime(0, 0, 0) + 86400 - ($config['board_timezone'] * 3600) - ($config['board_dst'] * 3600) - time();
	$cache->put('anniversary_list', $anniversary_list, $till_tomorrow);
}

// Assign specific vars
$template->assign_vars(array(
	'ANNIVERSARY_LIST'				=> $anniversary_list,
	'S_DISPLAY_ANNIVERSARY_LIST'	=> ($config['load_birthdays']) ? true : false,
	));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/anniversary_list.html',
	));

?>