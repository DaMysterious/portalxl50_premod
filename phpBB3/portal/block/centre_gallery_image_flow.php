<?php
/*
*
* @name centre_gallery_image_flow.php
* @package phpBB3 Portal XL Premod
* @version $Id: centre_gallery_image_flow.php,v 2.0 2011/08/18 portalxl group Exp $
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
* Random Image Flow Pics
*/
$sql_order = 'RAND()';
$sql_limit = 10;

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

$center_gallery_image_flow = array();

while( $row = $db->sql_fetchrow($result) )
{
	$center_gallery_image_flow[] = $row;
}

if (count($center_gallery_image_flow) > 0)
{
	for ($i = 0; $i < count($center_gallery_image_flow); $i += phpbb_gallery_config::get('album_columns'))
	{
		$template->assign_block_vars('image_flow', array());

		for ($j = $i; $j < ($i + phpbb_gallery_config::get('album_columns')); $j++)
		{
			if( $j >= count($center_gallery_image_flow) )
			{
				break;
			}

			$album_id = $center_gallery_image_flow[$j]['image_album_id'];

			$template->assign_block_vars('center_gallery_image_flow', array(
				'U_PIC' 		=> append_sid("{$phpbb_root_path}gallery/image_page.$phpEx?album_id=". $center_gallery_image_flow[$j]['image_album_id'] . '&amp;image_id=' . $center_gallery_image_flow[$j]['image_id']),
				'THUMBNAIL' 	=> append_sid("{$phpbb_root_path}gallery/image.$phpEx", 'album_id=' . $center_gallery_image_flow[$j]['image_album_id'] . '&amp;image_id=' . $center_gallery_image_flow[$j]['image_id']),
				'DESC'		    => generate_text_for_display($center_gallery_image_flow[$j]['image_desc'], $center_gallery_image_flow[$j]['image_desc_uid'], $center_gallery_image_flow[$j]['image_desc_bitfield'], 7),
				'TITLE' 		=> $center_gallery_image_flow[$j]['image_name'], 
				));
		}
	}
}
else
{
	$template->assign_block_vars('no_pics', array());
}
$db->sql_freeresult($result);

// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
$total_images	= phpbb_gallery_config::get('num_images');
/*
$total_comments	= phpbb_gallery_config::get('num_comments');
$total_pgalleries	= phpbb_gallery_config::get('num_pegas');

$l_total_image_s = ($total_images == 0) ? 'TOTAL_IMAGES_ZERO' : 'TOTAL_IMAGES_OTHER';
$l_total_comment_s = ($total_comments == 0) ? 'TOTAL_COMMENTS_ZERO' : 'TOTAL_COMMENTS_OTHER';
$l_total_pgallery_s = ($total_pgalleries == 0) ? 'TOTAL_PGALLERIES_ZERO' : 'TOTAL_PGALLERIES_OTHER';
*/

/*
* Start output the page
*/
$template->assign_vars(array(
	'L_CATEGORY' 			=> $user->lang['ALBUM'],
	'L_PICS' 				=> $user->lang['IMAGES'],
	'L_LAST_PIC' 			=> $user->lang['LAST_IMAGE'],

	'S_THUMBNAIL_SIZE'	=> phpbb_gallery_config::get('thumbnail_height') + 20 + ((phpbb_gallery_config::get('thumbnail_infoline')) ? phpbb_gallery_constants::THUMBNAIL_INFO_HEIGHT : 0),
	'S_COLS'			=> phpbb_gallery_config::get('album_columns'),
	'S_COL_WIDTH'		=> (100 / phpbb_gallery_config::get('album_columns')) . '%',

	'L_NO_PICS' 			=> $user->lang['NO_IMAGES'],
	'L_VIEW' 				=> $user->lang['VIEWS'],
	'L_POSTER' 				=> $user->lang['POSTER'],
	'L_POSTED' 				=> $user->lang['POSTED'],
	
	'TOTAL_IMAGES'		=> (phpbb_gallery_config::get('disp_statistic')) ? $user->lang('TOTAL_IMAGES_SPRINTF', $total_images) : '',
/*
	'TOTAL_COMMENTS'	=> (phpbb_gallery_config::get('allow_comments')) ? sprintf($user->lang[$l_total_comment_s], $total_comments) : '',
	'TOTAL_PGALLERIES'	=> (phpbb_gallery::$auth->acl_check('a_list', phpbb_gallery_auth::PERSONAL_ALBUM)) ? sprintf($user->lang[$l_total_pgallery_s], $total_pgalleries) : '',
	'NEWEST_PGALLERIES'	=> ($total_pgalleries) ? sprintf($user->lang['NEWEST_PGALLERY'], get_username_string('full', phpbb_gallery_config::get('newest_pega_user_id'), phpbb_gallery_config::get('newest_pega_username'), phpbb_gallery_config::get('newest_pega_user_colour'), '', phpbb_gallery_url::append_sid('album', 'album_id=' . phpbb_gallery_config::get('newest_pega_album_id')))) : '',
*/
	
	'L_GALLERY'			=> $user->lang['GALLERY'],
	'U_GALLERY_MOD'		=> phpbb_gallery_url::append_sid('index'),
	));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
	'body' => 'portal/block/centre_gallery_image_flow.html',
	));

?>