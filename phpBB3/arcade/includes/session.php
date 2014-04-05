<?php
/**
*
* @package arcade
* @version $Id: session.php 1663 2011-09-22 12:09:30Z killbill $
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

class arcade_session
{
	var $game_sid	= '';
	var $game_popup	= false;

	function session_begin($create = false, $game_id = false, $game_type = false, $popup = false)
	{
		global $config;

		$cookie_sid			= $config['cookie_name'] . '_arcade_sid';
		$cookie_popup		= $config['cookie_name'] . '_arcade_popup';
		$this->game_sid		= (isset($_COOKIE[$cookie_sid])) ? $_COOKIE[$cookie_sid] : ((isset($_POST['game_sid'])) ? request_var('game_sid', '') : '');
		$this->game_popup	= (isset($_COOKIE[$cookie_popup])) ? $_COOKIE[$cookie_popup] : false;

		if ($create)
		{
			global $db, $user;

			$start_time		= time();
			$game_id		= (int) $game_id;
			$user_id		= (int) $user->data['user_id'];

			// Prevent the Flash game to refresh the game page score saving before.
			if ($user_id != ANONYMOUS && !isset($_POST['start']) && !empty($this->game_sid))
			{
				$sql = 'SELECT game_id
						FROM ' . ARCADE_SESSIONS_TABLE . "
						WHERE session_id = '" . $db->sql_escape($this->game_sid) . "'
						AND game_id = $game_id";
				$result = $db->sql_query($sql);
				$gid = (int) $db->sql_fetchfield('game_id');
				$db->sql_freeresult($result);

				if ($gid)
				{
					$create = false;
				}
			}

			if ($create)
			{
				$this->delete_session('key');

				$this->game_sid = md5(uniqid($user->ip));

				$sql_ary = array(
					'session_id'		=> (string) $this->game_sid,
					'phpbb_session_id'	=> (string) $user->session_id,
					'game_id'			=> $game_id,
					'user_id'			=> $user_id,
					'randchar1'			=> 0,
					'randchar2'			=> 0,
					'randgid1'			=> rand(1, 200),
					'randgid2'			=> rand(1, 200),
					'game_type'			=> (int) $game_type,
					'start_time'		=> $start_time,
				);

				$db->sql_query('INSERT INTO ' . ARCADE_SESSIONS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

				$this->set_cookie(true, $popup);
				return (int) $game_id * $sql_ary['randgid1'] ^ $sql_ary['randgid2'];
			}
			else
			{
				return array();
			}
		}
	}

	function add_game_randchar()
	{
		global $db;

		$sql_ary = array(
			'randchar1' => rand(1, 200),
			'randchar2' => rand(1, 200),
		);

		$sql = 'UPDATE ' . ARCADE_SESSIONS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
				WHERE session_id = '" . $db->sql_escape($this->game_sid) . "'
				AND game_type = " . IBPROV3_GAME;
		$db->sql_query($sql);

		return $sql_ary;
	}

	function delete_session($type = '')
	{
		global $db, $user, $arcade_config;

		$sql = 'DELETE FROM ' . ARCADE_SESSIONS_TABLE;

		if ($type == 'gc')
		{
			$sql .= ' WHERE start_time < ' . intval(time() - $arcade_config['session_length']);
			$db->sql_query($sql);
		}
		else
		{
			$sql .= " WHERE phpbb_session_id = '" . $db->sql_escape($user->session_id) . "'";
			$db->sql_query($sql);

			if ($type != 'key')
			{
				$this->set_cookie();
			}
		}
	}

	/**
	* Set arcade cookie, this is used to track the game id,
	* sid and popup value
	*/
	function set_cookie($create = false, $popup = false, $show = false)
	{
		// The cookies is removed once the arcade_score.php output
		// is displayed.  It just used to check whether of not
		// to use the simple header

		// If the function is called with no parameters and show
		// set to true it will return all cookies formatted to be displayed
		if (empty($this->game_sid) && !$create && !$popup && $show)
		{
			$return = '<pre>';
			$return .= var_export($_COOKIE, true);
			$return .= '</pre>';

			return $return;
		}

		global $user, $arcade_config;

		// If the function is not sent anything it removes all its cookies
		if (!$create && !$popup)
		{
			$set_time = (time() - 31536000);
			$user->set_cookie('arcade_sid'	, '', $set_time);
			$user->set_cookie('arcade_popup', '', $set_time);
		}
		else if ($create)
		{
			$set_time = intval(time() + $arcade_config['session_length']);
			$user->set_cookie('arcade_sid'	, $this->game_sid, $set_time);
			$user->set_cookie('arcade_popup', ($popup) ? true : false, $set_time);
		}
	}
}

?>