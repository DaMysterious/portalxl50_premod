<?php

/**
*
* @mod package		Download Mod 6
* @file				dl_admin_config.php 35 2013/06/11 OXPUS
* @copyright		(c) 2005 oxpus (Karsten Ude) <webmaster@oxpus.de> http://www.oxpus.de
* @copyright mod	(c) hotschi / demolition fabi / oxpus
* @license			http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/*
* connect to phpBB
*/
if ( !defined('IN_PHPBB') )
{
	exit;
}

/*
* fetch all config data
*/
$submit = request_var('submit', '');

if ($submit && !check_form_key('dl_adm_config'))
{
	trigger_error('FORM_INVALID', E_USER_WARNING);
}

reset($config);

// Better work in the "old school method" to recalculate some numbers in an easier way
foreach ($config as $key => $value)
{
	if (substr($key, 0, 3) == 'dl_')
	{
		$new[$key] = utf8_normalize_nfc(request_var($key, $value, true));

		if ($submit)
		{
			if ($key == 'dl_traffics_overall_groups' || $key == 'dl_traffics_users_groups')
			{
				if ($key == 'dl_traffics_overall_groups' && $new['dl_traffics_overall'] <= 1)
				{
					$new[$key] = '';
				}
				else if ($key == 'dl_traffics_users_groups' && $new['dl_traffics_users'] <= 1)
				{
					$new[$key] = '';
				}
				else
				{
					$tmp_values = request_var($key, array(0));
					$new[$key] = implode(',', $tmp_values);
				}
			}
	
			if ($key == 'dl_thumb_xsize')
			{
				$new[$key] = floor(intval($new[$key]));
			}
	
			if ($key == 'dl_thumb_ysize')
			{
				$new[$key] = floor(intval($new[$key]));
			}

			if ($key == 'dl_thumb_fsize')
			{
				$f_quote = request_var('dl_f_quote', '');
				switch($f_quote)
				{
					case 'mb':
						$new[$key] = floor(intval($new[$key] * 1048576));
					break;

					case 'kb':
						$new[$key] = floor(intval($new[$key] * 1024));
					break;

					default:
						$new[$key] = floor(intval($new[$key]));
				}
			}

			if ($key == 'dl_physical_quota')
			{
				$x = request_var('dl_x_quota', '');
				switch($x)
				{
					case 'kb':
						$new[$key] = floor(intval($new[$key]) * 1024);
					break;

					case 'mb':
						$new[$key] = floor(intval($new[$key]) * 1048576);
					break;

					case 'gb':
						$new[$key] = floor(intval($new[$key]) * 1073741824);
					break;
				}
			}

			if ($key == 'dl_overall_traffic')
			{
				$x = request_var('dl_x_over', '');
				switch($x)
				{
					case 'kb':
						$new[$key] = floor(intval($new[$key]) * 1024);
					break;

					case 'mb':
						$new[$key] = floor(intval($new[$key]) * 1048576);
					break;

					case 'gb':
						$new[$key] = floor(intval($new[$key]) * 1073741824);
					break;
				}
			}

			if ($key == 'dl_overall_guest_traffic')
			{
				$x = request_var('dl_x_g_over', '');
				switch($x)
				{
					case 'kb':
						$new[$key] = floor(intval($new[$key]) * 1024);
					break;

					case 'mb':
						$new[$key] = floor(intval($new[$key]) * 1048576);
					break;

					case 'gb':
						$new[$key] = floor(intval($new[$key]) * 1073741824);
					break;
				}
			}

			if ($key == 'dl_newtopic_traffic')
			{
				$x = request_var('dl_x_new', '');
				switch($x)
				{
					case 'kb':
						$new[$key] = floor(intval($new[$key]) * 1024);
					break;

					case 'mb':
						$new[$key] = floor(intval($new[$key]) * 1048576);
					break;
				}
			}

			if ($key == 'dl_reply_traffic')
			{
				$x = request_var('dl_x_reply', '');
				switch($x)
				{
					case 'kb':
						$new[$key] = floor(intval($new[$key]) * 1024);
					break;

					case 'mb':
						$new[$key] = floor(intval($new[$key]) * 1048576);
					break;
				}
			}

			if ($key == 'dl_method_quota')
			{
				$m = request_var('dl_m_quota', '');
				switch($m)
				{
					case 'kb':
						$new[$key] = floor(intval($new[$key]) * 1024);
					break;

					case 'mb':
						$new[$key] = floor(intval($new[$key]) * 1048576);
					break;

					case 'gb':
						$new[$key] = floor(intval($new[$key]) * 1073741824);
					break;
				}
			}

			if ($key == 'dl_enable_rate')
			{
				$cur_rate_points = $config['dl_rate_points'];
				$new_rate_points = request_var('dl_rate_points', 0);

				if (isset($cur_rate_points) && $cur_rate_points <> $new_rate_points)
				{
					$sql = 'DELETE FROM ' . DL_RATING_TABLE;
					$db->sql_query($sql);

					$sql = 'UPDATE ' . DOWNLOADS_TABLE . ' SET rating = 0';
					$db->sql_query($sql);
				}
			}

			if ($key == 'dl_rss_cats_select')
			{
				$new[$key] = '-';
				$rss_cats_select = request_var('dl_rss_cats_select', array(0));

				if (sizeof($rss_cats_select))
				{
					$tmp_cats = array();

					foreach ($rss_cats_select as $tmp_key => $tmp_value)
					{
						$tmp_cats[] = $rss_cats_select[$tmp_key];
					}

					$new[$key] = implode(',', array_map('intval', $tmp_cats));
					unset($tmp_cats);
				}

				unset($rss_cats_select);
			}

			if ($key == 'dl_topic_user')
			{
				if (!$new[$key])
				{
					$new[$key] = $user->data['user_id'];
				}
				else
				{
					$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE user_id = ' . (int) $new[$key];
					$result = $db->sql_query($sql);
					$user_exists = $db->sql_affectedrows($result);
					$db->sql_freeresult($result);

					if (!$user_exists)
					{
						$new[$key] = $user->data['user_id'];
					}
				}
			}

			if ($key == 'dl_file_hash_algo')
			{
				if ($new[$key] != $config['dl_file_hash_algo'])
				{
					$sql = 'UPDATE ' . DOWNLOADS_TABLE . " SET file_hash = ''";
					$db->sql_query($sql);
					$sql = 'UPDATE ' . DL_VERSIONS_TABLE . " SET ver_file_hash = ''";
					$db->sql_query($sql);
				}
			}

			set_config($key, $new[$key], false);
		}
	}
}

if($submit)
{
	add_log('admin', 'DL_LOG_CONFIG');

	// Purge the config cache
	$cache->destroy('config');

	$message = $user->lang['DL_CONFIG_UPDATED'] . "<br /><br />" . sprintf($user->lang['CLICK_RETURN_DL_CONFIG'], '<a href="' . $basic_link . '">', '</a>') . adm_back_link($this->u_action);

	trigger_error($message);
}

$template->set_filenames(array(
	'dl_config' => 'dl_mod/dl_config_body.html')
);

$enable_post_dl_traffic_yes = ( $new['dl_enable_post_dl_traffic'] ) ? 'checked="checked"' : '';
$enable_post_dl_traffic_no = ( !$new['dl_enable_post_dl_traffic'] ) ? 'checked="checked"' : '';

$stop_uploads_yes = ( $new['dl_stop_uploads'] ) ? 'checked="checked"' : '';
$stop_uploads_no = ( !$new['dl_stop_uploads'] ) ? 'checked="checked"' : '';

$upload_traffic_count_yes = ( $new['dl_upload_traffic_count'] ) ? 'checked="checked"' : '';
$upload_traffic_count_no = ( !$new['dl_upload_traffic_count'] ) ? 'checked="checked"' : '';

$disable_email_yes = ( $new['dl_disable_email'] ) ? 'checked="checked"' : '';
$disable_email_no = ( !$new['dl_disable_email'] ) ? 'checked="checked"' : '';

$disable_popup_yes = ( $new['dl_disable_popup'] ) ? 'checked="checked"' : '';
$disable_popup_no = ( !$new['dl_disable_popup'] ) ? 'checked="checked"' : '';

$disable_popup_notify_yes = ( $new['dl_disable_popup_notify'] ) ? 'checked="checked"' : '';
$disable_popup_notify_no = ( !$new['dl_disable_popup_notify'] ) ? 'checked="checked"' : '';

$guest_stats_show_yes = ( $new['dl_guest_stats_show'] ) ? 'checked="checked"' : '';
$guest_stats_show_no = ( !$new['dl_guest_stats_show'] ) ? 'checked="checked"' : '';

$dl_method = $new['dl_method'];
$s_dl_method_select = '<select name="dl_method">';
$s_dl_method_select .= '<option value="1">' . $user->lang['DL_METHOD_OLD'] . '</option>';
$s_dl_method_select .= '<option value="2">' . $user->lang['DL_METHOD_NEW'] . '</option>';
$s_dl_method_select .= '<option value="3">' . $user->lang['DL_DIRECT_DOWNLOAD'] . '</option>';
$s_dl_method_select .= '</select>';
$s_dl_method_select = str_replace('value="' . $dl_method . '">', 'value="' . $dl_method . '" selected="selected">', $s_dl_method_select);

$use_hacklist_yes = ( $new['dl_use_hacklist'] ) ? 'checked="checked"' : '';
$use_hacklist_no = ( !$new['dl_use_hacklist'] ) ? 'checked="checked"' : '';

$use_ext_blacklist_yes = ( $new['dl_use_ext_blacklist']) ? 'checked="checked"' : '';
$use_ext_blacklist_no = ( !$new['dl_use_ext_blacklist']) ? 'checked="checked"' : '';

$show_footer_legend_yes = ( $new['dl_show_footer_legend']) ? 'checked="checked"' : '';
$show_footer_legend_no = ( !$new['dl_show_footer_legend']) ? 'checked="checked"' : '';

$show_footer_stat_yes = ( $new['dl_show_footer_stat']) ? 'checked="checked"' : '';
$show_footer_stat_no = ( !$new['dl_show_footer_stat']) ? 'checked="checked"' : '';

$thumbs_xsize = $new['dl_thumb_xsize'];
$thumbs_ysize = $new['dl_thumb_ysize'];
$thumbs_fsize = $new['dl_thumb_fsize'];

$show_real_filetime_yes = ( $new['dl_show_real_filetime']) ? 'checked="checked"' : '';
$show_real_filetime_no = ( !$new['dl_show_real_filetime']) ? 'checked="checked"' : '';

$limit_desc_on_index = $new['dl_limit_desc_on_index'];

$user_traffic_once_yes = ( $new['dl_user_traffic_once']) ? 'checked="checked"' : '';
$user_traffic_once_no = ( !$new['dl_user_traffic_once']) ? 'checked="checked"' : '';

$prevent_hotlink_yes = ( $new['dl_prevent_hotlink']) ? 'checked="checked"' : '';
$prevent_hotlink_no = ( !$new['dl_prevent_hotlink']) ? 'checked="checked"' : '';

$hotlink_action = $new['dl_hotlink_action'];
$s_hotlink_action_select = '<select name="dl_hotlink_action">';
$s_hotlink_action_select .= '<option value="1">' . $user->lang['DL_HOTLINK_ACTION_ONE'] . '</option>';
$s_hotlink_action_select .= '<option value="0">' . $user->lang['DL_HOTLINK_ACTION_TWO'] . '</option>';
$s_hotlink_action_select .= '</select>';
$s_hotlink_action_select = str_replace('value="' . $hotlink_action . '">', 'value="' . $hotlink_action . '" selected="selected">', $s_hotlink_action_select);

$edit_own_downloads_yes = ( $new['dl_edit_own_downloads']) ? 'checked="checked"' : '';
$edit_own_downloads_no = ( !$new['dl_edit_own_downloads']) ? 'checked="checked"' : '';

$icon_free_for_reg_yes = ( $new['dl_icon_free_for_reg']) ? 'checked="checked"' : '';
$icon_free_for_reg_no = ( !$new['dl_icon_free_for_reg']) ? 'checked="checked"' : '';

$report_broken = $new['dl_report_broken'];
$s_report_broken_select = '<select name="dl_report_broken">';
$s_report_broken_select .= '<option value="0">' . $user->lang['NO'] . '</option>';
$s_report_broken_select .= '<option value="1">' . $user->lang['YES'] . '</option>';
$s_report_broken_select .= '<option value="2">' . $user->lang['DL_OFF_GUESTS'] . '</option>';
$s_report_broken_select .= '</select>';
$s_report_broken_select = str_replace('value="' . $report_broken . '">', 'value="' . $report_broken . '" selected="selected">', $s_report_broken_select);

$report_broken_lock_yes = ( $new['dl_report_broken_lock']) ? 'checked="checked"' : '';
$report_broken_lock_no = ( !$new['dl_report_broken_lock']) ? 'checked="checked"' : '';

$report_broken_message_yes = ( $new['dl_report_broken_message']) ? 'checked="checked"' : '';
$report_broken_message_no = ( !$new['dl_report_broken_message']) ? 'checked="checked"' : '';

$drop_traffic_postdel_yes = ( $new['dl_drop_traffic_postdel']) ? 'checked="checked"' : '';
$drop_traffic_postdel_no = ( !$new['dl_drop_traffic_postdel']) ? 'checked="checked"' : '';

$sort_perform = $new['dl_sort_preform'];
$s_sort_preform_select = '<select name="dl_sort_preform">';
$s_sort_preform_select .= '<option value="1">' . $user->lang['DL_SORT_ACP'] . '</option>';
$s_sort_preform_select .= '<option value="0">' . $user->lang['DL_SORT_USER'] . '</option>';
$s_sort_preform_select .= '</select>';
$s_sort_preform_select = str_replace('value="' . $sort_perform . '">', 'value="' . $sort_perform . '" selected="selected">', $s_sort_preform_select);

$sort_preform_fix = ( $new['dl_sort_preform']) ? 'checked="checked"' : '';
$sort_preform_user = ( !$new['dl_sort_preform']) ? 'checked="checked"' : '';

$s_thumbs_fsize_range = '<select name="dl_f_quote">';
$s_thumbs_fsize_range .= '<option value="byte">' . $user->lang['DL_BYTES_LONG'] . '</option>';
$s_thumbs_fsize_range .= '<option value="kb">' . $user->lang['DL_KB'] . '</option>';
$s_thumbs_fsize_range .= '<option value="mb">' . $user->lang['DL_MB'] . '</option>';
$s_thumbs_fsize_range .= '</select>';

$enable_dl_topic_yes = ( $new['dl_enable_dl_topic']) ? 'checked="checked"' : '';
$enable_dl_topic_no = ( !$new['dl_enable_dl_topic']) ? 'checked="checked"' : '';

$ext_new_window_yes = ( $new['dl_ext_new_window']) ? 'checked="checked"' : '';
$ext_new_window_no = ( !$new['dl_ext_new_window']) ? 'checked="checked"' : '';

$traffic_off_yes = ( $new['dl_traffic_off']) ? 'checked="checked"' : '';
$traffic_off_no = ( !$new['dl_traffic_off']) ? 'checked="checked"' : '';

$todo_link_off_yes = ( $new['dl_todo_link_onoff']) ? 'checked="checked"' : '';
$todo_link_off_no = ( !$new['dl_todo_link_onoff']) ? 'checked="checked"' : '';

$todo_off_yes = ( $new['dl_todo_onoff']) ? 'checked="checked"' : '';
$todo_off_no = ( !$new['dl_todo_onoff']) ? 'checked="checked"' : '';

$u_config_off_yes = ( $new['dl_uconf_link_onoff']) ? 'checked="checked"' : '';
$u_config_off_no = ( !$new['dl_uconf_link_onoff']) ? 'checked="checked"' : '';

$dl_topic_forum = $new['dl_topic_forum'];
$forum_select_tmp = get_forum_list('f_list', false);
$s_forum_select = '';

$dl_active_yes = ( $new['dl_active']) ? 'checked="checked"' : '';
$dl_active_no = ( !$new['dl_active']) ? 'checked="checked"' : '';

$dl_off_hide_yes = ( $new['dl_off_hide']) ? 'checked="checked"' : '';
$dl_off_hide_no = ( !$new['dl_off_hide']) ? 'checked="checked"' : '';

$dl_off_now_time_yes = ( $new['dl_off_now_time']) ? 'checked="checked"' : '';
$dl_off_now_time_no = ( !$new['dl_off_now_time']) ? 'checked="checked"' : '';

$dl_on_admins_yes = ( $new['dl_on_admins']) ? 'checked="checked"' : '';
$dl_on_admins_no = ( !$new['dl_on_admins']) ? 'checked="checked"' : '';

$dl_enable_rate_yes = ( $new['dl_enable_rate']) ? 'checked="checked"' : '';
$dl_enable_rate_no = ( !$new['dl_enable_rate']) ? 'checked="checked"' : '';

$dl_enable_jumpbox_yes = ( $new['dl_enable_jumpbox']) ? 'checked="checked"' : '';
$dl_enable_jumpbox_no = ( !$new['dl_enable_jumpbox']) ? 'checked="checked"' : '';

$dl_rss_enable_yes = ($new['dl_rss_enable']) ? 'checked="checked"' : '';
$dl_rss_enable_no = (!$new['dl_rss_enable']) ? 'checked="checked"' : '';

$dl_rss_cats_all = (!$new['dl_rss_cats']) ? 'checked="checked"' : '';
$dl_rss_cats_selected = ($new['dl_rss_cats'] == 1) ? 'checked="checked"' : '';
$dl_rss_cats_not_selected = ($new['dl_rss_cats'] == 2) ? 'checked="checked"' : '';

$dl_rss_perms_user = ($new['dl_rss_perms']) ? 'checked="checked"' : '';
$dl_rss_perms_guests = (!$new['dl_rss_perms']) ? 'checked="checked"' : '';

$dl_rss_select_last = (!$new['dl_rss_select']) ? 'checked="checked"' : '';
$dl_rss_select_random = ($new['dl_rss_select']) ? 'checked="checked"' : '';

$dl_rss_new_update_yes = ($new['dl_rss_new_update']) ? 'checked="checked"' : '';
$dl_rss_new_update_no = (!$new['dl_rss_new_update']) ? 'checked="checked"' : '';

foreach ($forum_select_tmp as $key => $value)
{
	switch ($value['forum_type'])
	{
		case FORUM_CAT:
			if ($s_forum_select)
			{
				$s_forum_select .= '</optgroup>';
			}
			$s_forum_select .= '<optgroup label="' . $value['forum_name'] . '">';
		break;
		case FORUM_POST:
			$s_forum_select .= '<option value="' . $value['forum_id'] . '">' . $value['forum_name'] . '</option>';
		break;
	}
}

$s_forum_select = '<select name="dl_topic_forum"><option value="-1">' . $user->lang['DL_TOPIC_FORUM_C'] . '</option><option value="0">' . $user->lang['DEACTIVATE'] . '</option>' . $s_forum_select . '</optgroup></select>';
$s_forum_select = str_replace('value="' . $dl_topic_forum . '">', 'value="' . $dl_topic_forum . '" selected="selected">', $s_forum_select);

$thumb_fsize_tmp = dl_format::dl_size($thumbs_fsize, 2, 'select');
$thumbs_fsize_out = $thumb_fsize_tmp['size_out'];
$data_range_select = $thumb_fsize_tmp['range'];
$s_thumbs_fsize_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_thumbs_fsize_range);

$s_select_datasize = '<option value="byte">' . $user->lang['DL_BYTES_LONG'] . '</option>';
$s_select_datasize .= '<option value="kb">' . $user->lang['DL_KB'] . '</option>';
$s_select_datasize .= '<option value="mb">' . $user->lang['DL_MB'] . '</option>';
$s_select_datasize .= '<option value="gb">' . $user->lang['DL_GB'] . '</option>';
$s_select_datasize .= '</select>';

$physical_quota_tmp = dl_format::dl_size($new['dl_physical_quota'], 2, 'select');
$physical_quota_out = $physical_quota_tmp['size_out'];
$data_range_select = $physical_quota_tmp['range'];
$physical_quota_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
$physical_quota_range = '<select name="dl_x_quota">' . $physical_quota_range;

$overall_traffic_tmp = dl_format::dl_size($new['dl_overall_traffic'], 2, 'select');
$overall_traffic_out = $overall_traffic_tmp['size_out'];
$data_range_select = $overall_traffic_tmp['range'];
$overall_traffic_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
$overall_traffic_range = '<select name="dl_x_over">' . $overall_traffic_range;

$remain_traffic_text = $user->lang['DL_REMAIN_OVERALL_TRAFFIC'];
$remain_traffic = $config['dl_overall_traffic'] - $config['dl_remain_traffic'];
$remain_traffic = ($remain_traffic <= 0) ? 0 : $remain_traffic;

$remain_traffic_tmp = dl_format::dl_size($remain_traffic, 2, 'none');
$remain_traffic_out = $remain_traffic_tmp['size_out'];
$x_rem = $remain_traffic_tmp['range'];
$remain_text_out = $remain_traffic_text . $remain_traffic_out . $x_rem;

$overall_guest_traffic_tmp = dl_format::dl_size($new['dl_overall_guest_traffic'], 2, 'select');
$overall_guest_traffic_out = $overall_guest_traffic_tmp['size_out'];
$data_range_select = $overall_guest_traffic_tmp['range'];
$overall_guest_traffic_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
$overall_guest_traffic_range = '<select name="dl_x_g_over">' . $overall_guest_traffic_range;

$remain_guest_traffic_text = $user->lang['DL_REMAIN_OVERALL_GUEST_TRAFFIC'];
$remain_guest_traffic = $config['dl_overall_guest_traffic'] - $config['dl_remain_guest_traffic'];
$remain_guest_traffic = ($remain_guest_traffic <= 0) ? 0 : $remain_guest_traffic;

$remain_guest_traffic_tmp = dl_format::dl_size($remain_guest_traffic, 2, 'none');
$remain_guest_traffic_out = $remain_guest_traffic_tmp['size_out'];
$x_rem = $remain_guest_traffic_tmp['range'];
$remain_guest_text_out = $remain_guest_traffic_text . $remain_guest_traffic_out . $x_rem;

$dl_method_quota_tmp = dl_format::dl_size($new['dl_method_quota'], 2, 'select');
$dl_method_quota_out = $dl_method_quota_tmp['size_out'];
$data_range_select = $dl_method_quota_tmp['range'];
$dl_method_quota_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
$dl_method_quota_range = '<select name="dl_m_quota">' . $dl_method_quota_range;

$s_select_datasize = '<option value="byte">' . $user->lang['DL_BYTES_LONG'] . '</option>';
$s_select_datasize .= '<option value="kb">' . $user->lang['DL_KB'] . '</option>';
$s_select_datasize .= '<option value="mb">' . $user->lang['DL_MB'] . '</option>';
$s_select_datasize .= '</select>';

$newtopic_traffic_tmp = dl_format::dl_size($new['dl_newtopic_traffic'], 2, 'select');
$newtopic_traffic_out = $newtopic_traffic_tmp['size_out'];
$data_range_select = $newtopic_traffic_tmp['range'];
$newtopic_traffic_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
$newtopic_traffic_range = '<select name="dl_x_new">' . $newtopic_traffic_range;

$reply_traffic_tmp = dl_format::dl_size($new['dl_reply_traffic'], 2, 'select');
$reply_traffic_out = $reply_traffic_tmp['size_out'];
$data_range_select = $reply_traffic_tmp['range'];
$reply_traffic_range = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
$reply_traffic_range = '<select name="dl_x_reply">' . $reply_traffic_range;

$s_stats_perm_select = '<select name="dl_stats_perm">';
$s_stats_perm_select .= '<option value="0">' . $user->lang['DL_STAT_PERM_ALL'] . '</option>';
$s_stats_perm_select .= '<option value="1">' . $user->lang['DL_STAT_PERM_USER'] . '</option>';
$s_stats_perm_select .= '<option value="2">' . $user->lang['DL_STAT_PERM_MOD'] . '</option>';
$s_stats_perm_select .= '<option value="3">' . $user->lang['DL_STAT_PERM_ADMIN'] . '</option>';
$s_stats_perm_select .= '</select>';
$s_stats_perm_select = str_replace('value="' . $new['dl_stats_perm'] . '">', 'value="' . $new['dl_stats_perm'] . '" selected="selected">', $s_stats_perm_select);

$s_topic_user_select = '<select name="dl_diff_topic_user">';
$s_topic_user_select .= '<option value="0">' . $user->lang['DL_TOPIC_USER_SELF'] . '</option>';
$s_topic_user_select .= '<option value="1">' . $user->lang['DL_TOPIC_USER_OTHER'] . '</option>';
$s_topic_user_select .= '<option value="2">' . $user->lang['DL_TOPIC_USER_CAT'] . '</option>';
$s_topic_user_select .= '</select>';
$s_topic_user_select = str_replace('value="' . $new['dl_diff_topic_user'] . '">', 'value="' . $new['dl_diff_topic_user'] . '" selected="selected">', $s_topic_user_select);

$s_topic_more_details = '<select name="dl_topic_more_details">';
$s_topic_more_details .= '<option value="0">' . $user->lang['DL_TOPIC_NO_MORE_DETAILS'] . '</option>';
$s_topic_more_details .= '<option value="1">' . $user->lang['DL_TOPIC_MORE_DETAILS_UNDER'] . '</option>';
$s_topic_more_details .= '<option value="2">' . $user->lang['DL_TOPIC_MORE_DETAILS_OVER'] . '</option>';
$s_topic_more_details .= '</select>';
$s_topic_more_details = str_replace('value="' . $new['dl_topic_more_details'] . '">', 'value="' . $new['dl_topic_more_details'] . '" selected="selected">', $s_topic_more_details);

$s_rss_off_action = '<select name="dl_rss_off_action">';
$s_rss_off_action .= '<option value="0">' . $user->lang['DL_RSS_ACTION_R_DLX'] . '</option>';
$s_rss_off_action .= '<option value="1">' . $user->lang['DL_RSS_ACTION_R_IDX'] . '</option>';
$s_rss_off_action .= '<option value="2">' . $user->lang['DL_RSS_ACTION_D_TXT'] . '</option>';
$s_rss_off_action .= '</select>';
$s_rss_off_action = str_replace('value="' . $new['dl_rss_off_action'] . '">', 'value="' . $new['dl_rss_off_action'] . '" selected="selected">', $s_rss_off_action);

$s_rss_desc_length = '<select name="dl_rss_desc_length">';
$s_rss_desc_length .= '<option value="0">' . $user->lang['DL_RSS_DESC_LENGTH_NONE'] . '</option>';
$s_rss_desc_length .= '<option value="1">' . $user->lang['DL_RSS_DESC_LENGTH_FULL'] . '</option>';
$s_rss_desc_length .= '<option value="2">' . $user->lang['DL_RSS_DESC_LENGTH_SHORT'] . '</option>';
$s_rss_desc_length .= '</select>';
$s_rss_desc_length = str_replace('value="' . $new['dl_rss_desc_length'] . '">', 'value="' . $new['dl_rss_desc_length'] . '" selected="selected">', $s_rss_desc_length);

$dl_traffics_guests_yes = ($new['dl_traffics_guests']) ? 'checked="checked"' : '';
$dl_traffics_guests_no = (!$new['dl_traffics_guests']) ? 'checked="checked"' : '';

$sql = 'SELECT group_id, group_name, group_type FROM ' . GROUPS_TABLE . "
	WHERE group_name NOT IN ('GUESTS', 'BOTS')
	ORDER BY group_name";
$result = $db->sql_query($sql);
$total_groups = $db->sql_affectedrows($result);

$traffics_overall_group_ids = explode(',', $new['dl_traffics_overall_groups']);
$traffics_users_group_ids = explode(',', $new['dl_traffics_users_groups']);

$s_groups_overall_select = $s_groups_users_select = '';

while ($row = $db->sql_fetchrow($result))
{
	$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];
	$group_id = $row['group_id'];

	if (in_array($group_id, $traffics_overall_group_ids) && $new['dl_traffics_overall'] > 1)
	{
		$s_groups_overall_select .= '<option value="' . $group_id . '" selected="selected">' . $group_name . '</option>';
	}
	else
	{
		$s_groups_overall_select .= '<option value="' . $group_id . '">' . $group_name . '</option>';
	}

	if (in_array($group_id, $traffics_users_group_ids) && $new['dl_traffics_users'] > 1)
	{
		$s_groups_users_select .= '<option value="' . $group_id . '" selected="selected">' . $group_name . '</option>';
	}
	else
	{
		$s_groups_users_select .= '<option value="' . $group_id . '">' . $group_name . '</option>';
	}
}

$db->sql_freeresult($result);

$group_select_size = ($total_groups < 10) ? $total_groups : 10;

$dl_traffics_overall_groups = '<select name="dl_traffics_overall_groups[]" multiple="multiple" size="' . $group_select_size . '">' . $s_groups_overall_select . '</select>';
$dl_traffics_users_groups = '<select name="dl_traffics_users_groups[]" multiple="multiple" size="' . $group_select_size . '">' . $s_groups_users_select . '</select>';

$dl_traffics_overall = '<select name="dl_traffics_overall">';
$dl_traffics_overall .= '<option value="1">' . $user->lang['DL_TRAFFICS_ON_ALL'] . '</option>';
if ($total_groups)
{
	$dl_traffics_overall .= '<option value="2">' . $user->lang['DL_TRAFFICS_ON_GROUPS'] . '</option>';
	$dl_traffics_overall .= '<option value="3">' . $user->lang['DL_TRAFFICS_OFF_GROUPS'] . '</option>';
}
$dl_traffics_overall .= '<option value="0">' . $user->lang['DL_TRAFFICS_OFF_ALL'] . '</option>';
$dl_traffics_overall .= '</select>';
$dl_traffics_overall = str_replace('value="' . $new['dl_traffics_overall'] . '">', 'value="' . $new['dl_traffics_overall'] . '" selected="selected">', $dl_traffics_overall);

$dl_traffics_users = '<select name="dl_traffics_users">';
$dl_traffics_users .= '<option value="1">' . $user->lang['DL_TRAFFICS_ON_ALL'] . '</option>';
if ($total_groups)
{
	$dl_traffics_users .= '<option value="2">' . $user->lang['DL_TRAFFICS_ON_GROUPS'] . '</option>';
	$dl_traffics_users .= '<option value="3">' . $user->lang['DL_TRAFFICS_OFF_GROUPS'] . '</option>';
}
$dl_traffics_users .= '<option value="0">' . $user->lang['DL_TRAFFICS_OFF_ALL'] . '</option>';
$dl_traffics_users .= '</select>';
$dl_traffics_users = str_replace('value="' . $new['dl_traffics_users'] . '">', 'value="' . $new['dl_traffics_users'] . '" selected="selected">', $dl_traffics_users);

$dl_traffics_founder_yes = ($new['dl_traffics_founder']) ? 'checked="checked"' : '';
$dl_traffics_founder_no = (!$new['dl_traffics_founder']) ? 'checked="checked"' : '';

$s_captcha_perm_download = '<select name="dl_download_vc">';
$s_captcha_perm_download .= '<option value="0">' . $user->lang['DL_CAPTCHA_PERM_0'] . '</option>';
$s_captcha_perm_download .= '<option value="1">' . $user->lang['DL_CAPTCHA_PERM_1'] . '</option>';
$s_captcha_perm_download .= '<option value="2">' . $user->lang['DL_CAPTCHA_PERM_2'] . '</option>';
$s_captcha_perm_download .= '<option value="3">' . $user->lang['DL_CAPTCHA_PERM_3'] . '</option>';
$s_captcha_perm_download .= '<option value="4">' . $user->lang['DL_CAPTCHA_PERM_4'] . '</option>';
$s_captcha_perm_download .= '<option value="5">' . $user->lang['DL_CAPTCHA_PERM_5'] . '</option>';
$s_captcha_perm_download .= '</select>';
$s_captcha_perm_download = str_replace('value="' . $new['dl_download_vc'] . '">', 'value="' . $new['dl_download_vc'] . '" selected="selected">', $s_captcha_perm_download);

$s_captcha_perm_report = '<select name="dl_report_broken_vc">';
$s_captcha_perm_report .= '<option value="0">' . $user->lang['DL_CAPTCHA_PERM_0'] . '</option>';
$s_captcha_perm_report .= '<option value="1">' . $user->lang['DL_CAPTCHA_PERM_1'] . '</option>';
$s_captcha_perm_report .= '<option value="2">' . $user->lang['DL_CAPTCHA_PERM_2'] . '</option>';
$s_captcha_perm_report .= '<option value="3">' . $user->lang['DL_CAPTCHA_PERM_3'] . '</option>';
$s_captcha_perm_report .= '<option value="4">' . $user->lang['DL_CAPTCHA_PERM_4'] . '</option>';
$s_captcha_perm_report .= '<option value="5">' . $user->lang['DL_CAPTCHA_PERM_5'] . '</option>';
$s_captcha_perm_report .= '</select>';
$s_captcha_perm_report = str_replace('value="' . $new['dl_report_broken_vc'] . '">', 'value="' . $new['dl_report_broken_vc'] . '" selected="selected">', $s_captcha_perm_report);

$s_dl_cat_edit = '<select name="dl_cat_edit">';
$s_dl_cat_edit .= '<option value="0">' . $user->lang['DL_CAT_EDIT_LINK_0'] . '</option>';
$s_dl_cat_edit .= '<option value="1">' . $user->lang['DL_CAT_EDIT_LINK_1'] . '</option>';
$s_dl_cat_edit .= '<option value="2">' . $user->lang['DL_CAT_EDIT_LINK_2'] . '</option>';
$s_dl_cat_edit .= '<option value="3">' . $user->lang['DL_CAT_EDIT_LINK_3'] . '</option>';
$s_dl_cat_edit .= '</select>';
$s_dl_cat_edit = str_replace('value="' . $new['dl_cat_edit'] . '">', 'value="' . $new['dl_cat_edit'] . '" selected="selected">', $s_dl_cat_edit);

$dl_overview_link_yes = ($new['dl_overview_link_onoff']) ? 'checked="checked"' : '';
$dl_overview_link_no = (!$new['dl_overview_link_onoff']) ? 'checked="checked"' : '';

$s_dl_hash_algo = '<select name="dl_file_hash_algo">';
$s_dl_hash_algo .= '<option value="md5">MD5</option>';
$s_dl_hash_algo .= '<option value="sha1">SHA1</option>';
$s_dl_hash_algo .= '</select>';
$s_dl_hash_algo = str_replace('value="' . $new['dl_file_hash_algo'] . '">', 'value="' . $new['dl_file_hash_algo'] . '" selected="selected">', $s_dl_hash_algo);

$dl_similar_yes = ($new['dl_similar_dl']) ? 'checked="checked"' : '';
$dl_similar_no = (!$new['dl_similar_dl']) ? 'checked="checked"' : '';

add_form_key('dl_adm_config');

$template->assign_vars(array(
	'L_DL_ACTIVE_EXPLAIN'					=> 'DL_ACTIVE_EXPLAIN',
	'L_DL_ANTISPAM_EXPLAIN'					=> 'DL_ANTISPAM',
	'L_DL_CAT_EDIT_LINK_EXPLAIN'			=> 'DL_CAT_EDIT_LINK',
	'L_DL_DELAY_AUTO_TRAFFIC_EXPLAIN'		=> 'DL_DELAY_AUTO_TRAFFIC',
	'L_DL_DELAY_POST_TRAFFIC_EXPLAIN'		=> 'DL_DELAY_POST_TRAFFIC',
	'L_DL_DISABLE_EMAIL_EXPLAIN'			=> 'DL_DISABLE_EMAIL',
	'L_DL_DISABLE_POPUP_EXPLAIN'			=> 'DL_DISABLE_POPUP',
	'L_DL_DISABLE_POPUP_NOTIFY_EXPLAIN'		=> 'DL_DISABLE_POPUP_NOTIFY',
	'L_DL_EDIT_TIME_EXPLAIN'				=> 'DL_EDIT_TIME',
	'L_DL_ENABLE_JUMPBOX_EXPLAIN'			=> 'DL_ENABLE_JUMPBOX',
	'L_DL_ENABLE_RATE_EXPLAIN'				=> 'DL_ENABLE_RATE',
	'L_DL_EXT_NEW_WINDOW_EXPLAIN'			=> 'DL_EXT_NEW_WINDOW',
	'L_DL_GUEST_STATS_SHOW_EXPLAIN'			=> 'DL_GUEST_STATS_SHOW',
	'L_DL_FILE_HASH_ALGO_EXPLAIN'			=> 'DL_FILE_HASH_ALGO',
	'L_DL_HOTLINK_ACTION_EXPLAIN'			=> 'DL_HOTLINK_ACTION',
	'L_DL_LINKS_PER_PAGE_EXPLAIN'			=> 'DL_LINKS_PER_PAGE',
	'L_DL_METHOD_EXPLAIN'					=> 'DL_METHOD',
	'L_DL_METHOD_QUOTA_EXPLAIN'				=> 'DL_METHOD_QUOTA',
	'L_DL_NEW_TIME_EXPLAIN'					=> 'DL_NEW_TIME',
	'L_DL_OFF_HIDE_EXPLAIN'					=> 'DL_OFF_HIDE',
	'L_DL_OFF_NOW_TIME_EXPLAIN'				=> 'DL_OFF_NOW_TIME',
	'L_DL_OFF_TIME_PERIOD_EXPLAIN'			=> 'DL_OFF_PERIOD',
	'L_DL_ON_ADMINS_EXPLAIN'				=> 'DL_ON_ADMINS',
	'L_DL_OVERVIEW_LINK_EXPLAIN'			=> 'DL_OVERVIEW_LINK',
	'L_DL_PATH_EXPLAIN'						=> 'DOWNLOAD_PATH',
	'L_DL_PHYSICAL_QUOTA_EXPLAIN'			=> 'DL_PHYSICAL_QUOTA',
	'L_DL_PHYSICAL_QUOTA_SECOND'			=> sprintf($user->lang['DL_PHYSICAL_QUOTA_EXPLAIN'], dl_format::dl_size($total_size, 2)),
	'L_DL_POSTS_EXPLAIN'					=> 'DL_POSTS',
	'L_DL_RATE_POINTS_EXPLAIN'				=> 'DL_RATE_POINTS',
	'L_DL_RECENT_EXPLAIN'					=> 'NUMBER_RECENT_DL_ON_PORTAL',
	'L_DL_REPORT_BROKEN_LOCK_EXPLAIN'		=> 'DL_REPORT_BROKEN_LOCK',
	'L_DL_REPORT_BROKEN_MESSAGE_EXPLAIN'	=> 'DL_REPORT_BROKEN_MESSAGE',
	'L_DL_REPORT_BROKEN_VC_EXPLAIN'			=> 'DL_REPORT_BROKEN_VC',
	'L_DL_RSS_CATS_EXPLAIN'					=> 'DL_RSS_CATS',
	'L_DL_RSS_DESC_LENGTH_EXPLAIN'			=> 'DL_RSS_DESC_LENGTH',
	'L_DL_RSS_DESC_LENGTH_SHORTEN_EXPLAIN'	=> 'DL_RSS_DESC_LENGTH_SHORTEN',
	'L_DL_RSS_ENABLE_EXPLAIN'				=> 'DL_RSS_ENABLE',
	'L_DL_RSS_NEW_UPDATE_EXPLAIN'			=> 'DL_RSS_NEW_UPDATE',
	'L_DL_RSS_NUMBER_EXPLAIN'				=> 'DL_RSS_NUMBER',
	'L_DL_RSS_OFF_ACTION_EXPLAIN'			=> 'DL_RSS_OFF_ACTION',
	'L_DL_RSS_OFF_TEXT_EXPLAIN'				=> 'DL_RSS_OFF_TEXT',
	'L_DL_RSS_PERMS_EXPLAIN'				=> 'DL_RSS_PERMS',
	'L_DL_RSS_SELECT_EXPLAIN'				=> 'DL_RSS_SELECT',
	'L_DL_STATS_PERM_EXPLAIN'				=> 'DL_STAT_PERM',
	'L_DL_STOP_UPLOADS_EXPLAIN'				=> 'DL_STOP_UPLOADS',
	'L_DL_THUMBSNAIL_DIM_EXPLAIN'			=> 'DL_THUMB_MAX_DIM',
	'L_DL_THUMBSNAIL_SIZE_EXPLAIN'			=> 'DL_THUMB_MAX_SIZE',
	'L_DL_TODO_LINK_EXPLAIN'				=> 'DL_TODO_LINK',
	'L_DL_USE_TODOLIST_EXPLAIN'				=> 'DL_USE_TODOLIST',
	'L_DL_TOPIC_DETAILS_EXPLAIN'			=> 'DL_TOPIC_DETAILS',
	'L_DL_TOPIC_FORUM_EXPLAIN'				=> 'DL_TOPIC_FORUM',
	'L_DL_TOPIC_TEXT_EXPLAIN'				=> 'DL_TOPIC_TEXT',
	'L_DL_TOPIC_USER_EXPLAIN'				=> 'DL_TOPIC_USER',
	'L_DL_TRAFFIC_OFF_EXPLAIN'				=> 'DL_TRAFFIC_OFF',
	'L_DL_TRAFFICS_FOUNDER_EXPLAIN'			=> 'DL_TRAFFICS_FOUNDER',
	'L_DL_TRAFFICS_GUESTS_EXPLAIN'			=> 'DL_TRAFFICS_GUESTS',
	'L_DL_TRAFFICS_OVERALL_EXPLAIN'			=> 'DL_TRAFFICS_OVERALL',
	'L_DL_TRAFFICS_OVERALL_GROUPS_EXPLAIN'	=> 'DL_TRAFFICS_OVERALL_GROUPS',
	'L_DL_TRAFFICS_USERS_EXPLAIN'			=> 'DL_TRAFFICS_USERS',
	'L_DL_TRAFFICS_USERS_GROUPS_EXPLAIN'	=> 'DL_TRAFFICS_USERS_GROUPS',
	'L_DL_UCONF_LINK_EXPLAIN'				=> 'DL_UCONF_LINK',
	'L_DL_UPLOAD_TRAFFIC_COUNT_EXPLAIN'		=> 'DL_UPLOAD_TRAFFIC_COUNT',
	'L_DL_USE_EXT_BLACKLIST_EXPLAIN'		=> 'DL_USE_EXT_BLACKLIST',
	'L_DL_USE_HACKLIST_EXPLAIN'				=> 'DL_USE_HACKLIST',
	'L_DOWNLOAD_VC_EXPLAIN'					=> 'DL_VISUAL_CONFIRMATION',
	'L_DROP_TRAFFIC_POSTDEL_EXPLAIN'		=> 'DL_DROP_TRAFFIC_POSTDEL',
	'L_EDIT_OWN_DOWNLOADS_EXPLAIN'			=> 'DL_EDIT_OWN_DOWNLOADS',
	'L_ENABLE_POST_TRAFFIC_EXPLAIN'			=> 'DL_ENABLE_POST_TRAFFIC',
	'L_ENABLE_TOPIC_EXPLAIN'				=> 'DL_ENABLE_TOPIC',
	'L_ICON_FREE_FOR_REG_EXPLAIN'			=> 'DL_ICON_FREE_FOR_REG',
	'L_LATEST_COMMENTS_EXPLAIN'				=> 'DL_LATEST_COMMENTS',
	'L_LIMIT_DESC_ON_INDEX_EXPLAIN'			=> 'DL_LIMIT_DESC_ON_INDEX',
	'L_NEWTOPIC_TRAFFIC_EXPLAIN'			=> 'DL_NEWTOPIC_TRAFFIC',
	'L_OVERALL_GUEST_TRAFFIC_EXPLAIN'		=> 'DL_OVERALL_GUEST_TRAFFIC',
	'L_OVERALL_TRAFFIC_EXPLAIN'				=> 'DL_OVERALL_TRAFFIC',
	'L_PREVENT_HOTLINK_EXPLAIN'				=> 'DL_PREVENT_HOTLINK',
	'L_REPLY_TRAFFIC_EXPLAIN'				=> 'DL_REPLY_TRAFFIC',
	'L_REPORT_BROKEN_EXPLAIN'				=> 'DL_REPORT_BROKEN',
	'L_SHORTEN_EXTERN_LINKS_EXPLAIN'		=> 'DL_SHORTEN_EXTERN_LINKS',
	'L_SHOW_FOOTER_LEGEND_EXPLAIN'			=> 'DL_SHOW_FOOTER_LEGEND',
	'L_SHOW_FOOTER_STATS_EXPLAIN'			=> 'DL_SHOW_FOOTER_STAT',
	'L_SHOW_REAL_FILETIME_EXPLAIN'			=> 'DL_SHOW_REAL_FILETIME',
	'L_SORT_PREFORM_EXPLAIN'				=> 'DL_SORT_PREFORM',
	'L_USER_TRAFFIC_ONCE_EXPLAIN'			=> 'DL_USER_TRAFFIC_ONCE',
	'L_SIMILAR_DL_OPTION'					=> 'DL_SIMILAR_DL',
	'L_SIMILAR_DL_LIMIT'					=> 'DL_SIMILAR_DL_LIMIT',

	'DELAY_AUTO_TRAFFIC'	=> $new['dl_delay_auto_traffic'],
	'DELAY_POST_TRAFFIC'	=> $new['dl_delay_post_traffic'],
	'DL_ANTISPAM_HOURS'		=> $new['dl_antispam_hours'],
	'DL_ANTISPAM_POSTS'		=> $new['dl_antispam_posts'],
	'DL_EDIT_TIME'			=> $new['dl_edit_time'],
	'DL_LINKS_PER_PAGE'		=> $new['dl_links_per_page'],
	'DL_NEW_TIME'			=> $new['dl_new_time'],
	'DL_OFF_FROM'			=> $new['dl_off_from'],
	'DL_OFF_TILL'			=> $new['dl_off_till'],
	'DL_PATH'				=> $new['dl_download_dir'],
	'DL_POSTS'				=> $new['dl_posts'],
	'DL_RATE_POINTS'		=> $new['dl_rate_points'],
	'DL_RECENT'				=> $new['dl_recent_downloads'],
	'DL_RSS_DESC_SHORTEN'	=> $new['dl_rss_desc_shorten'],
	'DL_RSS_NUMBER'			=> $new['dl_rss_number'],
	'DL_RSS_OFF_TEXT'		=> $new['dl_rss_off_text'],
	'DL_TOPIC_TEXT'			=> $new['dl_topic_text'],
	'DL_TOPIC_USER'			=> $new['dl_topic_user'],
	'LATEST_COMMENTS'		=> $new['dl_latest_comments'],
	'SHORTEN_EXTERN_LINKS'	=> $new['dl_shorten_extern_links'],
	'SIMILAR_DL_LIMIT'		=> $new['dl_similar_limit'],

	'DISABLE_EMAIL_NO'					=> $disable_email_no,
	'DISABLE_EMAIL_YES'					=> $disable_email_yes,
	'DISABLE_POPUP_NO'					=> $disable_popup_no,
	'DISABLE_POPUP_NOTIFY_NO'			=> $disable_popup_notify_no,
	'DISABLE_POPUP_NOTIFY_YES'			=> $disable_popup_notify_yes,
	'DISABLE_POPUP_YES'					=> $disable_popup_yes,
	'DL_ACTIVE_NO'						=> $dl_active_no,
	'DL_ACTIVE_YES'						=> $dl_active_yes,
	'DL_ENABLE_JUMPBOX_NO'				=> $dl_enable_jumpbox_no,
	'DL_ENABLE_JUMPBOX_YES'				=> $dl_enable_jumpbox_yes,
	'DL_ENABLE_RATE_NO'					=> $dl_enable_rate_no,
	'DL_ENABLE_RATE_YES'				=> $dl_enable_rate_yes,
	'DL_METHOD_QUOTA'					=> $dl_method_quota_out,
	'DL_OFF_HIDE_NO'					=> $dl_off_hide_no,
	'DL_OFF_HIDE_YES'					=> $dl_off_hide_yes,
	'DL_OFF_NOW_TIME_NO'				=> $dl_off_now_time_no,
	'DL_OFF_NOW_TIME_YES'				=> $dl_off_now_time_yes,
	'DL_ON_ADMINS_NO'					=> $dl_on_admins_no,
	'DL_ON_ADMINS_YES'					=> $dl_on_admins_yes,
	'DL_RSS_CATS_ALL'					=> $dl_rss_cats_all,
	'DL_RSS_CATS_NOT_SELECTED'			=> $dl_rss_cats_not_selected,
	'DL_RSS_CATS_SELECTED'				=> $dl_rss_cats_selected,
	'DL_RSS_ENABLE_NO'					=> $dl_rss_enable_no,
	'DL_RSS_ENABLE_YES'					=> $dl_rss_enable_yes,
	'DL_RSS_NEW_UPDATE_NO'				=> $dl_rss_new_update_no,
	'DL_RSS_NEW_UPDATE_YES'				=> $dl_rss_new_update_yes,
	'DL_RSS_PERMS_GUESTS'				=> $dl_rss_perms_guests,
	'DL_RSS_PERMS_USER'					=> $dl_rss_perms_user,
	'DL_RSS_SELECT_LAST'				=> $dl_rss_select_last,
	'DL_RSS_SELECT_RANDOM'				=> $dl_rss_select_random,
	'DL_TRAFFICS_FOUNDER_NO'			=> $dl_traffics_founder_no,
	'DL_TRAFFICS_FOUNDER_YES'			=> $dl_traffics_founder_yes,
	'DL_TRAFFICS_GUESTS_NO'				=> $dl_traffics_guests_no,
	'DL_TRAFFICS_GUESTS_YES'			=> $dl_traffics_guests_yes,
	'DROP_TRAFFIC_POSTDEL_NO'			=> $drop_traffic_postdel_no,
	'DROP_TRAFFIC_POSTDEL_YES'			=> $drop_traffic_postdel_yes,
	'EDIT_OWN_DOWNLOADS_NO'				=> $edit_own_downloads_no,
	'EDIT_OWN_DOWNLOADS_YES'			=> $edit_own_downloads_yes,
	'ENABLE_DL_TOPIC_NO'				=> $enable_dl_topic_no,
	'ENABLE_DL_TOPIC_YES'				=> $enable_dl_topic_yes,
	'ENABLE_POST_TRAFFIC_NO'			=> $enable_post_dl_traffic_no,
	'ENABLE_POST_TRAFFIC_YES'			=> $enable_post_dl_traffic_yes,
	'EXT_NEW_WINDOW_NO'					=> $ext_new_window_no,
	'EXT_NEW_WINDOW_YES'				=> $ext_new_window_yes,
	'GUEST_STATS_SHOW_NO'				=> $guest_stats_show_no,
	'GUEST_STATS_SHOW_YES'				=> $guest_stats_show_yes,
	'ICON_FREE_FOR_REG_NO'				=> $icon_free_for_reg_no,
	'ICON_FREE_FOR_REG_YES'				=> $icon_free_for_reg_yes,
	'LIMIT_DESC_ON_INDEX'				=> $limit_desc_on_index,
	'NEWTOPIC_TRAFFIC'					=> $newtopic_traffic_out,
	'OVERALL_GUEST_TRAFFIC'				=> $overall_guest_traffic_out,
	'OVERALL_TRAFFIC'					=> $overall_traffic_out,
	'OVERVIEW_LINK_OFF_YES'				=> $dl_overview_link_yes,
	'OVERVIEW_LINK_OFF_NO'				=> $dl_overview_link_no,
	'PHYSICAL_QUOTA'					=> $physical_quota_out,
	'PREVENT_HOTLINK_NO'				=> $prevent_hotlink_no,
	'PREVENT_HOTLINK_YES'				=> $prevent_hotlink_yes,
	'REMAINING_OVERALL_GUEST_TRAFFIC'	=> $remain_guest_text_out,
	'REMAINING_OVERALL_TRAFFIC'			=> $remain_text_out,
	'REPLY_TRAFFIC'						=> $reply_traffic_out,
	'REPORT_BROCKEN_LOCK_NO'			=> $report_broken_lock_no,
	'REPORT_BROCKEN_LOCK_YES'			=> $report_broken_lock_yes,
	'REPORT_BROCKEN_MESSAGE_NO'			=> $report_broken_message_no,
	'REPORT_BROCKEN_MESSAGE_YES'		=> $report_broken_message_yes,
	'SIMILAR_DL_YES'					=> $dl_similar_yes,
	'SIMILAR_DL_NO'						=> $dl_similar_no,
	'SHOW_FOOTER_LEGEND_NO'				=> $show_footer_legend_no,
	'SHOW_FOOTER_LEGEND_NO'				=> $show_footer_legend_no,
	'SHOW_FOOTER_LEGEND_YES'			=> $show_footer_legend_yes,
	'SHOW_FOOTER_STATS_NO'				=> $show_footer_stat_no,
	'SHOW_FOOTER_STATS_YES'				=> $show_footer_stat_yes,
	'SHOW_REAL_FILETIME_NO'				=> $show_real_filetime_no,
	'SHOW_REAL_FILETIME_YES'			=> $show_real_filetime_yes,
	'STOP_UPLOADS_NO'					=> $stop_uploads_no,
	'STOP_UPLOADS_YES'					=> $stop_uploads_yes,
	'THUMB_FSIZE' 						=> $thumbs_fsize_out,
	'THUMB_XSIZE' 						=> $thumbs_xsize,
	'THUMB_YSIZE' 						=> $thumbs_ysize,
	'TODO_LINK_OFF_NO'					=> $todo_link_off_no,
	'TODO_LINK_OFF_YES'					=> $todo_link_off_yes,
	'TODO_OFF_NO'						=> $todo_off_no,
	'TODO_OFF_YES'						=> $todo_off_yes,
	'TRAFFIC_OFF_NO'					=> $traffic_off_no,
	'TRAFFIC_OFF_YES'					=> $traffic_off_yes,
	'U_CONFIG_OFF_NO'					=> $u_config_off_no,
	'U_CONFIG_OFF_YES'					=> $u_config_off_yes,
	'UPLOAD_TRAFFIC_COUNT_NO'			=> $upload_traffic_count_no,
	'UPLOAD_TRAFFIC_COUNT_YES'			=> $upload_traffic_count_yes,
	'USE_EXT_BLACKLIST_NO'				=> $use_ext_blacklist_no,
	'USE_EXT_BLACKLIST_YES'				=> $use_ext_blacklist_yes,
	'USE_HACKLIST_NO'					=> $use_hacklist_no,
	'USE_HACKLIST_YES'					=> $use_hacklist_yes,
	'USER_TRAFFIC_ONCE_NO'				=> $user_traffic_once_no,
	'USER_TRAFFIC_ONCE_YES'				=> $user_traffic_once_yes,

	'S_CAPTCHA_PERM_DOWNLOAD'		=> $s_captcha_perm_download,
	'S_CAPTCHA_PERM_REPORT'			=> $s_captcha_perm_report,
	'S_CONFIG_ACTION'				=> $basic_link,
	'S_DL_CAT_EDIT'					=> $s_dl_cat_edit,
	'S_DL_DIFF_TOPIC_USER'			=> $s_topic_user_select,
	'S_DL_HASH_ALGO'				=> $s_dl_hash_algo,
	'S_DL_METHOD_QUOTA_RANGE'		=> $dl_method_quota_range,
	'S_DL_METHOD_SELECT'			=> $s_dl_method_select,
	'S_DL_TOPIC_FORUM'				=> $s_forum_select,
	'S_HOTLINK_ACTION_SELECT'		=> $s_hotlink_action_select,
	'S_NEWTOPIC_TRAFFIC_RANGE'		=> $newtopic_traffic_range,
	'S_OVERALL_GUEST_TRAFFIC_RANGE'	=> $overall_guest_traffic_range,
	'S_OVERALL_TRAFFIC_RANGE'		=> $overall_traffic_range,
	'S_PHYSICAL_QUOTA_RANGE'		=> $physical_quota_range,
	'S_REPLY_TRAFFIC_RANGE'			=> $reply_traffic_range,
	'S_REPORT_BROKEN_SELECT'		=> $s_report_broken_select,
	'S_RSS_CATS_SELECT'				=> dl_extra::dl_cat_select(0, 0, array_map('intval', explode(',', $new['dl_rss_cats_select']))),
	'S_RSS_DESC_LENGTH'				=> $s_rss_desc_length,
	'S_RSS_OFF_ACTION'				=> $s_rss_off_action,
	'S_SORT_PREFORM_SELECT'			=> $s_sort_preform_select,
	'S_STATS_PERM_SELECT'			=> $s_stats_perm_select,
	'S_THUMBS_FSIZE_RANGE'			=> $s_thumbs_fsize_range,
	'S_TOPIC_MORE_DETAILS'			=> $s_topic_more_details,
	'S_TRAFFICS_OVERALL'			=> $dl_traffics_overall,
	'S_TRAFFICS_OVERALL_GROUPS'		=> $dl_traffics_overall_groups,
	'S_TRAFFICS_USERS'				=> $dl_traffics_users,
	'S_TRAFFICS_USERS_GROUPS'		=> $dl_traffics_users_groups,

	'U_CAPTCHA_EXAMPLE' => append_sid("{$phpbb_root_path}downloads.$phpEx", "view=code&amp;code=oxpus123"),
));

$acl_cat_names = array(
	0 => $user->lang['DL_ACP_CONF_GENERAL'],
	1 => $user->lang['DL_ACP_CONF_VIEW'],
	2 => $user->lang['DL_ACP_CONF_PROTECT'],
	3 => $user->lang['DL_ACP_CONF_LIMIT'],
	4 => $user->lang['DL_ACP_CONF_TRAFFIC'],
	5 => $user->lang['DL_ACP_CONF_MESSAGE'],
	6 => $user->lang['DL_ACP_CONF_TOPIC'],
	7 => $user->lang['DL_ACP_CONF_RSS'],
);

for ($i = 0; $i < sizeof($acl_cat_names); $i++)
{
	$template->assign_block_vars('category', array('CAT_NAME' => $acl_cat_names[$i]));
}

if (file_exists("{$phpbb_root_path}portal.$phpEx"))
{
	$template->assign_var('S_PORTAL_BLOCK', true);
}

$template->assign_var('S_DL_CONFIG', true);

$template->assign_display('dl_config');

?>