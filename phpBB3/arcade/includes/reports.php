<?php
/**
*
* @package arcade
* @version $Id: reports.php 1663 2011-09-22 12:09:30Z killbill $
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

$row = $arcade->get_game_data($game_id);

$result	   = $db->sql_query('SELECT game_id FROM ' . ARCADE_REPORTS_TABLE . ' WHERE game_id = ' . (int) $game_id);
$d_game_id = $db->sql_fetchfield('game_id');
$db->sql_freeresult($result);

if ($d_game_id)
{
	trigger_error($user->lang['ARCADE_GAME_ALREADY_REPORTED'] . $arcade->return_links($row));
}

if (!$auth_arcade->acl_get('c_report', $row['cat_id']))
{
	trigger_error($user->lang['NO_PERMISSION_ARCADE_REPORT'] . $arcade->return_links($row));
}

add_form_key('arcade_report');
$submit = (isset($_POST['submit'])) ? true : false;
generate_arcade_nav($row, true);

if ($submit)
{
	if (!check_form_key('arcade_report'))
	{
		trigger_error($user->lang['FORM_INVALID'] . $arcade->return_links($row));
	}

	$report_type = request_var('report_type', 0);
	$report_desc = utf8_normalize_nfc(request_var('report_desc', '', true));

	$report_data = array(
		'report_type'				=> request_var('report_type', 0),
		'report_desc' 				=> utf8_normalize_nfc(request_var('report_desc', '', true)),
		'report_desc_bitfield'		=> '',
		'report_desc_options'		=> '',
		'report_desc_uid'			=> '',
		'report_time'				=> time(),
		'game_id'					=> $game_id,
		'user_id'					=> $user->data['user_id'],
		'report_ip'					=> $user->ip,
	);

	generate_text_for_storage($report_data['report_desc'], $report_data['report_desc_uid'], $report_data['report_desc_bitfield'], $report_data['report_desc_options'], true, true, true);

	$sql = 'INSERT INTO ' . ARCADE_REPORTS_TABLE . ' ' . $db->sql_build_array('INSERT', $report_data);
	$db->sql_query($sql);

	$arcade->set_config('reports_open', ($arcade_config['reports_open'] + 1));

	$template->assign_vars(array(
		'S_ARCADE_REPORT_ADD'	=> true,
		'MESSAGE_TITLE'			=> $user->lang['ARCADE_REPORT_SUCCESS'],
		'MESSAGE_TEXT'			=> sprintf($user->lang['ARCADE_REPORT_ADDED'], $row['game_name']),
		'ARCADE_RETURN_LINKS'	=> $arcade->return_links($row, false)));
}
else
{
	$s_hidden_fields = build_hidden_fields(array(
		'mode'		=> 'report',
		'g'			=> $game_id,
	));

	$arcade_report_type_select  = '<select id="report_type" name="report_type">
								<option value="' . ARCADE_REPORT_SCORING . '" selected="selected">' . $user->lang['ARCADE_REPORT_SCORING'] . '</option>
								<option value="' . ARCADE_REPORT_PLAYING . '">' . $user->lang['ARCADE_REPORT_PLAYING'] . '</option>
								<option value="' . ARCADE_REPORT_DOWNLOADING . '">' . $user->lang['ARCADE_REPORT_DOWNLOADING'] . '</option>
								<option value="' . ARCADE_REPORT_OTHER . '">' . $user->lang['ARCADE_REPORT_OTHER'] . '</option>
							</select>';

	$template->assign_vars(array(
		'S_ACTION'						=> $arcade->url(),
		'S_HIDDEN_FIELDS'				=> $s_hidden_fields,

		'ARCADE_REPORT_TYPE_SELECT'		=> $arcade_report_type_select,
		'GAME_DESC'						=> censor_text(nl2br($row['game_desc'])),
		'GAME_NAME'						=> $row['game_name'],
		'GAME_IMAGE'					=> ($row['game_image']) ? $arcade->url("img=" . $row['game_image']) : '',
		'GAME_FILESIZE'					=> ($row['game_filesize'] > 0 ) ? sprintf($user->lang['ARCADE_GAMES_FILESIZE'], get_formatted_filesize($row['game_filesize'])) : sprintf($user->lang['ARCADE_GAMES_FILESIZE'], get_formatted_filesize($arcade->set_filesize($row['game_id']))),
	));
}

page_header($user->lang['ARCADE_REPORT'], false);

$template->set_filenames(array(
	'body' => 'arcade/reports_body.html')
);

page_footer();

?>