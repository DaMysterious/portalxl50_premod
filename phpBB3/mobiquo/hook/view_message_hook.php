<?php
if(!defined('IN_PHPBB')) exit;
// display emoji from app
if(!defined('IN_MOBIQUO'))
{
	$protocol = ($config['cookie_secure'])  ? 'https' : 'http';
	$message = preg_replace('/\[emoji(\d+)\]/i', '<img src="'.$protocol.'://s3.amazonaws.com/tapatalk-emoji/emoji\1.png" />', $message);
}
