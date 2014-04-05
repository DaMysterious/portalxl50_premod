<?php

/**
*
* @package post_links
* @version $Id: acp_post_links.php 2010-03-19 22:46:00Z Senky $
* @copyright (c) 2010 Senky
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

class acp_post_links_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_post_links',
			'title'		=> 'ACP_POST_LINKS_TITLE',
			'version'	=> '1.0.1',
			'modes'		=> array(
				'default'	=> array(
				'title'			=> 'ACP_POST_LINKS_TITLE',
				'auth'			=> 'acl_a_board',
				'cat'			=> array('ACP_MESSAGES'),
				),
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