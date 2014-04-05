<?php
/*
*
* @name acp_donate_mod_settings.php
* @package phpBB3 Portal XL 5.0
* @version $Id: acp_donate_mod_settings.php,v 1.1.0 2010/10/08 portalxl group Exp $
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
class acp_donate_mod_settings
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix;

		$this->tpl_name = 'acp_donate_mod_settings';
		$this->page_title = 'ACP_DONATE_MOD_SETTINGS';
		
		$submit = isset($_POST['submit']) ? true : false;
		switch ($mode)
		{
			default:
				if ($submit)
				{
					// Get posting variables
					$user_account 	= request_var('user_account', ''); 
					$money 			= request_var('money', 0); 
					$date 			= request_var('date', ''); 
					$txn_id 		= request_var('txn_id', ''); 
					$donor_pay_acct = request_var('donor_pay_acct', ''); 
					
					// Get posting variables
					$newdata = array();
					$count = 0;
					$donate_currencies = request_var('donate_currencies', '');
					$newdata[$count]['config_value'] = $donate_currencies;
					$newdata[$count]['config_name'] = 'donate_currencies';
					
					$paypal_currency_code = request_var('paypal_currency_code', '');
					$count += 1;
					$newdata[$count]['config_value'] = $paypal_currency_code;
					$newdata[$count]['config_name'] = 'paypal_currency_code';
				
					$usd_to_primary = request_var('usd_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $usd_to_primary;
					$newdata[$count]['config_name'] = 'usd_to_primary';
				
					$eur_to_primary = request_var('eur_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $eur_to_primary;
					$newdata[$count]['config_name'] = 'eur_to_primary';
				
					$gbp_to_primary = request_var('gbp_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $gbp_to_primary;
					$newdata[$count]['config_name'] = 'gbp_to_primary';
				
					$cad_to_primary = request_var('cad_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $cad_to_primary;
					$newdata[$count]['config_name'] = 'cad_to_primary';
				
					$jpy_to_primary = request_var('jpy_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $jpy_to_primary;
					$newdata[$count]['config_name'] = 'jpy_to_primary';
					
					$aud_to_primary = request_var('aud_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $aud_to_primary;
					$newdata[$count]['config_name'] = 'aud_to_primary';
				
					$czk_to_primary = request_var('czk_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $czk_to_primary;
					$newdata[$count]['config_name'] = 'czk_to_primary';
					
					$dkk_to_primary = request_var('dkk_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $dkk_to_primary;
					$newdata[$count]['config_name'] = 'dkk_to_primary';
					
					$hkd_to_primary = request_var('hkd_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $hkd_to_primary;
					$newdata[$count]['config_name'] = 'hkd_to_primary';
					
					$huf_to_primary = request_var('huf_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $huf_to_primary;
					$newdata[$count]['config_name'] = 'huf_to_primary';
					
					$nzd_to_primary = request_var('nzd_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $nzd_to_primary;
					$newdata[$count]['config_name'] = 'nzd_to_primary';
					
					$nok_to_primary = request_var('nok_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $nok_to_primary;
					$newdata[$count]['config_name'] = 'nok_to_primary';
					
					$pln_to_primary = request_var('pln_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $pln_to_primary;
					$newdata[$count]['config_name'] = 'pln_to_primary';
					
					$sgd_to_primary = request_var('sgd_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $sgd_to_primary;
					$newdata[$count]['config_name'] = 'sgd_to_primary';
					
					$sek_to_primary = request_var('sek_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $sek_to_primary;
					$newdata[$count]['config_name'] = 'sek_to_primary';
				
					$chf_to_primary = request_var('chf_to_primary', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $chf_to_primary;
					$newdata[$count]['config_name'] = 'chf_to_primary';
					
					$paypal_p_acct = request_var('paypal_p_acct', '');
					$count += 1;
					$newdata[$count]['config_value'] = $paypal_p_acct;
					$newdata[$count]['config_name'] = 'paypal_p_acct';
				
					$paypal_b_acct = request_var('paypal_b_acct', '');
					$count += 1;
					$newdata[$count]['config_value'] = $paypal_b_acct;
					$newdata[$count]['config_name'] = 'paypal_b_acct';
						
					$dislay_x_donors = request_var('dislay_x_donors', '');
					$count += 1;
					$newdata[$count]['config_value'] = $dislay_x_donors;
					$newdata[$count]['config_name'] = 'dislay_x_donors';
				
					$list_top_donors = request_var('list_top_donors', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $list_top_donors;
					$newdata[$count]['config_name'] = 'list_top_donors';
				
					$donate_description = request_var('donate_description', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_description;
					$newdata[$count]['config_name'] = 'donate_description';
				
					$donate_cur_goal = request_var('donate_cur_goal', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_cur_goal;
					$newdata[$count]['config_name'] = 'donate_cur_goal';
				
					$donate_start_time = request_var('donate_start_time', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_start_time;
					$newdata[$count]['config_name'] = 'donate_start_time';
				
					$donate_end_time = request_var('donate_end_time', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_end_time;
					$newdata[$count]['config_name'] = 'donate_end_time';
					
					$donate_to_points = request_var('donate_to_points', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_to_points;
					$newdata[$count]['config_name'] = 'donate_to_points';
					
					$donate_to_posts = request_var('donate_to_posts', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_to_posts;
					$newdata[$count]['config_name'] = 'donate_to_posts';
					
					$donate_to_grp_one = request_var('donate_to_grp_one', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_to_grp_one;
					$newdata[$count]['config_name'] = 'donate_to_grp_one';
					
					$to_grp_one_amount = request_var('to_grp_one_amount', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $to_grp_one_amount;
					$newdata[$count]['config_name'] = 'to_grp_one_amount';
				
					$donate_to_grp_two = request_var('donate_to_grp_two', '');
					$count += 1;
					$newdata[$count]['config_value'] = $donate_to_grp_two;
					$newdata[$count]['config_name'] = 'donate_to_grp_two';
				
					$to_grp_two_amount = request_var('to_grp_two_amount', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $to_grp_two_amount;
					$newdata[$count]['config_name'] = 'to_grp_two_amount';
				
					$donor_rank_id = request_var('donor_rank_id', '0');
					$count += 1;
					$newdata[$count]['config_value'] = $donor_rank_id;
					$newdata[$count]['config_name'] = 'donor_rank_id';
				
					$enable_paypal = request_var('enable_paypal', '1');
					$count += 1;
					$newdata[$count]['config_value'] = $enable_paypal;
					$newdata[$count]['config_name'] = 'enable_paypal';
				
					$paypal_support_currency = request_var('paypal_support_currency', '');
					$count += 1;
					$newdata[$count]['config_value'] = $paypal_support_currency;
					$newdata[$count]['config_name'] = 'paypal_support_currency';

					$enable_mod = request_var('enable_mod', '1');
					$count += 1;
					$newdata[$count]['config_value'] = $enable_mod;
					$newdata[$count]['config_name'] = 'enable_mod';
					
					$explanation_postid = request_var('explanation_postid', '1');
					$count += 1;
					$newdata[$count]['config_value'] = $explanation_postid;
					$newdata[$count]['config_name'] = 'explanation_postid';
					
					global $cache;
					$cache->purge();
					add_log('admin', 'LOG_PURGE_CACHE');
												
					for($i = 0; $i <= $count; $i++ )
					{
						$sql = "UPDATE " . CONFIG_TABLE . " SET
							config_value = '" . str_replace("\'", "''", $newdata[$i]['config_value']) . "'
							WHERE config_name = '" . $newdata[$i]['config_name'] . "'";
						if( !$db->sql_query($sql) )
						{
							trigger_error(sprintf($user->lang['update_currency_info_error'], $newdata[$i]['config_name']) . adm_back_link($this->u_action));
							exit;
						}
					}					
				
					// Return a message...
					$message = $user->lang['DONATION_SETTINGS_UPDATE'];			
					trigger_error($message . adm_back_link($this->u_action));
					exit;
				}
				
				$list_top_donors_yes 	= ( intval($config['list_top_donors']) == 1 ) ? "checked=\"checked\"" : "";
				$list_top_donors_no 	= ( intval($config['list_top_donors']) == 0 ) ? "checked=\"checked\"" : "";
				$enable_paypal_yes 		= ( intval($config['enable_paypal']) == 1 ) ? "checked=\"checked\"" : "";
				$enable_paypal_no 		= ( intval($config['enable_paypal']) == 0 ) ? "checked=\"checked\"" : "";
				$enable_mod_yes 		= ( intval($config['enable_mod']) == 1 ) ? "checked=\"checked\"" : "";
				$enable_mod_no 			= ( intval($config['enable_mod']) == 0 ) ? "checked=\"checked\"" : "";
				
				
				$template->assign_vars(array(
					'L_CURRENCY_CONFIGURATION_TITLE' 		=> $user->lang['L_CURRENCY_CONFIGURATION_TITLE'],
					'L_CURRENCY_CONFIGURATION_EXPLAIN' 		=> $user->lang['L_CURRENCY_CONFIGURATION_EXPLAIN'],
					'L_CURRENCY_GENERAL_SETTINGS' 			=> $user->lang['L_CURRENCY_GENERAL_SETTINGS'],
					'L_DONATE_CURRENCY' 					=> $user->lang['L_DONATE_CURRENCY'],
					'L_DONATE_CURRENCY_EXPLAIN' 			=> $user->lang['L_DONATE_CURRENCY_EXPLAIN'],
					'DONATE_CURRENCY' 						=> $config['donate_currencies'],
					'L_DONATE_CURRENCY_PRI' 				=> $user->lang['L_DONATE_CURRENCY_PRI'],
					'L_DONATE_CURRENCY_PRI_EXPLAIN' 		=> $user->lang['L_DONATE_CURRENCY_PRI_EXPLAIN'],
					'DONATE_CURRENCY_PRI' 					=> $config['paypal_currency_code'],
					
					"L_DONATION_SETTINGS" 					=> $user->lang['L_DONATION_SETTINGS'],
					"L_PERSONAL_PAYPAL_ACCT" 				=> $user->lang['L_PERSONAL_PAYPAL_ACCT'],
					"L_PERSONAL_PAYPAL_ACCT_EXPLAIN" 		=> $user->lang['L_PERSONAL_PAYPAL_ACCT_EXPLAIN'],
					"L_BUSINESS_PAYPAL_ACCT" 				=> $user->lang['L_BUSINESS_PAYPAL_ACCT'],
					"L_BUSINESS_PAYPAL_ACCT_EXPLAIN" 		=> $user->lang['L_BUSINESS_PAYPAL_ACCT_EXPLAIN'],
					"L_PAYPAL_CURRENCY_CODE" 				=> $user->lang['L_PAYPAL_CURRENCY_CODE'],
					"L_PAYPAL_CURRENCY_CODE_EXPLAIN" 		=> $user->lang['L_PAYPAL_CURRENCY_CODE_EXPLAIN'],
					"L_DISPLAY_X_DONORS" 					=> $user->lang['L_DISPLAY_X_DONORS'],
					"L_DISPLAY_X_DONORS_EXPLAIN" 			=> $user->lang['L_DISPLAY_X_DONORS_EXPLAIN'],
					"L_DONATION_DESCRIPTION" 				=> $user->lang['L_DONATION_DESCRIPTION'],
					"L_DONATION_DESCRIPTION_EXPLAIN" 		=> $user->lang['L_DONATION_DESCRIPTION_EXPLAIN'],
					"L_DONATION_GOAL" 						=> $user->lang['L_DONATION_GOAL'],
					"L_DONATION_GOAL_EXPLAIN" 				=> $user->lang['L_DONATION_GOAL_EXPLAIN'],
					"L_DONATION_START" 						=> $user->lang['L_DONATION_START'],
					"L_DONATION_START_EXPLAIN" 				=> $user->lang['L_DONATION_START_EXPLAIN'],
					"L_DONATION_END" 						=> $user->lang['L_DONATION_END'],
					"L_DONATION_END_EXPLAIN" 				=> $user->lang['L_DONATION_END_EXPLAIN'],
					"L_DONATION_POINTS" 					=> $user->lang['L_DONATION_POINTS'],
					"L_DONATION_POINTS_EXPLAIN" 			=> $user->lang['L_DONATION_POINTS_EXPLAIN'],
					"TOP_DONORS" 							=> $config['list_top_donors'], 
					"L_TOP_DONORS" 							=> $user->lang['L_TOP_DONORS'],
					"L_TOP_DONORS_EXPLAIN" 					=> $user->lang['L_TOP_DONORS_EXPLAIN'],
					"L_POSTS_COUNTS" 						=> $user->lang['L_POSTS_COUNTS'],
					"L_POSTS_COUNTS_EXPLAIN" 				=> $user->lang['L_POSTS_COUNTS_EXPLAIN'],
					"POSTS_COUNTS" 							=> $config['donate_to_posts'], 
					"PERSONAL_PAYPAL_ACCT" 					=> $config['paypal_p_acct'], 
					"BUSINESS_PAYPAL_ACCT" 					=> $config['paypal_b_acct'], 
					"PAYPAL_CURRENCY_CODE" 					=> $config['paypal_currency_code'], 
					"DISPLAY_X_DONORS" 						=> $config['dislay_x_donors'], 
					"DONATION_DESCRIPTION" 					=> $config['donate_description'], 
					"DONATION_GOAL" 						=> $config['donate_cur_goal'], 
					"DONATION_START" 						=> $config['donate_start_time'], 
					"DONATION_END" 							=> $config['donate_end_time'], 
					"DONATION_POINTS" 						=> $config['donate_to_points'], 
					"L_DONATE_TOGRP_ONE" 					=> $user->lang['L_DONATE_TOGRP_ONE'],
					"L_DONATE_TOGRP_ONE_EXPLAIN" 			=> $user->lang['L_DONATE_TOGRP_ONE_EXPLAIN'],
					"L_TOGRPONE_AMOUNT" 					=> $user->lang['L_TOGRPONE_AMOUNT'],
					"L_TOGRPONE_AMOUNT_EXPLAIN" 			=> $user->lang['L_TOGRPONE_AMOUNT_EXPLAIN'],
					"L_DONATE_TOGRP_TWO" 					=> $user->lang['L_DONATE_TOGRP_TWO'],
					"L_DONATE_TOGRP_TWO_EXPLAIN" 			=> $user->lang['L_DONATE_TOGRP_TWO_EXPLAIN'],
					"L_TOGRPTWO_AMOUNT" 					=> $user->lang['L_TOGRPTWO_AMOUNT'],
					"L_TOGRPTWO_AMOUNT_EXPLAIN" 			=> $user->lang['L_TOGRPTWO_AMOUNT_EXPLAIN'],
					"L_TORANK_ID" 							=> $user->lang['L_TORANK_ID'],
					"L_TORANK_ID_EXPLAIN" 					=> $user->lang['L_TORANK_ID_EXPLAIN'],
				
					"DONATE_TOGRP_ONE" 						=> $config['donate_to_grp_one'], 
					"TOGRPONE_AMOUNT" 						=> $config['to_grp_one_amount'], 
					"DONATE_TOGRP_TWO" 						=> $config['donate_to_grp_two'], 
					"TOGRPTWO_AMOUNT" 						=> $config['to_grp_two_amount'], 
					"TORANK_ID" 							=> $config['donor_rank_id'], 
					
					'L_EXPLANATION_POSTID' 					=> $user->lang['L_EXPLANATION_POSTID'],
					'L_EXPLANATION_POSTID_EXPLAIN' 			=> $user->lang['L_EXPLANATION_POSTID_EXPLAIN'],
					'EXPLANATION_POSTID' 					=> $config['explanation_postid'], 
					
					'L_PAYPAL_GATEWAY_SETTINGS' 			=> $user->lang['PAYPAL_GATEWAY_SETTINGS'],
					'S_TOP_DONORS_YES' 						=> $list_top_donors_yes,
					'S_TOP_DONORS_NO' 						=> $list_top_donors_no,
					'L_DM_ENABLE_PAYPAL' 					=> $user->lang['L_DM_ENABLE_PAYPAL'],
					'L_DM_ENABLE_PAYPAL_EXPLAIN' 			=> $user->lang['L_DM_ENABLE_PAYPAL_EXPLAIN'],
					'S_ENABLE_PAYPAL_YES' 					=> $enable_paypal_yes,
					'S_ENABLE_PAYPAL_NO' 					=> $enable_paypal_no,
					'L_CURRENCY_PAYPAL_SUPPORTED' 			=> $user->lang['L_CURRENCY_PAYPAL_SUPPORTED'],
					'L_CURRENCY_PAYPAL_SUPPORTED_EXPLAIN' 	=> $user->lang['L_CURRENCY_PAYPAL_SUPPORTED_EXPLAIN'],
					'CURRENCY_PAYPAL_SUPPORTED' 			=> $config['paypal_support_currency'], 
					
					'L_ENABLE_MOD' 							=> $user->lang['L_ENABLE_MOD'],
					'L_ENABLE_MOD_EXPLAIN' 					=> $user->lang['L_ENABLE_MOD_EXPLAIN'],
					'S_ENABLE_MOD_YES' 						=> $enable_mod_yes,
					'S_ENABLE_MOD_NO' 						=> $enable_mod_no,					
					
					'L_DONATE_CZK_TO_PRI' 					=> $user->lang['L_DONATE_CZK_TO_PRI'],
					'DONATE_CZK_TO_PRI' 					=> $config['czk_to_primary'],
					'L_DONATE_DKK_TO_PRI' 					=> $user->lang['L_DONATE_DKK_TO_PRI'],
					'DONATE_DKK_TO_PRI' 					=> $config['dkk_to_primary'],
					'L_DONATE_HKD_TO_PRI' 					=> $user->lang['L_DONATE_HKD_TO_PRI'],
					'DONATE_HKD_TO_PRI' 					=> $config['hkd_to_primary'],
					'L_DONATE_HUF_TO_PRI' 					=> $user->lang['L_DONATE_HUF_TO_PRI'],
					'DONATE_HUF_TO_PRI' 					=> $config['huf_to_primary'],
					'L_DONATE_NZD_TO_PRI' 					=> $user->lang['L_DONATE_NZD_TO_PRI'],
					'DONATE_NZD_TO_PRI' 					=> $config['nzd_to_primary'],
					'L_DONATE_NOK_TO_PRI' 					=> $user->lang['L_DONATE_NOK_TO_PRI'],
					'DONATE_NOK_TO_PRI' 					=> $config['nok_to_primary'],
					'L_DONATE_PLN_TO_PRI' 					=> $user->lang['L_DONATE_PLN_TO_PRI'],
					'DONATE_PLN_TO_PRI' 					=> $config['pln_to_primary'],
					'L_DONATE_SGD_TO_PRI' 					=> $user->lang['L_DONATE_SGD_TO_PRI'],
					'DONATE_SGD_TO_PRI' 					=> $config['sgd_to_primary'],
					'L_DONATE_SEK_TO_PRI' 					=> $user->lang['L_DONATE_SEK_TO_PRI'],
					'DONATE_SEK_TO_PRI' 					=> $config['sek_to_primary'],
					'L_DONATE_CHF_TO_PRI' 					=> $user->lang['L_DONATE_CHF_TO_PRI'],
					'DONATE_CHF_TO_PRI' 					=> $config['chf_to_primary'],
					'L_DONATE_USD_TO_PRI' 					=> $user->lang['L_DONATE_USD_TO_PRI'],
					'DONATE_USD_TO_PRI' 					=> $config['usd_to_primary'],
					'L_DONATE_EUR_TO_PRI' 					=> $user->lang['L_DONATE_EUR_TO_PRI'],
					'DONATE_EUR_TO_PRI' 					=> $config['eur_to_primary'],
					'L_DONATE_GBP_TO_PRI' 					=> $user->lang['L_DONATE_GBP_TO_PRI'],
					'DONATE_GBP_TO_PRI' 					=> $config['gbp_to_primary'],
					'L_DONATE_CAD_TO_PRI' 					=> $user->lang['L_DONATE_CAD_TO_PRI'],
					'DONATE_CAD_TO_PRI' 					=> $config['cad_to_primary'],
					'L_DONATE_JPY_TO_PRI' 					=> $user->lang['L_DONATE_JPY_TO_PRI'],
					'DONATE_JPY_TO_PRI' 					=> $config['jpy_to_primary'],
					'L_DONATE_AUD_TO_PRI' 					=> $user->lang['L_DONATE_AUD_TO_PRI'],
					'DONATE_AUD_TO_PRI' 					=> $config['aud_to_primary'],
			
					'S_CURRENCY_CONFIG_ACTION' 				=> append_sid($phpbb_admin_path . $this->u_action),
					'S_HIDDEN_FIELDS' 						=> '',
					
					'L_YES' 								=> $user->lang['Yes'],
					'L_NO' 									=> $user->lang['No'],		
					'L_SUBMIT' 								=> $user->lang['L_SUBMIT'],
					'L_RESET' 								=> $user->lang['L_RESET'],
					)
				);
				
			break;
		}
	}
}

?>