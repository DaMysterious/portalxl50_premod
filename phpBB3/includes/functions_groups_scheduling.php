<?php
/**
*
* @package Scheduled Group Membership
* @version $Id: functions_groups_scheduling.php 2008-12-29 DavidIQ $
* @copyright (c) 2009 DavidIQ.com
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
* Add sgm columns to SQL query
*/
function sgm_sql_add_cols($sql, $table_alias = '')
{
	//Add table alias
	$ta = $table_alias . (!empty($table_alias) ? '.' : '');
	$sgm_cols = " {$ta}group_schedule_days, {$ta}group_schedule_start, {$ta}group_schedule_end, {$ta}group_schedule_start_date, {$ta}group_schedule_end_date, ";
	
	return substr_replace($sql, $sgm_cols, strpos($sql, ' '), 1);
}

/**
* Assign the template variables outside of admin
*/
function sgm_template_append(&$template, $data, $block_name)
{
	$template->alter_block_array($block_name, array(
		'S_GROUP_SCHEDULED'			=> ($data['group_schedule_start'] > 0) ? true : false,
		'SCHEDULE_DAYS'				=> schedule_group_days_convert(unserialize($data['group_schedule_days'])),
		'SCHEDULE_START'			=> schedule_group_time_convert($data['group_schedule_start'], $data['group_schedule_start_date']),
		'SCHEDULE_END'				=> schedule_group_time_convert($data['group_schedule_end'], $data['group_schedule_end_date']),
		'SCHEDULE_START_DATE'		=> @gmdate('M d Y', $data['group_schedule_start_date']),
		'SCHEDULE_END_DATE'			=> @gmdate('M d Y', $data['group_schedule_end_date'])), true, 'change');
}

/**
* Check if a user has a scheduled group membership
*/
function user_has_sgm($user_id)
{
	global $db;
	
	$sql = 'SELECT group_schedule_days, group_schedule_start, group_schedule_end
			FROM ' . USER_GROUP_TABLE . "
			WHERE user_id = $user_id";
	$result = $db->sql_query($sql);

	$has_sgm = false;
	while ($row = $db->sql_fetchrow($result))
	{
		if ((isset($row['group_schedule_start']) && !empty($row['group_schedule_start'])) || (isset($row['group_schedule_start_date']) && !empty($row['group_schedule_start_date'])) || (isset($row['group_schedule_end_date']) && !empty($row['group_schedule_end_date'])))
		{
			$has_sgm = true;
			break;
		}
	}
	
	$db->sql_freeresult($result);
	
	return $has_sgm;
}

/**
* Check for group membership scheduling
*/
function group_membership_scheduled($sgm_ary)
{
	global $config;
	
	$schedule_days = $sgm_ary['group_schedule_days'];
	$schedule_start = $sgm_ary['group_schedule_start'];
	$schedule_end = $sgm_ary['group_schedule_end'];
	$start_date = $sgm_ary['group_schedule_start_date'];
	$end_date = $sgm_ary['group_schedule_end_date'];
	
	//Not a scheduled membership
	if (empty($schedule_days) && $schedule_start == 0 && $schedule_end == 0 && $start_date == 0 && $end_date == 0)
	{
		return true;
	}
	
	//Get the weekday number
	$today = @gmdate('w', time() + ($config['board_timezone'] * 3600) + ($config['board_dst'] * 3600));
	//Get the current board time. This prevents the user from manipulating the schedule.
	$now = @gmdate('Hi', time() + ($config['board_timezone'] * 3600) + ($config['board_dst'] * 3600));
	//Get today's UNIX timestamp
	$date_today = (time() + ($config['board_timezone'] * 3600) + ($config['board_dst'] * 3600));
	
	$return = false;
	
	//Check the dates to ensure they're good to go
	if ((($start_date > 0 && $end_date > 0) && (($date_today >= $start_date) && ($date_today <= $end_date)))
		|| ($start_date > 0 && $date_today >= $start_date) || ($end_date > 0 && $date_today <= $end_date))
	{
		$return = true;
	}
	
	$days = unserialize($schedule_days);
	
	//Let's look through the days
	if (sizeof($days))
	{
		foreach ($days as $day)
		{
			//Check if time is within the set timeframe
			if ($day == $today && $schedule_start <= $now && $schedule_end >= $now)
			{
				$return = true;
			}
		}
	}
	elseif ($schedule_start > 0 && ($schedule_start <= $now) && ($schedule_end >= $now))
	{
		$return = true;
	}
	
	return $return;
}

/**
* Build Scheduled Group Membership array for saving
*/
function schedule_group_membership(&$sgm_ary)
{
	global $phpbb_root_path;
	
	$weekday_chk = serialize(request_var('weekday_chk', array(0)));
	$timespan_start_hour = request_var('timespan_start_hour', '');
	$timespan_start_minute = request_var('timespan_start_minute', '');
	$timespan_start_ampm = request_var('timespan_start_ampm', '');
	$timespan_end_hour = request_var('timespan_end_hour', '');
	$timespan_end_minute = request_var('timespan_end_minute', '');
	$timespan_end_ampm = request_var('timespan_end_ampm', '');
	$start_day = request_var('start_day', 0);
	$start_month = request_var('start_month', 0);
	$start_year = request_var('start_year', 0);
	$end_day = request_var('end_day', 0);
	$end_month = request_var('end_month', 0);
	$end_year = request_var('end_year', 0);
	//This is the necessary format for phpBB's validate_date function
	$start_date = sprintf('%2d-%2d-%4d', $start_day, $start_month, $start_year);
	$end_date = sprintf('%2d-%2d-%4d', $end_day, $end_month, $end_year);
	
	$start_time = 0;
	$end_time = 0;
	
	//Validate start and end times
	if (empty($timespan_start_hour) || empty($timespan_start_minute) || empty($timespan_start_ampm))
	{
		return 'START_TIME_INVALID';
	}
	elseif (empty($timespan_end_hour) || empty($timespan_end_minute) || empty($timespan_end_ampm))
	{
		return 'END_TIME_INVALID';
	}
	else
	{
		if (!function_exists('validate_date'))
		{
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		}
		
		if (validate_date($start_date, true))
		{
			return 'START_DATE_INVALID';
		}
		
		if (validate_date($start_date, true))
		{
			return 'END_DATE_INVALID';
		}
		
		if (!date_compare_convert($start_date, $end_date))
		{
			return 'END_DATE_NOT_BEFORE_START';
		}
		
		//Convert the entered times
		$start_time = schedule_group_time_convert(0, 0, $timespan_start_hour, $timespan_start_minute, $timespan_start_ampm);
		$end_time = schedule_group_time_convert(0, 0, $timespan_end_hour, $timespan_end_minute, $timespan_end_ampm);
		
		//The end time cannot happen before the start time so let's make sure of that
		if ($end_time < $start_time)
		{
			return 'END_NOT_BEFORE_START';
		}
		else
		{
			//We passed!  Let's build the array to return to the pointer array.
			$sgm_ary = array('group_schedule_days' 			=> 	$weekday_chk,
							 'group_schedule_start'			=>	$start_time,
							 'group_schedule_end'			=>	$end_time,
							 'group_schedule_start_date'	=>	$start_date,
							 'group_schedule_end_date'		=>	$end_date);
			
			//Now we return false
			return false;
		}
	}
}

/**
* Convert the scheduled time into correct format
*/
function schedule_group_time_convert($schedule_time, $schedule_date = 0, $schedule_hour = 0, $schedule_minute = 0, $schedule_ampm = '')
{
	global $user;
	
	if ($schedule_ampm != '')
	{
		if (strtoupper($schedule_ampm) == $user->lang['TIME_AM'] && $schedule_hour == 12)
		{
			$schedule_hour = 0;
			//This is actually midnight
			if ($schedule_minute == 0)
			{
				return 12;
			}
		}
		elseif (strtoupper($schedule_ampm) == $user->lang['TIME_PM'] && $schedule_hour < 12)
		{
			$schedule_hour += 12;
		}
		
		return ($schedule_hour * 100) + $schedule_minute;
	}
	else
	{
		$date = '';
		
		if ($schedule_date > 0)
		{
			$date = $user->format_date($schedule_date, ' d M Y') . ' ' . $user->lang['AT'] . ' ';
		}
		
		if ($schedule_time == 1)
		{
			return $date . '12:00 ' . $user->lang['TIME_AM'];
		}
		else
		{
			$time = explode('.', number_format($schedule_time / 100, 2));
			$hour = (empty($time[0]) ? 12 : $time[0]);
			$hour = str_pad((($hour > 12) ? (int)$hour - 12 : $hour), 2, '0', STR_PAD_LEFT);
			$minute = $time[1];
			return $date . $hour . ':' . $minute . ' ' . (($schedule_time >= 1200) ? $user->lang['TIME_PM'] : $user->lang['TIME_AM']);
		}
	}
}

/**
* Convert the days scheduled number with actual text
*/
function schedule_group_days_convert($schedule_days_ary)
{
	global $user;
	
	$days = array();
	
	if (!empty($schedule_days_ary))
	{
		foreach ($schedule_days_ary as $day)
		{
			switch ($day)
			{
				case '1':
					$days[] = $user->lang['MON'];
				break;
				case '2':
					$days[] = $user->lang['TUE'];
				break;
				case '3':
					$days[] = $user->lang['WED'];
				break;
				case '4':
					$days[] = $user->lang['THU'];
				break;
				case '5':
					$days[] = $user->lang['FRI'];
				break;
				case '6':
					$days[] = $user->lang['SAT'];
				break;
				case '0':
					$days[] = $user->lang['SUN'];
				break;
			}
		}
		
		return implode(", ", $days);
	}
	else
	{
		return $user->lang['DAILY'];
	}
}

/**
* Build the hours drop-down list
*/
function hours_dropdown($hour = -1)
{
	//Build hours drop-down for group scheduling
	$hours_ddl = '<option value="">--</option>';
	for ($i = 1; $i < 13; $i++)
	{
		$selected = ($i == $hour) ? ' selected="selected"' : '';
		$value = ($i < 10) ? '0' . $i : $i;
		$hours_ddl .= "\n<option value=\"$value\"$selected>$value</option>";
	}
	
	return $hours_ddl;
}

/**
* Build the minutes drop-down list
*/
function minutes_dropdown($minute = -1)
{
	//Build minutes drop-down for group scheduling
	$minutes_ddl = '<option value="">--</option>';
	for ($i = 0; $i < 60; $i += 15)
	{
		$selected = ($i == $minute) ? ' selected="selected"' : '';
		$value = ($i < 10) ? '0' . $i : $i;
		$minutes_ddl .= "\n<option value=\"$value\"$selected>$value</option>";
	}
	
	return $minutes_ddl;
}

/**
* Build the days drop-down list
*/
function days_dropdown($day = 0)
{
	//Build minutes drop-down for group scheduling
	$days_ddl = '<option value="0">--</option>';
	for ($i = 1; $i < 32; $i++)
	{
		$selected = ($i == $day) ? ' selected="selected"' : '';
		$value = ($i < 10) ? '0' . $i : $i;
		$days_ddl .= "\n<option value=\"$value\"$selected>$value</option>";
	}
	
	return $days_ddl;
}

/**
* Build the months drop-down list
*/
function months_dropdown($month = 0)
{
	//Build minutes drop-down for group scheduling
	$month_ddl = '<option value="0">--</option>';
	for ($i = 1; $i < 13; $i++)
	{
		$selected = ($i == $month) ? ' selected="selected"' : '';
		$value = ($i < 10) ? '0' . $i : $i;
		$month_ddl .= "\n<option value=\"$value\"$selected>$value</option>";
	}
	
	return $month_ddl;
}

/**
* Build the years drop-down list
*/
function years_dropdown($year = 0)
{
	//Build minutes drop-down for group scheduling
	$years_ddl = '<option value="0">--</option>';
	$now = getdate();
	for ($i = $now['year']; $i < $now['year'] + 15; $i++)
	{
		$selected = ($i == $year) ? ' selected="selected"' : '';
		$years_ddl .= "\n<option value=\"$i\"$selected>$i</option>";
	}
	
	unset($now);
	
	return $years_ddl;
}

/**
* Compare 2 dates and convert them to unix time
*/
function date_compare_convert(&$start_date, &$end_date)
{
	$start_date_ary = explode('-', $start_date);
	$end_date_ary = explode('-', $end_date);

	foreach ($start_date_ary as $date_part)
	{
		//If any of the date parts are 0 it's not a good date so let's clear the variables
		if ($date_part == '0')
		{
			$start_date = '';
		}
	}
	
	foreach ($end_date_ary as $date_part)
	{
		//If any of the date parts are 0 it's not a good date so let's clear the variables
		if ($date_part == '0')
		{
			$end_date = '';
		}
	}
	
	$start_date_int = !empty($start_date) ? mktime(0, 0, 0, $start_date_ary[1], $start_date_ary[0], $start_date_ary[2]) : 0;
	$end_date_int = !empty($end_date) ? mktime(0, 0, 0, $end_date_ary[1], $end_date_ary[0], $end_date_ary[2]) : 0;
	
	//If the start date is greater than the end date we have a problem
	if ($start_date_int > $end_date_int && $end_date_int > 0)
	{
		return false;
	}
	else
	{
		$start_date = $start_date_int;
		$end_date = $end_date_int;
	}
	
	return true;
}

?>