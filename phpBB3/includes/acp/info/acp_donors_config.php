<?php
/*
*
* @name acp_donors_config.php
* @package phpBB3 Portal XL 5.0
* @version $Id: acp_donors_config.php,v 1.1.0 2010/10/08 portalxl group Exp $
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
class acp_donors_config_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_donors_config',
			'title'		=> 'ACP_DONORS_CONFIG',
			'version'	=> '1.0.3',
			'modes'		=> array(
				'default'	=> array('title' => 'ACP_DONORS_CONFIG', 'auth' => 'acl_a_', 'cat' => array('ACP_DOT_MODS')),
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