<?php
/**
*
* @package acp Breizh Shoutbox
* @version $Id: acp_shoutbox.php 140 20:10 31/12/2010 Sylver35 Exp $ 
* @copyright (c) 2010, 2011 Breizh Portal  http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_shoutbox_info
{
	function module()
	{
		
		return array(
			'filename'	=> 'acp_shoutbox',
			'title'		=> 'ACP_SHOUTBOX',
			'version'	=> '1.4.0',
			'modes'		=> array(
				// General settings
				'version'		=> array('title' => 'ACP_SHOUT_VERSION', 	'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_GENERAL_CAT')),
				'configs'		=> array('title' => 'ACP_SHOUT_CONFIGS', 	'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_GENERAL_CAT')),
				'rules'			=> array('title' => 'ACP_SHOUT_RULES', 		'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_GENERAL_CAT')),
				// Main shoutbox
				'overview'		=> array('title' => 'ACP_SHOUT_OVERVIEW', 	'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_PRINCIPAL_CAT')),
				'config_gen'	=> array('title' => 'ACP_SHOUT_CONFIG_GEN', 'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_PRINCIPAL_CAT')),
				// Private shoutbox
				'private'		=> array('title' => 'ACP_SHOUT_PRIVATE', 	'auth' 	=> 'acl_a_shout_priv', 		'cat' => array('ACP_SHOUT_PRIVATE_CAT')),
				'config_priv'	=> array('title' => 'ACP_SHOUT_CONFIG_PRIV','auth' 	=> 'acl_a_shout_priv', 		'cat' => array('ACP_SHOUT_PRIVATE_CAT')),
				// Popup shoutbox
				'popup'			=> array('title' => 'ACP_SHOUT_POPUP', 		'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_POPUP_CAT')),
				// Smilies
				'smilies'		=> array('title' => 'ACP_SHOUT_SMILIES', 	'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_SMILIES_CAT')),
				// Robot
				'robot'			=> array('title' => 'ACP_SHOUT_ROBOT', 		'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_ROBOT_CAT')),
				'robot_mod'		=> array('title' => 'ACP_SHOUT_ROBOT_MOD', 	'auth' 	=> 'acl_a_shout_manage', 	'cat' => array('ACP_SHOUT_ROBOT_CAT')),
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