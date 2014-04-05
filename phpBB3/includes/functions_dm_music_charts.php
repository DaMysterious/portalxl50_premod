<?php
/**
*
* @package - phpbb3 DM Music Charts
* @version $Id: functions_dm_music_charts.php 144 2011-01-30 03:01:22Z femu $
* @copyright (c) 2010 femu - http://die-muellers.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/*
 * @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}


/**
* Set Charts config config values. Creates missing config entry.
*/
function check_charts_reset()
{
	global $cache, $db, $template, $phpbb_root_path, $phpEx, $user, $config, $auth;

	// Get config values
	$sql_array = array(
		'SELECT'    => 'config_name, config_value',

		'FROM'      => array(
			DM_MUSIC_CHARTS_CONFIG_TABLE => 'vc'
		),
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$mc_config[$row['config_name']] = $row['config_value'];
	}
	$db->sql_freeresult($result);
	
	// Clear the cache
	$cache->destroy('config');

	// Check if current time is higher than the reset time
	$new_chart_start_time = $config['chart_start_time'] + $config['chart_period'];

	// Update chart start time with old next reset time
	$sql = 'UPDATE ' . CONFIG_TABLE . "
		SET config_value = " . $new_chart_start_time . "
		WHERE config_name = 'chart_start_time'";
	$db->sql_query($sql);

	// Check if there are any voters - needed to decide, if we have winners in this period
	$sql = 'SELECT COUNT(vote_id) AS total_votes
		FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$total_votes = $row['total_votes'];
	$db->sql_freeresult($result);

	if (empty($total_votes))
	{
			$template->assign_vars(array(
				'S_NO_VOTES'	=> true,
			));
	}

	// Select the current rating and users
	$sql = 'SELECT u.user_id, u.username, u.user_colour, c.*
		FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
		LEFT JOIN ' . USERS_TABLE . ' u
			ON u.user_id = c.chart_poster_id
		ORDER BY c.chart_hot DESC, c.chart_not ASC, c.chart_last_pos ' . $default_sort;
	$result = $db->sql_query($sql);
	$rowset = $db->sql_fetchrowset($result);
	$chart_count = count($rowset);

	for ($i = 0; $i < $chart_count; $i++)
	{
		if ($i+1 < $rowset[$i]['chart_best_pos'] || $rowset[$i]['chart_best_pos'] == 0)
		{
			$add = ', chart_best_pos = ' . ($i+1);
		}
		else
		{
			$add = '';
		}

		$sql = 'UPDATE ' . DM_MUSIC_CHARTS_TABLE . '
			SET chart_last_pos = ' . ($i+1) . $add . '
			WHERE chart_id = ' . $rowset[$i]['chart_id'];
		$db->sql_query($sql);

	}
	$db->sql_freeresult($result);

	// Select a random voter to get a bonus, if UPS is enabled and active
	if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'] && $mc_config['voters_points'] > 0)
	{
		$user->add_lang('mods/dm_music_charts');

		if (!function_exists('add_points'))
		{
			includes($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
		}

		switch ($db->sql_layer)
		{
			case 'postgres':
				$order_by = 'RANDOM()';
			break;

			case 'mssql':
			case 'mssql_odbc':
				$order_by = 'NEWID()';
			break;

			default:
				$order_by = 'RAND()';
			break;
		}

		$sql = 'SELECT u.user_id, u.username, u.user_colour
			FROM ' . DM_MUSIC_CHARTS_VOTERS_TABLE . ' v
			LEFT JOIN ' . USERS_TABLE . ' u
				ON v.vote_user_id = u.user_id
			ORDER BY ' . $order_by;
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);

		if ($row)
		{
			$voters_winner_id = $row['user_id'];
			$voters_winner_name = $row['username'];
			$voters_winner_colour = $row['user_colour'];

			// Add the points
			add_points((int) $voters_winner_id, $mc_config['voters_points']);

			// Update last winner id
			$sql = 'UPDATE ' . DM_MUSIC_CHARTS_CONFIG_TABLE . '
				SET config_value = "' . $voters_winner_id . '"
				WHERE config_name = "last_voters_winner_id"';
			$db->sql_query($sql);

			// Inform the lucky winner by PM, if PM is enabled
			if ($mc_config['pm_enable'] == 1)
			{
				include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

				$pm_user = $mc_config['pm_user'];

				$my_subject_voters = utf8_normalize_nfc($user->lang['DM_MC_PM_VOTERS_SUBJECT']);
				$my_text_voters = utf8_normalize_nfc(sprintf($user->lang['DM_MC_PM_VOTERS_MESSAGE'], $voters_winner_name, $mc_config['voters_points'], $config['points_name']));

				$poll = $uid = $bitfield = $options = '';
				generate_text_for_storage($my_subject_voters, $uid, $bitfield, $options, false, false, false);
				generate_text_for_storage($my_text_voters, $uid, $bitfield, $options, true, true, true);

				$data = array(
				'address_list'		=> array ('u' => array($voters_winner_id => 'to')),
				'from_user_id' 		=> $pm_user,
				'from_username' 	=> 'Administration',
				'icon_id'			=> 0,
				'from_user_ip'		=> '',
				'enable_bbcode' 	=> true,
				'enable_smilies' 	=> true,
				'enable_urls' 		=> true,
				'enable_sig' 		=> true,

				'message' 			=> $my_text_voters,
				'bbcode_bitfield' 	=> $bitfield,
				'bbcode_uid' 		=> $uid,
				);

				submit_pm('post', $my_subject_voters, $data, false);
			}
		}
	}

	// Give points to the first three users, if UPS is installed and points are enabled
	if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'] && $total_votes > 0)
	{
		$user->add_lang('mods/dm_music_charts');

		// Pickup the points
		$first = $mc_config['chart_1st_place'];
		$second = $mc_config['chart_2nd_place'];
		$third = $mc_config['chart_3rd_place'];

		// Find the three winners
		$sql = 'SELECT chart_last_pos, chart_poster_id
			FROM ' . DM_MUSIC_CHARTS_TABLE . '
			ORDER BY chart_last_pos ASC';
		$result = $db->sql_query_limit($sql, 3);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($first > 0 && $row['chart_last_pos'] == 1)
			{
				if (!function_exists('add_points'))
				{
					includes($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
				}

				// Add the points
				add_points((int) $row['chart_poster_id'], $first);
			}

			if ($second > 0 && $row['chart_last_pos'] == 2)
			{
				if (!function_exists('add_points'))
				{
					includes($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
				}

				// Add the points
				add_points((int) $row['chart_poster_id'], $second);
			}

			if ($third > 0 && $row['chart_last_pos'] == 3)
			{
				if (!function_exists('add_points'))
				{
					includes($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
				}

				// Add the points
				add_points((int) $row['chart_poster_id'], $third);
			}
		}
		$db->sql_freeresult($result);
	}

	// Send PM to the winners, if enabled
	if ($mc_config['pm_enable'] == 1 && $total_votes > 0)
	{
		include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

		// Find the three winners
		$sql = 'SELECT c.*, u.user_id, u.username, u.user_colour
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			LEFT JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			WHERE chart_last_pos > 0
			ORDER BY chart_last_pos ASC';
		$result = $db->sql_query_limit($sql, 3);

		while ($row = $db->sql_fetchrow($result))
		{
			$pm_user = $mc_config['pm_user'];

			if (!defined('IN_ULTIMATE_POINTS') || (defined('IN_ULTIMATE_POINTS') && $config['points_enable'] == 0))
			{
				$my_text = utf8_normalize_nfc(sprintf($user->lang['DM_MC_PM_MESSAGE'], $row['username'], $row['chart_last_pos'], $row['chart_song_name'], $row['chart_artist']));
			}
			else
			{
				if ($first > 0 && $row['chart_last_pos'] == 1)
				{
					$price = $first;
				}

				if ($second > 0 && $row['chart_last_pos'] == 2)
				{
					$price = $second;
				}

				if ($third > 0 && $row['chart_last_pos'] == 3)
				{
					$price = $third;
				}

				$my_text = utf8_normalize_nfc(sprintf($user->lang['DM_MC_PM_MESSAGE_UPS'], $row['username'], $row['chart_last_pos'], $row['chart_song_name'], $row['chart_artist'], $price, $config['points_name']));
			}

			$my_subject = utf8_normalize_nfc(sprintf($user->lang['DM_MC_PM_SUBJECT'], $row['chart_last_pos']));

			$poll = $uid = $bitfield = $options = '';
			generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
			generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

			$data = array(
			'address_list'		=> array ('u' => array($row['user_id'] => 'to')),
			'from_user_id' 		=> $pm_user,
			'from_username' 	=> 'Administration',
			'icon_id'			=> 0,
			'from_user_ip'		=> '',
			'enable_bbcode' 	=> true,
			'enable_smilies' 	=> true,
			'enable_urls' 		=> true,
			'enable_sig' 		=> true,

			'message' 			=> $my_text,
			'bbcode_bitfield' 	=> $bitfield,
			'bbcode_uid' 		=> $uid,
			);

			submit_pm('post', $my_subject, $data, false);
		}
	}

	// Reset the votes
	$sql = 'UPDATE ' . DM_MUSIC_CHARTS_TABLE . '
		SET chart_hot = 0
		WHERE chart_hot > 0';
	$db->sql_query($sql);

	$sql = 'UPDATE ' . DM_MUSIC_CHARTS_TABLE . '
		SET chart_not = 0
		WHERE chart_not > 0';
	$db->sql_query($sql);

	// Empty the voters table
	$sql = 'TRUNCATE ' . DM_MUSIC_CHARTS_VOTERS_TABLE;
	$db->sql_query($sql);

	// Reset the check 1 value
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET dm_mc_check_1 = 0
		WHERE dm_mc_check_1 > 0';
	$db->sql_query($sql);

	// Reset the check 2 value
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET dm_mc_check_2 = 0
		WHERE dm_mc_check_2 > 0';
	$db->sql_query($sql);

	// Enable board after the update again
	$sql = 'UPDATE ' . CONFIG_TABLE . '
		SET config_value = 0
		WHERE config_name = "board_disable"';
	$db->sql_query($sql);

	// Add a log entry, when the job is done
	add_log('admin', 'LOG_ADMIN_CHART_RESET');
}

function check_charts_voted()
{
	global $cache, $db, $template, $phpbb_root_path, $phpEx, $user, $config, $auth;

	// Get config values
	$sql_array = array(
		'SELECT'    => 'config_name, config_value',

		'FROM'      => array(
			DM_MUSIC_CHARTS_CONFIG_TABLE => 'vc'
		),
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$mc_config[$row['config_name']] = $row['config_value'];
	}
	$db->sql_freeresult($result);

	if ($mc_config['chart_check_1'] == 1 && $user->data['dm_mc_check_1'] == 0)
	{
		$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");

		// Last winners
		$sql = 'SELECT c.*, u.user_id, u.username, u.user_colour
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			LEFT JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			WHERE chart_last_pos > 0
			ORDER BY chart_last_pos ASC';
		$result = $db->sql_query_limit($sql, 3);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['chart_last_pos'] == 1)
			{
				$rank_img = '<img style="float: center;" src="' . $phpbb_root_path . 'images/dm_music_charts/1st.gif" alt="" title="' . $user->lang['DM_MC_FIRST'] . '" />';
				$win = sprintf($user->lang['DM_MC_WON_VALUE'], $mc_config['chart_1st_place'], $config['points_name']);
			}
			else if ($row['chart_last_pos'] == 2)
			{
				$rank_img = '<img style="float: center;" src="' . $phpbb_root_path . 'images/dm_music_charts/2nd.gif" alt="" title="' . $user->lang['DM_MC_SECOND'] . '" />';
				$win = sprintf($user->lang['DM_MC_WON_VALUE'], $mc_config['chart_2nd_place'], $config['points_name']);
			}
			else
			{
				$rank_img = '<img style="float: center;" src="' . $phpbb_root_path . 'images/dm_music_charts/3rd.gif" alt="" title="' . $user->lang['DM_MC_THIRD'] . '" />';
				$win = sprintf($user->lang['DM_MC_WON_VALUE'], $mc_config['chart_3rd_place'], $config['points_name']);
			}

			if (empty($row['chart_picture']))
			{
				$img = $phpbb_root_path . 'images/dm_music_charts/icon_charts_video.png';
			}
			else
			{
				$img = $row['chart_picture'];
			}

			$template->assign_block_vars('winners', array(
				'RANK'		=> $rank_img,
				'USER'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'SONG'		=> $row['chart_song_name'],
				'ARTIST'	=> $row['chart_artist'],
				'VIDEO'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=showvideo&amp;v=" . $row['chart_id']),
				'IMG'		=> '<img title="' . $user->lang['DM_MC_CLICK_VIDEO'] . '" alt="' . $user->lang['DM_MC_CLICK_VIDEO'] . '" height="50px" src="' . $img . '" />',
				'WIN'		=> $win,
				'FROM'		=> $user->lang['DM_MC_FROM'],
			));
		}

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

		// Check, if Highslide JS is installed
		if (file_exists($phpbb_root_path . 'highslide/highslide-full.js'))
		{
			$template->assign_vars(array(
				'S_HIGHSLIDE'   => true,
			));
		}      

		if ($row)
		{
			$template->assign_vars(array(
				'S_LAST_WINNERS'	=> true,
			));
		}

		$template->assign_vars(array(
			'S_DM_MC_CHECK_FIRST' => true,
			'PERIOD'    => $user->format_date($config['chart_start_time']),         
			'VOTE'   => sprintf($user->lang['DM_MC_VOTE_CHECK_FIRST'], $user->data['username']) . sprintf($user->lang['DM_MC_VOTE_CHECK_LINK'], '<br /><br /><a href="' . $redirect_url . '">', '</a>'),
		));
	}
	else if ($mc_config['chart_check_1'] == 1 && $mc_config['chart_check_2'] == 1 && $user->data['dm_mc_check_1'] == 1 && $user->data['dm_mc_check_2'] == 0 && (time() > ($config['chart_start_time'] + $config['chart_period'] - ($mc_config['chart_check_time'] * 3600))))
	{
		$redirect_url = append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=list");

		// List xx newest chart songs
		$num_songs = 0;
		$sql = 'SELECT u.user_id, u.username, u.user_colour, c.*
			FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
			LEFT JOIN ' . USERS_TABLE . ' u
				ON u.user_id = c.chart_poster_id
			WHERE c.chart_add_time > ' . $config['chart_start_time'] . '
			ORDER BY c.chart_id DESC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$number_songs = count($row);
			$num_songs++;
			
			if (empty($row['chart_picture']))
			{
				$img = $phpbb_root_path . 'images/dm_music_charts/icon_charts_video.png';
			}
			else
			{
				$img = $row['chart_picture'];
			}

			$template->assign_block_vars('newest', array(
				'USER'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'SONG'		=> $row['chart_song_name'],
				'ARTIST'	=> $row['chart_artist'],
				'VIDEO'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx", "mode=showvideo&amp;v=" . $row['chart_id']),
				'IMG'		=> '<img title="' . $user->lang['DM_MC_CLICK_VIDEO'] . '" alt="' . $user->lang['DM_MC_CLICK_VIDEO'] . '" height="50px" src="' . $img . '" />',
				'FROM'		=> $user->lang['DM_MC_FROM'],
			));

			$template->assign_vars(array(
				'S_NEWEST'	=> true,
			));
		}
		$db->sql_freeresult($result);

		for ($j = 0; $j < (4 - ($num_songs % 4)); $j++)
		{
			$template->assign_block_vars('newest', array());
		}

		// Check, if Highslide JS is installed
		if (file_exists($phpbb_root_path . 'highslide/highslide-full.js'))
		{
			$template->assign_vars(array(
				'S_HIGHSLIDE'   => true,
			));
		}   

		$template->assign_vars(array(
			'S_DM_MC_CHECK_SECOND' => true,
			'REMINDER' => sprintf($user->lang['DM_MC_VOTE_CHECK_SECOND'], $user->data['username']) . sprintf($user->lang['DM_MC_VOTE_CHECK_LINK'], '<br /><br /><a href="' . $redirect_url . '">', '</a>'),
		));
	}
}

// Function to announce new songs
function create_announcement($song_subject, $song_msg, $forum_id)
{
	global $db, $phpbb_root_path, $phpEx;

	if (!function_exists('submit_post'))
	{
		include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
	}

	$subject 	= utf8_normalize_nfc((string) $song_subject);
	$text 		= utf8_normalize_nfc((string) $song_msg);

	// Do not try to post message if subject or text is empty
	if (empty($subject) || empty($text))
	{
		return;
	}

	// variables to hold the parameters for submit_post
	$poll = $uid = $bitfield = $options = '';

	generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
	generate_text_for_storage($text, $uid, $bitfield, $options, true, true, true);

	$data = array(
		'forum_id'        	=> (int)$forum_id,
		'icon_id'       	=> false,

		'enable_bbcode'     => true,
		'enable_smilies'    => true,
		'enable_urls'       => true,
		'enable_sig'        => true,

		'message'        	=> (string) $text,
		'message_md5'    	=> (string) md5($text),

		'bbcode_bitfield'   => (string) $bitfield,
		'bbcode_uid'        => (string) $uid,

		'post_edit_locked'	=> 0,
		'topic_title'       => (string) $subject,
		'notify_set'        => false,
		'notify'            => false,
		'post_time'         => 0,
		'forum_name'        => '',
		'enable_indexing'   => true,
	);

	submit_post('post', (string) $subject, '', POST_NORMAL, $poll, $data);
}


?>