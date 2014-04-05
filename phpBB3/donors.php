<?php
/*
*
* @name donors.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donors.php,v 1.1.0 2012/05/28 portalxl group Exp $
*
* @copyright (c) Zou Xiong - Enterprise admin@loewen.com.sg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
$user->add_lang('mods/lang_donations');
// End session management

if( !$user->data['is_registered'] )
{
   redirect(append_sid("{$phpbb_root_path}ucp.$phpEx", "mode=login&amp;redirect=donors." . $phpEx));
}

//get necessary page header inforamtion first.
page_header($config['sitename'] . ' : ' . $user->lang['MORE_DONORS']);

$start = request_var('start', '') + 0;

// Grab rank information for later
$ranks = $cache->obtain_ranks();

// Generate a 'Show topics in previous x days' select box. If the topicsdays var is sent
// then get it's value, find the number of topics with dates newer than it (to properly
// handle pagination) and alter the main query
$previous_days = array(7, 14, 30, 90, 180, 364, 400);
$previous_days_text = array($user->lang['7_DAYS'], $user->lang['2_WEEKS'], $user->lang['1_MONTH'], $user->lang['3_MONTHS'], $user->lang['6_MONTHS'], $user->lang['1_YEAR'], $user->lang['ALL_RECORDS']);

$topic_days = 7;
$temp_topicdays = request_var('topicdays', '');
if ( strlen($temp_topicdays) > 0 )
{
	$topic_days = intval($temp_topicdays);

	if($topic_days > 0 && $topic_days < 400)
	{
		$min_topic_time = time() - ($topic_days * 86400);

		$limit_topics_time = " AND date >= $min_topic_time";
	}
	else
	{
		$topic_days = 400;
		$limit_topics_time = '';
	}
}
else
{
	$topic_days = 30;

	$limit_topics_time = '';

	$min_topic_time = time() - ($topic_days * 86400);

	$limit_topics_time = " AND date >= $min_topic_time";
}

$donormode = request_var('mode', 'viewall');
$donorswhere = '';

if(strcmp($donormode, 'viewcurrent') == 0)
{	
  //format can only be 2004/08/04 yyyy/mm/dd
  $starttime = 0;
  $endtime = 0;
  if(strlen($config['donate_start_time']) == 10)
  {
	  $starttime = mktime(0, 0, 0, substr($config['donate_start_time'], 5, 2), substr($config['donate_start_time'], 8, 2), substr($config['donate_start_time'], 0, 4) );
  }
  if(strlen($config['donate_end_time']) == 10)
  {
	  $endtime = mktime(0, 0, 0, substr($config['donate_end_time'], 5, 2), substr($config['donate_end_time'], 8, 2), substr($config['donate_end_time'], 0, 4) );
  }	
  if($starttime > 0)
  {
	  if($endtime <= $starttime)
	  {
		  $donorswhere = ' AND a.date >= ' . $starttime;
	  }
	  else
	  {
		  $donorswhere = ' AND a.date >= ' . $starttime . ' AND a.date <= ' . $endtime;
	  }
  }
}

$select_topic_days = '<select name="topicdays">';
for($i = 0; $i < count($previous_days); $i++)
{
	$selected = ($topic_days == $previous_days[$i]) ? ' selected="selected"' : '';
	$select_topic_days .= '<option value="' . $previous_days[$i] . '"' . $selected . '>' . $previous_days_text[$i] . '</option>';
}
$select_topic_days .= '</select>';

$user_id = intval(request_var('userid', '0'));
if($user_id <= 0)
{
	$user_id = ($user->data['user_id']);
}
if($user_id != $user->data['user_id'] 
        && (!isset($user->data['session_admin']) || !$user->data['session_admin']) )
{
	$message = $user->lang['NO_PRIVILEGE'] . '<br /><br />' .  sprintf($user->lang['CLICK_RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>', $user->lang['FORUM_INDEX']);
	trigger_error($message);
	exit;
}

$template->assign_vars(array(
	  'U_INDEX'					=> append_sid("{$phpbb_root_path}index.".$phpEx),
	  'L_INDEX'					=> $user->lang['FORUM_INDEX'],
	  'L_DONORS_NAME' 			=> $user->lang['L_DONORS_NAME'],
	  'L_MONEY' 				=> $user->lang['L_MONEY'],
	  'L_COMMENT' 				=> $user->lang['ACCT_COMMENT'],
	  'L_DATE' 					=> $user->lang['L_DATE'],
	  'L_DATE_START' 			=> $user->lang['L_DATE_START'],
	  'L_DATE_END' 				=> $user->lang['L_DATE_END'],
	  'L_GO' 					=> $user->lang['L_GO'],
	  'U_INDEX' 				=> append_sid("{$phpbb_root_path}index.".$phpEx),
	  'L_DISPLAY_TOPICS' 		=> $user->lang['DONORS_DISPLAY_FROM'],
	  'S_SELECT_TOPIC_DAYS' 	=> $select_topic_days,
	  'S_RECORDS_DAYS_ACTION' 	=> 'donors.' . $phpEx,
	  'HIDDEN_FIELDS' 			=> '<input type="hidden" name="mode" value="' . $donormode . '">',
	));

$topics_count = 0;
$sql = "SELECT COUNT(*) 
		FROM " . ACCT_HIST_TABLE . " a, " . USERS_TABLE . " u" . " 
		WHERE a.comment LIKE '%' 
		AND status = 'Completed' 
		AND u.user_id = a.user_id" . "$limit_topics_time $donorswhere";
$result = $db->sql_query($sql);
if($row = $db->sql_fetchrow($result))
{
	$topics_count = $row["COUNT(*)"];
}

$topicsperpage = $config['topics_per_page'];

$sql = "SELECT a.*, u.* 
		FROM " . ACCT_HIST_TABLE . " a, " . USERS_TABLE . " u" . " 
		WHERE a.comment LIKE '%' 
		AND status = 'Completed' 
		AND u.user_id = a.user_id" . "$limit_topics_time $donorswhere 
		ORDER BY date DESC 
		LIMIT $start, $topicsperpage";

$result = $db->sql_query($sql);

while ( $row = $db->sql_fetchrow($result) ) {
		
	$last_donors = '';
	$style_color = '';
	$row['username'] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
	if($row['user_id'] == ANONYMOUS)
	{
		$last_donors .= $user->lang['ANONYMOUS_DONOR'];
	}
	else
	{
		$rank_title = $rank_img = '';
		get_user_rank($row['user_rank'], (($row['user_id'] == ANONYMOUS) ? false : $row['user_posts']), $rank_title, $rank_img, $rank_img_src);
		$last_donors .= '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;" . "u" . "=" . $row['user_id']) . '">' . $row['username'] . ',&nbsp;' . $rank_title . '</a>&nbsp;';
	}

	// query the donation custom profile field	
	$sql3 = 'SELECT user_id, pf_user_donation
		FROM ' . PROFILE_FIELDS_DATA_TABLE . '
			WHERE user_id = ' . $row['user_id'] . '
			 AND pf_user_donation = pf_user_donation';         
	$result3 = $db->sql_query($sql3);
	$row3 = $db->sql_fetchrow($result3);
	$db->sql_freeresult($result3);
	// query the donation custom profile field	
	
	$row3['pf_user_donation'] = str_replace(array(' '), '0', $row3['pf_user_donation']);
  
	// Get user donation
	if ( $row3['pf_user_donation'] != NULL ) { 
		$user_donation = $row3['pf_user_donation'];
	} else {
		$user_donation = $user->lang['NULL_DONOR'];
	}
	// Get user donation

   $template->assign_block_vars('donorrow', array(
	  'DONORS_NAME' 	=> $last_donors,
	  'MONEY' 			=> $row['money'] . ' ' . $config['paypal_currency_code'],
	  'DATE' 			=> $user->format_date($row['date']),
	// query the donation custom profile field	
	  'DATE_END' 		=> $user_donation,
	// query the donation custom profile field	
	));

}

if($topics_count > 0)
{
	$template->assign_vars(array(
		'PAGINATION' 	=> generate_pagination( append_sid("{$phpbb_root_path}donors.$phpEx",  "topicdays=$topic_days&userid=$user_id&mode=$donormode"), $topics_count, $topicsperpage, $start),
		'PAGE_NUMBER' 	=> sprintf($user->lang['Page_of'], ( floor( $start / $topicsperpage ) + 1 ), ceil( $topics_count / $topicsperpage )),

		'L_GOTO_PAGE' 	=> $user->lang['Goto_page'])
	);
}

if($topics_count <= 0)
{
	// No records
	$no_topics_msg = $user->lang['NO_RECORDS'];
	$template->assign_vars(array(
		'L_NO_RECORDS' => $no_topics_msg,
		'S_NO_RECORDS' => true, )
	);
}

// Output the page
$template->set_filenames(array(
	'body' => 'donation/donors_body.html')
);

$template->assign_block_vars('navlinks', array(
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}donors.$phpEx"),
	'FORUM_NAME'	=> $user->lang['MORE_DONORS'],
));

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>