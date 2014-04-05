<?php
/*
*
* @name gallery_single_image.php
* @package phpBB3 Portal XL Premod
* @version $Id: gallery_single_image.php,v 2.0 2011/08/18 portalxl group Exp $
*
* @copyright (c) 2007, 2011  Portal XL Group
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/
if (class_exists('phpbb_gallery_integration'))
{
	phpbb_gallery::init(array('mods/gallery'));
}

/**
* Random scroll limited to 1 pic random
*/
$sql_order = 'RAND()';
$sql_limit = 1;

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

$picrow = $db->sql_fetchrow($result); 

$template->assign_vars(array(
	'S_THUMBNAIL_SIZE'	=> phpbb_gallery_config::get('thumbnail_height') + 20 + ((phpbb_gallery_config::get('thumbnail_infoline')) ? phpbb_gallery_constants::THUMBNAIL_INFO_HEIGHT : 0),
	'S_COLS'			=> phpbb_gallery_config::get('album_columns'),
	'S_COL_WIDTH'		=> (100 / phpbb_gallery_config::get('album_columns')) . '%',

	'PIC_IMAGE' 		=> phpbb_gallery_url::append_sid('image', "image_id=" . $picrow['image_id']), 
	'PIC_THUMBNAIL'		=> phpbb_gallery_image::generate_link('thumbnail', 'plugin', $picrow['image_id'], $picrow['image_name'], $picrow['image_album_id']),
	'U_PIC_LINK' 		=> phpbb_gallery_url::append_sid('image_page', "album_id=" . $picrow['image_id']), 
	'IMAGE_NAME'		=> $picrow['image_name'],
	'DESC'		    	=> generate_text_for_display($picrow['image_desc'], $picrow['image_desc_uid'], $picrow['image_desc_bitfield'], 7),
							 
	'L_NEWEST_PICS' 			=> $user->lang['NEWEST_PIC'],
	'L_PIC_RECEIVED' 			=> $user->lang['PIC_RECEIVED'],
	'L_PIC_POSTER' 				=> $user->lang['PIC_POSTER'],
	'L_LOGIN_LOGOUT_GALLERY' 	=> $user->lang['LOGIN_LOGOUT_GALLERY'],
	'L_PIC_TITLE' 				=> $user->lang['IMAGE_TITLE'],
	'PIC_TITLE' 				=> $picrow['image_name'], 
	'PIC_POSTER' 				=> $picrow['username'], 
	'PIC_TIME' 					=> $user->format_date($picrow['image_time']), 
	'PIC_DESCR' 				=> $picrow['image_desc'],
	
	'L_GALLERY'					=> $user->lang['GALLERY'],
	'U_GALLERY_MOD'				=> phpbb_gallery_url::append_sid('index'),
	));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/gallery_single_image.html',
	));

?>