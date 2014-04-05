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
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

// We use this to display the flash and image files for the game.  This is to hide the acutal path from the end user.
// However this will not work with IBProV3 or IBPro arcadelib games so the real path must be used for the flash file for those type of games.
if (isset($_GET['swf']) || isset($_GET['img']))
{
	require($phpbb_root_path . 'arcade/includes/protect.' . $phpEx);
}

// implicit else: we are not in swf or img mode
include($phpbb_root_path . 'common.' . $phpEx);

// Handling of V3arcade games
// Only accept the var if its a POST
$v3arcade = (isset($_POST['sessdo'])) ? request_var('sessdo', '') : '';
if (!empty($v3arcade))
{
	$scoretype = 'V3ARCADE_GAME';
	require($phpbb_root_path . 'arcade/includes/scoretype.' . $phpEx);
}
// End Handling of V3arcade games

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

include($phpbb_root_path . 'arcade/includes/common.' . $phpEx);
// Initialize arcade auth
$auth_arcade->acl($user->data);

if (!$auth_arcade->acl_get('u_arcade'))
{
	trigger_error('ARCADE_NO_PERMISSION');
}

// Is arcade disabled and user not an admin?
if ($arcade_config['arcade_disable'] && !$auth->acl_get('a_'))
{
	send_status_line(503, 'Service Unavailable');

	$message = (!empty($arcade_config['arcade_disable_msg'])) ? $arcade_config['arcade_disable_msg'] : 'ARCADE_DISABLE';
	trigger_error($message);
}

// Initialize arcade class
$arcade = new arcade();

// Get the varibles we will use to build the arcade pages
$mode = request_var('mode', '');
$cat_id	= request_var('c', 0);
$game_id = request_var('g', 0);
$user_id = request_var('u', 0);
$start = request_var('start', 0);
$search_id = request_var('search_id', '');
$term = utf8_normalize_nfc(request_var('term', '', true));
$type = request_var('type', '');

if ($mode == 'download' && $type == 'data' || $type == 'list')
{
	if ($arcade_config['download_list'])
	{
		if ($type == 'data')
		{
			$arcade->display_download_data();
		}
		else
		{
			$sort_time = request_var('st', 0);
			$sort_key = request_var('sk', 'n');
			$sort_dir = request_var('sd', 'a');
			$per_page = request_var('per_page', 50);

			$arcade->display_download_list($cat_id, $start, $sort_key, $sort_dir, $sort_time, $per_page);
		}
	}
	else
	{
		$mode = $type = '';
	}
}

switch ($mode)
{
	case 'random':
		if ($random_game_id = $arcade->get_random_game())
		{
			redirect($arcade->url('mode=play&amp;g='. $random_game_id));
		}
	break;

	case 'report':
		if (empty($game_id))
		{
			break;
		}

		include($phpbb_root_path . 'arcade/includes/reports.'.$phpEx);
	break;

	case 'stats':
		if ($auth_arcade->acl_get('u_viewstats'))
		{
			if (!$user_id)
			{
				$arcade->set_data($mode);
			}

			include($phpbb_root_path . 'arcade/includes/stats.'.$phpEx);
		}
		else
		{
			trigger_error($user->lang['ARCADE_NO_PERMISSION_STATS'] . $arcade->back_link());
		}
	break;

	case 'download':
		include($phpbb_root_path . 'arcade/includes/download.'.$phpEx);
	break;

	case 'play':
	case 'popup':
		// We won't let guests play games in a new window, it would cause some other problems.
		if (empty($game_id))
		{
			break;
		}

		if (!$user->data['is_registered'] && !$auth_arcade->acl_getc_global('c_play'))
		{
			$total_games = $arcade->get_total('games');
			$message = sprintf($user->lang['ARCADE_REGISTER_MESSAGE_PLAY'], $total_games);

			if ($mode != 'popup')
			{
				$message .= '<br />' . sprintf($user->lang['ARCADE_REGISTER_LOGIN_MESSAGE'], '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", "mode=register") . '">', '</a>', '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login&amp;redirect=' . urlencode("arcade.$phpEx?mode=play&g=$game_id")) . '">', '</a>');
			}

			trigger_error($message);
		}

		include($phpbb_root_path . 'arcade/includes/play.'.$phpEx);
	break;

	case 'score':
	case 'done':
		if (empty($game_id))
		{
			break;
		}

		// Well you should never get to this condition if you are not
		// logged in however just in case...
		if (!$user->data['is_registered'])
		{
			redirect(append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'));
		}

		// If playing the game in a popup window only use simple header when calling trigger_error()
		$arcade->session_begin();
		$popup = $use_simple_header = $arcade->game_popup;

		include($phpbb_root_path . 'arcade/includes/score.'.$phpEx);
	break;

	case 'cat':
		// Break out and diplay main arcade page if the cat_id is not set
		if (empty($cat_id))
		{
			break;
		}

	case 'fav':
	case 'search':
		include($phpbb_root_path . 'arcade/includes/games.'.$phpEx);
	break;

	default:
	// The default case is to continue to load the page
	break;
}
// At the index display all the categories...
// Configure style, language, etc.
display_arcade_header($arcade_config['welcome_index'], $arcade_config['search_index'], $arcade_config['links_index']);
display_arcade();
display_arcade_online();

// Output page
page_header($user->lang['ARCADE_INDEX'], false);

// Assign index specific vars
$template->assign_vars(array(
	'CAT_IMG'				=> $user->img('forum_read', 'NO_NEW_GAMES'),
	'CAT_NEW_IMG'			=> $user->img('forum_unread', 'NEW_GAMES'),
	'CAT_LOCKED_IMG'		=> $user->img('forum_read_locked', 'NEW_GAMES_LOCKED'),
	'CAT_NEW_LOCKED_IMG'	=> $user->img('forum_unread_locked', 'NO_NEW_GAMES_LOCKED'),
));

$template->set_filenames(array(
	'body' => 'arcade/index_body.html')
);

page_footer();

?>