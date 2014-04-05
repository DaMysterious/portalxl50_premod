<?php
/**
*
* @package arcade
* @version $Id: common.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

include($phpbb_root_path . 'arcade/includes/cache.' . $phpEx);
include($phpbb_root_path . 'arcade/includes/session.' . $phpEx);

$arcade_cache  = new arcade_cache();
$arcade_config = $arcade_cache->obtain_arcade_config();

if (!defined('ARCADE_DIMINISHED_DATA'))
{
	$user->add_lang('mods/arcade');

	include($phpbb_root_path . 'arcade/includes/functions.' . $phpEx);
	include($phpbb_root_path . 'arcade/includes/functions_files.' . $phpEx);
	// Define file functions class
	$file_functions = new file_functions();

	include($phpbb_root_path . 'arcade/includes/arcade.' . $phpEx);

	if (!defined('IN_ADMIN'))
	{
		set_error_handler('arcade_msg_handler');
	}

	if (!class_exists('auth_arcade'))
	{
		include($phpbb_root_path . 'arcade/includes/auth.' . $phpEx);
	}

	include($phpbb_root_path . 'arcade/includes/class.' . $phpEx);

	if (defined('IN_ADMIN') || defined('USE_ARCADE_ADMIN'))
	{
		include($phpbb_root_path . 'arcade/includes/auth_admin.' . $phpEx);
		include($phpbb_root_path . 'arcade/includes/class_admin.' . $phpEx);
	}

	// Arcade auth
	$auth_arcade = new auth_arcade();

	if (!function_exists('get_user_avatar') || !function_exists('get_user_rank'))
	{
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
	}

	// Add arcade user data to $user
	set_arcade_userdata($user);
}

?>