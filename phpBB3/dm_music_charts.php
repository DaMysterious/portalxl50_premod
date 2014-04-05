<?php
/**
*
* @package - DM Music Charts
* @version $Id: dm_music_charts.php 143 2011-01-29 17:26:31Z femu $
* @copyright ( (c) femu - http://die-muellers.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_dm_music_charts.' . $phpEx);

// Start session
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/dm_music_charts');

// Main template variables for the navigation
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['DM_MC_TITLE'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx"),
));

// Title for the browser header
$page_title = $user->lang['DM_MC_TITLE_SHORT'];

// Check authorisation
$is_authorised = ($auth->acl_get('u_dm_mc_view')) ? true : false;

if (!$is_authorised)
{
	trigger_error('NOT_AUTHORISED');
}

// Get variable for pagination and mode
$u_id = $user->data['user_id'];
$mode = request_var('mode', '');
$submit	= (isset($_POST['post'])) ? true : false;

// Output the page
page_header($page_title);

// Get config values
$sql = 'SELECT config_name, config_value
	FROM ' . DM_MUSIC_CHARTS_CONFIG_TABLE;
$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result))
{
    $mc_config[$row['config_name']] = $row['config_value'];
}
$db->sql_freeresult($result);

// Total number of charts
$sql = 'SELECT COUNT(chart_id) AS total_charts
	FROM ' . DM_MUSIC_CHARTS_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$total_charts = $row['total_charts'];
$db->sql_freeresult($result);

// Select the users, which already voted
$sql = 'SELECT COUNT(u.user_id) AS number_voted
	FROM ' . USERS_TABLE . ' u
	LEFT JOIN ' . DM_MUSIC_CHARTS_VOTERS_TABLE . ' v
		on u.user_id = v.vote_user_id
	WHERE u.user_id = v.vote_user_id
	GROUP BY v.vote_user_id';
$result = $db->sql_query($sql);
$number_voted = (int) $db->sql_fetchfield('number_voted');
$db->sql_freeresult($result);

// Select users, which already voted
$sql = 'SELECT u.user_id, u.username, u.user_colour
	FROM ' . USERS_TABLE . ' u
	LEFT JOIN ' . DM_MUSIC_CHARTS_VOTERS_TABLE . ' v
		on u.user_id = v.vote_user_id
	WHERE u.user_id = v.vote_user_id
	GROUP BY v.vote_user_id';
$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result))
{
	$vote_user_id = $vote_username = $vote_user_colour = $vote_user_full = '';
	
	$vote_user_id = $row['user_id'];
	$vote_username = $row['username'];
	$vote_user_colour = $row['user_colour'];
	
	$vote_user_full = get_username_string('full', $vote_user_id, $vote_username, $vote_user_colour);
	
	$template->assign_block_vars('user_voted', array(
		'USER_VOTED'	=> '&bull; ' . $vote_user_full . ' ',
	));  
}

if ($number_voted > 0)
{
	$template->assign_vars(array(
		'S_USERS_VOTED'		=> true,
		'VOTED_USERS'	=> ($number_voted == 1) ? $user->lang['DM_MC_VOTED_USERS_SINGLE'] : $user->lang['DM_MC_VOTED_USERS_MULTI'],
	));  
}
$db->sql_freeresult($result);

// Switch the mode
switch ($mode)
{
	// List charts - Top XX
	default:
	case 'list':

		$template_html = 'dm_music_charts/dm_music_charts.html';

		$i = 1;
		$video_id = request_var('v', 0);
		$start	= request_var('start', 0);
		$number = $mc_config['chart_num_top'];

		if ($user->data['dm_mc_check_1'] == 0)
		{
			$sql = "UPDATE " . USERS_TABLE . "
				SET dm_mc_check_1 = '1'
				WHERE user_id = " . $user->data['user_id'];
			$db->sql_query($sql);
		}

		if ($user->data['dm_mc_check_1'] == 1 && $user->data['dm_mc_check_2'] == 0 && (time() > ($config['chart_start_time'] + $config['chart_period'] - ($mc_config['chart_check_time'] * 3600))))
		{
			$sql = "UPDATE " . USERS_TABLE . "
				SET dm_mc_check_2 = '1'
				WHERE user_id = " . $user->data['user_id'];
			$db->sql_query($sql);
		}

		// Total voters
		$sql = 'SELECT vote_user_id
			FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
			GROUP BY vote_user_id';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrowset($result);
		$total_voters = sizeof($row);
		$db->sql_freeresult($result);

		if ($total_voters == 0)
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> $user->lang['DM_MC_NO_VOTERS'],
			));
		}
		else if ($total_voters == 1)
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> $user->lang['DM_MC_SINGLE_VOTER'],
			));
		}
		else
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> sprintf($user->lang['DM_MC_MULTI_VOTERS'], $total_voters),
			));
		}

		if ($mc_config['default_sort'])
		{
			$default_sort = 'ASC';
		}
		else
		{
			$default_sort = 'DESC';
		}

		// List Top xx chart songs
		$sql = 'SELECT u.user_id, u.username, u.user_colour, c.*
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			LEFT JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			ORDER BY c.chart_hot DESC, c.chart_not ASC, c.chart_last_pos ' . $default_sort;
		$result = $db->sql_query_limit($sql, $number, $start);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['chart_song_name'])
			{
				$position = $i;
				$chart_id = $row['chart_id'];

				// Set the voting images in order, if the user already voted, did not voted yet or is not logged in
				$sql2 = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
					WHERE vote_chart_id = ' . (int) $chart_id . '
						AND vote_user_id = ' . (int) $u_id;
				$result2 = $db->sql_query($sql2);
				$row2 = $db->sql_fetchrow($result2);

				// Suppress username, if not enough titles in charts
				if (!$row['chart_song_name'])
				{
					$username = '';
				}
				else
				{
					$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				}

				// Display web image, if website exists
				if (!$row['chart_song_name'])
				{
					$website = '';
				}
				else
				{
					if (!$row['chart_website'])
					{
						$website = '';
					}
					else
					{
						$website = '<a href="' . $row['chart_website'] . '" onclick="window.open(this.href); return false"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_www_charts.png" alt="" title="' . sprintf($user->lang['DM_MC_GOTO_WEB'], $row['chart_artist']) . '" /></a>';
					}
				}

				// Display video image, if video exists
				$video = $row['chart_video'];
				if (!$row['chart_song_name'] || (!$row['chart_video'] && $row['chart_video_no'] == 0))
				{
					$video = '';
				}
				else
				{
					$video = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_charts_video.png" alt="" title="' . sprintf($user->lang['DM_MC_SHOW_VIDEO'], $row['chart_song_name']) . '" />';
				}

				// Check if user is logged in
				if (!$row['chart_song_name'])
				{
					$vote_hot = $vote_not = '';
				}
				else
				{
					if (!$user->data['is_registered'])
					{
						$vote_hot = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot_vote.gif" alt="" title="' . $user->lang['DM_MC_NOT_LOGGED_IN'] . '" />';
						$vote_not = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not_vote.gif" alt="" title="' . $user->lang['DM_MC_NOT_LOGGED_IN'] . '" />';
					}
					else if (!$row2['vote_user_id'])
					{
						$vote_hot = '<a href = "' . append_sid("dm_music_charts.$phpEx", 'mode=vote&amp;rate=1&amp;id=' . $chart_id . '&amp;f=1') . '"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot.gif" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_HOT_TITLE'], $row['chart_artist']) . '" /></a>';
						$vote_not = '<a href = "' . append_sid("dm_music_charts.$phpEx", 'mode=vote&amp;rate=2&amp;id=' . $chart_id . '&amp;f=1') . '"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not.gif" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_NOT_TITLE'], $row['chart_artist']) . '" /></a>';
					}
					else
					{
						$vote_hot = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot_vote.gif" alt="" title="' . $user->lang['DM_MC_ALREADY_VOTED'] . '" />';
						$vote_not = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not_vote.gif" alt="" title="' . $user->lang['DM_MC_ALREADY_VOTED'] . '" />';
					}
				}

				if (file_exists($phpbb_root_path . 'dm_video/index.php'))
				{
					$dm_video = true;
				}
				else
				{
					$dm_video = false;
				}

				if ($position < $row['chart_last_pos'])
				{
					$tendency_img = '<img src="images/dm_music_charts/up.gif" width="12px" height="12px" alt="" title="" />';
				}
				else if ($position == $row['chart_last_pos'])
				{
					$tendency_img = '<img src="images/dm_music_charts/up_down.gif" width="15px" height="13px" alt="" title="" />';
				}
				else
				{
					$tendency_img = '<img src="images/dm_music_charts/down.gif" width="12px" height="12px" alt="" title="" />';
				}

				// Check, if Highslide JS is installed
				if (file_exists($phpbb_root_path . 'styles/' . $user->theme['theme_path'] . '/theme/highslide/highslide-full.js'))
				{
					$template->assign_vars(array(
						'S_HIGHSLIDE'	=> true,
					));
				}

				$template->assign_block_vars('charts', array(
					'S_DM_VIDEO' 	=> $dm_video,
					'S_EDIT_SONG'	=> (($auth->acl_get('u_dm_mc_edit') && $row['chart_poster_id'] == $u_id) || $auth->acl_get('a_dm_mc_manage')),
					'EDIT_IMG' 		=> $user->img('icon_post_edit', 'EDIT_POST'),
					'DELETE_IMG' 	=> $user->img('icon_post_delete', 'DELETE_POST'),
					'TENDENCY_IMG'	=> ($row['chart_last_pos'] == 0) ? '' : $tendency_img,
					'POSITION'		=> $position,
					'USERNAME'		=> $username,
					'TITLE'			=> $row['chart_song_name'],
					'ARTIST'		=> $row['chart_artist'],
					'ALBUM'			=> $row['chart_album'],
					'ALBUM_IMG'		=> ($row['chart_picture'] == '') ? '' : '<img src="' . $row['chart_picture'] . '" width="50px" height="50px" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_TITLE'], $row['chart_artist']) . '" />',
					'YEAR'			=> $row['chart_year'],
					'WEBSITE'		=> $website,
					'VIDEO'			=> $video,
					'VOTE_HOT'		=> $vote_hot,
					'VOTE_NOT'		=> $vote_not,
					'LAST'			=> ($row['chart_last_pos'] == 0) ? '<img src="images/dm_music_charts/icon_new_charts.gif" alt="" title="' . $user->lang['DM_MC_NEW_PLACED'] . '" />' : $row['chart_last_pos'],
					'CHART_HOT_NOT' => '(' . $user->lang['DM_MC_HOT'] . ': ' . $row['chart_hot'] . ' - ' . $user->lang['DM_MC_NOT'] . ': ' . $row['chart_not'] . ')',
					'BEST'			=> $row['chart_best_pos'],
					'ADDED_TIME'	=> ($row['chart_add_time'] == 0) ? '' : sprintf($user->lang['DM_MC_ADDED_TIME'], $user->format_date($row['chart_add_time'])),
					'U_SHOW_VIDEO'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=showvideo&amp;v=" . $row['chart_id']),
					'U_DELETE_SONG'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=delete&amp;id=" . $row['chart_id']),
					'U_EDIT_SONG'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=edit&amp;id=" . $row['chart_id'] . '&amp;f=1'),

					));
				$i++;
			}
		}
		$db->sql_freeresult($result);

		$ranking_dir = ($mc_config['default_sort']) ? $user->lang['DM_MC_RANK_ASC'] : $user->lang['DM_MC_RANK_DESC'];

		$template->assign_vars(array(
			'S_LIST'			=> true,
			'S_ADD_SONG_LIST'	=> $auth->acl_get('u_dm_mc_add'),
			'MC_TITLE'			=> sprintf($user->lang['DM_MC_CHARTS'], $mc_config['chart_num_top']),
			'MC_TITLE_EXPLAIN'	=> sprintf($user->lang['DM_MC_HEADER_EXPLAIN'], $user->format_date($config['chart_start_time'] + $config['chart_period']), $ranking_dir),
			'MC_NEWEST_XX'		=> sprintf($user->lang['DM_MC_NEWEST_XX'], $mc_config['chart_num_last']),
			'MC_LIST_ALL'		=> sprintf($user->lang['DM_MC_SHOW_ALL'], $total_charts),
			'U_LIST_ALL'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=list_all'),
			'U_LIST_NEWEST'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_newest"),
			'U_ADD_SONG' 		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=add'),
			'U_LAST_WINNERS'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=winners'),
		));
	break;

	// List charts - All songs
	case 'list_all':

		$template_html = 'dm_music_charts/dm_music_charts.html';

		$i = 1;
		$video_id = request_var('v', 0);

		$start = request_var('start', 0);
		$number = $mc_config['chart_user_page'];

		// Total voters
		$sql = 'SELECT vote_user_id
			FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
			GROUP BY vote_user_id';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrowset($result);
		$total_voters = sizeof($row);
		$db->sql_freeresult($result);

		if ($total_voters == 0)
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> $user->lang['DM_MC_NO_VOTERS'],
			));
		}
		else if ($total_voters == 1)
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> $user->lang['DM_MC_SINGLE_VOTER'],
			));
		}
		else
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> sprintf($user->lang['DM_MC_MULTI_VOTERS'], $total_voters),
			));
		}

		if ($mc_config['default_sort'])
		{
			$default_sort = 'ASC';
		}
		else
		{
			$default_sort = 'DESC';
		}

		// List all chart songs
		$sql = 'SELECT u.user_id, u.username, u.user_colour, c.*
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			INNER JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			ORDER BY c.chart_hot DESC, c.chart_not ASC, c.chart_last_pos ' . $default_sort;
		$result = $db->sql_query_limit($sql, $number, $start);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['chart_song_name'])
			{
				$position = $i;
				$chart_id = $row['chart_id'];

				// Set the voting images in order, if the user already voted, did not voted yet or is not logged in
				$sql2 = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
					WHERE vote_chart_id = ' . (int) $chart_id . '
						AND vote_user_id = ' . (int) $u_id;
				$result2 = $db->sql_query($sql2);
				$row2 = $db->sql_fetchrow($result2);

				// Suppress username, if not enough titles in charts
				if (!$row['chart_song_name'])
				{
					$username = '';
				}
				else
				{
					$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				}

				// Display web image, if website exists
				$website = $row['chart_website'];
				if (!$row['chart_song_name'])
				{
					$website = '';
				}
				else
				{
					if (!$row['chart_website'])
					{
						$website = '';
					}
					else
					{
						$website = '<a href="' . $row['chart_website'] . '" onclick="window.open(this.href); return false"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_www_charts.png" alt="" title="' . sprintf($user->lang['DM_MC_GOTO_WEB'], $row['chart_artist']) . '" /></a>';
					}
				}

				// Display video image, if video exists
				$video = $row['chart_video'];
				if (!$row['chart_song_name'] || (!$row['chart_video'] && $row['chart_video_no'] == 0))
				{
					$video = '';
				}
				else
				{
					$video = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_charts_video.png" alt="" title="' . sprintf($user->lang['DM_MC_SHOW_VIDEO'],$row['chart_song_name']) . '" />';
				}

				// Check if user is logged in
				if (!$row['chart_song_name'])
				{
					$vote_hot = $vote_not = '';
				}
				else
				{
					if (!$user->data['is_registered'])
					{
						$vote_hot = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot_vote.gif" alt="" title="' . $user->lang['DM_MC_NOT_LOGGED_IN'] . '" />';
						$vote_not = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not_vote.gif" alt="" title="' . $user->lang['DM_MC_NOT_LOGGED_IN'] . '" />';
					}
					else if (!$row2['vote_user_id'])
					{
						$vote_hot = '<a href = "' . append_sid("dm_music_charts.$phpEx", 'mode=vote&amp;rate=1&amp;id=' . $chart_id . '&amp;f=2') . '"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot.gif" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_HOT_TITLE'], $row['chart_artist']) . '" /></a>';
						$vote_not = '<a href = "' . append_sid("dm_music_charts.$phpEx", 'mode=vote&amp;rate=2&amp;id=' . $chart_id . '&amp;f=2') . '"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not.gif" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_NOT_TITLE'], $row['chart_artist']) . '" /></a>';
					}
					else
					{
						$vote_hot = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot_vote.gif" alt="" title="' . $user->lang['DM_MC_ALREADY_VOTED'] . '" />';
						$vote_not = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not_vote.gif" alt="" title="' . $user->lang['DM_MC_ALREADY_VOTED'] . '" />';
					}
				}

				if (file_exists($phpbb_root_path . 'dm_video/index.php'))
				{
					$dm_video = true;
				}
				else
				{
					$dm_video = false;
				}

				if (($position + $start) < $row['chart_last_pos'])
				{
					$tendency_img = '<img src="images/dm_music_charts/up.gif" width="12px" height="12px" alt="" title="" />';
				}
				else if (($position + $start) == $row['chart_last_pos'])
				{
					$tendency_img = '<img src="images/dm_music_charts/up_down.gif" width="15px" height="13px" alt="" title="" />';
				}
				else
				{
					$tendency_img = '<img src="images/dm_music_charts/down.gif" width="12px" height="12px" alt="" title="" />';
				}

				// Check, if Highslide JS is installed
				if (file_exists($phpbb_root_path . 'styles/' . $user->theme['theme_path'] . '/theme/highslide/highslide-full.js'))
				{
					$template->assign_vars(array(
						'S_HIGHSLIDE'	=> true,
					));
				}

				$template->assign_block_vars('charts', array(
					'S_DM_VIDEO'	=> $dm_video,
					'S_EDIT_SONG'	=> (($auth->acl_get('u_dm_mc_edit') && $row['chart_poster_id'] == $u_id) || $auth->acl_get('a_dm_mc_manage')),
					'EDIT_IMG' 		=> $user->img('icon_post_edit', 'EDIT_POST'),
					'DELETE_IMG' 	=> $user->img('icon_post_delete', 'DELETE_POST'),
					'TENDENCY_IMG'	=> ($row['chart_last_pos'] == 0) ? '' : $tendency_img,
					'POSITION'		=> $position + $start,
					'USERNAME'		=> $username,
					'TITLE'			=> $row['chart_song_name'],
					'ARTIST'		=> $row['chart_artist'],
					'ALBUM'			=> $row['chart_album'],
					'ALBUM_IMG'		=> '<img src="' . $row['chart_picture'] . '" width="50px" height="50px" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_TITLE'], $row['chart_artist']) . '" />',
					'YEAR'			=> $row['chart_year'],
					'WEBSITE'		=> $website,
					'VIDEO'			=> $video,
					'VOTE_HOT'		=> $vote_hot,
					'VOTE_NOT'		=> $vote_not,
					'LAST'			=> ($row['chart_last_pos'] == 0) ? '<img src="images/dm_music_charts/icon_new_charts.gif" alt="" title="' . $user->lang['DM_MC_NEW_PLACED'] . '" />' : $row['chart_last_pos'],
					'BEST'			=> $row['chart_best_pos'],
					'ADDED_TIME'	=> ($row['chart_add_time'] == 0) ? '' : sprintf($user->lang['DM_MC_ADDED_TIME'], $user->format_date($row['chart_add_time'])),
					'CHART_HOT_NOT' => '(' . $user->lang['DM_MC_HOT'] . ': ' . $row['chart_hot'] . ' - ' . $user->lang['DM_MC_NOT'] . ': ' . $row['chart_not'] . ')',
					'U_SHOW_VIDEO'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=showvideo&amp;v=" . $row['chart_id']),
					'U_DELETE_SONG'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=delete&amp;id=" . (int) $chart_id),
					'U_EDIT_SONG'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=edit&amp;id=" . $row['chart_id'] . '&amp;f=2'),
				));
				$i++;
			}
		}
		$db->sql_freeresult($result);

		$ranking_dir = ($mc_config['default_sort']) ? $user->lang['DM_MC_RANK_ASC'] : $user->lang['DM_MC_RANK_DESC'];

		$template->assign_vars(array(
			'S_LIST_ALL'		=> true,
			'S_ADD_SONG_LIST'	=> $auth->acl_get('u_dm_mc_add'),
			'MC_TITLE'			=> $user->lang['DM_MC_ALL_TITLE'],
			'MC_TITLE_EXPLAIN'	=> sprintf($user->lang['DM_MC_HEADER_EXPLAIN'], $user->format_date($config['chart_start_time'] + $config['chart_period']), $ranking_dir),
			'MC_TOP_XX'			=> sprintf($user->lang['DM_MC_TOP_TEN'], $mc_config['chart_num_top']),
			'MC_NEWEST_XX'		=> sprintf($user->lang['DM_MC_NEWEST_XX'], $mc_config['chart_num_last']),
			'MC_LIST_ALL'		=> sprintf($user->lang['DM_MC_SHOW_ALL'], $total_charts),
			'U_LIST_TOP'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list"),
			'U_ADD_SONG' 		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=add"),
			'U_LIST_NEWEST'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_newest"),
			'U_LAST_WINNERS'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=winners'),
			'PAGINATION' 		=> generate_pagination(append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_all"), $total_charts, $number, $start, true),
			'PAGE_NUMBER'		=> on_page($total_charts, $number, $start),
			'TOTAL_CHARTS'		=> ($total_charts == 1) ? $user->lang['DM_MC_SINGLE'] : sprintf($user->lang['DM_MC_MULTI'], $total_charts),
		));
	break;

	case 'list_newest':

		$template_html = 'dm_music_charts/dm_music_charts.html';

		$i = 1;
		$video_id = request_var('v', 0);
		$start	= request_var('start', 0);
		$number = $mc_config['chart_num_last'];

		// Total voters
		$sql = 'SELECT vote_user_id
			FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
			GROUP BY vote_user_id';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrowset($result);
		$total_voters = sizeof($row);
		$db->sql_freeresult($result);

		if ($total_voters == 0)
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> $user->lang['DM_MC_NO_VOTERS'],
			));
		}
		else if ($total_voters == 1)
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> $user->lang['DM_MC_SINGLE_VOTER'],
			));
		}
		else
		{
			$template->assign_vars(array(
				'TOTAL_VOTERS'	=> sprintf($user->lang['DM_MC_MULTI_VOTERS'], $total_voters),
			));
		}

		// List xx newest chart songs
		$sql = 'SELECT u.user_id, u.username, u.user_colour, c.*
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			LEFT JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			ORDER BY c.chart_id DESC';
		$result = $db->sql_query_limit($sql, $number, $start);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['chart_song_name'])
			{
				$position = $i;
				$chart_id = $row['chart_id'];

				// Set the voting images in order, if the user already voted, did not voted yet or is not logged in
				$sql2 = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
					WHERE vote_chart_id = ' . (int) $chart_id . '
						AND vote_user_id = ' . (int) $u_id;
				$result2 = $db->sql_query($sql2);
				$row2 = $db->sql_fetchrow($result2);

				// Suppress username, if not enough titles in charts
				if (!$row['chart_song_name'])
				{
					$username = '';
				}
				else
				{
					$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				}

				// Display web image, if website exists
				if (!$row['chart_song_name'])
				{
					$website = '';
				}
				else
				{
					if (!$row['chart_website'])
					{
						$website = '';
					}
					else
					{
						$website = '<a href="' . $row['chart_website'] . '" onclick="window.open(this.href); return false"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_www_charts.png" alt="" title="' . sprintf($user->lang['DM_MC_GOTO_WEB'], $row['chart_artist']) . '" /></a>';
					}
				}

				// Display video image, if video exists
				$video = $row['chart_video'];
				if (!$row['chart_song_name'] || (!$row['chart_video'] && $row['chart_video_no'] == 0))
				{
					$video = '';
				}
				else
				{
					$video = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_charts_video.png" alt="" title="' . sprintf($user->lang['DM_MC_SHOW_VIDEO'],$row['chart_song_name']) . '" />';
				}

				// Check if user is logged in
				if (!$row['chart_song_name'])
				{
					$vote_hot = $vote_not = '';
				}
				else
				{
					if (!$user->data['is_registered'])
					{
						$vote_hot = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot_vote.gif" alt="" title="' . $user->lang['DM_MC_NOT_LOGGED_IN'] . '" />';
						$vote_not = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not_vote.gif" alt="" title="' . $user->lang['DM_MC_NOT_LOGGED_IN'] . '" />';
					}
					else if (!$row2['vote_user_id'])
					{
						$vote_hot = '<a href = "' . append_sid("dm_music_charts.$phpEx", 'mode=vote&amp;rate=1&amp;id=' . $chart_id . '&amp;f=3') . '"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot.gif" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_HOT_TITLE'], $row['chart_artist']) . '" /></a>';
						$vote_not = '<a href = "' . append_sid("dm_music_charts.$phpEx", 'mode=vote&amp;rate=2&amp;id=' . $chart_id . '&amp;f=3') . '"><img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not.gif" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_NOT_TITLE'], $row['chart_artist']) . '" /></a>';
					}
					else
					{
						$vote_hot = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_hot_vote.gif" alt="" title="' . $user->lang['DM_MC_ALREADY_VOTED'] . '" />';
						$vote_not = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/icon_not_vote.gif" alt="" title="' . $user->lang['DM_MC_ALREADY_VOTED'] . '" />';
					}
				}

				if (file_exists($phpbb_root_path . 'dm_video/index.php'))
				{
					$dm_video = true;
				}
				else
				{
					$dm_video = false;
				}

				if ($position < $row['chart_last_pos'])
				{
					$tendency_img = '<img src="images/dm_music_charts/up.gif" width="12px" height="12px" alt="" title="" />';
				}
				else if ($position == $row['chart_last_pos'])
				{
					$tendency_img = '<img src="images/dm_music_charts/up_down.gif" width="15px" height="13px" alt="" title="" />';
				}
				else
				{
					$tendency_img = '<img src="images/dm_music_charts/down.gif" width="12px" height="12px" alt="" title="" />';
				}

				// Check, if Highslide JS is installed
				if (file_exists($phpbb_root_path . 'styles/' . $user->theme['theme_path'] . '/theme/highslide/highslide-full.js'))
				{
					$template->assign_vars(array(
						'S_HIGHSLIDE'	=> true,
					));
				}

				$template->assign_block_vars('charts', array(
					'S_DM_VIDEO' 	=> $dm_video,
					'S_EDIT_SONG'	=> (($auth->acl_get('u_dm_mc_edit') && $row['chart_poster_id'] == $u_id) || $auth->acl_get('a_dm_mc_manage')),
					'EDIT_IMG' 		=> $user->img('icon_post_edit', 'EDIT_POST'),
					'DELETE_IMG' 	=> $user->img('icon_post_delete', 'DELETE_POST'),
					'TENDENCY_IMG'	=> ($row['chart_last_pos'] == 0) ? '' : $tendency_img,
					'POSITION'		=> $position,
					'USERNAME'		=> $username,
					'TITLE'			=> $row['chart_song_name'],
					'ARTIST'		=> $row['chart_artist'],
					'ALBUM'			=> $row['chart_album'],
					'ALBUM_IMG'		=> ($row['chart_picture'] == '') ? '' : '<img src="' . $row['chart_picture'] . '" width="50px" height="50px" alt="" title="' . sprintf($user->lang['DM_MC_PICTURE_TITLE'], $row['chart_artist']) . '" />',
					'YEAR'			=> $row['chart_year'],
					'WEBSITE'		=> $website,
					'VIDEO'			=> $video,
					'VOTE_HOT'		=> $vote_hot,
					'VOTE_NOT'		=> $vote_not,
					'LAST'			=> ($row['chart_last_pos'] == 0) ? '<img src="images/dm_music_charts/icon_new_charts.gif" alt="" title="' . $user->lang['DM_MC_NEW_PLACED'] . '" />' : $row['chart_last_pos'],
					'CHART_HOT_NOT' => '(' . $user->lang['DM_MC_HOT'] . ': ' . $row['chart_hot'] . ' - ' . $user->lang['DM_MC_NOT'] . ': ' . $row['chart_not'] . ')',
					'BEST'			=> $row['chart_best_pos'],
					'ADDED_TIME'	=> ($row['chart_add_time'] == 0) ? '' : sprintf($user->lang['DM_MC_ADDED_TIME'], $user->format_date($row['chart_add_time'])),
					'U_SHOW_VIDEO'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=showvideo&amp;v=" . $row['chart_id']),
					'U_DELETE_SONG'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=delete&amp;id=" . $row['chart_id']),
					'U_EDIT_SONG'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=edit&amp;id=" . $row['chart_id'] . '&amp;f=1'),

					));
				$i++;
			}
		}
		$db->sql_freeresult($result);

		$ranking_dir = ($mc_config['default_sort']) ? $user->lang['DM_MC_RANK_ASC'] : $user->lang['DM_MC_RANK_DESC'];

		$template->assign_vars(array(
			'S_LIST_NEWEST'		=> true,
			'S_ADD_SONG_LIST'	=> $auth->acl_get('u_dm_mc_add'),
			'MC_TITLE'			=> sprintf($user->lang['DM_MC_CHART_NEWEST'], $mc_config['chart_num_last']),
			'MC_TITLE_EXPLAIN'	=> sprintf($user->lang['DM_MC_HEADER_EXPLAIN'], $user->format_date($config['chart_start_time'] + $config['chart_period']), $ranking_dir),
			'MC_TOP_XX'			=> sprintf($user->lang['DM_MC_TOP_TEN'], $mc_config['chart_num_top']),
			'MC_NEWEST_XX'		=> sprintf($user->lang['DM_MC_NEWEST_XX'], $mc_config['chart_num_last']),
			'MC_LIST_ALL'		=> sprintf($user->lang['DM_MC_SHOW_ALL'], $total_charts),
			'U_LIST_TOP'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list"),
			'U_LIST_ALL'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=list_all'),
			'U_ADD_SONG' 		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=add'),
			'U_LAST_WINNERS'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=winners'),
		));
	break;

	case 'vote':

		$template_html = 'dm_music_charts/dm_music_charts.html';

		$chart_id		= request_var('id', 0);
		$rate			= request_var('rate', 0);
		$coming_from 	= request_var('f', 0);

		// Grab some data from the charts table
		$sql = 'SELECT chart_song_name, chart_artist
			FROM ' . DM_MUSIC_CHARTS_TABLE . '
			WHERE chart_id = ' . $chart_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$song = $row['chart_song_name'];
		$artist = $row['chart_artist'];
		$db->sql_freeresult($result);

		// Check if user already voted
		$sql = 'SELECT COUNT(vote_chart_id) AS counter
			FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . '
			WHERE vote_chart_id = ' . $chart_id . '
				AND vote_user_id = ' . $u_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$counter = $row['counter'];
		$db->sql_freeresult($result);

		if (!$counter)
		{
			// Create array for the voting
			$sql_ary = array(
				'vote_user_id'	=> $u_id,
				'vote_chart_id'	=> $chart_id,
				'vote_rate'		=> $rate,
			);

			$db->sql_query('INSERT INTO ' . DM_MUSIC_CHARTS_VOTERS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

			// Giving points for voting, if UPS is installed and active
			if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'] && $mc_config['points_per_vote'] > 0)
			{
				if (!function_exists('add_points'))
				{
					include($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
				}

				// Add the points
				add_points((int) $u_id, $mc_config['points_per_vote']);

				$voting_points = $mc_config['points_per_vote'];
			}

			// Update the charts table
			if ($rate==1)
			{
				$sql = 'UPDATE ' . DM_MUSIC_CHARTS_TABLE . '
					SET chart_hot = chart_hot + 1
				WHERE chart_id = ' . $chart_id;
				$db->sql_query($sql);
			}
			else
			{
				$sql = 'UPDATE ' . DM_MUSIC_CHARTS_TABLE . '
					SET chart_not = chart_not + 1
				WHERE chart_id = ' . $chart_id;
				$db->sql_query($sql);
			}

			if ($coming_from == 1)
			{
				$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");
			}
			elseif ($coming_from == 2)
			{
				$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_all");
			}
			elseif ($coming_from == 3)
			{
				$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_newest");
			}

			if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'] && $mc_config['points_per_vote'] > 0)
			{
				trigger_error(sprintf($user->lang['DM_MC_VOTE_SUCCESS_UPS'], $song, $artist, $voting_points, $config['points_name']) . sprintf($user->lang['DM_MC_BACKLINK'],'<br /><br /><a href="' . $redirect_url . '">', '</a>'));
			}
			else
			{
				trigger_error(sprintf($user->lang['DM_MC_VOTE_SUCCESS'], $song, $artist) . sprintf($user->lang['DM_MC_BACKLINK'],'<br /><br /><a href="' . $redirect_url . '">', '</a>'));
			}
		}
		else
		{
			if ($coming_from == 1)
			{
				$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");
			}
			elseif ($coming_from == 2)
			{
				$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_all");
			}
			elseif ($coming_from == 3)
			{
				$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_newest");
			}

			trigger_error($user->lang['DM_MC_ALREADY_VOTED'] . sprintf($user->lang['DM_MC_BACKLINK'],'<br /><br /><a href="' . $redirect_url . '">', '</a>'));
		}
	break;

	case 'showvideo':

		// Check, if Highslide JS is installed
		if (file_exists($phpbb_root_path . 'styles/' . $user->theme['theme_path'] . '/theme/highslide/highslide-full.js'))
		{
			$template_html = 'dm_music_charts/dm_music_charts_video_hs.html';
		}
		else
		{
			$template_html = 'dm_music_charts/dm_music_charts_video_popup.html';
		}

		$video_id	= request_var('v', 0);

		$sql = 'SELECT *
			FROM ' . DM_MUSIC_CHARTS_TABLE . '
			WHERE chart_id = ' . (int) $video_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row['chart_video_no'])
		{
			$video = $row['chart_video'];
		}
		else // We use a video from the DM Video
		{
			// Pickup the video code
			$sql = 'SELECT video_url
				FROM ' . DM_VIDEO_TABLE . '
				WHERE video_id = ' . (int) $row['chart_video_no'];
			$result = $db->sql_query($sql);
			$video = $db->sql_fetchfield('video_url');
			$db->sql_freeresult($result);
		}

		$video_title 	= (string) $row['chart_song_name'];
		$video_singer 	= (string) $row['chart_artist'];

		if ($row['chart_video_no'] > 0)
		{
			// Pickup the video code from DM Video
			$sql = 'SELECT video_url
				FROM ' . DM_VIDEO_TABLE . '
				WHERE video_id = ' . (int) $row['chart_video_no'];
			$result = $db->sql_query($sql);
			$video_url = $db->sql_fetchfield('video_url');
			$db->sql_freeresult($result);
		}
		else
		{
			$video_url = (string) $row['chart_video'];
		}

		// Send the variables to the template
		$template->assign_vars(array(
			'VIDEO_TITLE' 	=> $video_title,
			'VIDEO_SINGER'	=> $video_singer,
			'VIDEO_URL'		=> htmlspecialchars_decode($video_url),
			)
		);
		$db->sql_freeresult($result);
	break;

	case 'add':

		$announce_song_name = $announce_artist = '';

		if ($mc_config['chart_max_entries'] > 0)
		{
			// Check if maximum songs is reached
			$sql = 'SELECT COUNT(chart_id) AS counter
				FROM ' . DM_MUSIC_CHARTS_TABLE;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$counter = $row['counter'];
			$db->sql_freeresult($result);

			if ($counter >= $mc_config['chart_max_entries'])
			{
				$back_link = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx");
				trigger_error(sprintf($user->lang['DM_MC_COUNT_ERROR'], $mc_config['chart_max_entries']) . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $back_link . '">', '</a>'));
			}
		}

		$template_html = 'dm_music_charts/dm_music_charts.html';

		add_form_key('dm_charts');

		if (!$auth->acl_get('u_dm_mc_add', 'a_dm_mc_manage'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		if ($submit)
		{
			if (!check_form_key('dm_charts'))
			{
				trigger_error($user->lang['FORM_INVALID']);
			}

			$data['chart_song_name']	= utf8_normalize_nfc(request_var('chart_song_name', '', true));
			$data['chart_artist']		= utf8_normalize_nfc(request_var('chart_artist', '', true));
			$data['chart_album'] 		= utf8_normalize_nfc(request_var('chart_album', '', true));
			$data['chart_year']			= utf8_normalize_nfc(request_var('chart_year', '', true));
			$data['chart_picture']		= utf8_normalize_nfc(request_var('chart_picture', '', true));
			$data['chart_website']		= utf8_normalize_nfc(request_var('chart_website', '', true));
			$data['chart_video']		= utf8_normalize_nfc(request_var('chart_video', '', true));
			$data['chart_video_no']		= utf8_normalize_nfc(request_var('chart_video_no', 0));
			$data['chart_user_points']	= utf8_normalize_nfc(request_var('chart_user_points', 0));

			$back_link = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=add");
			$back_link_overview = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");

			if (empty($data['chart_song_name']) && empty($data['chart_artist']))
			{
				trigger_error($user->lang['DM_MC_FIELDS_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<br /><br /><a href="#" onclick="history.go(-1);return false;">', '</a>') . ' | ' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $back_link_overview . '">', '</a>'));
			}

			if (empty($data['chart_song_name']))
			{
				trigger_error($user->lang['DM_MC_TITLE_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if (empty($data['chart_artist']))
			{
				trigger_error($user->lang['DM_MC_ARTIST_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_1'] == 1 && empty($data['chart_album']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_ALBUM_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_2'] == 1 && empty($data['chart_picture']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_ALBUMCOVER_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_3'] == 1 && empty($data['chart_year']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_YEAR_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			
			if ($mc_config['required_4'] == 1 && empty($data['chart_website']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_WEBSITE_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_5'] == 1 && empty($data['chart_video']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_VIDEO_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the link to the album cover starts with http://
			if ((($mc_config['required_2'] == 0 && !empty($data['chart_picture'])) && (stripos($data['chart_picture'], 'http://') === false)) || (($mc_config['required_2'] == 1 && !empty($data['chart_picture'])) && (stripos($data['chart_picture'], 'http://') === false)))
			{
				trigger_error($user->lang['DM_MC_COVER_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the publishing year is between 1900 and 2099
			if ((($mc_config['required_3'] == 0 && !empty($data['chart_year'])) && ($data['chart_year'] < 1900 || $data['chart_year'] >= 2100)) || (($mc_config['required_3'] == 1 && !empty($data['chart_year'])) && ($data['chart_year'] < 1900 || $data['chart_year'] >= 2100)))
			{
				trigger_error($user->lang['DM_MC_YEAR_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the link to the website starts with http://
			if ((($mc_config['required_4'] == 0 && !empty($data['chart_website'])) && (stripos($data['chart_website'], 'http://') === false)) || (($mc_config['required_4'] == 1 && !empty($data['chart_website'])) && (stripos($data['chart_website'], 'http://') === false)))
			{
				trigger_error($user->lang['DM_MC_WEBSITE_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the embedded code is entered correctly
			$search_string = $data['chart_video'];
			$find_string_1 = 'object';
			$find_string_2 = 'iframe';
			$search_result_1 = stristr($search_string, $find_string_1);
			$search_result_2 = stristr($search_string, $find_string_2);

			if ((($mc_config['required_5'] == 0 && !empty($data['chart_video']) && $search_result_1 === false) || ($mc_config['required_5'] == 1 && !empty($data['chart_video']) && $search_result_1 === false)) && (($mc_config['required_5'] == 0 && !empty($data['chart_video']) && $search_result_2 === false) || ($mc_config['required_5'] == 1 && !empty($data['chart_video']) && $search_result_2 === false)))
			{
				trigger_error($user->lang['DM_MC_EMBED_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check if new song probably already exist
			$sql = 'SELECT *
				FROM ' . DM_MUSIC_CHARTS_TABLE . '
					WHERE LCASE(chart_song_name) LIKE LCASE("%' . $data['chart_song_name'] . '%")
						AND LCASE(chart_artist) LIKE LCASE("%' . $data['chart_artist'] . '%")';
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			
			if ($row['chart_song_name'])
			{
				trigger_error(sprintf($user->lang['DM_MC_ALREADY_EXISTS_ERROR'], $data['chart_song_name'], $data['chart_artist']) . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check if the video ID from DM Video really exists
			if ($data['chart_video_no'] > 0)
			{
				$sql = 'SELECT video_id
					FROM ' . DM_VIDEO_TABLE . '
					WHERE video_id = ' . $data['chart_video_no'];
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($user->lang['DM_MC_VIDEO_EXIST_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
				}
			}

			// UPS part
			if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'])
			{
				$user_points = $mc_config['chart_ups_points'];
			}
			else
			{
				$user_points = 0;
			}

			// Create array for charts
			$sql_ary = array(
				'chart_song_name'	=> $data['chart_song_name'],
				'chart_artist'		=> $data['chart_artist'],
				'chart_album'		=> $data['chart_album'],
				'chart_year'		=> $data['chart_year'],
				'chart_picture'		=> $data['chart_picture'],
				'chart_website'		=> $data['chart_website'],
				'chart_video'		=> $data['chart_video'],
				'chart_video_no'	=> $data['chart_video_no'],
				'chart_poster_id'	=> $u_id,
				'chart_user_points'	=> $user_points,
				'chart_add_time'	=> time(),
			);

			$db->sql_query('INSERT INTO ' . DM_MUSIC_CHARTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

			$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");

			// Announce new songs, if enabled
			if ($mc_config['announce_enable'])
			{
				$sql = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_TABLE . '
					ORDER BY chart_id DESC';
				$result = $db->sql_query_limit($sql, 1);

				while ($row = $db->sql_fetchrow($result))
				{
					$announce_song_name = $row['chart_song_name'];
					$announce_artist	= $row['chart_artist'];
					$announce_img      = $row['chart_picture'];
				}
				$db->sql_freeresult($result);

				$charts_link 	= '[url=' . generate_board_url() . '/dm_music_charts.php?mode=list_newest]' . $user->lang['DM_MC_GO_CHARTS'] . '[/url]';
				$song_subject 	= sprintf($user->lang['DM_MC_ANNOUNCE_TITLE'], $announce_song_name, $announce_artist);
				$song_msg 		= sprintf($user->lang['DM_MC_ANNOUNCE_MSG'], $announce_song_name, $announce_artist, $charts_link, $announce_img);

				create_announcement($song_subject, $song_msg, $mc_config['announce_forum']);
			}

			// UPS part
			if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'])
			{
				if ($mc_config['chart_ups_points'] > 0)
				{
					if ( !function_exists('add_points') )
					{
						include($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
					}

					add_points($u_id, $mc_config['chart_ups_points']);

					meta_refresh(3, $redirect_url);
					trigger_error(sprintf($user->lang['DM_MC_SONG_ADDED_UPS'], $mc_config['chart_ups_points'], $config['points_name']) . '<br />' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $redirect_url . '">', '</a>'));
				}
				else
				{
					meta_refresh(3, $redirect_url);
					trigger_error($user->lang['DM_MC_SONG_ADDED'] . '<br />' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $redirect_url . '">', '</a>'));
				}
			}
			else
			{
				meta_refresh(3, $redirect_url);
				trigger_error($user->lang['DM_MC_SONG_ADDED'] . '<br />' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $redirect_url . '">', '</a>'));
			}

			add_log('user', $u_id, 'LOG_USER_ADDED_SONG', $data['chart_song_name']);
		}

		if (file_exists($phpbb_root_path . 'dm_video/index.php'))
		{
			$template->assign_vars(array(
				'S_DM_VIDEO'	=> true,
			));
		}

		if ($mc_config['required_1'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_1'	=> true,
			));
		}

		if ($mc_config['required_2'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_2'	=> true,
			));
		}

		if ($mc_config['required_3'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_3'	=> true,
			));
		}

		if ($mc_config['required_4'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_4'	=> true,
			));
		}

		if ($mc_config['required_5'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_5'	=> true,
			));
		}

		// Start assigning vars for main posting page ...
		$template->assign_vars(array(
			'S_ADD_SONG'		=> true,
			'S_FORM_ENCTYPE'	=> ' enctype="multipart/form-data"',

			'CHART_SONG_NAME'	=> request_var('chart_song_name', '', true),
			'CHART_ARTIST'		=> request_var('chart_artist', '', true),
			'CHART_ALBUM'		=> request_var('chart_album', '', true),
			'CHART_YEAR'		=> request_var('chart_year', '', true),
			'CHART_PICTURE'		=> request_var('chart_picture', '', true),
			'CHART_WEBSITE'		=> request_var('chart_website', '', true),
			'CHART_VIDEO'		=> request_var('chart_video', '', true),
			'CHART_VIDEO_NO'	=> request_var('chart_video_no', 0),
		));

	break;

	case 'edit':

		$chart_id = request_var('id', 0);

		$sql = 'SELECT *
			FROM ' . DM_MUSIC_CHARTS_TABLE . '
			WHERE chart_id = ' . (int) $chart_id;
		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$template_html = 'dm_music_charts/dm_music_charts.html';

		add_form_key('dm_charts');

		if (!$auth->acl_get('u_dm_mc_edit', 'a_dm_mc_manage'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		if ($submit)
		{
			if (!check_form_key('dm_charts'))
			{
				trigger_error($user->lang['FORM_INVALID']);
			}

			$data['chart_song_name']	= utf8_normalize_nfc(request_var('chart_song_name', '', true));
			$data['chart_artist']		= utf8_normalize_nfc(request_var('chart_artist', '', true));
			$data['chart_album'] 		= utf8_normalize_nfc(request_var('chart_album', '', true));
			$data['chart_year']			= utf8_normalize_nfc(request_var('chart_year', '', true));
			$data['chart_picture']		= utf8_normalize_nfc(request_var('chart_picture', '', true));
			$data['chart_website']		= utf8_normalize_nfc(request_var('chart_website', '', true));
			$data['chart_video']		= utf8_normalize_nfc(request_var('chart_video', '', true));
			$data['chart_video_no']		= utf8_normalize_nfc(request_var('chart_video_no', 0));
			$data['chart_user_points']	= utf8_normalize_nfc(request_var('chart_user_points', 0));

			$back_link = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=edit");
			$back_link_overview = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");

			if (empty($data['chart_song_name']) && empty($data['chart_artist']))
			{
				trigger_error($user->lang['DM_MC_FIELDS_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<br /><br /><a href="' . $back_link . '">', '</a>') . ' | ' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $back_link_overview . '">', '</a>'));
			}

			if (empty($data['chart_song_name']))
			{
				trigger_error($user->lang['DM_MC_TITLE_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $back_link . '">', '</a>'));
			}

			if (empty($data['chart_artist']))
			{
				trigger_error($user->lang['DM_MC_ARTIST_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="' . $back_link . '">', '</a>'));
			}

			if ($mc_config['required_1'] == 1 && empty($data['chart_album']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_ALBUM_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_2'] == 1 && empty($data['chart_picture']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_ALBUMCOVER_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_3'] == 1 && empty($data['chart_year']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_YEAR_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			
			if ($mc_config['required_4'] == 1 && empty($data['chart_website']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_WEBSITE_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			if ($mc_config['required_5'] == 1 && empty($data['chart_video']))
			{
				trigger_error($user->lang['DM_MC_REQUIRED_VIDEO_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the link to the album cover starts with http://
			if ((($mc_config['required_2'] == 0 && !empty($data['chart_picture'])) && (stripos($data['chart_picture'], 'http://') === false)) || (($mc_config['required_2'] == 1 && !empty($data['chart_picture'])) && (stripos($data['chart_picture'], 'http://') === false)))
			{
				trigger_error($user->lang['DM_MC_COVER_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the publishing year is between 1900 and 2099
			if ((($mc_config['required_3'] == 0 && !empty($data['chart_year'])) && ($data['chart_year'] < 1900 || $data['chart_year'] >= 2100)) || (($mc_config['required_3'] == 1 && !empty($data['chart_year'])) && ($data['chart_year'] < 1900 || $data['chart_year'] >= 2100)))
			{
				trigger_error($user->lang['DM_MC_YEAR_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the link to the website starts with http://
			if ((($mc_config['required_4'] == 0 && !empty($data['chart_website'])) && (stripos($data['chart_website'], 'http://') === false)) || (($mc_config['required_4'] == 1 && !empty($data['chart_website'])) && (stripos($data['chart_website'], 'http://') === false)))
			{
				trigger_error($user->lang['DM_MC_WEBSITE_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check, if the embedded code is entered correctly
			$search_string = $data['chart_video'];
			$find_string_1 = 'object';
			$find_string_2 = 'iframe';
			$search_result_1 = stripos($search_string, $find_string_1);
			$search_result_2 = stripos($search_string, $find_string_2);

			if ((($mc_config['required_5'] == 0 && !empty($data['chart_video']) && $search_result_1 === false) || ($mc_config['required_5'] == 1 && !empty($data['chart_video']) && $search_result_1 === false)) && (($mc_config['required_5'] == 0 && !empty($data['chart_video']) && $search_result_2 === false) || ($mc_config['required_5'] == 1 && !empty($data['chart_video']) && $search_result_2 === false)))
			{
				trigger_error($user->lang['DM_MC_EMBED_FORMAT_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
			}

			// Check if the video ID from DM Video really exists
			if ($data['chart_video_no'] > 0)
			{
				$sql = 'SELECT video_id
					FROM ' . DM_VIDEO_TABLE . '
					WHERE video_id = ' . $data['chart_video_no'];
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($user->lang['DM_MC_VIDEO_EXIST_ERROR'] . sprintf($user->lang['DM_MC_BACKLINK_ADD'],'<a href="#" onclick="history.go(-1);return false;">', '</a>'));
				}
			}

			// Create array for editing charts
			$sql_ary = array(
				'chart_song_name'	=> $data['chart_song_name'],
				'chart_artist'		=> $data['chart_artist'],
				'chart_album'		=> $data['chart_album'],
				'chart_year'		=> $data['chart_year'],
				'chart_picture'		=> $data['chart_picture'],
				'chart_website'		=> $data['chart_website'],
				'chart_video'		=> $data['chart_video'],
				'chart_video_no'	=> $data['chart_video_no'],
			);

			$db->sql_query('UPDATE ' . DM_MUSIC_CHARTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' WHERE chart_id = ' . (int) $chart_id);

			$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");

			meta_refresh(3, $redirect_url);
			trigger_error(sprintf($user->lang['DM_MC_SONG_EDIT_SUCCESS'], $data['chart_song_name']) . '<br /><br />' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $redirect_url . '">', '</a>'));

			add_log('user', $u_id, 'LOG_USER_EDITED_SONG', $data['chart_song_name']);
			$cache->destroy('sql', DM_MUSIC_CHARTS_TABLE);
		}

		if (file_exists($phpbb_root_path . 'dm_video/index.php'))
		{
			$template->assign_vars(array(
				'S_DM_VIDEO'	=> true,
			));
		}

		if ($mc_config['required_1'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_1'	=> true,
			));
		}

		if ($mc_config['required_2'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_2'	=> true,
			));
		}

		if ($mc_config['required_3'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_3'	=> true,
			));
		}

		if ($mc_config['required_4'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_4'	=> true,
			));
		}

		if ($mc_config['required_5'] == 1)
		{
			$template->assign_vars(array(
				'S_REQ_5'	=> true,
			));
		}

		// Start assigning vars for main posting page ...
		$template->assign_vars(array(
			'S_EDIT_SONG'		=> true,
			'S_FORM_ENCTYPE'	=> ' enctype="multipart/form-data"',

			'CHART_SONG_NAME'	=> $data['chart_song_name'],
			'CHART_ARTIST'		=> $data['chart_artist'],
			'CHART_ALBUM'		=> $data['chart_album'],
			'CHART_YEAR'		=> $data['chart_year'],
			'CHART_PICTURE'		=> $data['chart_picture'],
			'CHART_WEBSITE'		=> $data['chart_website'],
			'CHART_VIDEO'		=> $data['chart_video'],
			'CHART_VIDEO_NO'	=> $data['chart_video_no'],
		));
	break;

	case 'delete':
		$delete_id	= request_var('id', 0);

		$s_hidden_fields = build_hidden_fields(array(
			'id'	=> (int) $delete_id,
			'mode'	=> 'delete')
		);

		if (confirm_box(true))
		{
			// Delete points, if UPS exists
			if (defined('IN_ULTIMATE_POINTS'))
			{
				$sql = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_TABLE . '
					WHERE chart_id = ' . $delete_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$poster_id = $row['chart_poster_id'];
				$title = $row['chart_song_name'];
				$delete_points = $row['chart_user_points'];
				$db->sql_freeresult($result);

				// Substract the points from the user account
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_points = user_points - ' . $delete_points . '
					WHERE user_id = "' . $poster_id . '"';
				$db->sql_query($sql);
			}


			$sql = 'DELETE
				FROM ' . DM_MUSIC_CHARTS_TABLE . '
				WHERE chart_id = ' . (int) $delete_id;
			$db->sql_query($sql);

			add_log('admin', 'LOG_ADMIN_CHART_DELETED', $title);
			$cache->destroy('sql', DM_MUSIC_CHARTS_TABLE);

			$sql = 'DELETE FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . ' WHERE vote_chart_id = ' . (int) $delete_id;
			$db->sql_query($sql);
			$cache->destroy('sql', DM_MUSIC_CHARTS_VOTERS_TABLE);

			$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx");
			meta_refresh(3, $redirect_url);
			trigger_error(sprintf($user->lang['DM_MC_DELETE_SUCCESS'], $title) . '<br /><br />' . sprintf($user->lang['DM_MC_BACKLINK'],'<a href="' . $redirect_url . '">', '</a>'));
		}
		else
		{
			if (isset($_POST['cancel']))
			{
				redirect(append_sid("{$phpbb_root_path}dm_music_charts.$phpEx"));
			}
			else
			{
				if (defined('IN_ULTIMATE_POINTS'))
				{
					confirm_box(false, sprintf($user->lang['DM_MC_DELETE_SONG_UPS'], $config['points_name']), build_hidden_fields(array(
						'id'		=> $delete_id,
						'action'	=> 'delete',
						))
					);
				}
				else
				{
					confirm_box(false, $user->lang['DM_MC_DELETE_SONG_REGULAR'], build_hidden_fields(array(
						'id'		=> $delete_id,
						'action'	=> 'delete',
						))
					);
				}
			}
		}
	break;

	case 'winners':

		$template_html = 'dm_music_charts/dm_music_charts.html';

		$sql = 'SELECT c.*, u.user_id, u.username, u.user_colour
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			LEFT JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			WHERE chart_hot > 0
			ORDER BY chart_hot ASC';
		$result = $db->sql_query_limit($sql, $mc_config['winners_per_page']);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['chart_hot'] == 1)
			{
				$rank_img = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/1st.gif" alt="" title="' . $user->lang['DM_MC_FIRST'] . '" />';
				$win = sprintf($user->lang['DM_MC_WON_VALUE'], $mc_config['chart_1st_place'], $config['points_name']);
			}
			else if ($row['chart_hot'] == 2)
			{
				$rank_img = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/2nd.gif" alt="" title="' . $user->lang['DM_MC_SECOND'] . '" />';
				$win = sprintf($user->lang['DM_MC_WON_VALUE'], $mc_config['chart_2nd_place'], $config['points_name']);
			}
			else  if ($row['chart_hot'] == 3)
			{
				$rank_img = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/3rd.gif" alt="" title="' . $user->lang['DM_MC_THIRD'] . '" />';
				$win = sprintf($user->lang['DM_MC_WON_VALUE'], $mc_config['chart_3rd_place'], $config['points_name']);
			}
			else
			{
				$rank_img = $row['chart_hot'];
				$win = '';
			}

			$template->assign_block_vars('winners', array(
				'RANK' 			=> $rank_img,
				'USER' 			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'SONG' 			=> $row['chart_song_name'],
				'ARTIST'		=> $row['chart_artist'],
				'WIN'			=> $win,
				'U_SHOW_VIDEO'	=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=showvideo&amp;v=" . $row['chart_id']),
			));
		}
		$db->sql_freeresult($result);
		
		// Last bonus winner
		if ($mc_config['last_voters_winner_id'] > 0)
		{
			$sql = 'SELECT user_id, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $mc_config['last_voters_winner_id'];
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$template->assign_vars(array(
				'S_BONUS_WINNER'	=> ($mc_config['last_voters_winner_id'] > 0) ? true : false,
				'BONUS_WINNER'		=> sprintf($user->lang['DM_MC_BONUS_WINNER'], get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']), $mc_config['voters_points'], $config['points_name']),
			));
		}

		if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'])
		{
			$template->assign_vars(array(
				'S_UPS'	=> true,
			));
		}

		$template->assign_vars(array(
			'S_LAST_WINNERS'	=> true,
			'S_ADD_SONG_LIST'	=> $auth->acl_get('u_dm_mc_add'),
			'MC_TOP_XX'			=> sprintf($user->lang['DM_MC_TOP_TEN'], $mc_config['chart_num_top']),
			'MC_NEWEST_XX'		=> sprintf($user->lang['DM_MC_NEWEST_XX'], $mc_config['chart_num_last']),
			'MC_LIST_ALL'		=> sprintf($user->lang['DM_MC_SHOW_ALL'], $total_charts),
			'U_LIST_TOP'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list"),
			'U_LIST_ALL'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", 'mode=list_all'),
			'U_LIST_NEWEST'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list_newest"),
			'U_ADD_SONG' 		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=add"),
		));
	break;
}

// Load charts template
$template->set_filenames(array(
	'body' => $template_html,
));

page_footer();

?>