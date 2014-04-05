<?php
/** 
*
* @name center_kb_articles.php
* @package phpBB3 Portal XL Premod
* @version $Id: center_kb_articles.php,v 1.3 2010/10/13 portalxl group Exp $
*
* @copyright (c) 2007, 2010 Portal XL Group
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/
$allow_max_articles = $portal_config['portal_max_topics'];
$allow_kb_recent_limit = $portal_config['portal_recent_title_limit'];

/**
*/
if (!class_exists('knowledge_base')) {
	include($phpbb_root_path . 'includes/constants_kb.' . $phpEx);
} else {
    // do nothing
}

/*
* Start session management
*/
$user->setup('mods/kb');

/*
* Begin block script here
*/

// Articles in this categorie
$sql = 'SELECT COUNT(article_id) AS num_articles
	FROM ' . KB_TABLE . '
	WHERE cat_id = cat_id';
$result = $db->sql_query($sql);
$articles_count = (int) $db->sql_fetchfield('num_articles');
$db->sql_freeresult($result);

// Get Articles
$sql = $db->sql_build_query('SELECT', array(
	'SELECT'	=> 'a.*',
	'FROM'		=> array(
		KB_TABLE => 'a'),
	'WHERE'		=> 'a.cat_id = a.cat_id',
	'GROUP_BY'	=> 'a.article_id',
	'ORDER_BY'  => 'a.article_time DESC',
));
$result = $db->sql_query_limit($sql, $allow_max_articles);
while($row = $db->sql_fetchrow($result))
{
  // Send vars to template
  $template->assign_block_vars('articlerow', array(
	  'ARTICLE_ID'					=> $row['article_id'],
	  'ARTICLE_AUTHOR_FULL'			=> get_username_string('full', $row['article_user_id'], $row['article_user_name'], $row['article_user_color']),
	  'FIRST_POST_TIME'				=> $user->format_date($row['article_time']),

	  'COMMENTS'					=> $row['article_comments'],
	  'VIEWS'						=> $row['article_views'],
	  'TIME'						=> $user->format_date($row['article_time']),
	  'ARTICLE_TITLE'				=> censor_text($row['article_title']),
	  'ARTICLE_DESC'				=> ($config['kb_show_desc_cat'] && !$config['kb_disable_desc']) ? generate_text_for_display($row['article_desc'], $row['article_desc_uid'], $row['article_desc_bitfield'], $row['article_desc_options']) : '',
	  'ARTICLE_CONTENT'				=> ($config['kb_show_desc_cat'] && !$config['kb_disable_desc']) ? generate_text_for_display($row['article_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['enable_bbcode']) : '',
	  'ARTICLE_FOLDER_IMG'			=> $user->img('topic_read', censor_text($row['article_title'])),
	  'ARTICLE_FOLDER_IMG_SRC'		=> $user->img('topic_read', censor_text($row['article_title']), false, '', 'src'),
	  'ARTICLE_FOLDER_IMG_ALT'		=> censor_text($row['article_title']),
	  'ARTICLE_FOLDER_IMG_WIDTH'  	=> $user->img('topic_read', '', false, '', 'width'),
	  'ARTICLE_FOLDER_IMG_HEIGHT'	=> $user->img('topic_read', '', false, '', 'height'),
	  'ARTICLE_UNAPPROVED'			=> ($row['article_status'] != STATUS_APPROVED && $auth->acl_gets('m_kb_view', 'm_kb_status')) ? true : false,
	  'U_MCP_QUEUE'					=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=kb&hmode=status&a=' . $row['article_id']),

	  'ARTICLE_TYPE_IMG'			=> $row['type_image']['img'],
	  'ARTICLE_TYPE_IMG_WIDTH'		=> $row['type_image']['width'],
	  'ARTICLE_TYPE_IMG_HEIGHT'		=> $row['type_image']['height'],
	  'ATTACH_ICON_IMG'				=> ($auth->acl_get('u_kb_download', $cat_id) && $row['article_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
	  
	  'U_VIEW_ARTICLE'				=> append_sid("{$phpbb_root_path}kb.$phpEx", 'a=' . $row['article_id']),
  ));
}
$db->sql_freeresult($result);
		
// Assign index specific vars
$template->assign_vars(array(
	'L_AUTHOR'			=> $user->lang['ARTICLE_AUTHOR'],
	'L_ARTICLES_LC' 	=> utf8_strtolower($user->lang['ARTICLES']),
	'TOTAL_ARTICLES' 	=> $articles_count,			
	'NEW_ARTICLE_IMG'	=> $user->img('button_article_new', 'KB_ADD_ARTICLE'),
));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/center_kb_articles.html',
	));

?>