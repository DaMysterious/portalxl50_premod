<?php
/** 
*
* @package Breizh Shoutbox
* @version $Id: ajax.php 140 20:07 31/12/2010 Sylver35 Exp $ 
* @copyright (c) 2010, 2011 Sylver35    http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './'; 
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include ($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
if (!function_exists('add_log'))
{
	include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
}

error_reporting(0);// Disable error reporting, can be bad for our headers ;)

// Start session management
$user->session_begin(false);
$auth->acl($user->data);
$user->setup(array('posting', 'mods/shout', 'mods/info_acp_shoutbox'));

$mode = request_var('m', '');

// To prevent auto refresh when edit a message
if ($mode == 'edit' || $mode == 'edit_priv')
{
	$last = 0;
}
else
{
	$last = request_var('last', 0);
}

$on1 = '<a href="%1$smod-breizh-shoutbox-f21.html">%2$s</a>';
$on2 = '<a href="%1$sindex.html">%2$s</a>';
// We have our own error handling!
$db->sql_return_on_error(true);

if ($mode != 'smilies_popup')
{
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT'); 
	header('Cache-Control: no-cache, must-revalidate'); 
	header('Pragma: no-cache');
	header('Content-type: text/xml; charset=UTF-8');	
	echo '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>
	<xml>';
}

switch ($mode)
{
	case 'smilies':
	
	    if (!$auth->acl_get('u_shout_smilies'))
	    {
	        shout_error('NO_SMILIE_PERM');
		}
		
		if ($user->lang['SHOUT_COPY'] !== $on1 || $user->lang['SHOUTBOX'] !== $on2)
		{
			exit;
		}

		$sql = 'SELECT *
			FROM ' . SMILIES_TABLE . ' 
			WHERE display_on_shout = 1
			GROUP BY smiley_url
			ORDER BY smiley_order DESC';
		$result = $db->sql_query($sql);
		if ($result)
		{
			$num_smilies = 0;
			$rowset = array();
			$last_url = '';
			
			while ($row = $db->sql_fetchrow($result))
			{
				if ($row['smiley_url'] !== $last_url)
				{
					echo "<smilies>\n
						<code>" . shout_xml($row['code']) . "</code>\n
						<img>" . shout_xml($phpbb_root_path . $config['smilies_path'] . '/' . $row['smiley_url']) . "</img>\n
						<width>" . shout_xml($row['smiley_width']) . "</width>\n
						<title>" . shout_xml($row['emotion']) . "</title>\n
						<alt>" . shout_xml($row['emotion']) . "</alt>\n
						</smilies>";
				}
				$last_url = $row['smiley_url'];
			}
			$db->sql_freeresult($result);
			echo '</xml>';
			exit;
		}	
		else
		{
			$db->sql_freeresult($result);
			shout_sql_error($sql, __LINE__, __FILE__);
		}
		
	break;
	
	case 'smilies_popup':

		page_header($user->lang['SMILIES']);
		$last_url = '';
		
		if ($user->lang['SHOUT_COPY'] !== $on1 || $user->lang['SHOUTBOX'] !== $on2)
		{
			exit;
		}

		$sql = 'SELECT *
			FROM ' . SMILIES_TABLE . '
			WHERE display_on_shout = 0
			GROUP BY smiley_url
			ORDER BY smiley_order';
		$result = $db->sql_query($sql);
		if ($result)
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('smiley', array(
					'SMILEY_CODE'	=> $row['code'],
					'A_SMILEY_CODE'	=> $row['code'],
					'SMILEY_WIDTH'	=> $row['smiley_width'],
					'SMILEY_HEIGHT'	=> $row['smiley_height'],
					'SMILEY_DESC'	=> $row['emotion'],
					'SMILEY_IMG'	=> $phpbb_root_path . $config['smilies_path'] . '/' . $row['smiley_url'],
				));
			}
		}
		else
		{
			shout_sql_error($sql, __LINE__, __FILE__);
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_IN_SHOUT_SMILIES'	=> true,
			'SHOUTBOX_VERSION' 		=> sprintf($user->lang['SHOUTBOX_VERSION_ACP_COPY'], $config['shout_version']),
		));

		$template->set_filenames(array(
			'body' => 'shout_template.html')
		);
		
		page_footer();

	break;

	case 'delete':
	case 'delete_priv':
	
		$id = request_var('id', 0);
		if ($mode == 'delete')
		{
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		else
		{
			$_priv = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		// If a user can delete all messages, he can delete it's messages :)
		$can_delete_mod = ($auth->acl_get('u_shout_delete')) ? true : false;
		$can_delete 	= ($can_delete_mod) ? true : $auth->acl_get('u_shout_delete_s');
		
		if (!$id)
		{
			shout_error('NO_SHOUT_ID');
		}
		elseif ($user_data['user_id'] == ANONYMOUS)
		{
			shout_error('NO_DELETE_PERM');
		}
		else
		{
			$sql = 'SELECT shout_user_id
				FROM ' .$_table. " 
				WHERE shout_id = $id";
			$result = $db->sql_query($sql);
			if ($result)
			{
				$row = $db->sql_fetchfield('shout_user_id', $result);
			}
			else
			{
				shout_sql_error($sql, __LINE__, __FILE__);
			}
			$db->sql_freeresult($result);
			
			if (!$can_delete && ($user->data['user_id'] == $row))
			{
				shout_error('NO_DELETE_PERM_S');
			}
			elseif (!$can_delete_mod && $can_delete && ($user->data['user_id'] != $row))
			{
				shout_error('NO_DELETE_PERM_T');
			}
			elseif (!$can_delete)
			{
				shout_error('NO_DELETE_PERM');
			}
			elseif ($can_delete && ($user->data['user_id'] == $row) || $can_delete_mod)
			{
				// Lets delete this post :D
				$sql = 'DELETE FROM ' .$_table. " WHERE shout_id = $id";
				$db->sql_query($sql);

				$sql = 'SELECT MAX(shout_id) AS shout_end FROM ' .$_table. " WHERE shout_id <> $id";
				$result = $db->sql_query($sql);
				$max_shout = $db->sql_fetchfield('shout_end');
				$db->sql_freeresult($result);
				
				$sql = 'UPDATE ' .$_table. " SET shout_time = shout_time + 1 WHERE shout_id = $max_shout";
				$db->sql_query($sql);
				
				set_config_count('shout_del_user' .$_priv, 1, true);
				echo '<msg></msg></xml>';
				exit;
			}
		}
	
	break;

	case 'purge':
	case 'purge_priv':
	
		if (!$auth->acl_get('u_shout_purge'))
		{
			shout_error('NO_PURGE_PERM');
		}
		if ($mode == 'purge')
		{
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		else
		{
			$_priv = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		
		$sql = 'SELECT COUNT(shout_id) as nr 
			FROM ' . $_table;
		$result = $db->sql_query($sql);
		$num = (int)$db->sql_fetchfield('nr');
		$db->sql_freeresult($result);
		
		$sql = 'DELETE FROM ' . $_table;
		$db->sql_query($sql);
		
		set_config_count('shout_del_purge' .$_priv, $num, true);
		post_robot_shout($user->data['user_id'], $user->ip, (($_priv == '_priv') ? true : false), true, false, false, false);
		
		echo '<msg></msg></xml>';
		exit;
	
	break;
	
	case 'purge_robot':
	case 'purge_robot_priv':
	
		if (!$auth->acl_get('u_shout_purge'))
		{
			shout_error('NO_PURGE_ROBOT_PERM');
		}
		if ($mode == 'purge_robot')
		{
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		else
		{
			$_priv = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		
		$sql = 'SELECT COUNT(shout_id) as nr 
			FROM ' .$_table. '
			WHERE shout_robot IN (1, ' .$config['shout_robot_choice']. ')';
		$result = $db->sql_query($sql);
		$num = (int)$db->sql_fetchfield('nr');
		$db->sql_freeresult($result);
		
		$sql = 'DELETE FROM ' .$_table. ' WHERE shout_robot IN (1, ' .$config['shout_robot_choice']. ')';
		$db->sql_query($sql);
		
		set_config_count('shout_del_purge' .$_priv, $num, true);
		post_robot_shout($user->data['user_id'], $user->ip, (($_priv == '_priv') ? true : false), true, true, false, false);
		
		echo '<msg></msg></xml>';
		exit;
	
	break;

	case 'add':
	case 'post':
	case 'edit':
	case 'add_priv':	
	case 'post_priv':
	case 'edit_priv':
	
		$shout_id 	= request_var('shout_id', 0);
		$mode 		= ($mode == 'add') ? 'post' : (($mode == 'add_priv') ? 'post_priv' : $mode);
		$mode_s 	= str_replace(array('_priv', '_pop'), '', $mode);
		$post	 	= ($auth->acl_get('u_shout_post')) ? true : false;
		// If a user can edit all messages, he can edit it's messages :)
		$can_edit_mod = ($auth->acl_get('u_shout_edit_mod')) ? true : false;
		$can_edit 	= ($can_edit_mod) ? true : $auth->acl_get('u_shout_edit');

		if ($mode == 'post' || $mode == 'edit')
		{
			$perm = '_view';
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		else
		{
			$_priv = $perm = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		// Protect by checking permissions
		if (!$auth->acl_get('u_shout' .$perm))
	    {
	        shout_error('NO_VIEW' .$_Priv. '_PERM');
		}
		elseif (!$post)
		{
			shout_error('NO_POST_PERM');
		}
		elseif ($mode_s == 'edit' && !$shout_id)
		{
			shout_error('NO_SHOUT_ID');
		}
		elseif ($mode_s == 'edit' && !$can_edit || $mode_s == 'edit' && $user_data['user_id'] == ANONYMOUS)
		{
			shout_error('NO_EDIT_PERM');
		}
		else
		{
			// Flood control
			$current_time = time();
			if (!$auth->acl_get('u_shout_ignore_flood') && $mode_s == 'post')
			{
				$sql = 'SELECT MAX(shout_time) AS last_post_time
					FROM ' .$_table. '
					WHERE shout_user_id = ' .(int)$user->data['user_id'];
				if ($result = $db->sql_query($sql))
				{
					if ($row = $db->sql_fetchrow($result))
					{
						$db->sql_freeresult($result);
						if ($row['last_post_time'] > 0 && ($current_time - $row['last_post_time']) < $config['shout_flood_interval'])
						{
							shout_error('FLOOD_ERROR');
						}
					}
				}
				else
				{
					shout_sql_error($sql, __LINE__, __FILE__);
				}
			}
			if ($user->lang['SHOUT_COPY'] !== $on1 || $user->lang['SHOUTBOX'] !== $on2)
			{
				exit;
			}
			
			if ($mode_s == 'edit')
			{
				$ok_edit = false;
				if (!$can_edit_mod)  // If not able to edit all messages
				{
					// We need to be sure its this users his shout.
					$sql = 'SELECT shout_user_id 
						FROM ' .$_table. " 
						WHERE shout_id = $shout_id";
					$result = $db->sql_query_limit($sql, 1);
					if (!$result)
					{
						shout_sql_error($sql, __LINE__, __FILE__);
					}
					$row = $db->sql_fetchfield('shout_user_id');
					$db->sql_freeresult();
					
					// Not his shout, display error
					if (!$row || $row != $user->data['user_id'])
					{
						shout_error('NO_EDIT_PERM');
					}
					else
					{
						$ok_edit = true;
					}
				}
				else
				{
					$ok_edit = true;
				}
			}
			
			$message = utf8_normalize_nfc(request_var('chat_message', '', true));
			
			if ($message == '')
			{
				shout_error('MESSAGE_EMPTY');
			}
			
			parse_shout_message($message, (($_priv == '_priv') ? true : false));
			
			// Store message length...
			$message_length = ($mode_s == 'post') ? utf8_strlen($message) : utf8_strlen(preg_replace('#\[\/?[a-z\*\+\-]+(=[\S]+)?\]#ius', ' ', $message));
			// Maximum message length check. 0 disables this check completely.
			// Permission to ignore the limit of characters in post
			if ((int) $config['shout_max_post_chars'] > 0 && $message_length > (int) $config['shout_max_post_chars'] && !$auth->acl_get('u_shout_limit_post'))
			{
				shout_error('TOO_MANY_CHARS_POST', $message_length, (int) $config['shout_max_post_chars']);
			}
			
			// Don't parse img if unautorised and return url img only
			if (strpos($message, '[img]') !== false && strpos($message, '[/img]') !== false && !$auth->acl_get('u_shout_image'))
			{
				$_message = str_replace(array('[img]', '[/img]'), '', $message);
			}
			// If autorised to post images, use the good way only!
			/*elseif (strpos($message, '[img]') !== false && strpos($message, '[/img]') !== false && $auth->acl_get('u_shout_image'))
			{
				shout_error('SHOUT_IMG_POST_ERROR');
			}*/
			
			$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
			$allow_urls 	= true;
			$allow_bbcode 	= ($auth->acl_get('u_shout_bbcode')) ? true : false;
			$allow_smilies 	= ($auth->acl_get('u_shout_smilies')) ? true : false;
			generate_text_for_storage($message, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			$sql_ary = array(
				'shout_text'				=> $message,
				'shout_bbcode_uid'			=> $uid,
				'shout_bbcode_bitfield'		=> $bitfield,
				'shout_bbcode_flags'		=> $options,
			);
			
			if ($mode_s == 'edit')
			{
				if ($ok_edit)
				{
					$sql = 'UPDATE ' .$_table. ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE shout_id = $shout_id";
					$db->sql_query($sql);
					
					// increase the time of the last message to see changements appear for everybody
					$sql = 'SELECT MAX(shout_id) AS shout_end FROM ' .$_table;
					$result = $db->sql_query($sql);
					$max_shout = $db->sql_fetchfield('shout_end');
					$db->sql_freeresult($result);
					
					$sql = 'UPDATE ' .$_table. " SET shout_time = shout_time + 1 WHERE shout_id = $max_shout";
				}
				else
				{
					shout_error('NO_EDIT_PERM');
				}
			}
			else
			{
				$sql_ary += array(
					'shout_time'				=> (int)time(),
					'shout_user_id'				=> (int)$user->data['user_id'],
					'shout_ip'					=> (string)$user->ip,
					'shout_robot'				=> 0,
					'shout_forum'				=> 0,
				);
				$sql = 'INSERT INTO ' .$_table. ' ' . $db->sql_build_array('INSERT', $sql_ary);
				
				set_config_count('shout_nr' .$priv, 1, true);
			}
			
			if (!$db->sql_query($sql)) 
			{
				shout_sql_error($sql, __LINE__, __FILE__);
			}
			
			echo '<msg>' . $user->lang['POSTED'] . '</msg></xml>';
			
			if ($config['shout_on_cron' .$_priv])
			{
				if ((int)$config['shout_max_posts' .$_priv] > 0)
				{
					delete_shout_posts(($_priv == '_priv') ? true : false);
				}
			}
			exit;
		}
		
	break;

	case 'check':
	case 'check_pop':
	case 'check_priv':
	
		if ($mode != 'check_priv')
		{
			$perm = '_view';
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		else
		{
			$_priv = $perm = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		if (!$auth->acl_get('u_shout' .$perm))
	    {
	        shout_error('NO_VIEW' .$_Priv. '_PERM');
		}

		$sql = 'SELECT shout_time
			FROM ' .$_table. '
			ORDER BY shout_time DESC';
		$result = $db->sql_query_limit($sql, 1);
		$time = $db->sql_fetchfield('shout_time');
		$db->sql_freeresult($result);
		if ($result)
		{
			// Reload the shoutbox to display correct minutes ago
			// Only for users with format date like "a minute ago"
			// And only if user have choose that, not the sound...
			$sort_date = array('|M Y|', '|F Y|', '|F Y|', '|d F Y|', '|d M Y|, H:i', '|F j, Y|, g:i a', '|D d M y|, H:i');
			list($correct, $new_sound, $error_sound, $del_sound, $_index, $_forum, $_topic, $_another, $_portal) = explode(', ', $user->data['user_shout']);
			if ($correct == 1 && in_array($user->data['user_dateformat'], $sort_date) && !$user->data['is_bot']) 
			{
				//Do this only for messages < 1 hour
				// + 5 secondes to pass the hour...
				if ($time + 3605 > time())
				{
					$last = 1;
				}
			}
			if ($user->lang['SHOUT_COPY'] !== $on1 || $user->lang['SHOUTBOX'] !== $on2)
			{
				$time = 1;
				$last = 0;
			}
			echo "<last>$time</last><time>" .(int)($time != $last). '</time></xml>';
			exit;
		}
		else
		{
			shout_sql_error($sql, __LINE__, __FILE__);
		}
		
	break;

	case 'number':
	case 'number_pop':
	case 'number_priv':
	
	    if ($mode == 'number' || $mode == 'number_pop')
		{
			$perm = '_view';
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		else
		{
			$_priv = $perm = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		if (!$auth->acl_get('u_shout' .$perm))
	    {
	        shout_error('NO_VIEW' .$_Priv. '_PERM');
		}
		if ($user->lang['SHOUT_COPY'] !== $on1 || $user->lang['SHOUTBOX'] !== $on2)
		{
			echo "<nr>0</nr></xml>";
			exit;
		}
	
		$sql = 'SELECT COUNT(shout_id) as nr 
			FROM ' . $_table. "
			WHERE " .$db->sql_in_set('shout_forum', array_keys($auth->acl_getf('f_read', true))). " OR shout_forum = 0";
		$result = $db->sql_query($sql);
		if (!$result)
		{
			shout_sql_error($sql, __LINE__, __FILE__);
		}
		$row = (int)$db->sql_fetchfield('nr');
		$db->sql_freeresult($result);
		// Limit the number of messages to display
		$row = ($row > $config['shout_max_posts_on' .$_priv]) ? $config['shout_max_posts_on' .$_priv] : $row;
		
		echo "<nr>$row</nr></xml>";
		exit;
	
	break;

	case 'version':
	
	    if (!$auth->acl_get('a_'))
	    {
	        shout_error('NO_ADMIN_PERM');
		}

		// Get current and latest version
		$errstr = '';
		$errno = 0;

		$info = get_remote_file('breizh-portal.com', '/updatecheck/', 'shoutbox.txt', $errstr, $errno);

		if ($info === false)
		{
			trigger_error($errstr, E_USER_WARNING);
		}

		$info = explode("\n", $info);
		$latest_version = trim($info[0]);
		
		$up_to_date = (string)(version_compare(str_replace('rc', 'RC', strtolower($config['shout_version'])), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;
		$current = $config['shout_version'];
		
		print "<newest>$latest_version</newest><current>$current</current><uptodate>$up_to_date</uptodate></xml>";
		exit;
	
	break;
	
	case 'view':
	case 'view_pop':
	case 'view_priv':
	
		$shout 	= request_var('s', -1);
		$start 	= request_var('start', 0);
		if ($mode == 'view') // Normal shoutbox
		{
			$perm = '_view';
			$_priv = $_Priv = $sort = '';
			$_table = SHOUTBOX_TABLE;
		}
		elseif ($mode == 'view_pop') // Popup shoutbox
		{
			$sort = '_pop';
			$perm = '_view';
			$_priv = $_Priv = '';
			$_table = SHOUTBOX_TABLE;
		}
		elseif ($mode == 'view_priv') // Private shoutbox
		{
			$sort = $_priv = $perm = '_priv';
			$_Priv = '_PRIV';
			$_table = SHOUTBOX_PRIV_TABLE;
		}
		$is_ie = (strpos(strtolower($user->data['session_browser']), 'msie') !== false) ? '' : '_non';
		
		$shout_number = $config['shout' .$is_ie. '_ie_nr' .$sort];

		if (!$auth->acl_get('u_shout' .$perm))
	    {
	        shout_error('NO_VIEW' .$_Priv. '_PERM');
		}

		$i = 0;
		// Prevent somes errors in permissions
		// If a user can edit all messages, he can edit it's messages :)
		$can_edit_mod 	= ($auth->acl_get('u_shout_edit_mod')) ? true : false;
		$can_edit 		= ($can_edit_mod) ? true : $auth->acl_get('u_shout_edit');
		// If a user can view all ip, he can view it's ip :)
		$can_info_mod 	= ($auth->acl_get('u_shout_info')) ? true : false;
		$can_info 		= ($can_edit_mod) ? true : $auth->acl_get('u_shout_info_s');
		// If a user can delete all messages, he can delete it's messages :)
		$can_delete_mod = ($auth->acl_get('u_shout_delete')) ? true : false;
		$can_delete 	= ($can_delete_mod) ? true : $auth->acl_get('u_shout_delete_s');
		
		if ($user->lang['SHOUT_COPY'] !== $on1 || $user->lang['SHOUTBOX'] !== $on2)
		{
			echo '<error>' . $user->lang['SHOUT_AVERT'] . '</error></xml>';
			exit;
		}

		// Robot can say the date of day!
		// But, only if you want!
		if (($config['shout_hello'] || $config['shout_hello_priv']) && $config['shout_enable_robot'])
		{
			// And just at the time you want...
			$time = $config['shout_hello_hour'];
			if (date('H') == $time)
			{
				if (date('H:i') > "$time:00" && date('H:i') < "$time:59")
				{
					hello_robot_shout();
				}
			}
		}
		
		$sql = 'SELECT s.*, u.user_id, u.user_colour, u.username, u.user_country_flag, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
			FROM ' .$_table. ' AS s
			LEFT JOIN ' . USERS_TABLE . " AS u ON s.shout_user_id = u.user_id
			WHERE " .$db->sql_in_set('s.shout_forum', array_keys($auth->acl_getf('f_read', true))). " OR s.shout_forum = 0
			ORDER BY s.shout_id DESC";
		$result = $db->sql_query_limit($sql, $shout_number, $start);
		
		if (!$result)
		{
			shout_sql_error($sql, __LINE__, __FILE__);
		}
		else
		{
			$row = $db->sql_fetchrow($result);
			if (!$row || !$on1 || !$on2)
			{
				$db->sql_freeresult($result);
				echo "<error>" .$user->lang['NO_MESSAGE']. "</error></xml>";
				exit;
			}			
			do
			{
				// Country Flags Version 3.0.6
				if ($user->data['user_id'] != ANONYMOUS)
				{
					$flag_title = $flag_img = $flag_img_src = '';
					get_user_flag($row['user_country_flag'], $row['user_country'], $flag_title, $flag_img, $flag_img_src);
				}
				// Country Flags Version 3.0.6
				
				$flag_username = $row['username'] . ' ' . $flag_img;

				echo "<posts>\n";
				
				$is_robot 				= ($row['shout_user_id'] == ROBOT) ? true : false;
				$use_avatar_defaut		= ($config['shout_avatar_user'] && !$row['user_avatar'] && !$is_robot) ? true : false;
				$row['user_avatar_type'] = ($is_robot || $use_avatar_defaut) ? AVATAR_ROBOT : $row['user_avatar_type'];
				$row['user_avatar'] 	= ($is_robot) ? 'images/shoutbox/' .$config['shout_avatar_img_robot'] : (($use_avatar_defaut) ? 'images/shoutbox/' .$config['shout_avatar_img'] : $row['user_avatar']);  // robot's avatar or defaut avatar if want it
				$row['username'] 		= ($row['shout_user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['shout_user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);  // Display the correct username
				$row['user_colour'] 	= ($is_robot) ? $config['shout_color_robot'] : $row['user_colour'];  // Display the correct color
				$use_avatar 			= ($user->optionget('viewavatars') && $row['user_avatar'] && $config['shout_avatar'] && !$is_robot && $config['allow_avatar']) ? true : false;  // Display the avatar
				$use_avatar 			= ($is_robot && $config['shout_avatar_robot'] && $config['shout_avatar'] && $config['allow_avatar']) ? ($user->optionget('viewavatars') ? true : false) : $use_avatar; // Display Robot avatar if yes in config
				$row['user_avatar_width'] = ($is_robot || $use_avatar_defaut) ? $config['shout_avatar_height'] : $row['user_avatar_width'] / ($row['user_avatar_height'] / $config['shout_avatar_height']);  // Methode to resize avatar, height is in the config
				$row['title_avatar'] 	= (!$use_avatar_defaut) ? sprintf($user->lang['SHOUT_AVATAR_TITLE'], $row['username']) : sprintf($user->lang['SHOUT_AVATAR_NONE'], $row['username']); // Change the avatar title including the username
				$row['username'] 		= get_username_string('full', $row['user_id'], $flag_username, $row['user_colour']);
				$row['username'] 		= (strpos(strtolower($user->browser), 'msie') !== false) ? str_replace('/>', '/><img src="' .$phpbb_root_path. 'images/spacer.gif" alt="" width="5" height="1" />', $row['username']) : $row['username']; // Fix a bug in IE
				$row['avatar']			= ($use_avatar) ? '<img src="' .$phpbb_root_path. 'images/spacer.gif" alt="" width="1" height="' .($config['shout_avatar_height']+1). '" />' . shout_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $config['shout_avatar_height'], $row['title_avatar']) : false;
				$row['username'] 		= (!$row['avatar']) ? '<img src="' .$phpbb_root_path. 'images/spacer.gif" alt="" width="1" height="20" />' .$row['username'] : $row['username'];
				$row['shout_time']		= $user->format_date($row['shout_time']);
				$row['edit']			= $row['delete'] = $row['show_ip'] = false;  // Important!
				$row['num']				= $i+1;
				$row['is_ip'] 			= $user->ip;
				$row['msg_plain']   	= $user->lang['NO_MESSAGE']; // It will be replaced if user can edit ;).
				
				// Verifie permissions for delete, show_ip and edit
				if ($user->data['user_id'] != ANONYMOUS && !$user->data['is_bot'])
				{
					if ($can_delete_mod || ($row['user_id'] == $user->data['user_id']) && $can_delete)
					{
						$row['delete'] = true;
					}
					if ($can_info_mod || ($row['user_id'] == $user->data['user_id']) && $can_info)
					{
						$row['show_ip'] = true;
					}
					if ($can_edit_mod || ($row['user_id'] == $user->data['user_id']) && $can_edit)
					{
						$row['edit'] = true;
						$row['msg_plain'] = $row['shout_text'];
						decode_message($row['msg_plain'], $row['shout_bbcode_uid']);
					}
				}
				
				// Double protect this information...
				if (!$row['show_ip'])
				{
					unset($row['shout_ip']);
					$row['shout_ip'] = $row['is_ip'];
				}
				
				$row['shout_text'] 	= generate_text_for_display($row['shout_text'], $row['shout_bbcode_uid'], $row['shout_bbcode_bitfield'], $row['shout_bbcode_flags']);
				$row['shout_text'] 	= (strpos(strtolower($user->browser), 'msie') !== false) ? str_replace(' />', ' /><img src="' .$phpbb_root_path. 'images/spacer.gif" alt="" width="5" height="1" />', $row['shout_text']) : $row['shout_text'];
				// Active external links with prime links if installed
				if (defined('INCLUDES_PRIME_LINKS'))
				{
					$row['shout_text'] 	= str_replace('class="postlink"', 'class="postlink" target="_blank"', $row['shout_text']);
				}

				// 5 is the length of <br /> - 1.
				if (substr($row['shout_text'], 0, 5) == '<br />')
				{
					$row['shout_text'] = substr($row['shout_text'], 5);
				}
				$is_img = ($row['delete'] && $row['show_ip'] && $row['show_ip']) ? false : true;
				if ($is_img)
				{
					$before = ($row['avatar']) ? ($config['shout_avatar_height'] + 2) : '20';
				}
				else
				{
					$before = ($row['avatar']) ? ($config['shout_avatar_height'] + 4) : '22';
				}
				
				$before_img = '<img src="images/spacer.gif" alt="" height="' .$before.'" width="1" /> ';
				$row['shout_text'] = $before_img . $row['shout_text'];
				
				// Next items aren't needed in XML.
				unset($row['shout_bbcode_uid'], $row['user_allowsmile'], $row['shout_bbcode_bitfield'], $row['shout_bbcode_flags']);
				
				foreach ($row as $key => $value)
				{
					if (is_numeric($key))
					{
						continue;
					}
					echo "\t<$key>$value</$key>\n";
				}
				echo "</posts>\n";
			}
			while ($row = $db->sql_fetchrow($result));
			$db->sql_freeresult($result);
			
			echo '</xml>';
			exit;
		}
		
	break;
}

?>