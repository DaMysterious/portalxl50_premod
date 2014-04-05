<?php
/*
*
* @name donate_functions_V3.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donate_functions_V3.php,v 1.1.0 2012/05/28 portalxl group Exp $
*
* @copyright (c) Zou Xiong - Enterprise admin@loewen.com.sg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

 // added at phpBB 2.0.12 to fix a bug in PHP 4.3.10 (only supporting charlist in php >= 4.1.0)
 function donate_rtrim($str, $charlist = false)
 {
 	if ($charlist === false)
 	{
 		return rtrim($str);
 	}
 	
 	$php_version = explode('.', PHP_VERSION);
 
 	// php version < 4.1.0
 	if ((int) $php_version[0] < 4 || ((int) $php_version[0] == 4 && (int) $php_version[1] < 1))
 	{
 		while ($str{strlen($str)-1} == $charlist)
 		{
 			$str = substr($str, 0, strlen($str)-1);
 		}
 	}
 	else
 	{
 		$str = rtrim($str, $charlist);
 	}
 
 	return $str;
 }
 
function donate_clean_str4sql_V3($input)
{
	$the_string = htmlentities(str_replace("\'", "'", trim($input)), ENT_QUOTES, 'UTF-8');
	$the_string = donate_rtrim($the_string, "\\");
	$the_string = str_replace("'", "\'", $the_string);

	$from = array("\'");
	$to = array("''");
	$output = str_replace($from, $to, $the_string);
	return $output;
}

function donate_cal_cash_exchange_rate_V3($currency, $configuration)
{
	$convertor = 1.0;
	if(strcasecmp($currency, 'USD') == 0)
	{
		$convertor = $configuration['usd_to_primary'];
	}
	else if(strcasecmp($currency, 'EUR') == 0)
	{
		$convertor = $configuration['eur_to_primary'];
	}
	else if(strcasecmp($currency, 'GBP') == 0)
	{
		$convertor = $configuration['gbp_to_primary'];
	}
	else if(strcasecmp($currency, 'CAD') == 0)
	{
		$convertor = $configuration['cad_to_primary'];
	}
	else if(strcasecmp($currency, 'JPY') == 0)
	{
		$convertor = $configuration['jpy_to_primary'];
	}
	else if(strcasecmp($currency, 'AUD') == 0)
	{
		$convertor = $configuration['aud_to_primary'];
	}
	else if(strcasecmp($currency, 'CZK') == 0)
	{
		$convertor = $configuration['czk_to_primary'];
	}
	else if(strcasecmp($currency, 'DKK') == 0)
	{
		$convertor = $configuration['dkk_to_primary'];
	}
	else if(strcasecmp($currency, 'HKD') == 0)
	{
		$convertor = $configuration['hkd_to_primary'];
	}
	else if(strcasecmp($currency, 'HUF') == 0)
	{
		$convertor = $configuration['huf_to_primary'];
	}
	else if(strcasecmp($currency, 'NZD') == 0)
	{
		$convertor = $configuration['nzd_to_primary'];
	}
	else if(strcasecmp($currency, 'NOK') == 0)
	{
		$convertor = $configuration['nok_to_primary'];
	}
	else if(strcasecmp($currency, 'PLN') == 0)
	{
		$convertor = $configuration['pln_to_primary'];
	}
	else if(strcasecmp($currency, 'SGD') == 0)
	{
		$convertor = $configuration['sgd_to_primary'];
	}
	else if(strcasecmp($currency, 'SEK') == 0)
	{
		$convertor = $configuration['sek_to_primary'];
	}
	else if(strcasecmp($currency, 'CHF') == 0)
	{
		$convertor = $configuration['chf_to_primary'];
	}
	return ($convertor+0.00);
}

function donate_display_currency_type_V3($input_currency)
{
	global $user;
	
	$output_currency = $input_currency;
	if(strcasecmp($input_currency, 'USD') == 0)
	{
		$output_currency = $user->lang['CURRENCY_USD'];
	}
	else if(strcasecmp($input_currency, 'AUD') == 0)
	{
		$output_currency = $user->lang['CURRENCY_AUD'];
	}
	else if(strcasecmp($input_currency, 'CAD') == 0)
	{
		$output_currency = $user->lang['CURRENCY_CAD'];
	}
	else if(strcasecmp($input_currency, 'CZK') == 0)
	{
		$output_currency = $user->lang['CURRENCY_CZK'];
	}
	else if(strcasecmp($input_currency, 'DKK') == 0)
	{
		$output_currency = $user->lang['CURRENCY_DKK'];
	}
	else if(strcasecmp($input_currency, 'EUR') == 0)
	{
		$output_currency = $user->lang['CURRENCY_EUR'];
	}
	else if(strcasecmp($input_currency, 'HKD') == 0)
	{
		$output_currency = $user->lang['CURRENCY_HKD'];
	}
	else if(strcasecmp($input_currency, 'HUF') == 0)
	{
		$output_currency = $user->lang['CURRENCY_HUF'];
	}
	else if(strcasecmp($input_currency, 'NZD') == 0)
	{
		$output_currency = $user->lang['CURRENCY_NZD'];
	}
	else if(strcasecmp($input_currency, 'NOK') == 0)
	{
		$output_currency = $user->lang['CURRENCY_NOK'];
	}
	else if(strcasecmp($input_currency, 'PLN') == 0)
	{
		$output_currency = $user->lang['CURRENCY_PLN'];
	}
	else if(strcasecmp($input_currency, 'GBP') == 0)
	{
		$output_currency = $user->lang['CURRENCY_GBP'];
	}
	else if(strcasecmp($input_currency, 'SGD') == 0)
	{
		$output_currency = $user->lang['CURRENCY_SGD'];
	}
	else if(strcasecmp($input_currency, 'SEK') == 0)
	{
		$output_currency = $user->lang['CURRENCY_SEK'];
	}
	else if(strcasecmp($input_currency, 'CHF') == 0)
	{
		$output_currency = $user->lang['CURRENCY_CHF'];
	}
	else if(strcasecmp($input_currency, 'JPY') == 0)
	{
		$output_currency = $user->lang['CURRENCY_JPY'];
	}
	return $output_currency;
}


function donate_process_payment_V3(&$input_array)
{
	global $phpbb_root_path, $phpEx, $config, $db, $table_prefix;

	$item_number 		= trim($input_array['ITEM_NUMBER']);
	$payment_status 	= trim($input_array['PAYMENT_STATUS']);
	$payment_currency 	= trim($input_array['MC_CURRENCY']);
	$payment_amount 	= $input_array['MC_GROSS'] + 0.00;
	$txn_id 			= trim($input_array['TXN_ID']);
	$payer_email 		= trim($input_array['PAYER_ACCT']);
	$receiver_email 	= trim($input_array['RECEIVER_ACCT']);
	$system_acct_one 	= trim($input_array['SYSTEM_ACCOUNT_1']);
	$system_acct_two 	= trim($input_array['SYSTEM_ACCOUNT_2']);
	
	$err_flag 	= 0;
	$pos 		= strpos($item_number, '-', 0);
	$user_id 	= 0;
	$anonymous 	= 0;
	
	if($pos !== false)
	{
		$user_id = intval(substr($item_number, 0, $pos));
		$anonymous = intval(substr($item_number, $pos + 1));
	}
	
	if($user_id <= 0)
	{
		$user_id = ANONYMOUS;
	}
	
	if($anonymous != 1)
	{
		$anonymous = 0;
	}

	$sql = "SELECT * FROM " . USERS_TABLE . " WHERE user_id = " . $user_id;
	if ( !($result = $db->sql_query($sql)) )
	{
		$user_id = ANONYMOUS;
	}
	$donateuserdata = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	if($donateuserdata['user_id'] <= 0)
	{
		$user_id = ANONYMOUS;
	}

	//update the payee's account with payment
	$poster_convertor = donate_cal_cash_exchange_rate_V3($payment_currency, $config) + 0;
	if($poster_convertor <= 0)
	{
		$poster_convertor = 1.0;
	}
	
	$donate_mny_payee = ($payment_amount + 0.00) / ($poster_convertor);

	$payment_amount = $donate_mny_payee;
	$payment_currency = $config['paypal_currency_code'];
	
	if( (strcasecmp($receiver_email, trim($system_acct_one)) != 0) && (strcasecmp($receiver_email, trim($system_acct_two)) != 0))
	{
			$err_flag = 1;
			$err_msg = "1. Is " . $receiver_email . " your paypal account?";		
	}

	if($err_flag == 0 && strcasecmp($payment_status, 'Completed') == 0)
	{
		//if previously has a record with same txn_id and its status is completed. exit
		$sql = "SELECT COUNT(*) as num FROM " . ACCT_HIST_TABLE . " WHERE txn_id = '" . donate_clean_str4sql_V3($txn_id) . "'";
		if ( !($resulta = $db->sql_query($sql)) )
		{
			//do nothing
		}
		if( !($rowa = $db->sql_fetchrow($resulta)) )
		{
			//do nothing
		}
		if($rowa['num'] > 0)
		{
			$err_flag = 1;
			$err_msg = "2. The same transaction id has already existed";
		}
		//end if repviously has a record with same txn_id and its status is completed. exit
		
		if($err_flag == 0)
		{
			if($user_id > 0)
			{
				if(intval($config['donate_to_points']) > 0)
				{
					$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points + " . (intval(intval($config['donate_to_points']) * ($payment_amount + 0.00))) . " WHERE user_id = " . $user_id;
					if ( !($result = $db->sql_query($sql)) )
					{
						//do nothing
					}
					
				}
				else if(intval($config['donate_to_posts']) > 0)
				{
					$sql = "UPDATE " . USERS_TABLE . " SET user_posts = user_posts + " . (intval(intval($config['donate_to_posts']) * ($payment_amount + 0.00))) . " WHERE user_id = " . $user_id;
					if ( !($result = $db->sql_query($sql)) )
					{
						//do nothing
					}
					
				}
			
				$sql = "SELECT SUM(money) FROM " . ACCT_HIST_TABLE . " WHERE comment LIKE 'Donation from%%' AND user_id = " . $user_id;
				$amount_donated = ($payment_amount + 0.00);
				if($result = $db->sql_query($sql))
				{
					if($row = $db->sql_fetchrow($result))
					{
						$amount_donated = $amount_donated + $row["SUM(money)"];
					}
				}

				$grptojoin = 0;
				if( intval($config['donate_to_grp_one']) > 0
					&& ($config['to_grp_one_amount'] + 0.00) < ($amount_donated) )
				{
					$grptojoin = intval($config['donate_to_grp_one']);
				}
				if(intval($config['donate_to_grp_two']) > 0
					&& ($config['to_grp_two_amount'] + 0.00) < ($amount_donated)
					&& ($config['to_grp_one_amount'] + 0.00) < ($config['to_grp_two_amount'] + 0.00) )
				{
					$grptojoin = intval($config['donate_to_grp_two']);
				}
				if($grptojoin > 0)
				{
					$sql = "SELECT * FROM " . USER_GROUP_TABLE . " WHERE group_id = " . $grptojoin . " AND user_id = " . $user_id;
						// query database 
					$need_to_add = 1;
					if ( ($result = $db->sql_query($sql)) )
					{
						if ( $row = $db->sql_fetchrow($result) )
						{
							if($row['user_pending'] == 0)
							{
								$need_to_add = 0;
							}
							if($row['user_pending'] != 0)
							{
								$need_to_add = 2; //need update
							}
						}
					}
					
					if($need_to_add == 1)
					{
						//add to the donor group
						$sql = "INSERT INTO " . USER_GROUP_TABLE . " (user_id, group_id, user_pending) VALUES ($user_id, $grptojoin, 0)";

						if( !($result = $db->sql_query($sql)) )
						{
							//do nothing
						}
						else
						{
							//update the donor default group
							$sql = "UPDATE " . USERS_TABLE . " SET group_id = " . $grptojoin . ", user_colour = (SELECT group_colour from " . GROUPS_TABLE . " WHERE group_id = " . $grptojoin . ") WHERE user_id = " . $user_id;
					
							if( !($result = $db->sql_query($sql)) )
							{
								//do nothing
							}
						}
						//end add to the donor group
							
					}
					if($need_to_add == 2)
					{
						//update the donor group
						$sql = "UPDATE " . USER_GROUP_TABLE . " SET user_pending = 0 WHERE group_id = " . $grptojoin . " AND user_id = " . $user_id;

						if( !($result = $db->sql_query($sql)) )
						{
							//do nothing
						}
						else
						{
							//update the donor default group
							$sql = "UPDATE " . USERS_TABLE . " SET group_id = " . $grptojoin . ", user_colour = (SELECT group_colour from " . GROUPS_TABLE . " WHERE group_id = " . $grptojoin . ") WHERE user_id = " . $user_id;
					
							if( !($result = $db->sql_query($sql)) )
							{
								//do nothing
							}
						}
						//end update the donor group
					}
				}
				
				if(intval($config['donor_rank_id']) > 0
					&& $anonymous != 1)
				{
					$sql = "UPDATE " . USERS_TABLE . " SET user_rank = " . intval($config['donor_rank_id']) . " WHERE user_id = " . $user_id;

					if ( !($result = $db->sql_query($sql)) )
					{
						//do nothing
					}
					
				}
				
			}
				
		}

	}
	else if($err_flag == 0)
	{
		$err_flag = 1;
		$err_msg = "3. Status: " . $payment_status;
	}
	
	if($anonymous == 1)
	{
		$user_id = ANONYMOUS;
	}
	if($err_flag == 0)
	{
		$sql = "INSERT INTO " . ACCT_HIST_TABLE . "(user_id, post_id, money, plus_minus, currency, date, comment, site, status, txn_id) VALUES(" . $user_id . ", 0, " . ($payment_amount + 0.00) . ", -1, '" . donate_clean_str4sql_V3($payment_currency) . "', " . time() . ", 'Donation from " . donate_clean_str4sql_V3($payer_email) . ", thank you!', '" . $config['sitename'] . "', '" . donate_clean_str4sql_V3($payment_status) . "', '" . donate_clean_str4sql_V3($txn_id) . "')";
		if ( !($result = $db->sql_query($sql)) )
		{
			//do nothing
		}		
	}
	else
	{
		$sql = "INSERT INTO " . ACCT_HIST_TABLE . "(user_id, post_id, money, plus_minus, currency, date, comment, site, status, txn_id) VALUES(" . $user_id . ", 0, " . ($payment_amount + 0.00) . ", -1, '" . donate_clean_str4sql_V3($payment_currency) . "', " . time() . ", 'For donation by: " . donate_clean_str4sql_V3($payer_email) . ", " . $err_msg . ".', '" . $config['sitename'] . "', '" . donate_clean_str4sql_V3($payment_status) . "', '" . donate_clean_str4sql_V3($txn_id) . "')";
		if ( !($result = $db->sql_query($sql)) )
		{
			//do nothing
		}				
	}

	return $err_flag;
}

function generate_donation_title()
{
	global $db, $phpEx, $theme, $user, $config, $phpbb_root_path;
	
	$donordesc = '';
	$style_color = '';
	
	if( strlen($config['donate_description']) > 0)
	{
		if(strlen($donordesc) <= 0)
		{
			$donordesc .= '&nbsp;[&nbsp;';
		}
		$donordesc .= sprintf($config['donate_description']);
	}
	
	if( intval($config['donate_cur_goal']) > 0)
	{
		$donorswhere = '';
	
		//format can only be 2004/08/04 yyyy/mm/dd
		$starttime = 0;
		$endtime = 0;
		$donatetime = '';
		if(strlen($config['donate_start_time']) == 10)
		{
			$starttime = mktime(0, 0, 0, substr($config['donate_start_time'], 5, 2), substr($config['donate_start_time'], 8, 2), substr($config['donate_start_time'], 0, 4) );
		}
		if(strlen($config['donate_end_time']) == 10)
		{
			$endtime = mktime(0, 0, 0, substr($config['donate_end_time'], 5, 2), substr($config['donate_end_time'], 8, 2), substr($config['donate_end_time'], 0, 4) );

			//$donatetime .= ' Ends <b>' . $config['donate_end_time'] . '</b>' . ';';
			$donatetime .= ' Ends <b>' . $user->format_date($endtime, 'D M d, Y') . '</b>' . ' - ';
		}
		
		$donordesc .= $donatetime;	
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
		
		$curcollected = 0;
		$sql = "SELECT SUM(a.money) FROM " . ACCT_HIST_TABLE . " a, " . USERS_TABLE . " u" . " WHERE a.comment LIKE '%' AND status = 'Completed' AND u.user_id = a.user_id" . 
			"$donorswhere";
		if($result = $db->sql_query($sql))
		{
			if($row = $db->sql_fetchrow($result))
			{
				$curcollected = $row["SUM(a.money)"];
			}
		}
		
		if(strlen($donordesc) <= 0)
		{
			$donordesc .= '&nbsp;[&nbsp;';
		}
		$donordesc .= sprintf($user->lang['WE_HAVE_COLLECT'], $curcollected, $config['donate_cur_goal'] . ' ' . $config['paypal_currency_code'] ) . " ";
	}
	
	if( strlen($donordesc) > 0)
	{
		$donordesc .= '<a href="' . append_sid("{$phpbb_root_path}donors.$phpEx", 'mode=viewcurrent') . '"' . $style_color .'>' . $user->lang['CURRENT_DONORS'] . '</a>';
		$donordesc .= '&nbsp;]';
	}
	
	$donationtitle = "";
	if(intval($config['list_top_donors']) == 1)
	{
		$donationtitle = sprintf($user->lang['L_TOP_DONORS_TITLE'], $config['dislay_x_donors']) . '<br /> ' . $donordesc;
	}
	else
	{
		$donationtitle = sprintf($user->lang['L_LAST_DONORS'], $config['dislay_x_donors']) . '<br /> ' . $donordesc;
	}
	
	return $donationtitle;
}

function donate_bar()
{
	global $db, $phpEx, $theme, $user, $config, $phpbb_root_path;
	
	if( intval($config['donate_cur_goal']) > 0)
	{
		$donorswhere = '';
	
		//format can only be 2004/08/04 yyyy/mm/dd
		$starttime = 0;
		$endtime = 0;
		$donatetime = '';
		if(strlen($config['donate_start_time']) == 10)
		{
			$starttime = mktime(0, 0, 0, substr($config['donate_start_time'], 5, 2), substr($config['donate_start_time'], 8, 2), substr($config['donate_start_time'], 0, 4) );
		}
		if(strlen($config['donate_end_time']) == 10)
		{
			$endtime = mktime(0, 0, 0, substr($config['donate_end_time'], 5, 2), substr($config['donate_end_time'], 8, 2), substr($config['donate_end_time'], 0, 4) );

			//$donatetime .= ' Ended at <b>' . $config['donate_end_time'] . '</b>' . ';';
			$donatetime .= ' Ended at <b>' . $user->format_date($endtime) . '</b>' . ';';
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
		$curcollected = 0;
		$sql = "SELECT SUM(a.money) FROM " . ACCT_HIST_TABLE . " a, " . USERS_TABLE . " u" . " WHERE a.comment LIKE '%' AND status = 'Completed' AND u.user_id = a.user_id" . 
			"$donorswhere";
		if($result = $db->sql_query($sql))
		{
			if($row = $db->sql_fetchrow($result))
			{
				$curcollected = $row["SUM(a.money)"];
			}
		}
	}
	
	$valuta 			= $config['paypal_currency_code'];
	$objective 			= $config['donate_cur_goal'];
	$donate_reached 	= 5*(( 100 * $curcollected ) / $objective );
	$donate_fund 		= ( 500 - $donate_reached );
	$percent 			= (( 100 * $curcollected ) / $objective );
	
	if ( $percent == 0 ) { $percentuale = ""; } else { $percentuale = $percent; }
	$donationbar = '<div style="diplay:block;background:#FF0000;border:1px solid;height:20px;width:' . $donate_reached . 'px;float:left;span:2px 0 10px 0;"><p style="font-weight:bold;color:#FFFFFF">&nbsp;' . $percentuale . '&#37;</p></div><div align="center" style="diplay:block;background:#CCCCCC;border:1px solid;height:20px;float:left;width:' . $donate_fund . 'px;span:2px 0 10px 0;border-left:0px;"><p style="font-weight:bold;color:#000000;">&nbsp;' . $objective . ' ' . $valuta . '</p></div><br>';
	
	$result = sprintf($donationbar);
	return $result;
}

function last_donors()
{
	global $db, $phpEx, $template, $user, $config, $auth, $phpbb_root_path;
	
		//format can only be yyyy/mm/dd
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
		$curdonorwhere = '';
		if($starttime > 0)
		{
			if($endtime <= $starttime)
			{
				$curdonorwhere = ' AND a.date >= ' . $starttime;
			}
			else
			{
				$curdonorwhere = ' AND a.date >= ' . $starttime . ' AND a.date <= ' . $endtime;
			}
		}
	
	// Show All
	$count = 0;		
	$sql = "SELECT COUNT(*) FROM " . ACCT_HIST_TABLE . " WHERE comment LIKE '%' AND status = 'Completed' GROUP BY user_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query forum donors information');
	}
	
	if($row = $db->sql_fetchrow($result))
	{
		$count = $row['COUNT(*)'];		
	}

	$sql = "SELECT a.* FROM " . ACCT_HIST_TABLE . " a WHERE a.comment LIKE '%' AND status = 'Completed' AND a.user_id = " . ANONYMOUS . 
					$curdonorwhere . " ORDER BY a.date DESC LIMIT 1";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query forum donors information');
	}
	$anony_donator = $db->sql_fetchrow($result);
	
	$orderby = "ORDER BY date DESC";
	$selectcolums = "MAX(a.date) as date, SUM(a.money) as money, a.currency, u.*";
	if(intval($config['list_top_donors']) == 1)
	{
		$orderby = "ORDER BY money DESC";
		$selectcolums = "SUM(a.money) as money, MAX(a.date) as date, a.currency, u.*";
	}	
	
	$str_input = intval($config['dislay_x_donors']);
		
	$sql = "SELECT $selectcolums from " . ACCT_HIST_TABLE . " a, " . USERS_TABLE . " u where a.comment like '%' AND status = 'Completed' AND u.user_id = a.user_id " . $curdonorwhere . " group by a.user_id"
	 	.  " $orderby LIMIT $str_input";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not query forum donors information');
	}
	$last_donors = '';

	while( $row = $db->sql_fetchrow($result) )
	{
		$style_color = '';
		$row['username'] = '<b>' . get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '</b>';					
		
		if($row['user_id'] == ANONYMOUS)
		{
			$last_donors .= '<b>' . $user->lang['ANONYMOUS_DONOR'] . '</b>&nbsp;(' . $row['currency'] . sprintf("%.2f", $anony_donator['money']) . ') ';
		}
		else
		{
			$last_donors .= '<b><a href="' . append_sid("{$phpbb_root_path}profile.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']) . '">' . $row['username'] . '</a></b>&nbsp;(' . $row['currency'] . sprintf("%.2f", $row['money']) . ') ';
		}
	}
	
	if($count > $str_input)
	{
		$last_donors .= '<a href="' . append_sid("{$phpbb_root_path}donors.$phpEx", 'mode=viewall') . '">' . $user->lang['MORE_DONORS'] . '</a>';
	}
	
	if($count == 0)
	{
		$last_donors = $user->lang['NO_DONORS_YET'];
	}
	
  return $last_donors;
}

?>