<?php
/**
*
* @package DM Music Charts - Current Charts on Portal XL 5.0
* @version $Id$
* @copyright (c) 2008, 2009 femu - http://die-muellers.org
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
* Some mod related variables and includes
*/
$user->add_lang('mods/dm_music_charts');

/**
* Check authorisation
*/
$is_authorised = ($auth->acl_get('u_dm_mc_view') || $auth->acl_get('a_dm_mc_manage')) ? true : false;

if ($is_authorised == true )
{
	/**
	* Set number of songs to show
	*/
	
	$number_of_newest = 5;
	
	/**
	* Select the charts
	*/
	$sql = 'SELECT c.*, u.*
		FROM ' . DM_MUSIC_CHARTS_TABLE . ' c
		LEFT JOIN ' . USERS_TABLE . ' u
			ON c.chart_poster_id = u.user_id
		ORDER BY c.chart_hot DESC, c.chart_not ASC
		LIMIT ' . $number_of_newest;
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		// Send the results to the template
		$template->assign_block_vars('current_charts', array(
			'TITLE'				=> $row['chart_song_name'],
			'ARTIST'			=> $row['chart_artist'],
			'COVER'				=> $row['chart_picture'],
			'POST_AUTHOR_FULL'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']),
			'NEWEST_POST_IMG'	=> $user->img('icon_topic_newest'),
			'READ_POST_IMG'		=> $user->img('icon_topic_latest'),
		));
	}
	$db->sql_freeresult($result);
	
	/**
	* Call the template
	*/
	$template->assign_vars(array(
		'S_CHARTS' => true,
		'U_GO_CHARTS'		=> append_sid("{$phpbb_root_path}dm_music_charts.$phpEx"),
	));

	$template->set_filenames(array(
	   'body'      => 'portal/block/dm_music_charts_current.html'
	));
}

?>