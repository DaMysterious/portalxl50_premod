<?php
/*
*
* @name center_newest_videos.php
* @package phpBB3 Portal XL Premod
* @version $Id: center_newest_videos.php,v 1.0.1 2011/10/10 portalxl group Exp $
*
* @copyright (c) 2007, 2011  Portal XL Group
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
*
*/
if (!defined('IN_PHPBB'))
{
   exit;
}

/*
* Start session management
*/
if (!function_exists('newest_set_rating_image'))
{
	$dm_video_path = (defined('DM_VIDEO_ROOT_PATH')) ? DM_VIDEO_ROOT_PATH : 'dm_video/';
	$user->add_lang('mods/dm_video');
	
	// Set the rating image for the video
	function newest_set_rating_image($votes, $sum, $avg_score)
	{
		global $user, $phpbb_root_path;
	
		$image 				= '';
		$rating_score 		= round($avg_score);
		$rating_score_text 	= $avg_score;
	
		if ($votes < 1)
		{
			$votes = $user->lang['DMV_RATING_NO'];
		}
		else
		{
			$votes = ($votes == 1 ) ? sprintf($user->lang['DMV_RATING_SUM'], $votes, $rating_score) : sprintf($user->lang['DMV_RATING_SUMS'], $votes, $rating_score_text);
		}
	
		for ($i = 1; $i <= $rating_score; $i++)
		{
			$image .= '<img src="' . $phpbb_root_path . 'images/dm_video/star1.gif" title="' . $votes . '" alt="' . $votes . '" />';
		}
	
		$blank_stars = ( 5 - $rating_score);
		for ($i = 1; $i <= $blank_stars; $i++)
		{
			$image .= '<img src="' . $phpbb_root_path . 'images/dm_video/star2.gif" title="' . $votes . '" alt="' . $votes . '" />';
		}
		return $image;
	}
}

/**
*/
$video_limit = $portal_config['portal_attachments_number']; // number of videos allowed to show, set in portal's ACP

/*
* Begin block script here
*/
$is_authorised = ($auth->acl_get('u_dm_video_view') || $auth->acl_get('a_dm_video_view')) ? true : false;

if ($is_authorised == true )
{
	$sql = 'SELECT v.*, u.*
		FROM ' . DM_VIDEO_TABLE . ' v
		LEFT JOIN ' . USERS_TABLE . ' u
			ON v.video_user_id = u.user_id
		WHERE v.video_approval = 1 
		ORDER BY v.video_time DESC
		LIMIT ' . $video_limit;
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$votes 		= $row['video_votetotal'];
		$sum 		= $row['video_votesum'];
		$avg_score 	= ($row['video_votetotal'] == 0) ? 0 : round($row['video_votesum'] / $row['video_votetotal'], 2);
		$cat_id 	= $row['video_cat_id'];

		$template->assign_block_vars('newest_videos', array(
			'TITLE'				=> $row['video_title'],
			'SINGER'			=> $row['video_singer'],
			'DURATION'			=> $row['video_duration'],
			'RATING'			=> newest_set_rating_image($votes, $sum, $avg_score),
			'VIEWS'				=> $row['video_counter'],
			'POST_AUTHOR_FULL'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']),
			'POST_TIME'			=> $user->format_date($row['video_time']),
			'U_SHOW_VIDEO'		=> append_sid("{$phpbb_root_path}{$dm_video_path}showvideo.$phpEx", 'v=' . $row['video_id'] . '&amp;c=' . $cat_id),
		));
	}
	$db->sql_freeresult($result);

	// Set the filename of the template you want to use for this file.
	$template->set_filenames(array(
	   'body'      => 'portal/block/center_newest_videos.html'
	));
}

?>