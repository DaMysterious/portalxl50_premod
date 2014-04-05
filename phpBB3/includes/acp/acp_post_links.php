<?php

/**
*
* @package post_links
* @version $Id: acp_post_links.php 2010-03-19 22:46:00Z Senky $
* @copyright (c) 2010 Senky
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

class acp_post_links
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $user, $template;

		$user->add_lang('mods/info_acp_post_links');
   
		$this->page_title = $user->lang['ACP_POST_LINKS_TITLE'];
		$this->tpl_name = 'acp_post_links';
		
		add_form_key('acp_post_links');
		
		$template->assign_vars(array(
			'PL_ENABLE' => $config['pl_enable'],
			'PL_LINK_ENABLE' => $config['pl_link'],
			'PL_BBCODE_ENABLE' => $config['pl_bbcode'],
			'PL_HTML_ENABLE' => $config['pl_html'],
		));

		$submit = (isset($_POST['submit'])) ? true : false;
		if ($submit && !check_form_key('acp_post_links'))
		{
			trigger_error($user->lang['FORM_INVALID']);
		}
		if ($submit)
		{
			set_config('pl_enable', request_var('pl_enable', 0));
			set_config('pl_link', request_var('pl_link', 0));
			set_config('pl_bbcode', request_var('pl_bbcode', 0));
			set_config('pl_html', request_var('pl_html', 0));
			trigger_error($user->lang['POST_LINKS_UPDATED'] . adm_back_link($this->u_action));
		}
	}
}

?>