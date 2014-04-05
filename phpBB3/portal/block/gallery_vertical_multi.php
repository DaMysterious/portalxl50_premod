<?php
/*
*
* @name gallery_vertical_multi.php
* @package phpBB3 Portal XL Premod
* @version $Id: gallery_vertical_scroll.php,v 2.0 2011/08/18 portalxl group Exp $
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
* Random scroll limited to 15 pics random
*/
$sql_order = 'RAND()';
$sql_limit = 5;

$sql_array = array(
	'SELECT'	=> 'i.*, ug.*, u.user_id, u.username, u.user_colour, u.user_country_flag',

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
		array(
			'FROM'	=>	array(USER_GROUP_TABLE	=> 'ug'),
			'ON'	=> 'i.image_user_id = ug.user_id',
			'AND'	=> 'ug.group_id = ' . FEATURED_PROVIDER 
		),
	),

	'WHERE'			=> '(' . $db->sql_in_set('i.image_album_id', phpbb_gallery::$auth->acl_album_ids('i_view'), false, true) . ' 
		AND i.image_status <> ' . phpbb_gallery_image::STATUS_UNAPPROVED . ')',
	'ORDER_BY'		=> $sql_order,
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query_limit($sql, $sql_limit);

$gallery_vertical_scroll_row = array();

while( $row = $db->sql_fetchrow($result) )
{
	$gallery_vertical_scroll_row[] = $row;
}

if (count($gallery_vertical_scroll_row) > 0)
{
	for ($i = 0; $i < count($gallery_vertical_scroll_row); $i += phpbb_gallery_config::get('album_columns'))
	{
		$template->assign_block_vars('image_random_scroll', array());

		for ($j = $i; $j < ($i + phpbb_gallery_config::get('album_columns')); $j++)
		{
			if( $j >= count($gallery_vertical_scroll_row) )
			{
				break;
			}

			$album_id = $gallery_vertical_scroll_row[$j]['image_album_id'];

		    // Country Flags Version 3.0.6
		    if ($user->data['user_id'] != ANONYMOUS)
		    {
			  $flag_title = $flag_img = $flag_img_src = '';
			  get_user_flag($gallery_vertical_scroll_row[$j]['user_country_flag'], $gallery_vertical_scroll_row[$j]['user_country'], $flag_title, $flag_img, $flag_img_src);
		    }
		    // Country Flags Version 3.0.6
			
			$template->assign_block_vars('image_random_scroll_col', array(
				'U_PIC' 		=> phpbb_gallery_url::append_sid('image_page', "album_id=" . $gallery_vertical_scroll_row[$j]['image_id']), 
				'THUMBNAIL'	    => phpbb_gallery_image::generate_link('thumbnail', 'plugin', $gallery_vertical_scroll_row[$j]['image_id'], $gallery_vertical_scroll_row[$j]['image_name'], $gallery_vertical_scroll_row[$j]['image_album_id']),
				'U_IMAGE' 		=> phpbb_gallery_url::append_sid('album', "album_id=$album_id"),
				'IMAGE_NAME'	=> $gallery_vertical_scroll_row[$j]['image_name'],
				'DESC'		    => generate_text_for_display($gallery_vertical_scroll_row[$j]['image_desc'], $gallery_vertical_scroll_row[$j]['image_desc_uid'], $gallery_vertical_scroll_row[$j]['image_desc_bitfield'], 7),
				
				'TITLE' 		=> $gallery_vertical_scroll_row[$j]['image_name'], 
				'POSTER' 		=> $gallery_vertical_scroll_row[$j]['username'], 
				'POSTER' 		=> get_username_string('full', $gallery_vertical_scroll_row[$j]['user_id'], ($gallery_vertical_scroll_row[$j]['user_id'] <> ANONYMOUS) ? $gallery_vertical_scroll_row[$j]['username'] : $user->lang['GUEST'], $gallery_vertical_scroll_row[$j]['user_colour']) . ' ' . $flag_img,
				'TIME' 			=> $user->format_date($gallery_vertical_scroll_row[$j]['image_time']), 
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


$template->assign_vars(array(
	'S_THUMBNAIL_SIZE'	=> phpbb_gallery_config::get('thumbnail_height') + 20 + ((phpbb_gallery_config::get('thumbnail_infoline')) ? phpbb_gallery_constants::THUMBNAIL_INFO_HEIGHT : 0),
	'S_COLS'			=> phpbb_gallery_config::get('album_columns'),
	'S_COL_WIDTH'		=> (100 / phpbb_gallery_config::get('album_columns')) . '%',
	
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
    'body' => 'portal/block/gallery_vertical_multi.html',
	));

?>