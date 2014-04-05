<?php
/*
*
* @name donateshowresult.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donateshowresult.php,v 1.1.0 2010/10/08 portalxl group Exp $
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

$txn_id = request_var('txn_id', '');
$tx = request_var('tx', '');
if(strlen($txn_id) > 0 || strlen($tx) > 0)
{
	$txn_id = strlen($txn_id) > 0 ? $txn_id : $tx;
}
$mc_currency = request_var('mc_currency', '');
$cc = request_var('cc', '');
if(strlen($mc_currency) > 0 || strlen($cc) > 0)
{
	$payment_currency = strlen($mc_currency) > 0 ? $mc_currency : $cc;
}
$mc_gross = request_var('mc_gross', '');
$amt = request_var('amt', '');
if(strlen($mc_gross) > 0 || strlen($amt) > 0)
{
	$payment_amount = strlen($mc_gross) > 0 ? $mc_gross : $amt;
}
$payment_status = request_var('payment_status', '');
$st = request_var('st', '');
if(strlen($payment_status) > 0 || strlen($st) > 0)
{
	$payment_status = strlen($payment_status) > 0 ? $payment_status : $st;
}

$message = "";
if(strcasecmp($payment_status, 'Completed') == 0)
{
	$message .= $user->lang['DONATE_DONE'];
}
else if(strcasecmp($payment_status, 'Pending') == 0)
{
	$message .= sprintf($user->lang['DONATE_PENDDING'], $payer_email);
}
else if(strcasecmp($payment_status, 'Denied') == 0)
{
	$message .= $user->lang['DONATE_DENIED'];
}
else if(strcasecmp($payment_status, 'Failed') == 0)
{
	$message .= $user->lang['DONATE_FAILED'];
}
else
{
	$message .= $user->lang['DONATE_DONE'];
}

// $message .= '<br /><br />' .  sprintf($user->lang['CLICK_RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.".$phpEx) . '">', '</a>', $user->lang['FORUM_INDEX']);
if(defined('PORTAL'))
{
	$user->setup('mods/portal_xl');

	// meta_refresh(5, append_sid("{$phpbb_root_path}portal.$phpEx"));
	$message = $message . '<br /><br />' . sprintf($user->lang['RETURN_PORTAL'], '<a href="' . append_sid("{$phpbb_root_path}portal.$phpEx") . '">', '</a> ');
}

trigger_error($message);

exit;
		
?>