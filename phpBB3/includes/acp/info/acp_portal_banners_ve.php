<?php
/** 
*
* @name acp_portal_banners_ve.php
* @package phpBB3 Portal XL 5.0
* @version $Id: acp_portal_banners_ve.php,v 1.2 2009/05/15 22:32:06 portalxl group Exp $
*
* @copyright (c) 2007, 2015 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/

class acp_portal_banners_ve_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_portal_banners_ve',
			'title'		=> 'ACP_PORTAL_CAT_BANNERS_VE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'banners_ve'		=> array('title' => 'ACP_MANAGE_BANNERS_VE', 'auth' => 'acl_a_portal', 'cat' => array('ACP_PORTAL')),
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
