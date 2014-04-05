<?php
/**
*
* @package arcade
* @version $Id: play.php 1663 2011-09-22 12:09:30Z killbill $
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

// Grab the data for game and navlinks
$sql_array = array(
	'SELECT'	=> 'g.game_id, g.game_name, g.game_desc, g.game_image, g.game_swf, g.game_width, g.game_height, g.game_scoretype, g.game_type, game_download, g.game_filesize, g.game_votetotal, g.game_votesum, g.game_cost, g.game_reward, g.game_jackpot, g.game_use_jackpot, g.game_plays, r.game_rating, c.*',

	'FROM'		=> array(
		ARCADE_GAMES_TABLE	=> 'g',
	),

	'LEFT_JOIN'	=> array(
		array(
			'FROM'	=> array(ARCADE_RATING_TABLE => 'r'),
			'ON'	=> 'r.game_id = g.game_id AND r.user_id = ' . (int) $user->data['user_id']
		),
		array(
			'FROM'	=> array(ARCADE_CATS_TABLE => 'c'),
			'ON'	=> 'g.cat_id = c.cat_id'
		),
	),

	'WHERE'		=> 'g.game_id = ' . (int) $game_id,
);

// This is only needed if we are playing in a popup
$popup = $use_simple_header = false;
if ($mode == 'popup')
{
	$popup = $use_simple_header = true;
	$sql_array['SELECT'] .= ', g.game_highscore, u.username';
	$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE => 'u'),	'ON' => 'g.game_highuser = u.user_id');
}

$sql = $db->sql_build_query('SELECT', $sql_array);

$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$row || !$auth_arcade->acl_get('c_list', $row['cat_id']))
{
	trigger_error($user->lang['NO_GAME_ID'] . ((!$popup) ? $arcade->back_link() : ''));
}

$gidencoded = $arcade->session_begin(true, $game_id, $row['game_type'], $popup);

$s_hidden_fields = build_hidden_fields(array(
	'mode'	=> $mode,
	'g'		=> $game_id,
	'start'	=> true,
));

$template->assign_var('S_HIDDEN_FIELDS', $s_hidden_fields);

if ((!sizeof($gidencoded)))
{
	$template->assign_vars(array(
		'S_RESTART_GAME'		=> true,
		'S_USE_SIMPLE_HEADER'	=> $use_simple_header,
		
		'MESSAGE_TITLE'			=> $user->lang['CONFIRM'],
		'MESSAGE_TEXT'			=> sprintf($user->lang['ARCADE_RESTART_GAME_EXPLAIN'], $row['game_name']),
		'L_ARCADE_RESTART_GAME'	=> sprintf($user->lang['ARCADE_RESTART_GAME'], $row['game_name']),
	));

	page_header(sprintf($user->lang['ARCADE_RESTART_GAME'], $row['game_name']), false);

	$html_body = ($mode == 'play') ? 'play_body' : 'popup_body';

	$template->set_filenames(array(
		'body' => 'arcade/message_body.html')
	);

	page_footer();
}

if (!$popup && $row['cat_style'] && ($user->data['user_style'] != $row['cat_style']))
{
	$user->setup('', $row['cat_style']);
}

generate_arcade_nav($row, true);

if (!$auth_arcade->acl_get('c_play', $row['cat_id']))
{
	trigger_error($user->lang['NO_PERMISSION_ARCADE_PLAY'] . ((!$popup) ? $arcade->back_link() : ''));
}

if ($row['cat_status'] == ITEM_LOCKED)
{
	trigger_error($user->lang['ARCADE_CAT_LOCKED_ERROR'] . ((!$popup) ? $arcade->back_link() : ''));
}

// Check flood control
$interval = ($user->data['user_id'] == ANONYMOUS) ? $arcade_config['play_anonymous_interval'] : $arcade_config['play_interval'];
if ($interval && !$auth_arcade->acl_get('c_ignoreflood_play', $row['cat_id']) && strpos($user->data['session_browser'] , 'www.facebook') === false)
{
	$time = time() - $interval;
	if ($user->data['user_arcade_last_play'] > $time)
	{
		trigger_error(sprintf($user->lang['ARCADE_NO_PLAY_TIME'], $arcade->time_format($interval, true), $arcade->time_format($user->data['user_arcade_last_play'] - $time, true)) . ((!$popup) ? $arcade->back_link() : ''));
	}
}

// Check if the limt play is set in ACP
$arcade->limit_play($row['cat_id']);

$page_title = $user->lang['ARCADE'] . ' - ' . $row['cat_name'] . ' - ' . $row['game_name'];

// age limit verify
if ($row['cat_age'])
{
	$arcade->verify_age('play', $row['cat_age']);
}

// Category is passworded ... check whether access has been granted to this
// user this session, if not show login box before playing the game
if ($row['cat_password'])
{
	login_arcade_box($row);
}

if ($row['cat_test'])
{
	$template->assign_var('S_ARCADE_CAT_TEST_MODE', true);
}

// Increase play count for games that do not submit scores
// only if we are not in a test category, we are basically
// only counting views for non scoring games and the users
// play count and time will not be increased. Currently if
// a NOSCORE_GAME is resynced it will reset the plays back
// to zero because there will be no data inside the plays
// table to accurately resync with.
if ($row['game_type'] == NOSCORE_GAME && !$row['cat_test'])
{
	$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
		SET game_plays = game_plays + 1
		WHERE game_id = ' . (int) $game_id;
	$db->sql_query($sql);
	$cache->destroy('sql', ARCADE_GAMES_TABLE);
}

// Handle points if needed and if we are not in a test category
if ($arcade->points['show'] && !$row['cat_test'])
{
	$game_cost = $arcade->get_cost($row);
	$game_reward = $arcade->get_reward($row);

	if (!$auth_arcade->acl_get('c_playfree', $row['cat_id']) && $game_cost != ARCADE_FREE)
	{
		if (!$arcade->set_points('subtract', $user->data['user_id'], $game_cost))
		{
			trigger_error(sprintf($user->lang['ARCADE_NO_POINTS'], $arcade->points['name']) . ((!$popup) ? $arcade->back_link() : ''));
		}

		// The jackpot is only increased if the user actually contributed something to play
		if ($arcade->use_jackpot($row))
		{
			$game_reward = $arcade->set_jackpot('add', $row);
		}
	}
}

$game_width = $game_height = 0;
$arcade->set_game_size($game_width, $game_height, $row['game_width'], $row['game_height'], $row['game_swf']);
$game_file = $file_functions->remove_extension($row['game_swf']);
$game_swf = ($arcade->get_protection($row['game_type'])) ? $arcade->url("swf=" . $game_file) : $arcade->set_path($row['game_swf']);

switch($mode)
{
	case 'play':
		$score_order		 = ($row['game_scoretype'] == SCORETYPE_HIGH) ? 'DESC' : 'ASC';
		$game_fav_data		 = (!isset($game_fav_data)) ? $arcade->get_fav_data() : $game_fav_data;
		$s_resolution_select = ($arcade_config['resolution_select'] && $auth_arcade->acl_get('c_resolution', $row['cat_id'])) ? true : false;

		$template->assign_vars(array(
			'S_RESOLUTION_SELECT'		=> $s_resolution_select,
			'S_GAME_DOWNLOAD' 			=> ($row['cat_download'] && $row['game_download'] && $auth_arcade->acl_get('c_download', $row['cat_id'])) ? true : false,
			'S_CAN_REPORT'	 			=> ($auth_arcade->acl_get('c_report', $row['cat_id'])) ? true : false,
			'S_DISPLAY_DESC'			=> $arcade_config['display_desc'],
			'S_DISPLAY_GAME_TYPE'		=> $arcade_config['display_game_type'],
			'S_ARCADE_FACEBOOK'			=> ($arcade_config['facebook_enable_like']) ? true : false,
			'S_FEELING_LUCKY'			=> ($auth_arcade->acl_getc_global('c_play')) ? true : false,

			'U_FEELING_LUCKY'			=> $arcade->url('mode=random'),
			'U_GAME_DOWNLOAD'			=> $arcade->url("mode=download&amp;g={$row['game_id']}"),
			'U_GAME_REPORT'				=> $arcade->url("mode=report&amp;g={$row['game_id']}"),
			'U_GAME_EDIT'				=> ($auth->acl_get('a_arcade_game') && !empty($user->data['is_registered'])) ? $arcade->url("i=arcade_games&amp;mode=edit_games&amp;action=edit&amp;g={$row['game_id']}", "adm/index", $user->session_id) : '',
			'U_SCORE_EDIT'				=> ($auth->acl_get('a_arcade_scores') && $row['game_plays'] && !empty($user->data['is_registered'])) ? $arcade->url("i=arcade_games&amp;mode=edit_scores&amp;action=show_scores&amp;g={$row['game_id']}", "adm/index", $user->session_id) : '',

			'GAME_DESC'					=> censor_text(nl2br($row['game_desc'])),
			'GAME_TYPE'					=> $arcade->display_game_type($row['game_type']),
			'GAME_FILESIZE'				=> ($row['game_filesize'] > 0 ) ? sprintf($user->lang['ARCADE_GAMES_FILESIZE'], get_formatted_filesize($row['game_filesize'])) : sprintf($user->lang['ARCADE_GAMES_FILESIZE'], get_formatted_filesize($arcade->set_filesize($row['game_id']))),
			'GAME_FAV_IMG'				=> $arcade->set_fav_image($game_fav_data, $row['game_id']),
			'GAME_RATING_IMG'			=> $arcade->set_rating_image($row),
			'SMALL_IMAGE'				=> $arcade->get_image('src', 'img', 'small.png'),
			'BIG_IMAGE'					=> $arcade->get_image('src', 'img', 'big.png'),
			'AUTO_IMAGE'				=> $arcade->get_image('src', 'img', 'automatic.png'),
			'OPEN_CLOSE_IMAGE'			=> $arcade->get_image('src', 'img', 'open_close.png'),
			'L_RESTART_GAME'			=> sprintf($user->lang['ARCADE_RESTART_GAME'], $row['game_name'])
		));

		if ($arcade_config['facebook_enable_like'])
		{
			$keys = array('c_play', 'c_list', 'c_playfree');
			$perms = $auth_arcade->acl_raw_data(ANONYMOUS, $keys, $row['cat_id']);
			$facebook_active = true;

			foreach ($keys as $key)
			{
				if (!isset($perms[ANONYMOUS][$row['cat_id']][$key]) || !$perms[ANONYMOUS][$row['cat_id']][$key])
				{
					$facebook_active = false;
				}
			}

			if ($facebook_active && $auth_arcade->acl_raw_data(ANONYMOUS, 'u_arcade'))
			{
				$game_urlencode		= urlencode(generate_board_url() . "/arcade.{$phpEx}?mode=play&amp;g={$row['game_id']}" . $arcade->gametop);
				$facebook_url		= "http://www.facebook.com/plugins/like.{$phpEx}";
				$facebook_width		= ($arcade_config['facebook_layout_style'] == 'standard') ? $arcade_config['facebook_table_width'] : 90;
				$facebook_height	= ($arcade_config['facebook_layout_style'] == 'standard' && $arcade_config['facebook_show_faces']) ? ($arcade_config['facebook_table_height'] + 80) : (($arcade_config['facebook_layout_style'] == 'standard') ? ($arcade_config['facebook_table_height'] + 35) : (($arcade_config['facebook_layout_style'] == 'button_count') ? 21 : 65));
				$params				= "?href={$game_urlencode}&amp;layout={$arcade_config['facebook_layout_style']}&amp;show_faces={$arcade_config['facebook_show_faces']}&amp;width={$facebook_width}&amp;action=like&amp;colorscheme={$arcade_config['facebook_color_scheme']}&amp;height={$facebook_height}&amp;font={$arcade_config['facebook_font_type']}";

				$template->assign_vars(array(
					'S_ARCADE_FB'		=> true,

					'GAME_URLENCODE'	=> $game_urlencode,
					'FACEBOOK_IKON'		=> $arcade->get_image('src', 'img', 'facebook.gif'),
					'ARCADE_FB_SRC'		=> $facebook_url . $params,
					'ARCADE_FB_ADD'		=> 'allowTransparency="true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $facebook_width . 'px; height:' . $facebook_height . 'px; ' . ($arcade_config['facebook_layout_style'] != 'standard' ? 'padding-left:20px;' : '') . '"',
					'ARCADE_FB_BGC'		=> $arcade_config['facebook_background_color'] ? $arcade_config['facebook_background_color'] : false,
					'ARCADE_FB_GN'		=> sprintf($user->lang['ARCADE_GAME_NAME_FLASH_GAME'], $row['game_name']),
					'ARCADE_FB_GI'		=> generate_board_url() . "/arcade.$phpEx?img=" . $row['game_image']
				));
			}
		}

		if ($arcade->points['show'] && !$row['cat_test'])
		{
			$template->assign_vars(array(
				'S_SHOW_POINTS'		=> true,
				'S_USE_JACKPOT'		=> ($arcade->use_jackpot($row)) ? true : false,

				'USER_POINTS'		=> $arcade->number_format($arcade->points['total']) . ' ' . $arcade->points['name'],
				'POINTS_NAME'		=> $arcade->points['name'],
				'GAME_COST'			=> ($game_cost == ARCADE_FREE) ? $user->lang['ARCADE_FREE'] : $arcade->number_format($game_cost) . ' ' . $arcade->points['name'],
				'GAME_REWARD'		=> ($game_reward == ARCADE_FREE) ? $user->lang['ARCADE_NONE'] : $arcade->number_format($game_reward) . ' ' . $arcade->points['name'],
				)
			);
		}

		$sql_array = array(
			'SELECT'	=> 's.*, p.total_time, p.total_plays, u.username, u.user_id, u.user_colour, u.user_avatar_type, u.user_avatar, u.user_avatar_width, u.user_avatar_height',

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

			'WHERE'		=> 's.game_id = ' . (int) $game_id,

			'ORDER_BY'	=> 's.score ' . $score_order . ', s.score_date ASC',
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$score_data = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		if (!empty($score_data))
		{
			$position = $actual_position = $lastscore = 0;
			foreach ($score_data as $data)
			{
				$actual_position++;
				if ($user->data['user_id'] != $data['user_id'] && $actual_position > $arcade_config['game_scores'])
				{
					continue;
				}

				// Handle the game champion
				if ($actual_position == 1)
				{
					$first_avatar = ($user->optionget('viewavatars')) ? true : false;

					if ($first_avatar)
					{
						if (!($first_avatar = get_user_avatar($data['user_avatar'], $data['user_avatar_type'], $data['user_avatar_width'], $data['user_avatar_height'], $data['username'])))
						{
							$first_avatar = '<img src="' . $arcade->get_image('src', 'img', 'noavatar.gif') . '" alt="' . $data['username'] . '" title="' . $data['username'] . '" />';
						}

						$first_avatar = ($data['user_id'] != ANONYMOUS && $auth_arcade->acl_get('u_viewstats') && $first_avatar) ? '<a href="' . $arcade->url("mode=stats&amp;u={$data['user_id']}") . '">' . $first_avatar . '</a>' : $first_avatar;
					}

					$template->assign_vars(array(
						'S_USER_ID'			=> $user->data['user_id'],
						'BEST_USER_NAME'	=> $arcade->get_username_string('full', $data['user_id'], $data['username'], $data['user_colour']),
						'BEST_COMMENT'		=> ($data['comment_text'] != '') ? generate_text_for_display(censor_text($data['comment_text']), $data['comment_uid'], $data['comment_bitfield'], $data['comment_options']) : '',
						'BEST_PLAYED'		=> $arcade->number_format($data['total_plays']),
						'BEST_TIME'			=> $arcade->time_format($data['total_time']),
						'BEST_SCORE'		=> $arcade->number_format($data['score']),
						'BEST_DATE_EXPLAIN'	=> sprintf($user->lang['ARCADE_BEST_DATE_EXPLAIN'], $user->format_date($data['score_date'])),
						'FIRST_AVATAR'		=> $first_avatar,
					));
				}

				if ($lastscore != $data['score'])
				{
					$position = $actual_position;
				}
				$lastscore = $data['score'];

				$template->assign_block_vars('scorerow', array(
					'POS' 		=> $position,
					'USER_ID'	=> $data['user_id'],
					'USERNAME' 	=> $arcade->get_username_string('full', $data['user_id'], $data['username'], $data['user_colour']),
					'SCORE' 	=> $arcade->number_format($data['score']))
				);

				// We break here because we no longer need to loop through the data, we have what we need
				if ($user->data['user_id'] == $data['user_id'] && $actual_position > $arcade_config['game_scores'])
				{
					break;
				}
			}
		}
	break;

	case 'popup':
		if (!$auth_arcade->acl_get('c_popup', $row['cat_id']))
		{
			trigger_error($user->lang['NO_PERMISSION_ARCADE_PLAY_POPUP'] . ((!$popup) ? $arcade->back_link() : ''));
		}

		$template->assign_var('POPUP_TITLE', ($row['username'] != '') ? sprintf($user->lang['ARCADE_POPUP_HIGHUSER'], $row['game_name'], $arcade->number_format($row['game_highscore']), $row['username']) : sprintf($user->lang['ARCADE_POP_NO_HIGHSCORE'], $row['game_name']));
	break;
}

// We will set this cookie to let the arcade know that the user is playing the game
// in a popup window and not on fullscreen. It will later be checked in score.php
$arcade->update_last_play_time($game_id);

$template->assign_vars(array(
	'L_ARCADE_FLASH_VERSION'=> sprintf($user->lang['ARCADE_FLASH_VERSION'], $arcade_config['flash_version']),

	'T_ARCADE_FLASH_UPDATE'	=> (file_exists($phpbb_root_path . 'arcade/swf/flash_player_update.swf')) ? $arcade->url("swf=flash_player_update&amp;p=1") : false,

	'S_ARCADE_GAME_PLAY'	=> true,
	'S_USE_HIGHSCORES'		=> ($row['game_type'] == NOSCORE_GAME) ? false : true,
	'S_POPUP'				=> ($mode == 'popup') ? true : false,

	'GAME_NAME'				=> $row['game_name'],
	'GAME_ID'				=> $row['game_id'],
	'GAME_WIDTH'			=> $game_width,
	'GAME_HEIGHT'			=> $game_height,
	'GAME_SWF'				=> $game_swf,
	'GAME_SID'				=> $arcade->game_sid,
	'GAME_GID_ENCODED'		=> $gidencoded,
	'GAME_IBPROV3'			=> ($row['game_type'] == IBPROV3_GAME) ? 'yes' : 'no',

	'FLASH_VERSION'			=> $arcade_config['flash_version'],
));

if ($mode == 'play')
{
	display_arcade_online();
}

page_header($page_title, false);

$html_body = ($mode == 'play') ? 'play_body' : 'popup_body';

$template->set_filenames(array(
	'body' => 'arcade/' . $html_body . '.html')
);

page_footer();

?>