<?php
/**
*
* @package arcade
* @version $Id: stats.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Set the link to the main statistics page in the nav
$template->assign_var('S_IN_STATS', true);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'		=> $user->lang['ARCADE_STATS'],
	'U_VIEW_FORUM'		=> $arcade->url("mode=stats")));

$page_title = '';

// Check game id
if ($game_id)
{
	// If this returns false the users does not have permission to view the category/game
	if (!$arcade->get_game_field($game_id))
	{
		trigger_error($user->lang['NO_GAME_ID'] . $arcade->back_link());
	}
}

if (($game_id && $user_id) || ($user_id))
{
	$highscores = request_var('hs', 0);
	if ($highscores && !$game_id)
	{
		$highscores = ' AND g.game_highuser = ' . (int) $user_id;
	}
	else
	{
		$highscores = '';
	}

	$template->assign_var('S_USER_STATS', true);

	$game_sql = '';
	if ($game_id)
	{
		$game_sql = ' AND s.game_id = ' . (int) $game_id;
	}

	$sql_sort = 'g.game_name ASC';
	$sql_cat_limit = '';
	if (!$game_id)
	{
		$sort_cat	= request_var('st', 0);
		$sort_key	= request_var('sk', 'g');
		$sort_dir	= request_var('sd', 'a');

		$limit_cat = $arcade->get_user_categories($user_id);
		$sort_by_text = array('g' => $user->lang['ARCADE_GAME_NAME'], 's' => $user->lang['ARCADE_SCORE'], 'd' => $user->lang['ARCADE_DATE'], 'p' => $user->lang['ARCADE_CHAMPION_PLAYED'], 'm' => $user->lang['ARCADE_STATS_TOTAL_TIME'], 't' => $user->lang['ARCADE_STATS_AVG']);
		$sort_by_sql = array('g' => 'game_name_clean', 's' => 'score', 'd' => 'score_date', 'p' => 'total_plays', 'm' => 'total_time', 't' => 'total_time / total_plays');

		$s_limit_cat = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_cat, $sort_by_text, $sort_cat, $sort_key, $sort_dir, $s_limit_cat, $s_sort_key, $s_sort_dir, $u_sort_param);

		$sql_cat_limit = ($sort_cat) ? ' AND g.cat_id = ' . (int) $sort_cat : '';
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
	}

	$sql_array = array(
		'SELECT'	=> 'COUNT(s.game_id) as total_games',

		'FROM'		=> array(
			ARCADE_SCORES_TABLE	=> 's',
		),

		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
				'ON'	=> 's.game_id = g.game_id'
			),
		),

		'WHERE'		=> 's.user_id = ' . (int) $user_id . $sql_cat_limit . $game_sql . $highscores . ' AND ' . $db->sql_in_set('g.cat_id', $arcade->get_permissions('c_view'), false, true),
	);

	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	$total_games = $db->sql_fetchfield('total_games');
	$db->sql_freeresult($result);

	if (!$total_games)
	{
		if (!$game_id)
		{
			$message = ($highscores) ? 'ARCADE_NO_HIGHSCORES' : 'ARCADE_PLAYED_NO_GAMES';
			trigger_error($user->lang[$message] . $arcade->back_link());
		}
		else
		{
			trigger_error($user->lang['ARCADE_PLAYED_NO_GAME'] . $arcade->back_link());
		}
	}

	// Quick jump of users favorite games...
	$user_fav_data = $arcade->get_fav_data($user_id);
	foreach ($user_fav_data as $game)
	{
		$template->assign_block_vars('userfav', array(
			'GAME_ID'	=> $game['game_id'],
			'GAME_NAME'	=> $game['game_name']
		));
	}
	// Quick jump of users favorite games...

	if ($config['load_onlinetrack'])
	{
		$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
			FROM ' . SESSIONS_TABLE . '
			WHERE session_user_id = ' . (int) $user_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$session_time = (isset($row['session_time'])) ? $row['session_time'] : 0;
		$session_viewonline = (isset($row['session_viewonline'])) ? $row['session_viewonline'] :	0;
		unset($row);

		$update_time = $config['load_online_time'] * 60;
		$online = (time() - $update_time < $session_time && ((isset($session_viewonline) && $session_viewonline) || $auth->acl_get('u_viewonline'))) ? true : false;
	}
	else
	{
		$online = false;
	}

	$sql_array = array(
		'SELECT'	=> 'g.game_name, g.game_name_clean, g.game_highuser, g.game_id, g.game_image, g.game_scorevar, g.cat_id, s.*, p.total_time, p.total_plays, c.cat_id, c.cat_name, c.parent_id, c.cat_parents, c.left_id, c.right_id, c.cat_desc, c.cat_desc_options, c.cat_desc_uid, c.cat_desc_bitfield',

		'FROM'		=> array(
			ARCADE_SCORES_TABLE	=> 's',
		),

		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
				'ON'	=> 's.game_id = g.game_id'
			),
			array(
				'FROM'	=> array(ARCADE_PLAYS_TABLE => 'p'),
				'ON'	=> 's.game_id = p.game_id AND s.user_id = p.user_id'
			),
			array(
				'FROM' => array(ARCADE_CATS_TABLE => 'c'),
				'ON'	=> 'g.cat_id = c.cat_id'
			),
		),

		'ORDER_BY'	=> $sql_sort,

		'WHERE'		=> 's.user_id = ' . (int) $user_id . $sql_cat_limit . $game_sql . $highscores . ' AND ' . $db->sql_in_set('g.cat_id', $arcade->get_permissions('c_view'), false, true),
	);

	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query_limit($sql, $arcade_config['stat_items_per_page'], $start);

	while ($row = $db->sql_fetchrow($result))
	{
		$game_name = $row['game_name'];
		// Generate the nav for the arcade if viewing a users game stats...
		if($game_id && $user_id)
		{
			generate_arcade_nav($row, true);
			$template->assign_var('GAME_NAME', $game_name);
		}

		$comment_display = generate_text_for_display($row['comment_text'], $row['comment_uid'], $row['comment_bitfield'], $row['comment_options']);

		$template->assign_block_vars('gamerow', array(
			'U_GAME_PLAY'			=> $arcade->url("mode=stats&amp;g=" . $row['game_id']),

			'GAME_NAME'				=> $game_name,
			'GAME_IMAGE'			=> ($row['game_image'] != '') ? $arcade->url("img=" . $row['game_image']) : '',
			'GAME_IMAGE_FIRST'		=> ($row['game_highuser'] == $user_id) ? $arcade->get_image('src', 'img', '1st.gif', '') : '' ,
			'SCORE'					=> $arcade->number_format($row['score']),
			'COMMENT'				=> ($comment_display != '') ? $comment_display : '&nbsp;',
			'DATE'					=> $user->format_date($row['score_date']),
			'TOTAL_PLAYS'			=> $arcade->number_format($row['total_plays']),
			'TOTAL_TIME'			=> $arcade->time_format($row['total_time']),
			'AVG_TIME'				=> $arcade->time_format($row['total_time']/$row['total_plays']),
		));
	}
	$db->sql_freeresult($result);

	// This will return the user rank, rank image, and avatar image
	// as well as the users total wins, total time, and games played...
	// Since $user_id is passed to this function it will return an array that has
	// the user info whose stats we are looking for stored.
	$userdata = $arcade->get_user_info($user_id);

	$s_user_fav_hidden_fields =  build_hidden_fields(array('mode' => 'play'));

	if ($_SID)
	{
		$s_user_fav_hidden_fields .= build_hidden_fields(array('sid' =>  $_SID));
	}

	$template->assign_vars(array(
		'S_ACTION_USER_FAV'			=> $arcade->url($arcade->gametop),
		'S_ARCADE_ACTION'			=> $arcade->url("mode=stats&amp;u={$user_id}" . ($highscores ? '&amp;hs=1' : '')),
		'S_USER_FAV_HIDDEN_FIELDS'	=> $s_user_fav_hidden_fields,
		'S_USER_STATS'				=> true,

		'S_SELECT_SORT_CAT'			=> (!$game_id) ? $s_limit_cat : false,
		'S_SELECT_SORT_DIR'			=> (!$game_id) ? $s_sort_dir : false,
		'S_SELECT_SORT_KEY'			=> (!$game_id) ? $s_sort_key : false,

		'L_ARCADE_USER_STATS_TYPE'	=> ($highscores) ? $user->lang['ARCADE_USER_SCORES'] : $user->lang['ARCADE_USER_HIGHSCORES'],
		'U_ARCADE_USER_STATS_TYPE'	=> $arcade->url("mode=stats&amp;u={$user_id}" . ($highscores ? '' : '&amp;hs=1')),

		'ONLINE_IMG'				=> (!$config['load_onlinetrack']) ? '' : (($online) ? $user->img('icon_user_online', 'ONLINE') : $user->img('icon_user_offline', 'OFFLINE')),
		'S_ONLINE'					=> ($config['load_onlinetrack'] && $online) ? true : false,
		'AVATAR'					=> $userdata['avatar'],
		'RANK_TITLE'				=> $userdata['rank_title'],
		'USER_COLOUR'				=> $userdata['user_colour'],
		'RANK_IMAGE'				=> $userdata['rank_image'],
		'RANK_IMAGE_SRC'			=> $userdata['rank_image_src'],
		'ARCADE_WINS'				=> $arcade->number_format($userdata['total_wins']),
		'ARCADE_PLAYS'				=> $arcade->number_format($userdata['total_plays']),
		'ARCADE_TIME'				=> $arcade->time_format($userdata['total_time']),
		'USERNAME'					=> ($game_id) ? $arcade->get_username_string('full', $userdata['user_id'], $userdata['username'], $userdata['user_colour']) : get_username_string('full', $userdata['user_id'], $userdata['username'], $userdata['user_colour']),
	));

	if (!$game_id)
	{
		$page_title = sprintf($user->lang['ARCADE_STATS_USER_GAME_PAGE_TITLE'], $userdata['username']);
		if ($total_games == 1)
		{
			$l_total_games = $total_games . ' ' . $user->lang['ARCADE_GAME'];
		}
		else if ($total_games > 1)
		{
			$l_total_games = $total_games . ' ' . $user->lang['ARCADE_GAMES'];
		}
		else
		{
			$l_total_games = false;
		}

		$template->assign_vars(array(
			'PAGE_NUMBER'	=> on_page($total_games, $arcade_config['stat_items_per_page'], $start),
			'TOTAL_GAMES'	=> $l_total_games,
			'PAGINATION'	=> generate_pagination($arcade->url("mode=stats&amp;u=$user_id" . (($highscores) ? '&amp;hs=1' : '') . "&amp;$u_sort_param"), $total_games, $arcade_config['stat_items_per_page'], $start),
			)
		);
	}
	else
	{
		$page_title = sprintf($user->lang['ARCADE_STATS_SCORE_PAGE_TITLE'],  $game_name, $userdata['username']);
	}

	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME'	=> $userdata['username'],
		'U_VIEW_FORUM'	=> get_username_string('profile', $userdata['user_id'], $userdata['username']),
	));
}
else if ($game_id && $game_id > 0)
{
	$template->assign_var('S_GAME_STATS', true);

	// Get game data for page and generate the nav
	$game_data = $arcade->get_game_data($game_id);
	if (!$game_data)
	{
		trigger_error($user->lang['NO_GAME_ID'] . $arcade->back_link());
	}
	generate_arcade_nav($game_data, true);

	$page_title = sprintf($user->lang['ARCADE_STATS_USER_GAME_PAGE_TITLE'], $game_data['game_name']);

	$sql_array = array(
		'SELECT'	=> 'COUNT(s.game_id) AS total_scores, SUM(p.total_plays) AS total_plays, SUM(p.total_time) AS total_time',

		'FROM'		=> array(
			ARCADE_SCORES_TABLE	=> 's',
		),

		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(ARCADE_PLAYS_TABLE => 'p'),
				'ON'	=> 's.game_id = p.game_id AND s.user_id = p.user_id'
			),
		),

		'WHERE'	=> 's.game_id = ' . (int) $game_data['game_id'],
	);

	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$game_data['total_scores'] = $row['total_scores'];
    $game_data['total_plays'] = $row['total_plays'];
    $game_data['total_time'] = $row['total_time'];

	if (!$game_data['total_scores'])
	{
		$game_link = $arcade->url("mode=play&amp;g={$game_data['game_id']}");
		trigger_error(sprintf($user->lang['ARCADE_GAME_NO_SCORES'], $game_data['game_name'], $game_link));
	}

	// Lets check if the game plays is out of sync...
	if ($game_data['game_plays'] != $game_data['total_plays'])
	{
		$db->sql_query('UPDATE ' . ARCADE_GAMES_TABLE . ' SET game_plays = ' . (int) $game_data['total_plays'] . ' WHERE game_id = ' . (int) $game_data['game_id']);
	}

	$score_order = ($game_data['game_scoretype'] == SCORETYPE_HIGH) ? 'DESC' : 'ASC';

	$sql_array = array(
		'SELECT'	=> 's.*, p.total_time, p.total_plays, u.user_id, u.user_colour, u.username, u.user_avatar_type, u.user_avatar, u.user_avatar_width, u.user_avatar_height',

		'FROM'		=> array(
			ARCADE_SCORES_TABLE	=> 's',
		),

		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(ARCADE_PLAYS_TABLE => 'p'),
				'ON'	=> 's.game_id = p.game_id AND s.user_id = p.user_id'
			),
			array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 's.user_id = u.user_id'
			),
		),

		'WHERE'		=> 's.game_id = ' . (int) $game_data['game_id'],

		'ORDER_BY'	=> 's.score ' . $score_order . ', s.score_date ASC',
	);

	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query_limit($sql, 1);
	$best_user = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$result = $db->sql_query_limit($sql, $arcade_config['stat_items_per_page'], $start);
	$position = ($start > 0 ) ? $start : 0;
	$actual_position = ($start > 0 ) ? $start : 0;
	$lastscore = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$actual_position++;
		if ($lastscore != $row['score'])
		{
			$position = $actual_position;
		}
		$lastscore = $row['score'];

		$comment_display = generate_text_for_display($row['comment_text'], $row['comment_uid'], $row['comment_bitfield'], $row['comment_options']);
		$template->assign_block_vars('scorerow', array(
			'POS'			=> $position,
			'USERNAME'		=> $arcade->get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'SCORE'			=> $arcade->number_format($row['score']),
			'COMMENT'		=> ($comment_display != '') ? $comment_display : '&nbsp;',
			'DATE'			=> $user->format_date($row['score_date']),
			'TOTAL_PLAYS'	=> $arcade->number_format($row['total_plays']),
			'TOTAL_TIME'	=> $arcade->time_format($row['total_time'])
		));
	}
	$db->sql_freeresult($result);

	$user_avatar = ($user->optionget('viewavatars')) ? true : false;

	if ($user_avatar)
	{
		if (!($user_avatar = get_user_avatar($best_user['user_avatar'], $best_user['user_avatar_type'], $best_user['user_avatar_width'], $best_user['user_avatar_height'], $best_user['username'])))
		{
			$user_avatar = '<img src="' . $arcade->get_image('src', 'img', 'noavatar.gif') . '" alt="' . $best_user['username'] . '" title="' . $best_user['username'] . '" />';
		}

		$user_avatar = ($auth_arcade->acl_get('u_viewstats') && $user_avatar) ? '<a href="' . $arcade->url("mode=stats&amp;u={$best_user['user_id']}") . '">' . $user_avatar . '</a>' : $user_avatar;
	}

	if ($game_data['total_scores'] == 1)
	{
		$l_total_scores = $game_data['total_scores'] . ' ' . $user->lang['ARCADE_SCORE'];
	}
	else if ($game_data['total_scores'] > 1)
	{
		$l_total_scores = $game_data['total_scores'] . ' ' . $user->lang['ARCADE_SCORES'];
	}
	else
	{
		$l_total_scores = false;
	}

	if ($config['load_onlinetrack'])
	{
		$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
			FROM ' . SESSIONS_TABLE . '
			WHERE session_user_id = ' . (int) $best_user['user_id'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$session_time = (isset($row['session_time'])) ? $row['session_time'] : 0;
		$session_viewonline = (isset($row['session_viewonline'])) ? $row['session_viewonline'] :	0;
		unset($row);

		$update_time = $config['load_online_time'] * 60;
		$online = (time() - $update_time < $session_time && ((isset($session_viewonline) && $session_viewonline) || $auth->acl_get('u_viewonline'))) ? true : false;
	}
	else
	{
		$online = false;
	}

	$template->assign_vars(array(
		'S_CAN_DOWNLOAD'	=> ($game_data['cat_download'] && $game_data['game_download'] && $auth_arcade->acl_get('c_download', $game_data['cat_id'])) ? true : false,
		'S_ONLINE'			=> ($config['load_onlinetrack'] && $online) ? true : false,

		'U_GAME_PLAY'		=> $arcade->url("mode=play&amp;g=" . $game_id),

		'ONLINE_IMG'		=> (!$config['load_onlinetrack']) ? '' : (($online) ? $user->img('icon_user_online', 'ONLINE') : $user->img('icon_user_offline', 'OFFLINE')),

		'BEST_USER'			=> $arcade->get_username_string('full', $best_user['user_id'], $best_user['username'], $best_user['user_colour']),
		'BEST_SCORE'		=> $arcade->number_format($best_user['score']),
		'BEST_DATE'			=> $user->format_date($best_user['score_date']),


		'GAME_TOTAL_TIME'	=> $arcade->time_format($game_data['total_time']),
		'GAME_TOTAL_PLAYS'	=> $arcade->number_format($game_data['total_plays']),

		'GAME_DESC'			=> $game_data['game_desc'],
		'GAME_NAME'			=> $game_data['game_name'],
		'GAME_DOWNLOAD'		=> $arcade->number_format($game_data['game_download_total']),
		'GAME_RATING_IMG'	=> $arcade->set_rating_image($game_data, $mode),
		'GAME_IMAGE'		=> ($game_data['game_image'] != '') ? $arcade->url("img=" . $game_data['game_image']) : '' ,

		'AVATAR'			=> $user_avatar,

		'PAGE_NUMBER'		=> on_page($game_data['total_scores'], $arcade_config['stat_items_per_page'], $start),
		'TOTAL_SCORES'		=> $l_total_scores,
		'PAGINATION'		=> generate_pagination($arcade->url("mode=stats&amp;g=$game_id"), $game_data['total_scores'], $arcade_config['stat_items_per_page'], $start),
	));
}
else
{
	$page_title = $user->lang['ARCADE_STATS_PAGE_TITLE'];
	// Lets get the longest held highscores....
	$rank = 0;
	$actual_rank = 0;
	$previous_time = 0;
	for ($i = 0, $game_count = sizeof($arcade->longest_highscores); $i < $game_count; $i++)
	{
		// This code is used to calculate the actual rank.
		// For example if there are ties...
		$actual_rank++;
		if ($previous_time != $arcade->longest_highscores[$i]['game_highdate'])
		{
			$rank = $actual_rank;
		}
		$previous_time = $arcade->longest_highscores[$i]['game_highdate'];

		$time_held = time() - $arcade->longest_highscores[$i]['game_highdate'];
		$time_held = $arcade->time_format($time_held);

		$template->assign_block_vars('record_row', array(
			'GAME_NAME'			=> '<a href="' . $arcade->url("mode=play&amp;g=" . $arcade->longest_highscores[$i]['game_id']) . '">' . $arcade->longest_highscores[$i]['game_name'] . '</a>',
			'USERNAME'			=> $arcade->get_username_string('full', $arcade->longest_highscores[$i]['user_id'], $arcade->longest_highscores[$i]['username'], $arcade->longest_highscores[$i]['user_colour']),
			'HIGHSCORE'			=> $arcade->number_format($arcade->longest_highscores[$i]['game_highscore']),
			'HIGHSCORE_DATE'	=> $user->format_date($arcade->longest_highscores[$i]['game_highdate']),
			'RANK'				=> $rank,
			'TIME_HELD'			=> $time_held)
		);
	}
	// End of longest held highscores

	// This next part is used to allow the user to see every game in a drop
	// so they can select one to see its scores...
	$arcade->games_list('game_jump');
	// End game select...

	// User select
	$arcade->users_list();
	// End user select...

	$s_hidden_fields =  build_hidden_fields(array('mode' => 'stats'));
	if ($_SID)
	{
		$s_hidden_fields .= build_hidden_fields(array('sid' =>  $_SID));
	}

	$template->assign_vars(array(
		'S_ACTION'			=> $arcade->url(),
		'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
	));

	// Most popular games
	foreach ($arcade->most_popular_games as $game)
	{
		$template->assign_block_vars('pop_games', array(
			'U_GAME_PLAY'		=> $arcade->url("mode=play&amp;g=" . $game['game_id']),
			'GAME_IMAGE'		=> ($game['game_image'] != '') ? $arcade->url("img=" . $game['game_image']) : '',
			'GAME_IMAGE_ALT'	=> $game['game_name'],
			'GAME_NAME'			=> ($game['game_plays'] == 1) ? sprintf($user->lang['ARCADE_STATS_PLAY'], $game['game_name'], $arcade->number_format($game['game_plays'])) : sprintf($user->lang['ARCADE_STATS_PLAYS'], $game['game_name'], $arcade->number_format($game['game_plays'])),
		));
	}
	// End Most popular games

	// Arcade leaders section
	$rank = 0;
	$actual_rank = 0;
	$previous_score = 0;
	$real_leaders_count = sizeof($arcade->leaders);
	$leaders_count = ($arcade_config['arcade_leaders'] > $real_leaders_count) ? $real_leaders_count : $arcade_config['arcade_leaders'];
	for ($i = 0, $leaders_count; $i < $leaders_count; $i++)
	{
		// This code is used to calculate the actual rank.
		// For example if there are ties...
		$actual_rank++;
		if ($previous_score != $arcade->leaders[$i]['total_wins'])
		{
			$rank = $actual_rank;
		}
		$previous_score = $arcade->leaders[$i]['total_wins'];

		$user_id = $arcade->leaders[$i]['user_id'];
		$username =  $arcade->get_username_string('full', $arcade->leaders[$i]['user_id'], $arcade->leaders[$i]['username'], $arcade->leaders[$i]['user_colour']);
		$total_wins = $arcade->leaders[$i]['total_wins'];

		$user_avatar = ($user->optionget('viewavatars')) ? true : false;

		if ($user_avatar)
		{
			if (!($user_avatar = str_replace('/>', 'style="vertical-align: middle;" />', get_user_avatar($arcade->leaders[$i]['user_avatar'], $arcade->leaders[$i]['user_avatar_type'], '20', '20', $arcade->leaders[$i]['username']))))
			{
				$user_avatar = '<img src="' . $arcade->get_image('src', 'img', 'noavatar.gif') . '" width="20" height="20" style="vertical-align: middle; padding:2px;" alt="' . $arcade->leaders[$i]['username'] . '" title="' . $arcade->leaders[$i]['username'] . '" />';
			}

			$user_avatar = ($auth_arcade->acl_get('u_viewstats') && $user_avatar) ? '<a href="' . $arcade->url("mode=stats&amp;u={$arcade->leaders[$i]['user_id']}") . '">' . $user_avatar . '</a>' : $user_avatar;
		}

		$template->assign_block_vars('users', array(
			'RANK'		=> $rank,
			'USERNAME'	=> ($total_wins == 1) ? sprintf($user->lang['ARCADE_STATS_WIN'], $username, $arcade->number_format($total_wins)) : sprintf($user->lang['ARCADE_STATS_WINS'], $username, $arcade->number_format($total_wins)),
			'AVATAR'	=> $user_avatar,
		));
	}
	// End Arcade leaders section

	// Least popular games
	foreach ($arcade->least_popular_games as $game)
	{
		$template->assign_block_vars('lpop_games', array(
			'U_GAME_PLAY'		=> $arcade->url("mode=play&amp;g=" . $game['game_id']),
			'GAME_IMAGE'		=> ($game['game_image'] != '') ? $arcade->url("img=" . $game['game_image']) : '',
			'GAME_IMAGE_ALT'	=> $game['game_name'],
			'GAME_NAME'			=> ($game['game_plays'] == 1) ? sprintf($user->lang['ARCADE_STATS_PLAY'], $game['game_name'], $arcade->number_format($game['game_plays'])) : sprintf($user->lang['ARCADE_STATS_PLAYS'], $game['game_name'], $arcade->number_format($game['game_plays'])),
		));
	}
	// End Least popular games

	if ($auth_arcade->acl_getc_global('c_download'))
	{
		if (sizeof($arcade->most_downloaded_games) && sizeof($arcade->least_downloaded_games))
		{
			$template->assign_var('S_DISPLAY_DOWNLOAD_STATS', true);

			// Most downloaded games
			foreach ($arcade->most_downloaded_games as $game)
			{
				$template->assign_block_vars('mostdownloaded_games', array(
					'U_GAME_DOWNLOAD'	=> $arcade->url("mode=download&amp;g=" . $game['game_id']),
					'GAME_IMAGE'		=> ($game['game_image'] != '') ? $arcade->url("img=" . $game['game_image']) : '',
					'GAME_IMAGE_ALT'	=> $game['game_name'],
					'GAME_NAME'			=> ($game['game_download_total'] == 1) ? sprintf($user->lang['ARCADE_GAME_DOWNLOAD_TOTAL'], $game['game_name'], $arcade->number_format($game['game_download_total'])) : sprintf($user->lang['ARCADE_GAME_DOWNLOAD_TOTALS'], $game['game_name'], $arcade->number_format($game['game_download_total'])),
				));
			}
			// Most downloaded games

			// Least downloaded games
			foreach ($arcade->least_downloaded_games as $game)
			{
				$template->assign_block_vars('leastdownloaded_games', array(
					'U_GAME_DOWNLOAD'	=> $arcade->url("mode=download&amp;g=" . $game['game_id']),
					'GAME_IMAGE'		=> ($game['game_image'] != '') ? $arcade->url("img=" . $game['game_image']) : '',
					'GAME_IMAGE_ALT'	=> $game['game_name'],
					'GAME_NAME'			=> ($game['game_download_total'] == 1) ? sprintf($user->lang['ARCADE_GAME_DOWNLOAD_TOTAL'], $game['game_name'], $arcade->number_format($game['game_download_total'])) : sprintf($user->lang['ARCADE_GAME_DOWNLOAD_TOTALS'], $game['game_name'], $arcade->number_format($game['game_download_total'])),
				));
			}
			// Least downloaded games
		}
	}
}

display_arcade_header($arcade_config['welcome_stats'], $arcade_config['search_stats'], $arcade_config['links_stats']);
display_arcade_online();

// Output page
page_header($page_title, false);

$template->set_filenames(array(
	'body' => 'arcade/stats_body.html')
);

page_footer();

?>