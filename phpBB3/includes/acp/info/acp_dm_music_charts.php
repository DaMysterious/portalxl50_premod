<?php
/**
*
* @package DM Music Charts
* @version $Id: acp_dm_music_charts.php 3 2010-07-20 11:03:50Z femu $
* @copyright (c) 2010 femu - http://die-muellers.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @Create the modules in ACP
*/
class acp_dm_music_charts_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_dm_music_charts',
			'title'		=> 'DM_MC_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'config'		=> array('title' => 'DM_MC_CONFIG', 'auth' => 'acl_a_dm_mc_manage', 'cat' => array('ACP_DM_MC')),
				'manage_charts'	=> array('title' => 'DM_MC_MANAGE', 'auth' => 'acl_a_dm_mc_manage', 'cat' => array('ACP_DM_MC')),
				'close_week'		=> array('title' => 'DM_MC_CLOSE', 'auth' => 'acl_a_dm_mc_manage', 'cat' => array('ACP_DM_MC')),
			),
		);
	}
}

?>