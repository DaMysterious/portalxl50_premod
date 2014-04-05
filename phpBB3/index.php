<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
// Users and Bots on Seperate Lines 1.0.0
define('IN_FORUM_INDEX', true);
// Users and Bots on Seperate Lines 1.0.0
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_calendar.' . $phpEx);

// phpBB Arcade Start
$scoretype = (strtolower(request_var('act', '')) == 'arcade' && strtolower(request_var('do', '')) == 'newscore') ? 'IBPRO_GAME' : ((strtolower(request_var('autocom', '')) == 'arcade') ? 'IBPROV3_GAME' : false);
if ($scoretype)
{
	include($phpbb_root_path . 'arcade/includes/scoretype.' . $phpEx);
}
// phpBB Arcade End

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> Zero dupe
if (!empty($phpbb_seo->seo_opt['url_rewrite'])) {
	$phpbb_seo->seo_path['canonical'] = $phpbb_seo->drop_sid(append_sid("{$phpbb_root_path}index.$phpEx"));
}
$seo_mark = request_var('mark', '');
$keep_mark = in_array($seo_mark, array('topics', 'topic', 'forums', 'all')) ? (boolean) ($user->data['is_registered'] || $config['load_anon_lastread']) : false;
$phpbb_seo->seo_opt['zero_dupe']['redir_def'] = array(
	'hash' => array('val' => request_var('hash', ''), 'keep' => $keep_mark),
	'mark' => array('val' => $seo_mark, 'keep' => $keep_mark),
);
if ( !$phpbb_seo->seo_opt['zero_dupe']['strict'] ) { // strict mode is here a bit faster
	if ( !empty($phpbb_seo->seo_static['index']) ) {
		$phpbb_seo->set_cond( (boolean) (utf8_strpos($phpbb_seo->seo_path['uri'], $phpbb_seo->seo_static['index']) === false), 'do_redir', (empty($_GET) || (!empty($seo_mark) && !$keep_mark)));
	} else {
		$phpbb_seo->set_cond( (boolean) (utf8_strpos($phpbb_seo->seo_path['uri'], "index.$phpEx") !== false), 'do_redir', (empty($_GET) || (!empty($seo_mark) && !$keep_mark)));
	}
}
$phpbb_seo->seo_chk_dupe();
// www.phpBB-SEO.com SEO TOOLKIT END -> Zero dupe

display_forums('', $config['load_moderators']);

// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
$total_posts	= $config['num_posts'];
$total_topics	= $config['num_topics'];
$total_users	= $config['num_users'];

$l_total_user_s = ($total_users == 0) ? 'TOTAL_USERS_ZERO' : 'TOTAL_USERS_OTHER';
$l_total_post_s = ($total_posts == 0) ? 'TOTAL_POSTS_ZERO' : 'TOTAL_POSTS_OTHER';
$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';

$boarddays = ceil((time() - $config['board_startdate']) / 86400);
$posts_per_day = $total_posts / $boarddays;
$l_posts_per_day_s = ($posts_per_day == 0) ? 'POSTS_PER_DAY_ZERO' : 'POSTS_PER_DAY_OTHER';

// Grab group details for legend display
if ($auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
{
	$sql = 'SELECT group_id, group_name, group_colour, group_type
		FROM ' . GROUPS_TABLE . '
		WHERE group_legend = 1
		ORDER BY group_name ASC';
}
else
{
	$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type
		FROM ' . GROUPS_TABLE . ' g
		LEFT JOIN ' . USER_GROUP_TABLE . ' ug
			ON (
				g.group_id = ug.group_id
				AND ug.user_id = ' . $user->data['user_id'] . '
				AND ug.user_pending = 0
			)
		WHERE g.group_legend = 1
			AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $user->data['user_id'] . ')
		ORDER BY g.group_name ASC';
}
$result = $db->sql_query($sql);

$legend = array();
while ($row = $db->sql_fetchrow($result))
{
	$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
	$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];

	if ($row['group_name'] == 'BOTS' || ($user->data['user_id'] != ANONYMOUS && !$auth->acl_get('u_viewprofile')))
	{
		$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
	}
	else
	{

		// www.phpBB-SEO.com SEO TOOLKIT BEGIN
		$phpbb_seo->prepare_url('group', $row['group_name'], $row['group_id']);
		// www.phpBB-SEO.com SEO TOOLKIT END

		$legend[] = '<a' . $colour_text . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
	}
}
$db->sql_freeresult($result);

$legend = implode(', ', $legend);

// Generate birthday list if required ...
$birthday_list = '';
if ($config['load_birthdays'] && $config['allow_birthdays'] && $auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
{
	$now = phpbb_gmgetdate(time() + $user->timezone + $user->dst);

	// Display birthdays of 29th february on 28th february in non-leap-years
	$leap_year_birthdays = '';
	if ($now['mday'] == 28 && $now['mon'] == 2 && !$user->format_date(time(), 'L'))
	{
		$leap_year_birthdays = " OR u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
	}

	$sql = 'SELECT u.user_id, u.username, u.user_colour, u.user_birthday
		FROM ' . USERS_TABLE . ' u
		LEFT JOIN ' . BANLIST_TABLE . " b ON (u.user_id = b.ban_userid)
		WHERE (b.ban_id IS NULL
			OR b.ban_exclude = 1)
			AND (u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
			AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$birthday_list .= (($birthday_list != '') ? ', ' : '') . get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);

		if ($age = (int) substr($row['user_birthday'], -4))
		{
			$birthday_list .= ' (' . max(0, $now['year'] - $age) . ')';
		}
		
		// Start Breizh Shoutbox
		$bd_shout[] = array(
			'userid'	=> $row['user_id'],
			'name'		=> $row['username'],
			'colour'	=> $row['user_colour'],
			'age'		=> ($now['year'] - $age),
			'flag'		=> (isset($row['user_country_flag'])) ? $row['user_country_flag'] : false,
		);
		// End Breizh Shoutbox
		
	}
	$db->sql_freeresult($result);
}

// Show amount of reported and queue posts for authenticated users
if ($auth->acl_getf_global('m_report') || $auth->acl_getf_global('m_approve'))
{
	if (!function_exists('get_forum_list'))
	{
		include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
	}
	$user->add_lang('mcp');
	
	// Reported posts
	$forum_list = get_forum_list('m_report');
	if (!empty($forum_list))
	{
		$sql = 'SELECT COUNT(r.report_id) AS total
			FROM ' . REPORTS_TABLE . ' r, ' . POSTS_TABLE . ' p
			WHERE r.post_id = p.post_id
				AND r.report_closed = 0
				AND ' . $db->sql_in_set('forum_id', $forum_list);
		$result = $db->sql_query($sql);
		$total = (int) $db->sql_fetchfield('total');
		$db->sql_freeresult($result);

		if ($total)
		{			
			$template->assign_vars(array(
				'L_REPORTS_TOTAL'	=> ($total == 1) ? $user->lang['REPORT_TOTAL'] : sprintf($user->lang['REPORTS_TOTAL'], $total),
				'U_MCP_REPORTS'		=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports'),
				'S_HAS_REPORTS'		=> true)
			);
		}
	}
	
	// Posts in queue
	$forum_list = get_forum_list('m_approve');
	if (!empty($forum_list))
	{
		$sql = 'SELECT COUNT(post_id) AS total
			FROM ' . POSTS_TABLE . '
			WHERE ' . $db->sql_in_set('forum_id', $forum_list) . '
				AND post_approved = 0';
		$result = $db->sql_query($sql);
		$total = (int) $db->sql_fetchfield('total');
		$db->sql_freeresult($result);

		if ($total)
		{
			$template->assign_vars(array(
				'L_UNAPPROVED_TOTAL'		=> ($total == 1) ? $user->lang['UNAPPROVED_POST_TOTAL'] : sprintf($user->lang['UNAPPROVED_POSTS_TOTAL'], $total),
				'U_MCP_QUEUE'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue'),
				'S_HAS_UNAPPROVED_POSTS'	=> true)
			);
		}
	}	
}

// announcement centre
if ( $config['announcement_show_index'] && $config['announcement_enable'] )
{
	if (!function_exists('get_announcement_data'))
	{
		include($phpbb_root_path . 'includes/functions_announcements.' . $phpEx);
	}
	get_announcement_data();
}

// User Reminder Version 1.0.5
// if automatic reminders is set, remind people. lets only run this once a day.
if (isset($config['user_reminder_enable']))
{
	if ( $config['user_reminder_enable'] == ENABLED )
	{
		$check_time = (int) gmdate('mdY',time() + (3600 * ($config['board_timezone'] + $config['board_dst'])));
	
		if ( $config['user_reminder_last_auto_run'] != $check_time)
		{
			if (!function_exists('send_user_reminders'))
			{
				include($phpbb_root_path . 'includes/functions_user_reminder.' . $phpEx);
			}
			
			send_user_reminders();
			
			if ($config['user_reminder_log_opt_users_react'])
			{
				add_log('admin', 'LOG_USER_REMINDER_AUTO_RUN');
			}
			
			set_config('user_reminder_last_auto_run', $check_time);
		}
	}
}
// User Reminder Version 1.0.5

// Start DM Music Charts
if (!function_exists('check_charts_voted'))
{
	include($phpbb_root_path . 'includes/functions_dm_music_charts.' . $phpEx);
}

if ($auth->acl_gets('u_dm_mc_view') && $user->data['user_id'] != ANONYMOUS)
{
	$user->add_lang('mods/dm_music_charts');
	check_charts_voted();
}
// End DM Music Charts

// phpBB Gallery integration
if (class_exists('phpbb_gallery_integration'))
{
	phpbb_gallery_integration::index_total_images();
}
// phpBB Gallery integration

// Assign index specific vars
$template->assign_vars(array(
	'TOTAL_POSTS'	=> sprintf($user->lang[$l_total_post_s], $total_posts),
	'TOTAL_TOPICS'	=> sprintf($user->lang[$l_total_topic_s], $total_topics),
	'TOTAL_USERS'	=> sprintf($user->lang[$l_total_user_s], $total_users),
	'POSTS_PER_DAY'	=> sprintf($user->lang[$l_posts_per_day_s], $posts_per_day),
	'NEWEST_USER'	=> sprintf($user->lang['NEWEST_USER'], get_username_string('full', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])),
        'USER_AVATAR'   => get_user_avatar($user->data['user_avatar'], $user->data['user_avatar_type'], $user->data['user_avatar_width'], $user->data['user_avatar_height']),
	
	'LEGEND'		=> $legend,
	'BIRTHDAY_LIST'	=> $birthday_list,

	'FORUM_IMG'				=> $user->img('forum_read', 'NO_UNREAD_POSTS'),
	'FORUM_UNREAD_IMG'			=> $user->img('forum_unread', 'UNREAD_POSTS'),
	'FORUM_LOCKED_IMG'		=> $user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
	'FORUM_UNREAD_LOCKED_IMG'	=> $user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

	'S_LOGIN_ACTION'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
	'S_DISPLAY_BIRTHDAY_LIST'	=> ($config['load_birthdays']) ? true : false,
	
// MOD : Index Tabbed - Start
	'S_INDEXTABS'				=> (isset($config['load_indextabs'])) ? $config['load_indextabs'] : false,
// MOD : Index Tabbed - END

	'U_MARK_FORUMS'		=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? append_sid("{$phpbb_root_path}index.$phpEx", 'hash=' . generate_link_hash('global') . '&amp;mark=forums') : '',
	'U_MCP'				=> ($auth->acl_get('m_') || $auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=main&amp;mode=front', true, $user->session_id) : '')
);

// Start DM Video
if ( isset($config['dm_video_version']) )
{
	$user->setup('mods/dm_video');

	$sql = 'SELECT COUNT(video_id) AS number_videos
	    FROM ' . DM_VIDEO_TABLE . '
	    WHERE video_approval = 1';
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	$sql2 = 'SELECT *
		FROM ' . DM_VIDEO_TABLE . '
		WHERE video_approval = 1
		ORDER BY video_counter DESC LIMIT 5';
	$result2 = $db->sql_query($sql2);

	while ( $row2 = $db->sql_fetchrow($result2) )
	{	
		$template->assign_block_vars('videoline', array(
			'VIDEO_COUNTER' => sprintf($user->lang['DMV_VIDEO_COUNTER'], $row2['video_counter']),
			'TITEL'			=> $row2['video_title'],
		));  
	}
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'S_VIDEO_EXIST'	=> true,
		'NUMBER_VIDEOS'	=> sprintf($user->lang['DMV_TOTAL_VIDEOS'], $row['number_videos']),
	));
}
// End DM Video

// Start Breizh Shoutbox
if (isset($config['shout_version']))
{
	if ($config['shout_enable'] && $config['shout_index'])
	{
		shout_display();
	}
}

$bd_shout = array();

$bd_shout[] = array(
	'userid'	=> $row['user_id'],
	'name'		=> $row['username'],
	'colour'	=> $row['user_colour'],
	'age'		=> ($now['year'] - $age),
	'flag'		=> (isset($row['user_country_flag'])) ? $row['user_country_flag'] : false,
);

if (isset($config['shout_version']))
{
	if (sizeof($bd_shout) && date('H') == $config['shout_birthday_hour'] && $config['shout_enable'])
	{
		foreach ($bd_shout as $pos => $shout)
		{
			birthday_robot_shout($shout['userid'], $shout['name'], $shout['colour'], $shout['age'], $shout['flag']);
		}
	}
}
// End Breizh Shoutbox

// Output page

// PayPal IPN Donation MOD
require_once($phpbb_root_path . 'includes/donate_functions_V3.'.$phpEx);
$template->assign_vars(array(
	'LAST_DONORS' 	=> last_donors(), 
	'L_LAST_DONORS' => generate_donation_title()
));
// PayPal IPN Donation MOD

// www.phpBB-SEO.com SEO TOOLKIT BEGIN - META
$seo_meta->collect('description', $config['sitename'] . ' : ' .  $config['site_desc']);
$seo_meta->collect('keywords', $config['sitename'] . ' ' . $seo_meta->meta['description']);
// www.phpBB-SEO.com SEO TOOLKIT END - META
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - TITLE
page_header($config['sitename']);
// www.phpBB-SEO.com SEO TOOLKIT END - TITLE

/*
* include portal block management if portal index/viewtopic is active
*/
if (defined('PORTAL_INDEX_PAGE')) {
	$user->setup('mods/portal_xl');
	include($phpbb_root_path . 'portal/includes/functions.'.$phpEx);
	include($phpbb_root_path . 'portal/includes/functions_acronym.' . $phpEx);
	include($phpbb_root_path . 'portal/includes/index_blocks.' . $phpEx);
	include($phpbb_root_path . 'portal/includes/functions_blocks_index.' . $phpEx);
}

/*
* switch template if the portal index/viewtopic is active
*/
	if (defined('PORTAL_INDEX_PAGE')) {
	$template->set_filenames(array(
		'body' => 'portal/portal_index_body.html',
		));
	}
	else {
	$template->set_filenames(array(
		'body' => 'index_body.html',
		));
	}

page_footer();

?>