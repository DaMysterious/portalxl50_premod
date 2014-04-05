<?php
/**
*
* @package acp
* @version $Id: acp_arcade_permissions.php 1663 2011-09-22 12:09:30Z killbill $
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
class acp_arcade_permissions_info
{
	function module()
	{
		return array(
			'filename'				 	=> 'acp_arcade_permissions',
			'title'						=> 'ACP_ARCADE_PERMISSIONS',
			'version'				 	=> '2.0.RC1',
			'modes'					 	=> array(
			'trace'					 	=> array('title' => 'ACP_ARCADE_PERMISSION_TRACE'			, 'auth' => 'acl_a_viewauth', 'display' => false					, 'cat' => array('ACP_CAT_ARCADE_PERMISSION_MASKS')),

			'setting_user_global'		=> array('title' => 'ACP_ARCADE_USERS_PERMISSIONS'			, 'auth' => 'acl_a_authusers && acl_a_cauth'						, 'cat' => array('ACP_CAT_ARCADE_GLOBAL_PERMISSIONS')),
			'setting_group_global'		=> array('title' => 'ACP_ARCADE_GROUPS_PERMISSIONS'			, 'auth' => 'acl_a_authgroups && acl_a_cauth'						, 'cat' => array('ACP_CAT_ARCADE_GLOBAL_PERMISSIONS')),

			'setting_category_local' 	=> array('title' => 'ACP_ARCADE_CATEGORY_PERMISSIONS'		, 'auth' => 'acl_a_cauth && (acl_a_authusers || acl_a_authgroups)'	, 'cat' => array('ACP_CAT_ARCADE_PERMISSIONS')),
			'setting_category_copy'  	=> array('title' => 'ACP_ARCADE_CATEGORY_PERMISSIONS_COPY'	, 'auth' => 'acl_a_cauth && acl_a_authusers && acl_a_authgroups'	, 'cat' => array('ACP_CAT_ARCADE_PERMISSIONS')),
			'setting_user_local'		=> array('title' => 'ACP_ARCADE_USERS_CATEGORY_PERMISSIONS'	, 'auth' => 'acl_a_authusers && acl_a_cauth'						, 'cat' => array('ACP_CAT_ARCADE_PERMISSIONS')),
			'setting_group_local'	 	=> array('title' => 'ACP_ARCADE_GROUPS_CATEGORY_PERMISSIONS', 'auth' => 'acl_a_authgroups && acl_a_cauth'						, 'cat' => array('ACP_CAT_ARCADE_PERMISSIONS')),

			'view_user_global'			=> array('title' => 'ACP_VIEW_ARCADE_USERS_PERMISSIONS'		, 'auth' => 'acl_a_viewauth'										, 'cat' => array('ACP_CAT_ARCADE_PERMISSION_MASKS')),
			'view_category_local'	 	=> array('title' => 'ACP_VIEW_ARCADE_CATEGORY_PERMISSIONS'	, 'auth' => 'acl_a_viewauth'										, 'cat' => array('ACP_CAT_ARCADE_PERMISSION_MASKS')),
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