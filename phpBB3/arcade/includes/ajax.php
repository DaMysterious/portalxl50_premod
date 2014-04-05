<?php
/**
*
* @package arcade
* @version $Id: ajax.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

if (!isset($_POST['g']))
{
	exit;
}

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

$game_id = request_var('g', 0);
$rating	 = request_var('r', 0);
$fav	 = request_var('f', 0);
$cat_id	 = request_var('c', 0);
$mode	 = request_var('mode', '');

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-type: text/xml; charset=UTF-8');

include($phpbb_root_path . 'arcade/includes/common.' . $phpEx);
$auth_arcade->acl($user->data);

$set_data = (in_array($mode, array('quick_jump', 'game_jump'))) ? true : false;

$arcade = new arcade(false, $set_data);

echo	"<" . "?xml version='1.0' encoding='UTF-8' ?" . "><phpbbarcade>";

if (!$user->data['is_registered'] && ($rating || $fav))
{
	if ($user->data['is_bot'])
	{
		_arcade_print('error', 'Exit');
	}
	else
	{
		_arcade_print('error', 'session_time_end');
	}

		echo "</phpbbarcade>";

	exit;
}

	$image = $info = '';
	$user_id = (int) $user->data['user_id'];

	if ($game_id && $cat_id && $rating)
	{
		$sql_ary = array(
			'game_id'		=> $game_id,
			'user_id'		=> $user_id,
			'game_rating'	=> $rating,
			'rating_date'	=> time(),
			'user_ip'		=> $user->ip);

		$sql = 'UPDATE ' . ARCADE_RATING_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE game_id = ' . $game_id . '
				AND user_id	  = ' . $user_id;
		$db->sql_query($sql);

		if (!$db->sql_affectedrows())
		{
			$db->sql_return_on_error(true);
			$db->sql_query('INSERT INTO ' . ARCADE_RATING_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));
			$db->sql_return_on_error(false);

			$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
					SET game_votetotal	= game_votetotal + 1,
						game_votesum	= game_votesum + ' . $rating . '
					WHERE game_id = ' . $game_id;
			$db->sql_query($sql);
		}

		$arcade->sync('rating', $game_id);

		$sql_array = array(
			'SELECT'	=> 'g.game_votetotal, g.game_votesum, r.game_rating',
			'FROM'		=> array(ARCADE_RATING_TABLE => 'r'),
			'LEFT_JOIN'	=> array(array(
			'FROM'		=> array(ARCADE_GAMES_TABLE	 => 'g'),
			'ON'		=> 'r.game_id = g.game_id')),
			'WHERE'		=> 'r.game_id = ' . $game_id . ' AND r.user_id = ' . $user_id);

		$result	= $db->sql_query($db->sql_build_query('SELECT', $sql_array));
		$row	= $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$c_new_rate = $auth_arcade->acl_get('c_re_rate', $cat_id) ? true : false;

		if ($row['game_votetotal'] > 0)
		{
			$star_width = (int) (($row['game_votesum'] / $row['game_votetotal']) * 16);
		}
		else
		{
			$star_width = 0;
		}

		$title = (!$c_new_rate && !empty($row['game_rating'])) ? sprintf($user->lang['ARCADE_RATING_ALREADY'], $rating) : '';

		$image = "<ul class='arcade-rate'" . ($title ? " title='{$title}'" : "") . ">
					<li class='arcade-current-rate' style='width:{$star_width}px;'></li>
					<li class='arcade-rating-num'><span title='{$user->lang['ARCADE_RATING_NUM']}'>({$row['game_votetotal']})</span></li>";

		if ($c_new_rate)
		{
			for ($x = 1; $x <= 5; $x++)
			{
				$image.= "<li><a onclick='arcade(\"rating\", \"{$game_id}\", \"{$x}\", \"{$cat_id}\", \"{$mode}\");return false;' title='" . sprintf($user->lang['ARCADE_RATING_VALUE'], $x) . "' alt='" . sprintf($user->lang['ARCADE_RATING_VALUE'], $x) . "' class='arcade-rate-{$x}' rel='nofollow'>{$x}</a></li>";
			}
		}

		$image.= "</ul>";
		$info = "<span class='arcade_info'>" . $user->lang['ARCADE_RATING_THX'] . "</span>";

		_arcade_print('image', $image);
		_arcade_print('info', $info);
	}
	else if ($game_id && $fav)
	{
		$fav_data = $fav_image = false;

		if ($fav == ARCADE_FAV_ADD)
		{
			$data = array(
				'user_id' => $user_id,
				'game_id' => $game_id
			);

			$db->sql_return_on_error(true);
			$db->sql_query('INSERT INTO ' . ARCADE_FAVS_TABLE . ' ' . $db->sql_build_array('INSERT', $data));
			$db->sql_return_on_error(false);

			$fav_image	= str_replace('../../', '', $arcade->get_image('src', 'img', 'remove_favorite.png'));
			$image		= "<a onclick='arcade(\"fav\", \"{$game_id}\", \"" . ARCADE_FAV_DEL . "\", \"\", \"{$mode}\"); return false;'><img class='arcade_fav' src='" . $fav_image . "' title='{$user->lang['ARCADE_REMOVE_FAV']}' alt='{$user->lang['ARCADE_REMOVE_FAV']}' /></a>";
			$info		= "<span class='arcade_info' style='vertical-align:middle;'>" . $user->lang['ARCADE_FAV_ADD'] . "</span>";
		}
		else if ($fav == ARCADE_FAV_DEL)
		{
			$sql = 'DELETE FROM ' . ARCADE_FAVS_TABLE . '
					WHERE game_id = ' . $game_id . '
					AND   user_id = ' . $user_id;
			$db->sql_query($sql);

			$fav_image	= str_replace('../../', '', $arcade->get_image('src', 'img', 'add_favorite.png'));
			$image		= "<a onclick='arcade(\"fav\", \"{$game_id}\", \"" . ARCADE_FAV_ADD . "\", \"\", \"{$mode}\"); return false;'><img class='arcade_fav' src='" . $fav_image . "' title='{$user->lang['ARCADE_ADD_FAV']}' alt='{$user->lang['ARCADE_ADD_FAV']}' /></a>";
			$info		= "<span class='arcade_info' style='vertical-align:middle;'>" . $user->lang['ARCADE_FAV_DEL'] . "</span>";
		}
		else
		{
			_arcade_print('error', $user->lang['NO_MODE']);
		}

		if ($fav_image)
		{
			if ($mode != '' && $arcade_config['search_cats'])
			{
				$game_fav_data = $arcade->get_fav_data();

				if (sizeof($game_fav_data))
				{
						$fav_data = '<select class="arcade-games-select" name="g">';

					foreach($game_fav_data as $game)
					{
						$fav_data.= "<option value='{$game['game_id']}'>{$game['game_name']}</option>";
					}
						$fav_data.= '</select>';
				}
			}

			_arcade_print('image', $image);
			_arcade_print('info', $info);

			if ($fav_data)
			{
				_arcade_print('favdata', $fav_data);
			}
		}
	}
	else if ($mode == 'quick_jump' || $mode == 'game_jump')
	{
		if ($user->data['is_registered'] && !$user->data['is_bot'] && $mode == 'quick_jump')
		{
			$played_games = $arcade->get_played_games();
		}

		if ($mode == 'quick_jump')
		{
			$played_game_color = str_replace('#', '', $arcade_config['played_colour']);
			$games_list  = '<select class="arcade-games-select" name="g" '.(!$user->data['is_bot'] && $user->data['is_registered'] ? 'title="'.$user->lang['ARCADE_PLAYED_GAMES_HIGHLIGHT'].'"' : '').'>';
		}
		else
		{
			$games_list  = '<select class="arcade-games-select" name="g">';
		}

		$g_list = false;
		foreach ($arcade->games as $gid => $game)
		{
			if ($mode == 'quick_jump')
			{
				if (!$auth_arcade->acl_get('c_play', $game['cat_id']))
				{
					continue;
				}

				$played = true;
				if (!isset($played_games[$gid]) && $played_game_color)
				{
					$played = false;
				}

				$g_list = true;
				$games_list .= '<option value="'. $gid .'"'. (!$played ? ' style="background-color: #'. $played_game_color .';"' : '') .'>'. $game['game_name'] .'</option>';
			}
			else
			{
				// Only show a game in the games statistics drop down if its been played
				if ($game['game_highuser'] == 0 && $game['game_highdate'] == 0)
				{
					continue;
				}

				$g_list = true;
				$games_list .= '<option value="'. $gid .'">'. $game['game_name'] .'</option>';
			}
		}

		$games_list .= '</select>';

		$games_list = ($g_list === true) ? $games_list : $user->lang[($mode == 'quick_jump') ? 'ARCADE_NO_GAMES' : 'ARCADE_NO_PLAYS'];

		_arcade_print('gameslist', $games_list);

		if ($g_list === true)
		{
			_arcade_print('listenable', 'yes');
		}
	}
	else if ($mode == 'users_list')
	{
		$arcade->set_data('stats');

		$users_list  = '<select name="u">';

		foreach ($arcade->users as $_uid => $_user)
		{
			$users_list .= '<option value="'. $_uid .'">'. $_user['username'] .'</option>';
		}

		$users_list .= '</select>';

		_arcade_print('userslist', $users_list);
	}
	else
	{
		_arcade_print('error', $user->lang['NO_MODE']);
	}

	echo "</phpbbarcade>";
	exit;

	function _arcade_xml($contents)
	{
		$contents = str_replace('&nbsp;', '', $contents);

		if ( preg_match('/\<(.*?)\>/xsi', $contents) )
		{
			$contents = preg_replace('/\<script[\s]+(.*)\>(.*)\<\/script\>/xsi', '', $contents);
		}

		if (!(strpos($contents, '>') === false) || !(strpos($contents, '<') === false) || !(strpos($contents, '&') === false))
		{
			if (!(strpos($contents, ']]>') === false))
			{
				return htmlspecialchars($contents);
			}
			else
			{
				return '<![CDATA[' . $contents . ']]>';
			}
		}
		else
		{
			return htmlspecialchars($contents);
		}

		return $contents;
	}

	function _arcade_print($xml, $value)
	{
		echo '<'.$xml.'>'. _arcade_xml($value) .'</'.$xml.'>';
	}

?>