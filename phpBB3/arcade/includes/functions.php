<?php
/**
*
* @package arcade
* @version $Id: functions.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
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
* Error and message handler, call with trigger_error if reqd
* Slightly modified phpBB function to include ability to
* display simple header and footers
*/
function arcade_msg_handler($errno, $msg_text, $errfile, $errline)
{
	global $cache, $db, $auth, $template, $config, $user;
	global $phpEx, $phpbb_root_path, $msg_title, $msg_long_text, $use_simple_header;

	// Do not display notices if we suppress them via @
	if (error_reporting() == 0 && $errno != E_USER_ERROR && $errno != E_USER_WARNING && $errno != E_USER_NOTICE)
	{
		return;
	}

	// Message handler is stripping text. In case we need it, we are possible to define long text...
	if (isset($msg_long_text) && $msg_long_text && !$msg_text)
	{
		$msg_text = $msg_long_text;
	}

	if (!defined('E_DEPRECATED'))
	{
		define('E_DEPRECATED', 8192);
	}

	switch ($errno)
	{
		case E_NOTICE:
		case E_WARNING:

			// Check the error reporting level and return if the error level does not match
			// If DEBUG is defined the default level is E_ALL
			if (($errno & ((defined('DEBUG')) ? E_ALL : error_reporting())) == 0)
			{
				return;
			}

			if (strpos($errfile, 'cache') === false && strpos($errfile, 'template.') === false)
			{
				// flush the content, else we get a white page if output buffering is on
				if ((int) @ini_get('output_buffering') === 1 || strtolower(@ini_get('output_buffering')) === 'on')
				{
					@ob_flush();
				}

				// Another quick fix for those having gzip compression enabled, but do not flush if the coder wants to catch "something". ;)
				if (!empty($config['gzip_compress']))
				{
					if (@extension_loaded('zlib') && !headers_sent() && !ob_get_level())
					{
						@ob_flush();
					}
				}

				// remove complete path to installation, with the risk of changing backslashes meant to be there
				$errfile = str_replace(array(phpbb_realpath($phpbb_root_path), '\\'), array('', '/'), $errfile);
				$msg_text = str_replace(array(phpbb_realpath($phpbb_root_path), '\\'), array('', '/'), $msg_text);
				echo '<b>[phpBB Debug] PHP Notice</b>: in file <b>' . $errfile . '</b> on line <b>' . $errline . '</b>: <b>' . $msg_text . '</b><br />' . "\n";

				// we are writing an image - the user won't see the debug, so let's place it in the log
				if (defined('IMAGE_OUTPUT') || defined('IN_CRON'))
				{
					add_log('critical', 'LOG_IMAGE_GENERATION_ERROR', $errfile, $errline, $msg_text);
				}
				// echo '<br /><br />BACKTRACE<br />' . get_backtrace() . '<br />' . "\n";
			}

			return;

		break;

		case E_USER_ERROR:

			if (!empty($user) && !empty($user->lang))
			{
				$msg_text = (!empty($user->lang[$msg_text])) ? $user->lang[$msg_text] : $msg_text;
				$msg_title = (!isset($msg_title)) ? $user->lang['GENERAL_ERROR'] : ((!empty($user->lang[$msg_title])) ? $user->lang[$msg_title] : $msg_title);

				$l_return_index = sprintf($user->lang['RETURN_INDEX'], '<a href="' . $phpbb_root_path . '">', '</a>');
				$l_notify = '';

				if (!empty($config['board_contact']))
				{
					$l_notify = '<p>' . sprintf($user->lang['NOTIFY_ADMIN_EMAIL'], $config['board_contact']) . '</p>';
				}
			}
			else
			{
				$msg_title = 'General Error';
				$l_return_index = '<a href="' . $phpbb_root_path . '">Return to index page</a>';
				$l_notify = '';

				if (!empty($config['board_contact']))
				{
					$l_notify = '<p>Please notify the board administrator or webmaster: <a href="mailto:' . $config['board_contact'] . '">' . $config['board_contact'] . '</a></p>';
				}
			}

			if ((defined('DEBUG') || defined('IN_CRON') || defined('IMAGE_OUTPUT')) && isset($db))
			{
				// let's avoid loops
				$db->sql_return_on_error(true);
				add_log('critical', 'LOG_GENERAL_ERROR', $msg_title, $msg_text);
				$db->sql_return_on_error(false);
			}

			// Do not send 200 OK, but service unavailable on errors
			send_status_line(503, 'Service Unavailable');

			garbage_collection();

			// Try to not call the adm page data...

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">';
			echo '<head>';
			echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
			echo '<title>' . $msg_title . '</title>';
			echo '<style type="text/css">' . "\n" . '/* <![CDATA[ */' . "\n";
			echo '* { margin: 0; padding: 0; } html { font-size: 100%; height: 100%; margin-bottom: 1px; background-color: #E4EDF0; } body { font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif; color: #536482; background: #E4EDF0; font-size: 62.5%; margin: 0; } ';
			echo 'a:link, a:active, a:visited { color: #006699; text-decoration: none; } a:hover { color: #DD6900; text-decoration: underline; } ';
			echo '#wrap { padding: 0 20px 15px 20px; min-width: 615px; } #page-header { text-align: right; height: 40px; } #page-footer { clear: both; font-size: 1em; text-align: center; } ';
			echo '.panel { margin: 4px 0; background-color: #FFFFFF; border: solid 1px  #A9B8C2; } ';
			echo '#errorpage #page-header a { font-weight: bold; line-height: 6em; } #errorpage #content { padding: 10px; } #errorpage #content h1 { line-height: 1.2em; margin-bottom: 0; color: #DF075C; } ';
			echo '#errorpage #content div { margin-top: 20px; margin-bottom: 5px; border-bottom: 1px solid #CCCCCC; padding-bottom: 5px; color: #333333; font: bold 1.2em "Lucida Grande", Arial, Helvetica, sans-serif; text-decoration: none; line-height: 120%; text-align: left; } ';
			echo "\n" . '/* ]]> */' . "\n";
			echo '</style>';
			echo '</head>';
			echo '<body id="errorpage">';
			echo '<div id="wrap">';
			echo '	<div id="page-header">';
			echo '		' . $l_return_index;
			echo '	</div>';
			echo '	<div id="acp">';
			echo '	<div class="panel">';
			echo '		<div id="content">';
			echo '			<h1>' . $msg_title . '</h1>';

			echo '			<div>' . $msg_text . '</div>';

			echo $l_notify;

			echo '		</div>';
			echo '	</div>';
			echo '	</div>';
			echo '	<div id="page-footer">';
			echo '		Powered by phpBB &copy; 2000, 2002, 2005, 2007 <a href="http://www.phpbb.com/">phpBB Group</a>';
			echo '	</div>';
			echo '</div>';
			echo '</body>';
			echo '</html>';

			exit_handler();

			// On a fatal error (and E_USER_ERROR *is* fatal) we never want other scripts to continue and force an exit here.
			exit;
		break;

		case E_USER_WARNING:
		case E_USER_NOTICE:

			define('IN_ERROR_HANDLER', true);

			if (empty($user->data))
			{
				$user->session_begin();
			}

			// We re-init the auth array to get correct results on login/logout
			$auth->acl($user->data);

			if (empty($user->lang))
			{
				$user->setup();
			}

			if ($msg_text == 'ERROR_NO_ATTACHMENT' || $msg_text == 'NO_FORUM' || $msg_text == 'NO_TOPIC' || $msg_text == 'NO_USER')
			{
				send_status_line(404, 'Not Found');
			}

			$msg_text = (!empty($user->lang[$msg_text])) ? $user->lang[$msg_text] : $msg_text;
			$msg_title = (!isset($msg_title)) ? $user->lang['INFORMATION'] : ((!empty($user->lang[$msg_title])) ? $user->lang[$msg_title] : $msg_title);

			if (!defined('HEADER_INC'))
			{
				if (defined('IN_ADMIN') && isset($user->data['session_admin']) && $user->data['session_admin'])
				{
					adm_page_header($msg_title);
				}
				else
				{
					page_header($msg_title, false);
				}
			}

			$template->set_filenames(array(
				'body' => 'arcade/message_body.html')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE'			=> $msg_title,
				'MESSAGE_TEXT'			=> $msg_text,
				'S_USE_SIMPLE_HEADER'	=> (isset($use_simple_header) && $use_simple_header) ? true : false,
				'S_USER_WARNING'		=> ($errno == E_USER_WARNING) ? true : false,
				'S_USER_NOTICE'			=> ($errno == E_USER_NOTICE) ? true : false)
			);

			// We do not want the cron script to be called on error messages
			define('IN_CRON', true);

			if (defined('IN_ADMIN') && isset($user->data['session_admin']) && $user->data['session_admin'])
			{
				adm_page_footer();
			}
			else
			{
				page_footer();
			}

			exit_handler();
		break;

		// PHP4 compatibility
		case E_DEPRECATED:
			return true;
		break;
	}

	// If we notice an error not handled here we pass this back to PHP by returning false
	// This may not work for all php versions
	return false;
}

// These are needed to ensure compatability with php4
if (!function_exists('scandir'))
{
	function scandir($directory, $sorting_order = 0)
	{
		$list = array();

		$dh = opendir($directory);
		while (($file = readdir($dh)) !== false)
		{
			$list[] = $file;
		}
		closedir($dh);

		(!$sorting_order) ? sort($list) : rsort($list);

		return $list;
	}
}

if (!function_exists('file_put_contents'))
{
	function file_put_contents($filename, $data)
	{
		$f = @fopen($filename, 'w');
		if (!$f)
		{
			return false;
		}
		else
		{
			$bytes = fwrite($f, $data);
			fclose($f);
			return $bytes;
		}
	}
}

	/**
	* Function used to quickly show the contents of variables
	*/
	function arcade_debug($var = false)
	{
		$return = '';
		if ($var !== false)
		{
			$return .= '<pre>';
			$return .= var_export($var, true);
			$return .= '</pre>';
		}
		else
		{
			$debug = array(
				'POST'		=> $_POST,
				'GET'		=> $_GET,
				'REQUEST'	=> $_REQUEST,
				'COOKIE'	=> $_COOKIE,
				'SERVER'	=> $_SERVER,
			);


			foreach ($debug as $key => $value)
			{
				$return .= '<p><b>' . $key . '</b>';
				$return .= '<pre>';
				$return .= var_export($value, true);
				$return .= '</pre>';
				$return .= '</p>';
			}
		}

		return $return;
	}

?>