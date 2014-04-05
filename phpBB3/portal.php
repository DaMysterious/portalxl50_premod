<?php
/*
*
* @name portal.php
* @package phpBB3 Portal XL 5.0
* @version $Id: portal.php,v 1.2 2009/05/15 22:32:06 portalxl group Exp $
*
* @copyright (c) 2007, 2013 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
// Users and Bots on Seperate Lines 1.0.0
define('IN_FORUM_INDEX', true);
// Users and Bots on Seperate Lines 1.0.0
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_calendar.' . $phpEx);

// IBPro Game Support
$autocom = request_var('autocom', '');
$act = request_var('act', '');
$do = request_var('do','');

if (strtolower($act) == 'arcade' && strtolower($do) == 'newscore')
{
	require($phpbb_root_path . 'includes/arcade/scoretype/ibpro.'.$phpEx);
}

if (strtolower($autocom) == 'arcade')
{
	require($phpbb_root_path . 'includes/arcade/scoretype/ibprov3.'.$phpEx);
}
//IBPro Game Support

/**
* initalise some global variables
*/
global $db, $auth, $user, $template, $SID, $_SID;
global $phpbb_root_path, $phpEx, $config, $portal_config;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/portal_xl');

// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> Zero dupe
if (!empty($phpbb_seo->seo_opt['url_rewrite'])) {
	$phpbb_seo->seo_path['canonical'] = $phpbb_seo->drop_sid(append_sid("{$phpbb_root_path}portal.$phpEx"));
}
$seo_mark = request_var('mark', '');
$keep_mark = in_array($seo_mark, array('topics', 'topic', 'forums', 'all')) ? (boolean) ($user->data['is_registered'] || $config['load_anon_lastread']) : false;
$phpbb_seo->seo_opt['zero_dupe']['redir_def'] = array(
	'hash' => array('val' => request_var('hash', ''), 'keep' => $keep_mark),
	'mark' => array('val' => $seo_mark, 'keep' => $keep_mark),
);
if ( !$phpbb_seo->seo_opt['zero_dupe']['strict'] ) { // strict mode is here a bit faster
	if ( !empty($phpbb_seo->seo_static['portal']) ) {
		$phpbb_seo->set_cond( (boolean) (utf8_strpos($phpbb_seo->seo_path['uri'], $phpbb_seo->seo_static['portal']) === false), 'do_redir', (empty($_GET) || (!empty($seo_mark) && !$keep_mark)));
	} else {
		$phpbb_seo->set_cond( (boolean) (utf8_strpos($phpbb_seo->seo_path['uri'], "portal.$phpEx") !== false), 'do_redir', (empty($_GET) || (!empty($seo_mark) && !$keep_mark)));
	}
}
$phpbb_seo->seo_chk_dupe();
// www.phpBB-SEO.com SEO TOOLKIT END -> Zero dupe

/**
* Automatic Update portal page views if counter block is disabled
*/
$sql = "SELECT block_name, block_disable
    FROM  " . PORTAL_BLOCK_TABLE ." 
	ORDER by block_id ASC";
    $result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		if (($row['block_name'] == 'digits_counter.html') & ($row['block_disable'] == 1))
		{
			$sql = "UPDATE " . PORTAL_CONFIG_TABLE . "
			SET config_value = config_value + 1
			WHERE config_name = 'portal_visit_counter'";
			$db->sql_query($sql);
		}
	}
	$db->sql_freeresult($result);
	
// Start Breizh Shoutbox
if (isset($config['shout_version']))
{
	if ($config['shout_enable'] && $config['shout_index'])
	{
		shout_display();
	}
}

$bd_shout = array();

$bd_shout[] = array(
	'userid'	=> $row['user_id'],
	'name'		=> $row['username'],
	'colour'	=> $row['user_colour'],
	'age'		=> ($now['year'] - $age),
	'flag'		=> (isset($row['user_country_flag'])) ? $row['user_country_flag'] : false,
);

if (isset($config['shout_version']))
{
	if (sizeof($bd_shout) && date('H') == $config['shout_birthday_hour'] && $config['shout_enable'])
	{
		foreach ($bd_shout as $pos => $shout)
		{
			birthday_robot_shout($shout['userid'], $shout['name'], $shout['colour'], $shout['age'], $shout['flag']);
		}
	}
}
// End Breizh Shoutbox
	
/*
* output the page
*/
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - META
$seo_meta->collect('description', $config['sitename'] . ' : ' .  $config['site_desc']);
$seo_meta->collect('keywords', $config['sitename'] . ' ' . $seo_meta->meta['description']);
// www.phpBB-SEO.com SEO TOOLKIT END - META
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - TITLE
// page_header($user->lang['PORTAL']);
// page_header($config['sitename']  . ' : ' . $user->lang['PORTAL']);
page_header($config['sitename'] . ' ~ ' . $config['site_desc'] . ' : ' . $user->lang['PORTAL']);
// www.phpBB-SEO.com SEO TOOLKIT END - TITLE

/*
* include here to load all availabe block modules
*/
if (defined('PORTAL')) {
	include($phpbb_root_path . 'portal/includes/functions.'.$phpEx);
	include($phpbb_root_path . 'portal/includes/portal_blocks.' . $phpEx);
	include($phpbb_root_path . 'portal/includes/functions_blocks_portal.' . $phpEx);
}

/*
* switch template if dag'n drop is on/off
*/
	if ($portal_config['portal_drag_drop'] == true)
	{
	$template->set_filenames(array(
		'body' => 'portal/portal_main_draggable.html',
		));
	}
	else
	{
	$template->set_filenames(array(
		'body' => 'portal/portal_main.html',
		));
	}

$template->assign_vars(array(
	'PORTAL'					=> defined('PORTAL') ? true : false,		
	'PORTAL_INDEX_PAGE'			=> ($portal_config['portal_index_layout']) ? true : false,
	));

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>