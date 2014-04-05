<?php
/**
*
* @package arcade
* @version $Id: arcade.php 1663 2011-09-22 12:09:30Z killbill $
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

// Common arcade functions

/**
* Display Arcade
*/
function display_arcade($root_data = '')
{
	global $db, $auth, $auth_arcade, $user, $template;
	global $config, $arcade, $arcade_config;

	$cat_rows = $subcats = $cat_ids = array();
	$parent_id = $visible_cats = 0;

	if (!$root_data)
	{
		$root_data = array('cat_id' => 0);
		$sql_where = '';
	}
	else
	{
		$sql_where = ' WHERE left_id > ' . (int) $root_data['left_id'] . ' AND left_id < ' . (int) $root_data['right_id'];
	}

	$sql = 'SELECT *
		FROM ' . ARCADE_CATS_TABLE . "
		$sql_where
		ORDER BY left_id";
	$result = $db->sql_query($sql, $arcade_config['cache_time'] * 3600);

	$branch_root_id = $root_data['cat_id'];
	while ($row = $db->sql_fetchrow($result))
	{
		$cat_id = $row['cat_id'];

		// Category with no members
		if ($row['cat_type'] == ARCADE_CAT && ($row['left_id'] + 1 == $row['right_id']))
		{
			continue;
		}

		// Skip branch
		if (isset($right_id))
		{
			if ($row['left_id'] < $right_id)
			{
				continue;
			}
			unset($right_id);
		}

		if (!$auth_arcade->acl_get('c_list', $cat_id))
		{
			// if the user does not have permissions to list this forum, skip everything until next branch
			$right_id = $row['right_id'];
			continue;
		}

		$cat_ids[] = $cat_id;

		if ($row['parent_id'] == $root_data['cat_id'] || $row['parent_id'] == $branch_root_id)
		{
			// Direct child of current branch
			$parent_id = $cat_id;
			$cat_rows[$cat_id] = $row;

			if ($row['cat_type'] == ARCADE_CAT && $row['parent_id'] == $root_data['cat_id'])
			{
				$branch_root_id = $cat_id;
			}
			$cat_rows[$parent_id]['orig_cat_last_play_time'] = $row['cat_last_play_time'];
		}
		else if ($row['cat_type'] != ARCADE_CAT)
		{
			$subcats[$parent_id][$cat_id]['display'] = ($row['display_on_index']) ? true : false;
			$subcats[$parent_id][$cat_id]['name'] = $row['cat_name'];
			$subcats[$parent_id][$cat_id]['orig_cat_last_play_time'] = $row['cat_last_play_time'];
			$subcats[$parent_id][$cat_id]['cat_last_game_installdate'] = $row['cat_last_game_installdate'];

			$cat_rows[$parent_id]['cat_games'] += $row['cat_games'];

			// Do not list redirects in LINK Forums as Posts.
			if ($row['cat_type'] != ARCADE_LINK)
			{
				$cat_rows[$parent_id]['cat_plays'] += $row['cat_plays'];
			}

			if ($row['cat_last_play_time'] > $cat_rows[$parent_id]['cat_last_play_time'])
			{
				$cat_rows[$parent_id]['cat_last_play_game_id'] = $row['cat_last_play_game_id'];
				$cat_rows[$parent_id]['cat_last_play_game_name'] = $row['cat_last_play_game_name'];
				$cat_rows[$parent_id]['cat_last_play_time'] = $row['cat_last_play_time'];
				$cat_rows[$parent_id]['cat_last_play_user_id'] = $row['cat_last_play_user_id'];
				$cat_rows[$parent_id]['cat_last_play_username'] = $row['cat_last_play_username'];
				$cat_rows[$parent_id]['cat_last_play_user_colour'] = $row['cat_last_play_user_colour'];
				$cat_rows[$parent_id]['cat_last_play_score'] = $row['cat_last_play_score'];
			}
		}
	}
	$db->sql_freeresult($result);

	// Used to tell whatever we have to create a dummy category or not.
	$last_catless = true;
	foreach ($cat_rows as $row)
	{
		// Empty category
		if ($row['parent_id'] == $root_data['cat_id'] && $row['cat_type'] == ARCADE_CAT)
		{
			$template->assign_block_vars('catrow', array(
				'S_IS_CAT'				=> true,
				'CAT_ID'				=> $row['cat_id'],
				'CAT_NAME'				=> $row['cat_name'],
				'CAT_DESC'				=> generate_text_for_display($row['cat_desc'], $row['cat_desc_uid'], $row['cat_desc_bitfield'], $row['cat_desc_options']),
				'CAT_FOLDER_IMG'		=> '',
				'CAT_FOLDER_IMG_SRC'	=> '',
				'CAT_IMAGE'				=> $arcade->get_image('full', 'cat', $row['cat_image'], $row['cat_name']),
				'CAT_IMAGE_SRC'			=> $arcade->get_image('src', 'cat', $row['cat_image'], $row['cat_name']),
				'U_VIEWCAT'				=> $arcade->url('mode=cat&amp;c=' . $row['cat_id'])));

			continue;
		}

		$visible_cats++;
		$cat_id = $row['cat_id'];

		$cat_new_games = ((time() - $row['cat_last_game_installdate']) <= ($arcade_config['new_games_delay'] * 86400))? true : false;

		$folder_image = $folder_alt = $l_subcats = '';
		$subcats_list = array();

		// Generate list of subforums if we need to
		if (isset($subcats[$cat_id]))
		{
			foreach ($subcats[$cat_id] as $subcat_id => $subcat_row)
			{
				$subcat_new_games =  ( (time() - $subcat_row['cat_last_game_installdate']) <= ($arcade_config['new_games_delay'] * 86400))? true : false;

				if ($subcat_row['display'] && $subcat_row['name'])
				{
					$subcats_list[] = array(
						'link'		=> $arcade->url('mode=cat&amp;c=' . $subcat_id),
						'name'		=> $subcat_row['name'],
						'unread'	=> $subcat_new_games,
					);
				}
				else
				{
					unset($subcats[$cat_id][$subcat_id]);
				}

				// If one subcat has new games the cat gets new games too...
				if ($subcat_new_games)
				{
					$cat_new_games = true;
				}
			}

			$l_subcats = (sizeof($subcats[$cat_id]) == 1) ? $user->lang['ARCADE_SUBCAT'] . ': ' : $user->lang['ARCADE_SUBCATS'] . ': ';
			$folder_image = ($cat_new_games) ? 'forum_unread_subforum' : 'forum_read_subforum';
		}
		else
		{
			switch ($row['cat_type'])
			{
				case ARCADE_CAT_GAMES:
					$folder_image = ($cat_new_games) ? 'forum_unread' : 'forum_read';
				break;

				case ARCADE_LINK:
					$folder_image = 'forum_link';
				break;
			}
		}

		// Which folder should we display?
		if ($row['cat_status'] == ITEM_LOCKED)
		{
			$folder_image = ($cat_new_games) ? 'forum_unread_locked' : 'forum_read_locked';
			$folder_alt = 'ARCADE_CAT_LOCKED';
		}
		else
		{
			$folder_alt = ($cat_new_games) ? 'ARCADE_NEW_GAMES' : 'ARCADE_NO_NEW_GAMES';
		}

		// Create last play link information, if appropriate
		if ($row['cat_last_play_game_id'] && $auth_arcade->acl_get('c_view', $cat_id))
		{
			$last_play_time = $user->format_date($row['cat_last_play_time']);
			$style_color = 'style="color: #' . $row['cat_last_play_user_colour'] . '; font-weight: bold;"';
			$game_link = '<a href="' . $arcade->url("mode=play&amp;g=" . $row['cat_last_play_game_id']) . '">' . $row['cat_last_play_game_name'] . '</a>';

			$player_link = $arcade->get_username_string('full', $row['cat_last_play_user_id'], $row['cat_last_play_username'], $row['cat_last_play_user_colour']);
			$last_play = sprintf($user->lang['ARCADE_LAST_PLAY'], $player_link, $arcade->number_format($row['cat_last_play_score']), $game_link);
		}
		else
		{
			$last_play_time = '';
			$last_play = '';
		}

		if ($row['cat_type'] != ARCADE_LINK)
		{
			$u_viewcat = $arcade->url('mode=cat&amp;c=' . $row['cat_id']);
		}
		else
		{
			// If the forum is a link and we count redirects we need to visit it
			// If the forum is having a password or no read access we do not expose the link, but instead handle it in viewforum
			if (($row['cat_flags'] & ARCADE_FLAG_LINK_TRACK) || $row['cat_password'] || !$auth_arcade->acl_get('c_view', $cat_id))
			{
				$u_viewcat = $arcade->url('mode=cat&amp;c=' . $row['cat_id']);
			}
			else
			{
				$u_viewcat = $row['cat_link'];
			}
		}

		$s_subcats_list = array();
		foreach ($subcats_list as $subcat)
		{
			$s_subcats_list[] = '<a href="' . $subcat['link'] . '" class="subforum ' . (($subcat['unread']) ? 'unread' : 'read') . '">' . $subcat['name'] . '</a>';
		}
		$s_subcats_list = (string) implode(', ', $s_subcats_list);
		$catless = ($row['parent_id'] == $root_data['cat_id']) ? true : false;

		$l_play_click_count = ($row['cat_type'] == ARCADE_LINK) ? 'CLICKS' : 'PLAYS';
		$play_click_count = ($row['cat_type'] != ARCADE_LINK || $row['cat_flags'] & ARCADE_FLAG_LINK_TRACK) ? $arcade->number_format($row['cat_plays']) : '';

		$template->assign_block_vars('catrow', array(
			'S_IS_CAT'			=> false,
			'S_NO_CAT'			=> $catless && !$last_catless,
			'S_IS_LINK'			=> ($row['cat_type'] == ARCADE_LINK) ? true : false,
			'S_CAT_NEW_GAMES'	=> $cat_new_games,
			'S_LOCKED_CAT'		=> ($row['cat_status'] == ITEM_LOCKED) ? true : false,
			'S_LIST_SUBCATS'	=> ($row['display_subcat_list']) ? true : false,
			'S_SUBCATS'			=> (sizeof($subcats_list)) ? true : false,

			'CAT_ID'				=> $row['cat_id'],
			'CAT_NAME'				=> $row['cat_name'],
			'CAT_DESC'				=> generate_text_for_display(censor_text($row['cat_desc']), $row['cat_desc_uid'], $row['cat_desc_bitfield'], $row['cat_desc_options']),
			'GAMES'					=> $arcade->number_format($row['cat_games']),
			$l_play_click_count		=> $play_click_count,
			'CAT_FOLDER_IMG'		=> $user->img($folder_image, $folder_alt),
			'CAT_FOLDER_IMG_SRC'	=> $user->img($folder_image, $folder_alt, false, '', 'src'),
			'CAT_FOLDER_IMG_ALT'	=> isset($user->lang[$folder_alt]) ? $user->lang[$folder_alt] : '',
			'CAT_IMAGE'				=> $arcade->get_image('full', 'cat', $row['cat_image'], $row['cat_name']),
			'CAT_IMAGE_SRC'			=> $arcade->get_image('src', 'cat', $row['cat_image'], $row['cat_name']),
			'CAT_DISPLAY'			=> $row['cat_display'],
			'SUBCATS'				=> $s_subcats_list,
			'LAST_PLAY_TIME'		=> $last_play_time,
			'LAST_PLAY'				=> $last_play,

			'L_SUBCAT_STR'			=> $l_subcats,
			'L_CAT_FOLDER_ALT'		=> $folder_alt,

			'U_VIEWCAT'				=> $u_viewcat,
		));

		// Assign subforums loop for style authors
		foreach ($subcats_list as $subcat)
		{
			$template->assign_block_vars('catrow.subcat', array(
				'U_SUBCAT	'		=> $subcat['link'],
				'SUBCAT_NAME'		=> $subcat['name'],
				'S_UNREAD'		=> $subcat['unread'])
			);
		}
		$last_catless = $catless;
	}

	$template->assign_vars(array(
		'S_HAS_SUBCAT'	=> ($visible_cats) ? true : false,
		'L_SUBCAT'		=> ($visible_cats == 1) ? $user->lang['ARCADE_SUBCAT'] : $user->lang['ARCADE_SUBCATS'],
	));

	return;
}

function obtain_arcade_users_online(&$online_users)
{
	global $db, $user, $auth, $config, $arcade, $arcade_config;

	$prew_id = array();
	$prew_ip = array();
	// a little discrete magic to cache this for 30 seconds
	$online_users['online_time'] = (int) (($arcade_config['online_time'] > 0) ? $arcade_config['online_time'] : $config['load_online_time']);
	$online_time = intval(time() - ($online_users['online_time'] * 60));

	$sql = 'SELECT a.start_time, s.session_viewonline, s.session_page, s.session_time, s.session_ip, u.user_id, u.username, u.user_colour, g.game_id, g.game_name
		FROM		' . ARCADE_SESSIONS_TABLE . '	a
		LEFT JOIN	' . SESSIONS_TABLE . '			s ON a.phpbb_session_id = s.session_id
		LEFT JOIN	' . USERS_TABLE . '				u ON a.user_id = u.user_id
		LEFT JOIN	' . ARCADE_GAMES_TABLE . '		g ON a.game_id = g.game_id
		WHERE s.session_user_id = u.user_id
		AND ' . $db->sql_in_set('g.cat_id', $arcade->get_permissions('c_view'), false, true) . '
		AND (a.user_id <> ' . ANONYMOUS . ' AND a.start_time >= ' . ($online_time - ((int) ($online_time % 30))) . ')
			OR
			(a.user_id =  ' . ANONYMOUS . ' AND a.start_time >= ' . ($online_time - ((int) ($online_time % 60))) . ')
		ORDER BY a.start_time DESC';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		if (($row['start_time'] < $row['session_time']) && (strpos($row['session_page'], 'mode=play') === false || strpos($row['session_page'], 'mode=popup')))
		{
			continue;
		}

		if ($row['user_id'] != ANONYMOUS && !isset($prew_id[$row['user_id']]))
		{
			$prew_id[$row['user_id']] = 1;

			if (!$row['session_viewonline'])
			{
				$player_link = '<em>' . $arcade->get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '</em>';
				$online_users['hidden_online']++;
			}
			else
			{
				$player_link = $arcade->get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				$online_users['visible_online']++;
			}
		}
		else if ($row['user_id'] == ANONYMOUS && !isset($prew_ip[$row['session_ip']]))
		{
			$prew_ip[$row['session_ip']] = 1;
			$player_link = $arcade->get_username_string('full', $row['user_id'], $user->lang['GUEST'], $row['user_colour']);
			$online_users['guests_online']++;
		}
		else
		{
			continue;
		}

		if ($row['session_viewonline'] || $auth->acl_get('u_viewonline'))
		{
			// Build a list of users playing a game
			if (!isset($online_users['games_names'][$row['game_id']]))
			{
				$online_users['games_names'][$row['game_id']] = $row['game_name'];
				$online_users['games_players'][$row['game_id']] = $player_link;
			}
			else
			{
				$online_users['games_players'][$row['game_id']] .=  ', ' . $player_link;
			}
		}
	}
	$db->sql_freeresult($result);

	$online_users['total_online'] = intval($online_users['guests_online'] + $online_users['visible_online'] + $online_users['hidden_online']);
}

/**
* Display Arcade Online
*/
function display_arcade_online()
{
	global $user, $template, $arcade, $auth_arcade;

	if ($auth_arcade->acl_get('u_view_whoisplaying'))
	{
		$online_users = array(
			'games_names'			=> array(),
			'games_players'			=> array(),
			'total_online'			=> 0,
			'visible_online'		=> 0,
			'hidden_online'			=> 0,
			'guests_online'			=> 0,
			'online_time'			=> 0
		);

		obtain_arcade_users_online($online_users);

		if ($online_users['total_online'] > 0)
		{
			foreach ($online_users['games_names'] as $key => $val)
			{
				$template->assign_block_vars('arcade_online_row', array(
					'GAME'			=> '<a href="' . $arcade->url("mode=play&amp;g={$key}") . '">' . $val . '</a>',
					'PLAYER_LIST'	=> $online_users['games_players'][$key]
				));
			}
		}

		$vars_online = array(
			'ARCADE_ONLINE'	=> array('total_online', 'l_t_user_s', 0),
			'REG'			=> array('visible_online', 'l_r_user_s', 1),
			'HIDDEN'		=> array('hidden_online', 'l_h_user_s', 1),
			'GUEST'			=> array('guests_online', 'l_g_user_s', 0)
		);

		foreach ($vars_online as $l_prefix => $var_ary)
		{
			if ($var_ary[2])
			{
				$l_suffix = '_AND';
			}
			else
			{
				$l_suffix = '';
			}
			switch ($online_users[$var_ary[0]])
			{
				case 0:
					${$var_ary[1]} = $user->lang[$l_prefix . '_USERS_ZERO_TOTAL' . $l_suffix];
				break;

				case 1:
					${$var_ary[1]} = $user->lang[$l_prefix . '_USER_TOTAL' . $l_suffix];
				break;

				default:
					${$var_ary[1]} = $user->lang[$l_prefix . '_USERS_TOTAL' . $l_suffix];
				break;
			}
		}
		unset($vars_online);

		$l_online_users  = sprintf($l_t_user_s, $online_users['total_online']);
		$l_online_users .= sprintf($l_r_user_s, $online_users['visible_online']);
		$l_online_users .= sprintf($l_h_user_s, $online_users['hidden_online']);
		$l_online_users .= sprintf($l_g_user_s, $online_users['guests_online']);
		$l_online_time   = sprintf($user->lang['VIEW_ONLINE_TIME' . (($online_users['online_time'] > 1) ? 'S' : '')], $online_users['online_time']);

		$template->assign_var('ARCADE_TOTAL_USERS_PLAYING', $l_online_users . ' (' . $l_online_time . ')');
	}
}

/**
* Display Arcade Header
*/
function display_arcade_header($show_welcome_box = true, $show_adv_search = true, $show_links = true)
{
	global $arcade, $arcade_config, $auth, $auth_arcade, $game_fav_data, $user, $template, $_SID;

	if ($show_welcome_box)
	{
		$user_avatar	= ($user->optionget('viewavatars')) ? true : false;
		$user_stats		= ($auth_arcade->acl_get('u_viewstats')) ? true : false;

		$template->assign_vars(array(
			'S_SHOW_WELCOME_BOX'	=> true,

			'L_ARCADE_WELCOME'		=> sprintf($user->lang['ARCADE_WELCOME'], $arcade->get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour'])),
		));

		// Newest Games
		foreach ($arcade->newest_games as $newest_games)
		{
			$template->assign_block_vars('newest_games', array(
				'U_GAME_PLAY'	=> $arcade->url('mode=play&amp;g=' . $newest_games['game_id']),
				'PLAY_POPUP'	=> ($arcade_config['game_popup_icon_enabled']) ? $arcade->get_popup_img($newest_games) : false,
				'GAME_IMAGE'	=> ($newest_games['game_image'] != '') ? $arcade->url("img=" . $newest_games['game_image']) : '',
				'GAME_NAME'		=> $newest_games['game_name'],
			));
		}
		// Newest Games

		// Start of Top 3 Arcade Players
		$real_leaders_count = sizeof($arcade->leaders);
		$leaders_count = ($real_leaders_count > $arcade_config['arcade_leaders_header']) ? $arcade_config['arcade_leaders_header'] : $real_leaders_count;

		$arcade_leaders_img = array(
			1		=> $arcade->get_image('src', 'img', '1st.gif', ''),
			2		=> $arcade->get_image('src', 'img', '2nd.gif', ''),
			3		=> $arcade->get_image('src', 'img', '3rd.gif', ''),
		);

		$arcade_leaders_img_alt = array(
			1		=> $user->lang['ARCADE_FIRST'],
			2		=> $user->lang['ARCADE_SECOND'],
			3		=> $user->lang['ARCADE_THIRD'],
		);

		if ($leaders_count > 0)
		{
			switch ($leaders_count)
			{
				case 1:
					$arcade_leaders_width = 100;
				break;

				case 2:
					$arcade_leaders_width = 49;
				break;

				default:
					$arcade_leaders_width = 33;
				break;
			}
			$template->assign_var('ARCADE_LEADERS_WIDTH', $arcade_leaders_width);

			$rank = 0;
			$actual_rank = 0;
			$previous_wins = 0;
			for ($i = 0; $i < $leaders_count; $i++)
			{
				// This code is used to calculate the actual rank.
				// For example if there are ties...
				$actual_rank++;

				if ($previous_wins != $arcade->leaders[$i]['total_wins'])
				{
					$rank = $actual_rank;
				}

				$previous_wins	= $arcade->leaders[$i]['total_wins'];
				$user_link		= $arcade->get_username_string('full', $arcade->leaders[$i]['user_id'], $arcade->leaders[$i]['username'], $arcade->leaders[$i]['user_colour']);

				if ($user_avatar)
				{
					if (!($user_avatar = str_replace('/>', 'style="vertical-align: middle; padding:2px;" />', get_user_avatar($arcade->leaders[$i]['user_avatar'], $arcade->leaders[$i]['user_avatar_type'], 30, 30, $arcade->leaders[$i]['username']))))
					{
						$user_avatar = '<img src="' . $arcade->get_image('src', 'img', 'noavatar.gif') . '" width="30" height="30" style="vertical-align: middle; padding:2px;" alt="' . $arcade->leaders[$i]['username'] . '" title="' . $arcade->leaders[$i]['username'] . '" />';
					}

					$user_avatar = ($user_stats && $user_avatar) ? '<a href="' . $arcade->url("mode=stats&amp;u={$arcade->leaders[$i]['user_id']}") . '">' . $user_avatar . '</a>' : $user_avatar;
				}

				$template->assign_block_vars('arcade_leaders', array(
					'ARCADE_LEADERS_RANK'			=> $rank,
					'ARCADE_LEADERS_ACTUAL_RANK'	=> $actual_rank,
					'ARCADE_LEADERS_AVATAR'			=> $user_avatar,
					'ARCADE_LEADERS'				=> $user_link,
					'ARCADE_LEADERS_IMAGE'			=> (isset($arcade_leaders_img[$rank])) ? $arcade_leaders_img[$rank] : $arcade->get_image('src', 'img', 'trophy.gif', ''),
					'ARCADE_LEADERS_IMAGE_ALT'		=> (isset($arcade_leaders_img_alt[$rank])) ? $arcade_leaders_img_alt[$rank] : sprintf($user->lang['ARCADE_PLACE'], $rank),
					'VICTORIES'						=> $arcade->leaders[$i]['total_wins']
				));
			}
		}
		// End of Top 3 Arcade Players

		// Start of Lastest highscores
		foreach ($arcade->latest_highscores as $latest_highscore)
		{
			if ($user_avatar)
			{
				if (!($user_avatar = str_replace('/>', 'style="vertical-align: middle; padding:2px;" />', get_user_avatar($latest_highscore['user_avatar'], $latest_highscore['user_avatar_type'], 20, 20, $latest_highscore['username']))))
				{
					$user_avatar = '<img src="' . $arcade->get_image('src', 'img', 'noavatar.gif') . '" width="20" height="20" style="vertical-align: middle; padding:2px;" alt="' . $latest_highscore['username'] . '" title="' . $latest_highscore['username'] . '" />';
				}

				$user_avatar = ($user_stats && $user_avatar) ? '<a href="' . $arcade->url("mode=stats&amp;u={$latest_highscore['game_highuser']}") . '">' . $user_avatar . '</a>' : $user_avatar;
			}

			$latest_scoreuser	= $arcade->get_username_string('full', $latest_highscore['game_highuser'], $latest_highscore['username'], $latest_highscore['user_colour']);
			$game_url			= '<a href="' . $arcade->url("mode=play&amp;g=" . $latest_highscore['game_id']) . '">' . $latest_highscore['game_name'] . '</a>';
			$latest_score		= sprintf($user->lang['ARCADE_WELCOME_SCORE'], $arcade->number_format($latest_highscore['game_highscore']), $user->format_date($latest_highscore['game_highdate']));
			$game_url_tooltip	= '<a href="' . $arcade->url("mode=play&amp;g=" . $latest_highscore['game_id']) . '" class="tooltip">' . $latest_highscore['game_name'] . '<span class="aheader">' . $latest_score . '</span></a>';
			$popup_img			= ($arcade_config['game_popup_icon_enabled']) ? $arcade->get_popup_img($latest_highscore, 11) : '';

			$template->assign_block_vars('latest_scores', array(
				'HEADING_CHAMP_AVATAR'		=> $user_avatar,
				'HEADING_CHAMP' 			=> sprintf($user->lang['ARCADE_WELCOME_CHAMP'], $latest_scoreuser, $popup_img, $game_url),
				'HEADING_CHAMP_SCORE'		=> $latest_score,
				'HEADING_CHAMP_TOOLTIP' 	=> sprintf($user->lang['ARCADE_WELCOME_CHAMP'], $latest_scoreuser, $popup_img, $game_url_tooltip),
			));
		}
		// End Lastest Highscores

		// This will return the user rank, rank image, and avatar image
		// as well as the users total wins, total time, and games played...
		$userdata = $arcade->get_user_info();

		$template->assign_vars(array(
			'HEADER_AVATAR' 			=> $userdata['avatar'],
			'HEADER_USER_RANK' 			=> $userdata['rank_title'],
			'HEADER_USER_COLOUR'		=> $user->data['user_colour'],
			'HEADER_RANK_IMAGE' 		=> $userdata['rank_image'],
			'HEADER_RANK_IMAGE_SRC' 	=> $userdata['rank_image_src'],
			'HEADER_ARCADE_WINS' 		=> $arcade->number_format($userdata['total_wins']),
			'HEADER_ARCADE_PLAYS'		=> $arcade->number_format($userdata['total_plays']),
			'HEADER_ARCADE_TIME' 		=> $arcade->time_format($userdata['total_time']),
			'HEADER_USERNAME' 			=> $arcade->get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour']),
		));

		if ($arcade->points['show'])
		{
			$template->assign_vars(array(
				'S_SHOW_POINTS'			=> true,
				'HEADER_USER_POINTS'	=> $arcade->number_format($arcade->points['total']) . ' ' . $arcade->points['name'],
				'HEADER_POINTS_NAME'	=> $arcade->points['name'],
				)
			);
		}

		$total_games_header = sizeof($arcade->games);
		if ($total_games_header)
		{
			$total_games_header = sprintf($user->lang['ARCADE_TOTAL_GAME' . (($total_games_header > 1) ? 'S' : '')], $arcade->number_format($total_games_header));
		}

		$total_downloads_header = $arcade_config['total_downloads'];
		if ($total_downloads_header)
		{
			$total_downloads_header = sprintf($user->lang['ARCADE_TOTAL_DOWNLOAD' . (($total_downloads_header > 1) ? 'S' : '')], $arcade->number_format($total_downloads_header));
		}

		$total_games_played = $arcade_config['total_plays'];
		if ($total_games_played )
		{
			$total_games_played = sprintf($user->lang['ARCADE_TOTAL_PLAY' . (($total_games_played > 1) ? 'S' : '')], $arcade->number_format($total_games_played), $arcade->time_format($arcade_config['total_plays_time']));
		}

		$template->assign_vars(array(
			'S_CAN_DOWNLOAD'			=> $auth_arcade->acl_getc_global('c_download'),
			'TOTAL_GAMES_PLAYED'		=> $total_games_played,
			'TOTAL_GAMES_HEADER'		=> $total_games_header,
			'TOTAL_DOWNLOADS_HEADER'	=> $total_downloads_header,
		));
	}

	if ($show_adv_search)
	{
		$arcade->games_list('quick_jump');

		// Quick jump of your favorite games...
		$game_fav_data = (!isset($game_fav_data)) ? $arcade->get_fav_data() : $game_fav_data;
		foreach ($game_fav_data as $game)
		{
			$template->assign_block_vars('fav', array(
				'GAME_ID'	=> $game['game_id'],
				'GAME_NAME' => $game['game_name'])
			);
		}
		// Quick jump of your favorite games...

		$s_header_hidden_fields =  build_hidden_fields(array('mode' => 'play'));
		if ($_SID)
		{
			$s_header_hidden_fields .= build_hidden_fields(array('sid' =>  $_SID));
		}

		$template->assign_vars(array(
			'S_ACTION'						=> $arcade->url($arcade->gametop),
			'S_SHOW_ADV_SEARCH'				=> true,
			'S_HEADER_HIDDEN_FIELDS'		=> $s_header_hidden_fields,
			'S_ACTION_SEARCH'				=> $arcade->url('mode=search'),
			'S_HEADER_HIDDEN_FIELDS_SEARCH' => build_hidden_fields(array('mode' => 'search'))
		));
	}

	if ($show_links)
	{
		generate_arcade_header_links();
	}
	return;
}

/*
* This is only uses outside of the arcade to display the total highscores of the user on the viewtopic.php and memberlist.php page...
*/
function display_arcade_highscores($user_id, $page = '')
{
	global $template, $user, $phpbb_root_path, $phpEx;

	$arcade_cache = new arcade_cache();
	if ($page == 'viewtopic' || $page == 'memberlist')
	{
		$arcade_config = $arcade_cache->obtain_arcade_config();
		if (($page == 'viewtopic' && !$arcade_config['display_viewtopic']) || ($page == 'memberlist' && !$arcade_config['display_memberlist']))
		{
			return;
		}
	}

	$highscore_data = $arcade_cache->obtain_arcade_leaders_all();

	$total_highscores = $leader_id = 0;
	$found = false;

	if (isset($highscore_data[$user_id]))
	{
		$total_highscores = $highscore_data[$user_id];
		$leader_id = array_shift(array_keys($highscore_data));
		$found = true;
	}

	if ($found)
	{
		$return = array(
			'S_HAS_HIGHSCORES'		=> true,
			'S_IS_ARCADE_LEADER'	=> ($leader_id == $user_id) ? true : false,
			'U_ARCADE_STATS'		=> append_sid("{$phpbb_root_path}arcade.$phpEx", "mode=stats&amp;u={$user_id}"),
			'TOTAL_HIGHSCORES'		=> $total_highscores,
		);

		return $return;
	}
}

function gen_arcade_auth_level($cat_id, $cat_status)
{
	global $template, $auth, $auth_arcade, $arcade, $user, $config;

	$locked = ($cat_status == ITEM_LOCKED && !$auth->acl_get('a_arcade_cat')) ? true : false;

	$rules = array_filter(array(
		($auth_arcade->acl_get('c_play', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_PLAY_CAN'] : $user->lang['ARCADE_RULES_PLAY_CANNOT'],
		($auth_arcade->acl_get('c_popup', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_PLAY_POPUP_CAN'] : $user->lang['ARCADE_RULES_PLAY_POPUP_CANNOT'],
		($arcade->points['show']) ? ($auth_arcade->acl_get('c_playfree', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_PLAY_FREE_CAN'] : $user->lang['ARCADE_RULES_PLAY_FREE_CANNOT'] : '',
		($auth_arcade->acl_get('c_score', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_SCORE_CAN'] : $user->lang['ARCADE_RULES_SCORE_CANNOT'],
		($auth_arcade->acl_get('c_download', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_DOWNLOAD_CAN'] : $user->lang['ARCADE_RULES_DOWNLOAD_CANNOT'],
		($auth_arcade->acl_get('c_rate', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_RATE_CAN'] : $user->lang['ARCADE_RULES_RATE_CANNOT'],
		($auth_arcade->acl_get('c_comment', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_COMMENT_CAN'] : $user->lang['ARCADE_RULES_COMMENT_CANNOT'],
		($auth_arcade->acl_get('c_report', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_REPORT_CAN'] : $user->lang['ARCADE_RULES_REPORT_CANNOT'],
		($auth_arcade->acl_get('c_ignorecontrol', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_IGNORE_CONTROL_CAN'] : $user->lang['ARCADE_RULES_IGNORE_CONTROL_CANNOT'],
		($auth_arcade->acl_get('c_resolution', $cat_id) && !$locked) ? $user->lang['ARCADE_RULES_RESOLUTION_CAN'] : $user->lang['ARCADE_RULES_RESOLUTION_CANNOT'],
	));

	foreach ($rules as $rule)
	{
		$template->assign_block_vars('rules', array('RULE' => $rule));
	}

	return;
}

function generate_arcade_header_links()
{
	global $template, $arcade, $auth_arcade, $user;

	$template->assign_vars(array(
		'S_SHOW_LINKS'				=> true,
		'S_HEADER_FEELING_LUCKY'	=> ($auth_arcade->acl_getc_global('c_play')) ? true : false,

		'U_HEADER_FEELING_LUCKY'	=> $arcade->url('mode=random'),
		'U_HEADER_NEW_GAMES'		=> $arcade->url('mode=search&amp;search_id=newgames'),
		'U_HEADER_MY_FAV'			=> $arcade->url('mode=fav'),
		'U_HEADER_STATS'			=> ($auth_arcade->acl_get('u_viewstats')) ? $arcade->url('mode=stats') : false,
		'U_HEADER_MY_STATS'			=> $arcade->url("mode=stats&amp;u={$user->data['user_id']}")));

	return;
}

/**
* Create forum rules for given forum
*/
function generate_cat_rules(&$cat_data)
{
	if (!$cat_data['cat_rules'] && !$cat_data['cat_rules_link'])
	{
		return;
	}

	global $template;

	if ($cat_data['cat_rules'])
	{
		$cat_data['cat_rules'] = generate_text_for_display(censor_text($cat_data['cat_rules']), $cat_data['cat_rules_uid'], $cat_data['cat_rules_bitfield'], $cat_data['cat_rules_options']);
	}

	$template->assign_vars(array(
		'S_CAT_RULES'	=> true,
		'U_CAT_RULES'	=> $cat_data['cat_rules_link'],
		'CAT_RULES'		=> $cat_data['cat_rules']
	));
}

function set_arcade_userdata(&$userdata)
{
	global $db;

	// Add arcade user data to $user->data
	$sql = 'SELECT *
		FROM ' . ARCADE_USERS_TABLE . '
		WHERE user_id = ' . (int) $userdata->data['user_id'];
	$result = $db->sql_query($sql);
	$array = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if (!$db->sql_affectedrows())
	{
		$array = array(
			'user_id'						=> (int) $userdata->data['user_id'],
			'user_arcade_permissions'		=> '',
			'user_arcade_perm_from'			=> false,
			'user_arcade_pm'				=> true,
			'user_arcade_last_download'		=> 0,
			'user_arcade_last_play'			=> 0,
			'user_arcade_last_search'		=> 0,
			'user_arcade_last_search_term'	=> '',
			'games_per_page'				=> 0,
			'games_sort_dir'				=> 'a',
			'games_sort_order'				=> 'n',
		);

		$db->sql_query('INSERT INTO ' . ARCADE_USERS_TABLE . ' ' . $db->sql_build_array('INSERT', $array));
	}

	$userdata->data = array_merge($userdata->data, $array);
}

/**
* Create forum navigation links for given forum, create parent
* list if currently null, assign basic forum info to template
*/
function generate_arcade_nav(&$cat_data, $in_game = false)
{
	global $db, $user, $template, $auth, $arcade, $auth_arcade;

	if (!$auth_arcade->acl_get('c_list', $cat_data['cat_id']))
	{
		return;
	}

	// Get forum parents
	$cat_parents = get_arcade_parents($cat_data);

	if (!empty($cat_data))
	{
		// Build navigation links
		foreach ($cat_parents as $parent_cat_id => $parent_data)
		{
			list($parent_name, $parent_type) = array_values($parent_data);

			// Skip this parent if the user does not have the permission to view it
			if (!$auth_arcade->acl_get('c_list', $parent_cat_id))
			{
				continue;
			}

			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'		=> $parent_name,
				'FORUM_ID'			=> $parent_cat_id,
				'U_VIEW_FORUM'		=> $arcade->url('mode=cat&amp;c=' . $parent_cat_id)));
		}
	}

	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME'		=> $cat_data['cat_name'],
		'FORUM_ID'			=> $cat_data['cat_id'],
		'U_VIEW_FORUM'		=> $arcade->url('mode=cat&amp;c=' . $cat_data['cat_id'])));

	if ($in_game)
	{
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'		=> $cat_data['game_name'],
			'U_VIEW_FORUM'		=> str_replace($arcade->gametop, '', $arcade->url('mode=play&amp;g=' . $cat_data['game_id']))));
	}

	$template->assign_vars(array(
		'CAT_ID' 		=> $cat_data['cat_id'],
		'CAT_NAME'		=> $cat_data['cat_name'],
		'U_VIEW_FORUM'	=> $arcade->url('mode=cat&amp;c=' . $cat_data['cat_id']),
		'CAT_DESC'		=> generate_text_for_display(censor_text($cat_data['cat_desc']), $cat_data['cat_desc_uid'], $cat_data['cat_desc_bitfield'], $cat_data['cat_desc_options'])));

	return;
}

/**
* Returns forum parents as an array. Get them from forum_data if available, or update the database otherwise
*/
function get_arcade_parents(&$cat_data)
{
	global $db;

	$cat_parents = array();

	if ($cat_data['parent_id'] > 0)
	{
		if ($cat_data['cat_parents'] == '')
		{
			$sql = 'SELECT cat_id, cat_name, cat_type
				FROM ' . ARCADE_CATS_TABLE . '
				WHERE left_id < ' . (int) $cat_data['left_id'] . '
					AND right_id > ' . (int) $cat_data['right_id'] . '
				ORDER BY left_id ASC';
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$cat_parents[$row['cat_id']] = array($row['cat_name'], (int) $row['cat_type']);
			}
			$db->sql_freeresult($result);

			$cat_data['cat_parents'] = serialize($cat_parents);

			$sql = 'UPDATE ' . ARCADE_CATS_TABLE . "
				SET cat_parents = '" . $db->sql_escape($cat_data['cat_parents']) . "'
				WHERE parent_id = " . (int) $cat_data['parent_id'];
			$db->sql_query($sql);
		}
		else
		{
			$cat_parents = unserialize($cat_data['cat_parents']);
		}
	}

	return $cat_parents;
}


function login_arcade_box($cat_data)
{
	global $db, $config, $arcade_config, $user, $template, $arcade;

	// Founder ignore the password.
	if ($arcade_config['founder_exempt'] && $user->data['user_type'] == USER_FOUNDER)
	{
		return true;
	}

	$password = request_var('password', '', true);

	$sql = 'SELECT cat_id
		FROM ' . ARCADE_ACCESS_TABLE . '
		WHERE cat_id = ' . $cat_data['cat_id'] . '
			AND user_id = ' . $user->data['user_id'] . "
			AND session_id = '" . $db->sql_escape($user->session_id) . "'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if ($row)
	{
		return true;
	}

	if ($password)
	{
		// Remove expired authorised sessions
		$sql = 'SELECT session_id
			FROM ' . SESSIONS_TABLE;
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			$sql_in = array();
			do
			{
				$sql_in[] = (string) $row['session_id'];
			}
			while ($row = $db->sql_fetchrow($result));

			// Remove expired sessions
			$sql = 'DELETE FROM ' . ARCADE_ACCESS_TABLE . '
				WHERE ' . $db->sql_in_set('session_id', $sql_in, true);
			$db->sql_query($sql);
		}
		$db->sql_freeresult($result);

		if (phpbb_check_hash($password, $cat_data['cat_password']))
		{
			$sql_ary = array(
				'cat_id'		=> (int) $cat_data['cat_id'],
				'user_id'		=> (int) $user->data['user_id'],
				'session_id'	=> (string) $user->session_id,
			);

			$db->sql_query('INSERT INTO ' . ARCADE_ACCESS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

			return true;
		}

		$template->assign_var('LOGIN_ERROR', $user->lang['WRONG_PASSWORD']);
	}

	page_header($user->lang['LOGIN']);

	$template->assign_vars(array(
		'CAT_NAME'				=> $cat_data['cat_name'],
		'S_LOGIN_ACTION'		=> build_url(),
	));

	$template->set_filenames(array(
		'body' => 'arcade/login_cat.html')
	);

	page_footer();
}

/**
* Generate Jumpbox
*/
function make_arcade_jumpbox($action, $cat_id = false, $select_all = false, $acl_list = false)
{
	global $config, $auth, $auth_arcade, $template, $user, $db, $arcade;

	if (!$config['load_jumpbox'])
	{
		return;
	}

	$sql = 'SELECT cat_id, cat_name, parent_id, cat_type, left_id, right_id
		FROM ' . ARCADE_CATS_TABLE . '
		ORDER BY left_id ASC';
	$result = $db->sql_query($sql, 600);

	$right = $padding = 0;
	$padding_store = array('0' => 0);
	$display_jumpbox = false;
	$iteration = 0;

	// Sometimes it could happen that forums will be displayed here not be displayed within the index page
	// This is the result of forums not displayed at index, having list permissions and a parent of a forum with no permissions.
	// If this happens, the padding could be "broken"

	while ($row = $db->sql_fetchrow($result))
	{
		if ($row['left_id'] < $right)
		{
			$padding++;
			$padding_store[$row['parent_id']] = $padding;
		}
		else if ($row['left_id'] > $right + 1)
		{
			// Ok, if the $padding_store for this parent is empty there is something wrong. For now we will skip over it.
			// @todo digging deep to find out "how" this can happen.
			$padding = (isset($padding_store[$row['parent_id']])) ? $padding_store[$row['parent_id']] : $padding;
		}

		$right = $row['right_id'];

		if ($row['cat_type'] == ARCADE_CAT && ($row['left_id'] + 1 == $row['right_id']))
		{
			// Non-postable forum with no subforums, don't display
			continue;
		}

		if (!$auth_arcade->acl_get('c_list', $row['cat_id']))
		{
			// if the user does not have permissions to list this forum skip
			continue;
		}

		if ($acl_list && !$auth_arcade->acl_gets($acl_list, $row['cat_id']))
		{
			continue;
		}
		if (!$display_jumpbox)
		{
			$template->assign_block_vars('jumpbox_arcade', array(
				'CAT_ID'		=> ($select_all) ? 0 : -1,
				'CAT_NAME'		=> ($select_all) ? $user->lang['ALL_FORUMS'] : $user->lang['ARCADE_SELECT_CATEGORY'],
				'S_CAT_COUNT'	=> $iteration)
			);

			$iteration++;
			$display_jumpbox = true;
		}

		$template->assign_block_vars('jumpbox_arcade', array(
			'CAT_ID'			=> $row['cat_id'],
			'CAT_NAME'			=> $row['cat_name'],
			'SELECTED'			=> ($row['cat_id'] == $cat_id) ? ' selected="selected"' : '',
			'S_CAT_COUNT'		=> $iteration,
		));

		for ($i = 0; $i < $padding; $i++)
		{
			$template->assign_block_vars('jumpbox_arcade.level', array());
		}
		$iteration++;
	}
	$db->sql_freeresult($result);
	unset($padding_store);

	$template->assign_vars(array(
		'S_DISPLAY_JUMPBOX'	=> $display_jumpbox,
		'S_JUMPBOX_ACTION'	=> $action)
	);

	return;
}

?>