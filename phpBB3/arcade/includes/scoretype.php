<?php
/**
*
* @package arcade
* @version $Id: scoretype.php 1663 2011-09-22 12:09:30Z killbill $
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

// Start session management here so that we can set update session page to false
// This is done so that the view online from phpBB will show the correct location
$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

include($phpbb_root_path . 'arcade/includes/common.' . $phpEx);
// Initialize arcade auth
$auth_arcade->acl($user->data);
// Initialize arcade class
$arcade = new arcade();

if (($arcade_config['arcade_disable'] && !$auth->acl_get('a_')) || !$auth_arcade->acl_get('u_arcade'))
{
	redirect($arcade->url());
}

$error_msg = $prepare_score = false;
$arcade->session_begin();

// If playing the game in a popup window only use simple header when calling trigger_error()
$popup = $use_simple_header = $arcade->game_popup;

	if (empty($arcade->game_sid))
	{
		$error_msg = 'COOKIE_ERROR';
	}

	if (isset($scoretype))
	{
		$scoretype = constant($scoretype);

		switch ($scoretype)
		{
			case AMOD_GAME:
				// Only allow games that use $_POST method.  The AMOD games that use $_GET method are very easy to cheat at.
				$game_scorevar = (isset($_POST['game_name'])) ? request_var('game_name', '') : '';
				$score = (isset($_POST['score'])) ? request_var('score', 0.000) : 0;
				$prepare_score = true;
			break;

			case IBPRO_GAME:
				$game_scorevar = (isset($_POST['gname'])) ? request_var('gname', '') : '';
				$score = (isset($_POST['gscore'])) ? request_var('gscore', 0.000) : 0;
				$prepare_score = true;
			break;

			case V3ARCADE_GAME:
				$game_scorevar = (isset($_POST['gamename'])) ? request_var('gamename', '') : '';
				$micro_one = (isset($_POST['microone'])) ? request_var('microone', '') : '';
				$score = (isset($_POST['score'])) ? request_var('score', 0.000) : 0;
				$fake_key = (isset($_POST['fakekey'])) ? request_var('fakekey', '') : '';

				switch ($v3arcade)
				{
					case 'sessionstart':
						$initbar =  $game_scorevar . '|' . $arcade->game_sid;
						$arcade->set_header_no_cache();
						echo '&connStatus=1&initbar='. $initbar .'&val=x';
						garbage_collection();
						exit_handler();
					break;

					case 'permrequest':
						$arcade->set_header_no_cache();
						echo '&validate=1&microone='. $score .'|'. $fake_key .'&val=x';
						garbage_collection();
						exit_handler();
					break;

					case 'burn':
						if ($error_msg === false)
						{
							$data 	= explode('|', $micro_one);

							if ($arcade->game_sid != $data[2])
							{
								$error_msg = 'COOKIE_ERROR';
								break;
							}

							$game_scorevar	= str_replace("\'", "''", htmlspecialchars(trim($data[1])));
							$score 	= (float) $data[0];
							$prepare_score = true;
						}
					break;
				}
			break;

			case IBPROV3_GAME:
				$do = request_var('do','');

				if ($do == 'verifyscore')
				{
					$randchar = $arcade->add_game_randchar();
					$arcade->set_header_no_cache();
					echo '&randchar=' . $randchar['randchar1'] . '&randchar2=' . $randchar['randchar2'] . '&savescore=1&blah=OK';
					garbage_collection();
					exit_handler();
				}
				else if (($error_msg === false) && ($do == 'savescore' || $do == 'newscore'))
				{
					$score 		= (isset($_POST['gscore']))		? request_var('gscore',  0.000) : 0;
					$enscore 	= (isset($_POST['enscore']))	? request_var('enscore', 0.000) : false;
					$gidencoded	= (isset($_POST['arcadegid']))	? request_var('arcadegid',   0) : false;

					$sql = 'SELECT game_id, randchar1, randchar2, randgid1, randgid2
							FROM ' . ARCADE_SESSIONS_TABLE . "
							WHERE session_id = '" . $db->sql_escape($arcade->game_sid) . "'
							AND game_type = " . IBPROV3_GAME;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if (!$row)
					{
						$error_msg = 'BACK_BUTTON_ERROR';
						break;
					}

					$game_scorevar	= $arcade->get_game_field((int) $row['game_id'], 'game_scorevar');
					$decodescore	= (float) $score * $row['randchar1'] ^ $row['randchar2'];
					$encoded_gid	= (int) $row['game_id'] * $row['randgid1'] ^ $row['randgid2'];

					if ($enscore != $decodescore || (file_exists($phpbb_root_path . 'arcade/gamedata/' . $game_scorevar . '/v32game.txt') && $gidencoded != $encoded_gid))
					{
						$error_msg = 'IBPROV3_ERROR';
						break;
					}

					$prepare_score = true;
				}
			break;

			case AR_GAME:
				// Only allow games that use $_POST method.
				$game_scorevar = (isset($_POST['g'])) ? request_var('g', 0) : '';
				$score = (isset($_POST['score'])) ? request_var('score', 0.000) : 0;

				if ($game_scorevar <= 0)
				{
					$game_scorevar = '';
				}
				else
				{
					$prepare_score = true;
				}
			break;
		}
	}

	if (!$error_msg && $prepare_score)
	{
		if (empty($game_scorevar))
		{
			$error_msg = 'SCOREVAR_ERROR';
		}
		else
		{
			$game_data = $arcade->prepare_score($game_scorevar, $scoretype);
			require($phpbb_root_path . 'arcade/includes/score.'.$phpEx);
		}
	}

	if ($error_msg)
	{
		trigger_error($user->lang['ARCADE_' . $error_msg] . ((!$arcade->game_popup) ? $arcade->back_link() : ''));
	}

	$arcade->delete_session();

	// Something went wrong lets send them to the arcade index.
	redirect($arcade->url());

?>