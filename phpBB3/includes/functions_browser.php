<?php
/**
*
* @package Browser, os & screen
* @version $Id: functions_browser.php 030 14:24 14/08/2011 Sylver35 Exp $
* @copyright (c) 2010, 2011 Sylver35 - http://breizh-portal.com
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

function switch_os($user_agent)
{
	global $user, $phpbb_root_path;

	$os_name_alt = $user->lang['OS_UNKNOW'];
	$image_os = "unknown-os";
	$source = base64_decode('Jm5ic3A7PGltZyBzcmM9Ii4vaW1hZ2VzL2Jyb3dzZXJzLw==');
	
	if ($user_agent == '0' || !$user_agent)
	{
		return '';
	}

	$os_name = strtolower($user_agent);
	
	if (preg_match('/Android/i', $os_name))
	{
		$os_name_alt = 'Android';
		$image_os = 'android';
	}
	elseif (preg_match('/HTC/i', $os_name)) 
	{
		$os_name_alt = "HTC";
		$image_os = "htc";
	}
	elseif (preg_match('/iPad/i', $os_name)) 
	{
		$os_name_alt = "iPad";
		$image_os = "ipad";
	}
	elseif (preg_match('/iPhone/i', $os_name)) 
	{
		$os_name_alt = "iPhone";
		$image_os = "iphone";
	}
	elseif (preg_match('/Nokia/i', $os_name) || preg_match('/SymbOS/i', $os_name)) 
	{
		$os_name_alt = "Nokia";
		$image_os = "nokia";
	}
	elseif (preg_match('/Windows NT 6\.2/i', $os_name))
	{
		$image_os = 'windows';
		$os_name_alt = 'Windows 8';
	}
	elseif (preg_match('/Windows NT 6\.1/i', $os_name))
	{
		$image_os = 'windowseven';
		$os_name_alt = 'Windows 7';
	}
	elseif (preg_match('/Windows NT 6\.0/i', $os_name))
	{
		$image_os = 'windowsvista';
		$os_name_alt = 'Windows Vista';
	}
	elseif (preg_match('/Windows NT 5\.1/i', $os_name))
	{
		$image_os = 'windows';
		$os_name_alt = 'Windows XP';
	}
	elseif (preg_match('/Windows NT 5\.2/i', $os_name))
	{
		$image_os ='windows';
		$os_name_alt = 'Windows Server 2003';
	}
	elseif (preg_match('/Windows NT 5\.0/i', $os_name) || preg_match('/Windows 2000/i', $os_name)) 
	{
		$image_os = 'windows';
		$os_name_alt = 'Windows 2000';
	}
	elseif (preg_match('/Windows NT 4\.0/i', $os_name) || preg_match('/WinNT4\.0/i', $os_name)) 
	{
		$image_os = "windowsnt";
		$os_name_alt = "Windows NT 4\.0";
	}
	elseif (preg_match('/Windows NT/i', $os_name) || preg_match('/WinNT/i', $os_name)) 
	{
		$image_os = "windowsnt";
		$os_name_alt = "Windows NT";
	}
	elseif (preg_match('/Windows 95/i', $os_name) || preg_match('/Win95/', $os_name))
	{
		$image_os = 'windows98';
		$os_name_alt = "Windows 95";
	}
	elseif (preg_match('/Windows\.98/i', $os_name) || preg_match('/Win98/i', $os_name)) 
	{
		$image_os = 'windows98';
		$os_name_alt = 'Windows 98';
	}
	elseif (preg_match('/Win 9x 4\.90/i', $os_name) || preg_match('/Windows ME/i', $os_name)) 
	{
		$image_os = 'windows';
		$os_name_alt = 'Windows ME';
	} 
	elseif (preg_match('/Windows CE/i', $os_name)) 
	{
		$image_os = 'windows';
		$os_name_alt = 'Windows CE';
	}
	elseif (preg_match('/Mac_PowerPC/i', $os_name)) 
	{
		$image_os = 'macos';
		$os_name_alt = 'Mac OS';
	}
	elseif (preg_match('/SunOS/i', $os_name)) 
	{
		$image_os = 'sun';
		$os_name_alt = 'Solaris';
	}
	elseif (preg_match('/Linux/i', $os_name)) 
	{
		$os_name_alt = "Linux";
		$image_os = "linux";
		if (preg_match('#Mandrake#i', $os_name)) 
		{
			$image_os = "mandrake";
			$os_name_alt = "Mandrake Linux";
		} 
		elseif (preg_match('#SuSE#i', $os_name)) 
		{
			$image_os = "suse";
			$os_name_alt = "SuSE Linux";
		} 
		elseif (preg_match('#Novell#i', $os_name)) 
		{
			$image_os = "novell";
			$os_name_alt = "Novell Linux";
		} 
		elseif (preg_match('#kubuntu#i', $os_name)) 
		{
			$image_os = "kubuntu";
			$os_name_alt = "Kubuntu Linux";
		} 
		elseif (preg_match('#xubuntu#i', $os_name)) 
		{
			$image_os = "xubuntu";
			$os_name_alt = "Xubuntu Linux";
		} 
		elseif (preg_match('#Edubuntu#i', $os_name)) 
		{
			$image_os = "edubuntu";
			$os_name_alt = "Edubuntu Linux";
		} 
		elseif (preg_match('#Ubuntu#i', $os_name)) 
		{
			$image_os = "ubuntu";
			$os_name_alt = "Ubuntu Linux";
		} 
		elseif (preg_match('#Debian#i', $os_name)) 
		{
			$image_os = "debian";
			$os_name_alt = "Debian GNU/Linux";
		} 
		elseif (preg_match('#Red ?Hat#i', $os_name)) 
		{
			$image_os = "redhat";
			$os_name_alt = "RedHat Linux";
		} 
		elseif (preg_match('#Gentoo#i', $os_name)) 
		{
			$image_os = "gentoo";
			$os_name_alt = "Gentoo Linux";
		} 
		elseif (preg_match('#Fedora#i', $os_name)) 
		{
			$image_os = "fedora";
			$os_name_alt = "Fedora Linux";
		} 
		elseif (preg_match('#MEPIS#i', $os_name)) 
		{
			$os_name_alt = "MEPIS Linux";
		} 
		elseif (preg_match('#Knoppix#i', $os_name)) 
		{
			$os_name_alt = "Knoppix Linux";
		} 
		elseif (preg_match('#Slackware#i', $os_name)) 
		{
			$image_os = "slackware";
			$os_name_alt = "Slackware Linux";
		} 
		elseif (preg_match('#Xandros#i', $os_name)) 
		{
			$os_name_alt = "Xandros Linux";
		}
		elseif (preg_match('#Kanotix#i', $os_name)) 
		{
			$os_name_alt = "Kanotix Linux";
		}
	}
	elseif (preg_match('/FreeBSD/i', $os_name)) 
	{
		$os_name_alt = "FreeBSD";
		$image_os = "freebsd";
	}
	elseif (preg_match('/NetBSD/i', $os_name)) 
	{
		$os_name_alt = "NetBSD";
		$image_os = "netbsd";
	}
	elseif (preg_match('/OpenBSD/i', $os_name)) 
	{
		$os_name_alt = "OpenBSD";
		$image_os = "openbsd";
	}
	elseif (preg_match('/IRIX/i', $os_name)) 
	{
		$os_name_alt = "SGI Irix";
		$image_os = "sgi";
	}
	elseif (preg_match('/SunOS/i', $os_name)) 
	{
		$os_name_alt = "Solaris";
		$image_os = "sun";
	}
	elseif (preg_match('/Mac OS X/i', $os_name)) 
	{
		$os_name_alt = "Mac OS X";
		$image_os = "macos";
	}
	elseif (preg_match('/Macintosh/i', $os_name)) 
	{
		$os_name_alt = "Mac OS";
		$image_os = "macos";
	}
	elseif (preg_match('/Unix/i', $os_name)) 
	{
		$os_name_alt = "Unix";
		$image_os = "unix";
	}
	
	if (preg_match('/WOW64/i', $os_name) || preg_match('/Win64/i', $os_name) || preg_match('/OS64/i', $os_name) || preg_match('/X64/i', $os_name))  //  Somes Browsers can detect 64 bits versions
	{
		$os_name_alt = $os_name_alt. ' 64 bits';
	}
	
	return $source . $image_os. '.png" alt="' .$os_name_alt. '" title="' .$user->lang['OS_NAME'] . $os_name_alt. '" height="16" style="vertical-align: -10%;" /> ' .$os_name_alt;
}

function switch_screen($screen)
{
	global $user, $phpbb_root_path;
	
	$source = base64_decode('Jm5ic3A7PGltZyBzcmM9Ii4vaW1hZ2VzL2Jyb3dzZXJzL21vbml0b3IucG5nIiB0aXRsZT0i');
	if ($screen == '0' || !$screen)
	{
		return '';
	}
	else
	{
		$screen = $source . $user->lang['RESOLUTION_TYPE'] . $screen. '" alt="' .$user->lang['RESOLUTION_TYPE'] . $screen. '" style="vertical-align: -10%;" /> ' .$screen;
	}
	return $screen;
}

function switch_agent($user_agent)
{   
	global $user, $phpbb_root_path;

	if ($user_agent == '0' || !$user_agent)
	{
		return false;
	}
	
	$source = base64_decode('Jm5ic3A7PGltZyBzcmM9Ii4vaW1hZ2VzL2Jyb3dzZXJzLw==');
	$user_agent = str_replace(array('Mozilla/5.0', 'Mozilla/4.0', '.NET4.0C', '.NET4.1C', 'GTB7.1', '3.5.30729', 'Gecko/20110420'), '', $user_agent);
	$agentstring = strtolower($user_agent);

	if (false !== strpos($agentstring, 'camino'))
	{
		$browser = 'camino';
		$browser_alt = 'Camino';
	}
	elseif (false !== strpos($agentstring, 'chimera'))
	{
		$browser = 'chimera';
		$browser_alt = 'Chimera';
	}
	elseif (false !== strpos($agentstring, 'symbian'))
	{
		$browser = 'symbian';
		$browser_alt = 'Symbian Mobile';
	}
	elseif (false !== strpos($agentstring, 'valve steam'))
	{
		$browser = 'steam';
		$browser_alt = 'Valve Steam';
	}
	elseif (false !== strpos($agentstring, 'iceweasel'))
	{
		$browser = 'iceweasel';
		$browser_alt = 'Iceweasel';
	}
	elseif (false !== strpos($agentstring, 'iceCat'))
	{
		$browser = 'iceCat';
		$browser_alt = 'IceCat';
	}
	elseif (false !== strpos($agentstring, 'america online browser'))
	{
		$browser = 'aol';
		$browser_alt = 'America Online Browser';
	}
	elseif (false !== strpos($agentstring, 'flock'))
	{
		$browser = 'flock';
		$browser_alt = 'Flock';
		$_alt = 'Flock ';
		if (preg_match('/3\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '3.7';
		}
		elseif (preg_match('/3\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6';
		}
		elseif (preg_match('/3\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5';
		}
		elseif (preg_match('/3\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '3.4';
		}
		elseif (preg_match('/3\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '3.3';
		}
		elseif (preg_match('/3\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.2';
		}
		elseif (preg_match('/3\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0';
		}
		elseif (preg_match('/2\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '2.9';
		}
		elseif (preg_match('/2\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '2.8';
		}
		elseif (preg_match('/2\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '2.7';
		}
		elseif (preg_match('/2\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '2.6';
		}
		elseif (preg_match('/2\.5\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5.9';
		}
		elseif (preg_match('/2\.5\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5.8';
		}
		elseif (preg_match('/2\.5\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5.7';
		}
		elseif (preg_match('/2\.5\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5.6';
		}
		elseif (preg_match('/2\.5\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5.5';
		}
		elseif (preg_match('/2\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5';
		}
		elseif (preg_match('/2\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '2.4';
		}
		elseif (preg_match('/2\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '2.3';
		}
		elseif (preg_match('/2\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '2.2';
		}
		elseif (preg_match('/2\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '2.1';
		}
		elseif (preg_match('/2\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '2';
		}
		elseif (preg_match('/1\./i', $agentstring))
		{
			$browser_alt = $_alt. '1';
		}
	}
	elseif (false !== strpos($agentstring, 'lunascape'))
	{
		$browser = 'lunascape';
		$browser_alt = 'Lunascape';
		$_alt = 'Lunascape ';
		if (preg_match('/6\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '6.6';
		}
		elseif (preg_match('/6\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '6.5';
		}
		elseif (preg_match('/6\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '6.4';
		}
		elseif (preg_match('/6\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '6.3';
		}
		elseif (preg_match('/6\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '6.2';
		}
		elseif (preg_match('/6\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '6.1';
		}
		elseif (preg_match('/6\.0.3/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.3';
		}
		elseif (preg_match('/6\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.2';
		}
		elseif (preg_match('/6\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.1';
		}
		elseif (preg_match('/6\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.0';
		}
		elseif (preg_match('/5\.0\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.8';
		}
		elseif (preg_match('/5\.0\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.7';
		}
		elseif (preg_match('/5.0.6/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.6';
		}
		elseif (preg_match('/5\.0\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.5';
		}
		elseif (preg_match('/5\.0\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.4';
		}
		elseif (preg_match('/5\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.3';
		}
		elseif (preg_match('/5\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.2';
		}
		elseif (preg_match('/5\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.1';
		}
		elseif (preg_match('/5\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.0';
		}
		elseif (preg_match('/4\.9\.9/i', $agentstring))
		{
			$browser_alt = $_alt. 'Beta';
		}
		elseif (preg_match('/4\.8.1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.8.1';
		}
		elseif (preg_match('/4\.7.3/i', $agentstring))
		{
			$browser_alt = $_alt. '4.7.3';
		}
	}
	elseif (false !== strpos($agentstring, 'chrome'))
	{
		$browser = 'chrome';
		$browser_alt = 'Google Chrome';
		$_alt = 'Google Chrome ';
		if (preg_match('/Mobile/i', $agentstring))
		{
			$browser_alt = $_alt. 'Mobile';
		}
		elseif (preg_match('/26\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '26';
		}
		elseif (preg_match('/25\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '25';
		}
		elseif (preg_match('/24\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '24';
		}
		elseif (preg_match('/23\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '23';
		}
		elseif (preg_match('/22\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '22';
		}
		elseif (preg_match('/21\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '21';
		}
		elseif (preg_match('/20\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '20';
		}
		elseif (preg_match('/19\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '19';
		}
		elseif (preg_match('/18\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '18';
		}
		elseif (preg_match('/17\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '17';
		}
		elseif (preg_match('/16\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '16';
		}
		elseif (preg_match('/15\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '15';
		}
		elseif (preg_match('/14\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '14';
		}
		elseif (preg_match('/13\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '13';
		}
		elseif (preg_match('/12\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '12';
		}
		elseif (preg_match('/11\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '11';
		}
		elseif (preg_match('/10\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '10';
		}
		elseif (preg_match('/9\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '9';
		}
		elseif (preg_match('/8\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '8';
		}
		elseif (preg_match('/7\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '7';
		}
		elseif (preg_match('/6\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '6';
		}
		elseif (preg_match('/5\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '5';
		}
		elseif (preg_match('/4\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '4';
		}
		elseif (preg_match('/3\.1([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '3.1';
		}
		elseif (preg_match('/3\.0([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0';
		}
		elseif (preg_match('/2\.1([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '2.1';
		}
		elseif (preg_match('/2\.0([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '2.0';
		}
		elseif (preg_match('/1\.0([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '1.0';
		}
		elseif (preg_match('/1\.1([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '1.1';
		}
	}
	elseif (false !== strpos($agentstring, 'mozilla firebird') || false !== strpos($agentstring, 'firebird'))
	{
		$browser = 'mozilla';
		$browser_alt = 'Mozilla Firebird';
	}
	elseif (false !== strpos($agentstring, 'fedora'))
	{
		$browser = 'fedora';
		$browser_alt = 'Fedora';
	}
	elseif (false !== strpos($agentstring, 'navigator') || false !== strpos($agentstring, 'netscape'))
	{
		$browser = 'netscape';
		$browser_alt = 'Netscape Navigator';
		$_alt = 'Netscape Navigator ';
		if (preg_match('/9\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '9.4';
		}
		elseif (preg_match('/9\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '9.3';
		}
		elseif (preg_match('/9\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '9.2';
		}
		elseif (preg_match('/9\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '9.1';
		}
		elseif (preg_match('/9\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0';
		}
		elseif (preg_match('/8\./i', $agentstring))
		{
			$browser_alt = $_alt. '8';
		}
		elseif (preg_match('/7\./i', $agentstring))
		{
			$browser_alt = $_alt. '7';
		}
		elseif (preg_match('/6\./i', $agentstring))
		{
			$browser_alt = $_alt. '6';
		}
	}
	elseif (false !== strpos($agentstring, 'firefox'))
	{
		$browser = 'firefox';
		$browser_alt = 'Firefox';
		$_alt = 'Firefox ';
		if (preg_match('/Mobile/i', $agentstring))
		{
			$browser_alt = $_alt. 'Mobile';
		}
		elseif (preg_match('/'.$browser.'\/20\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '20.0';
		}
		elseif (preg_match('/'.$browser.'\/19\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '19.0';
		}
		elseif (preg_match('/'.$browser.'\/18\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '18.0';
		}
		elseif (preg_match('/'.$browser.'\/17\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '17.0';
		}
		elseif (preg_match('/'.$browser.'\/16\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '16.0';
		}
		elseif (preg_match('/'.$browser.'\/15\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '15.0';
		}
		elseif (preg_match('/'.$browser.'\/14\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '14.0';
		}
		elseif (preg_match('/'.$browser.'\/13\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '13.0';
		}
		elseif (preg_match('/'.$browser.'\/12\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '12.0';
		}
		elseif (preg_match('/'.$browser.'\/11\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '11.0';
		}
		elseif (preg_match('/'.$browser.'\/10\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '10.5';
		}
		elseif (preg_match('/'.$browser.'\/10\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '10.5';
		}
		elseif (preg_match('/'.$browser.'\/10\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '10.4';
		}
		elseif (preg_match('/'.$browser.'\/10\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '10.3';
		}
		elseif (preg_match('/'.$browser.'\/10\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '10.2';
		}
		elseif (preg_match('/'.$browser.'\/10\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '10.2';
		}
		elseif (preg_match('/'.$browser.'\/10\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '10.1';
		}
		elseif (preg_match('/'.$browser.'\/10\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '10.0';
		}
		elseif (preg_match('/'.$browser.'\/9\.0\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0.5';
		}
		elseif (preg_match('/'.$browser.'\/9\.0\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0.4';
		}
		elseif (preg_match('/'.$browser.'\/9\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0.3';
		}
		elseif (preg_match('/'.$browser.'\/9\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0.2';
		}
		elseif (preg_match('/'.$browser.'\/9\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0.1';
		}
		elseif (preg_match('/'.$browser.'\/9\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0.0';
		}
		elseif (preg_match('/'.$browser.'\/8\.0\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '8.0.5';
		}
		elseif (preg_match('/'.$browser.'\/8\.0\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '8.0.4';
		}
		elseif (preg_match('/'.$browser.'\/8\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '8.0.3';
		}
		elseif (preg_match('/'.$browser.'\/8\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '8.0.2';
		}
		elseif (preg_match('/'.$browser.'\/8\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '8.0.1';
		}
		elseif (preg_match('/'.$browser.'\/8\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '8.0.0';
		}
		elseif (preg_match('/'.$browser.'\/7\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '7.0.0';
		}
		elseif (preg_match('/'.$browser.'\/6\.0\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.5';
		}
		elseif (preg_match('/'.$browser.'\/6\.0\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.4';
		}
		elseif (preg_match('/'.$browser.'\/6\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.3';
		}
		elseif (preg_match('/'.$browser.'\/6\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.2';
		}
		elseif (preg_match('/'.$browser.'\/6\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0.1';
		}
		elseif (preg_match('/'.$browser.'\/6\.0\.0/i', $agentstring) || preg_match('/'.$browser.'\/6\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0';
		}
		elseif (preg_match('/'.$browser.'\/6\.0b/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0 Beta';
		}
		elseif (preg_match('/'.$browser.'\/6\.0a/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0 Aurora';
		}
		elseif (preg_match('/'.$browser.'\/6\.0n/i', $agentstring))
		{
			$browser_alt = $_alt. '6.0 Nightly';
		}
		elseif (preg_match('/'.$browser.'\/5\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.1';
		}
		elseif (preg_match('/'.$browser.'\/5\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0';
		}
		elseif (preg_match('/'.$browser.'\/5\.0b/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0 Beta';
		}
		elseif (preg_match('/'.$browser.'\/5\.0a/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0 Aurora';
		}
		elseif (preg_match('/'.$browser.'\/4\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '4.5';
		}
		elseif (preg_match('/'.$browser.'\/4\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '4.4';
		}
		elseif (preg_match('/'.$browser.'\/4\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '4.3';
		}
		elseif (preg_match('/'.$browser.'\/4\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '4.2';
		}
		elseif (preg_match('/'.$browser.'\/4\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.1';
		}
		elseif (preg_match('/'.$browser.'\/4\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.3';
		}
		elseif (preg_match('/'.$browser.'\/4\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.2';
		}
		elseif (preg_match('/'.$browser.'\/4\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.1';
		}
		elseif (preg_match('/'.$browser.'\/4\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.0';
		}
		elseif (preg_match('/'.$browser.'\/4\.0b/i', $agentstring) || preg_match('/'.$browser.'\/4.0b1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0 Beta';
		}
		elseif (preg_match('/'.$browser.'\/3\.8./i', $agentstring))
		{
			$browser_alt = $_alt. '3.8';
		}
		elseif (preg_match('/'.$browser.'\/3\.7\./i', $agentstring))
		{
			$browser_alt = $_alt. '3.7';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.20/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.20';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.19/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.19';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.18/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.18';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.17/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.17';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.16/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.16';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.15/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.15';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.14/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.14';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.13/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.13';
		}
		elseif (preg_match('/'.$browser.'\/3\.6.12/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.12';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.11/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.11';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.10/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.10';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.9';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.8';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.7';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.6';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.5';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.4';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.3';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.2';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.1';
		}
		elseif (preg_match('/'.$browser.'\/3\.6\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6.0';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.9';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.8';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.7';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.6';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.5';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.5';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.4';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.2';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.1';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5.0';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.20/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.20';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.19/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.19';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.18/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.18';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.17/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.17';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.16/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.16';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.15/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.15';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.14/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.14';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.13/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.13';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.12/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.12';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.11/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.11';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.10/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.10';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.9';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.8';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.7';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.6';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.5';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.4';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.3';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.2';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.1';
		}
		elseif (preg_match('/'.$browser.'\/3\.0\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.0';
		}
		elseif (preg_match('/'.$browser.'\/2\.0/i', $agentstring) && !(preg_match('/20090824/i', $agentstring)))  // No confusion with 3.5.3 version
		{
			$browser_alt = $_alt. '2.0';
		}
		elseif (preg_match('/'.$browser.'\/3\.5\.3/i', $agentstring))  // This version here because of confusion...
		{
			$browser_alt = $_alt. '3.5.3';
		}
		elseif (preg_match('/'.$browser.'\/2([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '2';
		}
		elseif (preg_match('/'.$browser.'\/1([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '1';
		}
	}
	elseif (false !== strpos($agentstring, 'galeon'))
	{
		$browser = 'galeon';
		$browser_alt = 'Galeon';
	}
	elseif (false !== strpos($agentstring, 'gentoo'))
	{
		$browser = 'gentoo';
		$browser_alt = 'Gentoo';
	}
	elseif (false !== strpos($agentstring, 'msie') || false !== strpos($agentstring, 'microsoft internet explorer') || false !== strpos($agentstring, 'internet explorer'))
	{
		$browser = 'ie';
		$x64 = '';  //  IE 64 bits version can be detected
		if (preg_match('/x64/i', $agentstring))
		{
			$x64 = ' (64 bits)';
		}
		if (preg_match('/Avant Browser/i', $agentstring) || preg_match('/Avant Browse/i', $agentstring))  // Avant Browser is here because he is based on internet explorer
		{
			$browser_alt = 'Avant Browser' .$x64;
			$browser = 'avantbrowser';
		}
		elseif (preg_match('/MSIE 10\.0/i', $agentstring))
		{
			$browser_alt = 'Internet Explorer 10' .$x64;

			$browser = 'ie7';
		}
		elseif (preg_match('/MSIE 9\.0/i', $agentstring))
		{
			$browser_alt = 'Internet Explorer 9' .$x64;
			$browser = 'ie7';
		}
		elseif (preg_match('/MSIE 8\.0/i', $agentstring))
		{
			$browser_alt = 'Internet Explorer 8' .$x64;
			$browser = 'ie7';
		}
		elseif (preg_match('/MSIE 7\.0/i', $agentstring))
		{
			$browser_alt = 'Internet Explorer 7' .$x64;
			$browser = 'ie7';
		}
		elseif (preg_match('/MSIE 6\.0/i', $agentstring))
		{
			$browser_alt = 'Internet Explorer 6' .$x64;
		}
		elseif (preg_match('/MSIE 5\.0/i', $agentstring))
		{
			$browser_alt = 'Internet Explorer 5';
		}
	}
	elseif (false !== strpos($agentstring, 'k-meleon'))
	{
		$browser = 'kmeleon';
		$browser_alt = 'K-meleon';
	}
	elseif (false !== strpos($agentstring, 'konqueror'))
	{
		$browser = 'konqueror';
		$browser_alt = 'Konqueror';
		$_alt = 'Konqueror ';
		if (preg_match('/4\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '4.6';
		}
		elseif (preg_match('/4\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '4.5';
		}
		elseif (preg_match('/4\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '4.4';
		}
		elseif (preg_match('/4\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '4.3';
		}
		elseif (preg_match('/4\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '4.2';
		}
		elseif (preg_match('/4\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.1';
		}
		elseif (preg_match('/4\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0';
		}
		elseif (preg_match('/3\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6';
		}
		elseif (preg_match('/3\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5';
		}
		elseif (preg_match('/3\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '3.4';
		}
		elseif (preg_match('/3\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '3.3';
		}
		elseif (preg_match('/3\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.2';
		}
		elseif (preg_match('/3\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '3.1';
		}
		elseif (preg_match('/3\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0';
		}
	}
	elseif (false !== strpos($agentstring, 'lynx'))
	{
		$browser = 'lynx';
		$browser_alt = 'Lynx';
	}
	elseif (false !== strpos($agentstring, 'safari'))
	{
		$browser = 'safari';
		$browser_alt = 'Safari';
		$_alt = 'Safari ';
		if (false !== strpos($agentstring, 'mobile'))
		{
			$_alt = 'Mobile ' .$_alt;
		}
		if (preg_match('/5\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '5.4';
		}
		elseif (preg_match('/5\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '5.2';
		}
		elseif (preg_match('/5\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '5.1';
		}
		elseif (preg_match('/5\.0\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.9';
		}
		elseif (preg_match('/5\.0\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.8';
		}
		elseif (preg_match('/5.0.7/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.7';
		}
		elseif (preg_match('/5\.0\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.6';
		}
		elseif (preg_match('/5\.0\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.5';
		}
		elseif (preg_match('/5\.0\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.4';
		}
		elseif (preg_match('/5\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.3';
		}
		elseif (preg_match('/5\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.2';
		}
		elseif (preg_match('/5\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0.1';
		}
		elseif (preg_match('/5\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '5.0';
		}
		elseif (preg_match('/5\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '5.3';
		}
		elseif (preg_match('/5\./i', $agentstring))
		{
			$browser_alt = $_alt. '5';
		}
		elseif (preg_match('/4\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '4.6';
		}
		elseif (preg_match('/4\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '4.5';
		}
		elseif (preg_match('/4\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '4.4';
		}
		elseif (preg_match('/4\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '4.3';
		}
		elseif (preg_match('/4\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '4.2';
		}
		elseif (preg_match('/4\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.1';
		}
		elseif (preg_match('/4\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0';
		}
		elseif (preg_match('/3\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '3.6';
		}
		elseif (preg_match('/3\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '3.5';
		}
		elseif (preg_match('/3\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '3.4';
		}
		elseif (preg_match('/3\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '3.3';
		}
		elseif (preg_match('/3\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.2';
		}
		elseif (preg_match('/3\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '3.1';
		}
		elseif (preg_match('/3\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0';
		}
		elseif (preg_match('/2\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '2.0';
		}
		elseif (preg_match('/1\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '1.3';
		}
		elseif (preg_match('/1\.2\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '1.2.4';
		}
		elseif (preg_match('/1\.2\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '1.2.3';
		}
		elseif (preg_match('/1\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '1.2';
		}
		elseif (preg_match('/1\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '1.1';
		}
		elseif (preg_match('/1\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '1.0';
		}
	}
	elseif (false !== strpos($agentstring, 'seamonkey'))
	{
		$browser = 'seamonkey';
		$browser_alt = 'SeaMonkey';
		$_alt = 'SeaMonkey ';
		if (preg_match('/2\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '2.4';
		}
		elseif (preg_match('/2\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '2.3';
		}
		elseif (preg_match('/2\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '2.2';
		}
		elseif (preg_match('/2\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '2.1';
		}
		elseif (preg_match('/2\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '2.0';
		}
		elseif (preg_match('/1\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '1.2';
		}
		elseif (preg_match('/1\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '1.1';
		}
		elseif (preg_match('/1\.0([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '1.0';
		}
		elseif (preg_match('/2\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '2.0';
		}
	}
	elseif (false !== strpos($agentstring, 'opera'))
	{
		$browser = 'opera';
		$browser_alt = 'Opera';
		$_alt = 'Opera ';
		if (false !== strpos($agentstring, 'opera mobi'))
		{
			$_alt = $_alt. 'Mobile ';
		}
		if (preg_match('/version\/13\./i', $agentstring))
		{
			$browser_alt = $_alt. '13';
		}
		elseif (preg_match('/version\/12\./i', $agentstring))
		{
			$browser_alt = $_alt. '12';
		}
		elseif (preg_match('/version\/11\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '11.9';
		}
		elseif (preg_match('/version\/11\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '11.8';
		}
		elseif (preg_match('/version\/11\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '11.7';
		}
		elseif (preg_match('/version\/11\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '11.6';
		}
		elseif (preg_match('/version\/11\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '11.5';
		}
		elseif (preg_match('/version\/11\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '11.4';
		}
		elseif (preg_match('/version\/11\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '11.3';
		}
		elseif (preg_match('/version\/11\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '11.2';
		}
		elseif (preg_match('/version\/11\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '11.1';
		}
		elseif (preg_match('/version\/11\.05/i', $agentstring))
		{
			$browser_alt = $_alt. '11.05';
		}
		elseif (preg_match('/version\/11.\04/i', $agentstring))
		{
			$browser_alt = $_alt. '11.04';
		}
		elseif (preg_match('/version\/11\.03/i', $agentstring))
		{
			$browser_alt = $_alt. '11.03';
		}
		elseif (preg_match('/version\/11\.02/i', $agentstring))
		{
			$browser_alt = $_alt. '11.02';
		}
		elseif (preg_match('/version\/11\.01/i', $agentstring))
		{
			$browser_alt = $_alt. '11.01';
		}
		elseif (preg_match('/version\/11\./i', $agentstring))
		{
			$browser_alt = $_alt. '11';
		}
		elseif (preg_match('/version\/10\.64/i', $agentstring))
		{
			$browser_alt = $_alt. '10.64';
		}
		elseif (preg_match('/version\/10\.63/i', $agentstring))
		{
			$browser_alt = $_alt. '10.63';
		}
		elseif (preg_match('/version\/10\.62/i', $agentstring))
		{
			$browser_alt = $_alt. '10.62';
		}
		elseif (preg_match('/version\/10\.61/i', $agentstring))
		{
			$browser_alt = $_alt. '10.61';
		}
		elseif (preg_match('/version\/10\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '10.60';
		}
		elseif (preg_match('/version\/10\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '10.5';
		}
		elseif (preg_match('/version\/10\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '10.4';
		}
		elseif (preg_match('/version\/10\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '10.2';
		}
		elseif (preg_match('/version\/10\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '10.1';
		}
		elseif (preg_match('/version\/10\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '10.0';
		}
		elseif (preg_match('/version\/9\.9/i', $agentstring))
		{
			$browser_alt = $_alt. '9.9';
		}
		elseif (preg_match('/version\/9\.8/i', $agentstring))
		{
			$browser_alt = $_alt. '9.8';
		}
		elseif (preg_match('/version\/9\.7/i', $agentstring))
		{
			$browser_alt = $_alt. '9.7';
		}
		elseif (preg_match('/version\/9\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '9.6';
		}
		elseif (preg_match('/version\/9\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '9.5';
		}
		elseif (preg_match('/version\/9\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '9.4';
		}
		elseif (preg_match('/version\/9\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '9.3';
		}
		elseif (preg_match('/version\/9\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '9.2';
		}
		elseif (preg_match('/version\/9\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '9.1';
		}
		elseif (preg_match('/version\/9\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '9.0';
		}
		elseif (preg_match('/version\/8\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '8';
		}
		elseif (preg_match('/version\/7\.([a-zA-Z0-9.]+)/i', $agentstring))
		{
			$browser_alt = $_alt. '7';
		}
	}
	elseif (false !== strpos($agentstring, 'android'))
	{
		$browser = 'android';
		$browser_alt = 'Android';
		$_alt = 'Android ';
		if (preg_match('/4\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0';
		}
		elseif (preg_match('/4\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.1';
		}
		elseif (preg_match('/4\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.2';
		}
		elseif (preg_match('/4\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '4.0.3';
		}
		elseif (preg_match('/3\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0';
		}
		elseif (preg_match('/3\.0\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.1';
		}
		elseif (preg_match('/3\.0\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.2';
		}
		elseif (preg_match('/3\.0\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '3.0.3';
		}
		elseif (preg_match('/2\.6/i', $agentstring))
		{
			$browser_alt = $_alt. '2.6';
		}
		elseif (preg_match('/2\.5/i', $agentstring))
		{
			$browser_alt = $_alt. '2.5';
		}
		elseif (preg_match('/2\.4/i', $agentstring))
		{
			$browser_alt = $_alt. '2.4';
		}
		elseif (preg_match('/2\.3/i', $agentstring))
		{
			$browser_alt = $_alt. '2.3';
		}
		elseif (preg_match('/2\.2\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '2.2.1';
		}
		elseif (preg_match('/2\.2/i', $agentstring))
		{
			$browser_alt = $_alt. '2.2';
		}
		elseif (preg_match('/2\.1/i', $agentstring))
		{
			$browser_alt = $_alt. '2.1';
		}
		elseif (preg_match('/2\.0/i', $agentstring))
		{
			$browser_alt = $_alt. '2.0';
		}
	}
	elseif (false !== strpos($agentstring, 'omniweb'))
	{
		$browser = 'omniweb';
		$browser_alt = 'Omniweb';
	}
	elseif (false !== strpos($agentstring, 'mozilla'))
	{
		$browser = 'mozilla';
		$browser_alt = 'Mozilla';
	}
	else
	{
		$browser = 'anonymouse';
		$browser_alt = $user->lang['BROWSER_UNKNOW'];
	}
	return $source . $browser. '.png" alt="' .$browser_alt. '" title="' .$user->lang['BROWSER_NAME'] . $browser_alt. '" height="16" style="vertical-align: -10%;" /> ' .$browser_alt;
}

?>