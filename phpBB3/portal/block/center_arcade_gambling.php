<?php
/*
*
* @name center_arcade_gambling.php
* @package phpBB3 Portal XL 5.0
* @version $Id: center_arcade_gambling.php,v 1.1 2012/11/13 portalxl group Exp $
*
* @copyright (c) 2007, 2012 Portal XL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

/*
* Begin block script here
*/

/**
* Only include once
*/

/*
* Start session management
*/
if (file_exists($phpbb_root_path . 'arcade/includes/common.' . $phpEx))
{
	include($phpbb_root_path . 'arcade/includes/common.' . $phpEx);		
	// Initialize arcade auth
	$auth_arcade->acl($user->data);
	// Initialize arcade class
	$arcade = new arcade(false);

	display_arcade_online();
}

$template->assign_vars(array(
    'U_ARCADE' 		=> append_sid("{$phpbb_root_path}arcade.$phpEx"),
	'S_IN_ARCADE'	=> false
	));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/center_arcade_gambling',
	));

?>