<?php
/**
*
* @package acm
* @version $Id: cache.php 1663 2011-09-22 12:09:30Z killbill $
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

/**
* Class for grabbing/handling cached entries, extends cache.php
* @package acm
*/
class arcade_cache extends cache
{
	/**
	* Obtain arcade config
	*/
	function obtain_arcade_config()
	{
		global $db;

		if (($config = $this->get('_arcade')) !== false)
		{
			$sql = 'SELECT config_name, config_value
				FROM ' . ARCADE_CONFIG_TABLE . '
				WHERE is_dynamic = 1';
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$config[$row['config_name']] = $row['config_value'];
			}
			$db->sql_freeresult($result);
		}
		else
		{
			$config = $cached_config = array();

			$sql = 'SELECT config_name, config_value, is_dynamic
				FROM ' . ARCADE_CONFIG_TABLE;
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				if (!$row['is_dynamic'])
				{
					$cached_config[$row['config_name']] = $row['config_value'];
				}

				$config[$row['config_name']] = $row['config_value'];
			}
			$db->sql_freeresult($result);

			$this->put('_arcade', $cached_config);
		}

		return $config;
	}

	/**
	* Obtain arcade cats
	*/
	function obtain_arcade_cats()
	{
		if (($cats = $this->get('_arcade_cats')) === false)
		{
			global $db;

			$sql = 'SELECT  cat_id, cat_name, cat_style, cat_status, cat_link, cat_type, cat_test, cat_age, cat_download, cat_password, cat_rules, cat_rules_link, parent_id, cat_parents, left_id, right_id, cat_desc, cat_desc_options, cat_desc_uid, cat_desc_bitfield, cat_rules_options, cat_rules_uid, cat_rules_bitfield, cat_games_per_page
				FROM ' . ARCADE_CATS_TABLE . '
				ORDER BY cat_id ASC';
			$result = $db->sql_query($sql);

			$cats = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$cats[$row['cat_id']] = array(
					'cat_id'				=> $row['cat_id'],
					'cat_name'				=> $row['cat_name'],
					'cat_style'				=> $row['cat_style'],
					'cat_status'			=> $row['cat_status'],
					'cat_link'				=> $row['cat_link'],
					'cat_type'				=> $row['cat_type'],
					'cat_test'				=> $row['cat_test'],
					'cat_age'				=> $row['cat_age'],
					'cat_download'			=> $row['cat_download'],
					'cat_password'			=> $row['cat_password'],
					'cat_rules'				=> $row['cat_rules'],
					'cat_rules_link'		=> $row['cat_rules_link'],
					'cat_rules_options'		=> $row['cat_rules_options'],
					'cat_rules_uid'			=> $row['cat_rules_uid'],
					'cat_rules_bitfield'	=> $row['cat_rules_bitfield'],
					'parent_id'				=> $row['parent_id'],
					'cat_parents'			=> $row['cat_parents'],
					'left_id'				=> $row['left_id'],
					'right_id'				=> $row['right_id'],
					'cat_desc'				=> $row['cat_desc'],
					'cat_desc_options'		=> $row['cat_desc_options'],
					'cat_desc_uid'			=> $row['cat_desc_uid'],
					'cat_desc_bitfield'		=> $row['cat_desc_bitfield'],
					'cat_games_per_page'	=> $row['cat_games_per_page'],
				);
			}
			$db->sql_freeresult($result);

			$this->put('_arcade_cats', $cats);
		}

		return $cats;
	}

	function obtain_arcade_leaders($limit)
	{
		if (($row = $this->get('_arcade_leaders')) === false)
		{
			global $db;

			$sql_array = array(
				'SELECT'	=> 'COUNT(g.game_id) AS total_wins, u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type',

				'FROM'		=> array(
					ARCADE_GAMES_TABLE	=> 'g',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(USERS_TABLE => 'u'),
						'ON'	=> 'g.game_highuser = u.user_id'
					),
				),

				'WHERE'		=> 'u.user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ')',

				'GROUP_BY'	=> 'u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type',

				'ORDER_BY'	=> 'total_wins DESC',
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query_limit($sql, $limit);
			$row = $db->sql_fetchrowset($result);
			$db->sql_freeresult($result);

			$this->put('_arcade_leaders', $row);
		}
		return $row;
	}

	function obtain_arcade_leaders_all()
	{
		if (($row = $this->get('_arcade_leaders_all')) === false)
		{
			global $db;

			$highscore_data = array();
			$sql_array = array(
				'SELECT'	=> 'u.user_id, COUNT(g.game_id) AS total_wins',

				'FROM'		=> array(
					ARCADE_GAMES_TABLE	=> 'g',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(USERS_TABLE => 'u'),
						'ON'	=> 'g.game_highuser = u.user_id'
					),
				),

				'WHERE'		=> 'u.user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ')',

				'GROUP_BY'	=> 'u.user_id',

				'ORDER_BY'	=> 'total_wins DESC',
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			while ($highscore_data = $db->sql_fetchrow($result))
			{
				$row[$highscore_data['user_id']] = $highscore_data['total_wins'];
			}
			$db->sql_freeresult($result);

			$this->put('_arcade_leaders_all', $row);
		}
		return $row;
	}

	function obtain_arcade_games_filesize()
	{
		if (($games_filesize = $this->get('_arcade_games_filesize')) === false)
		{
			global $db;

			$sql = 'SELECT SUM(game_filesize) as games_filesize
				FROM ' . ARCADE_GAMES_TABLE;
			$result = $db->sql_query($sql);
			$games_filesize = (int) $db->sql_fetchfield('games_filesize');
			$db->sql_freeresult($result);

			$this->put('_arcade_games_filesize', $games_filesize);
		}

		return $games_filesize;
	}

	/**
	* Obtain arcade config
	*/
	function obtain_arcade_recent_sites($download_url)
	{
		if (($row = $this->get('_arcade_recent_sites')) === false)
		{
			$row[] = $download_url;
			$this->put('_arcade_recent_sites', array_filter($row));
		}

		if ($download_url != '')
		{
			$add_site = true;
			foreach ($row as $site)
			{
				if ($site == $download_url)
				{
					$add_site = false;
				}
			}

			if ($add_site)
			{
				$row[] = $download_url;
				$this->put('_arcade_recent_sites', array_filter($row));
			}
		}

		return array_filter($row);
	}
}

?>