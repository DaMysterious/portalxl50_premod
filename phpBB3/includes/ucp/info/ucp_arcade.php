<?php
/**
*
* @package ucp
* @version $Id: ucp_arcade.php 1663 2011-09-22 12:09:30Z killbill $
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
class ucp_arcade_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_arcade',
			'title'		=> 'UCP_ARCADE',
			'version'	=> '2.0.RC1',
			'modes'		=> array(
			'settings'	=> array('title' => 'UCP_ARCADE_SETTINGS',  'auth' => '', 'cat' => array('UCP_CAT_ARCADE')),
			'favorites'	=> array('title' => 'UCP_ARCADE_FAVORITES', 'auth' => '', 'cat' => array('UCP_CAT_ARCADE')),
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