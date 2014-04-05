<?php
/**
*
* @package acp
* @version $Id: acp_arcade_utilities.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_arcade_utilities_info
{
	function module()
	{
		return array(
			'filename'			=> 'acp_arcade_utilities',
			'title'				=> 'ACP_ARCADE_UTILITIES',
			'version'			=> '2.0.RC1',
			'modes'				=> array(
			'backup'			=> array('title' => 'ACP_ARCADE_UTILITIES_BACKUP'			, 'auth' => 'acl_a_arcade_backup'		, 'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'create_install'	=> array('title' => 'ACP_ARCADE_UTILITIES_CREATE_INSTALL'	, 'auth' => 'acl_a_arcade_utilities'	, 'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'downloads'			=> array('title' => 'ACP_ARCADE_UTILITIES_DOWNLOADS'		, 'auth' => 'acl_a_arcade_utilities'	, 'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'download_stats'	=> array('title' => 'ACP_ARCADE_UTILITIES_DOWNLOAD_STATS'	, 'auth' => 'acl_a_arcade_utilities'	, 'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'reports'			=> array('title' => 'ACP_ARCADE_UTILITIES_REPORTS'			, 'auth' => 'acl_a_arcade_utilities'	, 'cat' => array('ACP_CAT_ARCADE_UTILITIES')),
			'errors'			=> array('title' => 'ACP_ARCADE_UTILITIES_ERRORS'			, 'auth' => 'acl_a_arcade_utilities'	, 'cat' => array('ACP_CAT_ARCADE_UTILITIES')),
			'user_guide'		=> array('title' => 'ACP_ARCADE_UTILITIES_USER_GUIDE'		, 'auth' => ''							, 'cat' => array('ACP_CAT_ARCADE_UTILITIES')),
			));
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>