<?php
/*
*
* @name donate_paypal_functions.php
* @package phpBB3 Portal XL 5.0
* @version $Id: donate_paypal_functions.php,v 1.1.0 2010/10/08 portalxl group Exp $
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

function donate_paypal_convert_currency_type_V3($input_currency)
{
	$output_currency = $input_currency;
	if(strcasecmp($input_currency, 'USD') == 0)
	{
		$output_currency = 'USD';
	}
	else if(strcasecmp($input_currency, 'AUD') == 0)
	{
		$output_currency = 'AUD';		
	}
	else if(strcasecmp($input_currency, 'CAD') == 0)
	{
		$output_currency = 'CAD';		
	}
	else if(strcasecmp($input_currency, 'CZK') == 0)
	{
		$output_currency = 'CZK';		
	}
	else if(strcasecmp($input_currency, 'DKK') == 0)
	{
		$output_currency = 'DKK';		
	}
	else if(strcasecmp($input_currency, 'EUR') == 0)
	{
		$output_currency = 'EUR';		
	}
	else if(strcasecmp($input_currency, 'HKD') == 0)
	{
		$output_currency = 'HKD';		
	}
	else if(strcasecmp($input_currency, 'HUF') == 0)
	{
		$output_currency = 'HUF';		
	}
	else if(strcasecmp($input_currency, 'NZD') == 0)
	{
		$output_currency = 'NZD';		
	}
	else if(strcasecmp($input_currency, 'NOK') == 0)
	{
		$output_currency = 'NOK';		
	}
	else if(strcasecmp($input_currency, 'PLN') == 0)
	{
		$output_currency = 'PLN';		
	}
	else if(strcasecmp($input_currency, 'GBP') == 0)
	{
		$output_currency = 'GBP';		
	}
	else if(strcasecmp($input_currency, 'SGD') == 0)
	{
		$output_currency = 'SGD';		
	}
	else if(strcasecmp($input_currency, 'SEK') == 0)
	{
		$output_currency = 'SEK';		
	}
	else if(strcasecmp($input_currency, 'CHF') == 0)
	{
		$output_currency = 'CHF';		
	}
	else if(strcasecmp($input_currency, 'JPY') == 0)
	{
		$output_currency = 'JPY';		
	}
	return $output_currency;
}

function donate_construct_paypal_donation(&$the_template, &$Input_Array)
{
	global $user, $config;

	$format = '';
	$donate_submit_hidden_fields = '';
	$donate_submit = '';

	$user_in_grp_flag = 0;
	$j = 0;
	
	if(intval($Input_Array['ENABLE_THIS_GATEWAY']) > 0)
	{
		$support_currencies = array();
		$support_currencies = split("[;,]", $config['paypal_support_currency']);

		if( !(in_array($Input_Array['CURRENCY_CODE'], $support_currencies)))
		{
			return -1;
		}

		$format = 'https://www.paypal.com/cgi-bin/webscr';
		$donate_submit = "<input type=\"image\" src=\"./images/paypal_donate.gif\" name=\"submit\" alt=\"\">";
        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"currency_code\" value=\"" . $Input_Array['CURRENCY_CODE'] . "\">";
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"business\" value=\"" . $Input_Array['RECEIVER_ACCT'] . "\">";
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"item_name\" value=\"" . $Input_Array['ITEM_NAME'] . "\">";
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"item_number\" value=\"" . $Input_Array['ITEM_NUMBER'] . "\">";
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"no_shipping\" value=\"1\">";	
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"notify_url\" value=\"" . $Input_Array['NOTIFY_URL'] . "\">";
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"return\" value=\"" . $Input_Array['RETURN_URL'] . "\">";
		$donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"cancel_return\" value=\"" . $Input_Array['CANCEL_RETURN_URL'] . "\">";

        switch($Input_Array['PAYMENT_METHOD'])
        {
        	case PAYMENT_RECURRING_W:
        	case PAYMENT_RECURRING_M:
        	case PAYMENT_RECURRING_Q:
        	case PAYMENT_RECURRING_H:
        	case PAYMENT_RECURRING_Y:
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"cmd\" value=\"_xclick-subscriptions\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"a3\" value=\"" . $Input_Array['AMOUNT_TO_PAY'] . "\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"no_note\" value=\"1\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"src\" value=\"1\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"sra\" value=\"1\">";
        	    break;	
        	default:   //PAYMENT_MANUAL
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"amount\" value=\"" . $Input_Array['AMOUNT_TO_PAY'] . "\">";
        	    break;	    
        }
		
        switch($Input_Array['PAYMENT_METHOD'])
        {
        	case PAYMENT_RECURRING_W:
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"p3\" value=\"1\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"t3\" value=\"W\">";
        	    break;	
        	case PAYMENT_RECURRING_M:
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"p3\" value=\"1\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"t3\" value=\"M\">";
        	    break;	
        	case PAYMENT_RECURRING_Q:
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"p3\" value=\"3\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"t3\" value=\"M\">";
        	    break;	
        	case PAYMENT_RECURRING_H:
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"p3\" value=\"6\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"t3\" value=\"M\">";
        	    break;	
        	case PAYMENT_RECURRING_Y:
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"p3\" value=\"1\">";
		        $donate_submit_hidden_fields .= "<input type=\"hidden\" name=\"t3\" value=\"Y\">";
        	    break;	
        	default:   //PAYMENT_MANUAL
        	    break;	    
        }
		
		$the_template->assign_vars(array(
				'PAYPAL_ACTION'			=> $format,	
				'SUBMIT_HIDDEN_FIELDS' 	=> $donate_submit_hidden_fields,
				'SUBMIT_ACTION' 		=> $donate_submit,
				'S_PAYPAL_DONATION' 	=> true,
			)
		);
						
		return 0;

	}
	
	return -1;

}

?>