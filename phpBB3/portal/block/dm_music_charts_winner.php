<?php
/**
* @package DM Music Charts Block on Portal XL 5.0
* @version $Id$
* @copyright (C) 2010 femu - http://die-muellers.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
   exit;
}

/**
* Some mod related variables and includes
*/
$user->add_lang('mods/dm_music_charts');

$sql = 'SELECT c.*, u.user_id, u.username, u.user_colour
	FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
	LEFT JOIN ' . USERS_TABLE . ' u
		ON u.user_id = c.chart_poster_id
	WHERE chart_hot > 0
	ORDER BY chart_hot ASC';
$result = $db->sql_query_limit($sql, 3);

while ($row = $db->sql_fetchrow($result))
{
	if ($row['chart_hot'] == 1)
	{
		$rank_img = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/1st.gif" alt="" title="' . $user->lang['DM_MC_FIRST'] . '" />';
	}
	else if ($row['chart_hot'] == 2)
	{
		$rank_img = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/2nd.gif" alt="" title="' . $user->lang['DM_MC_SECOND'] . '" />';
	}
	else
	{
		$rank_img = '<img src="' . $phpbb_root_path . 'images/dm_music_charts/3rd.gif" alt="" title="' . $user->lang['DM_MC_THIRD'] . '" />';
	}
	
	/**
	* Call the template
	*/
	$template->assign_block_vars('winners', array(
		'RANK' 		=> $rank_img,
		'USER' 		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'SONG' 		=> $row['chart_song_name'],
		'ARTIST'	=> $row['chart_artist'],
	));

	$template->set_filenames(array(
	   'body'      => 'portal/block/dm_music_charts_winner.html'
	));
}

?>