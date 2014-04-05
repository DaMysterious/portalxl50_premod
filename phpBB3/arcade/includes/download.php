<?php
/**
*
* @package arcade
* @version $Id: download.php 1663 2011-09-22 12:09:30Z killbill $
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

if (!$game_id)
{
	trigger_error($user->lang['NO_GAME_ID'] . $arcade->back_link());
}

$game_info = $arcade->get_game_data($game_id);

$s_download = false;
if ($game_info['cat_download'])
{
	if ($game_info['game_download'])
	{
		if ($auth_arcade->acl_get('c_download', $game_info['cat_id']))
		{
			$s_download = true;
		}
	}
}

if (!$s_download)
{
	trigger_error($user->lang['NO_PERMISSION_ARCADE_DOWNLOAD'] . $arcade->back_link());
}

// Check flood control
$interval = ($user->data['user_id'] == ANONYMOUS) ? $arcade_config['download_anonymous_interval'] : $arcade_config['download_interval'];
if ($interval && !$auth_arcade->acl_get('c_ignoreflood_download', $game_info['cat_id']))
{
	$sql = 'SELECT download_time
		FROM ' . ARCADE_DOWNLOAD_TABLE . '
		WHERE user_id = ' . $user->data['user_id'] . '
		ORDER BY download_time DESC';
	$result = $db->sql_query_limit($sql, 1);
	$last_download_time = $db->sql_fetchfield('download_time');
	$db->sql_freeresult($result);

	$time = time() - $interval;
	if ($last_download_time > $time)
	{
		trigger_error(sprintf($user->lang['ARCADE_NO_DOWNLOAD_TIME'], $arcade->time_format($interval, true), $arcade->time_format($last_download_time - $time, true)) . $arcade->back_link());
	}
}

generate_arcade_nav($game_info, true);

$use_method = request_var('use_method', '');
$methods = $arcade->compress_methods();

// Let the user decide in which format he wants to have the game downloaded in
if (!$use_method)
{
	$page_title = sprintf($user->lang['ARCADE_DOWNLOAD_FORMAT'], $game_info['game_name']);

	$radio_buttons = '';
	foreach ($methods as $method)
	{
		$checked = (($method == $use_method) || (!$use_method && $method == '.tar')) ? ' checked="checked"' : '';
		$radio_buttons .= '<input type="radio"' . ((!$radio_buttons) ? ' id="use_method"' : '') . ' class="radio" value="' . $method . '" name="use_method"' . $checked . ' />&nbsp;' . $method . '&nbsp;';
	}

	$template->assign_vars(array(
		'S_SELECT_METHOD'			=> true,
		'GAME_NAME'					=> $game_info['game_name'],
		'ARCADE_DOWNLOAD_FORMAT' 	=> $page_title,
		'U_ACTION'					=> $arcade->url("mode=download&amp;g={$game_id}"),
		'RADIO_BUTTONS'				=> $radio_buttons)
	);

	display_arcade_online();
	// Output page
	page_header($page_title, false);

	$template->set_filenames(array(
		'body' => 'arcade/download_body.html')
	);

	page_footer();
}

$arcade->download_game($game_info, $use_method, $methods);

?>