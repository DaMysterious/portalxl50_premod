<?php
/*
*
* @name center_calendar_week.php
* @package phpBB3 Portal XL Premod
* @version $Id: center_calendar_week.php,v 1.2 2009/05/15 22:32:06 portalxl group Exp $
*
* @copyright (c) 2007, 2015 PortalXL Group
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/
$user->setup('calendar');

// Generate calendar week view or next events if required ...
// calendar_display_calendar_on_index();

// Assign specific vars
$template->assign_vars(array(
		'U_CALENDAR'	=> append_sid("{$phpbb_root_path}calendar.$phpEx"),
	));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/center_calendar_week.html',
	));


?>