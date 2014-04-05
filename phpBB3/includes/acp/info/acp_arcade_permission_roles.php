<?php
/**
*
* @package acp
* @version $Id: acp_arcade_permission_roles.php 1663 2011-09-22 12:09:30Z killbill $
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
class acp_arcade_permission_roles_info
{
	function module()
	{
		return array(
			'filename'		=> 'acp_arcade_permission_roles',
			'title'			=> 'ACP_ARCADE_PERMISSION_ROLES',
			'version'		=> '2.0.RC1',
			'modes'			=> array(
			'cat_roles'		=> array('title' => 'ACP_ARCADE_CAT_ROLES'	, 'auth' => 'acl_a_roles && acl_a_cauth', 'cat' => array('ACP_CAT_ARCADE_PERMISSION_ROLES')),
			'user_roles'	=> array('title' => 'ACP_ARCADE_USER_ROLES'	, 'auth' => 'acl_a_roles && acl_a_cauth', 'cat' => array('ACP_CAT_ARCADE_PERMISSION_ROLES')),
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