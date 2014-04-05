<?php
/**
*
* @package install
* @version $Id: install_main.php 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/
if (!defined('IN_INSTALL') || !defined('IN_PHPBB'))
{
	exit;
}

if (!empty($setmodules))
{
	$module[] = array(
		'module_type'		=> 'install',
		'module_title'		=> 'OVERVIEW',
		'module_filename'	=> substr(basename(__FILE__), 0, -strlen($phpEx)-1),
		'module_order'		=> 0,
		'module_subs'		=> array('INTRO', 'LICENSE', 'SUPPORT'),
		'module_stages'		=> '',
		'module_reqs'		=> ''
	);
}

/**
* Main Tab - Installation
* @package install
*/
class install_main extends module
{
	function install_main(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($mode, $sub)
	{
		global $user, $template;

		switch ($sub)
		{
			case 'intro' :
				$title = $user->lang['SUB_INTRO'];
				$body = $user->lang['OVERVIEW_BODY'];
			break;

			case 'license' :
				$title = $user->lang['GPL'];
				$body = implode("<br/>\n", file('LICENSE'));
			break;

			case 'support' :
				$title = $user->lang['SUB_SUPPORT'];
				$body = $user->lang['SUPPORT_BODY'];
			break;
		}

		$this->tpl_name = 'install_main';
		$this->page_title = $title;

		$template->assign_vars(array(
			'TITLE'		=> $title,
			'BODY'		=> $body,
		));
	}
}

?>