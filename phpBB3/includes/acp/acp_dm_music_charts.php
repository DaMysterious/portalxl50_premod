<?php
/**
*
* @package acp
* @version $Id: acp_dm_music_charts.php 139 2011-01-29 05:40:09Z femu $
* @copyright (c) 2010 femu - http://die-muellers.org
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
* Here we start the ACP panel
*/
class acp_dm_music_charts
{
	var $u_action;
	function main($id, $mode)
	{
		// Let's first set some globals, includes, variables and the base template
		global $db, $cache, $user, $phpbb_root_path, $phpEx, $template, $u_action, $SID,  $config;

		$action = request_var('action', '');
		$id		= request_var('id', 0);
		$form_action = $this->u_action. '&amp;action=add';
		$lang_mode = $user->lang['DM_MC_ADD'];

		$template->assign_vars(array(
			'BASE'		=> $this->u_action,
		));

		// Add form key for S_FORM_TOKEN
		add_form_key('acp_music_charts');

		// Here we set the main switches to use within the ACP
		switch ($mode)
		{
			// Set the main switch for editing videos
			case 'manage_charts':
				$this->page_title = 'DM_MC_MANAGE';
				$this->tpl_name = 'acp_dm_mc_manage';

				// Read out config values 
				$sql = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_CONFIG_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$dm_mc_config[$row['config_name']] = $row['config_value'];
				}
				$db->sql_freeresult($result);
				
				$start = request_var('start', 0);
				$number = $dm_mc_config['chart_acp_page'];
				$sort_days	= request_var('st', 0);
				$sort_key	= request_var('sk', 'chart_song_name');
				$sort_dir	= request_var('sd', 'ASC');
				$limit_days = array(0 => $user->lang['DM_MC_ALL_CHARTS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);

				$sort_by_text = array('n' => $user->lang['DM_MC_SONG_TITLE'], 'a' => $user->lang['DM_MC_SONG_ARTIST'], 'f' => $user->lang['DM_MC_FROM_NAME'], 'p' => $user->lang['DM_MC_LAST_RANK']);
				$sort_by_sql = array('n' => 'chart_song_name', 'a' => 'chart_artist', 'f' => 'chart_poster_id', 'p' => 'chart_last_pos');

				$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
				gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);
				$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

				// Count number of charts
				$sql = 'SELECT COUNT(chart_id) AS total_charts
					FROM ' . DM_MUSIC_CHARTS_TABLE;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$total_charts = $row['total_charts'];
				$db->sql_freeresult($result);

				// List all charts
				$sql = 'SELECT *
					FROM ' . DM_MUSIC_CHARTS_TABLE . '
					ORDER by ' . $sql_sort_order;
				$result = $db->sql_query_limit($sql, $number, $start);
				
				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('charts', array(
						'TITLE'			=> $row['chart_song_name'],
						'ARTIST'		=> $row['chart_artist'],
						'ALBUM'			=> $row['chart_album'],
						'PICTURE'		=> $row['chart_picture'],
						'YEAR'			=> $row['chart_year'],
						'LAST_RANK'		=> $row['chart_last_pos'],
						'URL'			=> $row['chart_website'],
						'VIDEO'			=> $row['chart_video'],
						'VIDEO_ID'		=> $row['chart_video_no'],
						'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' . $row['chart_id'] . '&amp;sk=' . $sort_key .'&amp;sd=' . $sort_dir,
						'U_DEL'			=> $this->u_action . '&amp;action=delete&amp;id=' .$row['chart_id'] . '&amp;sk=' . $sort_key .'&amp;sd=' . $sort_dir,
						'U_VIDEO_PLAY'	=> append_sid("{$phpbb_root_path}dm_music_charts/showvideo.$phpEx", "v={$row['chart_id']}"),
						)
					);
				}
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'S_CHARTS_ACTION' 	=> $this->u_action,
					'S_SELECT_SORT_DIR'	=> $s_sort_dir,
					'S_SELECT_SORT_KEY'	=> $s_sort_key,
					'PAGINATION' 		=> generate_pagination($this->u_action . '&amp;sk=' . $sort_key .'&amp;sd=' . $sort_dir, $total_charts, $number, $start, true),
					'L_MODE_TITLE'		=> $lang_mode,
					'U_EDIT_ACTION'		=> $this->u_action,
					'PAGE_NUMBER'		=> on_page($total_charts, $number, $start),
					'TOTAL_CHARTS'		=> ($total_charts == 1) ? $user->lang['DM_MC_SINGLE'] : sprintf($user->lang['DM_MC_MULTI'], $total_charts),
				));

				// Now let's define, what to do within the module Edit Charts
				switch ($action)
				{
					// Edit an existing chart entry
					case 'edit':
						$this->page_title = 'DM_MC_EDIT';
						$this->tpl_name = 'acp_dm_mc_edit';
						$form_action = $this->u_action. '&amp;action=update';
						$lang_mode = $user->lang['DM_MC_MANAGE'];

						$action = (!isset($_GET['action'])) ? '' : $_GET['action'];
						$action = ((isset($_POST['submit']) && !$_POST['id']) ? 'add' : $action );

						$id = request_var('id', '');
						$sk = request_var('sk', '');
						$sd = request_var('sd', '');

						$sql = 'SELECT *
							FROM ' . DM_MUSIC_CHARTS_TABLE . ' 
							WHERE chart_id = ' . $id;
						$result = $db->sql_query_limit($sql,1);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						decode_message($row['chart_song_name'], $row['bbcode_uid']);
						$chart_id = $row['chart_id'];

						if (file_exists($phpbb_root_path . 'dm_video/index.php'))
						{
							$dm_video = true;
						}
						else
						{
							$dm_video = false;
						}

						$template->assign_vars(array(
							'S_DM_VIDEO'	=> $dm_video,
							'ID'			=> $chart_id,
							'SK'			=> $sk,
							'SD'			=> $sd,
							'TITLE'			=> $row['chart_song_name'],
							'ARTIST'		=> $row['chart_artist'],
							'ALBUM'			=> $row['chart_album'],
							'PICTURE'		=> $row['chart_picture'],
							'YEAR'			=> $row['chart_year'],
							'URL'			=> $row['chart_website'],
							'VIDEO'			=> $row['chart_video'],
							'VIDEO_ID'		=> $row['chart_video_no'],
						));

						$template->assign_vars(array(
							'U_ACTION'		=> $form_action,
							'L_MODE_TITLE'	=> $lang_mode,
						));
					break;

					// Change an existing chart entry
					case 'update':
						$title 		= utf8_normalize_nfc(request_var('chart_song_name', '', true));
						$artist		= utf8_normalize_nfc(request_var('chart_artist', '', true));
						$album		= utf8_normalize_nfc(request_var('chart_album', '', true));
						$year		= request_var('chart_year', '');
						$picture	= request_var('chart_picture', '');
						$url		= request_var('chart_website', '');
						$video		= request_var('chart_video', '');
						$video_id	= request_var('chart_video_no', 0);
						$back_sort	= request_var('sd', '');
						$back_key	= request_var('sk', '');
						
						$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
						$allow_bbcode = $allow_urls = $allow_smilies = true;
						generate_text_for_storage($title, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

						$sql_ary = array(
							'chart_song_name'	=> $title,
				    		'chart_artist'		=> $artist,
							'chart_album'		=> $album,
							'chart_year'		=> $year,
				    		'chart_picture'		=> $picture,
							'chart_website'		=> $url,
							'chart_video'		=> $video,
							'chart_video_no'	=> $video_id,
						    'bbcode_uid'		=> $uid,
						    'bbcode_bitfield'	=> $bitfield,
						    'bbcode_options'	=> $options,
						);

						if ($title == '' || $artist == '')
						{
							if ($title == '' && $artist == '')
							{
								trigger_error($user->lang['DM_MC_NEED_DATA'] . adm_back_link($this->u_action));	
							}
							elseif ($title == '')
							{
								trigger_error($user->lang['DM_MC_NEED_TITLE'] . adm_back_link($this->u_action));	
							}
							elseif ($artist == '')
							{
								trigger_error($user->lang['DM_MC_NEED_ARTIST'] . adm_back_link($this->u_action));	
							}
						}
						else
						{
							$db->sql_query('UPDATE ' . DM_MUSIC_CHARTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' WHERE chart_id = ' . $id);

							add_log('admin', 'LOG_ADMIN_CHART_UPDATED', $title);
							trigger_error($user->lang['DM_MC_UPDATED'] . adm_back_link($this->u_action . '&amp;sk=' . $back_key .'&amp;sd=' . $back_sort));
						}
					break;

					// Delete an existing chart entry
					case 'delete':
						if (confirm_box(true))
						{
							$sk = request_var('sk', '');
							$sd = request_var('sd', '');

							$sql = 'SELECT chart_song_name
								FROM ' . DM_MUSIC_CHARTS_TABLE . '
								WHERE chart_id = ' . $id;
							$result = $db->sql_query_limit($sql,1);
							$title = $db->sql_fetchfield('chart_song_name');
							$db->sql_freeresult($result);

							$sql = 'DELETE FROM ' . DM_MUSIC_CHARTS_TABLE . '
								WHERE chart_id = ' . $id;
							$db->sql_query($sql);

							add_log('admin', 'LOG_ADMIN_CHART_DELETED', $title);
							trigger_error($user->lang['DM_MC_DELETED'] . adm_back_link($this->u_action . '&amp;sk=' . $sk .'&amp;sd=' . $sd));
						}
						else
						{
							confirm_box(false, $user->lang['DM_MC_REALLY_DELETE'], build_hidden_fields(array(
								'chart_id'	=> $id,
								'action'	=> 'delete',
								))
							);
						}
					break;
				}

			break;

			// Set the main switch for the config
			case 'config':
				$submit = (isset($_POST['submit'])) ? true: false;
				$this->page_title = 'DM_MC_CONFIG';
				$this->tpl_name = 'acp_dm_mc_config';

				// Clear the cache
				$cache->destroy('config');

				if ($submit == true)
				{
					if (!check_form_key('acp_music_charts'))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}				

					// Values for phpbb_config
					$chart_start_time	= request_var('chart_start_time', 0);
					$chart_period		= request_var('chart_period', 0);

					// Update values in phpbb_config
					if ($chart_start_time != $config['chart_start_time'])
					{
						set_config('chart_start_time', $chart_start_time);
					}

					if ($chart_period != $config['chart_period'])
					{
						set_config('chart_period', $chart_period);
					}

					$sql = 'SELECT *
						FROM ' . DM_MUSIC_CHARTS_CONFIG_TABLE;
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$config_name = $row['config_name'];
						$config_value = request_var($config_name, '', true);

						$sql = 'UPDATE ' . DM_MUSIC_CHARTS_CONFIG_TABLE . " 
							SET config_value = '$config_value'
							WHERE config_name = '$config_name'";
						$db->sql_query($sql);
					}

					add_log('admin', 'LOG_ADMIN_CHART_CONFIG_UPDATED');
					trigger_error($user->lang['DM_MC_CONFIG_UPDATED'] . adm_back_link($this->u_action));

					// Clear the cache
					$cache->destroy('config');
				}

				$sql = 'SELECT config_name, config_value
					FROM ' . DM_MUSIC_CHARTS_CONFIG_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$new[$row['config_name']] = $row['config_value'];
				}

				$board_dst = $config['board_dst'];
				$user_dst = $user->data['user_dst'];

				// Check if UPS is installed and active
				if (defined('IN_ULTIMATE_POINTS') && $config['points_enable'])
				{
					$template->assign_vars(array(
						'S_UPS_INSTALLED'				=> true,
						'CHART_UPS_POINTS'				=> $new['chart_ups_points'],
						'UPS'							=> sprintf($user->lang['DM_MC_UPS'], $config['points_name']),
						'UPS_EXPLAIN'					=> sprintf($user->lang['DM_MC_UPS_EXPLAIN'], $config['points_name']),
						'POINTS'						=> sprintf($user->lang['DM_MC_RANKING'], $config['points_name']),
						'POINTS_EXPLAIN'				=> sprintf($user->lang['DM_MC_RANKING_EXPLAIN'], $config['points_name']),
						'1ST'							=> sprintf($user->lang['DM_MC_FIRST'], $config['points_name']),
						'2ND'							=> sprintf($user->lang['DM_MC_SECOND'], $config['points_name']),
						'3RD'							=> sprintf($user->lang['DM_MC_THIRD'], $config['points_name']),
						'POINTS_PER_VOTE_DESC'			=> sprintf($user->lang['DM_MC_POINTS_PER_VOTE'], $config['points_name']),
						'POINTS_PER_VOTE_DESC_EXPLAIN'	=> sprintf($user->lang['DM_MC_POINTS_PER_VOTE_EXPLAIN'], $config['points_name']),
						'POINTS_NAME'					=> $config['points_name'],
						'POINTS_VOTERS_BONUS'			=> sprintf($user->lang['DM_MC_VOTERS_POINTS'], $config['points_name']),
						'POINTS_VOTERS_BONUS_EXPLAIN'	=> sprintf($user->lang['DM_MC_VOTERS_POINTS_EXPLAIN'], $config['points_name']),
					));
				}

				// Check PM username
				if ($new['pm_user'] > 0)
				{
					$sql1 = 'SELECT user_id, username
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . $new['pm_user'];
					$result1 = $db->sql_query_limit($sql1, 1);
					$row1 = $db->sql_fetchrow($result1);
					$db->sql_freeresult($result);
					$username = $row1['username'];
				}
				else
				{
					$username = '';
				}

				// Check last bonus winner
				if ($new['last_voters_winner_id'] > 0)
				{
					$sql = 'SELECT username
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . $new['last_voters_winner_id'];
					$result = $db->sql_query_limit($sql, 1);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if ($row)
					{
						$template->assign_vars(array(
							'S_BONUS_WINNER'	=> true,
							'BONUS_WINNER_NAME'	=> $row['username'],
						));
					}
				}
				// Grab forum list  for announcing new songs
				$sql = 'SELECT forum_id, forum_name
					FROM ' . FORUMS_TABLE . '
					ORDER by forum_name ASC';
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrowset($result);
				$forum_list = '';
				for ( $i = 0, $size = sizeof($row); $i < $size ; $i ++ )
				{
					$forum_list .= '<option value = "' . $row[$i]['forum_id'] . '" ' .  ($row[$i]['forum_id'] == $new['announce_forum'] ? 'selected' : '') . '>' . $row[$i]['forum_name'] . '</option>';
				}

				// Send all values to the template
				$template->assign_vars(array(
					'CHART_MAX_ENTRIES'				=> $new['chart_max_entries'],
					'CHART_NUM_TOP'					=> $new['chart_num_top'],
					'CHART_NUM_LAST'				=> $new['chart_num_last'],
					'CHART_ACP_PAGE'				=> $new['chart_acp_page'],
					'CHART_USER_PAGE'				=> $new['chart_user_page'],
					'CHART_START_TIME'				=> $config['chart_start_time'],
					'CHART_START_TIME_READABLE'		=> $user->format_date($config['chart_start_time']),
					'CHART_PERIOD'					=> $config['chart_period'],
					'CHART_PERIOD_READABLE'			=> ($config['chart_period']/604800 == 1) ? $user->lang['DM_MC_WEEK'] : sprintf($user->lang['DM_MC_WEEKS'], $config['chart_period']/604800),
					'CHART_NEXT_RESET'				=> $config['chart_start_time'] + $config['chart_period'],
					'CHART_NEXT_RESET_READABLE'		=> $user->format_date(($config['chart_start_time'] + $config['chart_period'])),
					'CHART_CHECK_1'					=> $new['chart_check_1'],
					'CHART_CHECK_2'					=> $new['chart_check_2'],
					'CHART_CHECK_TIME'				=> $new['chart_check_time'],
					'CHART_1ST_PLACE'				=> $new['chart_1st_place'],
					'CHART_2ND_PLACE'				=> $new['chart_2nd_place'],
					'CHART_3RD_PLACE'				=> $new['chart_3rd_place'],
					'DEFAULT_SORT'					=> $new['default_sort'],
					'REQUIRED_1'					=> $new['required_1'],
					'REQUIRED_2'					=> $new['required_2'],
					'REQUIRED_3'					=> $new['required_3'],
					'REQUIRED_4'					=> $new['required_4'],
					'REQUIRED_5'					=> $new['required_5'],
					'PM_USER'						=> $new['pm_user'],
					'PM_ENABLE'						=> $new['pm_enable'],
					'PM_USER_NAME'					=> $username,
					'ANNOUNCE_FORUM_LIST'			=> $forum_list,
					'ANNOUNCE_ENABLE'				=> $new['announce_enable'],
					'POINTS_PER_VOTE'				=> $new['points_per_vote'],
					'VOTERS_POINTS'					=> $new['voters_points'],
					'LAST_VOTERS_WINNER_ID'			=> $new['last_voters_winner_id'],
					'WINNERS_PER_PAGE'				=> $new['winners_per_page'],
					'L_MODE_TITLE'					=> $lang_mode,
					'U_ACTION'						=> $this->u_action,
				));
			break;
		}
	}

	/*
	* Functions needed in the ACP
	*/
	
	// Function for pagination
	function generate_page($replies, $url, $top)
	{
		global $config, $user;

		// Make sure $per_page is a valid value
		$per_page = ($top <= 0) ? 1 : $top;

		if (($replies) > $per_page)
		{
			$total_pages = ceil(($replies + 1) / $per_page);
			$pagination = '';

			$times = 1;
			for ($j = 0; $j < $replies + 1; $j += $per_page)
			{
				$pagination .= '<a href="' . $url . '&amp;start=' . $j . '">' . $times . '</a>';
				if ($times == 1 && $total_pages > 5)
				{
					$pagination .= ' ... ';

					// Display the last three pages
					$times = $total_pages - 3;
					$j += ($total_pages - 4) * $per_page;
				}
				else if ($times < $total_pages)
				{
					$pagination .= '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
				}
				$times++;
			}
		}
		else
		{
			$pagination = '1';
		}

		return $pagination;
	}
}

?>