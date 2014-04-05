<?php
/*
*
* @name acp_donors_config.php
* @package phpBB3 Portal XL 5.0
* @version $Id: acp_donors_config.php,v 1.1.0 2010/10/08 portalxl group Exp $
*
* @copyright (c) Zou Xiong - Enterprise admin@loewen.com.sg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

/**
* @package acp
*/
class acp_donors_config
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix;

		$this->tpl_name = 'acp_donors_config';
		$this->page_title = 'ACP_DONORS_CONFIG';
		
		$submit = isset($_POST['submit']) ? true : false;
		switch ($mode)
		{
			default:
				if ($submit)
				{
					// Get posting variables
					$user_account 	= request_var('user_account', ''); 
					$money 			= request_var('money', '0.0'); 
					$date 			= request_var('date', ''); 
					$txn_id 		= request_var('txn_id', ''); 
					$donor_pay_acct = request_var('donor_pay_acct', ''); 
			
					$sql = "SELECT * FROM " . USERS_TABLE . " WHERE username = '" . $user_account . "'";
					$user_id = ANONYMOUS;
					if ( ($result = $db->sql_query($sql)) )
					{
						if( ($donateuserdata = $db->sql_fetchrow($result)) )
						{
							if($donateuserdata['user_id'] > 0)
							{
								$user_id = $donateuserdata['user_id'];
							}
						}
					}
					$db->sql_freeresult($result);
					if($user_id > 0)
					{			
						if(intval($config['donate_to_points']) > 0)
						{			
							$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points + " . (intval(intval($config['donate_to_points']) * ($money + 0.00))) . " WHERE user_id = " . $user_id;
							if ( !($result = $db->sql_query($sql)) )
							{
								//do nothing
							}
							
						}
						else if(intval($config['donate_to_posts']) > 0)
						{			
							$sql = "UPDATE " . USERS_TABLE . " SET user_posts = user_posts + " . (intval(intval($config['donate_to_posts']) * ($money + 0.00))) . " WHERE user_id = " . $user_id;
							if ( !($result = $db->sql_query($sql)) )
							{
								//do nothing
							}
							
						}
						
						$sql = "SELECT SUM(money) FROM " . ACCT_HIST_TABLE . " WHERE comment LIKE '%%' AND user_id = " . $user_id;
						$amount_donated = ($money + 0.00);
						if($result = $db->sql_query($sql))
						{
							if($row = $db->sql_fetchrow($result))
							{
								$amount_donated = $amount_donated + $row["SUM(money)"];
							}
						}
			
						$grptojoin = 0;
						if( intval($config['donate_to_grp_one']) > 0 
							&& ($config['to_grp_one_amount'] + 0.00) <= ($amount_donated) )
						{
							$grptojoin = intval($config['donate_to_grp_one']);
						}
						if(intval($config['donate_to_grp_two']) > 0 
							&& ($config['to_grp_two_amount'] + 0.00) <= ($amount_donated) 
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
								//end update the donor group
						   	}
						}
						
						if( intval($config['donor_rank_id']) > 0 )
						{
							$sql = "UPDATE " . USERS_TABLE . " SET user_rank = " . intval($config['donor_rank_id']) . " WHERE user_id = " . $user_id;
					
							if ( !($result = $db->sql_query($sql)) )
							{
								//do nothing
							}
							
						}
						
					}
			
					//date format YYYY/MM/DD hh:mm:ss
					$user_donate_date = time();
					if(strlen($date) == strlen('YYYY/MM/DD hh:mm:ss'))
					{
						$user_donate_date = mktime(substr($date, 11, 2), substr($date, 14, 2), substr($date, 17, 2), substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
					}
			
					$post_id 	= $user->data['user_id'];
					$donor_id 	= utf8_normalize_nfc(request_var('user_account', '1', true));
					if ( is_numeric($donor_id) ) { $donatore = $donor_id; }
					else { 
					$sql = "SELECT * FROM " . USERS_TABLE . " WHERE username = '" . $donor_id . "'";
				    $result		= $db->sql_query ( $sql );
		            $row		= $db->sql_fetchrow ( $result );
					$donatore 	= $row['user_id'];
	              	$db->sql_freeresult ( $result ); }
					
					$sql = "INSERT INTO " . ACCT_HIST_TABLE . "(user_id, post_id, money, plus_minus, currency, date, comment, site, status, txn_id) VALUES(" . $donatore . ", " . $post_id . ", " . ($money + 0.00) . ", -1, '" . str_replace("\'", "''", $config['paypal_currency_code']) . "', " . $user_donate_date . ", '" . str_replace("\'", "''", $donor_pay_acct) . "', '" . $config['sitename'] . "', 'Completed', '" . str_replace("\'", "''", $txn_id) . "')";
					if ( !($result = $db->sql_query($sql)) )
					{
						// Return a message...
						$message = $user->lang['New_donor_record_error'];
					}	
					else
					{
						// Return a message...
						$message = $user->lang['New_donor_record_added'];
					}
								
					trigger_error($message . adm_back_link($this->u_action));
				}
				
				$template->assign_vars(array(
					'L_DONOR_CONFIGURATION_TITLE' 	=> $user->lang['L_DONOR_CONFIGURATION_TITLE'],
					'L_DONOR_CONFIGURATION_EXPLAIN' => $user->lang['L_DONOR_CONFIGURATION_EXPLAIN'],
					'L_DONOR_GENERAL_SETTINGS' 		=> $user->lang['L_DONOR_GENERAL_SETTINGS'],
					'L_USER_ACCOUNT' 				=> $user->lang['L_USER_ACCOUNT'],
					'L_DONATE_MONEY' 				=> $user->lang['L_DONATE_MONEY'],
					'L_DONATE_DATE' 				=> $user->lang['L_DONATE_DATE'],
					'L_DONATE_DATE_EXPLAIN' 		=> $user->lang['L_DONATE_DATE_EXPLAIN'],
					'L_TRANSACTION_ID' 				=> $user->lang['L_TRANSACTION_ID'],
					'L_DONOR_PAY_ACCOUNT' 			=> $user->lang['L_DONOR_PAY_ACCOUNT'],
					'L_DONOR_PAY_ACCOUNT_EXPLAIN' 	=> $user->lang['L_DONOR_PAY_ACCOUNT_EXPLAIN'],
					'L_DATE_START' 					=> $user->lang['L_DATE_START'],
					'L_DATE_END' 					=> $user->lang['L_DATE_END'],
		
					'U_ACTION' 						=> append_sid($phpbb_admin_path . $this->u_action),
					'S_HIDDEN_FIELDS' 				=> '',
					'L_SUBMIT' 						=> $user->lang['L_SUBMIT'],
					'L_RESET' 						=> $user->lang['L_RESET'],
					));

			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			$sql_type	= " SELECT * FROM " . ACCT_HIST_TABLE . " WHERE status = 'Incomplete' ";
			$result_type = $db->sql_query ( $sql_type );
			while ( $row_type = $db->sql_fetchrow ( $result_type ) )
			{
			  $user_id = $row_type['user_id'];
			  
			  $sql2 = 'SELECT username, user_colour FROM ' . USERS_TABLE . ' WHERE user_id = ' . $user_id;
			  $result2 = $db->sql_query($sql2);
			  $row2 = $db->sql_fetchrow($result2);
			  $db->sql_freeresult($result2);
			
			  $template->assign_block_vars ( 'check', array (
				  'USERNAME' 	=> get_username_string('full', $user_id, $row2['username'], $row2['user_colour'], false, append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview')),
				  'MONEY' 		=> $row['money'],
				  'VALUTA' 		=> $row['currency'],
				  'DATA' 		=> $user->format_date($row['date']),
				  'TRANSACTION' => $row['txn_id'],
				  'COMMENT' 	=> $row['comment'],
				  'USER_ID' 	=> $user_id,
				  'DATE' 		=> $row['date']
			  ));
			}
		   $db->sql_freeresult($result_type);
	
			$sql = 'SELECT * FROM ' . ACCT_HIST_TABLE . '
			        ORDER BY date ASC';
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
			  $user_id = $row['user_id'];
			  
			  $sql2 = 'SELECT username, user_colour FROM ' . USERS_TABLE . ' WHERE user_id = ' . $user_id;
			  $result2 = $db->sql_query($sql2);
			  $row2 = $db->sql_fetchrow($result2);
			  $db->sql_freeresult($result2);

			  // query the donation custom profile field	
			  $sql3 = 'SELECT f.field_name, l.lang_name, d.user_id, d.pf_user_donation
				  FROM ' . PROFILE_LANG_TABLE . ' l, ' . PROFILE_FIELDS_TABLE . ' f, ' . PROFILE_FIELDS_DATA_TABLE . ' d
					  WHERE  l.lang_id = ' . $user->get_iso_lang_id() . '
					   AND d.user_id = ' . $user_id . '
					   AND d.pf_user_donation = d.pf_user_donation';         
			  $result3 = $db->sql_query($sql3);
			  $row3 = $db->sql_fetchrow($result3);
			  $db->sql_freeresult($result3);
			  // query the donation custom profile field	

			  $template->assign_block_vars('donated', array (
				  'DONATOR_NAME' 		=> get_username_string('full', $user_id, $row2['username'], $row2['user_colour'], false, append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview')),
				  'DONATOR_MONEY' 		=> $row['money'],
				  'DONATOR_VALUTA' 		=> $row['currency'],
				  'DONATOR_DATA' 		=> $user->format_date($row['date']),
				  'DONATOR_TRANSACTION' => $row['txn_id'],
				  'DONATOR_COMMENT' 	=> $row['comment'],
				  'DONATOR_USER_ID' 	=> $row['user_id'],
				  'DONATOR_DATE' 		=> $row['date'],
				  'DONATOR_DATA_END' 	=> '<a href="' . $phpbb_root_path . 'ucp.php?i=pm&mode=compose&u=' . $row['user_id'] . '">' . $row3['pf_user_donation'] . '</a>'
			  ));
			}
		   $db->sql_freeresult($result);
	
		   // Delete expired entries with no renewal
		   $delete_renewal	= request_var ( 'delete_renewal', '' );
		   if ( !empty ( $delete_renewal ) ) {
		   $delete_renewal_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : '0';

		   $sql	= "DELETE FROM " . ACCT_HIST_TABLE . " WHERE user_id = $delete_renewal_id";
		   $db->sql_query ( $sql );
		   
		   $redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=donors_config&amp;mode=default");
				meta_refresh(2, $redirect_url);
				trigger_error ('DONATION_DELETED'); }
		   // Delete expired entries with no renewal
		   
		   // Entries delete
		   $delete	= request_var ( 'delete', '' );
		   if ( !empty ( $delete ) ) {
		   $delete_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : '0';
		   $date = isset($_POST['date']) ? intval($_POST['date']) : '0';

		   $sql	= "DELETE FROM " . ACCT_HIST_TABLE . " WHERE user_id = $delete_id AND status = 'Incomplete' AND $date = 'date'";
		   $db->sql_query ( $sql );
		   
		   $redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=donors_config&amp;mode=default");
				meta_refresh(2, $redirect_url);
				trigger_error ('DONATION_DELETED'); }
		   // Entries delete
				
	       // Entries confirm
		   $ok	= request_var ( 'ok', '' );
		   if ( !empty ( $ok ) ) {
		   $id_ok = isset($_POST['user_id']) ? intval($_POST['user_id']) : '0';
		   $date = isset($_POST['date']) ? intval($_POST['date']) : '0';
		   
		   $transaction = utf8_normalize_nfc(request_var('transaction', '', true));
		   $comment = utf8_normalize_nfc(request_var('comment', '', true));
		   
		   $sql_array	= array (
					'txn_id' 	=> $transaction,
					'comment' 	=> $comment,
					'status' 	=> 'Completed',
					'site' 		=> $config['sitename']
				);
  
		   $sql = "UPDATE " . ACCT_HIST_TABLE. " SET " . $db->sql_build_array('UPDATE', $sql_array) . " WHERE user_id = $id_ok AND status = 'Incomplete' AND $date = 'date'";
		   $db->sql_query ( $sql );;
		   
		   $redirect_url = append_sid("{$phpbb_admin_path}index.$phpEx", "i=donors_config&amp;mode=default");
				meta_refresh(2, $redirect_url);
				trigger_error ('DONATION_ACCEPTED'); }
			break;
		}
	}
}

?>