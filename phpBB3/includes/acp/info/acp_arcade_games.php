<?php
/**
*
* @package acp
* @version $Id: acp_arcade_games.php 1663 2011-09-22 12:09:30Z killbill $
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
class acp_arcade_games_info
{
	function module()
	{
		return array(
			'filename'		=> 'acp_arcade_games',
			'title'			=> 'ACP_ARCADE_GAMES',
			'version'		=> '2.0.RC1',
			'modes'			=> array(
			'add_games'		=> array('title' => 'ACP_ARCADE_ADD_GAMES',    'auth' => 'acl_a_arcade_game',   'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'edit_games'	=> array('title' => 'ACP_ARCADE_EDIT_GAMES',   'auth' => 'acl_a_arcade_game',   'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'unpack_games'	=> array('title' => 'ACP_ARCADE_UNPACK_GAMES', 'auth' => 'acl_a_arcade_game',   'cat' => array('ACP_CAT_ARCADE_GAMES')),
			'edit_scores'	=> array('title' => 'ACP_ARCADE_EDIT_SCORES',  'auth' => 'acl_a_arcade_scores', 'cat' => array('ACP_CAT_ARCADE_GAMES')),
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