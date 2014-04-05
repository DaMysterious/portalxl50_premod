<?php
/**
*
* @package arcade
* @version $Id: protect.php 1663 2011-09-22 12:09:30Z killbill $
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

// Thank you sun.
if (isset($_SERVER['CONTENT_TYPE']))
{
	if ($_SERVER['CONTENT_TYPE'] === 'application/x-java-archive')
	{
		exit;
	}
}
else if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Java') !== false)
{
	exit;
}

require($phpbb_root_path . 'includes/startup.' . $phpEx);
require($phpbb_root_path . 'config.' . $phpEx);

if (!defined('PHPBB_INSTALLED') || empty($dbms) || empty($acm_type))
{
	exit;
}

require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
require($phpbb_root_path . 'includes/cache.' . $phpEx);
require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
require($phpbb_root_path . 'includes/constants.' . $phpEx);
require($phpbb_root_path . 'includes/functions.' . $phpEx);

require($phpbb_root_path . 'arcade/includes/arcade.' . $phpEx);
require($phpbb_root_path . 'arcade/includes/cache.' . $phpEx);

$db = new $sql_db();
// Connect to DB
if (!@$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false))
{
	exit;
}
unset($dbpasswd);

// worst-case default
$browser = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : 'msie 6.0';

$cache = new arcade_cache();
$config = $cache->obtain_arcade_config();

if (isset($_GET['swf']))
{
	$filename = request_var('swf', '');

	if (!$filename)
	{
		send_status_line(403, 'Forbidden');
	}
	else
	{
		send_swf_to_browser($filename, $browser);
	}

	file_gc();
}

if (isset($_GET['img']))
{
	$filename = request_var('img', '');

	$ext = substr(strrchr($filename, '.'), 1);

	if (!in_array($ext, array('png', 'gif', 'jpg', 'jpeg')) || !$filename || strpos($filename, '.') == false)
	{
		send_status_line(403, 'Forbidden');
	}
	else
	{
		send_img_to_browser($filename, $browser);
	}

	file_gc();
}

function send_swf_to_browser($file, $browser)
{
	global $config, $phpbb_root_path;

	$path = request_var('p', 0);

	$game_path = ($path == 1) ? "arcade/swf/" : $config['game_path'];
	// Adjust game path (no trailing slash)
	if (substr($game_path, -1, 1) == '/' || substr($game_path, -1, 1) == '\\')
	{
		$game_path = substr($game_path, 0, -1) . '/';
	}
	$game_path = str_replace(array('../', '..\\', './', '.\\'), '', $game_path);

	if ($game_path && ($game_path[0] == '/' || $game_path[0] == '\\'))
	{
		$game_path = '';
	}

	$file_path = ($path == 1) ? "arcade/swf/{$file}.swf" : "{$game_path}/{$file}/{$file}.swf";

	$file_path = $phpbb_root_path . $file_path;

	if ((@file_exists($file_path) && @is_readable($file_path)) && !headers_sent())
	{
		header('Pragma: public');
		header('Content-Type: application/x-shockwave-flash');

		if (strpos(strtolower($browser), 'msie') !== false && strpos(strtolower($browser), 'msie 8.0') === false)
		{
			if (strpos(strtolower($browser), 'msie 6.0') !== false)
			{
				header('Expires: -1');
			}
			else
			{
				header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
			}
		}
		else
		{
			header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
		}

		$size = @filesize($file_path);
		if ($size)
		{
			header("Content-Length: $size");
		}

		if (@readfile($file_path) == false)
		{
			$fp = @fopen($file_path, 'rb');

			if ($fp !== false)
			{
				while (!feof($fp))
				{
					echo fread($fp, 8192);
				}
				fclose($fp);
			}
		}

		flush();
	}
	else
	{
		send_status_line(403, 'Not Found');
	}
}

function send_img_to_browser($file, $browser)
{
	global $config, $phpbb_root_path;

	$file_folder = remove_ext($file);
	$game_path = $config['game_path'];
	// Adjust game path (no trailing slash)
	if (substr($game_path, -1, 1) == '/' || substr($game_path, -1, 1) == '\\')
	{
		$game_path = substr($game_path, 0, -1) . '/';
	}
	$game_path = str_replace(array('../', '..\\', './', '.\\'), '', $game_path);

	if ($game_path && ($game_path[0] == '/' || $game_path[0] == '\\'))
	{
		$game_path = '';
	}

	// Try to be more tolerant for people that do not RTFM
	$file_path = $phpbb_root_path . $game_path . '/' . $file_folder . '/' . $file;
	if (!@file_exists($file_path) || !@is_readable($file_path))
	{
		$ext = strrchr($file, '.');
		$ibp_image = array(
			$phpbb_root_path . $game_path . '/' . $file_folder . '/' . $file_folder . '1' . $ext,
			$phpbb_root_path . $game_path . '/' . $file_folder . '/' . $file_folder . '2' . $ext,
		);

		foreach ($ibp_image as $image)
		{
			if (@file_exists($image) && @is_readable($image))
			{
				$file_path = $image;
				break;
			}
		}
	}

	if ((@file_exists($file_path) && @is_readable($file_path)) && !headers_sent())
	{
		header('Pragma: public');

		$image_data = @getimagesize($file_path);
		header('Content-Type: ' . image_type_to_mime_type($image_data[2]));

		if (strpos(strtolower($browser), 'msie') !== false && strpos(strtolower($browser), 'msie 8.0') === false)
		{
			header('Content-Disposition: attachment; ' . header_filename($file));

			if (strpos(strtolower($browser), 'msie 6.0') !== false)
			{
				header('Expires: -1');
			}
			else
			{
				header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
			}
		}
		else
		{
			header('Content-Disposition: inline; ' . header_filename($file));
			header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
		}

		$size = @filesize($file_path);
		if ($size)
		{
			header("Content-Length: $size");
		}

		if (@readfile($file_path) == false)
		{
			$fp = @fopen($file_path, 'rb');

			if ($fp !== false)
			{
				while (!feof($fp))
				{
					echo fread($fp, 8192);
				}
				fclose($fp);
			}
		}

		flush();
	}
	else
	{
		send_status_line(403, 'Not Found');
	}
}

/**
* Get a browser friendly UTF-8 encoded filename
*/
function header_filename($file)
{
	$user_agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';

	// There be dragons here.
	// Not many follows the RFC...
	if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Safari') !== false || strpos($user_agent, 'Konqueror') !== false)
	{
		return "filename=" . rawurlencode($file);
	}

	// follow the RFC for extended filename for the rest
	return "filename*=UTF-8''" . rawurlencode($file);
}

function file_gc()
{
	global $cache, $db;
	if (!empty($cache))
	{
		$cache->unload();
	}
	$db->sql_close();
	exit;
}

function remove_ext($file)
{
	$ext = strrchr($file, '.');
	while ($ext !== false)
	{
		$file = substr($file, 0, -strlen($ext));
		$ext = strrchr($file, '.');
	}

	return $file;
}

?>