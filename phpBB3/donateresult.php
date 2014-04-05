<?php
/*
*
* @name donateresult.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donateresult.php,v 1.1.0 2010/10/08 portalxl group Exp $
*
* @copyright (c) Zou Xiong - Enterprise admin@loewen.com.sg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
//include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
$user->add_lang('mods/lang_donations');
// End session management

require_once($phpbb_root_path . 'includes/donate_functions_V3.'.$phpEx);
require_once($phpbb_root_path . 'includes/donate_paypal_functions.'.$phpEx);

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
  $value = urlencode(stripslashes($value));
  $req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp) {
// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {

			$PayPal_Data = array(
			     'ITEM_NUMBER' 		=> trim($item_number),
			     'SYSTEM_ACCOUNT_1' => trim($config['paypal_p_acct']),
			     'SYSTEM_ACCOUNT_2' => trim($config['paypal_b_acct']),
				 'PAYMENT_STATUS' 	=> trim($payment_status),
			     'RECEIVER_ACCT'	=> trim($receiver_email),
			     'PAYER_ACCT'		=> trim($payer_email),
			 	 'MC_CURRENCY'		=> donate_paypal_convert_currency_type_V3(trim($payment_currency)),
			     'TXN_ID'			=> trim($txn_id),
			     'MC_GROSS' 		=> ($payment_amount + 0.00),
			);

			donate_process_payment_V3($PayPal_Data);			
		}
		else if (strcmp ($res, "INVALID") == 0) 
		{
			// log for manual investigation
		    add_log('admin', 'PayPal - Invalid IPN: <b>' . $res . '</b>');
			
			/*
			* Paypal didnt like what we sent. 
			* If you start getting these after system was working ok in the past, 
			* check if Paypal has altered its IPN format
			*/
			$mail_To = $config['board_contact'];
			$mail_Subject = "PayPal - Invalid IPN";
			$mail_Body = "We have had an INVALID response. \n\nThe transaction ID number is: $txn_id \n\n Username = $payer_email";
			mail($mail_To, $mail_Subject, $mail_Body);			
			
		}
	}
	fclose ($fp);
}

die("Process End");

?>