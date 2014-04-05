<?php
/**
*
* @package acp Breizh Shoutbox
* @version $Id: acp_shoutbox.php 140 20:09 31/12/2010 Sylver35 Exp $
* @copyright (c) 2010, 2011 Sylver35    http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
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
* @package acp
*/
class acp_shoutbox
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang(array('mods/shout', 'install'));
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;
		add_form_key('acp_shoutbox');
		$this->tpl_name 	= 'acp_shoutbox';
		$this->page_title 	= 'ACP_SHOUT_' . strtoupper($mode) . '_T';

		switch ($mode)
		{
			case 'version':
				
				// Get current and latest version
				$current_version = (isset($config['shout_version'])) ? str_replace(' ', '.', $config['shout_version']) : '0';
				$errstr = '';
				$errno = 0;
				$source = 'breizh-portal.com';
				if (!isset($config['shout_source']) || $config['shout_source'] !== 'http://' .$source. '/')
				{
					set_config('shout_source', "http://$source/", false);
				}
				$info = get_remote_file($source, '/updatecheck/', 'shoutbox.txt', $errstr, $errno);
				set_shout_value();
				if ($info === false)
				{
					$template->assign_vars(array(
						'S_ERROR'   => true,
						'ERROR_MSG' => sprintf($user->lang['UNABLE_CONNECT'], $errstr),
					));
				}
				else
				{
					$info = explode("\n", $info);
					$latest_version = trim($info[0]);

					$up_to_date = (version_compare(str_replace('rc', 'RC', strtolower($config['shout_version'])), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;
					
					if (!$up_to_date)
					{
						$template->assign_vars(array(
							'S_ERROR'   			=> true,
							'ERROR_MSG' 			=> sprintf($user->lang['NEW_VERSION'], $config['shout_version'], $latest_version),
							'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['SHOUTBOX_UPDATE_INSTRUCTIONS'], trim($info[1]), append_sid("{$phpbb_root_path}install/index.$phpEx"), $latest_version, trim($info[2]), append_sid("{$phpbb_admin_path}index.$phpEx", 'i=styles&amp;mode=template'), append_sid("{$phpbb_admin_path}index.$phpEx", 'i=styles&amp;mode=theme'), trim($info[3])),
							'S_UP_TO_DATE'			=> false,
						));
					}
					elseif ($up_to_date)
					{
						$template->assign_vars(array(
							'S_ERROR'   			=> false,
							'UP_TO_DATE_MSG'		=> sprintf($user->lang['SHOUT_VERSION_UP_TO_DATE'], $config['shout_version']),
							'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['SHOUTBOX_INSTRUCTIONS'], $config['shout_version'], trim($info[1]), trim($info[2]), trim($info[3])),
							'S_UP_TO_DATE'			=> true,
						));
					}
				}
				
				$img_src = 'demande.png';
				$template->assign_vars(array(
					'S_VERSION'			=> true,
					'SHOUT_VERSION'     => isset($config['shout_version']) ? $config['shout_version'] : '0',
					'LATEST_VERSION'	=> ($info) ? $latest_version : $user->lang['SHOUT_NO_VERSION'],
				));
			
			break;
			
			case 'configs':

				$update = (isset($_POST['update'])) ? true : false;
				
				if ($update)
				{
					$shout_enable				= request_var('shout_enable', 1);
					$shout_see_buttons			= request_var('shout_see_buttons', 1);
					$shout_see_buttons_left		= request_var('shout_see_buttons_left', 1);
					$shout_avatar				= request_var('shout_avatar', 1);
					$shout_avatar_height		= request_var('shout_avatar_height', 20);
					$shout_avatar_robot			= request_var('shout_avatar_robot', 1);
					$shout_avatar_user			= request_var('shout_avatar_user', 1);
					$shout_avatar_img			= request_var('shout_avatar_img', 'no_avatar.gif');
					$shout_avatar_img_robot		= request_var('shout_avatar_img_robot', 'avatar_robot.png');
					$shout_sound_on				= request_var('shout_sound_on', 1);
					$shout_sound_new			= request_var('shout_sound_new', '');
					$shout_sound_error			= request_var('shout_sound_error', '');
					$shout_sound_del			= request_var('shout_sound_del', '');
					$shout_rules				= request_var('shout_rules', 1);
					$shout_panel				= request_var('shout_panel', 1);
					$shout_panel_all			= request_var('shout_panel_all', 0);
					$shout_nr_acp				= request_var('shout_nr_acp', 20);
					$shout_max_post_chars		= request_var('shout_max_post_chars', 300);
					$shout_smilies_width		= request_var('shout_smilies_width', 350);
					$shout_smilies_height		= request_var('shout_smilies_height', 400);
					$shout_index				= request_var('shout_index', 1);
					$shout_position_index		= request_var('shout_position_index', 0);
					$shout_forum				= request_var('shout_forum', 1);
					$shout_position_forum		= request_var('shout_position_forum', 0);
					$shout_topic				= request_var('shout_topic', 1);
					$shout_position_topic		= request_var('shout_position_topic', 0);
					$shout_another				= request_var('shout_another', 0);
					$shout_position_another		= request_var('shout_position_another', 0);
					$shout_portal				= request_var('shout_portal', 0);
					$shout_position_portal		= request_var('shout_position_portal', 0);

					$settings = array (
						'shout_enable'				=> $shout_enable,
						'shout_see_buttons'			=> $shout_see_buttons,
						'shout_see_buttons_left'	=> $shout_see_buttons_left,
						'shout_avatar'				=> $shout_avatar,
						'shout_avatar_height'		=> $shout_avatar_height,
						'shout_avatar_robot'		=> $shout_avatar_robot,
						'shout_avatar_user'			=> $shout_avatar_user,
						'shout_avatar_img'			=> $shout_avatar_img,
						'shout_avatar_img_robot'	=> $shout_avatar_img_robot,
						'shout_sound_on'			=> $shout_sound_on,
						'shout_sound_new'			=> $shout_sound_new,
						'shout_sound_error'			=> $shout_sound_error,
						'shout_sound_del'			=> $shout_sound_del,
						'shout_rules'				=> $shout_rules,
						'shout_panel'				=> $shout_panel,
						'shout_panel_all'			=> $shout_panel_all,
						'shout_nr_acp'				=> $shout_nr_acp,
						'shout_max_post_chars'		=> $shout_max_post_chars,
						'shout_smilies_width'		=> $shout_smilies_width,
						'shout_smilies_height'		=> $shout_smilies_height,
						'shout_index'				=> $shout_index,
						'shout_position_index'		=> $shout_position_index,
						'shout_forum'				=> $shout_forum,
						'shout_position_forum'		=> $shout_position_forum,
						'shout_topic'				=> $shout_topic,
						'shout_position_topic'		=> $shout_position_topic,
						'shout_another'				=> $shout_another,
						'shout_position_another'	=> $shout_position_another,
						'shout_portal'				=> $shout_portal,
						'shout_position_portal'		=> $shout_position_portal,
					);

					foreach ($settings as $config_name => $config_value)
					{
						set_config($config_name, $config_value, false);
					}
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$template->assign_vars(array(
						'SHOUT_ENABLE'			=> (isset($config['shout_enable'])) ? (((bool)$config['shout_enable'] == 1) ? true : false) : '',
						'SHOUT_SEE_BUTTONS'		=> (isset($config['shout_see_buttons'])) ? (((bool)$config['shout_see_buttons'] == 1) ? true : false) : '',
						'SHOUT_SEE_BUTTONS_LEFT'=> (isset($config['shout_see_buttons_left'])) ? (((bool)$config['shout_see_buttons_left'] == 1) ? true : false) : '',
						'SHOUT_AVATAR'			=> (isset($config['shout_avatar'])) ? (((bool)$config['shout_avatar'] == 1) ? true : false) : '',
						'SHOUT_AVATAR_HEIGHT'	=> (isset($config['shout_avatar_height'])) ? (int)$config['shout_avatar_height'] : '',
						'SHOUT_AVATAR_ROBOT'	=> (isset($config['shout_avatar_robot'])) ? (((bool)$config['shout_avatar_robot'] == 1) ? true : false) : '',
						'SHOUT_AVATAR_USER'		=> (isset($config['shout_avatar_user'])) ? (((bool)$config['shout_avatar_user'] == 1) ? true : false) : '',
						'SHOUT_AVATAR_IMG'		=> (isset($config['shout_avatar_img'])) ? (string)$config['shout_avatar_img'] : '',
						'SHOUT_AVATAR_IMG_BOT'	=> (isset($config['shout_avatar_img_robot'])) ? (string)$config['shout_avatar_img_robot'] : '',
						'SHOUT_SOUND_ON'		=> (isset($config['shout_sound_on'])) ? (((bool)$config['shout_sound_on'] == 1) ? true : false) : '',
						'NEW_SOUND'				=> build_adm_sound_select('new'),
						'ERROR_SOUND'			=> build_adm_sound_select('error'),
						'DEL_SOUND'				=> build_adm_sound_select('del'),
						'SHOUT_NR_ACP'			=> (isset($config['shout_nr_acp'])) ? (int)$config['shout_nr_acp'] : '',
						'SHOUT_MAX_POST_CHARS'	=> (isset($config['shout_max_post_chars'])) ? (int)$config['shout_max_post_chars'] : '',
						'SHOUT_SMILIES_W'		=> (isset($config['shout_smilies_width'])) ? (int)$config['shout_smilies_width'] : '',
						'SHOUT_SMILIES_H'		=> (isset($config['shout_smilies_height'])) ? (int)$config['shout_smilies_height'] : '',
						'SHOUT_RULES'			=> (isset($config['shout_rules'])) ? (((bool)$config['shout_rules'] == 1) ? true : false) : '',
						'SHOUT_PANEL'			=> (isset($config['shout_panel'])) ? (((bool)$config['shout_panel'] == 1) ? true : false) : '',
						'SHOUT_PANEL_ALL'		=> (isset($config['shout_panel_all'])) ? (((bool)$config['shout_panel_all'] == 1) ? true : false) : '',
						'SHOUT_INDEX_ON'		=> (isset($config['shout_index'])) ? (((bool)$config['shout_index'] == 1) ? true : false) : '',
						'INDEX_SHOUT_TOP'		=> (isset($config['shout_position_index'])) ? (((int)$config['shout_position_index'] == 0) ? true : false) : '',
						'INDEX_SHOUT_AFTER'		=> (isset($config['shout_position_index'])) ? (((int)$config['shout_position_index'] == 1) ? true : false) : '',
						'INDEX_SHOUT_END'		=> (isset($config['shout_position_index'])) ? (((int)$config['shout_position_index'] == 2) ? true : false) : '',
						'SHOUT_FORUM_ON'		=> (isset($config['shout_forum'])) ? (((bool)$config['shout_forum'] == 1) ? true : false) : '',
						'POS_SHOUT_FORUM_TOP'	=> (isset($config['shout_position_forum'])) ? (((int)$config['shout_position_forum'] == 0) ? true : false) : '',
						'POS_SHOUT_FORUM_END'	=> (isset($config['shout_position_forum'])) ? (((int)$config['shout_position_forum'] == 1) ? true : false) : '',
						'SHOUT_TOPIC_ON'		=> (isset($config['shout_topic'])) ? (((bool)$config['shout_topic'] == 1) ? true : false) : '',
						'POS_SHOUT_TOPIC_TOP'	=> (isset($config['shout_position_topic'])) ? (((int)$config['shout_position_topic'] == 0) ? true : false) : '',
						'POS_SHOUT_TOPIC_END'	=> (isset($config['shout_position_topic'])) ? (((int)$config['shout_position_topic'] == 1) ? true : false) : '',
						'SHOUT_ANOTHER_ON'		=> (isset($config['shout_another'])) ? (((bool)$config['shout_another'] == 1) ? true : false) : '',
						'POS_SHOUT_ANOTHER_TOP'	=> (isset($config['shout_position_another'])) ? (((int)$config['shout_position_another'] == 0) ? true : false) : '',
						'POS_SHOUT_ANOTHER_END'	=> (isset($config['shout_position_another'])) ? (((int)$config['shout_position_another'] == 1) ? true : false) : '',
						'SHOUT_PORTAL_ON'		=> (isset($config['shout_portal'])) ? (((bool)$config['shout_portal'] == 1) ? true : false) : '',
						'POS_SHOUT_PORTAL_TOP'	=> (isset($config['shout_position_portal'])) ? (((int)$config['shout_position_portal'] == 0) ? true : false) : '',
						'POS_SHOUT_PORTAL_END'	=> (isset($config['shout_position_portal'])) ? (((int)$config['shout_position_portal'] == 1) ? true : false) : '',
						'SHOUT_PORTAL'			=> (file_exists($phpbb_root_path. 'portal.' .$phpEx)) ? true : false,
						'U_ACTION'				=> $this->u_action,
					));
				}
				
				$img_src = 'settings.png';
				$template->assign_var('S_CONFIGS', true);
			
			break;
			
			case 'rules':
				
				include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
				include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
				$update = (isset($_POST['update'])) ? true : false;
				
				if ($update)
				{
					$sql = "SELECT lang_iso FROM " . LANG_TABLE . " ORDER BY lang_id";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$rules_iso 			= utf8_normalize_nfc(request_var('shout_rules_' .$row['lang_iso'], '', true));
						$rules_uid 			= utf8_normalize_nfc(request_var('shout_rules_uid_' .$row['lang_iso'], '', true));
						$rules_bitfield 	= utf8_normalize_nfc(request_var('shout_rules_bitfield_' .$row['lang_iso'], '', true));
						$rules_flags 		= utf8_normalize_nfc(request_var('shout_rules_flags_' .$row['lang_iso'], '', true));
						$rules_iso_priv 	= utf8_normalize_nfc(request_var('shout_rules_' .$row['lang_iso']. '_priv', '', true));
						$rules_uid_priv 	= utf8_normalize_nfc(request_var('shout_rules_uid_' .$row['lang_iso']. '_priv', '', true));
						$rules_bitfield_priv = utf8_normalize_nfc(request_var('shout_rules_bitfield_' .$row['lang_iso']. '_priv', '', true));
						$rules_flags_priv 	= utf8_normalize_nfc(request_var('shout_rules_flags_' .$row['lang_iso']. '_priv', '', true));
						
						generate_text_for_storage($rules_iso, $rules_uid, $rules_bitfield, $rules_flags, true, true, true);
						generate_text_for_storage($rules_iso_priv, $rules_uid_priv, $rules_bitfield_priv, $rules_flags_priv, true, true, true);
						
						$settings = array (
							'shout_rules_' .$row['lang_iso']  					=> $rules_iso,
							'shout_rules_bitfield_' .$row['lang_iso']  			=> $rules_bitfield,
							'shout_rules_uid_' .$row['lang_iso']  				=> $rules_uid,
							'shout_rules_flags_' .$row['lang_iso']  			=> $rules_flags,
							'shout_rules_' .$row['lang_iso']. '_priv'  			=> $rules_iso_priv,
							'shout_rules_bitfield_' .$row['lang_iso']. '_priv'  => $rules_bitfield_priv,
							'shout_rules_uid_' .$row['lang_iso']. '_priv'  		=> $rules_uid_priv,
							'shout_rules_flags_' .$row['lang_iso']. '_priv'  	=> $rules_flags_priv,
						);
						
						foreach ($settings as $config_name => $config_value)
						{
							set_config($config_name, $config_value, false);
						}
					}
					$db->sql_freeresult($result);
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$i = 1;
					$sql = "SELECT lang_iso, lang_local_name
						FROM " . LANG_TABLE . "
						ORDER BY lang_id";
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$iso 			= $row['lang_iso'];
						$text 			= (isset($config['shout_rules_' .$iso])) ? $config['shout_rules_' .$iso] : '';
						$uid 			= (isset($config['shout_rules_uid_' .$iso])) ? $config['shout_rules_uid_' .$iso] : '';
						$bitfield 		= (isset($config['shout_rules_bitfield_' .$iso])) ? $config['shout_rules_bitfield_' .$iso] : '';
						$flags 			= (isset($config['shout_rules_flags_' .$iso])) ? $config['shout_rules_flags_' .$iso] : '';
						$text_priv 		= (isset($config['shout_rules_' .$iso. '_priv'])) ? $config['shout_rules_' .$iso. '_priv'] : '';
						$uid_priv		= (isset($config['shout_rules_uid_' .$iso. '_priv'])) ? $config['shout_rules_uid_' .$iso. '_priv'] : '';
						$bitfield_priv 	= (isset($config['shout_rules_bitfield_' .$iso. '_priv'])) ? $config['shout_rules_bitfield_' .$iso. '_priv'] : '';
						$flags_priv 	= (isset($config['shout_rules_flags_' .$iso. '_priv'])) ? $config['shout_rules_flags_' .$iso. '_priv'] : '';
						
						$display = generate_text_for_display($text, $uid, $bitfield, $flags);
						decode_message($text, $uid);
						$display_priv = generate_text_for_display($text_priv, $uid_priv, $bitfield_priv, $flags_priv);
						decode_message($text_priv, $uid_priv);
						
						$template->assign_block_vars('rules', array(
							'RULES_NR'				=> $i,
							'RULES_TEXT'			=> $text,
							'RULES_TEXT_DISPLAY'	=> $display,
							'RULES_ON'				=> sprintf($user->lang['SHOUT_RULES_ON'], $iso),
							'RULES_ON_EXPLAIN'		=> sprintf($user->lang['SHOUT_RULES_ON_EXPLAIN'], $row['lang_local_name']),
							'RULES_TEXT_PRIV'		=> $text_priv,
							'RULES_TEXT_DISPLAY_PRIV'=> $display_priv,
							'RULES_ON_PRIV_EXPLAIN'	=> sprintf($user->lang['SHOUT_RULES_ON_PRIV_EXPLAIN'], $row['lang_local_name']),
							'RULES_LANG'			=> $row['lang_local_name'],
							'RULES_ISO'				=> $iso,
						));
						$i++;
					}
					$db->sql_freeresult($result);
				}
				
				$img_src = 'alert_2.png';
				$template->assign_var('S_RULES', true);
			break;
			
			case 'overview':
			    
				$action 		= request_var('action', '');
				$start			= request_var('start', 0);
				$marked			= request_var('mark', array(0));
				$deletemark 	= (!empty($_POST['delmarked'])) ? true : false;
				$deletemarklog 	= (!empty($_POST['delmarkedlog'])) ? true : false;

				if ($deletemark)
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						$del_acp = $ni = 0;

						if ($deletemark && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
								$nb_del_acp = $ni + ($del_acp + 1);
								$ni++;
							}
							
							$where_sql = ' WHERE ' . $db->sql_in_set('shout_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql)
						{
							$sql = 'DELETE FROM ' . SHOUTBOX_TABLE . " $where_sql";
							$db->sql_query($sql);
							
							$log_entrie = 'LOG_SELECT' . (($nb_del_acp > 1) ? 'S' : '') . '_SHOUTBOX';
							add_log('admin', $log_entrie, $nb_del_acp);
							set_config_count('shout_del_acp', $nb_del_acp, true);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'		=> $start,
							'delmarked'	=> $deletemark,
							'mark'		=> $marked,
							'i'			=> $id,
							'mode'		=> $mode,
							'action'	=> $action))
						);
					}
				}
				
				if ($deletemarklog)
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						$del_acp = $ni = 0;

						if ($deletemarklog && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
								$nb_del_acp = $ni + ($del_acp + 1);
								$ni++;
							}
							
							$where_sql = ' WHERE ' . $db->sql_in_set('log_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql)
						{
							$sql = 'DELETE FROM ' . LOG_TABLE . " $where_sql";
							$db->sql_query($sql);
							
							$log_entrie = 'LOG_LOG' . (($nb_del_acp > 1) ? 'S' : '') . '_SHOUTBOX';
							add_log('admin', $log_entrie, $nb_del_acp);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'			=> $start,
							'delmarkedlog'	=> $deletemarklog,
							'mark'			=> $marked,
							'i'				=> $id,
							'mode'			=> $mode,
							'action'		=> $action))
						);
					}
				}
				
				if ($action)
				{
					if (!confirm_box(true))
					{
						switch ($action)
						{
							default:
								$confirm = true;
								$confirm_lang = 'CONFIRM_OPERATION';
						}

						if ($confirm)
						{
							confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
								'i'			=> $id,
								'mode'		=> $mode,
								'action'	=> $action,
							)));
						}
					}
					else
					{
						switch ($action)
						{
						    case 'purge':
						        $sql = 'SELECT COUNT(shout_id) as nr FROM ' . SHOUTBOX_TABLE;
								$result = $db->sql_query($sql);
								$row = (int)$db->sql_fetchfield('nr');
								$db->sql_freeresult($result);
								
								$sql = 'DELETE FROM ' . SHOUTBOX_TABLE;
						        $db->sql_query($sql);
								
								set_config_count('shout_del_purge', $row, true);
								add_log('admin', 'LOG_PURGE_SHOUTBOX');
								post_robot_shout(0, $user->ip, false, true, false);
							break;
							
							case 'purge_1':
							case 'purge_2':
							case 'purge_3':
							case 'purge_4':
							case 'purge_5':
							case 'purge_6':
							case 'purge_7':
							case 'purge_8':
								$sort = str_replace('purge_', '', $action);
								$retour = purge_shout_admin($sort, false);
								if ($retour)
								{
									adm_back_link($this->u_action);
								}
							break;
						}
					}
				}
				
				$sql_nr = 'SELECT COUNT(DISTINCT shout_id) as total FROM ' . SHOUTBOX_TABLE;
				$result_nr = $db->sql_query($sql_nr);
				$total_posts = $db->sql_fetchfield('total', $result_nr);
				$db->sql_freeresult($result_nr);
				
				$start = request_var('start', 0);
				$i = $start_log = $li = 0;
				$shout_number = $config['shout_nr_acp'];
				
				$sql = 'SELECT s.*, u.user_id, u.user_colour, u.username
					FROM ' . SHOUTBOX_TABLE . ' AS s
						LEFT JOIN ' . USERS_TABLE . ' AS u ON s.shout_user_id = u.user_id 
					ORDER BY s.shout_time DESC';
				$result = $db->sql_query_limit($sql, $shout_number, $start);
				while ($row = $db->sql_fetchrow($result))
				{
					$row['user_colour'] = ($row['user_id'] == ROBOT) ? $config['shout_color_robot'] : $row['user_colour'];
					$row['username'] 	= ($row['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);
					$shout_text			= str_replace('./', generate_board_url() . '/', $row['shout_text']);
					
					$template->assign_block_vars('messages', array(
						'TIME'				=> $user->format_date($row['shout_time']),
						'POSTER'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
						'ID'				=> $row['shout_id'],
						'MESSAGE'			=> generate_text_for_display($shout_text, $row['shout_bbcode_uid'], $row['shout_bbcode_bitfield'], $row['shout_bbcode_flags']),
						'ROW_NUMBER'		=> $i + ($start + 1),
					));
					$i++;
				}
				$db->sql_freeresult($result);
				
				$sql_nr = 'SELECT COUNT(DISTINCT log_id) as totalog 
					FROM ' . LOG_TABLE . " 
					WHERE log_operation IN ('LOG_SHOUT_SCRIPT', 'LOG_SHOUT_ACTIVEX', 'LOG_SHOUT_APPLET', 'LOG_SHOUT_OBJECTS', 'LOG_SHOUT_IFRAME')";
				$result_nr = $db->sql_query($sql_nr);
				$total_logs = $db->sql_fetchfield('totalog', $result_nr);
				$db->sql_freeresult($result_nr);
				
				$sql = 'SELECT l.log_id, l.user_id, l.log_type, l.log_ip, l.log_time, l.log_operation, l.reportee_id, u.user_id, u.user_colour, u.username
					FROM ' . LOG_TABLE . ' AS l
						LEFT JOIN ' . USERS_TABLE . " AS u ON l.user_id = u.user_id 
					WHERE log_operation IN ('LOG_SHOUT_SCRIPT', 'LOG_SHOUT_ACTIVEX', 'LOG_SHOUT_APPLET', 'LOG_SHOUT_OBJECTS', 'LOG_SHOUT_IFRAME')
					ORDER BY log_time DESC";
				$result = $db->sql_query_limit($sql, $total_logs);
				while ($row = $db->sql_fetchrow($result))
				{
					$row['user_colour'] = ($row['user_id'] == ROBOT) ? $config['shout_color_robot'] : $row['user_colour'];
					$row['username'] 	= ($row['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);
					
					$template->assign_block_vars('logs', array(
						'TIME'				=> $user->format_date($row['log_time']),
						'REPORTEE'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
						'OPERATION'			=> $user->lang[$row['log_operation']],
						'ID'				=> $row['log_id'],
						'IP'				=> $row['log_ip'],
						'ROW_NUMBER'		=> $li + ($start_log + 1),
					));
					$li++;
				}
				$db->sql_freeresult($result);
				
				$img_src = 'stat.png';
				$total_del = $config['shout_del_acp'] + $config['shout_del_auto'] + $config['shout_del_purge'] + $config['shout_del_user'];
				$template->assign_vars(array(
					'S_OVERVIEW'				=> true,
					'S_DISPLAY_MESSAGES'		=> ($i > 0) ? true : false,
					'S_ON_PAGE'					=> ($total_posts > $shout_number) ? true : false,
					'TOTAL_POSTS'				=> $total_posts,
					'SHOUT_VERSION'     		=> $config['shout_version'],
					'TOTAL_MESSAGES'			=> ($total_posts > 1) ? sprintf($user->lang['NUMBER_MESSAGES'], $total_posts) : sprintf($user->lang['NUMBER_MESSAGE'], $total_posts),
					'MESSAGES_TOTAL_NR'			=> sprintf($user->lang['SHOUT_MESSAGES_TOTAL_NR'], $config['shout_nr'], $user->format_date($config['shout_time'])),
					'PAGE_NUMBER' 				=> on_page($total_posts, $shout_number, $start),	
					'PAGINATION' 				=> generate_pagination($this->u_action, $total_posts, $shout_number, $start),
					'LAST_SHOUT_RUN'			=> ($config['shout_last_run'] == $config['shout_time']) ? $user->lang['SHOUT_NEVER'] : $user->format_date($config['shout_last_run']),
					'S_DISPLAY_LOGS'			=> ($li > 0) ? true : false,
					'LOGS_TOTAL_NR'				=> sprintf($user->lang['NUMBER_LOG' .(($config['shout_nr_log'] > 1) ? 'S' : ''). '_TOTAL'], $config['shout_nr_log'], $user->format_date($config['shout_time'])),
					'MESSAGES_DEL_TOTAL'		=> sprintf($user->lang['SHOUT_DEL_NR' .(($total_del  > 1) ? 'S' : ''). ''], $total_del) . $user->lang['SHOUT_DEL_TOTAL'],
					'MESSAGES_DEL_ACP'			=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_acp'] > 1) ? 'S' : ''). ''], $config['shout_del_acp']),
					'MESSAGES_DEL_AUTO'			=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_auto'] > 1) ? 'S' : ''). ''], $config['shout_del_auto']),
					'MESSAGES_DEL_PURGE'		=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_purge'] > 1) ? 'S' : ''). ''], $config['shout_del_purge']),
					'MESSAGES_DEL_USER'			=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_user'] > 1) ? 'S' : ''). ''], $config['shout_del_user']),
				));
			
			break;
			
			case 'config_gen':
			
				$update = isset($_POST['update']) ? true : false;
				
				if ($update)
				{
					$shout_title				= utf8_normalize_nfc(request_var('shout_title', 'shoutbox', true));
					$shout_width_post			= request_var('shout_width_post', 325);
					$shout_prune				= request_var('shout_prune', 0);
					$shout_max_posts_on			= request_var('shout_max_posts_on', 100);
					$shout_max_posts			= request_var('shout_max_posts', 300);
					$shout_on_cron				= request_var('shout_on_cron', 1);
					$shout_log_cron				= request_var('shout_log_cron', 0);
					$shout_non_ie_nr			= request_var('shout_non_ie_nr', 25);
					$shout_ie_nr				= request_var('shout_ie_nr', 5);
					$shout_height				= request_var('shout_height', 160);
					$shout_pos_smil				= request_var('shout_pos_smil', 1);
					$shout_pos_rules			= request_var('shout_pos_rules', 1);
					$shout_pos_color			= request_var('shout_pos_color', 1);
					$shout_pos_chars			= request_var('shout_pos_chars', 1);
					$shout_color_background 	= request_var('shout_color_background', 'blue');
					$shout_color_background_sub = request_var('shout_color_background_sub', 'transparent');
					$shout_button_background 	= request_var('shout_button_background', 1);
					$shout_bar_option			= request_var('shout_bar_option', 1);
					$shout_pagin_option			= request_var('shout_pagin_option', 0);

					$settings = array (
						'shout_title'				=> str_replace("'", $user->lang['SHOUT_PROTECT'], $shout_title),
						'shout_width_post'			=> $shout_width_post,
						'shout_prune'				=> $shout_prune,
						'shout_max_posts_on'		=> $shout_max_posts_on,
						'shout_max_posts'			=> $shout_max_posts,
						'shout_on_cron'				=> $shout_on_cron,
						'shout_log_cron'			=> $shout_log_cron,
						'shout_non_ie_nr'			=> $shout_non_ie_nr,
						'shout_ie_nr'				=> $shout_ie_nr,
						'shout_height'				=> $shout_height,
						'shout_pos_smil'			=> $shout_pos_smil,
						'shout_pos_rules'			=> $shout_pos_rules,
						'shout_pos_color'			=> $shout_pos_color,
						'shout_pos_chars'			=> $shout_pos_chars,
						'shout_color_background' 	=> $shout_color_background,
						'shout_color_background_sub'=> $shout_color_background_sub,
						'shout_button_background' 	=> $shout_button_background,
						'shout_bar_option'			=> $shout_bar_option,
						'shout_pagin_option'		=> $shout_pagin_option,
					);

					foreach ($settings as $config_name => $config_value)
					{
						set_config($config_name, $config_value, false);
					}
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$sql_style = 'SELECT s.style_name, s.style_id, t.theme_path as theme
						FROM ' . STYLES_TABLE . ' AS s
						LEFT JOIN ' . STYLES_THEME_TABLE . ' AS t ON t.theme_id = s.theme_id
						WHERE style_id = ' .(int)$config['default_style'];
					$result_style = $db->sql_query($sql_style);
					$style = $db->sql_fetchfield('theme');
					$db->sql_freeresult($result_style);
					$color_path = $phpbb_root_path . 'styles/' .$style. '/theme/images/shout/fond/';
					$option = $option_sub = '';
					
					$imglist = @filelist($color_path, '', 'gif');
					if (sizeof($imglist))
					{
						$imglist = array_values($imglist);
						$imglist = $imglist[0];
						foreach ($imglist as $key => $img)
						{
							$img_id = $img;
							$img = str_replace('.gif', '', $img);
							if (isset($config['shout_color_background']))
							{
								if (!$config['shout_color_background'])
								{
									$no_image_select = true;
									$selected = '';
								}
								else
								{
									$selected = ($config['shout_color_background'] == $img) ? ' selected="selected"' : '';
								}
							}
							else
							{
								$selected = '';
							}
							if (strlen($img) > 255)
							{
								continue;
							}
							$option .= '<option id="' . htmlspecialchars($img_id) . '" title="' . htmlspecialchars($img) . '" value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
						}
						foreach ($imglist as $key_sub => $img_sub)
						{
							$img_id_sub = $img_sub;
							$img_sub = str_replace(array('.gif', '.jpg', '.jpeg', '.png', '.GIF', '.JPG', '.JPEG', '.PNG'), '', $img_sub);
							if (isset($config['shout_color_background_sub']))
							{
								if (!$config['shout_color_background_sub'])
								{
									$no_image_select_sub = true;
									$selected_sub = '';
								}
								else
								{
									$selected_sub = ($config['shout_color_background_sub'] == $img_sub) ? ' selected="selected"' : '';
								}
							}
							else
							{
								$selected_sub = '';
							}
							if (strlen($img_sub) > 255)
							{
								continue;
							}
							$option_sub .= '<option id="' . htmlspecialchars($img_id_sub) . '" title="' . htmlspecialchars($img_sub) . '" value="' . htmlspecialchars($img_sub) . '"' . $selected_sub . '>' . htmlspecialchars($img_sub) . '</option>';
						}
					}
					
					$template->assign_vars(array(
						'SHOUT_TITLE'			=> isset($config['shout_title']) ? (string)utf8_normalize_nfc($config['shout_title'], true) : '',
						'SHOUT_WIDTH_POST'		=> isset($config['shout_width_post']) ? (int)$config['shout_width_post'] : '',
						'SHOUT_PRUNE'			=> isset($config['shout_prune']) ? (int)$config['shout_prune'] : '',
						'SHOUT_MAX_POSTS'		=> isset($config['shout_max_posts']) ? (int)$config['shout_max_posts'] : '',
						'SHOUT_MAX_POSTS_ON'	=> isset($config['shout_max_posts_on']) ? (int)$config['shout_max_posts_on'] : '',
						'SHOUT_NON_IE_NR'		=> isset($config['shout_non_ie_nr']) ? (int)$config['shout_non_ie_nr'] : '',
						'SHOUT_IE_NR'			=> isset($config['shout_ie_nr']) ? (int)$config['shout_ie_nr'] : '',
						'SHOUT_HEIGHT'			=> isset($config['shout_height']) ? (int)$config['shout_height'] : '',
						'SHOUT_POS_SMIL'		=> isset($config['shout_pos_smil']) ? (((bool)$config['shout_pos_smil'] == 1) ? true : false) : '',
						'SHOUT_POS_RULES'		=> isset($config['shout_pos_rules']) ? (((bool)$config['shout_pos_rules'] == 1) ? true : false) : '',
						'SHOUT_POS_COLOR'		=> isset($config['shout_pos_color']) ? (((bool)$config['shout_pos_color'] == 1) ? true : false) : '',
						'SHOUT_POS_CHARS'		=> isset($config['shout_pos_chars']) ? (((bool)$config['shout_pos_chars'] == 1) ? true : false) : '',
						'SHOUT_ON_CRON'			=> isset($config['shout_on_cron']) ? (((bool)$config['shout_on_cron'] == 1) ? true : false) : '',
						'SHOUT_LOG_CRON'		=> isset($config['shout_log_cron']) ? (((bool)$config['shout_log_cron'] == 1) ? true : false) : '',
						'SHOUT_BACKGROUND'		=> isset($config['shout_color_background']) ? (string)$config['shout_color_background'] : '',
						'COLOR_IMAGE'			=> isset($config['shout_color_background']) ? (string)$color_path . $config['shout_color_background'] . '.gif' : '',
						'SHOUT_BACKGROUND_SUB'	=> isset($config['shout_color_background_sub']) ? (string)$config['shout_color_background_sub'] : '',
						'COLOR_IMAGE_SUB'		=> isset($config['shout_color_background_sub']) ? (string)$color_path . $config['shout_color_background_sub'] . '.gif' : '',
						'SHOUT_BUTTON'			=> isset($config['shout_button_background']) ? (((bool)$config['shout_button_background'] == 1) ? true : false) : '',
						'SHOUT_BAR_TOP'			=> isset($config['shout_bar_option']) ? (((bool)$config['shout_bar_option'] == 1) ? true : false) : '',
						'SHOUT_PAGIN_OPTION'	=> isset($config['shout_pagin_option']) ? (((bool)$config['shout_pagin_option'] == 1) ? true : false) : '',
						'OPTION'				=> $option,
						'OPTION_SUB'			=> $option_sub,
						'COLOR_PATH'			=> $color_path,
						'U_ACTION'				=> $this->u_action,
					));
				}
				
				$img_src = 'parametres.png';
				$template->assign_var('S_CONFIG_GEN', true);
			
			break;
			
			case 'private':
			    
				$action 		= request_var('action', '');
				$start			= request_var('start', 0);
				$marked			= request_var('mark', array(0));
				$deletemark 	= (!empty($_POST['delmarked'])) ? true : false;
				$deletemarklog 	= (!empty($_POST['delmarkedlog'])) ? true : false;

				if ($deletemark)
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						$del_acp = $ni = 0;

						if ($deletemark && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
								$nb_del_acp = $ni + ($del_acp + 1);
								$ni++;
							}
							
							$where_sql = ' WHERE ' . $db->sql_in_set('shout_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql)
						{
							$sql = 'DELETE FROM ' . SHOUTBOX_PRIV_TABLE . "
								$where_sql";
							$db->sql_query($sql);
							
							$log_entrie = 'LOG_SELECT' . (($nb_del_acp > 1) ? 'S' : '') . '_SHOUTBOX_PRIV';
							add_log('admin', $log_entrie, $nb_del_acp);
							set_config_count('shout_del_acp_priv', $nb_del_acp, true);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'		=> $start,
							'delmarked'	=> $deletemark,
							'mark'		=> $marked,
							'i'			=> $id,
							'mode'		=> $mode,
							'action'	=> $action))
						);
					}
				}
				
				if ($deletemarklog)
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						$del_acp = $ni = 0;

						if ($deletemarklog && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
								$nb_del_acp = $ni + ($del_acp + 1);
								$ni++;
							}
							
							$where_sql = ' WHERE ' . $db->sql_in_set('log_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql)
						{
							$sql = 'DELETE FROM ' . LOG_TABLE . "
								$where_sql";
							$db->sql_query($sql);
							
							$log_entrie = 'LOG_LOG' . (($nb_del_acp > 1) ? 'S' : '') . '_SHOUTBOX_PRIV';
							add_log('admin', $log_entrie, $nb_del_acp);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'			=> $start,
							'delmarkedlog'	=> $deletemarklog,
							'mark'			=> $marked,
							'i'				=> $id,
							'mode'			=> $mode,
							'action'		=> $action))
						);
					}
				}
				
				if ($action)
				{
					if (!confirm_box(true))
					{
						switch ($action)
						{
							default:
								$confirm = true;
								$confirm_lang = 'CONFIRM_OPERATION';
						}

						if ($confirm)
						{
							confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
								'i'			=> $id,
								'mode'		=> $mode,
								'action'	=> $action,
							)));
						}
					}
					else
					{
						switch ($action)
						{
						    case 'purge':
						        $sql = 'SELECT COUNT(shout_id) as nr 
									FROM ' . SHOUTBOX_PRIV_TABLE;
								$result = $db->sql_query($sql);
								$row = (int)$db->sql_fetchfield('nr');
								$db->sql_freeresult($result);
								
								$sql = 'DELETE FROM ' . SHOUTBOX_PRIV_TABLE;
						        $db->sql_query($sql);
								
								set_config_count('shout_del_purge_priv', $row, true);
								add_log('admin', 'LOG_PURGE_SHOUTBOX_PRIV');
								post_robot_shout(0, $user->ip, true, true, false);
							break;
							
							case 'purge_1':
							case 'purge_2':
							case 'purge_3':
							case 'purge_4':
							case 'purge_5':
							case 'purge_6':
							case 'purge_7':
							case 'purge_8':
								$sort = str_replace('purge_', '', $action);
								$retour = purge_shout_admin($sort, true);
								if ($retour)
								{
									adm_back_link($this->u_action);
								}
							break;
						}
					}
				}
				
				$sql_nr = 'SELECT COUNT(DISTINCT shout_id) as total 
					FROM ' . SHOUTBOX_PRIV_TABLE;
				$result_nr = $db->sql_query($sql_nr);
				$total_posts = $db->sql_fetchfield('total', $result_nr);
				$db->sql_freeresult($result_nr);
				
				$start = request_var('start', 0);
				$i = $start_log = $li = 0;
				$shout_number = $config['shout_nr_acp'];
				
				$sql = 'SELECT s.*, u.user_id, u.user_colour, u.username
					FROM ' . SHOUTBOX_PRIV_TABLE . ' AS s
						LEFT JOIN ' . USERS_TABLE . ' AS u ON s.shout_user_id = u.user_id 
					ORDER BY s.shout_id DESC';
				$result = $db->sql_query_limit($sql, $shout_number, $start);
				while ($row = $db->sql_fetchrow($result))
				{
					$row['user_colour'] = ($row['user_id'] == ROBOT) ? $config['shout_color_robot'] : $row['user_colour'];
					$row['username'] 	= ($row['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);
					$shout_text			= str_replace('./', generate_board_url() . '/', $row['shout_text']);
					
					$template->assign_block_vars('messages', array(
						'TIME'				=> $user->format_date($row['shout_time']),
						'POSTER'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
						'ID'				=> $row['shout_id'],
						'MESSAGE'			=> generate_text_for_display($shout_text, $row['shout_bbcode_uid'], $row['shout_bbcode_bitfield'], $row['shout_bbcode_flags']),
						'ROW_NUMBER'		=> $i + ($start + 1),
					));
					$i++;
				}
				$db->sql_freeresult($result);
				
				$sql_nr = 'SELECT COUNT(DISTINCT log_id) as totalog 
					FROM ' . LOG_TABLE . " 
					WHERE log_operation IN ('LOG_SHOUT_SCRIPT_PRIV', 'LOG_SHOUT_ACTIVEX_PRIV', 'LOG_SHOUT_APPLET_PRIV', 'LOG_SHOUT_OBJECTS_PRIV', 'LOG_SHOUT_IFRAME_PRIV')";
				$result_nr = $db->sql_query($sql_nr);
				$total_logs = $db->sql_fetchfield('totalog', $result_nr);
				$db->sql_freeresult($result_nr);
				
				$sql = 'SELECT l.log_id, l.user_id, l.log_type, l.log_ip, l.log_time, l.log_operation, l.reportee_id, u.user_id, u.user_colour, u.username
					FROM ' . LOG_TABLE . ' AS l
						LEFT JOIN ' . USERS_TABLE . " AS u ON l.user_id = u.user_id 
					WHERE log_operation IN ('LOG_SHOUT_SCRIPT_PRIV', 'LOG_SHOUT_ACTIVEX_PRIV', 'LOG_SHOUT_APPLET_PRIV', 'LOG_SHOUT_OBJECTS_PRIV', 'LOG_SHOUT_IFRAME_PRIV')
					ORDER BY log_time DESC";
				$result = $db->sql_query_limit($sql, $total_logs);
				while ($row = $db->sql_fetchrow($result))
				{
					$row['username'] = ($row['user_id'] == ANONYMOUS) ? $user->lang['GUEST'] : (($row['user_id'] == ROBOT) ? $user->lang['SHOUT_ROBOT'] : $row['username']);
					
					$template->assign_block_vars('logs', array(
						'TIME'				=> $user->format_date($row['log_time']),
						'REPORTEE'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
						'OPERATION'			=> $user->lang[$row['log_operation']],
						'ID'				=> $row['log_id'],
						'IP'				=> $row['log_ip'],
						'ROW_NUMBER'		=> $li + ($start_log + 1),
					));
					$li++;
				}
				$db->sql_freeresult($result);
				
				$img_src = 'stat.png';
				$total_del = $config['shout_del_acp_priv'] + $config['shout_del_auto_priv'] + $config['shout_del_purge_priv'] + $config['shout_del_user_priv'];
				$template->assign_vars(array(
					'S_PRIVATE'					=> true,
					'TOTAL_POSTS'				=> $total_posts,
					'S_DISPLAY_MESSAGES'		=> ($i > 0) ? true : false,
					'S_ON_PAGE'					=> ($total_posts > $shout_number) ? true : false,
					'TOTAL_MESSAGES'			=> ($total_posts > 1) ? sprintf($user->lang['NUMBER_MESSAGES'], $total_posts) : sprintf($user->lang['NUMBER_MESSAGE'], $total_posts),
					'MESSAGES_TOTAL_NR'			=> sprintf($user->lang['SHOUT_MESSAGES_TOTAL_NR'], $config['shout_nr_priv'], $user->format_date($config['shout_time_priv'])),
					'PAGE_NUMBER' 				=> on_page($total_posts, $shout_number, $start),	
					'PAGINATION' 				=> generate_pagination($this->u_action, $total_posts, $shout_number, $start),
					'LAST_SHOUT_RUN'			=> ($config['shout_last_run_priv'] == $config['shout_time_priv']) ? $user->lang['SHOUT_NEVER'] : $user->format_date($config['shout_last_run_priv']),
					'S_DISPLAY_LOGS'			=> ($li > 0) ? true : false,
					'LOGS_TOTAL_NR'				=> sprintf($user->lang['NUMBER_LOG' .(($config['shout_nr_log_priv'] > 1) ? 'S' : ''). '_TOTAL'], $config['shout_nr_log_priv'], $user->format_date($config['shout_time_priv'])),
					'MESSAGES_DEL_TOTAL'		=> sprintf($user->lang['SHOUT_DEL_NR' .(($total_del > 1) ? 'S' : ''). ''], $total_del) . $user->lang['SHOUT_DEL_TOTAL'],
					'MESSAGES_DEL_ACP'			=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_acp_priv'] > 1) ? 'S' : ''). ''], $config['shout_del_acp_priv']),
					'MESSAGES_DEL_AUTO'			=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_auto_priv'] > 1) ? 'S' : ''). ''], $config['shout_del_auto_priv']),
					'MESSAGES_DEL_PURGE'		=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_purge_priv'] > 1) ? 'S' : ''). ''], $config['shout_del_purge_priv']),
					'MESSAGES_DEL_USER'			=> sprintf($user->lang['SHOUT_DEL_NR' .(($config['shout_del_user_priv'] > 1) ? 'S' : ''). ''], $config['shout_del_user_priv']),
				));
			
			break;
			
			case 'config_priv':
			
				$update = isset($_POST['update']) ? true : false;
				
				if ($update)
				{
					$shout_title_priv				= utf8_normalize_nfc(request_var('shout_title_priv', 'shoutbox', true));
					$shout_width_post_priv			= request_var('shout_width_post_priv', 325);
					$shout_prune_priv				= request_var('shout_prune_priv', 0);
					$shout_on_cron_priv				= request_var('shout_on_cron_priv', 1);
					$shout_log_cron_priv			= request_var('shout_log_cron_priv', 0);
					$shout_max_posts_priv			= request_var('shout_max_posts_priv', 400);
					$shout_ie_nr_priv				= request_var('shout_ie_nr_priv', 20);
					$shout_max_posts_on_priv		= request_var('shout_max_posts_on_priv', 300);
					$shout_non_ie_height_priv		= request_var('shout_non_ie_height_priv', 460);
					$shout_non_ie_nr_priv			= request_var('shout_non_ie_nr_priv', 25);
					$shout_pos_smil_priv			= request_var('shout_pos_smil_priv', 1);
					$shout_pos_rules_priv			= request_var('shout_pos_rules_priv', 1);
					$shout_pos_color_priv			= request_var('shout_pos_color_priv', 1);
					$shout_pos_chars_priv			= request_var('shout_pos_chars_priv', 1);
					$shout_color_background_priv	= request_var('shout_color_background_priv', 'soft');
					$shout_color_background_sub_priv= request_var('shout_color_background_sub_priv', 'transparent');
					$shout_button_background_priv	= request_var('shout_button_background_priv', 1);
					$shout_bar_option_priv			= request_var('shout_bar_option_priv', 1);
					$shout_pagin_option_priv		= request_var('shout_pagin_option_priv', 0);

					$settings = array (
						'shout_title_priv'					=> $shout_title_priv,
						'shout_width_post_priv'				=> $shout_width_post_priv,
						'shout_prune_priv'					=> $shout_prune_priv,
						'shout_on_cron_priv'				=> $shout_on_cron_priv,
						'shout_log_cron_priv'				=> $shout_log_cron_priv,
						'shout_max_posts_priv'				=> $shout_max_posts_priv,
						'shout_ie_nr_priv'					=> $shout_ie_nr_priv,
						'shout_max_posts_on_priv'			=> $shout_max_posts_on_priv,
						'shout_non_ie_height_priv'			=> $shout_non_ie_height_priv,
						'shout_non_ie_nr_priv'				=> $shout_non_ie_nr_priv,
						'shout_pos_smil_priv'				=> $shout_pos_smil_priv,
						'shout_pos_rules_priv'				=> $shout_pos_rules_priv,
						'shout_pos_color_priv'				=> $shout_pos_color_priv,
						'shout_pos_chars_priv'				=> $shout_pos_chars_priv,
						'shout_color_background_priv'		=> $shout_color_background_priv,
						'shout_color_background_sub_priv'	=> $shout_color_background_sub_priv,
						'shout_button_background_priv'		=> $shout_button_background_priv,
						'shout_bar_option_priv'				=> $shout_bar_option_priv,
						'shout_pagin_option_priv'			=> $shout_pagin_option_priv,
					);

					foreach ($settings as $config_name => $config_value)
					{
						set_config($config_name, $config_value, false);
					}
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$sql_style = 'SELECT s.style_name, s.style_id, t.theme_path as theme
						FROM ' . STYLES_TABLE . ' AS s
						LEFT JOIN ' . STYLES_THEME_TABLE . ' AS t ON t.theme_id = s.theme_id
						WHERE style_id = ' .(int)$config['default_style'];
					$result_style = $db->sql_query($sql_style);
					$style = $db->sql_fetchfield('theme');
					$db->sql_freeresult($result_style);
					$color_path = $phpbb_root_path . 'styles/' .$style. '/theme/images/shout/fond/';
					$option = $option_sub = $selected = $selected_sub = '';
					
					$imglist = filelist($color_path);
					if (sizeof($imglist))
					{
						$imglist = array_values($imglist);
						$imglist = $imglist[0];

						foreach ($imglist as $key => $img)
						{
							$img_id = $img;
							$img = str_replace(array('.gif', '.jpg', '.jpeg', '.png', '.GIF', '.JPG', '.JPEG', '.PNG'), '', $img);
							if (isset($config['shout_color_background_priv']))
							{
								if (!$config['shout_color_background_priv'])
								{
									$no_image_select = true;
									$selected = '';
								}
								else
								{
									$selected = ($config['shout_color_background_priv'] == $img) ? ' selected="selected"' : '';
								}
							}
							else
							{
								$selected = '';
							}
							if (strlen($img) > 255)
							{
								continue;
							}
							$option .= '<option id="' . htmlspecialchars($img_id) . '" title="' . htmlspecialchars($img) . '" value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
						}
						foreach ($imglist as $key_sub => $img_sub)
						{
							$img_id_sub = $img_sub;
							$img_sub = str_replace(array('.gif', '.jpg', '.jpeg', '.png', '.GIF', '.JPG', '.JPEG', '.PNG'), '', $img_sub);
							if (isset($config['shout_color_background_sub_priv']))
							{
								if (!$config['shout_color_background_sub_priv'])
								{
									$no_image_select_sub = true;
									$selected_sub = '';
								}
								else
								{
									$selected_sub = ($config['shout_color_background_sub_priv'] == $img_sub) ? ' selected="selected"' : '';
								}
							}
							else
							{
								$selected_sub = '';
							}
							if (strlen($img_sub) > 255)
							{
								continue;
							}
							$option_sub .= '<option id="' . htmlspecialchars($img_id_sub) . '" title="' . htmlspecialchars($img_sub) . '" value="' . htmlspecialchars($img_sub) . '"' . $selected_sub . '>' . htmlspecialchars($img_sub) . '</option>';
						}
					}
					
					$template->assign_vars(array(
						'SHOUT_TITLE_PRIV'			=> isset($config['shout_title_priv']) ? utf8_normalize_nfc($config['shout_title_priv'], true) : '',
						'SHOUT_WIDTH_POST'			=> isset($config['shout_width_post_priv']) ? (int)$config['shout_width_post_priv'] : '',
						'SHOUT_PRUNE_PRIV'			=> isset($config['shout_prune_priv']) ? (int)$config['shout_prune_priv'] : '',
						'SHOUT_ON_CRON_PRIV'		=> isset($config['shout_on_cron_priv']) ? (((bool)$config['shout_on_cron_priv'] == 1) ? true : false) : '',
						'SHOUT_LOG_CRON_PRIV'		=> isset($config['shout_log_cron_priv']) ? (((bool)$config['shout_log_cron_priv'] == 1) ? true : false) : '',
						'SHOUT_MAX_POSTS'			=> isset($config['shout_max_posts_priv']) ? (int)$config['shout_max_posts_priv'] : '',
						'SHOUT_IE_NR_PRIV'			=> isset($config['shout_ie_nr_priv']) ? (int)$config['shout_ie_nr_priv'] : '',
						'SHOUT_MAX_POSTS_ON'		=> isset($config['shout_max_posts_on_priv']) ? (int)$config['shout_max_posts_on_priv'] : '',
						'SHOUT_NON_IE_HEIGHT_PRIV' 	=> isset($config['shout_non_ie_height_priv']) ? (int)$config['shout_non_ie_height_priv'] : '',
						'SHOUT_NON_IE_NR_PRIV'		=> isset($config['shout_non_ie_nr_priv']) ? (int)$config['shout_non_ie_nr_priv'] : '',
						'SHOUT_POS_SMIL'			=> isset($config['shout_pos_smil_priv']) ? (((bool)$config['shout_pos_smil_priv'] == 1) ? true : false) : '',
						'SHOUT_POS_RULES'			=> isset($config['shout_pos_rules_priv']) ? (((bool)$config['shout_pos_rules_priv'] == 1) ? true : false) : '',
						'SHOUT_POS_COLOR'			=> isset($config['shout_pos_color_priv']) ? (((bool)$config['shout_pos_color_priv'] == 1) ? true : false) : '',
						'SHOUT_POS_CHARS'			=> isset($config['shout_pos_chars_priv']) ? (((bool)$config['shout_pos_chars_priv'] == 1) ? true : false) : '',
						'SHOUT_BACKGROUND'			=> isset($config['shout_color_background_priv']) ? (string)$config['shout_color_background_priv'] : '',
						'COLOR_IMAGE'				=> isset($config['shout_color_background_priv']) ? (string)$color_path . $config['shout_color_background_priv'] . '.gif' : '',
						'SHOUT_BACKGROUND_SUB'		=> isset($config['shout_color_background_sub_priv']) ? (string)$config['shout_color_background_sub_priv'] : '',
						'COLOR_IMAGE_SUB'			=> isset($config['shout_color_background_sub_priv']) ? (string)$color_path . $config['shout_color_background_sub_priv'] . '.gif' : '',
						'SHOUT_BUTTON'				=> isset($config['shout_button_background_priv']) ? (((bool)$config['shout_button_background_priv'] == 1) ? true : false) : '',
						'SHOUT_BAR_TOP'				=> isset($config['shout_bar_option_priv']) ? (((bool)$config['shout_bar_option_priv'] == 1) ? true : false) : '',
						'SHOUT_PAGIN_OPTION'		=> isset($config['shout_pagin_option_priv']) ? (((bool)$config['shout_pagin_option_priv'] == 1) ? true : false) : '',
						'OPTION'					=> $option,
						'OPTION_SUB'				=> $option_sub,
						'COLOR_PATH'				=> $color_path,
						'U_ACTION'					=> $this->u_action,
					));
				}
				
				$img_src = 'parametres.png';
				$template->assign_var('S_PRIV_CONFIG', true);
			
			break;
			
			case 'popup':
			
				$update = isset($_POST['update']) ? true : false;
				
				if ($update)
				{
					$shout_width_post_pop			= request_var('shout_width_post_pop', 325);
					$shout_non_ie_height_pop		= request_var('shout_non_ie_height_pop', 460);
					$shout_ie_nr_pop				= request_var('shout_ie_nr_pop', 20);
					$shout_non_ie_nr_pop			= request_var('shout_non_ie_nr_pop', 25);
					$shout_popup_height				= request_var('shout_popup_height', 580);
					$shout_popup_width				= request_var('shout_popup_width', 1100);
					$shout_pos_smil_pop				= request_var('shout_pos_smil_pop', 1);
					$shout_pos_rules_pop			= request_var('shout_pos_rules_pop', 1);
					$shout_pos_color_pop			= request_var('shout_pos_color_pop', 1);
					$shout_pos_chars_pop			= request_var('shout_pos_chars_pop', 1);
					$shout_color_background_pop		= request_var('shout_color_background_pop', 'blue');
					$shout_color_background_sub_pop = request_var('shout_color_background_sub_pop', 'transparent');
					$shout_button_background_pop	= request_var('shout_button_background_pop', 1);
					$shout_bar_option_pop			= request_var('shout_bar_option_pop', 1);
					$shout_pagin_option_pop			= request_var('shout_pagin_option_pop', 0);

					$settings = array(
						'shout_width_post_pop'			=> $shout_width_post_pop,
						'shout_non_ie_height_pop'		=> $shout_non_ie_height_pop,
						'shout_ie_nr_pop'				=> $shout_ie_nr_pop,
						'shout_non_ie_nr_pop'			=> $shout_non_ie_nr_pop,
						'shout_popup_height'			=> $shout_popup_height,
						'shout_popup_width'				=> $shout_popup_width,
						'shout_pos_smil_pop'			=> $shout_pos_smil_pop,
						'shout_pos_rules_pop'			=> $shout_pos_rules_pop,
						'shout_pos_color_pop'			=> $shout_pos_color_pop,
						'shout_pos_chars_pop'			=> $shout_pos_chars_pop,
						'shout_color_background_pop'	=> $shout_color_background_pop,
						'shout_color_background_sub_pop'=> $shout_color_background_sub_pop,
						'shout_button_background_pop'	=> $shout_button_background_pop,
						'shout_bar_option_pop'			=> $shout_bar_option_pop,
						'shout_pagin_option_pop'		=> $shout_pagin_option_pop,
					);

					foreach ($settings as $config_name => $config_value)
					{
						set_config($config_name, $config_value, false);
					}
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$sql_style = 'SELECT s.style_name, s.style_id, t.theme_path as theme
						FROM ' . STYLES_TABLE . ' AS s
						LEFT JOIN ' . STYLES_THEME_TABLE . ' AS t ON t.theme_id = s.theme_id
						WHERE style_id = ' .(int)$config['default_style'];
					$result_style = $db->sql_query($sql_style);
					$style = $db->sql_fetchfield('theme');
					$db->sql_freeresult($result_style);
					$color_path = $phpbb_root_path . 'styles/' .$style. '/theme/images/shout/fond/';
					$option = $option_sub = $selected = $selected_sub = '';
					
					$imglist = @filelist($color_path);
					if (sizeof($imglist))
					{
						$imglist = array_values($imglist);
						$imglist = $imglist[0];

						foreach ($imglist as $key => $img)
						{
							$img_id = $img;
							$img = str_replace(array('.gif', '.jpg', '.jpeg', '.png', '.GIF', '.JPG', '.JPEG', '.PNG'), '', $img);
							if (isset($config['shout_color_background_pop']))
							{
								if (!$config['shout_color_background_pop'])
								{
									$no_image_select = true;
									$selected = '';
								}
								else
								{
									$selected = ($config['shout_color_background_pop'] == $img) ? ' selected="selected"' : '';
								}
							}
							else
							{
								$selected = '';
							}
							if (strlen($img) > 255)
							{
								continue;
							}
							$option .= '<option id="' . htmlspecialchars($img_id) . '" title="' . htmlspecialchars($img) . '" value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
						}
						foreach ($imglist as $key_sub => $img_sub)
						{
							$img_id_sub = $img_sub;
							$img_sub = str_replace(array('.gif', '.jpg', '.jpeg', '.png', '.GIF', '.JPG', '.JPEG', '.PNG'), '', $img_sub);
							if (isset($config['shout_color_background_sub_pop']))
							{
								if (!$config['shout_color_background_sub_pop'])
								{
									$no_image_select_sub = true;
									$selected_sub = '';
								}
								else
								{
									$selected_sub = ($config['shout_color_background_sub_pop'] == $img_sub) ? ' selected="selected"' : '';
								}
							}
							else
							{
								$selected_sub = '';
							}
							if (strlen($img_sub) > 255)
							{
								continue;
							}
							$option_sub .= '<option id="' . htmlspecialchars($img_id_sub) . '" title="' . htmlspecialchars($img_sub) . '" value="' . htmlspecialchars($img_sub) . '"' . $selected_sub . '>' . htmlspecialchars($img_sub) . '</option>';
						}
					}
					
					$template->assign_vars(array(
						'SHOUT_WIDTH_POST'			=> isset($config['shout_width_post_pop']) ? (int)$config['shout_width_post_pop'] : '',
						'SHOUT_NON_IE_HEIGHT_POP' 	=> isset($config['shout_non_ie_height_pop']) ? (int)$config['shout_non_ie_height_pop'] : '',
						'SHOUT_IE_NR_POP'			=> isset($config['shout_ie_nr_pop']) ? (int)$config['shout_ie_nr_pop'] : '',
						'SHOUT_NON_IE_NR_POP'		=> isset($config['shout_non_ie_nr_pop']) ? (int)$config['shout_non_ie_nr_pop'] : '',
						'SHOUT_HEIGHT_POP'			=> isset($config['shout_popup_height']) ? (int)$config['shout_popup_height'] : '',
						'SHOUT_WIDTH_POP'			=> isset($config['shout_popup_width']) ? (int)$config['shout_popup_width'] : '',
						'SHOUT_POS_SMIL'			=> isset($config['shout_pos_smil_pop']) ? (((bool)$config['shout_pos_smil_pop'] == 1) ? true : false) : '',
						'SHOUT_POS_RULES'			=> isset($config['shout_pos_rules_pop']) ? (((bool)$config['shout_pos_rules_pop'] == 1) ? true : false) : '',
						'SHOUT_POS_COLOR'			=> isset($config['shout_pos_color_pop']) ? (((bool)$config['shout_pos_color_pop'] == 1) ? true : false) : '',
						'SHOUT_POS_CHARS'			=> isset($config['shout_pos_chars_pop']) ? (((bool)$config['shout_pos_chars_pop'] == 1) ? true : false) : '',
						'SHOUT_BACKGROUND'			=> isset($config['shout_color_background_pop']) ? (string)$config['shout_color_background_pop'] : '',
						'COLOR_IMAGE'				=> isset($config['shout_color_background_pop']) ? (string)$color_path . $config['shout_color_background_pop'] . '.gif' : '',
						'SHOUT_BACKGROUND_SUB'		=> isset($config['shout_color_background_sub_pop']) ? (string)$config['shout_color_background_sub_pop'] : '',
						'COLOR_IMAGE_SUB'			=> isset($config['shout_color_background_sub_pop']) ? (string)$color_path . $config['shout_color_background_sub_pop'] . '.gif' : '',
						'SHOUT_BUTTON'				=> isset($config['shout_button_background_pop']) ? (((bool)$config['shout_button_background_pop'] == 1) ? true : false) : '',
						'SHOUT_BAR_TOP'				=> isset($config['shout_bar_option_pop']) ? (((bool)$config['shout_bar_option_pop'] == 1) ? true : false) : '',
						'SHOUT_PAGIN_OPTION'		=> isset($config['shout_pagin_option_pop']) ? (((bool)$config['shout_pagin_option_pop'] == 1) ? true : false) : '',
						'OPTION'					=> $option,
						'OPTION_SUB'				=> $option_sub,
						'COLOR_PATH'				=> $color_path,
						'U_ACTION'					=> $this->u_action,
					));
				}
				
				$img_src = 'parametres.png';
				$template->assign_var('S_POPUP', true);
			
			break;
			
			case 'smilies':
				
				$update 	= isset($_POST['update']) ? true : false;
				$start		= request_var('start', 0);
				$smiley		= request_var('smiley', -1);
				$display	= request_var('display', -1);
				$marked		= request_var('mark', array(0));
				$activer 	= (!empty($_POST['activer'])) ? true : false;
				$desactiver = (!empty($_POST['desactiver'])) ? true : false;
				
				if ($activer)
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						$del_acp = $ni = 0;

						if ($activer && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
								$nb_del_acp = $ni + ($del_acp + 1);
								$ni++;
							}
							
							$where_sql = ' WHERE ' . $db->sql_in_set('smiley_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql)
						{
							$sql = 'UPDATE ' . SMILIES_TABLE . " SET display_on_shout = 1 $where_sql";
							$db->sql_query($sql);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'			=> $start,
							'activer'		=> $activer,
							'mark'			=> $marked,
							'i'				=> $id,
							'mode'			=> $mode,
							'action'		=> $action))
						);
					}
				}
				if ($desactiver)
				{
					if (confirm_box(true))
					{
						$where_sql = '';
						$del_acp = $ni = 0;

						if ($desactiver && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
								$nb_del_acp = $ni + ($del_acp + 1);
								$ni++;
							}
							
							$where_sql = ' WHERE ' . $db->sql_in_set('smiley_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql)
						{
							$sql = 'UPDATE ' . SMILIES_TABLE . " SET display_on_shout = 0 $where_sql";
							$db->sql_query($sql);
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'			=> $start,
							'desactiver'	=> $desactiver,
							'mark'			=> $marked,
							'i'				=> $id,
							'mode'			=> $mode,
							'action'		=> $action))
						);
					}
				}
				
				$smilies_path = $config['smilies_path'];
				$i = 0;
				
				if ($update)
				{
					$smiley_id	= request_var('smiley', array(0));
					$settings 	= array('smiley_id'	=> $smiley_id,);

					foreach ($settings as $key => $value)
					{
						$sql_ary = array('shout_order' => $value);
						$db->sql_query('UPDATE ' . SMILIES_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE smiley_id = " .(int)$smiley_id);
					}
					
					$cache->destroy('sql', SMILIES_TABLE);
					add_log('admin', 'LOG_SHOUT_SMILIES');
					trigger_error($user->lang['CONFIG_SMILIES_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$sql = 'SELECT COUNT(smiley_id) AS total
						FROM ' . SMILIES_TABLE;
					$result = $db->sql_query($sql);
					$total_i = (int) $db->sql_fetchfield('total');
					$db->sql_freeresult($result);
					
					$sql = 'SELECT *
						FROM ' . SMILIES_TABLE . '
						GROUP BY smiley_url
						ORDER BY smiley_order';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('smilies', array(
							'S_SMILEY_URL'		=> '<img src ="' . $phpbb_root_path . $smilies_path . '/' . $row['smiley_url'] . '" alt="' . $row['emotion'] . '" title="' . $row['emotion'] . '" width="' . $row['smiley_width'] . '" />',
							'ID'				=> $row['smiley_id'],
							'CODE'				=> $row['code'],
							'EMOTION'			=> $row['emotion'],
							'SMILEY_WIDTH'		=> $row['smiley_width'],
							'SMILEY_HEIGHT'		=> $row['smiley_height'],
							'SMILEY_DISPLAYED'	=> ($row['display_on_shout']) ? '<b><span style="color: green;" title="' . $user->lang['SMILIES_CLIC_NO'] . '">' . $user->lang['SMILIES_DISPLAYED'] . '</span></b>' : '<b><span style="color: red;" title="' . $user->lang['SMILIES_CLIC_YES'] . '">' . $user->lang['SMILIES_NO_DISPLAYED'] . '</span></b>',
							'U_DISPLAYED'		=> $this->u_action . '&amp;smiley=' .$row['smiley_id']. '&amp;display=' .$row['display_on_shout'],
							'ORDER'				=> $row['smiley_order'],
							'FIRST_SMILIE'		=> ($i == 1) ? true : false,
							'LAST_SMILIE'		=> ($i == $total_i) ? true : false,
							'ROW_NUMBER'		=> $i + 1,
						));
						$i++;
					}
					$db->sql_freeresult($result);

					$sql_d = 'SELECT *
						FROM ' . SMILIES_TABLE . '
						WHERE display_on_shout = 1
						GROUP BY smiley_url
						ORDER BY smiley_order ASC';
					$result_d = $db->sql_query($sql_d);
					while ($row_d = $db->sql_fetchrow($result_d))
					{
						$template->assign_block_vars('smilies_shout', array(
							'S_SMILEY_URL'	=> '<img src ="' . $phpbb_root_path . $smilies_path . '/' . $row_d['smiley_url'] . '" alt="' . $row_d['emotion'] . '" title="' . $row_d['emotion'] . '" width="' . $row_d['smiley_width'] . '" />',
						));
					}
					$db->sql_freeresult($result_d);

					$sql_s = 'SELECT *
						FROM ' . SMILIES_TABLE . '
						WHERE display_on_shout = 0
						GROUP BY smiley_url
						ORDER BY smiley_order ASC';
					$result_s = $db->sql_query($sql_s);
					while ($row_s = $db->sql_fetchrow($result_s))
					{
						$template->assign_block_vars('smilies_popup', array(
							'S_SMILEY_URL'	=> '<img src ="' . $phpbb_root_path . $smilies_path . '/' . $row_s['smiley_url'] . '" alt="' . $row_s['emotion'] . '" title="' . $row_s['emotion'] . '" width="' . $row_s['smiley_width'] . '" />',
						));
					}
					$db->sql_freeresult($result_s);

					$template->assign_var('U_ACTION', $this->u_action);
					
					if ($display == 1)
					{
						$sql = "UPDATE " . SMILIES_TABLE . " SET display_on_shout = 0 WHERE smiley_id = " .(int)$smiley;
						$db->sql_query($sql);
						
						$cache->destroy('sql', SMILIES_TABLE);
						meta_refresh(0, $this->u_action. '#smiley_' .$smiley);
					}
					elseif ($display == 0)
					{
						$sql = "UPDATE " . SMILIES_TABLE . " SET display_on_shout = 1 WHERE smiley_id = " .(int)$smiley;
						$db->sql_query($sql);
						
						$cache->destroy('sql', SMILIES_TABLE);
						meta_refresh(0, $this->u_action. '#smiley_' .$smiley);
					}
				}
				
				$img_src = 'casque.png';
				$lien_title = append_sid("{$phpbb_admin_path}index.{$phpEx}", "i=icons&amp;mode=smilies");
				$template->assign_vars(array(
					'S_SMILIES'					=> true,
					'ICON_MOVE_UP'				=> '<img src="' . $phpbb_admin_path . 'images/icon_up.gif" alt="' . $user->lang['MOVE_UP'] . '" title="' . $user->lang['MOVE_UP'] . '" />',
					'ICON_MOVE_UP_DISABLED'		=> '<img src="' . $phpbb_admin_path . 'images/icon_up_disabled.gif" alt="' . $user->lang['MOVE_UP'] . '" title="' . $user->lang['MOVE_UP'] . '" />',
					'ICON_MOVE_DOWN'			=> '<img src="' . $phpbb_admin_path . 'images/icon_down.gif" alt="' . $user->lang['MOVE_DOWN'] . '" title="' . $user->lang['MOVE_DOWN'] . '" />',
					'ICON_MOVE_DOWN_DISABLED'	=> '<img src="' . $phpbb_admin_path . 'images/icon_down_disabled.gif" alt="' . $user->lang['MOVE_DOWN'] . '" title="' . $user->lang['MOVE_DOWN'] . '" />',
				));
				
			break;
			
			case 'robot':
			
				$update = isset($_POST['update']) ? true : false;
				
				if ($update)
				{
					$shout_robot_choice				= request_var('shout_robot_choice', array(0));
					$shout_enable_robot				= request_var('shout_enable_robot', 1);
					$shout_post_robot				= request_var('shout_post_robot', 1);
					$shout_rep_robot				= request_var('shout_rep_robot', 1);
					$shout_edit_robot				= request_var('shout_edit_robot', 1);
					$shout_post_robot_priv			= request_var('shout_post_robot_priv', 1);
					$shout_rep_robot_priv			= request_var('shout_rep_robot_priv', 1);
					$shout_edit_robot_priv			= request_var('shout_edit_robot_priv', 1);
					$shout_prez_form				= request_var('shout_prez_form', '');
					$shout_exclude_forums			= request_var('shout_exclude_forums', '');
					$shout_color_robot				= request_var('shout_color_robot', '');
					$shout_color_message			= request_var('shout_color_message', '');
					$shout_delete_robot				= request_var('shout_delete_robot', 1);
					$shout_sessions					= request_var('shout_sessions', 1);
					$shout_sessions_priv			= request_var('shout_sessions_priv', 0);
					$shout_sessions_bots			= request_var('shout_sessions_bots', 0);
					$shout_sessions_bots_priv		= request_var('shout_sessions_bots_priv', 0);
					$shout_hello					= request_var('shout_hello', 1);
					$shout_hello_priv				= request_var('shout_hello_priv', 1);
					$shout_hello_hour				= request_var('shout_hello_hour', 08);
					$shout_newest					= request_var('shout_newest', 1);
					$shout_newest_priv				= request_var('shout_newest_priv', 1);
					$shout_birthday					= request_var('shout_birthday', 1);
					$shout_birthday_priv			= request_var('shout_birthday_priv', 1);
					$shout_birthday_hour			= request_var('shout_birthday_hour', 08);
					
					$in = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
					$out = array('01', '02', '03', '04', '05', '06', '07', '08', '09');
					if ($shout_hello_hour < 10 && $shout_hello_hour != '00')
					{
						$shout_hello_hour = str_replace($in, $out, $shout_hello_hour);
					}
					elseif ($shout_hello_hour == '0')
					{
						$shout_hello_hour = '00';
					}
					if ($shout_birthday_hour < 10 && $shout_birthday_hour != '00')
					{
						$shout_birthday_hour = str_replace($in, $out, $shout_birthday_hour);
					}
					elseif ($shout_birthday_hour == '0')
					{
						$shout_birthday_hour = '00';
					}

					$settings = array (
						'shout_enable_robot'		=> $shout_enable_robot,
						'shout_post_robot'			=> $shout_post_robot,
						'shout_rep_robot'			=> $shout_rep_robot,
						'shout_edit_robot'			=> $shout_edit_robot,
						'shout_post_robot_priv'		=> $shout_post_robot_priv,
						'shout_rep_robot_priv'		=> $shout_rep_robot_priv,
						'shout_edit_robot_priv'		=> $shout_edit_robot_priv,
						'shout_prez_form'			=> $shout_prez_form,
						'shout_exclude_forums'		=> $shout_exclude_forums,
						'shout_color_robot'			=> $shout_color_robot,
						'shout_color_message'		=> $shout_color_message,
						'shout_delete_robot'		=> $shout_delete_robot,
						'shout_sessions'			=> $shout_sessions,
						'shout_sessions_priv'		=> $shout_sessions_priv,
						'shout_sessions_bots'		=> $shout_sessions_bots,
						'shout_sessions_bots_priv'	=> $shout_sessions_bots_priv,
						'shout_hello'				=> $shout_hello,
						'shout_hello_priv'			=> $shout_hello_priv,
						'shout_hello_hour'			=> $shout_hello_hour,
						'shout_newest'				=> $shout_newest,
						'shout_newest_priv'			=> $shout_newest_priv,
						'shout_birthday'			=> $shout_birthday,
						'shout_birthday_priv'		=> $shout_birthday_priv,
						'shout_birthday_hour'		=> $shout_birthday_hour,
						'shout_robot_choice'		=> implode(', ', $shout_robot_choice),
					);

					foreach ($settings as $config_name => $config_value)
					{
						set_config($config_name, $config_value, false);
					}
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$template->assign_vars(array(
						'SHOUT_ENABLE_ROBOT'	=> isset($config['shout_enable_robot']) ? 	(bool)$config['shout_enable_robot'] : '',
						'SHOUT_POST_ROBOT'		=> isset($config['shout_post_robot']) ? 	(bool)$config['shout_post_robot'] : '',
						'SHOUT_REP_ROBOT'		=> isset($config['shout_rep_robot']) ? 		(bool)$config['shout_rep_robot'] : '',
						'SHOUT_EDIT_ROBOT'		=> isset($config['shout_edit_robot']) ? 	(bool)$config['shout_edit_robot'] : '',
						'SHOUT_POST_ROBOT_PRIV' => isset($config['shout_post_robot_priv']) ? (bool)$config['shout_post_robot_priv'] : '',
						'SHOUT_REP_ROBOT_PRIV'	=> isset($config['shout_rep_robot_priv']) ? (bool)$config['shout_rep_robot_priv'] : '',
						'SHOUT_EDIT_ROBOT_PRIV'	=> isset($config['shout_edit_robot_priv']) ? (bool)$config['shout_edit_robot_priv'] : '',
						'SHOUT_PREZ_FORM'		=> isset($config['shout_prez_form']) ? 		(int)$config['shout_prez_form'] : '',
						'SHOUT_EXCLUDE_FORUMS'	=> isset($config['shout_exclude_forums']) ? (int)$config['shout_exclude_forums'] : '',
						'SHOUT_COLOR_ROBOT'		=> isset($config['shout_color_robot']) ? 	(string)$config['shout_color_robot'] : '',
						'SHOUT_COLOR_MESSAGE'	=> isset($config['shout_color_message']) ? 	(string)$config['shout_color_message'] : '',
						'SHOUT_DELETE_ROBOT'	=> isset($config['shout_delete_robot']) ? 	(bool)$config['shout_delete_robot'] : '',
						'SHOUT_SESSIONS'		=> isset($config['shout_sessions']) ? 		(bool)$config['shout_sessions'] : '',
						'SHOUT_SESSIONS_PRIV'	=> isset($config['shout_sessions_priv']) ? 	(bool)$config['shout_sessions_priv'] : '',
						'SHOUT_SESSIONS_BOTS'	=> isset($config['shout_sessions_bots']) ? 	(bool)$config['shout_sessions_bots'] : '',
						'SHOUT_SESSIONS_BOTS_PRIV'=> isset($config['shout_sessions_bots_priv']) ? (bool)$config['shout_sessions_bots_priv'] : '',
						'SHOUT_HELLO'			=> isset($config['shout_hello']) ? 			(bool)$config['shout_hello'] : '',
						'SHOUT_HELLO_PRIV'		=> isset($config['shout_hello_priv']) ? 	(bool)$config['shout_hello_priv'] : '',
						'SHOUT_HELLO_HOUR'		=> isset($config['shout_hello_hour']) ? 	(int)$config['shout_hello_hour'] : '',
						'SHOUT_NEWEST'			=> isset($config['shout_newest']) ? 		(bool)$config['shout_newest'] : '',
						'SHOUT_NEWEST_PRIV'		=> isset($config['shout_newest_priv']) ? 	(bool)$config['shout_newest_priv'] : '',
						'SHOUT_BIRTHDAY'		=> isset($config['shout_birthday']) ? 		(bool)$config['shout_birthday'] : '',
						'SHOUT_BIRTHDAY_PRIV'	=> isset($config['shout_birthday_priv']) ? 	(bool)$config['shout_birthday_priv'] : '',
						'SHOUT_BIRTHDAY_HOUR'	=> isset($config['shout_birthday_hour']) ? 	(int)$config['shout_birthday_hour'] : '',
						'CHOICE_1'				=> isset($config['shout_robot_choice']) ? ((preg_match('/1/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_2'				=> isset($config['shout_robot_choice']) ? ((preg_match('/2/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_3'				=> isset($config['shout_robot_choice']) ? ((preg_match('/3/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_4'				=> isset($config['shout_robot_choice']) ? ((preg_match('/4/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_5'				=> isset($config['shout_robot_choice']) ? ((preg_match('/5/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_6'				=> isset($config['shout_robot_choice']) ? ((preg_match('/6/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_7'				=> isset($config['shout_robot_choice']) ? ((preg_match('/7/i', $config['shout_robot_choice'])) ? true : false) : false,
						'CHOICE_8'				=> isset($config['shout_robot_choice']) ? ((preg_match('/8/i', $config['shout_robot_choice'])) ? true : false) : false,
						'SERVER_HOUR'			=> sprintf($user->lang['SHOUT_SERVER_HOUR' .((date('H') > 1) ? 'S' : '')], date('H'), date('i')),
						'U_SWATCH'				=> append_sid("swatch.php", 'form=shout_robot&amp;name=shout_color_robot'),
						'U_SWATCH_2'			=> append_sid("swatch.php", 'form=shout_robot&amp;name=shout_color_message'),
					));
				}
				
				$img_src = 'automator.png';
				$template->assign_var('S_ROBOT', true);
			
			break;
			
			case 'robot_mod':
			
				$update = isset($_POST['update']) ? true : false;
				
				if ($update)
				{
					$shout_robbery					= request_var('shout_robbery', 1);
					$shout_lottery					= request_var('shout_lottery', 1);
					$shout_robbery_priv				= request_var('shout_robbery_priv', 1);
					$shout_lottery_priv				= request_var('shout_lottery_priv', 1);
					$shout_hangman					= request_var('shout_hangman', 1);
					$shout_hangman_priv				= request_var('shout_hangman_priv', 1);
					$shout_tracker					= request_var('shout_tracker', 1);
					$shout_tracker_rep				= request_var('shout_tracker_rep', 1);
					$shout_tracker_edit				= request_var('shout_tracker_edit', 1);
					$shout_tracker_priv				= request_var('shout_tracker_priv', 1);
					$shout_tracker_rep_priv			= request_var('shout_tracker_rep_priv', 1);
					$shout_tracker_edit_priv		= request_var('shout_tracker_edit_priv', 1);

					$settings = array (
						'shout_robbery'				=> $shout_robbery,
						'shout_lottery'				=> $shout_lottery,
						'shout_robbery_priv'		=> $shout_robbery_priv,
						'shout_lottery_priv'		=> $shout_lottery_priv,
						'shout_hangman'				=> $shout_hangman,
						'shout_hangman_priv'		=> $shout_hangman_priv,
						'shout_tracker'				=> $shout_tracker,
						'shout_tracker_rep'			=> $shout_tracker_rep,
						'shout_tracker_edit'		=> $shout_tracker_edit,
						'shout_tracker_priv'		=> $shout_tracker_priv,
						'shout_tracker_rep_priv'	=> $shout_tracker_rep_priv,
						'shout_tracker_edit_priv'	=> $shout_tracker_edit_priv,
					);

					foreach ($settings as $config_name => $config_value)
					{
						set_config($config_name, $config_value, false);
					}
					
					add_log('admin', 'LOG_SHOUT_' . strtoupper($mode));
					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}
				else
				{
					$_points = file_exists($phpbb_root_path . 'points.' .$phpEx) ? true : false;
					$_points_version = isset($config['ultimate_points_version']) ? $config['ultimate_points_version'] : false;
					$_hangman = file_exists($phpbb_root_path . 'hangman.' .$phpEx) ? true : false;
					$_hangman_version = isset($config['hangman_version']) ? $config['hangman_version'] : false;
					$_tracker = file_exists($phpbb_root_path . 'tracker.' .$phpEx) ? true : false;
					$_tracker_version = false;
					set_shout_value();
					if ($_tracker)
					{
						include($phpbb_root_path . 'includes/tracker/tracker_constants.' . $phpEx);
						$sql = 'SELECT config_value FROM ' . TRACKER_CONFIG_TABLE . " WHERE config_name = 'version'";
						$result = $db->sql_query($sql);
						$_tracker_version = $db->sql_fetchfield('config_value', $result);
						$db->sql_freeresult($result);
					}
					$all_false = (!$_points && !$_hangman && !$_tracker) ? true : false;

					$info = get_remote_file('breizh-portal.com', '/updatecheck/', 'robot_mods', $errstr, $errno);
					if ($info === false)
					{
						$template->assign_vars(array(
							'S_ERROR'   => true,
							'ERROR_MSG' => sprintf($user->lang['UNABLE_CONNECT'], $errstr),
						));
					}
					else
					{
						$active_fopen = false;
						if (@ini_get('allow_url_fopen') == '0' || strtolower(@ini_get('allow_url_fopen')) == 'off') // If allow_url_fopen is disabled
						{
							$active_fopen = true;
						}
						if ($active_fopen)
						{
							@ini_set("allow_url_fopen", 1); // To enable allow_url_fopen
						}
						$file = @file_get_contents('http://breizh-portal.com/updatecheck/robot_mods.txt');
						$lines = str_replace(array("\n", '@', '!', '*', '#'), array('<br />', $user->lang['SHOUT_FILE'], $user->lang['SHOUT_FILE_OK'], $user->lang['SHOUT_FILE_LIEN'], $user->lang['SHOUT_FILE_GO']), $file);
						$template->assign_vars(array(
							'S_ERROR'   => false,
							'LINES'		=> ($lines) ? $lines : sprintf($user->lang['UNABLE_CONNECT'], 'allow_url_fopen = 0'),
						));
						if ($active_fopen)
						{
							@ini_set("allow_url_fopen", 0); // To disable allow_url_fopen
						}
					}
					$template->assign_vars(array(
						'SHOUT_ROBBERY'			=> isset($config['shout_robbery']) ? 		(bool)$config['shout_robbery'] : '',
						'SHOUT_LOTTERY'			=> isset($config['shout_lottery']) ? 		(bool)$config['shout_lottery'] : '',
						'SHOUT_ROBBERY_PRIV'	=> isset($config['shout_robbery_priv']) ? 	(bool)$config['shout_robbery_priv'] : '',
						'SHOUT_LOTTERY_PRIV'	=> isset($config['shout_lottery_priv']) ? 	(bool)$config['shout_lottery_priv'] : '',
						'SHOUT_HANGMAN'			=> isset($config['shout_hangman']) ? 		(bool)$config['shout_hangman'] : '',
						'SHOUT_HANGMAN_PRIV'	=> isset($config['shout_hangman_priv']) ? 	(bool)$config['shout_hangman_priv'] : '',
						'SHOUT_TRACKER'			=> isset($config['shout_tracker']) ? 		(bool)$config['shout_tracker'] : '',
						'SHOUT_TRACKER_REP'		=> isset($config['shout_tracker_rep']) ? 	(bool)$config['shout_tracker_rep'] : '',
						'SHOUT_TRACKER_EDIT'	=> isset($config['shout_tracker_edit']) ? 	(bool)$config['shout_tracker_edit'] : '',
						'SHOUT_TRACKER_PRIV'	=> isset($config['shout_tracker_priv']) ? 	(bool)$config['shout_tracker_priv'] : '',
						'SHOUT_TRACKER_REP_PRIV'=> isset($config['shout_tracker_rep_priv']) ? (bool)$config['shout_tracker_rep_priv'] : '',
						'SHOUT_TRACKER_EDIT_PRIV'=> isset($config['shout_tracker_edit_priv']) ? (bool)$config['shout_tracker_edit_priv'] : '',
						'IS_ULTIMATE'			=> $_points,
						'ULTIMATE_VERSION'		=> $_points_version,
						'IS_HANGMAN'			=> $_hangman,
						'HANGMAN_VERSION'		=> $_hangman_version,
						'IS_TRACKER'			=> $_tracker,
						'TRACKER_VERSION'		=> $_tracker_version,
						'ALL_FALSE'				=> $all_false,
					));
				}
				
				$img_src = 'robot.png';
				$template->assign_var('S_ROBOT_MOD', true);
			
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
		
		$template->assign_vars(array(
			'U_ACTION'			=> append_sid($this->u_action),
			'SEPARATION'		=> '- - - - - - - - - - - -',
			'SHOUTBOX_VERSION'	=> sprintf($user->lang['SHOUTBOX_VERSION_ACP_COPY'], $config['shout_version']),
			'TITLE'				=> $user->lang['ACP_SHOUT_' . strtoupper($mode) . '_T'],
			'TITLE_EXPLAIN'		=> ($mode != 'smilies') ? $user->lang['ACP_SHOUT_' . strtoupper($mode) . '_T_EXPLAIN'] : sprintf($user->lang['ACP_SHOUT_SMILIES_T_EXPLAIN'], $lien_title),
			'IMAGE_TITLE'		=> '<img src="' .$phpbb_root_path. 'images/shoutbox/' .$img_src. '" title="' .$user->lang['ACP_SHOUT_' . strtoupper($mode) . '_T']. '" alt="' .$user->lang['ACP_SHOUT_' . strtoupper($mode) . '_T']. '" /> ',
			'IMAGE_SUBMIT'		=> '<img src="' .$phpbb_root_path. 'images/shoutbox/submit.png" alt="' .$user->lang['SUBMIT']. '" title="' .$user->lang['SUBMIT']. '" /> ',
			'IMAGE_RULES'		=> '<img src="' .$phpbb_root_path. 'images/shoutbox/messages.png" width="38" height="38" title="' .$user->lang['ACP_SHOUT_RULES']. '" alt="' .$user->lang['ACP_SHOUT_RULES']. '" /> ',
			'IMAGE_MESSAGES'	=> '<img src="' .$phpbb_root_path. 'images/shoutbox/messages.png" width="38" height="38" title="' .$user->lang['SHOUT_STATS']. '" alt="' .$user->lang['SHOUT_STATS']. '" /> ',
			'IMAGE_REGLAGES'	=> '<img src="' .$phpbb_root_path. 'images/shoutbox/reglages.png" width="38" height="38" title="' .$user->lang['GENERAL_SETTINGS']. '" alt="' .$user->lang['GENERAL_SETTINGS']. '" /> ',
			'IMAGE_PURGE'		=> '<img src="' .$phpbb_root_path. 'images/shoutbox/burn.png" width="38" height="38" title="' .$user->lang['SHOUT_OPTIONS']. '" alt="' .$user->lang['SHOUT_OPTIONS']. '" /> ',
			'IMAGE_STATS'		=> '<img src="' .$phpbb_root_path. 'images/shoutbox/numbers.png" width="38" height="38" title="' .$user->lang['SHOUT_STATISTICS']. '" alt="' .$user->lang['SHOUT_STATISTICS']. '" /> ',
			'IMAGE_ALERT'		=> '<img src="' .$phpbb_root_path. 'images/shoutbox/alert.png" width="38" height="38" title="' .$user->lang['SHOUT_LOGS']. '" alt="' .$user->lang['SHOUT_LOGS']. '" /> ',
		));
	}
}

?>