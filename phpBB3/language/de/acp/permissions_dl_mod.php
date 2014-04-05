<?php

/**
*
* @mod package		Download Mod 6
* @file				permissions_dl_mod.php 2 2011/06/17 OXPUS
* @copyright		(c) 2005 oxpus (Karsten Ude) <webmaster@oxpus.de> http://www.oxpus.de
* @copyright mod	(c) hotschi / demolition fabi / oxpus
* @license			http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* Language pack for MOD permissions [German]
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Adding new category
$lang['permission_cat']['downloads'] = 'Download Bereich';

// Download MOD Permissions
$lang = array_merge($lang, array(
	'acl_a_dl_overview'		=> array('lang' => 'Kann die Startseite ansehen', 'cat' => 'downloads'),
	'acl_a_dl_config'		=> array('lang' => 'Kann die allgemeinen Einstellungen verwalten', 'cat' => 'downloads'),
	'acl_a_dl_traffic'		=> array('lang' => 'Kann den Traffic verwalten', 'cat' => 'downloads'),
	'acl_a_dl_categories'	=> array('lang' => 'Kann die Kategorien verwalten', 'cat' => 'downloads'),
	'acl_a_dl_files'		=> array('lang' => 'Kann die Downloads verwalten', 'cat' => 'downloads'),
	'acl_a_dl_permissions'	=> array('lang' => 'Kann die Berechtigungen verwalten', 'cat' => 'downloads'),
	'acl_a_dl_stats'		=> array('lang' => 'Kann die Statistiken einsehen und verwalten', 'cat' => 'downloads'),
	'acl_a_dl_banlist'		=> array('lang' => 'Kann die Bannliste verwalten', 'cat' => 'downloads'),
	'acl_a_dl_blacklist'	=> array('lang' => 'Kann die Blackliste der Dateiendungen verwalten', 'cat' => 'downloads'),
	'acl_a_dl_toolbox'		=> array('lang' => 'Kann die Toolbox verwenden', 'cat' => 'downloads'),
	'acl_a_dl_fields'		=> array('lang' => 'Kann benutzerdefinierte Felder verwalten', 'cat' => 'downloads'),
	'acl_a_dl_browser'		=> array('lang' => 'Kann User Agents verwalten', 'cat' => 'downloads'),
));

?>