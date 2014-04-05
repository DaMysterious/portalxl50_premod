<?php
/*
*
* @name donate.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donate.php,v 1.1.0 2010/10/08 portalxl group Exp $
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
//include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
$user->add_lang('mods/lang_donations');
// End session management

/**
* @todo normalize?
*/
if(strlen($config['paypal_b_acct']) <=0 
	|| strlen($config['paypal_p_acct']) <=0 )
{
	trigger_error('The paypal account has not been setup yet.');
	exit;
}

// Only registered users can go beyond this point
if (!$user->data['is_registered'])
{
	if ($user->data['is_bot'])
	{
		redirect(append_sid("{$phpbb_root_path}portal.$phpEx"));
	}

	login_box('', $user->lang['LOGIN_EXPLAIN_UCP']);
}

//get necessary page header inforamtion first.
page_header($config['sitename'] . ' : ' . $user->lang['ACCT_DONATE_US']);

$currencydisplay = '';
$currencyoptions = '';
if(strlen($config['donate_currencies']) < 4)
{
	$config['donate_currencies'] = $config['paypal_currency_code'] . ";";
}
$currencyoptions = '<select name="currency_code" >';

$board_currencies = array();
$board_currencies = split("[;,]", $config['donate_currencies']);
for($i = 0; $i < count($board_currencies); $i++)
{
  if(strlen(trim($board_currencies[$i])) > 0)
  {
	  $selected = '';
	  if(strcasecmp(trim($config['paypal_currency_code']), trim($board_currencies[$i])) == 0)
	  {
		  $selected = 'selected';
	  }
	  $currencyoptions .= ('<option value="' . trim($board_currencies[$i]) . '" ' . $selected . '>' . donate_display_currency_type_V3(trim($board_currencies[$i])) . '</option>');		
  }
}
$currencyoptions .= '</select>';

$methodoptions = '<select name="payment_method" >';
$payment_methods = array($user->lang['One_time_donation'], 
                         $user->lang['Recurring_one_week'],
                         $user->lang['Recurring_one_month'],
                         $user->lang['Recurring_three_month'],
                         $user->lang['Recurring_six_month'],
                         $user->lang['Recurring_one_year'],
                         );
for($i = 0; $i < count($payment_methods); $i++)
{
	if(strlen(trim($payment_methods[$i])) > 0)
	{
		$methodoptions .= ('<option value="' . $i . '">' . $payment_methods[$i] . '</option>');		
	}
}
$methodoptions .= '</select>';

//Get the donation explanation post
$message = $user->lang['AMOUNT_TO_DONATE_EXPLAIN'];
$row = false;

if( $config['explanation_postid'] != null 
		&& strlen(trim($config['explanation_postid'])) > 0
		&& intval($config['explanation_postid']) > 0)
{
  $sql = $db->sql_build_query('SELECT', array(
	  'SELECT'	=> 'p.*',
  
	  'FROM'		=> array(
		  POSTS_TABLE		=> 'p',
	  ),
  
	  'WHERE'		=> 'p.post_id = ' . intval($config['explanation_postid']),
  ));
  
  $result = $db->sql_query($sql);
  
  $row = $db->sql_fetchrow($result);
}

if( $row )
{
  // Parse the message and subject
  $message = censor_text($row['post_text']);

  // Define the global bbcode bitfield, will be used to load bbcodes
  $bbcode_bitfield = base64_decode($row['bbcode_bitfield']);	
  if ($bbcode_bitfield !== '')
  {
	  $bbcode = new bbcode(base64_encode($bbcode_bitfield));
	  $bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
  }

  $message = bbcode_nl2br($message);
  $message = smiley_text($message);
}

$template->assign_vars(array(
	'SITENAME'					=> $config['sitename'],
	'SITE_DESCRIPTION'			=> $config['site_desc'],
	'U_INDEX'					=> append_sid("{$phpbb_root_path}index.".$phpEx),
	'L_INDEX'					=> $user->lang['FORUM_INDEX'],
	'S_TOPUP'					=> append_sid("{$phpbb_root_path}donate.".$phpEx),
	'L_TOPUP'					=> $user->lang['ACCT_DONATE_INTO'],
	'PAYPAL_ACTION'				=> append_sid("{$phpbb_root_path}donateconfirm.".$phpEx),	
	'L_TOPUP_TITLE'				=> $user->lang['DONATE_TITLE'], 	
	'L_AMOUNT_TO_PAY'			=> $user->lang['AMOUNT_TO_DONATE'], 	
	'L_AMOUNT_TO_PAY_EXPLAIN' 	=> $user->lang['AMOUNT_TO_DONATE_EXPLAIN'],
	'L_CURRENCY_TO_PAY'			=> $user->lang['CURRENCY_TO_PAY'],
	'L_CURRENCY_TO_PAY_EXPLAIN' => sprintf($user->lang['CURRENCY_TO_PAY_EXPLAIN'], $config['donate_currencies']),
	'WANT_ANONYMOUS' 			=> $user->lang['WANT_ANONYMOUS'],
	'L_DONATE_WAY' 				=> $user->lang['L_DONATE_WAY'],
	'DONATE_METHOD_EXPLAIN' 	=> $user->lang['DONATE_METHOD_EXPLAIN'],
	'L_DONATE_METHOD' 			=> $user->lang['L_DONATE_METHOD'],
	'DONATE_METHOD_OPTIONS' 	=> $methodoptions,
	'CURRENCY_OPTIONS' 			=> $currencyoptions,
	'L_DONATE_EXPLANATION' 		=> $user->lang['DONATE_EXPLANATION'],
	'L_DONATE_EXPLANATION_TEXT' => $message,
	));
	
// Output the page
$template->set_filenames(array(
	'body' => 'donation/donate_body.html')
);

$template->assign_block_vars('navlinks', array(
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}donate.$phpEx"),
	'FORUM_NAME'	=> $user->lang['ACCT_DONATE_US'],
));

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>