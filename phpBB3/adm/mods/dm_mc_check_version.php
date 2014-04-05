<?php
/**
*
* @package version check
* @version $Id: dm_mc_check_version.php 118 2011-01-10 02:30:28Z femu $
* @copyright (c) 2010 femu - http://die-muellers.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package mod_version_check
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class dm_mc_check_version
{
	function version()
	{
		global $config;

		return array(
			'author'	=> 'femu',
			'title'		=> 'DM Music Charts',
			'tag'		=> 'dm_mc',
			'version'	=> '1.0.2',
			'file'		=> array('die-muellers.org', 'updatecheck', 'dm_music_charts.xml'),
		);
	}
}

?>