<?php
/**
*
* @package arcade
* @version $Id: score.php 1663 2011-09-22 12:09:30Z killbill $
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

// If you are not logged in the score is never saved...
// Lets show you a nice message and send you on your way.
if (!$user->data['is_registered'])
{
	$arcade->update_last_play_time($game_data['game_id']);

	$total_games = $arcade->get_total('games');
	$message = sprintf($user->lang['ARCADE_REGISTER_MESSAGE_SCORE'], $total_games);

	if (!$popup)
	{
		$message .= '<br />' . sprintf($user->lang['ARCADE_REGISTER_LOGIN_MESSAGE'], '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", "mode=register") . '">', '</a>', '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login&amp;redirect=' . urlencode("arcade.$phpEx")) . '">', '</a>');
	}

	trigger_error($message);
}

$mode = (isset($mode)) ? $mode : '';

// We can't continue if we don't have a game_id
if (($mode == 'score' || $mode == 'done') && !$game_id)
{
	redirect(append_sid("{$phpbb_root_path}arcade.$phpEx"));
}

// This handles the data from the screen you see immediately after playing a game.
// The handling of the comments are done here.
if ($mode == 'score' && $game_id)
{
	if (!check_form_key('arcade_score'))
	{
		trigger_error($user->lang['FORM_INVALID'] . ((!$popup) ? $arcade->back_link() : ''));
	}

	$cat_id = $arcade->get_game_field($game_id, 'cat_id');
	if (!$cat_id || !$auth_arcade->acl_get('c_score', $cat_id))
	{
		trigger_error($user->lang['NO_PERMISSION_ARCADE_SCORE'] . ((!$popup) ? $arcade->back_link() : ''));
	}

	if ($auth_arcade->acl_get('c_comment', $cat_id))
	{
		$comment_data = array(
			'comment_text' 		=> utf8_normalize_nfc(request_var('message', '', true)),
			'comment_bitfield'	=> '',
			'comment_options'	=> 7,
			'comment_uid'		=> '',
		);

		generate_text_for_storage($comment_data['comment_text'], $comment_data['comment_uid'], $comment_data['comment_bitfield'], $comment_data['comment_options'], $arcade_config['parse_bbcode'], $arcade_config['parse_links'], $arcade_config['parse_smilies']);

		$sql = 'UPDATE ' . ARCADE_SCORES_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $comment_data) . '
				WHERE user_id = ' . (int) $user->data['user_id'] . '
				AND   game_id = ' . (int) $game_id;
		$db->sql_query($sql);
	}

	// Everything is all set so lets display the finished screen...
	redirect($arcade->url("mode=done&amp;g={$game_id}#game_done"));
}
// This displays the messages and links that are shown after
// the comments have been selected.
else if ($mode == 'done' && $game_id)
{
	// Retrieves all the information for the game in question...
	$game_info = $arcade->get_game_data($game_id);

	if (!$auth_arcade->acl_get('c_score', $game_info['cat_id']))
	{
		trigger_error($user->lang['NO_PERMISSION_ARCADE_SCORE'] . ((!$popup) ? $arcade->back_link() : ''));
	}

	generate_arcade_nav($game_info, true);

	if ($popup)
	{
		$template->assign_var('S_USE_SIMPLE_HEADER', true);
		$game_url = $arcade->url("mode=popup&amp;g={$game_id}");
		$message  = sprintf($user->lang['ARCADE_POPUP_DONE'], $game_info['game_name'], '<a href="' . $game_url . '">', $game_info['game_name'], '</a>', '<a href="#" onclick="arcade_refresh_page(\''. $arcade->url("mode=cat&amp;c={$game_info['cat_id']}&amp;g={$game_info['game_id']}#g{$game_info['game_id']}") .'\'); return false;">', '</a>');
	}
	else
	{
		$game_url = $arcade->url("mode=play&amp;g={$game_id}");
		$message  = sprintf($user->lang['ARCADE_FULL_DONE'], $game_info['game_name']) . $arcade->return_links($game_info);
	}

	$game_width = $game_height = 0;
	$arcade->set_game_size($game_width, $game_height, $game_info['game_width'], $game_info['game_height'], $game_info['game_swf']);

	$template->assign_vars(array(
		'S_SCORE_DONE'		=> true,

		'L_ARCADE_DONE' 	=> $message,

		'GAME_RATING_IMG'	=> $arcade->set_rating_image($game_info),
		'GAME_IMAGE' 		=> $arcade->url("img=" . $game_info['game_image']),
		'GAME_NAME' 		=> $game_info['game_name'],
		'GAME_URL' 			=> $game_url,
		'GAME_WIDTH'		=> $game_width,
		'GAME_HEIGHT'		=> $game_height
	));

	display_arcade_online();

	// Output page
	page_header($user->lang['INDEX'], false);

	$template->set_filenames(array(
		'body' => 'arcade/score_body.html'
	));

	page_footer();
}

if (!$auth_arcade->acl_get('c_score', $game_data['cat_id']))
{
	trigger_error($user->lang['NO_PERMISSION_ARCADE_SCORE'] . ((!$popup) ? $arcade->back_link() : ''));
}

generate_arcade_nav($game_data, true);

$error = false;
$lang = '';
if ($arcade_config['game_zero_negative_score'])
{
	if (!isset($score))
	{
		$error = true;
		$lang = 'ARCADE_SCORE_ERROR';
	}
}
else
{
	if (!isset($score) || $score <= 0)
	{
		$error = true;
		$lang = 'ARCADE_ZERO_NEGATIVE_SCORE';
	}
}

if ($game_data['cat_test'])
{
	$template->assign_var('S_ARCADE_CAT_TEST_MODE', true);
}

if ($error || $game_data['cat_test'])
{
	if ($game_data['cat_test'])
	{
		$message = sprintf($user->lang['ARCADE_TEST_SCORE'], $score) . $arcade->return_links($game_data, true, $popup);
		if ($auth->acl_get('a_') && defined('DEBUG_EXTRA'))
		{
			$message .= '<br /><br />' . arcade_debug($game_data);
		}
	}
	else
	{
		$message = $user->lang[$lang] . $arcade->return_links($game_data, true, $popup);
	}

	trigger_error($message);
}

$sql = 'SELECT score, comment_text, comment_bitfield, comment_options, comment_uid
		FROM ' . ARCADE_SCORES_TABLE . '
		WHERE game_id = ' . (int) $game_data['game_id'] . '
		AND   user_id = ' . (int) $user->data['user_id'];
$result = $db->sql_query($sql);
$saved_score = false;
$saved_highscore = false;
$reward = false;

// If the user has no current score we insert it.
if (!($row = $db->sql_fetchrow($result)))
{
	$sql_ary = array(
		'game_id'		=> (int) $game_data['game_id'],
		'user_id'		=> (int) $user->data['user_id'],
		'score'			=> (float) $score,
		'score_date'	=> (int) $game_data['current_time'],
		'comment_text'	=> '',
	);

	$db->sql_query('INSERT INTO ' . ARCADE_SCORES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

	$saved_score = true;
}
else
{
	$old_score = $row['score'];
	// So you have an old score, is you new one better?  (Make sure we check the scoretype)
	if (($game_data['game_scoretype'] == SCORETYPE_HIGH && $old_score < $score) || ($game_data['game_scoretype'] == SCORETYPE_LOW && $old_score > $score))
	{
		// Update score data
		$sql_ary = array(
			'score'			=> $score,
			'score_date'	=> $game_data['current_time']
		);

		$sql = 'UPDATE ' . ARCADE_SCORES_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE game_id = ' . (int) $game_data['game_id'] . '
				AND user_id   = ' . (int) $user->data['user_id'];
		$db->sql_query($sql);

		$saved_score = true;
	}
}

$comment_data = generate_text_for_edit($row['comment_text'], $row['comment_uid'], $row['comment_options']);
$db->sql_freeresult($result);

// Update play data
$sql = 'UPDATE ' . ARCADE_PLAYS_TABLE . '
		SET total_plays = total_plays + 1, total_time = total_time + ' . (int) $game_data['total_time'] . '
		WHERE game_id = ' . (int) $game_data['game_id'] . '
		AND user_id   = ' . (int) $user->data['user_id'];
$db->sql_query($sql);

if (!$db->sql_affectedrows())
{
	$sql = 'INSERT INTO ' . ARCADE_PLAYS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
		'game_id'		=> $game_data['game_id'],
		'user_id'		=> $user->data['user_id'],
		'total_time'	=> $game_data['total_time'],
		'total_plays'	=> 1,
	));
	$db->sql_query($sql);
}

$arcade->update_last_play_time($game_data['game_id']);
$arcade->set_config('total_plays', ($arcade_config['total_plays'] + 1), true);
$arcade->set_config('total_plays_time', ($arcade_config['total_plays_time'] + (int) $game_data['total_time']), true);

// Here we update the category information, this is done to save SQL queries
$sql_ary = array(
	'cat_last_play_game_id'		=> $game_data['game_id'],
	'cat_last_play_game_name'	=> $game_data['game_name'],
	'cat_last_play_user_id'		=> $user->data['user_id'],
	'cat_last_play_score'		=> $score,
	'cat_last_play_time'		=> $game_data['current_time'],
	'cat_last_play_username'	=> $user->data['username'],
	'cat_last_play_user_colour'	=> $user->data['user_colour'],
);

$sql = 'UPDATE ' . ARCADE_CATS_TABLE . '
		SET cat_plays = cat_plays + 1, ' .
		$db->sql_build_array('UPDATE', $sql_ary) . '
		WHERE cat_id = ' . (int) $game_data['cat_id'];
$db->sql_query($sql);

// Here we update the total times a game has been played
$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
		SET game_plays = game_plays + 1
		WHERE game_id = ' . (int) $game_data['game_id'];
$db->sql_query($sql);

// This needs to be checked incase the game has no score yet and we are scoring lowest to highest
// If this was not checked there would never be a highscore holder for games that score
// lowest to highest.
$has_highscore = ($game_data['game_highuser'] == 0 && $game_data['game_highdate'] == 0) ? false : true;
$first_score = (!$has_highscore) ? true : false;
$game_data['game_highscore'] = ($game_data['game_scoretype'] == SCORETYPE_LOW && !$has_highscore) ? $score + 1 : $game_data['game_highscore'];

// Here we check if you are the new highscore holder
if (($game_data['game_scoretype'] == SCORETYPE_HIGH && $game_data['game_highscore'] < $score) || ($game_data['game_scoretype'] == SCORETYPE_LOW && $game_data['game_highscore']  > $score) || ($arcade_config['game_zero_negative_score'] && $score <= 0 && !$has_highscore))
{
	$sql_ary = array(
		'game_highscore'=> $score,
		'game_highuser'	=> $user->data['user_id'],
		'game_highdate'	=> $game_data['current_time'],
	);

	$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE game_id = ' . (int) $game_data['game_id'];
	$db->sql_query($sql);

	$cache->destroy('sql', ARCADE_CATS_TABLE);
	$cache->destroy('sql', ARCADE_GAMES_TABLE);
	$cache->destroy('sql', ARCADE_SCORES_TABLE);
	$cache->destroy('_arcade_leaders');
	$cache->destroy('_arcade_leaders_all');

	$saved_highscore = true;

	$sql = 'SELECT user_arcade_pm
			FROM ' . ARCADE_USERS_TABLE . '
			WHERE user_id = ' . (int) $game_data['game_highuser'];
	$result = $db->sql_query($sql);
	$user_arcade_pm = (int) $db->sql_fetchfield('user_arcade_pm');
	$db->sql_freeresult($result);

	if ($arcade->points['show'])
	{
		$reward = $arcade->get_reward($game_data);
		if ($reward == ARCADE_FREE || ($auth_arcade->acl_get('c_playfree', $game_data['cat_id']) && !$arcade_config['playfree_reward']))
		{
			$reward = false;
		}
		else
		{
			// If we are using the jackpot setting we must clear the jackpot
			if ($arcade->use_jackpot($game_data))
			{
				$arcade->set_jackpot('clear', $game_data);
			}
			$arcade->set_points('add', $user->data['user_id'], $reward);
		}
	}

	// We don't need to send a pm if there was never a highscore...
	// We don't need to send a pm if we beat our own score...
	if ($game_data['game_highuser'] && $arcade_config['send_arcade_pm'] && $user_arcade_pm && ($game_data['game_highuser'] != $user->data['user_id']))
	{
		// This sets the score data that is available to use
		// in the pm sent when you lose a highscore.  It
		// is customizable in the ACP
		$score_data = array(
			'game_id' 		=> $game_data['game_id'],
			'game_name' 	=> $game_data['game_name'],
			'old_user_id' 	=> $game_data['game_highuser'],
			'old_username' 	=> $game_data['username'],
			'old_score' 	=> $arcade->number_format($game_data['game_highscore']),
			'new_score' 	=> $arcade->number_format($score),
			'game_image'	=> $game_data['game_image'],

			'cat_id'		=> $game_data['cat_id'],
			'game_width'	=> $game_data['game_width'],
			'game_height'	=> $game_data['game_height'],
		);

		$arcade->send_pm($score_data);

	}
}
else
{
	$cache->destroy('sql', ARCADE_CATS_TABLE);
	$cache->destroy('sql', ARCADE_GAMES_TABLE);
	$cache->destroy('sql', ARCADE_SCORES_TABLE);
}

if (!$saved_score)
{
	$score_desc = sprintf($user->lang['ARCADE_NO_SCORE_SAVED'], $arcade->number_format($score), $arcade->number_format($old_score));
}
else if (!$saved_highscore)
{
	$score_desc = sprintf($user->lang['ARCADE_SCORE_SAVED'], $arcade->number_format($score), $arcade->number_format($game_data['game_highscore']));
}
else
{
	if ($first_score)
	{
		$score_desc = sprintf($user->lang['ARCADE_HIGH_SCORE_SAVED_NEW'], $game_data['game_name'], $arcade->number_format($score));
	}
	else
	{
		$score_desc = sprintf($user->lang['ARCADE_HIGH_SCORE_SAVED'], $game_data['game_name'], $arcade->number_format($score), $arcade->number_format($game_data['game_highscore']));
	}
}

if ($arcade->points['show'] && $reward)
{
	$score_desc .= '<br /><br />' . sprintf($user->lang['ARCADE_REWARD_MESSAGE'], $arcade->number_format($reward), $arcade->points['name'], $arcade->points['name'], $arcade->number_format($arcade->points['total']));
}

// User is not allowed to comment the game so display the final score explanation and redirect to the finished page...
if (!$auth_arcade->acl_get('c_comment', $game_data['cat_id']))
{
	$meta_info = $arcade->url("mode=done&amp;g={$game_data['game_id']}#game_done");
	meta_refresh(5, $meta_info);

	$score_desc .= '<br /><br />' . sprintf($user->lang['ARCADE_REDIRECT'], '<a href="' . $meta_info . '">', '</a>');

	trigger_error($score_desc);
}

// Add posting language file.
$user->add_lang('posting');

$s_hidden_fields = build_hidden_fields(array(
	'mode'	=> 'score',
	'g'		=> $game_data['game_id'],
	'c'		=> $game_data['cat_id'],
));

if ($popup)
{
	$template->assign_var('S_USE_SIMPLE_HEADER', true);
}

add_form_key('arcade_score');

// Generate smiley listing
if (!function_exists('generate_smilies'))
{
	include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
}
generate_smilies('inline', false);

// Build custom bbcodes array
if (!function_exists('display_custom_bbcodes'))
{
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
}
display_custom_bbcodes();

$template->assign_vars(array(
	'S_HAS_PERM_COMMENT'		=> $auth_arcade->acl_get('c_comment', $game_data['cat_id']),
	'S_ACTION' 					=> $arcade->url(),
	'S_HIDDEN_FIELDS' 			=> $s_hidden_fields,
	'S_BBCODE_ALLOWED'			=> ($config['allow_bbcode'] && $user->optionget('bbcode') && $arcade_config['parse_bbcode']) ? true : false,
	'S_SMILIES_ALLOWED'			=> ($config['allow_smilies'] && $user->optionget('smilies') && $arcade_config['parse_smilies']) ? true : false,
	'S_BBCODE_IMG'				=> ($config['allow_bbcode'] && $user->optionget('bbcode') && $arcade_config['parse_bbcode']) ? true : false,
	'S_BBCODE_QUOTE'			=> ($config['allow_bbcode'] && $user->optionget('bbcode') && $arcade_config['parse_bbcode']) ? true : false,
	'S_LINKS_ALLOWED'			=> ($config['allow_post_links'] && $arcade_config['parse_links']) ? true : false,
	'S_BBCODE_FLASH'			=> ($config['allow_bbcode'] && $user->optionget('bbcode') && $config['allow_post_flash'] && $arcade_config['parse_bbcode']) ? true : false,

	'ARCADE_SCORE_DESC' 		=> $score_desc,
	'GAME_NAME' 				=> $game_data['game_name'],
	'SCORE_COMMENT' 			=> $comment_data['text'],
	));

display_arcade_online();

// Output page
page_header($user->lang['INDEX'], false);

$template->set_filenames(array(
	'body' => 'arcade/score_body.html')
);

page_footer();

?>