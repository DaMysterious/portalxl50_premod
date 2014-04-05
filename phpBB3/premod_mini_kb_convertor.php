<?php
/*
* CONVERTOR BETWEEN Knowledge Base v0.2.12 by tas2580 to Knowledge Base Mod v1.0.0 by Poppertom69 & Imladris *

* TABLES WE USE FROM TAS2580 *
	kb_article
	kb_rating
 	kb_categorie
 	kb_types 
	
* TABLES WE DO NOT USE FROM TAS2580 REASON IN () *
	kb_changelog (we do not have a table like this ours is all done in edit table)
	kb_reports (we do not have a repots table)
	kb_article_track (we do not track unread articles)
	kb_article_diff (even though we have a diff table ours is so different it would stop it from working)
	
* 7 STEPS IN THIS CONVERTOR *
	1 - make sure both mods are installed or else this won't work and our mod is a fresh install
	2 - convert old article table to ours
	3 - convert old cat table to ours
	4 - convert old rate table to ours
	5 - convert old type table to ours
	6 - convert old attachments to ours
	7 - resync numbers
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
include($phpbb_root_path . 'includes/functions_kb.' . $phpEx);
include($phpbb_root_path . 'includes/constants_kb.' . $phpEx);
$db_tools = new phpbb_db_tools($db);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

// Get and set some variables
$step = request_var('step', 1);
$submit = request_var('submit', 0);
$first_time = request_var('first_time', 0);
$error = array();
$sql_array = array();
$sql_inside_array = array();

//Set some Lang vars
$user->lang['CONVERT'] 				= 'Convert to our Knowledge Base Mod';
$user->lang['CONVERT_CONFIRM'] 		= 'Are you sure you want to convert to our Knowledge Base Mod? Please notice that if your Knowledge Base Mod is not a fresh install, the converter will delete ALL data, so remember to backup.';
$user->lang['CONTINUE_TO'] 			= 'Continue to step ' . ($step + 1);
$user->lang['CONTINUE_TO_CONFIRM'] 	= 'Are you sure you want to continue to step ' . ($step + 1) . '?';

if (!$submit)
{
	if (confirm_box(true))
	{
		redirect("premod_mini_kb_convertor.$phpEx?step=1&amp;submit=1");
	}
	else
	{
		//display mode
		confirm_box(false, 'CONVERT', '', 'confirm_body.html', "premod_mini_kb_convertor.$phpEx");
	}
}
else
{
	if (confirm_box(true))
	{
		redirect("premod_mini_kb_convertor.$phpEx?step=$step&amp;submit=1");
	}

	switch ($step)
	{
		case 1:			
			// First check tas2580 mod
			$tas_installed = $db_tools->sql_table_exists($table_prefix . 'kb_article');
			
			// Second check ours
			$kb_installed = $db_tools->sql_table_exists($table_prefix . 'articles');
			
			if (!$tas_installed)
			{
				$error[] = "tas2580's Knowledge Base Mod isn't installed";
			}
			
			if (!$kb_installed && file_exists($phpbb_root_path . 'kb.' . $phpEx))
			{
				$error[] = "Our Knowledge Base Mod isn't installed, please navigate to kb." . $phpEx . " to install then come back here";
			}
			else if (!file_exists($phpbb_root_path . 'kb.' . $phpEx))
			{
				$error[] = "Please download our Knowledge Base Mod and upload the files to your server and install.";
			}
			
			if (sizeof($error))
			{
				trigger_error(implode('<br />', $error));
			}			
		break;
		
		case 2:
		break;
		
		case 3:
			// First get all the data from old table
			$article_table_change = array(
				'article_id'		=> 'article_id',
				'cat_id'			=> 'cat_id',
				'type_id'			=> 'article_type',
				'hits'				=> 'article_views',
				'titel'				=> 'article_title',
				'description'		=> 'article_desc',
				'article'			=> 'article_text',
				'user_id'			=> 'article_user_id',
				'activ'				=> 'article_status', // Check this
				'bbcode_uid'		=> 'bbcode_uid',
				'bbcode_bitfield'	=> 'bbcode_bitfield',
				'enable_magic_url'	=> 'enable_magic_url',
				'enable_bbcode'		=> 'enable_bbcode',
				'enable_smilies'	=> 'enable_smilies',
				'post_time'			=> 'article_time',
				'last_change'		=> 'article_last_edit_time',
				'last_edit_user'	=> 'article_last_edit_id',
				'has_attachment'	=> 'article_attachment',
			);		

			$sql = 'SELECT user_id, username, user_colour
					FROM ' . USERS_TABLE;
			$result = $db->sql_query($sql, 3600);
			$rows = $db->sql_fetchrowset($result);
			$user_array = array();
			foreach ($rows as $row)
			{
				$user_array[$row['user_id']] = array(
					'user_id'		=> $row['user_id'],
					'username'		=> $row['username'],
					'user_colour'	=> $row['user_colour'],
				);
			}
			
			$sql = 'SELECT ' . implode(', ', array_keys($article_table_change)) . '
					FROM ' . $table_prefix . 'kb_article';
			$result = $db->sql_query($sql);
			$rows = $db->sql_fetchrowset($result);
			
			foreach ($rows as $row)
			{
				foreach ($row as $key => $value)
				{
					$sql_inside_array[$article_table_change[$key]] = $value;
				}			
			
				$array_merge = array(
					'article_edit_reason'	=> '',
					'article_tags'			=> '',
					'article_checksum'		=> md5($sql_inside_array['article_text']),
					'article_desc_bitfield'	=> '',
					'article_desc_uid'		=> '',
					'article_title_clean'	=> utf8_clean_string($sql_inside_array['article_title']),
					'article_user_name'		=> (isset($user_array[$sql_inside_array['article_user_id']]['username'])) ? $user_array[$sql_inside_array['article_user_id']]['username'] : '',
					'article_user_color'	=> (isset($user_array[$sql_inside_array['article_user_id']]['user_colour'])) ? $user_array[$sql_inside_array['article_user_id']]['user_colour'] : '',
				);
				
				$sql_array[] = array_merge($sql_inside_array, $array_merge);			
				unset($sql_inside_array);
			}
			
			$db->sql_multi_insert($table_prefix . 'articles', $sql_array);
		break;
		
		case 4:
			$cat_table_change = array(
				'cat_id'			=> 'cat_id',
				'right_id'			=> 'right_id',
				'left_id'			=> 'left_id',
				'parent_id'			=> 'parent_id',
				'cat_title'			=> 'cat_name',
				'description'		=> 'cat_desc',
				'bbcode_uid'		=> 'cat_desc_uid',
				'bbcode_bitfield'	=> 'cat_desc_bitfield',
				'bbcode_options'	=> 'cat_desc_options',
				'image'				=> 'cat_image',
			);
			
			$sql = 'SELECT ' . implode(', ', array_keys($cat_table_change)) . '
					FROM ' . $table_prefix . 'kb_categorie';
			$result = $db->sql_query($sql);
			$rows = $db->sql_fetchrowset($result);
			
			foreach ($rows as $row)
			{
				foreach ($row as $key => $value)
				{
					$sql_inside_array[$cat_table_change[$key]] = $value;
				}			
			
				$array_merge = array(
					'latest_ids'	=> serialize(array()),
				);
				
				$sql_array[] = array_merge($sql_inside_array, $array_merge);			
				unset($sql_inside_array);
			}
			
			$db->sql_multi_insert(KB_CATS_TABLE, $sql_array);
		break;
		
		case 5:
		break;
		
		case 6:
			$type_table_change = array(
				'type_id'			=> 'type_id',
				'name'				=> 'type_title',
			);
			
			$sql = 'SELECT ' . implode(', ', array_keys($type_table_change)) . '
					FROM ' . $table_prefix . 'kb_types
					ORDER BY name DESC';
			$result = $db->sql_query($sql);
			$rows = $db->sql_fetchrowset($result);
			$num = 0;
			
			foreach ($rows as $row)
			{
				foreach ($row as $key => $value)
				{
					$sql_inside_array[$type_table_change[$key]] = $value;
				}			
				$num++;
			
				$array_merge = array(
					'type_before'	=> '[' . $sql_inside_array['type_title'] . ']',
					'type_after'	=> '',
					'type_image'	=> '',
					'type_order'	=> $num,
				);
				
				$sql_array[] = array_merge($sql_inside_array, $array_merge);			
				unset($sql_inside_array);
			}
			
			$db->sql_multi_insert($table_prefix . 'article_types', $sql_array);
		break;
		
		case 7:
			$attachment_table_change = array(
				'attach_id'				=> 'attach_id',
				'post_msg_id'			=> 'article_id',
				'poster_id'				=> 'poster_id',
				'is_orphan'				=> 'is_orphan',
				'physical_filename'		=> 'physical_filename',
				'real_filename'			=> 'real_filename',
				'download_count'		=> 'download_count',
				'attach_comment'		=> 'attach_comment',
				'extension'				=> 'extension',
				'mimetype'				=> 'mimetype',
				'filesize'				=> 'filesize',
				'filetime'				=> 'filetime',
				'thumbnail'				=> 'thumbnail',
			);
			
			$sql = 'SELECT ' . implode(', ', array_keys($attachment_table_change)) . '
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE in_message = 2';
			$result = $db->sql_query($sql);
			$rows = $db->sql_fetchrowset($result);
			$attach_ids = array();
			
			foreach ($rows as $row)
			{
				$attach_ids[] = $row['attach_id'];
				
				foreach ($row as $key => $value)
				{
					$sql_inside_array[$attachment_table_change[$key]] = $value;
				}	
				
				$sql_array[] = $sql_inside_array;			
				unset($sql_inside_array);
			}
			
			$db->sql_multi_insert($table_prefix . 'article_attachments', $sql_array);
			
			if (sizeof($attach_ids))
			{
				$sql = "DELETE 
						FROM " . ATTACHMENTS_TABLE . '
						WHERE ' . $db->sql_in_set('attach_id', $attach_ids);
				$db->sql_query($sql);
			}
		break;
		
		case 8:
			// Resync cat count
			// Fix article count
			$articles_by_cats = $articles_by_users = array();
			$total_articles = 0;
			$sql = 'SELECT cat_id, article_user_id
					FROM ' . KB_TABLE . '
					WHERE article_status = ' . STATUS_APPROVED . '
					ORDER BY article_user_id';
			$result = $db->sql_query($sql);
			
			while($row = $db->sql_fetchrow($result))
			{
				if(isset($articles_by_cats[$row['cat_id']]))
				{
					$articles_by_cats[$row['cat_id']]++;
				}
				else
				{
					$articles_by_cats[$row['cat_id']] = 1;
				}
				
				if(isset($articles_by_users[$row['article_user_id']]))
				{
					$articles_by_users[$row['article_user_id']]++;
				}
				else
				{
					$articles_by_users[$row['article_user_id']] = 1;
				}
				
				$total_articles++;
			}
			$db->sql_freeresult($result);
			$articles_by_users = array_unique($articles_by_users);
			
			foreach($articles_by_users as $user_id => $article_count)
			{
				$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_articles = ' . $article_count . '
						WHERE user_id = ' . $user_id;
				$db->sql_query($sql);
			}
			
			foreach($articles_by_cats as $cat_id => $article_count)
			{
				$sql = 'UPDATE ' . KB_CATS_TABLE . '
						SET cat_articles = ' . $article_count . '
						WHERE cat_id = ' . $cat_id;
				$db->sql_query($sql);
			}
			
			set_config('kb_total_articles', $total_articles, true);
			
			$sql = 'SELECT cat_id
					FROM ' . $table_prefix . 'article_cats';
			$result = $db->sql_query($sql);
			
			$cat_ids = array();
			while($row = $db->sql_fetchrow($result))
			{
				$cat_ids[] = $row['cat_id'];
			}
			
			foreach($cat_ids as $cat_id)
			{
				$sql = 'SELECT article_id, article_title
						FROM ' . KB_TABLE . '
						WHERE cat_id = ' . $cat_id . ' 
						ORDER BY article_last_edit_time DESC';
				$result = $db->sql_query_limit($sql, 5);
				
				$latest_articles = array();
				while($row = $db->sql_fetchrow($result))
				{
					$latest_articles[] = array(
						'article_id'	=> $row['article_id'],
						'article_title'	=> $row['article_title'],
					);
				}
				$db->sql_freeresult($result);
				
				$sql_ary = array(
					'latest_ids' 	=> serialize($latest_articles),
				);
				$sql = 'UPDATE ' . KB_CATS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
						WHERE cat_id = '" . $db->sql_escape($cat_id) . "'";
				$db->sql_query($sql);
			}
			
			// Resync article count
			$sql = 'SELECT article_id
					FROM ' . $table_prefix . 'articles';
			$result = $db->sql_query($sql);
			
			$article_ids = array();
			while($row = $db->sql_fetchrow($result))
			{
				$article_ids[] = $row['article_id'];
			}
			
			set_config('kb_total_cats', sizeof($cat_ids));
			set_config('kb_total_articles', sizeof($article_ids));
			set_config('kb_last_updated', time());
			
			$sql = 'SELECT COUNT(attach_id) as stat
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE is_orphan = 0';
			$result = $db->sql_query($sql);
			set_config('num_files', (int) $db->sql_fetchfield('stat'), true);
			$db->sql_freeresult($result);

			$sql = 'SELECT SUM(filesize) as stat
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE is_orphan = 0';
			$result = $db->sql_query($sql);
			set_config('upload_dir_size', (float) $db->sql_fetchfield('stat'), true);
			$db->sql_freeresult($result);
		break;
	}

	if ($step >= 8)
	{
		trigger_error('The conversion is complete, go to the acp and sort permissions and sort everything out');
	}
	else
	{	
		//display mode
		confirm_box(false, 'CONTINUE_TO', '', 'confirm_body.html', "premod_mini_kb_convertor.{$phpEx}?step=" . ($step + 1) . "&amp;submit=1");
	}
}


?>
	