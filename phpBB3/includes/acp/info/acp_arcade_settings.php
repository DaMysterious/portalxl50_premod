<?php
/**
*
* @package acp
* @version $Id: acp_arcade_settings.php 1663 2011-09-22 12:09:30Z killbill $
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
class acp_arcade_settings_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_arcade_settings',
			'title'		=> 'ACP_ARCADE_SETTINGS',
			'version'	=> '2.0.RC1',
			'modes'		=> array(
			'settings'	=> array('title' => 'ACP_ARCADE_SETTINGS_GENERAL', 'auth' => 'acl_a_arcade_settings', 'cat' => array('ACP_CAT_ARCADE_SETTINGS')),
			'game'		=> array('title' => 'ACP_ARCADE_SETTINGS_GAME',    'auth' => 'acl_a_arcade_settings', 'cat' => array('ACP_CAT_ARCADE_SETTINGS')),
			'feature'	=> array('title' => 'ACP_ARCADE_SETTINGS_FEATURE', 'auth' => 'acl_a_arcade_settings', 'cat' => array('ACP_CAT_ARCADE_SETTINGS')),
			'page'		=> array('title' => 'ACP_ARCADE_SETTINGS_PAGE',    'auth' => 'acl_a_arcade_settings', 'cat' => array('ACP_CAT_ARCADE_SETTINGS')),
			'path'		=> array('title' => 'ACP_ARCADE_SETTINGS_PATH',    'auth' => 'acl_a_arcade_settings', 'cat' => array('ACP_CAT_ARCADE_SETTINGS')),
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