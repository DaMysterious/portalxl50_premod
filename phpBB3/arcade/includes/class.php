<?php
/**
*
* @package arcade
* @version $Id: class.php 1663 2011-09-22 12:09:30Z killbill $
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
* Class for handling arcade
* @package arcade
*/
class arcade extends arcade_session
{
	var $games = array();
	var $cats = array();
	var $users = array();
	var $leaders = array();
	var $totals = array();
	var $latest_highscores = array();
	var $most_popular_games = array();
	var $least_popular_games = array();
	var $longest_highscores = array();
	var $most_downloaded_games = array();
	var $least_downloaded_games = array();
	var $newest_games = array();
	var $points = array();
	var $gametop = '#gametop';

	/**
	* Constructor used to setup arcade
	* $in_arcade can be set to false
	* to not alter board navgation links
	*/
	function arcade($in_arcade = true, $set_data = true)
	{
		global $user, $auth, $config, $template, $arcade_config, $auth_arcade, $phpbb_root_path, $phpEx;

		$template->assign_vars(array(
			'S_IN_ARCADE'				=> $in_arcade,
			'S_ARCADE_DISABLED'			=> ($arcade_config['arcade_disable']) ? true : false,
			'S_ARCADE_REPORTS'			=> $auth->acl_get('a_arcade_utilities'),
			'S_CAN_VIEW_WHOISPLAYING'	=> $auth_arcade->acl_get('u_view_whoisplaying'),
			'S_ARCADE_REPORTS_OPEN'		=> ($arcade_config['reports_open']) ? true : false,

			'T_ARCADE_JS_PATH'			=> "{$phpbb_root_path}arcade/js",

			'U_ARCADE' 					=> $this->url(),
			'U_ARCADE_FAV'				=> $this->url('mode=fav'),
			'U_ARCADE_STATS'			=> $this->url('mode=stats'),
			'U_ARCADE_REPORTS_OPEN'		=> $this->url("i=arcade_utilities&amp;mode=reports", "adm/index", $user->session_id),

			'GAMETOP'					=> str_replace('#', '', $this->gametop),
			'PHPEX'						=> $phpEx,
			'LOADING_IMG1'				=> $this->get_image('src', 'img', 'loading1.gif'),
			'LOADING_IMG2'				=> $this->get_image('src', 'img', 'loading2.gif'),
			'STAR_IMG'					=> $this->get_image('src', 'img', 'star.png'),
			'ARCADE_VERSION_INFO'		=> $arcade_config['copyright'],
			'ARCADE_REPORTS_OPEN'		=> ($arcade_config['reports_open'] > 1) ? sprintf($user->lang['ARCADE_REPORTS_OPEN'], $arcade_config['reports_open']) : sprintf($user->lang['ARCADE_REPORT_OPEN'], $arcade_config['reports_open']),
		));

		if ($set_data === false)
		{
			return;
		}

		$this->init_points();
		$this->set_data();
		$this->set_disabled();

		if ($in_arcade)
		{
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['ARCADE_INDEX'],
				'U_VIEW_FORUM'	=> $this->url()
			));
		}
	}

	/**
	* Setup arcade data, the information
	* is pulled from the cache
	*/
	function set_data($type = false)
	{
		global $arcade_cache, $auth_arcade, $arcade_config;

		switch($type)
		{
			case 'stats':
				$this->users = $this->obtain_arcade_users();
				$this->most_popular_games = $this->obtain_popular_games('most', $arcade_config['most_popular']);
				$this->least_popular_games = $this->obtain_popular_games('least', $arcade_config['least_popular']);
				$this->longest_highscores = $this->obtain_longest_highscores($arcade_config['longest_held_scores']);

				if ($auth_arcade->acl_getc_global('c_download'))
				{
					$this->most_downloaded_games = $this->obtain_downloaded_games('most', $arcade_config['most_downloaded']);
					$this->least_downloaded_games = $this->obtain_downloaded_games('least', $arcade_config['least_downloaded']);
				}
			break;

			default:
				if (!defined('IN_ADMIN'))
				{
					$this->newest_games = $this->obtain_newest_games($arcade_config['newest_games']);
					$this->leaders = $arcade_cache->obtain_arcade_leaders(($arcade_config['arcade_leaders'] > $arcade_config['arcade_leaders_header']) ? $arcade_config['arcade_leaders'] : $arcade_config['arcade_leaders_header']);
					$this->latest_highscores = $this->obtain_latest_highscores($arcade_config['latest_highscores']);
				}

				if (!defined('IN_ARCADE_NO_LOAD_GAMES'))
				{
					$this->games = $this->obtain_games();
					$this->cats = $arcade_cache->obtain_arcade_cats();
				}
			break;
		}
		return;
	}

	/**
	* Generate the correct arcade url
	*
	* @param mixed $params String or array of additional url parameters
	* @param string $page The name of page
	* @param string $session_id Possibility to use a custom session id instead of the global one
	*/
	function url($params = false, $page = 'arcade', $sid = false)
	{
		global $phpbb_root_path, $phpEx;

		if ($params)
		{
			if ((strpos($params, 'img=') !== false) || (strpos($params, 'swf=') !== false))
			{
				return "{$phpbb_root_path}{$page}.{$phpEx}?{$params}";
			}

			if (strpos($params, 'mode=play') !== false)
			{
				$params = $params . $this->gametop;
			}
		}
		return append_sid($phpbb_root_path . $page . '.' . $phpEx, $params, true, $sid);
	}

	/**
	* Generate return links for a game, category and the arcade
	*/
	function return_links($game_data, $br = true, $popup = false)
	{
		global $user;

		$s_content_flow_begin = ($user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right';

		return ($br ? '<br /><br />' : '') . (($popup) ? sprintf($user->lang['ARCADE_RETURN_LINKS_POPUP'], '<a class="'. $s_content_flow_begin .'" href="'. $this->url("mode=popup&amp;g={$game_data['game_id']}") .'">', $game_data['game_name'], '</a>') : sprintf($user->lang['ARCADE_RETURN_LINKS'], '<a class="'. $s_content_flow_begin .'" href="'. $this->url("mode=play&amp;g={$game_data['game_id']}") .'">', $game_data['game_name'], '</a>', '<a class="'. $s_content_flow_begin .'" href="'. $this->url("mode=cat&amp;c={$game_data['cat_id']}&amp;g={$game_data['game_id']}#g{$game_data['game_id']}") .'">', $game_data['cat_name'], '</a>', '<a class="'. $s_content_flow_begin .'" href="'. $this->url() .'">', '</a>'));
	}

	/**
	* Check if the arcade should be closed down
	*/
	function set_disabled()
	{
		global $arcade_config;

		// Return if auto disable is not turned on
		if (!$arcade_config['auto_disable'])
		{
			return;
		}

		$current_date = getdate();
		$current_time = time();

		// Return if auto disable start time is not valid
		$time_array = $this->validate_time($arcade_config['auto_disable_start']);
		if (!sizeof($time_array))
		{
			return;
		}
		$auto_disable_start = mktime($time_array['hour'], $time_array['min'], 0, $current_date['mon'], $current_date['mday'], $current_date['year']);

		// Return if auto disable end time is not valid
		$time_array = $this->validate_time($arcade_config['auto_disable_end']);
		if (!sizeof($time_array))
		{
			return;
		}
		$auto_disable_end = mktime($time_array['hour'], $time_array['min'], 0, $current_date['mon'], $current_date['mday'], $current_date['year']);

		// Return if auto disable start time is not set eariler than end time
		if ($auto_disable_start >= $auto_disable_end)
		{
			return;
		}

		// If everything checks out let set the correct value for arcade_disable
		if ($current_time >= $auto_disable_start && $current_time < $auto_disable_end)
		{
			if ($arcade_config['arcade_disable'] == false)
			{
				$this->set_config('arcade_disable', true);
			}
		}
		else
		{
			if ($arcade_config['arcade_disable'] == true)
			{
				$this->set_config('arcade_disable', false);
			}
		}
	}

	/**
	* Returns an array of ids for the specified type
	* This is for the local arcade permissions
	*/
	function get_permissions($type)
	{
		global $auth_arcade;

		return array_unique(array_keys($auth_arcade->acl_getc($type, true)));
	}

	/**
	* Set config value. Creates missing config entry.
	*/
	function set_config($config_name, $config_value, $is_dynamic = false)
	{
		global $db, $cache, $arcade_config;

		$sql = 'UPDATE ' . ARCADE_CONFIG_TABLE . "
				SET config_value = '" . $db->sql_escape($config_value) . "'
				WHERE config_name = '" . $db->sql_escape($config_name) . "'";
		$db->sql_query($sql);

		if (!$db->sql_affectedrows() && !isset($arcade_config[$config_name]))
		{
			$sql = 'INSERT INTO ' . ARCADE_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
					'config_name'	=> $config_name,
					'config_value'	=> $config_value,
					'is_dynamic'	=> ($is_dynamic) ? 1 : 0));
			$db->sql_query($sql);
		}

		$arcade_config[$config_name] = $config_value;

		if (!$is_dynamic)
		{
			$cache->destroy('_arcade');
		}
	}

	/**
	* Set various file/path name based off of passed file name
	*
	* Types
	* - install				Returns filename with path of install file
	* - path				Returns game path
	* - gamedata 			Returns gamedata path
	* - v3arcade_gamedata	Returns v3arcade games gamedata path
	* - 					Returns filename with path
	*/
	function set_path($file, $type = '', $use_phpbb_root = true)
	{
		global $phpbb_root_path, $phpEx, $file_functions, $arcade_config;
		$path = $file_functions->remove_extension($file);

		$return = '';
		switch ($type)
		{
			case 'install':
				$return = $arcade_config['game_path'] . $path . '/' . $path . '.' . $phpEx;
			break;

			case 'path':
				$return = $arcade_config['game_path'] . $path . '/';
			break;

			case 'gamedata':
				$return =  'arcade/gamedata/' . $path . '/';
			break;

			case 'v3arcade_gamedata':
				$return = 'games/' . $path . '/';
			break;

			default:
				$return = $arcade_config['game_path'] . $path . '/' . $file;
			break;
		}

		return ($use_phpbb_root) ? $phpbb_root_path . $return : $return;
	}

	function get_gamedata($file, $use_phpbb_root = true)
	{
		if (file_exists($this->set_path($file, 'gamedata')))
		{
			return $this->set_path($file, 'gamedata', $use_phpbb_root);
		}
		else if (file_exists($this->set_path($file, 'v3arcade_gamedata')))
		{
			return $this->set_path($file, 'v3arcade_gamedata', $use_phpbb_root);
		}
		else
		{
			return false;
		}
	}

	function download_game($game, $use_method, $methods, $update = true, $download = true)
	{
		global $user, $cache, $arcade_config, $file_functions, $phpbb_root_path, $phpEx;

		if (!class_exists('compress'))
		{
			include($phpbb_root_path . 'includes/functions_compress.' . $phpEx);
		}

		$backup_path = $phpbb_root_path . $arcade_config['cat_backup_path'] . $this->characters_encoding($game['cat_name']) . '/';
		$path = ($download && $arcade_config['download_on_demand']) ? $phpbb_root_path . 'store/' : $backup_path;

		// If we are not in download mode or we are but we are not serving games on demand
		// we check for and setup the backup directory category folder
		if (!$download || ($download && !$arcade_config['download_on_demand']))
		{
			if (!phpbb_is_writable($phpbb_root_path . $arcade_config['cat_backup_path']))
			{
				trigger_error(sprintf($user->lang['ARCADE_ERROR_DIR_WRITABLE'], $arcade_config['cat_backup_path']), E_USER_WARNING);
			}

			if (!file_exists($path))
			{
				@mkdir($path, 0777);
				@chmod($path, 0777);
			}

			if (!file_exists($path . 'index.htm'))
			{
				file_put_contents($path . 'index.htm', '');

			}

			if (!phpbb_is_writable($path))
			{
				trigger_error(sprintf($user->lang['ARCADE_ERROR_DIR_WRITABLE'], str_replace($phpbb_root_path, '', $path)), E_USER_WARNING);
			}
		}

		if ($update)
		{
			$this->update_download_total($game['game_id']);
			$this->set_config('total_downloads', ($arcade_config['total_downloads'] + 1));
			$cache->destroy('sql', ARCADE_GAMES_TABLE);
		}

		$filename = $file_functions->remove_extension($game['game_swf']);

		if (!in_array($use_method, $methods))
		{
			$use_method = '.tar';
		}

		// If the file exists in the backup directory and if the file modification time of the game install file is less than or equal to
		// the file modification time of the compressed folder in the backup directory send the file in the backup directory instead
		// of creating the compressed file again.
		if ($download && file_exists($backup_path . $filename . $use_method) && (@filemtime($this->set_path($game['game_swf'], 'install')) <= @filemtime($backup_path . $filename . $use_method)))
		{
			$this->send_download_to_browser($backup_path, $filename, $use_method);
			garbage_collection();
			exit_handler();
		}

		$game_file = $this->set_path($game['game_swf']);
		$game_path = $this->set_path($game['game_swf'], 'path');
		$gamedata = $this->get_gamedata($game['game_swf']);

		$error = array();
		if (!file_exists($game_file))
		{
			$error[] = $game_file;
		}

		if (sizeof($error))
		{
			$sql_ary = array(
				'user_id'				=> $user->data['user_id'],
				'game_id'				=> $game['game_id'],
				'error_date'			=> time(),
				'error_type'			=> ARCADE_ERROR_FILEMISSING,
				'error_ip'				=> $user->ip,
			);

			$sql = 'INSERT INTO ' . ARCADE_ERRORS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);

			if ($auth->acl_get('a_') && defined('DEBUG_EXTRA'))
			{
				trigger_error(sprintf($user->lang['ARCADE_DOWNLOAD_MISSING_FILES_DEBUG'], implode('<br />', $error)) . $this->back_link());
			}
			else
			{
				trigger_error(sprintf($user->lang['ARCADE_DOWNLOAD_MISSING_FILES']) . $this->back_link());
			}
		}

		if ($use_method == '.zip')
		{
			$compress = new compress_zip('w', $path . $filename . $use_method);
		}
		else
		{
			$compress = new compress_tar('w', $path . $filename . $use_method, $use_method);
		}

		$file_list = $file_functions->filelist('', $game_path);
		if ($gamedata)
		{
			$file_list = array_merge($file_list, $file_functions->filelist('', $gamedata));
		}

		// Correct path locations before adding files
		$dir_list = array();
		$search = array($game_path, $phpbb_root_path . 'arcade/gamedata', $phpbb_root_path . 'games');
		$replace = array('', 'gamedata', 'games');

		// Add all the game files
		$file_list = array_unique($file_list);
		foreach ($file_list as $file)
		{
			$dir_list[] = dirname($file);
			$compress->add_custom_file($file, str_replace($search, $replace, $file));
		}
		unset($file_list);

		// Add blank index.htm files
		$dir_list = array_unique($dir_list);
		foreach ($dir_list as $dir)
		{
			$file_functions->append_slash($dir);
			$compress->add_data('', str_replace($search, $replace, $dir) . 'index.htm');
		}
		unset($dir_list);

		// Close file
		$compress->close();

		if ($download)
		{
			$this->send_download_to_browser($path, $filename, $use_method);
			// Delete file from the store once user downloads it if serving download on demand
			if ($arcade_config['download_on_demand'])
			{
				@unlink($path . $filename . $use_method);
			}
			garbage_collection();
			exit_handler();
		}
	}

	function send_download_to_browser($path, $filename, $use_method, $download_name = false)
	{

		if ($download_name === false)
		{
			$download_name = $filename;
		}

		switch ($use_method)
		{
			case '.tar':
				$mimetype = 'application/x-tar';
			break;

			case '.tar.gz':
				$mimetype = 'application/x-gzip';
			break;

			case '.tar.bz2':
				$mimetype = 'application/x-bzip2';
			break;

			case '.zip':
				$mimetype = 'application/zip';
			break;

			default:
				$mimetype = 'application/octet-stream';
			break;
		}

		header('Pragma: no-cache');
		header("Content-Type: $mimetype; name=\"$download_name$use_method\"");
		header("Content-disposition: attachment; filename=$download_name$use_method");

		$fp = @fopen("$path$filename$use_method", 'rb');
		if ($fp)
		{
			while ($buffer = fread($fp, 1024))
			{
				echo $buffer;
			}
			fclose($fp);
		}
	}

	function characters_encoding($string)
	{
		$string  = str_replace(array("\xC3\x80", "\xC3\x81", "\xC3\x82", "\xC3\x83", "\xC3\x84", "\xC3\x85")			, "\x41", $string); // A
		$string  = str_replace(array("\xC3\x88", "\xC3\x89", "\xC3\x8a", "\xC3\x8b")									, "\x45", $string); // E
		$string  = str_replace(array("\xC3\x8c", "\xC3\x8d", "\xC3\x8e", "\xC3\x8f")									, "\x49", $string); // I
		$string  = str_replace(array("\xC3\x92", "\xC3\x93", "\xC3\x94", "\xC3\x95", "\xC3\x96", "\xC5\x90")			, "\x4F", $string); // O
		$string  = str_replace(array("\xC3\x99", "\xC3\x9a", "\xC3\x9b", "\xC3\x9c", "\xC5\xB0")						, "\x55", $string); // U
		$string  = str_replace(array("\xC3\x9d")																		, "\x59", $string); // Y
		$string  = str_replace(array("\xC3\xa0", "\xC3\xa1", "\xC3\xa2", "\xC3\xa3", "\xC3\xa4", "\xC3\xa5")			, "\x61", $string); // a
		$string  = str_replace(array("\xC3\xa8", "\xC3\xa9", "\xC3\xaa", "\xC3\xab")									, "\x65", $string); // e
		$string  = str_replace(array("\xC3\xac", "\xC3\xad", "\xC3\xae", "\xC3\xaf")									, "\x69", $string); // i
		$string  = str_replace(array("\xC3\xb1")																		, "\x6e", $string); // n
		$string  = str_replace(array("\xC3\xb2", "\xC3\xb3", "\xC3\xb4", "\xC3\xb5", "\xC3\xb6", "\xC3\xb0", "\xC5\x91"), "\x6F", $string); // o
		$string  = str_replace(array("\xC3\xb9", "\xC3\xba", "\xC3\xbb", "\xC3\xbc", "\xC5\xB1")						, "\x75", $string); // u
		$string  = str_replace(array("\xC3\xbd", "\xC3\xbf")															, "\x79", $string); // y
		$string  = str_replace(" "																						, "_"	, $string); // space

		return trim(preg_replace('/[^\w -]+/', '', htmlspecialchars_decode($string)));
	}

	/*
	* Set filesize for game this
	* includes all extra files
	*/
	function set_filesize($game_id)
	{
		global $cache, $db, $file_functions;

		$sql = 'SELECT game_id, game_swf
				FROM ' . ARCADE_GAMES_TABLE . '
				WHERE game_id = ' . (int) $game_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$filesize = $file_functions->filesize(array_filter(array($this->set_path($row['game_swf']), $this->get_gamedata($row['game_swf']))));

		if ($filesize)
		{
			$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
					SET game_filesize = '. (int) $filesize . '
					WHERE game_id = ' . (int) $game_id;
			$db->sql_query($sql);
		}

		$cache->destroy('_arcade_games_filesize');

		return $filesize;
	}

	/*
	* Set game display size
	*/
	function set_game_size(&$new_game_width, &$new_game_height, $game_width, $game_height, $game_swf)
	{
		global $arcade_config;

		if ($arcade_config['game_autosize'])
		{
			if (list($width, $height) = @getimagesize($this->set_path($game_swf)))
			{
				$new_game_width = $width;
				$new_game_height = $height;
			}
		}

		if (empty($new_game_width) || empty($new_game_height))
		{
			if (!empty($arcade_config['game_width']))
			{
				$new_game_width = $arcade_config['game_width'];
			}
			else
			{
				$new_game_width = $game_width;
			}


			if (!empty($arcade_config['game_height']))
			{
				$new_game_height = $arcade_config['game_height'];
			}
			else
			{
				$new_game_height = $game_height;
			}
		}
	}

	function back_link($page = 'arcade', $lang = 'arcade', $params = false)
	{
		global $user;

		$s_content_flow_begin = ($user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right';
		return '<br /><br /><a class="'. $s_content_flow_begin .'" href="' . $this->url($params, $page) . '">' . $user->lang['ARCADE_BACK_TO_' . strtoupper($lang)] . '</a>';
	}

	/**
	* Displays html code for images including
	* dimensions if possible
	*
	* Thanks easygo
	*/
	function set_image($path, $alt = '', $style = '')
	{
		$alt	= (!empty($alt)) ? 'alt="' . $alt . '" title="' . $alt . '"' : 'alt=""';
		$style	= (!empty($style)) ? 'style="' . $style . '"' : '';

		if (list($width, $height) = @getimagesize($path))
		{
			$img = '<img src="' . $path . '" width="' . $width . '" height="' . $height . '" ' . $alt . $style . ' />';
		}
		else
		{
			$img = '<img src="' . $path . '" ' . $alt . $style . ' />';
		}

		return $img;
	}

	/**
	* This function checks to see if there are style specific
	* category images first
	*/
	function get_image($mode, $type, $image, $alt = '', $default = false)
	{
		if (!$image)
		{
			return;
		}

		global $user, $phpbb_root_path, $arcade_config;

		$theme_path = $image_path = '';
		switch (strtolower($type))
		{
			case 'cat':
				$theme_path = "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme/images/arcade/cats/';
				$image_path = $phpbb_root_path . $arcade_config['cat_image_path'];
			break;

			case 'img':
				$theme_path = "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme/images/arcade/';
				$image_path = $phpbb_root_path . $arcade_config['image_path'];
			break;

			default:
				return;
			break;
		}

		if (file_exists($theme_path . $image) && $default === false)
		{
			$path = $theme_path . $image;
		}
		else if (file_exists($image_path . $image))
		{
			$path = $image_path . $image;
		}
		else
		{
			return;
		}

		switch (strtolower($mode))
		{
			case 'full':
				return $this->set_image($path, $alt);
			break;

			case 'src':
				return $path;
			break;

			default:
				return;
			break;
		}
	}

	/**
	* Get user information includes user_colour, rank, avatar
	* and various bits of arcade info
	*/
	function get_user_info($user_id = false)
	{
		global $db, $user, $auth_arcade;

		if ($user_id)
		{
			$sql = 'SELECT * FROM ' . USERS_TABLE . '
					WHERE user_id = ' . (int) $user_id;
			$result = $db->sql_query($sql);
			$user_info = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		else
		{
			$user_info = $user->data;
		}

		$user_avatar = ($user->optionget('viewavatars')) ? true : false;

		if ($user_avatar)
		{
			if (!($user_avatar = get_user_avatar($user_info['user_avatar'], $user_info['user_avatar_type'], $user_info['user_avatar_width'], $user_info['user_avatar_height'], $user_info['username'])))
			{
				$user_avatar = '<img src="' . $this->get_image('src', 'img', 'noavatar.gif') . '" alt="' . $user_info['username'] . '" title="' . $user_info['username'] . '" />';
			}

			$user_avatar = ($user_info['user_id'] != ANONYMOUS && $auth_arcade->acl_get('u_viewstats') && $user_avatar) ? '<a href="' . $this->url("mode=stats&amp;u={$user_info['user_id']}") . '">' . $user_avatar . '</a>' : $user_avatar;
		}

		if ($user_info['user_id'] == ANONYMOUS)
		{
			$data = array(
				'user_id'			=> $user_info['user_id'],
				'username'			=> $user_info['username'],
				'user_colour'		=> $user_info['user_colour'],
				'avatar'			=> $user_avatar,
				'rank_title'		=> '',
				'rank_image'		=> '',
				'rank_image_src'	=> '',
				'total_wins'		=> 0,
				'total_plays'		=> 0,
				'total_time'		=> 0,
			);
		}
		else
		{
			$rank_title = $rank_img = $rank_img_src = '';
			get_user_rank($user_info['user_rank'], $user_info['user_posts'], $rank_title, $rank_img, $rank_img_src);

			// Calculates the users total number of arcade victories
			$sql = 'SELECT COUNT(game_id) AS total_wins
					FROM ' . ARCADE_GAMES_TABLE . '
					WHERE game_highuser = ' . (int) $user_info['user_id'];
			$result = $db->sql_query($sql);
			$total_wins = $db->sql_fetchfield('total_wins');
			$db->sql_freeresult($result);

			// Total plays and times using Arcade
			$sql = 'SELECT SUM(total_plays) AS games_played, SUM(total_time) AS games_time
					FROM ' . ARCADE_PLAYS_TABLE . '
					WHERE user_id = ' . (int) $user_info['user_id'];
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$total_plays = (int) $row['games_played'];
			$total_time = $row['games_time'];

			$data = array(
				'user_id'			=> $user_info['user_id'],
				'username'			=> $user_info['username'],
				'user_colour'		=> $user_info['user_colour'],
				'avatar'	 		=> $user_avatar,
				'rank_title' 		=> $rank_title,
				'rank_image' 		=> $rank_img,
				'rank_image_src'	=> $rank_img_src,
				'total_wins' 		=> $total_wins,
				'total_plays' 		=> $total_plays,
				'total_time' 		=> $total_time,
			);
		}

		return $data;
	}

	/**
	* Get all the categories the user has played games in
	* Follows the permissions systems so only categories
	* the viewing user can see are returned
	*/
	function get_user_categories($user_id = false)
	{
		global $user, $db;

		if (!$user_id)
		{
			$user_id = $user->data['user_id'];
		}

		$sql_array = array(
			'SELECT'	=> 'c.cat_id, c.cat_name',

			'FROM'		=> array(
				ARCADE_SCORES_TABLE	=> 's',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
					'ON'	=> 's.game_id = g.game_id'
				),
				array(
					'FROM' => array(ARCADE_CATS_TABLE => 'c'),
					'ON'	=> 'g.cat_id = c.cat_id'
				),
			),

			'ORDER_BY'	=> 'c.cat_name ASC',

			'WHERE'		=> 's.user_id = ' . (int) $user_id . ' AND ' . $db->sql_in_set('c.cat_id', $this->get_permissions('c_view'), false, true),
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		$cats = array();
		$cats[] = $user->lang['ARCADE_ALL_CATEGORIES'];
		while ($row = $db->sql_fetchrow($result))
		{
			$cats[$row['cat_id']] = $row['cat_name'];
		}
		$db->sql_freeresult($result);

		return $cats;
	}

	/**
	* Get one of the following fields from the games array (games user has permission for):
	* game_id, game_name, game_name_clean, game_swf, game_scorevar, game_highuser, game_highdate, cat_id
	*
	* To add more field modify the obtain_games() method of this class.
	*
	* This can be used to check if a user has permission to view a game by just passing
	* the game id
	* @returns requested field or boolean
	*/
	function get_game_field($game_id, $field = false)
	{
		if (isset($this->games[$game_id]))
		{
			if (empty($field))
			{
				return true;
			}

			if (!isset($this->games[$game_id][$field]))
			{
				trigger_error('ARCADE_INVALID_FIELD', E_USER_ERROR);
			}

			return $this->games[$game_id][$field];
		}

		return false;
	}

	/**
	* Gets game data of passed game id or ids
	*/
	function get_game_data($game_id, $order = false, $limit = false, $start = 0)
	{
		global $db, $user;

		$sql_array = array(
			'SELECT'	=> 'g.*, r.game_rating, c.cat_name, c.cat_desc, c.cat_desc_uid, c.cat_desc_bitfield, c.cat_desc_options, c.parent_id, c.cat_parents, c.left_id, c.right_id, c.cat_download, c.cat_cost, c.cat_reward, c.cat_use_jackpot',

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

			'WHERE'		=> $db->sql_in_set('g.game_id', $game_id),

		);

		if ($order)
		{
			$sql_array['ORDER_BY'] = $order;
		}

		$sql = $db->sql_build_query('SELECT', $sql_array);

		if ($limit)
		{
			$result = $db->sql_query_limit($sql, $limit, $start);
		}
		else
		{
			$result = $db->sql_query($sql);
		}

		if (is_array($game_id))
		{
			$row = $db->sql_fetchrowset($result);
		}
		else
		{
			$row = $db->sql_fetchrow($result);
		}

		$db->sql_freeresult($result);

		return ($row) ? $row : false;
	}

	/**
	* Gets any category field
	*/
	function get_cat_field($cat_id, $field = false)
	{
		if (isset($this->cats[$cat_id]))
		{
			if (empty($field))
			{
				return true;
			}

			return $this->cats[$cat_id]['cat_name'];
		}

		$cat = $this->get_cat_data($cat_id);
		return ($cat && !empty($field) && isset($cat[$field])) ? $cat[$field] : false;
	}

	/**
	* Get category data for passed
	* cat id or ids
	*/
	function get_cat_data($cat_id)
	{
		global $db;

		$sql_array = array(
			'SELECT'	=> 'c.cat_id, c.cat_name, c.cat_desc, c.cat_desc_uid, c.cat_desc_bitfield, c.cat_desc_options, c.parent_id, c.cat_parents, c.left_id, c.right_id',

			'FROM'		=> array(
				ARCADE_CATS_TABLE	=> 'c',
			),

			'WHERE'	=> $db->sql_in_set('cat_id', $cat_id),
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		if (is_array($cat_id))
		{
			$row = $db->sql_fetchrowset($result);
		}
		else
		{
			$row = $db->sql_fetchrow($result);
		}

		$db->sql_freeresult($result);

		return ($row) ? $row : false;
	}

	/**
	* Get a random game_id from the arcade
	* This is done like this to support all db types
	* since some do not have a RAND() function
	*/
	function get_random_game()
	{
		global $db;

		$sql_array = array(
			'SELECT'	=> 'g.game_id',

			'FROM'		=> array(
				ARCADE_GAMES_TABLE	=> 'g',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(ARCADE_CATS_TABLE => 'c'),
					'ON'	=> 'g.cat_id = c.cat_id'
				),
			),

			'WHERE'	=> 'c.cat_status <> ' . ITEM_LOCKED . ' AND ' . $db->sql_in_set('c.cat_id', $this->get_permissions('c_play'), false, true),
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		// Return false if no games are found
		$game_id = false;
		if (sizeof($row))
		{
			$game_id = $row[mt_rand(0, sizeof($row) - 1)]['game_id'];
		}

		return $game_id;
	}

	/**
	* Display a users favorite games, if no user id
	* is passed it default to viewing user
	*/
	function get_fav_data($user_id = false)
	{
		global $db, $user;

		if (!$user_id)
		{
			$user_id = $user->data['user_id'];
		}

		$sql_array = array(
			'SELECT'	=> 'g.game_id, g.game_name, g.game_name_clean, g.game_swf, g.game_scorevar, g.cat_id',

			'FROM'		=> array(
				ARCADE_GAMES_TABLE	=> 'g',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(ARCADE_FAVS_TABLE => 'f'),
					'ON'	=> 'g.game_id = f.game_id'
				),
			),

			'WHERE'	=> 'f.user_id = ' . (int) $user_id . ' AND ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true),

			'ORDER_BY' => 'g.game_name_clean ASC',
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$fav = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$fav[$row['game_id']] = array(
				'game_id'		=> $row['game_id'],
				'game_name'		=> $row['game_name'],
				'game_swf'		=> $row['game_swf'],
				'game_scorevar'	=> $row['game_scorevar'],
				'cat_id'		=> $row['cat_id'],
			);
		}
		$db->sql_freeresult($result);
		return $fav;
	}

	/**
	* Return an array contain all the game ids and names
	* of the games the user has played, if no user id is
	* passed it defaults to viewing user
	*/
	function get_played_games($user_id = false)
	{
		global $arcade_config;

		if (!$arcade_config['played_colour'])
		{
			return array();
		}

		global $db, $user;

		if (!$user_id)
		{
			$user_id = $user->data['user_id'];
		}

		$sql_array = array(
			'SELECT'	=> 'g.game_id, g.game_name',

			'FROM'		=> array(
				ARCADE_SCORES_TABLE	=> 's',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
					'ON'	=> 's.game_id = g.game_id'
				),
			),

			'WHERE'		=> 's.user_id = ' . (int) $user_id,
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		$played = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$played[$row['game_id']] = $row['game_name'];
		}
		$db->sql_freeresult($result);
		return $played;
	}

	/**
	* Display correct game type based on constant passed type
	*/
	function display_game_type($game_type)
	{
		global $user;

		$type = $user->lang['ARCADE_UNKNOWN'];
		switch ($game_type)
		{
			case AMOD_GAME:
				$type = $user->lang['AMOD_GAME'];
			break;

			case AR_GAME:
				$type = $user->lang['AR_GAME'];
			break;

			case IBPRO_GAME:
				$type = $user->lang['IBPRO_GAME'];
			break;

			case V3ARCADE_GAME:
				$type = $user->lang['V3ARCADE_GAME'];
			break;

			case IBPROV3_GAME:
				$type = $user->lang['IBPROV3_GAME'];
			break;

			case IBPRO_ARCADELIB_GAME:
				$type = $user->lang['IBPRO_ARCADELIB_GAME'];
			break;

			case NOSCORE_GAME:
				$type = $user->lang['NOSCORE_GAME'];
			break;

			default:
			break;
		}
		return $type;
	}

	/*
	* This function displays the site name, categories and download methods
	* available from the server you are download from
	*/
	function display_download_data()
	{
		global $arcade_config;

		if ($arcade_config['download_list'])
		{
			global $config;

			$categories = array();
			foreach ($this->cats as $cats)
			{
				if ($cats['cat_download'] == true)
				{
					$categories[$cats['cat_id']] = array(
						'cat_id'	=> $cats['cat_id'],
						'cat_name'	=> $cats['cat_name'],
					);
				}
			}

			$download_data = array(
				'sitename'		=> $config['sitename'],
				'message'		=> $arcade_config['download_list_message'],
				'methods'		=> $this->compress_methods(),
				'categories'	=> $categories,
			);

			$download_data = gzcompress(serialize($download_data), 9);
			echo $download_data;
		}
		exit;
	}

	/**
	* This function displays all the games for download.  One issue occurs though,
	* since the server is connecting to the site to get the list and the server is most likely
	* not ever going to be logged in we must display every game where the category and/or game
	* is able to be downloaded based on the cat_download and game_download setting.  Once the
	* list is displayed in the ACP the permissions system will take over for downloading.
	*/
	function display_download_list($cat_id, $start, $sort_key, $sort_dir, $sort_time, $per_page)
	{
		global $arcade_config;

		if ($arcade_config['download_list'])
		{
			global $db;

			$where = ($cat_id) ? ' AND g.cat_id = ' . (int) $cat_id : '';
			$install_time = ($sort_time) ? time() - ($sort_time * 86400) : false;
			$where = ($install_time) ? $where . ' AND g.game_installdate > ' . $install_time : $where;

			$sort = ($sort_key == 'n') ? 'g.game_name_clean' : 'g.game_installdate';
			$sort_dir = ($sort_dir == 'a') ? 'ASC' : 'DESC';

			// Only allow 50, 100 or 200 games per page
			// This will make sure that there are not tons of different settings out there
			$per_page = (in_array($per_page, array(50, 100, 200))) ? $per_page : 50;

			$download_list = array();

			$sql_array = array(
				'SELECT'	=> 'COUNT(g.game_id) as total',

				'FROM'		=> array(
					ARCADE_GAMES_TABLE	=> 'g',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(ARCADE_CATS_TABLE => 'c'),
						'ON'	=> 'g.cat_id = c.cat_id'
					),
				),

				'WHERE'	=> 'c.cat_download = 1 AND g.game_download = 1 ' . $where,
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql, $arcade_config['cache_time'] * 3600);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$download_list['total'] = $row['total'];
			unset($row);

			$sql_array = array(
				'SELECT'	=> 'g.game_id, g.game_swf, g.game_name, g.game_name_clean, g.game_desc, g.game_installdate, g.game_filesize, c.cat_id',

				'FROM'		=> array(
					ARCADE_GAMES_TABLE	=> 'g',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(ARCADE_CATS_TABLE => 'c'),
						'ON'	=> 'g.cat_id = c.cat_id'
					),
				),

				'WHERE'	=> 'c.cat_download = 1 AND g.game_download = 1 ' . $where,

				'ORDER_BY' => $sort . ' ' . $sort_dir,
			);

			$result = $db->sql_query($sql);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query_limit($sql, $per_page, $start, $arcade_config['cache_time'] * 3600);

			$games = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$games[$row['game_id']] = array(
					'game_id'			=> $row['game_id'],
					'game_name'			=> $row['game_name'],
					'game_name_clean'	=> $row['game_name_clean'],
					'game_swf'			=> $row['game_swf'],
					'game_desc'			=> $row['game_desc'],
					'game_installdate'	=> $row['game_installdate'],
					'game_filesize'		=> $row['game_filesize'],
					'cat_id'			=> $row['cat_id'],
				);
			}
			$db->sql_freeresult($result);
			$download_list['games'] = $games;
			unset($games);

			// Try to save some bandwidth and allows us to still have an array on the other side
			$download_list = gzcompress(serialize($download_list), 9);
			echo $download_list;
		}
		exit;
	}

	/**
	* Updates a games download total
	*/
	function update_download_total($game_id)
	{
		global $db, $user;

		// Update download count
		$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
				SET game_download_total = game_download_total + 1
				WHERE game_id = ' . (int) $game_id;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . ARCADE_DOWNLOAD_TABLE. '
				SET total = total + 1, download_time = ' . time() . '
				WHERE game_id = ' . (int) $game_id . ' AND user_id = ' . (int) $user->data['user_id'];
		$db->sql_query($sql);

		if (!$db->sql_affectedrows())
		{
			$sql = 'INSERT INTO ' . ARCADE_DOWNLOAD_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'game_id'			=> (int) $game_id,
				'user_id'			=> (int) $user->data['user_id'],
				'total'				=> 1,
				'download_time'		=> time(),
			));
			$db->sql_query($sql);
		}
	}

	/**
	* Take seconds and convert to readable format
	* Example usage of filter:
	* To return days, hours, minutes and seconds
	* $filter = 'day|hour|minute|second';
	* If $filter is set to false it returns full result
	*/
	function time_format($secs, $full_label = false, $filter = false)
	{
		global $user;

		$output = '';
		$filter = ($filter) ? explode('|', strtolower($filter)) : false;

		$minute = ($full_label) ? 'minute' : 'min';
		$second = ($full_label) ? 'second' : 'sec';

		$time_array = array(
			'year'		=> 60 * 60 * 24 * 365,
			'month'		=> 60 * 60 * 24 * 30,
			'week'		=> 60 * 60 * 24 * 7,
			'day'		=> 60 * 60 * 24,
			'hour'		=> 60 * 60,
			$minute		=> 60,
			$second		=> 0,
		);

		foreach ($time_array as $key => $value)
		{
			if ($filter && !in_array($key, $filter))
			{
				continue;
			}

			$item = ($value) ? intval(intval($secs) / $value) : intval($secs);
			if ($item > 0)
			{
				$secs = $secs - ($item * $value);
				$output .= ' ' . $item . ' ' . (($item > 1) ? $user->lang['TIME_' . strtoupper($key) . 'S'] : $user->lang['TIME_' . strtoupper($key)]);
			}
		}

		return $output;
	}

	/**
	* Wrapper function for php number format
	* Displays scores in users local language format
	*/
	function number_format($num)
	{
		global $user;

		// This function will format the the passed number to be displayed by the arcade.
		// This will fix the problem that was happening with some games
		// using decimal numbers for scores.  It will only display decimal
		// places if necessary.

		// Anytime that a number is displayed in the arcade for any reason it should
		// go through this function.  This includes portal blocks and such...

		// Lets see if the number has a decimal point
		$decimals = explode('.', $num);
		if (isset($decimals[1]))
		{
			// Ok so we have a decimal point remove all trailing zeroes and get the length
			$decimals = strlen(rtrim($decimals[1], '0'));
		}
		else
		{
			// No decimal point so set it to zero
			$decimals = 0;
		}

		return number_format($num, $decimals, $user->lang['SEPARATOR_DECIMAL'], $user->lang['SEPARATOR_THOUSANDS']);
	}

	/**
	* Wrapper function for phpbb3 get_username_string function
	* Points link to arcade stats page
	*/
	function get_username_string($mode, $user_id, $username, $user_colour)
	{
		static $_stat_auth_cache;

		if (empty($_stat_auth_cache))
		{
			global $auth_arcade;

			$_stat_auth_cache = ($auth_arcade->acl_get('u_viewstats')) ? true : false;
		}

		return get_username_string($mode, $user_id, $username, $user_colour, false, (($_stat_auth_cache) ? $this->url('mode=stats') : false));
	}

	/**
	* Tries to make sure the time is set correctly in
	* the settings page
	*
	* We expect military time, for example 14:23
	* Value must be a hour between 1 and 23 and a minute between 0 and 59
	* these must be separated with a colon ':'
	*/
	function validate_time($value)
	{
		$value = explode(':', $value);
		if (sizeof($value) < 2)
		{
			return array();
		}

		list($hour, $min) = array_map('intval', $value);
		if (($hour < 1 || $hour > 23) || ($min < 0 || $min > 59))
		{
			return array();
		}
		else
		{
			return array(
				'hour'			=> $hour,
				'min'			=> $min,
			);
		}
	}

	/**
	* All-encompasing sync function
	*
	* Modes:
	* - category		Sync category data
	* - game			Sync game data
	* - rating			Sync rating data
	* - total_data		Sync games, plays, downloads, users, reports data
	*/
	function sync($mode, $id = '')
	{
		global $db, $user, $arcade_cache;

		if ($id == '' && ($mode == 'rating' || $mode == 'game'))
		{
			trigger_error('ARCADE_SYNC_MODE_NOT_SUPPORTED');
		}

		switch($mode)
		{
			case 'category':
				$sql = 'SELECT cat_id
					FROM ' . ARCADE_CATS_TABLE;

				if ($id != '')
				{
					if (!is_array($id))
					{
						$id = array((int) $id);
					}

					$sql .= ' WHERE ' . $db->sql_in_set('cat_id', $id) . ' GROUP BY cat_id';
				}

				$cat_result = $db->sql_query($sql);

				while ($cat_row = $db->sql_fetchrow($cat_result))
				{
					$cat_id = $cat_row['cat_id'];

					$sql = 'SELECT game_installdate
						FROM ' . ARCADE_GAMES_TABLE . '
						WHERE cat_id = ' . (int) $cat_id . '
						ORDER BY game_installdate DESC';
					$result = $db->sql_query_limit($sql, 1);
					$cat_last_game_installdate = $db->sql_fetchfield('game_installdate');
					$db->sql_freeresult($result);

					$sql = 'SELECT COUNT(*) AS cat_games
							FROM ' . ARCADE_GAMES_TABLE . '
							WHERE cat_id = ' . (int) $cat_id;
					$result = $db->sql_query($sql);
					$cat_games = $db->sql_fetchfield('cat_games');
					$db->sql_freeresult($result);

					$sql_array = array(
						'SELECT'	=> 'SUM(p.total_plays) AS cat_plays',

						'FROM'		=> array(
							ARCADE_PLAYS_TABLE	=> 'p',
						),

						'LEFT_JOIN'	=> array(
							array(
								'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
								'ON'	=> 'p.game_id = g.game_id'
							),
						),

						'WHERE'	=> 'g.cat_id = ' . (int) $cat_id,
					);

					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					$cat_plays = $db->sql_fetchfield('cat_plays');
					$db->sql_freeresult($result);

					$sql = 'UPDATE ' . ARCADE_CATS_TABLE  . '
							SET ' . $db->sql_build_array('UPDATE', array(
							'cat_games'						=> (int) $cat_games,
							'cat_plays'						=> (int) $cat_plays,
							'cat_last_game_installdate'		=> (int) $cat_last_game_installdate,
						)) . '
						WHERE cat_id = ' . (int) $cat_id;
					$db->sql_query($sql);
				}
				$db->sql_freeresult($cat_result);
			break;

			case 'game':
				if (!is_array($id))
				{
					$id = ($id) ? array((int) $id) : array();
				}

				$sql = 'SELECT SUM(total_plays) AS game_plays, game_id
						FROM ' . ARCADE_PLAYS_TABLE . '
						WHERE ' . $db->sql_in_set('game_id', $id) . '
						GROUP BY game_id';
				$result = $db->sql_query($sql);

				$plays_id = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$plays_id[] = $row['game_id'];
					$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
							SET game_plays = ' . (int) $row['game_plays'] . '
							WHERE game_id = ' . (int) $row['game_id'];
					$db->sql_query($sql);
				}
				$db->sql_freeresult($result);

				$no_plays = array();
				if (!empty($plays_id))
				{
					foreach ($id as $game_id)
					{
						if (!in_array($game_id, $plays_id))
						{
							$no_plays[] = $game_id;
						}
					}
				}
				else
				{
					$no_plays = $id;
				}

				if (!empty($no_plays))
				{
					$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
							SET game_plays = 0
							WHERE ' . $db->sql_in_set('game_id', $no_plays);
					$db->sql_query($sql);
				}
			break;

			case 'rating':
				if (!is_array($id))
				{
					$id = ($id) ? array((int) $id) : array();
				}

				$sql = 'SELECT game_id, COUNT(*) as game_votetotal, SUM(game_rating) as game_votesum
						FROM ' . ARCADE_RATING_TABLE . '
						WHERE ' . $db->sql_in_set('game_id', $id) . '
						GROUP BY game_id';
				$result = $db->sql_query($sql);

				$plays_id = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$plays_id[] = $row['game_id'];
					$returned_results = true;
					$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
							SET game_votetotal	= ' . (int) $row['game_votetotal'] . ',
								game_votesum	= ' . (int) $row['game_votesum'] . '
							WHERE game_id = ' . (int) $row['game_id'];
					$db->sql_query($sql);
				}
				$db->sql_freeresult($result);

				$no_plays = array();
				if (!empty($plays_id))
				{
					foreach ($id as $game_id)
					{
						if (!in_array($game_id, $plays_id))
						{
							$no_plays[] = $game_id;
						}
					}
				}
				else
				{
					$no_plays = $id;
				}

				if (!empty($no_plays))
				{
					$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
							SET game_votetotal = 0, game_votesum = 0
							WHERE ' . $db->sql_in_set('game_id', $no_plays);
					$db->sql_query($sql);
				}
			break;

			case 'total_data':
				if (!is_array($id))
				{
					$id = array($id);
				}

				if (in_array('plays' , $id) || in_array('all' , $id))
				{
					$play_data = $this->get_total('plays');
					$total_plays = (int) $play_data['games_played'];
					$total_times = (int) $play_data['games_time'];
					$this->set_config('total_plays', $total_plays, true);
					$this->set_config('total_plays_time', $total_times, true);
				}

				if (in_array('downloads' , $id) || in_array('all' , $id))
				{
					$total_downloads = (int) $this->get_total('downloads');
					$this->set_config('total_downloads', $total_downloads, true);
				}

				if (in_array('reports' , $id) || in_array('all' , $id))
				{
					$reports_open = (int) $this->get_total('reports_open');
					$this->set_config('reports_open', $reports_open);
				}
			break;

			default:
				trigger_error('NO_MODE');
			break;
		}
		return;
	}

	/**
	* Gets a total number of games based on the parameters it
	* was sent.
	*
	* $type can be set to fav, games or search
	*/
	function get_total($type = '', $id = 0)
	{
		global $db, $user;

		$type = strtolower($type);
		$sql_where = '';

		switch ($type)
		{
			case 'fav':
				if ($id)
				{
					$sql_array = array(
						'SELECT'	=> 'COUNT(f.game_id) as total',

						'FROM'		=> array(
							ARCADE_FAVS_TABLE	=> 'f',
						),

						'LEFT_JOIN'	=> array(
							array(
								'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
								'ON'	=> 'f.game_id = g.game_id'
							),
						),

						'WHERE'		=> 'f.user_id = ' . (int) $id . ' AND ' . $db->sql_in_set('g.cat_id', $this->get_permissions('c_view'), false, true),
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
				}
				else
				{
					trigger_error('ARCADE_NO_ID_ERROR');
				}
			break;

			case 'games':
				$sql_array = array(
					'SELECT'	=> 'COUNT(game_id) as total',

					'FROM'		=> array(
						ARCADE_GAMES_TABLE	=> 'g',
					),
				);

				if ($id)
				{
					$sql_array['WHERE'] = 'cat_id = ' . (int) $id;
				}
				$sql = $db->sql_build_query('SELECT', $sql_array);
			break;

			case 'scores':
				if ($id)
				{
					$sql_array = array(
						'SELECT'	=> 'COUNT(score) as total',

						'FROM'		=> array(
							ARCADE_SCORES_TABLE	=> 's',
						),

						'LEFT_JOIN'	=> array(
							array(
								'FROM'	=> array(USERS_TABLE => 'u'),
								'ON'	=> 's.user_id = u.user_id'
							),
						),

						'WHERE'		=> 's.game_id = ' . (int) $id,
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
				}
				else
				{
					$sql_array = array(
						'SELECT'	=> 'g.game_id',

						'FROM'		=> array(
							ARCADE_SCORES_TABLE	=> 's',
						),

						'LEFT_JOIN'	=> array(
							array(
								'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
								'ON'	=> 'g.game_id = s.game_id'
							),
						),
					);

					$sql = $db->sql_build_query('SELECT_DISTINCT', $sql_array);
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrowset($result);
					$db->sql_freeresult($result);

					return sizeof($row);
				}
			break;

			case 'search_newgames':
				if ($id)
				{
					// Return all the newest games
					$sql_array = array(
						'SELECT'	=> 'COUNT(g.game_id) as total',

						'FROM'		=> array(
							ARCADE_GAMES_TABLE	=> 'g',
						),
						'WHERE'		=> 'g.game_installdate >= ' . (int) $id . '
										AND ' . $db->sql_in_set('g.cat_id', $this->games_search_protection(), true, true) . '
										AND ' . $db->sql_in_set('g.cat_id', $this->get_permissions('c_view'), false, true),
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
				}
				else
				{
					trigger_error('ARCADE_NO_ID_ERROR');
				}
			break;

			case 'search':
				if ($id)
				{
					$sql_array = array(
						'SELECT'	=> 'COUNT(g.game_id) as total',

						'FROM'		=> array(
							ARCADE_GAMES_TABLE	=> 'g',
						),
						'WHERE'		=> '(g.game_name_clean '. $db->sql_like_expression($id) . '
										OR LOWER(g.game_desc) ' . $db->sql_like_expression($id) . ')
										AND ' . $db->sql_in_set('g.cat_id', $this->games_search_protection(), true, true) . '
										AND ' . $db->sql_in_set('g.cat_id', $this->get_permissions('c_view'), false, true),
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
				}
				else
				{
					trigger_error('ARCADE_NO_ID_ERROR');
				}
			break;

			case 'download_stats':
				if ($id)
				{
					$sql_array = array(
						'SELECT'	=> 'COUNT(game_id) as total',

						'FROM'		=> array(
							ARCADE_DOWNLOAD_TABLE	=> 'd',
						),

						'WHERE'		=> 'user_id = ' . (int) $id,
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
				}
				else
				{
					$sql_array = array(
						'SELECT'	=> 'user_id',

						'FROM'		=> array(
							ARCADE_DOWNLOAD_TABLE	=> 'd',
						),

						'GROUP_BY'		=> 'user_id',
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);

					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrowset($result);
					$db->sql_freeresult($result);

					return sizeof($row);
				}
			break;

			case 'plays':
				$sql = 'SELECT SUM(total_plays) AS games_played, SUM(total_time) AS games_time
						FROM ' . ARCADE_PLAYS_TABLE;
				$result = $db->sql_query($sql);
				$play_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				return $play_data;
			break;

			case 'downloads':
				$sql = 'SELECT SUM(total) as total
						FROM ' . ARCADE_DOWNLOAD_TABLE;
			break;

			case 'reports_open':
				$sql = 'SELECT COUNT(report_id) as total
						FROM ' . ARCADE_REPORTS_TABLE . '
						WHERE report_closed = ' . ARCADE_REPORT_OPEN;
			break;

			case 'users_played':
				if ($db->sql_layer === 'sqlite')
				{
					$sql = 'SELECT COUNT(user_id) as total
						FROM (
							SELECT DISTINCT user_id
							FROM ' . ARCADE_SCORES_TABLE . '
						)';
				}
				else
				{
					$sql = 'SELECT COUNT(DISTINCT user_id) as total
							FROM ' . ARCADE_SCORES_TABLE;
				}
			break;

			default:
				trigger_error('ARCADE_NO_TOTAL_TYPE_ERROR');
			break;
		}

		$result = $db->sql_query($sql);
		$total = (int) $db->sql_fetchfield('total');
		$db->sql_freeresult($result);

		return $total;
	}

	/**
	* This is never used for IBProV3 or IBPro arcadelib games because it will cause scoring errors with these types.
	* If a game is having scoring errors, try shutting this off for the specific game type.
	*/
	function get_protection($type)
	{
		global $arcade_config;

		$use_protection = false;
		switch($type)
		{
			case AMOD_GAME:
				$use_protection = ($arcade_config['protect_amod']) ? true : false;
			break;

			case IBPRO_GAME:
				$use_protection = ($arcade_config['protect_ibpro']) ? true : false;
			break;

			case V3ARCADE_GAME:
				$use_protection = ($arcade_config['protect_v3arcade']) ? true : false;
			break;

			default:
			break;
		}

		return $use_protection;
	}

	/**
	* Set the rating image for the game
	*/
	function set_rating_image($data, $mode = '')
	{
		global $user, $arcade, $auth_arcade, $template, $phpbb_root_path;

		$rate_block	= (!$user->data['is_registered'] || $user->data['is_bot'])	? true : false;
		$c_rate		= $auth_arcade->acl_get('c_rate', $data['cat_id'])			? true : false;
		$c_new_rate = $auth_arcade->acl_get('c_re_rate', $data['cat_id'])		? true : false;

		if ($data['game_votetotal'] > 0)
		{
			$star_width = (int) (($data['game_votesum'] / $data['game_votetotal']) * 16);
		}
		else
		{
			$star_width = 0;
		}

		$title = '';

		if (!$c_rate && !$rate_block)
		{
			$title.= $user->lang['ARCADE_RATING_NO_PERMISSION'];
		}
		else if ($c_rate && !$c_new_rate && (!empty($data['game_rating'])) && !$rate_block)
		{
			$title.= sprintf($user->lang['ARCADE_RATING_ALREADY'], $data['game_rating']);
		}

		$star_image = "<div style='height:16px; ". (!$mode ? "width:100px;margin-right:auto;margin-left:auto;" : '') ."' id='star_{$data['game_id']}'>
						<ul class='arcade-rate'" . ($title ? "title='{$title}'" : "") . ">
							<li class='arcade-current-rate' style='width:{$star_width}px;'>.</li>
							<li class='arcade-rating-num'><span title='{$user->lang['ARCADE_RATING_NUM']}'>({$data['game_votetotal']})</span></li>";

		if (($c_rate && (empty($data['game_rating'])) && !$rate_block) || ($c_rate && $c_new_rate && !$rate_block))
		{
			for ($x = 1; $x <= 5; $x++)
			{
				$star_image.= "<li><a onclick='arcade(\"rating\", \"{$data['game_id']}\", \"{$x}\", \"{$data['cat_id']}\", \"{$mode}\");return false;' title='" . sprintf($user->lang['ARCADE_RATING_VALUE'], $x) . "' class='arcade-rate-{$x}' rel='nofollow'>{$x}</a></li>";
			}
		}

		$star_image.= "</ul></div>";

		return $star_image;
	}

	/**
	* Set the favorites image for the game
	*/
	function set_fav_image($game_data, $game_id, $mode = '')
	{
		global $user;

		if (!$user->data['is_registered'] || $user->data['is_bot'])
		{
			return;
		}
		else
		{
			if (!empty($game_data[$game_id]))
			{
				$image = "<span id='fav_{$game_id}'><a onclick='arcade(\"fav\", \"{$game_id}\", \"" . ARCADE_FAV_DEL . "\", \"\", \"{$mode}\"); return false;'><img class='arcade_fav' src='" . $this->get_image('src', 'img', 'remove_favorite.png') . "' title='{$user->lang['ARCADE_REMOVE_FAV']}' alt='{$user->lang['ARCADE_REMOVE_FAV']}' /></a></span>";
			}
			else
			{
				$image = "<span id='fav_{$game_id}'><a onclick='arcade(\"fav\", \"{$game_id}\", \"" . ARCADE_FAV_ADD . "\", \"\", \"{$mode}\"); return false;'><img class='arcade_fav' src='" . $this->get_image('src', 'img', 'add_favorite.png')	  . "' title='{$user->lang['ARCADE_ADD_FAV']}'	  alt='{$user->lang['ARCADE_ADD_FAV']}'	/></a></span>";
			}

			return $image;
		}
	}

	/**
	* This function checks to make sure the game type is the expected type
	* and that the user and game ids exist in the sessions table.
	* It then returns the needed information in an array.
	*/
	function prepare_score($game_scorevar, $game_type)
	{
		global $db, $user, $score;

		$error = false;

		$sql_array = array(
			'SELECT'	=> 's.start_time,
							g.game_id, g.game_name, g.game_image, g.game_width, g.game_height, g.game_scorevar, g.game_highscore, g.game_highuser, g.game_highdate, g.game_scoretype, g.game_type, g.game_votetotal, g.game_votesum, g.game_cost, g.game_reward, g.game_jackpot, g.game_use_jackpot, g.cat_id,
							c.cat_name, c.cat_desc, c.cat_desc_uid, c.cat_desc_bitfield, c.cat_desc_options, c.parent_id, c.left_id, c.right_id, c.cat_parents, c.cat_test, c.cat_cost, c.cat_reward, c.cat_use_jackpot,
							u.username',
			'FROM'			=> array(ARCADE_SESSIONS_TABLE	=> 's',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(ARCADE_GAMES_TABLE		=> 'g'),
					'ON'	=> 's.game_id = g.game_id'
				),
				array(
					'FROM'	=> array(ARCADE_CATS_TABLE		=> 'c'),
					'ON'	=> 'g.cat_id = c.cat_id'
				),
				array(
					'FROM'	=> array(USERS_TABLE			=> 'u'),
					'ON'	=> 'g.game_highuser = u.user_id'
				),
			),
			'WHERE' 		=> "s.session_id = '" . $db->sql_escape($this->game_sid) . "'",
		);

		if (is_int($game_scorevar))
		{
			$sql_array['WHERE'] .= ' AND g.game_id = ' . (int) $game_scorevar;
		}
		else
		{
			$sql_array['WHERE'] .= " AND g.game_scorevar = '" . $db->sql_escape($game_scorevar) . "'";
		}

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row)
		{
			if (!is_int($game_scorevar))
			{
				$sql = 'SELECT g.game_id, g.game_type, g.game_scorevar, s.start_time
						FROM ' . ARCADE_SESSIONS_TABLE . ' s
						LEFT JOIN ' . ARCADE_GAMES_TABLE . " g ON s.game_id = g.game_id
						WHERE s.session_id = '" . $db->sql_escape($this->game_sid) . "'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if ($row && $game_scorevar != $row['game_scorevar'])
				{
					$error = 'SCOREVAR_ERROR';
				}
			}

			if ($error === false)
			{
				$error = 'BACK_BUTTON_ERROR';
			}
		}

		if ($error === false)
		{
			$row['current_time']	= time();
			$total_time				= intval($row['current_time'] - $row['start_time']);
			$row['total_time']		= ($total_time < 0) ? 0 : $total_time;

			// This will check somethings to try and verify the integrity of the score
			// If something is in question it will add an entry to the error table which
			// the admin will be able to view in the ACP.  There they can make a decision
			// what to do...

			$game_type_error = false;

			if (!($row['game_type'] == IBPRO_ARCADELIB_GAME && $game_type == IBPRO_GAME) && ($row['game_type'] != $game_type))
			{
				$game_type_error = true;
			}

			if ($game_type_error || ($row['total_time'] == 0) || ($error == 'SCOREVAR_ERROR'))
			{
				$error_type = ARCADE_ERROR_UNKNOWN;

				if ($row['game_type'] != $game_type)
				{
					$error_type = ARCADE_ERROR_GAMETYPE;
					$error = 'TYPE_ERROR';
				}
				else if ($row['total_time'] == 0)
				{
					$error_type = ARCADE_ERROR_TIME;
					$error = 'TIME_ERROR';
				}

				$sql_ary = array(
					'user_id'				=> $user->data['user_id'],
					'game_id'				=> $row['game_id'],
					'score'					=> $score,
					'error_date'			=> time(),
					'error_type'			=> $error_type,
					'game_type'				=> $row['game_type'],
					'submitted_game_type'	=> $game_type,
					'played_time'			=> $row['total_time'],
					'error_ip'				=> $user->ip,
				);

				$sql = 'INSERT INTO ' . ARCADE_ERRORS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
			}
		}

		if ($error)
		{
			$this->delete_session();
			trigger_error($user->lang['ARCADE_' . $error] . ((!$this->game_popup) ? $this->back_link() : ''));
		}

		$this->delete_session('key');

		return $row;
	}

	function games_list($type)
	{
		global $user, $template, $arcade_config, $auth_arcade;

		if ($arcade_config['load_list'])
		{
			$played_game_color = str_replace('#', '', $arcade_config['played_colour']);

			// Quick jump of all the games in the arcade...
			if ($user->data['is_registered'] && !$user->data['is_bot'] && $type == 'quick_jump')
			{
				$played_games = $this->get_played_games();
				$template->assign_var('PLAYED_GAME_COLOUR', $played_game_color);
			}

			foreach ($this->games as $gid => $game)
			{
				if (($type == 'quick_jump') && (!$auth_arcade->acl_get('c_play', $game['cat_id'])))
				{
					continue;
				}

				$played = true;
				if (!isset($played_games[$gid]) && $played_game_color)
				{
					$played = false;
				}

				// Only show a game in the games statistics drop down if its been played
				if (($type == 'game_jump') && ($game['game_highuser'] == 0 && $game['game_highdate'] == 0))
				{
					continue;
				}

				$template->assign_block_vars($type, array(
					'S_PLAYED_GAME' => ($type == 'quick_jump') ? $played : false,
					'GAME_ID' 		=> $gid,
					'GAME_NAME'		=> $game['game_name'],
				));
			}

		}
		else
		{
			$total_play_games = array();

			foreach ($this->games as $gid => $game)
			{
				if (($type == 'quick_jump') && (!$auth_arcade->acl_get('c_play', $game['cat_id'])))
				{
					continue;
				}

				$total_play_games[] = $gid;
			}

			$template->assign_var((($type == 'quick_jump') ? 'HEADER_' : 'STATS_') . 'GAMES_LIST_LOADING', (sizeof($total_play_games)) ? '<a style="cursor:pointer; font-weight: bold;" onclick="arcade(\'games_list\', \'' . (($type == 'quick_jump') ? 1 : 2) . '\'); return false;">' . $user->lang['ARCADE_GAMES_LIST_LOAD'] .'</a>' : $user->lang['ARCADE_NO_GAMES']);
		}
	}

	function users_list()
	{
		global $template, $arcade_config;

		if ($arcade_config['load_list'])
		{
			foreach ($this->users as $_uid => $_user)
			{
				$template->assign_block_vars('user_jump', array(
					'USER_ID' 	=> $_uid,
					'USERNAME' 	=> $_user['username'])
				);
			}
		}
		else
		{
			global $user;
			$template->assign_var('USERS_LIST_LOADING', (sizeof($this->users)) ? '<a style="cursor:pointer; font-weight: bold;" onclick="arcade(\'users_list\'); return false;">' . $user->lang['ARCADE_USERS_LIST_LOAD'] .'</a>' : false);
		}
	}
	/**
	* Replace the placeholder inside the private message
	*/
	function prepare_pm(&$text, $score_data, $lang)
	{
		global $user, $arcade_config, $phpEx;

		// Used in conjuction with the send_pm function.  This is
		// used to replace the place holders with the data...
		$text = str_replace('[game_id]'		, $score_data['game_id']		, $text);
		$text = str_replace('[game_name]'	, $score_data['game_name']		, $text);
		$text = str_replace('[old_user_id]'	, $score_data['old_user_id']	, $text);
		$text = str_replace('[old_username]', $score_data['old_username']	, $text);
		$text = str_replace('[new_user_id]'	, $user->data['user_id']		, $text);
		$text = str_replace('[new_username]', $user->data['username']		, $text);
		$text = str_replace('[old_score]'	, $score_data['old_score']		, $text);
		$text = str_replace('[new_score]'	, $score_data['new_score']		, $text);

		$username			= ($user->data['user_colour']) ? '[color=#' .  $user->data['user_colour'] . ']' . $user->data['username'] . '[/color]' : $user->data['username'];
		$game_image			= '[url=' . generate_board_url() . '/arcade.' . $phpEx . '?mode=play&amp;g='  . $score_data['game_id'] . $this->gametop . '][img]' . generate_board_url() . '/arcade.' . $phpEx . '?img=' . $score_data['game_image'] . '[/img][/url]';
		$game_link			= (($arcade_config['game_popup_icon_enabled']) ? $this->get_popup_img($score_data, 11, true) : '') . '[url=' . generate_board_url() . '/arcade.' . $phpEx . '?mode=play&amp;g='  . $score_data['game_id'] . $this->gametop . ']' . $score_data['game_name'] . '[/url]';
		$new_user_link		= '[url=' . generate_board_url() . '/arcade.' . $phpEx . '?mode=stats&amp;u=' . $user->data['user_id'] . ']'	 . $username . '[/url]';
		$old_user_link		= '[url=' . generate_board_url() . '/arcade.' . $phpEx . '?mode=stats&amp;u=' . $score_data['old_user_id'] . ']' . $lang['ARCADE_HERE'] . '[/url]';
		$game_stats_link	= '[url=' . generate_board_url() . '/arcade.' . $phpEx . '?mode=stats&amp;g=' . $score_data['game_id'] . ']'	 . $lang['ARCADE_GAME_STATS'] . '[/url]';

		$text = str_replace('[game_image]'		, $game_image		, $text);
		$text = str_replace('[game_link]'		, $game_link		, $text);
		$text = str_replace('[new_user_link]'	, $new_user_link	, $text);
		$text = str_replace('[old_user_link]'	, $old_user_link	, $text);
		$text = str_replace('[game_stats_link]'	, $game_stats_link	, $text);

		return;
	}

	/**
	* Send a private message with the
	* message set in the acp
	*/
	function send_pm($score_data)
	{
		global $db, $user, $phpbb_root_path, $phpEx;

		if (!$user->data['is_registered'])
		{
			return;
		}

		$sql = 'SELECT user_lang
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $score_data['old_user_id'];

		$result = $db->sql_query($sql);
		$user_lang = $db->sql_fetchfield('user_lang');
		$db->sql_freeresult($result);

		if($user->data['user_lang'] != $user_lang && file_exists($phpbb_root_path . 'language/' . $user_lang . "/mods/arcade.{$phpEx}"))
		{
			$lang = array();
			include($phpbb_root_path . 'language/' . $user_lang . "/mods/arcade.{$phpEx}");
		}
		else
		{
			$lang = $user->lang;
		}

		$subject = $lang['ARCADE_PM_SUBJECT'];
		$message = $lang['ARCADE_PM_MESSAGE'];

		include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

		$message_parser = new parse_message();

		$this->prepare_pm($subject, $score_data, $lang);
		$this->prepare_pm($message, $score_data, $lang);

		$subject = utf8_normalize_nfc($subject);
		$message_parser->message = utf8_normalize_nfc($message);
		$address_list['u'][$score_data['old_user_id']] = 'to';

		$message_parser->parse(true, true, true);

		$pm_data = array(
					'msg_id'				=> 0,
					'from_user_id'			=> $user->data['user_id'],
					'from_user_ip'			=> $user->data['user_ip'],
					'from_username'			=> $user->data['username'],
					'reply_from_root_level'	=> 0,
					'reply_from_msg_id'		=> 0,
					'icon_id'				=> 0,
					'enable_sig'			=> true,
					'enable_bbcode'			=> true,
					'enable_smilies'		=> true,
					'enable_urls'			=> true,
					'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
					'bbcode_uid'			=> $message_parser->bbcode_uid,
					'message'				=> $message_parser->message,
					'attachment_data'		=> $message_parser->attachment_data,
					'filename_data'			=> $message_parser->filename_data,
					'address_list'			=> $address_list
		);
		unset($message_parser);
		submit_pm('post', $subject, $pm_data, true);

		return;
	}

	/**
	* Limits access to play games based on parameters set in the ACP
	*/
	function limit_play($cat_id)
	{
		global $user, $db, $auth_arcade, $arcade_config;

		if ($auth_arcade->acl_get('c_ignorecontrol', $cat_id))
		{
			return;
		}

		if ($arcade_config['limit_play'] == LIMIT_PLAY_TYPE_POSTS || $arcade_config['limit_play'] == LIMIT_PLAY_TYPE_DAYS || $arcade_config['limit_play'] == LIMIT_PLAY_TYPE_BOTH)
		{
			$total_posts = $arcade_config['limit_play_total_posts'];
			$days = $arcade_config['limit_play_days'];
			$posts = $arcade_config['limit_play_posts'];
			$current_time = time();
			$old_time = $current_time - (60 * 60 * 24 * $days);
			$error = array();

			if ($arcade_config['limit_play'] == LIMIT_PLAY_TYPE_POSTS || $arcade_config['limit_play'] == LIMIT_PLAY_TYPE_BOTH)
			{
				$sql = 'SELECT COUNT(post_id) as total_posts
						FROM ' . POSTS_TABLE . '
						WHERE poster_id = ' . (int) $user->data['user_id'] . '
						AND post_postcount > 0';
				$result = $db->sql_query($sql);
				$actual_total_posts = $db->sql_fetchfield('total_posts');
				$db->sql_freeresult($result);

				if ($actual_total_posts < $total_posts)
				{
					$error[] = sprintf($user->lang['ARCADE_LIMIT_PLAY_TYPE_POSTS'], $total_posts,  $total_posts - $actual_total_posts);
				}
			}

			if ($arcade_config['limit_play'] == LIMIT_PLAY_TYPE_DAYS || $arcade_config['limit_play'] == LIMIT_PLAY_TYPE_BOTH)
			{
				$sql = 'SELECT COUNT(post_id) as total_posts
						FROM ' . POSTS_TABLE . '
						WHERE poster_id = ' . (int) $user->data['user_id'] . '
						AND post_time
						BETWEEN ' . (int) $old_time . ' AND ' . (int) $current_time . '
						AND post_postcount > 0';
				$result = $db->sql_query($sql);
				$actual_posts_per_day = $db->sql_fetchfield('total_posts');
				$db->sql_freeresult($result);

				if ($actual_posts_per_day < $posts)
				{
					$error[] = sprintf($user->lang['ARCADE_LIMIT_PLAY_TYPE_DAYS'], $posts, $days, $posts - $actual_posts_per_day, $days);
				}
			}

			if (sizeof($error))
			{
				trigger_error(implode('<br /><br />', $error) . $this->back_link());
			}

		}
	}

	/**
	* Set the headers to notify the browser not to cache
	* If this isn't here the IBPro v3 games will not work
	*/
	function set_header_no_cache()
	{
		@header("HTTP/1.0 200 OK");
		@header("HTTP/1.1 200 OK");
		@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		@header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		@header('Pragma: no-cache');
	}

	function update_last_play_time($game_id)
	{
		global $db, $user, $auth_arcade, $arcade_config;

		$time			= time();
		$cat_id			= $this->get_game_field($game_id, 'cat_id');
		$interval		= ($user->data['user_id'] == ANONYMOUS) ? $arcade_config['play_anonymous_interval'] : $arcade_config['play_interval'];
		$remaining_time = ($time - $user->data['user_arcade_last_play'] > $interval) ? true : false;

		if ($interval && !$auth_arcade->acl_get('c_ignoreflood_play', $cat_id) && $remaining_time)
		{
			$sql = 'UPDATE ' . ARCADE_USERS_TABLE . '
					SET user_arcade_last_play = ' . $time . '
					WHERE user_id = ' . (int) $user->data['user_id'];
			$db->sql_query($sql);
		}
	}

	/**
	* Find available compress methods on the server
	*/
	function compress_methods($remove_dot = false)
	{
		if ($remove_dot)
		{
			$methods = array('tar');
			$available_methods = array('gz' => 'zlib', 'bz2' => 'bz2', 'zip' => 'zlib');
		}
		else
		{
			$methods = array('.tar');
			$available_methods = array('.tar.gz' => 'zlib', '.tar.bz2' => 'bz2', '.zip' => 'zlib');
		}

		foreach ($available_methods as $type => $module)
		{
			if (!@extension_loaded($module))
			{
				continue;
			}
			$methods[] = $type;
		}

		return $methods;
	}

	/**
	* Cache and return users who have played the arcade based on viewing users permission
	*/
	function obtain_arcade_users()
	{
		global $db, $arcade_config;

		// This is used to allow the user to see every user in a drop
		// so they can select one to see his/her scores...
		$sql_array = array(
			'SELECT'	=> 'u.user_id, u.username, u.username_clean',

			'FROM'		=> array(
				ARCADE_SCORES_TABLE	=> 's',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 's.user_id = u.user_id'
				),
				array(
					'FROM'	=> array(ARCADE_GAMES_TABLE => 'g'),
					'ON'	=> 's.game_id = g.game_id'
				),
			),

			'WHERE'		=> 'u.user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ') AND ' . $db->sql_in_set('g.cat_id', $this->get_permissions('c_view'), false, true),

			'ORDER_BY' 	=> 'u.username_clean ASC',
		);

		$sql = $db->sql_build_query('SELECT_DISTINCT', $sql_array);
		$result = $db->sql_query($sql);

		$arcade_users = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$arcade_users[$row['user_id']] = array(
				'user_id'	=> $row['user_id'],
				'username'	=> $row['username']
			);
		}
		$db->sql_freeresult($result);


		return $arcade_users;
	}

	/**
	* Cache and return the latest highscores based on viewing users permission
	*/
	function obtain_latest_highscores($limit)
	{
		global $db, $arcade_config;

		$sql_array = array(
			'SELECT'	=> 'g.game_id, g.game_name, g.game_width, g.game_height, g.game_highuser, g.game_highscore, g.game_highdate, g.cat_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type',

			'FROM'		=> array(
				ARCADE_GAMES_TABLE	=> 'g',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.game_highuser = u.user_id'
				),
			),

			'WHERE'		=> 'g.game_highuser > 0 AND ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true),

			'ORDER_BY'	=> 'g.game_highdate DESC'
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $limit, 0, $arcade_config['cache_time'] * 3600);

		$row = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		return $row;
	}

	function get_popup_img($data, $wh = '', $raw = false)
	{
		global $auth_arcade;

		if ((!$raw && !$auth_arcade->acl_get('c_popup', $data['cat_id'])) || ($raw && !$auth_arcade->acl_raw_data($data['old_user_id'], 'c_popup', $data['cat_id'])))
		{
			return;
		}

		static $_popup_cache;

		if (empty($_popup_cache))
		{
			global $user;

			$_popup_cache['game_url'] = $this->url("mode=popup&amp;g={GAME_ID}");
			$_popup_cache['pop_img']  = '<a href="#" onclick="arcade_popup_game(\'{URL}\', \'{WIDTH}\', \'{HEIGHT}\'); return false;"><img style="vertical-align:middle;" src="' . $this->get_image('src', 'img', 'popup.png') . '" title="' . $user->lang['ARCADE_POPUP_LINK'] . '" alt="' . $user->lang['ARCADE_POPUP_LINK'] . '" /></a>&nbsp;';
		}

		return str_replace(array('{URL}', '{WIDTH}', '{HEIGHT}'), array(str_replace('{GAME_ID}', $data['game_id'], $_popup_cache['game_url']), $data['game_width'], $data['game_height']), (($wh) ? str_replace('middle;"', 'middle;" width="' . $wh . '" height="' . $wh . '"', $_popup_cache['pop_img']) : $_popup_cache['pop_img']));
	}

	/**
	* Cache and return the newest games based on viewing users permission
	*/
	function obtain_newest_games($limit)
	{
		global $db, $arcade_config;

		$sql = 'SELECT game_id, game_image, game_name, game_width, game_height, game_installdate, cat_id
			FROM ' . ARCADE_GAMES_TABLE . '
			WHERE ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true) . '
			ORDER BY game_installdate DESC';

		$result = $db->sql_query_limit($sql, $limit, 0, $arcade_config['cache_time'] * 3600);

		$row = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/**
	* Cache and return the most/least downloaded games based on viewing users permission
	*/
	function obtain_downloaded_games($type, $limit)
	{
		global $db, $arcade_config;

		$type = strtolower($type);
		switch ($type)
		{
			case 'most':
				$order = 'DESC';
			break;

			case 'least':
				$order = 'ASC';
			break;

			default:
				trigger_error('ARCADE_NO_ORDER_TYPE_ERROR');
			break;
		}

		$sql = 'SELECT game_id, game_image, game_name, game_scorevar, game_installdate, game_download_total, cat_id
			FROM ' . ARCADE_GAMES_TABLE . '
			WHERE ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true) . '
				AND game_download_total > 0
			ORDER BY game_download_total ' . $order;

		$result = $db->sql_query_limit($sql, $limit, 0, $arcade_config['cache_time'] * 3600);

		$row = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/**
	* Cache and return the most/least popular games based on viewing users permission
	*/
	function obtain_popular_games($type, $limit)
	{
		global $db, $arcade_config;

		$type = strtolower($type);
		switch ($type)
		{
			case 'most':
				$order = 'DESC';
			break;

			case 'least':
				$order = 'ASC';
			break;

			default:
				trigger_error('ARCADE_NO_ORDER_TYPE_ERROR');
			break;
		}

		$sql = 'SELECT game_id, game_name, game_image, game_plays, game_scorevar, cat_id
			FROM ' . ARCADE_GAMES_TABLE . '
			WHERE ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true) . '
				AND game_plays > 0
			ORDER BY game_plays ' . $order;

		$result = $db->sql_query_limit($sql, $limit, 0, $arcade_config['cache_time'] * 3600);

		$row = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/**
	* Cache and return longest held highscore based on viewing users permission
	*/
	function obtain_longest_highscores($limit)
	{
		global $db, $arcade_config;

		$sql_array = array(
			'SELECT'	=> 'g.game_id, g.game_name, g.game_highscore, g.game_highdate, g.cat_id, u.username, u.user_id, u.user_colour',

			'FROM'		=> array(
				ARCADE_GAMES_TABLE	=> 'g',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.game_highuser = u.user_id'
				),
			),

			'WHERE'		=> 'g.game_highscore > 0 AND ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true),

			'ORDER_BY'	=> 'g.game_highdate ASC'
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $limit, 0, $arcade_config['cache_time'] * 3600);

		$row = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/**
	* Return the games based on viewing users permission
	*/
	function obtain_games()
	{
		global $db, $arcade_config;

		$sql = 'SELECT game_id, game_name, game_name_clean, game_swf, game_scorevar, game_highuser, game_highdate, cat_id
			FROM ' . ARCADE_GAMES_TABLE . '
			WHERE ' . $db->sql_in_set('cat_id', $this->get_permissions('c_view'), false, true) . '
			ORDER BY game_name_clean ASC';
		$result = $db->sql_query($sql, $arcade_config['cache_time'] * 3600);

		$games = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$games[$row['game_id']] = array(
				'game_id'			=> $row['game_id'],
				'game_name'			=> $row['game_name'],
				'game_swf'			=> $row['game_swf'],
				'game_scorevar'		=> $row['game_scorevar'],
				'game_highuser'		=> $row['game_highuser'],
				'game_highdate'		=> $row['game_highdate'],
				'cat_id'			=> $row['cat_id'],
			);
		}
		$db->sql_freeresult($result);

		return $games;

	}

	/**
	* Initialize the points system integration
	*/
	function init_points()
	{
		global $config, $db, $phpbb_root_path, $phpEx, $arcade_config;

		$this->points = array(
			'installed'		=> false,
			'show'			=> false,
			'type'			=> false,
			'enabled'		=> true,
			'name'			=> '',
			'total'			=> 0,
		);

		if (defined('IN_ULTIMATE_POINTS'))
		{
			$this->points['type'] = ARCADE_ULTIMATE_POINTS_SYSTEM;
			$this->points['installed'] = true;
			$this->points['enabled'] = $config['points_enable'];
			define('USER_POINTS', 'user_points');
		}
		else if (file_exists($phpbb_root_path . 'includes/mods/functions_points.' . $phpEx))
		{
			if (!function_exists('add_points'))
			{
				include($phpbb_root_path . 'includes/mods/functions_points.' . $phpEx);
			}

			if (!function_exists('set_bank'))
			{
				$this->points['type'] = ARCADE_SIMPLE_POINTS_SYSTEM;
				$this->points['installed'] = true;
				$this->points['enabled'] = $config['points_enable'];
				define('USER_POINTS', 'user_points');
			}
			else
			{
				$this->points['type'] = ARCADE_POINTS_SYSTEM;
				$this->points['installed'] = true;

				$sql = 'SELECT points_enable FROM ' . POINTS_CONFIG_TABLE;
				$result = $db->sql_query($sql);
				$this->points['enabled'] = $db->sql_fetchfield('points_enable');
				$db->sql_freeresult($result);
			}
		}
		else if (file_exists($phpbb_root_path . 'includes/mods/cash/cash_class.' . $phpEx))
		{
			$this->points['type'] = ARCADE_CASH_MOD;
			$this->points['installed'] = true;
		}

		$this->points['show'] = ($this->points['installed'] && $this->points['enabled'] && $arcade_config['use_points']) ? true : false;

		if ($this->points['show'])
		{
			$points_array = $this->get_points();
			$this->points['name'] = $points_array['name'];
			$this->points['total'] = $points_array['total'];
		}
	}

	/**
	* Get points total and points name for a specified user
	* If no user is specified get the data for the current user
	*/
	function get_points($user_id = false)
	{
		global $user, $db, $config, $arcade_config;

		$return = array(
			'total'	=> 0,
			'name'	=> '',
		);

		if ($this->points['type'] == ARCADE_CASH_MOD)
		{
			$user_id = ($user_id) ? $user_id : $user->data['user_id'];

			$sql = $db->sql_build_query('SELECT', array(
				'SELECT'	=> 'c.cash_name, ca.cash_amt',
				'FROM'		=> array(
					CASH_TABLE			=> 'c',
					CASH_AMOUNT_TABLE	=> 'ca',
				),
				'WHERE'		=> 'ca.user_id = ' . (int) $user_id . ' AND c.cash_id = ' . (int) $arcade_config['cm_currency_id'] . ' AND ca.cash_id = c.cash_id',
			));

			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$return = array(
				'total'	=> $row['cash_amt'],
				'name'	=> $row['cash_name'],
			);

		}
		else
		{
			if ($this->points['type'] == ARCADE_SIMPLE_POINTS_SYSTEM || $this->points['type'] == ARCADE_ULTIMATE_POINTS_SYSTEM)
			{
				$return['name'] = $config['points_name'];
			}
			else
			{
				$sql = 'SELECT points_name
						FROM ' . POINTS_CONFIG_TABLE;
				$result = $db->sql_query($sql);
				$return['name'] = $db->sql_fetchfield('points_name');
				$db->sql_freeresult($result);
			}

			if (!$user_id)
			{
				$return['total'] = $user->data[USER_POINTS];
			}
			else
			{
				$sql = 'SELECT ' . USER_POINTS . '
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . (int) $user_id;
				$result = $db->sql_query($sql);
				$return['total'] = $db->sql_fetchfield(USER_POINTS);
				$db->sql_freeresult($result);
			}
		}

		return $return;
	}

	/**
	* Function to add or subtact points from a user
	*/
	function set_points($mode, $user_id, $amount)
	{
		global $user, $db, $arcade_config;

		// Return true if the amount is 0 or free (-1)
		if ($amount <= 0)
		{
			return true;
		}

		$result = false;
		$mode = strtolower($mode);
		switch ($mode)
		{
			case 'add':
				if ($this->points['type'] == ARCADE_CASH_MOD)
				{
					global $cash;
					$result = $cash->give_cash($user_id, $amount, $arcade_config['cm_currency_id']);
				}
				else
				{
					$sql = 'UPDATE ' . USERS_TABLE . '
							SET ' . USER_POINTS . ' = ' . USER_POINTS . ' + ' . $amount . '
							WHERE user_id = ' . (int) $user_id;
					$db->sql_query($sql);

					$result = true;
				}

			break;

			case 'subtract':
				if ($this->points['type'] == ARCADE_CASH_MOD)
				{
					global $cash;
					$result = $cash->take_cash($user_id, $amount, $arcade_config['cm_currency_id']);

				}
				else
				{
					// The user does not have enough points
					if ($user->data[USER_POINTS] < $amount)
					{
						$result = false;
					}
					else
					{
						$sql = 'UPDATE ' . USERS_TABLE . '
								SET ' . USER_POINTS . ' = ' . USER_POINTS . ' - ' . $amount . '
								WHERE user_id = ' . (int) $user_id;
						$db->sql_query($sql);

						$result = true;
					}
				}

			break;

			default:
			break;
		}

		// If setting points was successful, update the users current total for display as well
		if ($result && $user->data['user_id'] == $user_id)
		{
			if ($mode == 'add')
			{
				$this->points['total'] = $this->points['total'] + $amount;
			}
			else
			{
				$this->points['total'] = $this->points['total'] - $amount;
			}
		}

		return $result;
	}

	/**
	* Return cost infomation for a game
	*/
	function get_cost($data)
	{
		global $arcade_config;

		$cost = 0;
		$game_cost = (float) $data['game_cost'];
		$cat_cost = (float) $data['cat_cost'];

		if (!empty($game_cost))
		{
			$cost = $game_cost;
		}
		else if (!empty($cat_cost))
		{
			$cost = $cat_cost;
		}
		else
		{
			$cost = (float) $arcade_config['game_cost'];
		}

		return (float) $cost;
	}

	/**
	* Return reward infomation for a game
	*/
	function get_reward($data)
	{
		global $db, $arcade_config;

		$reward = 0;
		if ($this->use_jackpot($data))
		{
			$reward = (float) $data['game_jackpot'];
		}
		else
		{
			$game_reward = (float) $data['game_reward'];
			$cat_reward = (float) $data['cat_reward'];

			if (!empty($game_reward))
			{
				$reward = $game_reward;
			}
			else if (!empty($cat_reward))
			{
				$reward = $cat_reward;
			}
			else
			{
				$reward = (float) $arcade_config['game_reward'];
			}
		}

		return (float) $reward;
	}

	/**
	* Find out if the game should be using the jackpot setting
	*/
	function use_jackpot($data)
	{
		global $arcade_config;

		$result = false;

		if (!empty($data['game_use_jackpot']))
		{
			$result = true;
		}
		else if (!empty($data['cat_use_jackpot']))
		{
			$result = true;
		}
		else
		{
			$result = $arcade_config['use_jackpot'];
		}

		return $result;
	}

	/**
	* Function to add or clear jackpot
	* $mode can be 'add' or 'clear'
	*/
	function set_jackpot($mode, $data)
	{
		global $db, $arcade_config;

		$mode = strtolower($mode);
		switch ($mode)
		{
			case 'add':
				$cost = $this->get_cost($data);

				// Return if the amount is 0 or free (-1)
				if ($cost <= 0)
				{
					return false;
				}

				// Make sure jackpot is not less than the minimum
				$jackpot = ($data['game_jackpot'] < $arcade_config['jackpot_minimum']) ? $arcade_config['jackpot_minimum'] : $data['game_jackpot'];
				// Add to jackpot
				$jackpot += $cost;
				// Make sure jackpot is not more than the maximum if a maximum is set
				if ($arcade_config['jackpot_maximum'] && $jackpot > $arcade_config['jackpot_maximum'])
				{
					$jackpot = $arcade_config['jackpot_maximum'];
				}

				$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
						SET game_jackpot = ' . (float) $jackpot . '
						WHERE game_id = ' . (int) $data['game_id'];
				$db->sql_query($sql);

				return $jackpot;
			break;

			case 'clear':
				$sql = 'UPDATE ' . ARCADE_GAMES_TABLE . '
						SET game_jackpot = ' . (float) $arcade_config['jackpot_minimum'] . '
						WHERE game_id = ' . (int) $data['game_id'];
				$db->sql_query($sql);
			break;

			default:
			break;
		}
		return false;
	}

	/**
	* Returns list of categories to exclude from search based
	* on ACP settings and whether or not the category has an age limit
	* and/or is password protected
	*/
	function games_search_protection()
	{
		global $db, $user, $arcade_config;

		$cat_ids = array();

		$pass_check = (($arcade_config['search_filter_password'] == ARCADE_CHECK_EVERYONE) || ($arcade_config['search_filter_password']	== ARCADE_CHECK_USER_NORMAL && $user->data['user_type'] != USER_FOUNDER)) ? true : false;
		$age_check  = (($arcade_config['search_filter_age']		== ARCADE_CHECK_EVERYONE) || ($arcade_config['search_filter_age']		== ARCADE_CHECK_USER_NORMAL && $user->data['user_type'] != USER_FOUNDER)) ? true : false;

		if (!$pass_check && !$age_check)
		{
			return $cat_ids;
		}

		$birthday = $user->data['user_birthday'];

		$sql = 'SELECT c.cat_id, c.cat_age, c.cat_password, a.user_id
				FROM	  ' . ARCADE_CATS_TABLE   . ' c
				LEFT JOIN ' . ARCADE_ACCESS_TABLE . " a ON (c.cat_id = a.cat_id
				AND a.session_id = '" . $db->sql_escape($user->session_id) . "')";
		$result = $db->sql_query($sql);

		while($row = $db->sql_fetchrow($result))
		{
			$user_birthday = explode('-', $birthday);
			$user_age = isset($user_birthday[2]) ? (int) $user_birthday[2] : false;

			if ((!$user_age && $row['cat_age'] && $age_check) || ($row['cat_age'] && $user_age > (date('Y') - $row['cat_age']) && $age_check) || ($row['cat_password'] && $row['user_id'] != $user->data['user_id'] && $pass_check))
			{
				// forbidden categories
				$cat_ids[] = $row['cat_id'];
			}
		}
		$db->sql_freeresult($result);

		return $cat_ids;
	}

	/**
	* Verify a users age against category age limit
	*/
	function verify_age($type, $age)
	{
		global $user, $arcade_config;

		if ($arcade_config['founder_exempt'] && $user->data['user_type'] == USER_FOUNDER)
		{
			return;
		}

		if (!$user->data['is_registered'])
		 {
			if ($user->data['is_bot'])
			{
				redirect($this->url());
			}
			else
			{
				$message = ($type == 'cat') ? 'ARCADE_BLOCKED_CAT_VIEW' : 'ARCADE_BLOCKED_GAME_PLAY';
				trigger_error($message);
			}
		 }

		 $user_birthday = explode('-', $user->data['user_birthday']);
		 $user_age = isset($user_birthday[2]) ? (int) $user_birthday[2] : false;

		 if (!$user_age)
		 {
			$message = ($type == 'cat') ? 'ARCADE_BLOCKED_NO_AGE_CAT' : 'ARCADE_BLOCKED_NO_AGE_PLAY';
			trigger_error($message);
		 }

		 if ($user_age > date('Y') - $age)
		 {
			$message = ($type == 'cat') ? 'ARCADE_BLOCKED_CAT_AGE' : 'ARCADE_BLOCKED_PLAY_AGE';
			trigger_error(sprintf($user->lang[$message], $age));
		 }
	}
}

?>