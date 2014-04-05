<?php
/*
*
* @name acp_donate_mod_settings.php
* @package phpBB3 Portal XL 5.0
* @version $Id: acp_donate_mod_settings.php,v 1.1.0 2010/10/08 portalxl group Exp $
*
* @copyright (c) Zou Xiong - Enterprise admin@loewen.com.sg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

/**
* @package module_install
*/
class acp_donate_mod_settings_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_donate_mod_settings',
			'title'		=> 'ACP_DONATE_MOD_SETTINGS',
			'version'	=> '1.0.3',
			'modes'		=> array(
				'default'	=> array('title' => 'ACP_DONATE_MOD_SETTINGS', 'auth' => 'acl_a_', 'cat' => array('ACP_DOT_MODS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>