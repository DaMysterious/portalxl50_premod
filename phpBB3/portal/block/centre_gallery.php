<?php
/*
*
* @name centre_gallery.php
* @package phpBB3 Portal XL Premod
* @version $Id: centre_gallery.php,v 2.0 2011/08/18 portalxl group Exp $
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
* Recent images & comments and random images
*/
/**
* int		array	including all relevent numbers for rows, columns and stuff like that,
* display	int		sum of the options which should be displayed, see gallery/includes/constants.php "// Display-options for RRC-Feature" for values
* modes		int		sum of the modes which should be displayed, see gallery/includes/constants.php "// Mode-options for RRC-Feature" for values
* collapse	bool	collapse comments
* include_pgalleries	bool	include personal albums
* mode_id	string	'user' or 'album' to only display images of a certain user or album
* id		int		user_id for user profile or album_id for view of recent and random images
*/
if (phpbb_gallery_config::get('rrc_gindex_mode'))
{
	$ints = array(
		phpbb_gallery_config::get('rrc_gindex_rows'),
		phpbb_gallery_config::get('rrc_gindex_columns'),
		phpbb_gallery_config::get('rrc_gindex_crows'),
		phpbb_gallery_config::get('rrc_gindex_contests'),
	);
	$gallery_block = new phpbb_gallery_block(phpbb_gallery_config::get('rrc_gindex_mode'), phpbb_gallery_config::get('rrc_gindex_display'), $ints, phpbb_gallery_config::get('rrc_gindex_comments'), phpbb_gallery_config::get('rrc_gindex_pegas'));
	$gallery_block->display();
}

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
    'body' => 'portal/block/centre_gallery.html',
	));

?>