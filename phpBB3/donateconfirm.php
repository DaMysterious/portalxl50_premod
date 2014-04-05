<?php
/*
*
* @name donateconfirm.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donateconfirm.php,v 1.1.0 2010/10/08 portalxl group Exp $
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
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
$user->add_lang('mods/lang_donations');
// End session management

if(strlen($config['paypal_b_acct']) <=0 
	|| strlen($config['paypal_p_acct']) <=0 )
{
	//message_die(GENERAL_ERROR, $user->lang['PAYPAL_ACCT_ERROR']);
	trigger_error('The paypal account has not been setup yet.');
	exit;
}

//get necessary page header inforamtion first.
page_header($config['sitename'] . ' : ' . $user->lang['DONATE_CONFIRM_TITLE']);

$server_url = ($config['cookie_secure'] == 0 ? 'http://' : 'https://') . $config['server_name'];
$server_url .= ($config['server_port'] == 80) ? '' : ':' . $config['server_port'];
$server_url .= $config['script_path'];
$slashpos = ((strlen($config['script_path']) - 2) > 0 ) ? (strlen($config['script_path']) - 2) : 0;
$pos = strpos($config['script_path'], '/', ( $slashpos ) );
if($pos === false)
{
	$server_url .= '/';
}
	
// $notifyurl = $server_url . 'donateresult.' . $phpEx;
$returnurl = $server_url . 'donateshowresult.' . $phpEx;

$anonymous = intval(request_var('donate_anonymous', '')) + 0;
if($anonymous != 1)
{
	$anonymous = 0;
}

$subscription_notice = "";
$payment_method = intval(request_var('payment_method', '')) + 0;
switch($payment_method)
{
	case 1:
	    $payment_method = PAYMENT_RECURRING_W;
        $subscription_notice = $user->lang['Please_note_you_choose'] . $user->lang['Recurring_one_week'];
	    break;	
	case 2:
	    $payment_method = PAYMENT_RECURRING_M;
        $subscription_notice = $user->lang['Please_note_you_choose'] . $user->lang['Recurring_one_month'];
	    break;	
	case 3:
	    $payment_method = PAYMENT_RECURRING_Q;
        $subscription_notice = $user->lang['Please_note_you_choose'] . $user->lang['Recurring_three_month'];
	    break;	
	case 4:
	    $payment_method = PAYMENT_RECURRING_H;
        $subscription_notice = $user->lang['Please_note_you_choose'] . $user->lang['Recurring_six_month'];
	    break;	
	case 5:
	    $payment_method = PAYMENT_RECURRING_Y;
        $subscription_notice = $user->lang['Please_note_you_choose'] . $user->lang['Recurring_one_year'];
	    break;	
	default:
	    $payment_method = PAYMENT_MANUAL;
	    $subscription_notice = "";
	    break;	    
}

$amountopay = request_var('amount', '') + 0.00 ;
$currency = request_var('currency_code', '');

if(strlen($config['donate_currencies']) < 4) //if not set, so just use the primary currency code
{
	$config['donate_currencies'] = $config['paypal_currency_code'] . ";";
}

if($amountopay <= 0 || strpos($config['donate_currencies'], $currency) === false)
{
	$message = $user->lang['PAYMENT_DATA_ERROR'] . '<br /><br />' . sprintf($user->lang['Click_return_login'], "<a href=\"donate.$phpEx\">", '</a>') . '<br /><br />' .  sprintf($user->lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');
	trigger_error($message);
	exit;
}

$receiveacct = $config['paypal_b_acct'];
if($amountopay < 1)
{
	$receiveacct = $config['paypal_p_acct'];
}

//modify to add posts count
$donte_amount_pay = $user->lang['AMOUNT_TO_DONATE'] . "<b>" . $amountopay . '</b> ' . donate_display_currency_type_V3(trim($currency));

$poster_convertor = donate_cal_cash_exchange_rate_V3($currency, $config) + 0; 
if($poster_convertor <= 0)
{
	$poster_convertor = 1.0;
}
	
$donate_mny_payee = ($amountopay + 0.00) / ($poster_convertor);

if(!$user->data['is_registered'])
{
	//do nothing	
}
else if(intval($config['donate_to_points']) > 0)
{
	$donte_amount_pay .= '<br />' . sprintf($user->lang['DONATION_TO_POINTS'], intval(intval($config['donate_to_points']) * $donate_mny_payee));
}
else if(intval($config['donate_to_posts']) > 0)
{
	$donte_amount_pay .= '<br />' . sprintf($user->lang['DONATION_TO_POSTS'], intval(intval($config['donate_to_posts']) * $donate_mny_payee));
} 
$donte_amount_pay .= '<br /><br />' . $subscription_notice . '<br />';

//2 is a magic value to make the html output looks more nice
$num_payment_gateway = 2;

require_once($phpbb_root_path . 'includes/donate_paypal_functions.'.$phpEx);

$PayPal_Data = array(	
    'ITEM_NAME' 			=> sprintf($user->lang['DONATION_TO_WHO'], $config['sitename']),
	'ITEM_NUMBER' 			=> ($user->data['user_id'] <= 0 ? 0 : $user->data['user_id']) . '-' . $anonymous,
	'AMOUNT_TO_PAY' 		=> $amountopay,
	'CURRENCY_CODE' 		=> $currency,
	'RECEIVER_ACCT'			=> $receiveacct,
	'NOTIFY_URL'			=> $notifyurl,
	'RETURN_URL'			=> $returnurl,
	'CANCEL_RETURN_URL' 	=> $returnurl,
	'ENABLE_THIS_GATEWAY' 	=> intval($config['enable_paypal']),
	'PAYMENT_METHOD'		=> $payment_method,
);

if(donate_construct_paypal_donation($template, $PayPal_Data) == 0)
{
	$num_payment_gateway += 1;
}

$template->assign_vars(array(
	'U_INDEX'			=> append_sid("{$phpbb_root_path}index.".$phpEx),
	'L_INDEX'			=> $user->lang['FORUM_INDEX'],
	'S_TOPUP'			=> append_sid("{$phpbb_root_path}donate.".$phpEx),
	'L_TOPUP'			=> $user->lang['ACCT_DONATE_INTO'],
	'L_TOPUP_TITLE'		=> $user->lang['DONATE_CONFIRM_TITLE'],
	'L_AMOUNT_TO_PAY'	=> $donte_amount_pay,
	'RWO_WIDTH' 		=> intval(100 / $num_payment_gateway),
	));
	
	 $submit= request_var ( 'submit', '' );
	 $data = time();
	 if ( !empty($submit) ) {
	 $sql_array	= array (
	                'user_id' 		=> $user->data['user_id'],
					'money' 		=> $amountopay,
					'currency' 		=> $currency,
					'plus_minus' 	=> '-1',
					'date' 			=> $data,					
					'status' 		=> 'Incomplete'
				);
				
	$sql = "INSERT INTO " . ACCT_HIST_TABLE . "" . $db->sql_build_array ( 'INSERT', $sql_array );
	$db->sql_query ( $sql );
	}

// Output the page
$template->set_filenames(array(
	'body' => 'donation/donate_confirm_body.html')
);

$template->assign_block_vars('navlinks', array(
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}donate.$phpEx"),
	'FORUM_NAME'	=> $user->lang['ACCT_DONATE_US'],
));

$template->assign_block_vars('navlinks', array(
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}donateconfirm.$phpEx"),
	'FORUM_NAME'	=> $user->lang['DONATE_CONFIRM_TITLE'],
));

page_footer();

?>