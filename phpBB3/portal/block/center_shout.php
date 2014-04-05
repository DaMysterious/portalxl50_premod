<?php
/**
*
* @name center_shout.php
* @package phpBB3 Portal XL 5.0
* @version $Id: center_shout.php,v 1.3 2011/07/18 portalxl group Exp $
*
* @copyright (c) 2007, 2013 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Start Breizh Shoutbox
*/
if (isset($config['shout_version']))
{
	if ($config['shout_enable'] && $config['shout_index'])
	{
		shout_display();
	}
}

/*
* Start session management
*/
$user->setup('mods/shout');

/*
* Begin block script here
*/
$bd_shout = array();

$bd_shout[] = array(
	'userid'	=> $row['user_id'],
	'name'		=> $row['username'],
	'colour'	=> $row['user_colour'],
	'age'		=> ($now['year'] - $age),
	'flag'		=> (isset($row['user_country_flag'])) ? $row['user_country_flag'] : false,
);

if (isset($config['shout_version']))
{
	if (sizeof($bd_shout) && date('H') == $config['shout_birthday_hour'] && $config['shout_enable'])
	{
		foreach ($bd_shout as $pos => $shout)
		{
			birthday_robot_shout($shout['userid'], $shout['name'], $shout['colour'], $shout['age'], $shout['flag']);
		}
	}
}

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/center_shout.html',
	));

?>