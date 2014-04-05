<?php
/**
*
* @package acp
* @version $Id: acp_arcade_main.php 1663 2011-09-22 12:09:30Z killbill $
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
class acp_arcade_main_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_arcade_main',
			'title'		=> 'ACP_ARCADE_MAIN',
			'version'	=> '2.0.RC1',
			'modes'		=> array(
			'main'		=> array('title'=> 'ACP_ARCADE_MAIN_INDEX', 'auth' => '', 'cat' => array('ACP_CAT_ARCADE'))));
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>