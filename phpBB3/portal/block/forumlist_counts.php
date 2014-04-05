<?php
/*
*
* @name forumlist_counts.php
* @package phpBB3 Portal XL 5.0
* @version $Id: forumlist_counts.php,v 1.0 2011/01/13 portalxl group Exp $
*
* @copyright (c) 2007, 2009 Portal XL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

/*
* Start session management
*/

/*
* Begin block script here
*/
$sql = 'SELECT *
	FROM ' . FORUMS_TABLE . '
	WHERE forum_id = forum_id
	AND forum_posts != 0
	ORDER BY forum_id, forum_name ASC';
$result = $db->sql_query($sql);	

while ($row = $db->sql_fetchrow($result))
{
  if ($auth->acl_gets('f_list', 'f_read', $row['forum_id']) || ($row['forum_type'] == FORUM_LINK && $row['forum_link'] && !$auth->acl_get('f_read', $row['forum_id'])))
  {
	  $template->assign_block_vars('forumcount', array(
		  'FORUM_ID'			=> $row['forum_id'],
		  'FORUM_NAME'			=> $row['forum_name'],
		  'FORUM_DESC'			=> generate_text_for_display($row['forum_desc'], $row['forum_desc_uid'], $row['forum_desc_bitfield'], $row['forum_desc_options']),
		  'FORUM_TOPICS'		=> $row['forum_topics'],
		  'FORUM_POSTS'			=> $row['forum_posts'],
  
		  'FORUM_CAT_IMAGE'		=> '<img src="' . $phpbb_root_path . 'portal/images/icon_block/folder_link.png" alt="' . $user->lang['FORUM_CAT'] . '" />',
		  'FORUM_IMAGE'			=> '<img src="' . $phpbb_root_path . 'portal/images/icon_block/folder_page.png" alt="' . $user->lang['FORUM_CAT'] . '" />',
		  'U_VIEWFORUM'			=> ($row['forum_type'] != FORUM_LINK || ($row['forum_flags'] & FORUM_FLAG_LINK_TRACK)) ? append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $row['forum_id']) : $row['forum_link'],
		  ));
  }
}
$db->sql_freeresult($result);

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/forumlist_counts.html',
	));

?>