<?php
/*
*
* @name functions_portal.php
* @package phpBB3 Portal XL 5.0
* @version $Id: functions_portal.php,v 1.5 2011/07/13 portalxl group Exp $
*
* @copyright (c) 2007, 2015 PortalXL Group
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
*
*/

/**
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Available functions
*
* set_portal_config()
* portal_init()
* portal_end()
* portal_login_box()
* generate_repository()
*
*/

/**
* Set portal config value. Creates missing config entry.
*/
function set_portal_config($config_name, $config_value, $is_dynamic = false)
{
	global $db, $cache, $portal_config;

	$sql = 'UPDATE ' . PORTAL_CONFIG_TABLE . "
		SET config_value = '" . $db->sql_escape($config_value) . "'
		WHERE config_name = '" . $db->sql_escape($config_name) . "'";
	$db->sql_query($sql);

	if (!$db->sql_affectedrows() && !isset($portal_config[$config_name]))
	{
		$sql = 'INSERT INTO ' . PORTAL_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'config_name'	=> $config_name,
			'config_value'	=> $config_value,
			'is_dynamic'	=> ($is_dynamic) ? 1 : 0));
		$db->sql_query($sql);
	}

	$portal_config[$config_name] = $config_value;
	if (!$is_dynamic)
	{
		$cache->destroy('portal_config');
	}
}

/**
* invoke the portal here called by portal_init()
*/
function portal_init()
{
	/**
	* initalise some global variables
	*/
	global $portal_config, $db, $config, $template, $SID, $_SID, $user, $auth, $phpEx, $phpbb_root_path, $user_id, $username, $colour;

	/*
	* Start session management
	*/
	$user->add_lang('mods/portal_xl_user_info');
	$user->add_lang('mods/portal_xl');

	/**
	* invoke the random logo function boardwide
	*/
	$rand_logo = "";
	$imglist = "";
	$imgs ="";
	
	mt_srand((double)microtime()*1000000);

	$logos_dir = "{$phpbb_root_path}styles/" . $user->theme['imageset_path'] . '/imageset/logos';
	// $logos_dir = "{$phpbb_root_path}portal/images/logos";
	
	$handle = opendir( $logos_dir );
//	if( !$handle) echo 'Check to see if image directory eg. /portal/images/logos does exist!';
	if( !$handle) echo 'Check to see if image directory eg. /styles/prosilver/imageset/logos does exist!';

	if(!$handle) // we don't have a logo directory or we are attempting to login to ACP we need to return the default logo than //
	return $user->img('site_logo');
	
	while (false!==($file = readdir($handle)))
	{
//		if (eregi("gif", $file) || eregi("jpg", $file) || eregi("png", $file))  
		// It will match if it finds any occurrence of "jpg" or "gif" or 'png' within the string.
		// The "i" after the pattern delimiter indicates a case-insensitive search.
		if (preg_match('#^[^&\'"<>]+\.(?:gif|png|jpe?g)$#i', $file))
		{
			$imglist .= "$file ";
		}
	}
	closedir($handle);
	
	$imglist = explode(" ", $imglist);
	$a = sizeof($imglist)-2;
	$random = mt_rand(0, $a);
	$image = $imglist[$random];

	$rand_logo .= "<img src=\"{$logos_dir}/$image\" border=\"0\" alt=\"\" />";
	//$rand_logo .= "<center><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"{$phpbb_root_path}portal/images/logos/$image\" border=\"0\" alt=\"\"><br/ ></center>";
	$template->assign_vars(array('SITE_LOGO_IMGAGE' => $rand_logo));
	$imgs ='';

	/**
	* is portal active by @define('PORTAL', true); in config.php?
	*/
    if (defined('PORTAL')) {
		page_header($config['sitename'] . ' : ' . $user->lang['PORTAL']);
	
		$template->assign_vars(array(
			'PORTAL'						=> defined('PORTAL') ? true : false,
			'PORTAL_PAGE'					=> ($portal_config['portal_layout']) ? true : false,
			'U_HOME'						=> "{$phpbb_root_path}portal.$phpEx",

			'S_DISPLAY_PORTAL_LEFT'			=> ($portal_config['portal_left_column']) ? true : false,
			'S_DISPLAY_PORTAL_RIGHT'		=> ($portal_config['portal_right_column']) ? true : false,
			'PORTAL_LEFT_COLLUMN_WIDTH' 	=> $portal_config['portal_left_collumn_width'],
			'PORTAL_RIGHT_COLLUMN_WIDTH' 	=> $portal_config['portal_right_collumn_width'],
		));		
	}
	
	/**
	* is portal index active by @define('PORTAL_INDEX_PAGE', true); in config.php?
	*/
    if (defined('PORTAL_INDEX')) {
		page_header($config['sitename'] . ' : ' . $user->lang['PORTAL_INDEX']);
		
		$template->assign_vars(array(
			'PORTAL_INDEX'						=> defined('PORTAL_INDEX') ? true : false,
			'PORTAL_INDEX_PAGE'					=> ($portal_config['portal_index_layout']) ? true : false,
			'U_HOME'							=> "{$phpbb_root_path}index.$phpEx",
			
			'SI_DISPLAY_INDEX_LEFT'				=> ($portal_config['portal_index_left_column']) ? true : false,
			'SI_DISPLAY_INDEX_RIGHT'			=> ($portal_config['portal_index_right_column']) ? true : false,
			'PORTAL_INDEX_LEFT_COLLUMN_WIDTH'   => $portal_config['portal_index_left_collumn_width'],
			'PORTAL_INDEX_RIGHT_COLLUMN_WIDTH'  => $portal_config['portal_index_right_collumn_width'],
		));		
	}

	/**
	* is portal index active by @define('PORTAL_INDEX_PAGE', true); in config.php?
	*/
    if (defined('PORTAL_PAGES')) {
		page_header($config['sitename'] . ' : ' . $user->lang['PAGES_LIST_TITLE']);
		
		$template->assign_vars(array(
			'PORTAL_PAGES'						=> defined('PORTAL_PAGES') ? true : false,
			'PORTAL_PAGES_PAGE'					=> ($portal_config['portal_pages_layout']) ? true : false,
			'U_HOME'							=> "{$phpbb_root_path}portal.$phpEx",
			
			'SI_DISPLAY_PAGES_LEFT'				=> ($portal_config['portal_pages_left_column']) ? true : false,
			'SI_DISPLAY_PAGES_RIGHT'			=> ($portal_config['portal_pages_right_column']) ? true : false,
			'PORTAL_PAGES_LEFT_COLLUMN_WIDTH'   => $portal_config['portal_pages_left_collumn_width'],
			'PORTAL_PAGES_RIGHT_COLLUMN_WIDTH'  => $portal_config['portal_pages_right_collumn_width'],
		));		
	}
	
	/**
	* boardwide template variables
	*/
	$template->assign_vars(array(
		'PORTAL_VERSION'					=> htmlspecialchars_decode(sprintf($portal_config['portal_version'])),

		'L_PORTAL_VERSION'					=> $user->lang['PORTAL_VERSION'],
		'L_PORTAL_SYNDICATE'				=> $user->lang['PORTAL_SYNDICATE'],
		'L_NAVX'			            	=> $user->lang['PORTAL_NAVX'],
		'U_PORTAL'							=> append_sid("{$phpbb_root_path}portal.$phpEx"),
		'U_PORTAL_PAGES'					=> append_sid("{$phpbb_root_path}portal_pages.$phpEx"),
        'U_PORTAL_MODS'        				=> append_sid("{$phpbb_root_path}portal/portal_mods.$phpEx"),
        'U_PORTAL_ACRONYM'        			=> append_sid("{$phpbb_root_path}portal/portal_acronyms.$phpEx"),
        'U_SYNDICATE_FORUM'    				=> append_sid("{$phpbb_root_path}portal/syndicate.$phpEx"),
        'U_SYNDICATE_FILES'    				=> append_sid("{$phpbb_root_path}portal/syndicate_attachments.$phpEx"),
        'U_SYNDICATE_DOWNLOADS'				=> append_sid("{$phpbb_root_path}portal/syndicate_downloads.$phpEx"),
        'U_SYNDICATE_KB'					=> append_sid("{$phpbb_root_path}portal/syndicate_kb.$phpEx"),
        'U_SYNDICATE_GALLERY'				=> append_sid("{$phpbb_root_path}portal/syndicate_gallery.$phpEx"),
        'U_SYNDICATE_ARCADE'				=> append_sid("{$phpbb_root_path}portal/syndicate_arcade.$phpEx"),
        'U_SYNDICATE_VIDEO'					=> append_sid("{$phpbb_root_path}portal/syndicate_video.$phpEx"),
		'U_KB'								=> append_sid("{$phpbb_root_path}kb.$phpEx"),
		'L_KB'								=> (isset($config['kb_link_name']) && $config['kb_link_name'] != '') ? $config['kb_link_name'] : $user->lang['KB'],
		'S_CONTACT_ENABLED'					=> ($config['contact_enable']) ? true : false,
		'U_CONTACT'							=> append_sid("{$phpbb_root_path}contact.$phpEx"),
		'U_CALENDAR'						=> append_sid("{$phpbb_root_path}calendar.$phpEx"),
		'U_PORTAL_RECENT_TOPICS'			=> append_sid("{$phpbb_root_path}portal/portal_recent_topics.$phpEx"),
		'U_PORTAL_RANKS'			        => append_sid("{$phpbb_root_path}portal/portal_ranks.$phpEx"),
		'U_PORTAL_SMILES'			        => append_sid("{$phpbb_root_path}portal/portal_smiles.$phpEx"),
		'U_PORTAL_FLAGS'			        => append_sid("{$phpbb_root_path}portal/portal_flags.$phpEx"),
		'U_PORTAL_GROUPS'			        => append_sid("{$phpbb_root_path}portal/portal_groups.$phpEx"),
		'U_PORTAL_BBCODES'			        => append_sid("{$phpbb_root_path}portal/portal_bbcodes.$phpEx"),
		'U_PORTAL_REFERERS'			        => append_sid("{$phpbb_root_path}portal/portal_referers.$phpEx"),
		'U_DOWNLOADS' 					=> append_sid("{$phpbb_root_path}downloads.$phpEx"),
      	        'U_RADIO'               			=> append_sid("{$phpbb_root_path}portal/radio/radio.$phpEx"),

		'S_AJAX_USERINFO_ENABLED'			=> ($portal_config['portal_show_ajax_userinfo']) ? true : false,
		'S_TOPIC_HOVER_PREVIEW_ENABLED'		=> ($portal_config['portal_show_topic_hover_preview']) ? true : false,
		'S_TOOL_TIPS_ENABLED'				=> ($portal_config['portal_show_tool_tips']) ? true : false,
	    'S_PORTAL_SHOW_THANKS'				=> ($portal_config['portal_show_thanks']) ? true : false,
		'S_PORTAL_BBCODE_BOX_ENABLED'		=> ($portal_config['portal_show_bbcode_box']) ? true : false,
		'S_PORTAL_ZODIACS_ENABLED'			=> ($portal_config['portal_show_zodiacs']) ? true : false,
		'S_PORTAL_LOGO_ENABLED'				=> ($portal_config['portal_show_logo']) ? true : false,
		'S_PORTAL_SITENAME_ENABLED'			=> ($portal_config['portal_show_sitename']) ? true : false,
		'S_PORTAL_DRAG_DROP'				=> ($portal_config['portal_drag_drop']) ? true : false,

		'PORTAL_PICTURE_RESIZE'  			=> $portal_config['portal_picture_resize'],
		'U_PORTAL_FAQ'						=> append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=portal'),
		'U_RULES_FAQ'						=> append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=rules'),

		'L_CRAWLER_LINKS_TOTAL'				=> $user->lang['CRAWLER_LINKS_TOTAL'],
		'L_CRAWLER_LINKS'					=> $user->lang['CRAWLER_LINKS'],
		'U_CRAWLER_LINKER'					=> append_sid("{$phpbb_root_path}portal/portal_crawler_linker.$phpEx"),
	
		'USERNAME_FULL'     				=> get_username_string('full', $user_id, $username, $colour),
		'U_USERNAME'          				=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $user->data['user_id']),
		'USER_COLOR'        				=> get_username_string('colour', $user_id, $username, $colour),
		'U_VIEW_PROFILE'    				=> get_username_string('profile', $user_id, $username, $colour),
	
		'U_NEW_SEARCH'						=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=newposts'),
		'U_SELF_SEARCH'						=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=egosearch'),
		'U_UNANSWERED_SEARCH'				=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=unanswered'),
		'U_ACTIVE_TOPICS_SEARCH'			=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=active_topics'),
	
		'U_FRONTPAGE'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=main&amp;mode=front'),
		'U_BOOKMARKS'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=main&amp;mode=bookmarks'),
		'U_SUBSCRIBED'  	       			=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=main&amp;mode=subscribed'),
		'U_DRAFTS'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=main&amp;mode=drafts'),
		'U_ATTACHMENTS'      				=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=main&amp;mode=attachments'),
		
		'U_UPROFILE'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=profile&amp;mode=profile_info'),
		'U_SIGNATURE'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=profile&amp;mode=signature'),
		'U_AVATAR'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=profile&amp;mode=avatar'),
		'U_ACCOUNT'      					=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=profile&amp;mode=reg_details'),
		
		'U_GLOBALSETTINGS'      			=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=prefs&amp;mode=personal'),
		'U_POSTINDEFAULT'           		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=prefs&amp;mode=post'),
		'U_DISPLAYOPTIONS'      			=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=prefs&amp;mode=view'),
		
		'U_PRIVATEMSG'   					=> ($config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_'))) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;folder=inbox') : '',
		'U_COMPOSEPMMESSAGESG'      		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;mode=compose'),
		'U_MANAGEPMDRAFTS'          		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;mode=drafts'),
		'U_INBOX'                   		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;folder=inbox'),
		'U_OUTBOX'                  		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;folder=outbox'),
		'U_SENDMESSAGEBOX'          		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;folder=sentbox'),
		'U_UNREADMESSAGES'          		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;folder=unreadbox'),
		'U_RULEFOLDERSETTING'       		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=pm&amp;mode=options'),
		
		'U_EDITMEMBERSHIP'          		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=groups&amp;mode=membership'),
		'U_MANAGEGROUPS'            		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=groups&amp;mode=manage'),
		
		'U_MANAGEFRIENDS'           		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=zebra&amp;mode=friends'),
		'U_MANAGEFOES'              		=> append_sid($phpbb_root_path . 'ucp.' . $phpEx . '?i=zebra&amp;mode=foes'),
	
		'U_DELETE_COOKIES'					=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=delete_cookies'),
		'U_MARK_FORUMS'						=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? append_sid("{$phpbb_root_path}index.$phpEx", 'hash=' . generate_link_hash('global') . '&amp;mark=forums') : '',
	
		'U_RULES'				    		=> append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=rules'),
		
		'S_DISPLAY_WELCOME'					=> ($portal_config['portal_welcome_intro']) ? true : false,
		'S_DISPLAY_WELCOME_BACK'			=> ($portal_config['portal_welcome_back']) ? true : false,
		'S_PORTAL_COPY'						=> $user->lang['PORTAL_COPY'],
		'S_PORTAL_DRAG_DROP'				=> ($portal_config['portal_drag_drop']) ? true : false,
		));
}

/**
* portal_end() : The finishing touch
* We request that you keep this copyright notice as specified in the licence.
* If you do not like (we will not provide any support if our credit link is removed) to put this link, 
* you should at least provide us with one visible (can be small but visible) link on your home page or 
* your Portal Index using this code for example :
* <a href="http://www.portalxl.nl/forum/" title="Portal XL">Portal XL</a>
*/
function portal_end($portal_return = false, $portal_img = true) 
{
	global $user, $portal_config, $phpbb_root_path;
	if ($portal_img) 
	{
		$output = '<br /><span style="padding-top:5px; text-align: middle"><a href="http://www.portalxl.nl/forum/" title="' . $user->lang['PORTAL_VERSION'] . $portal_config['portal_version'] . '"><img src="' . $phpbb_root_path . 'portal/images/phpbb-portal-xl40.png" alt="' . $user->lang['PORTAL_VERSION'] . $portal_config['portal_version'] . '" /></a></span>';
	} else {
		$output = '<span style="padding-top:5px" text-align: middle"><a href="http://www.portalxl.nl/forum/" title="' . $user->lang['PORTAL_VERSION'] . $portal_config['portal_version'] . '">' . $user->lang['PORTAL_VERSION'] . $portal_config['portal_version'] . $user->lang['PORTAL_COPY'] . '</a></span>';
	}
	if ($portal_return) 
	{
		return $output;
	} else {
		$user->lang['TRANSLATION_INFO'] .= $output;
	}
	return;
}

/**
* Generate login box or verify password
*/
function portal_login_box($redirect = '', $l_explain = '', $l_success = '', $admin = false, $s_display = true)
{
	global $db, $user, $template, $auth, $phpEx, $phpbb_root_path, $config, $portal_config;
	
	if (!class_exists('phpbb_captcha_factory'))
	{
		include($phpbb_root_path . 'includes/captcha/captcha_factory.' . $phpEx);
	}

	$err = '';

	// Make sure user->setup() has been called
	if (empty($user->lang))
	{
		$user->setup();
	}

	// Print out error if user tries to authenticate as an administrator without having the privileges...
	if ($admin && !$auth->acl_get('a_'))
	{
		// Not authd
		// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
		if ($user->data['is_registered'])
		{
			add_log('connections', 'LOG_ADMIN_AUTH_FAIL_NO_ADMIN');
		}
		trigger_error('NO_AUTH_ADMIN');
	}

	if (isset($_POST['login']))
	{
		// Get credential
		if ($admin)
		{
			$credential = request_var('credential', '');

			if (strspn($credential, 'abcdef0123456789') !== strlen($credential) || strlen($credential) != 32)
			{
				if ($user->data['is_registered'])
				{
					add_log('connections', 'LOG_ADMIN_AUTH_FAIL_NO_ADMIN');
				}
				trigger_error('NO_AUTH_ADMIN');
			}

			$password	= request_var('password_' . $credential, '', true);
		}
		else
		{
			$password	= request_var('password', '', true);
		}

		$username	= request_var('username', '', true);
		$autologin	= (!empty($_POST['autologin'])) ? true : false;
		$viewonline = (!empty($_POST['viewonline'])) ? 0 : 1;
		$admin 		= ($admin) ? 1 : 0;
		$viewonline = ($admin) ? $user->data['session_viewonline'] : $viewonline;

		// Check if the supplied username is equal to the one stored within the database if re-authenticating
		if ($admin && utf8_clean_string($username) != utf8_clean_string($user->data['username']))
		{
			// We log the attempt to use a different username...
			add_log('connections', 'LOG_ADMIN_AUTH_FAIL_DIFFER', utf8_clean_string($username));
			trigger_error('NO_AUTH_ADMIN_USER_DIFFER');
		}

		// If authentication is successful we redirect user to previous page
		$result = $auth->login($username, $password, $autologin, $viewonline, $admin);

		// If admin authentication and login, we will log if it was a success or not...
		// We also break the operation on the first non-success login - it could be argued that the user already knows
		if ($admin)
		{
			if ($result['status'] == LOGIN_SUCCESS)
			{
				add_log('connections', 'LOG_ADMIN_AUTH_SUCCESS');
			}
			else
			{
				// Only log the failed attempt if a real user tried to.
				// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
				if ($user->data['is_registered'])
				{
					add_log('connections', 'LOG_ADMIN_AUTH_FAIL');
				}
			}
		}

		// The result parameter is always an array, holding the relevant information...
		if ($result['status'] == LOGIN_SUCCESS)
		{
			$redirect = request_var('redirect', "{$phpbb_root_path}portal.$phpEx");
			$message = ($l_success) ? $l_success : $user->lang['LOGIN_REDIRECT'];
			$l_redirect = ($admin) ? $user->lang['PROCEED_TO_ACP'] : (($redirect === "{$phpbb_root_path}portal.$phpEx" || $redirect === "portal.$phpEx") ? $user->lang['RETURN_INDEX'] : $user->lang['RETURN_PAGE']);

			// append/replace SID (may change during the session for AOL users)
			
			// Handle auto grouping
			if (!function_exists('auto_group'))
			{
				include($phpbb_root_path . 'includes/functions_autogroup.'.$phpEx);
			}
			auto_group();
			// Handle auto grouping
						
			$redirect = reapply_sid($redirect);

			// Special case... the user is effectively banned, but we allow founders to login
			if (defined('IN_CHECK_BAN') && $result['user_row']['user_type'] != USER_FOUNDER)
			{
				return;
			}

			/*
			* Welcome PM on First Login (WPM)
			* By DualFusion /adjusted by ..::Frans::.. for phpbb3 GOLD
			*/
			$sql = 'SELECT wpm_enable FROM '.WPM_TABLE.' WHERE wpm_config_id =1';
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if($row['wpm_enable'] && $user->data['user_lastvisit'] == 0)
			{
				include($phpbb_root_path . 'includes/functions_wpm.' . $phpEx);
				$wpm = new welcome_pm();
				$wpm->get_vars();
				$wpm->send_wpm();
			}
			/* End WPM */

			if ($admin)
			{
				redirect($redirect);
			}

			$redirect = meta_refresh(3, $redirect);
			trigger_error($message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a>'));
		}

		// Something failed, determine what...
		if ($result['status'] == LOGIN_BREAK)
		{
			trigger_error($result['error_msg']);
		}

		// Special cases... determine
		switch ($result['status'])
		{
			case LOGIN_ERROR_ATTEMPTS:

				$captcha = phpbb_captcha_factory::get_instance($config['captcha_plugin']);
				$captcha->init(CONFIRM_LOGIN);
				// $captcha->reset();

				$template->assign_vars(array(
					'CAPTCHA_TEMPLATE'			=> $captcha->get_template(),
				));

				$err = $user->lang[$result['error_msg']];
			break;

			case LOGIN_ERROR_PASSWORD_CONVERT:
				$err = sprintf(
					$user->lang[$result['error_msg']],
					($config['email_enable']) ? '<a href="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=sendpassword') . '">' : '',
					($config['email_enable']) ? '</a>' : '',
					($config['board_contact']) ? '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">' : '',
					($config['board_contact']) ? '</a>' : ''
				);
			break;

			// Username, password, etc...
			default:
				$err = $user->lang[$result['error_msg']];

				// Assign admin contact to some error messages
				if ($result['error_msg'] == 'LOGIN_ERROR_USERNAME' || $result['error_msg'] == 'LOGIN_ERROR_PASSWORD')
				{
					$err = (!$config['board_contact']) ? sprintf($user->lang[$result['error_msg']], '', '') : sprintf($user->lang[$result['error_msg']], '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">', '</a>');
				}

			break;
		}
	}

	if (!$redirect)
	{
		// We just use what the session code determined...
		// If we are not within the admin directory we use the page dir...
		$redirect = '';

		if (!$admin)
		{
			$redirect .= ($user->page['page_dir']) ? $user->page['page_dir'] . '/' : '';
		}

		$redirect .= $user->page['page_name'] . (($user->page['query_string']) ? '?' . htmlspecialchars($user->page['query_string']) : '');
	}

	// Assign credential for username/password pair
	$credential = ($admin) ? md5(unique_id()) : false;

	$s_hidden_fields = array(
		'sid'		=> $user->session_id,
	);

	if ($redirect)
	{
		$s_hidden_fields['redirect'] = $redirect;
	}

	if ($admin)
	{
		$s_hidden_fields['credential'] = $credential;
	}

	$s_hidden_fields = build_hidden_fields($s_hidden_fields);

	$template->assign_vars(array(
		'LOGIN_ERROR'		=> $err,
		'LOGIN_EXPLAIN'		=> $l_explain,

		'U_SEND_PASSWORD' 		=> ($config['email_enable']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=sendpassword') : '',
		'U_RESEND_ACTIVATION'	=> ($config['require_activation'] == USER_ACTIVATION_SELF && $config['email_enable']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=resend_act') : '',
		'U_TERMS_USE'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=terms'),
		'U_PRIVACY'				=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=privacy'),

		'S_DISPLAY_FULL_LOGIN'	=> ($s_display) ? true : false,
		'S_LOGIN_ACTION'		=> (!$admin) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login') : append_sid("index.$phpEx", false, true, $user->session_id), // Needs to stay index.$phpEx because we are within the admin directory
		'S_HIDDEN_FIELDS' 		=> $s_hidden_fields,

		'S_ADMIN_AUTH'			=> $admin,
		'USERNAME'				=> ($admin) ? $user->data['username'] : '',

		'USERNAME_CREDENTIAL'	=> 'username',
		'PASSWORD_CREDENTIAL'	=> ($admin) ? 'password_' . $credential : 'password',
	));

	$template->set_filenames(array(
		'body' => 'portal/block/login_box.html')
	);
}

/*
* Generate pagination routine borrowed from phpbb3
*/
function generate_repository($base_url, $num_items, $per_page, $start_item, $type, $add_prevnext_text = true, $tpl_prefix = '')
{
	global $template, $user;

    switch($type)
    {
 	  case "announcements":
		 $pagination_type = 'anc';
		 $anchor = '#announcepage';
	  break;
	  
	  case "news":
	  case "news_all":
		 $pagination_type = 'nws';
		 $anchor = '#newspage';
	  break;
	  
 	  case "attachments":
		 $pagination_type = 'att';
		 $anchor = '#attachpage';
	  break;
	  
 	  case "filebase":
		 $pagination_type = 'fbs';
		 $anchor = '#filebasepage';
	  break;
	  
 	  case "portalpages":
		 $pagination_type = 'pgs';
		 $anchor = '#pagetop';
	  break;
    }

	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;

	$seperator = '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';

	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . $anchor . '">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}" . $pagination_type . '=' . (($i - 1) * $per_page) . $anchor . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}" . $pagination_type . '=' . (($i - 1) * $per_page) . $anchor . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}" . $pagination_type . '=' . (($total_pages - 1) * $per_page) . $anchor . '">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . "{$url_delim}" . $pagination_type . '=' . (($on_page - 2) * $per_page) . $anchor . '">' . $user->lang['PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "{$url_delim}" . $pagination_type . '=' . ($on_page * $per_page) . $anchor . '">' . $user->lang['NEXT'] . '</a>';
		}
	}

	$prev =  ($on_page == 1) ? '' : $base_url . "{$url_delim}" . $pagination_type . '=' . (($on_page - 2) * $per_page) . $anchor;
	$next = ($on_page == $total_pages) ? '' : $base_url . "{$url_delim}" . $pagination_type . '=' . ($on_page * $per_page) . $anchor;

	$template->assign_vars(array(
		$tpl_prefix . 'BASE_URL'				=> $base_url,
		'A_' . $tpl_prefix . 'BASE_URL'			=> addslashes($base_url),
		$tpl_prefix . 'PER_PAGE'				=> $per_page,
		$tpl_prefix . 'PORTAL_PREVIOUS_PAGE'	=> $prev,
		$tpl_prefix . 'PORTAL_NEXT_PAGE'		=> $next,
		$tpl_prefix . 'TOTAL_PAGES'				=> $total_pages)
	);

	return $page_string;
}

function get_portal_genders()
{
	global $db;
	$return_ary = array();
	$sql = 'SELECT user_gender FROM ' . USERS_TABLE . '
				WHERE user_gender != ' . GENDER_X;
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$return_ary[$row['user_gender']]++;
	}
	$db->sql_freeresult($result);
	return $return_ary;
}

function portal_referers()
{
  global $cache, $config, $db, $user;
  
  $http_referers 	= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
  $browser			= (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
  $referer			= (!empty($_SERVER['HTTP_REFERER'])) ? htmlspecialchars((string) $_SERVER['HTTP_REFERER']) : '';
  $forwarded_for	= (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? htmlspecialchars((string) $_SERVER['HTTP_X_FORWARDED_FOR']) : '';

  if ($http_referers)
  {
	  if ($forwarded_for){
		  $http_ip = $forwarded_for;
		  $proxy = $_SERVER['REMOTE_ADDR'];
		  $http_host = @gethostbyaddr($forwarded_for);
	  }else{
		  $http_ip = $_SERVER['REMOTE_ADDR'];
		  $http_host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
	  } 
  
	  $int_check = strpos($http_referers, $http_host);
  
	  // Check if an internal referer?
	  if (!$int_check)	
	  {
		  // Do we have a host in the URL?
		  if ($_SERVER['REMOTE_ADDR'])							
		  {
			  $http_time = time();
			  
			  $sql = 'SELECT * 
					  FROM ' . PORTAL_REFERER_TABLE . " 
					  WHERE referer_host = '" . $http_host . "'";
			  $result = $db->sql_query($sql);
			  $row = $db->sql_fetchrow($result);
  
			  if ($row)
			  {
				  $update = 'UPDATE ' . PORTAL_REFERER_TABLE .
				  ' SET referer_hits = ' . ($row['referer_hits']+1) . ' , '.
					  ' referer_lastvisit = ' . $http_time.
				  " WHERE referer_host = '" . $http_host . "'";
				  $db->sql_query($update);
			  }
			  else
			  {
				  $insert = 'INSERT INTO ' . PORTAL_REFERER_TABLE . ' (referer_ip, referer_proxy, referer_host, referer_hits, referer_firstvisit, referer_lastvisit, referer_enabled)' .
				  " VALUES ('" . $http_ip . "', '" . $http_proxy . "', '" . $http_host . "' , 1 , " . $http_time . ' , ' . $http_time . ' , 1 )';
				  $db->sql_query($insert);
			  }
			  $db->sql_freeresult($result);
		  }
	  }
  }
}

if(!function_exists('bbcode_strip'))
{
	// thx to RMcGirr83
	function bbcode_strip($text)
	{
			static $RegEx = array();
			static $bbcode_strip = 'flash';
			// html is pretty but it may break the layout of the tooltip...let's
			// remove some common ones from the tip
			$text_html = array('&quot;','&amp;','&#039;','&lt;','&gt;');
			$text = str_replace($text_html,'',$text);
			if (empty($RegEx))
			{
				$RegEx = array('`<[^>]*>(.*<[^>]*>)?`Usi', // HTML code
					'`\[(' . $bbcode_strip . ')[^\[\]]+\].*\[/(' . $bbcode_strip . ')[^\[\]]+\]`Usi', // bbcode to strip
					'`\[/?[^\[\]]+\]`mi', // Strip all bbcode tags
					'`[\s]+`' // Multiple spaces
				);
			}
		return preg_replace($RegEx, ' ', $text );
	}
	// thx to RMcGirr83
}

?>