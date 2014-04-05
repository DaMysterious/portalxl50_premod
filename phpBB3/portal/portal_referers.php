<?php
/**
/*
*
* @name portal_referers.php
* @package phpBB3 Portal XL 5.0
* @version $Id: portal_referers.php,v 1.0 2011/11/13 portalxl group Exp $
*
* @copyright (c) 2007, 2010 Portal XL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* Censor title, return short title
*
* @param $title string title to censor
* @param $limit int short title character limit
*
*/
function character_limit(&$title, $limit = 0)
{
   $title = censor_text($title);
   if ($limit > 0)
   {
      return (utf8_strlen(utf8_decode($title)) > $limit + 3) ? truncate_string($title, $limit) . '...' : $title;
   }
   else
   {
	// return the result   
      return $title;
   }
}

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('mods/portal_xl'));

$start = request_var('start', 0);
$limit = $portal_config['portal_attachments_number'];

// grab the referers
$sql = "SELECT referer_id
	FROM " . PORTAL_REFERER_TABLE. " 
	ORDER BY referer_id ASC";
$result = $db->sql_query_limit($sql, $limit, $start);

$portal_referer = array();
while ($row = $db->sql_fetchrow($result))
{
	$portal_referer[] = (int) $row['referer_id'];
}
$db->sql_freeresult($result);

// count the referers
$sql = 'SELECT COUNT(referer_id) AS total_referer
    FROM ' . PORTAL_REFERER_TABLE;
$result = $db->sql_query($sql);
$total_referer = (int) $db->sql_fetchfield('total_referer');
$db->sql_freeresult($result);

// did we got some referers?
if (sizeof($portal_referer))
{
	$sql = 'SELECT * 
		FROM ' . PORTAL_REFERER_TABLE . '
		WHERE ' . $db->sql_in_set('referer_id', $portal_referer);
	$result = $db->sql_query($sql);

	$id_referer = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$id_referer[$row['referer_id']] = $row;
	}
	$db->sql_freeresult($result);

	for ($i = 0, $end = sizeof($portal_referer); $i < $end; $i++)
	{
		$referer_id = $portal_referer[$i];
		$row =& $id_referer[$referer_id];
	
		$replace = str_replace(array('_','-','.','www.'), ' ', $row['referer_host']);
		$ShortHostName = character_limit($replace, $num_characters);
	
		$template->assign_block_vars('referers', array(
			'HTTP_IP'			=> $row['referer_ip'],
			'HTTP_PROXY'		=> $row['referer_proxy'],
			//'HTTP_HOST'			=> $ShortHostName,
			'HTTP_HOST'			=> $row['referer_host'],
			'HHTP_LASTVISIT'	=> $user->format_date($row['referer_lastvisit']),
			'HHTP_HITS'			=> $row['referer_hits'],
			
			//'U_HTTP_HOST'		=> 'http://'.$row['referer_host'].'/',
			'U_HTTP_HOST'		=> 'http://en.utrace.de/?query='.$row['referer_ip'],
		));
	
		unset($id_referer[$referer_id]);
	}
}

// generate page
$template->assign_vars(array(
	'PAGINATION'	=> generate_pagination(append_sid("{$phpbb_root_path}portal/portal_referers.$phpEx"), $total_referer, $limit, $start),
	'PAGE_NUMBER'	=> on_page($total_referer, $limit, $start),
	'TOTAL_REFERERS'	=> ($total_referer == 1) ? $user->lang['REFERER_COUNT'] : sprintf($user->lang['REFERER_COUNT'], $total_referer)
	));

// Output the page
page_header($config['sitename'] . ' : ' . $user->lang['REFERER_TITLE']);

// Set up the Navlinks for the forums navbar
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'       => $user->lang['REFERER_TITLE'],
	'U_VIEW_FORUM'     => append_sid("{$phpbb_root_path}portal/portal_referers.$phpEx")
	));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/portal_referers.html',
	));

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>