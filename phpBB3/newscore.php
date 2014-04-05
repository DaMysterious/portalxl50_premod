<?php
/**
*
* @package arcade
* @version $Id: newscore.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
$scoretype = AMOD_GAME;
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'arcade/includes/scoretype.' . $phpEx);

?>