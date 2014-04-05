<?php
/**
*
* @name most_donors.php
* @package phpBB3 Portal XL Premod
* @version $Id: most_donors.php,v 1.0 2010/01/10 portalxl group Exp $
*
* @copyright (c) 2007, 2011 Portal XL Group
* @license http://opensource.org/licenses/gpl-3.0 GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/
$number_of_top_posters = $portal_config['portal_max_most_poster'];

$sql = "SELECT a.*, u.* 
		FROM " . ACCT_HIST_TABLE . " a, " . USERS_TABLE . " u" . " 
		WHERE a.comment LIKE 'Donation from%' 
		AND status = 'Completed' 
		AND u.user_id = a.user_id
		ORDER BY date DESC";
$result = $db->sql_query_limit($sql, $number_of_top_posters);

while( ($row = $db->sql_fetchrow($result)) && ($row['username'] != '') )
{         
  $last_donors = '';
  $style_color = '';
  $row['username'] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
  if($row['user_id'] == ANONYMOUS)
  {
	  $last_donors .= $user->lang['ANONYMOUS_DONOR'];
  }
  else
  {
	  $rank_title = $rank_img = '';
	  get_user_rank($row['user_rank'], (($row['user_id'] == ANONYMOUS) ? false : $row['user_posts']), $rank_title, $rank_img, $rank_img_src);
	  $last_donors .= '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;" . "u" . "=" . $row['user_id']) . '">' . $row['username'] . '</a>&nbsp;';
  }

  // Get the Avatar for display
  $avatar = '';
  $avatar_img = '';
  switch ($row['user_avatar_type'])
  {
	  case AVATAR_UPLOAD:
		  $avatar = "download/file.$phpEx?avatar=";
		  $avatar .= $row['user_avatar'];
		  $avatar_img = $avatar;
	  break;
  
	  case AVATAR_GALLERY:
		  $avatar = $config['avatar_gallery_path'] . '/';
		  $avatar .= $row['user_avatar'];
		  $avatar_img = $avatar;
	  break;
  }
  
  $avatar = '<img src="' . $avatar_img . '" width="60" alt="" />';
  
  if (!$row['user_avatar'])
  {
	 $avatar = '<img src="./images/avatars/no_avatar.png" width="60" alt="" />';
  }
  // Get the Avatar for display

  // Country Flags Version 3.0.6
  if ($row['user_id'] != ANONYMOUS)
  {
	  $flag_title = $flag_img = $flag_img_src = '';
	  get_user_flag($row['user_country_flag'], $row['user_country'], $flag_title, $flag_img, $flag_img_src);
  }
  // Country Flags Version 3.0.6

  $template->assign_block_vars('most_donors', array(
	'USERNAME'      	=> $row['username'],
	'U_USERNAME'   	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
													
	'DONORS_NAME' 	=> $last_donors,
	'MONEY' 			=> $row['money'] . ' ' . $config['paypal_currency_code'],
	'DATE' 			=> $user->format_date($row['date']),
	
	'DONORS_AV' 		=> $avatar,
	'RANK_TITLE'		=> $rank_title,
	'FLAG_TITLE'		=> $flag_title,
	'FLAG_IMG'		=> $flag_img,
	'FLAG_IMG_SRC'	=> $flag_img_src,
	'U_WWW'   		=> $row['user_website'],
	'WWW_IMG' 		=> $user->img('icon_contact_www', 'VISIT_WEBSITE'),
	));
}
$db->sql_freeresult($result);

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/most_donors.html',
	));

?>