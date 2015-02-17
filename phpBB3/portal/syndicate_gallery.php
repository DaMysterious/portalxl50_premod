<?php 
/** 
*
* @name syndicate_gallery.php
* @package phpBB3 Portal XL
* @version $Id: syndicate_gallery.php,v 2.0 2011/08/18 portalxl group Exp $
*
* @copyright (c) 2007, 2015 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

// XML and nocaching headers
header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
header ('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header ('Content-Type: text/xml');

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

global $auth, $cache, $config, $db, $gallery_config, $portal_config, $template, $user;
global $location, $location_url, $album_data, $gallery_root_path, $phpbb_root_path, $phpEx, $album_access_array;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/portal_xl');

// Create main board url
$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($config['script_path']));
$picture_url = ($script_name != '') ? $script_name . '/gallery/image_page.' . $phpEx : '/gallery/image_page.'. $phpEx;
$thumb_url = ($script_name != '') ? $script_name . '/gallery/image.' . $phpEx : '/gallery/image.'. $phpEx;
$index = ($script_name != '') ? $script_name . '/portal.' . $phpEx : 'portal.'. $phpEx;
$server_name = trim($config['server_name']);
$server_protocol = ($config['cookie_secure']) ? 'https://' : 'http://';
$server_port = ($config['server_port'] <> 80) ? ':' . trim($config['server_port']) . '/' : '/';

$time = time();
$pre_timezone = date('O', $time);
$time_zone = substr($pre_timezone, 0, 3).":".substr($pre_timezone, 3, 2);
$topics_per_page = $config['posts_per_page'];

$index_url = $server_protocol . $server_name . $server_port . $index;
$picture_url = $server_protocol . $server_name . $server_port . $picture_url;
$thumb_url = $server_protocol . $server_name . $server_port . $thumb_url;
$number_of_attachments = $portal_config['portal_attachments_number'];

$rdf  = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
$rdf .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";
$rdf .= '<channel>' . "\n";
$rdf .= '<title>' . strip_tags($config['sitename']) . ' Gallery Pictures</title>' . "\n";
$rdf .= '<link>' . $index_url . '</link>' . "\n";
$rdf .= '<description>' . strip_tags($config['site_desc']) . '</description>' . "\n";
$rdf .= '<pubDate>' . date("D, d M Y H:G:s O") . '</pubDate>' . "\n";
$rdf .= '<lastBuildDate>' . date("D, d M Y H:G:s O") . '</lastBuildDate>' . "\n";
$rdf .= '<copyright>Copyright 2009, ' . $config['sitename'] . '</copyright>' . "\n";
$rdf .= '<webMaster>' . $config['board_email'] . '</webMaster>' . "\n";
$rdf .= '<managingEditor>' . $config['board_email'] . '</managingEditor>' . "\n";

/**
* Fetch pictures
*
*/
if (class_exists('phpbb_gallery_integration'))
{
	phpbb_gallery::init(array('mods/gallery'));
}

$sql_order = 'i.image_time DESC';
$sql_limit = 15;

$sql_array = array(
	'SELECT'		=> 'i.*, u.user_id, u.username, u.user_colour',

	'FROM'		=> array(
		GALLERY_IMAGES_TABLE	=> 'i',
	),

	'LEFT_JOIN'	=> array(
		array(
			'FROM'	=>	array(USERS_TABLE	=> 'u'),
			'ON'	=> 'i.image_user_id = u.user_id'
	),
		array(
			'FROM'	=>	array(GALLERY_ALBUMS_TABLE	=> 'ct'),
			'ON'	=> 'i.image_album_id = ct.album_id'
		),
	),

	'WHERE'			=> '(' . $db->sql_in_set('i.image_album_id', phpbb_gallery::$auth->acl_album_ids('i_view'), false, true) . ' 
		AND i.image_status <> ' . phpbb_gallery_image::STATUS_UNAPPROVED . ')',
	'ORDER_BY'		=> $sql_order,
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query_limit($sql, $sql_limit);

while ($picrow = $db->sql_fetchrow($result))
{
  $rdf .= "<item>";
  $rdf .= "<title>" . $picrow['image_name'] . "</title>";
  $rdf .= "<link>" . $picture_url . "?album_id=" . $picrow['image_album_id'] . "&amp;image_id=" . $picrow['image_id'] . "</link>";
  $rdf .= "<pubDate>" . date("D, d M Y H:G:s O", $picrow['image_time']) . "</pubDate>";
//$rdf .= "<description>" . htmlspecialchars($picrow['image_desc'], ENT_QUOTES, 'utf-8') . "</description>";
  $rdf .= "<description><![CDATA[<table><tr><td><img src=" . $thumb_url . "?mode=thumbnail&amp;album_id=" . $picrow['image_album_id'] . "&amp;image_id=" . $picrow['image_id'] . " /></td></tr><tr><td>" . generate_text_for_display($picrow['image_desc'], ENT_QUOTES, 'utf-8', '') . "</td></tr></table>]]></description>";
  $rdf .= "</item>";
}
	
$db->sql_freeresult($result);
unset($picrow);

// Create RDF footer
$rdf .= "</channel></rss>";

// Output the RDF
echo $rdf;
?>