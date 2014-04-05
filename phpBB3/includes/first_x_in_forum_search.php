<?php
/**
*
* @package First Topic [pic] on Forum Index  v.0.0.6
* @version $Id: first_x_in_forum_index_search.php 2356 2356 2010-11-04 03:25:05Z 4seven $
* @copyright (c) 2010 / 4seven
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

// First Topic [pic] on Forum Index  / 4seven / 2010
// basics
$template->assign_vars(array(
	'ACTIVE_SEARCH'				=> ($active_4_search == 'yes') ? true : false,
	'EMPTY_IMG'				    => ($no_pic_img == 'yes') ? $phpbb_root_path . 'images/' . 'no_img.jpg' : false,
	'PLACEHOLDER'				=> (($no_pic_img == 'no') && ($no_img_placeholder == 'yes')) ? true : false,
	'BORDER_COLOR'				=> (isset($border_color)) ? $border_color : '',
	'CONVERT_SIZE'		        => $convert_max_size + 6, 
	'CONVERT_DIV_SIZE'		    => $convert_max_size + 64,	
));
// basics
// First Topic [pic] on Forum Index  / 4seven / 2010	

// First Topic [pic] on Forum Index  / 4seven / 2010	
// attachment or mix mode
if (($function_mode == 'attachment') or ($function_mode == 'mix'))
{

if (isset($res_top_id_1))
{

$sqlss = 'SELECT a.post_msg_id, a.topic_id, a.attach_id, a.physical_filename, a.real_filename, p.forum_id 
FROM    ' . ATTACHMENTS_TABLE . ' a, ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
WHERE (mimetype = "image/jpeg" OR mimetype = "image/png" OR mimetype = "image/gif")
AND   in_message = 0
AND   ' . $db->sql_in_set('p.topic_id', $res_top_id_1) . '
AND   ' . $db->sql_in_set('p.forum_id', $affected_forum_att) . '
AND   post_id = topic_first_post_id
AND   a.topic_id = p.topic_id
ORDER BY post_msg_id DESC';

$resultss = $db->sql_query_limit($sqlss, sizeof($res_top_id_1));
$rows_att = $db->sql_fetchrowset($resultss);
$db->sql_freeresult($resultss);

$link_att  = $phpbb_root_path . 'images/att_thumbs/%1s_%2s%3s';
$link_pat = append_sid($phpbb_root_path . 'viewtopic.' . $phpEx, array('f' => '%1$s', 't' => '%2$s', 'p' => '%3$s', '#' => 'p' . '%3$s'));

$res_top_id_1 = array_flip($res_top_id_1);

if (isset($resultss))
{
foreach($rows_att as $rowss)
{
if (in_array($rowss['forum_id'], $affected_forum_att))
{

$str_finds = array(
'.jpeg', '.png.jpeg', '.png.jpg', '.gif.jpeg', '.gif.jpg',
'.jpeg.png', '.jpg.png', '.gif.png', 
'.jpeg.gif', '.jpg.gif', '.png.gif'
);
$str_repla = array(
'.jpg', '.jpg', '.jpg', '.jpg', '.jpg',
'.png', '.png', '.png', 
'.gif', '.gif', '.gif'
);

$rowss_real_filename = str_replace($str_finds, $str_repla, strtolower($rowss['real_filename']));

$row_att = sprintf($link_att, $rowss['post_msg_id'], $rowss['physical_filename'], substr(strtolower($rowss_real_filename), -4));
$row_lin = sprintf($link_pat, $rowss['forum_id'], $rowss['topic_id'], $rowss['post_msg_id']);

$template->alter_block_array('searchresults', array(
	
			'PRE_ATTACH'				=> (file_exists($row_att)) ? $row_att : '',	
			'ORIG_ATTACH_LINK'			=> $row_lin,	
			'CONVERT_SIZE'		        => $convert_max_size + 6, 
			'CONVERT_DIV_SIZE'		    => $convert_max_size + 64,			
			 
), $res_top_id_1[$rowss['topic_id']], 'change');

}
}
}
// else
// {
// echo '';
// }
}
}
// attachment or mix mode

// ------------------------

// img or mix mode
if (($function_mode == 'img') or ($function_mode == 'mix'))
{

if (isset($res_top_id_2))
{

$sqlz = 'SELECT min(p.post_id) as post_id, p.topic_id, p.forum_id, p.post_text 
FROM    ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
WHERE   ' . $db->sql_in_set('p.topic_id', $res_top_id_2) . '
AND     ' . $db->sql_in_set('p.forum_id', $affected_forum_img) . '
AND     post_id = topic_first_post_id
AND    (post_text LIKE "%[img:%]%JPG[/img:%]%"
OR      post_text LIKE "%[img:%]%jpg[/img:%]%"
OR      post_text LIKE "%[img:%]%PNG[/img:%]%"
OR      post_text LIKE "%[img:%]%png[/img:%]%"
OR      post_text LIKE "%[img:%]%GIF[/img:%]%"
OR      post_text LIKE "%[img:%]%gif[/img:%]%")
GROUP   BY topic_id';

$resultz  = $db->sql_query_limit($sqlz, sizeof($res_top_id_2));
$rows_img = $db->sql_fetchrowset($resultz);
$db->sql_freeresult($resultz);

$link_img  = $phpbb_root_path . 'images/img_thumbs/%1s_%2s';
$link_path = append_sid($phpbb_root_path . 'viewtopic.' . $phpEx, array('f' => '%1$s', 't' => '%2$s', 'p' => '%3$s', '#' => 'p' . '%3$s'));

$res_top_id_2 = array_flip($res_top_id_2);

if (isset($rows_img))
{
foreach($rows_img as $rowz)
{
if (in_array($rowz['forum_id'], $affected_forum_img))
{
preg_match('/\[img:(.*?)\](.*?)\[\/img:(.*?)\]/', $rowz['post_text'], $rowz_text);
$remote_raw_pic = preg_replace(array('/&\#46;/','/&\#58;/'), array('.',':'), @$rowz_text[2]);
$remote_mid_pic = strrchr($remote_raw_pic,"/");
$remote_plr_pic = substr($remote_mid_pic, 1);
$remote_clr_pic = strtolower(preg_replace('/[^a-zA-Z0-9_+.-]/','',$remote_plr_pic));

$str_finds = array(
'.jpeg', '.png.jpeg', '.png.jpg', '.gif.jpeg', '.gif.jpg',
'.jpeg.png', '.jpg.png', '.gif.png', 
'.jpeg.gif', '.jpg.gif', '.png.gif'
);
$str_repla = array(
'.jpg', '.jpg', '.jpg', '.jpg', '.jpg',
'.png', '.png', '.png', 
'.gif', '.gif', '.gif'
);

$remote_clr_pic = str_replace($str_finds, $str_repla, strtolower($remote_clr_pic));

$row_img  = sprintf($link_img, $rowz['topic_id'], $remote_clr_pic);
$row_link = sprintf($link_path, $rowz['forum_id'], $rowz['topic_id'], $rowz['post_id']);

$template->alter_block_array('searchresults', array(
	
			'PRE_IMG'				    => (file_exists($row_img)) ? $row_img : '',
			'ORIG_LINK'				    => $row_link,
			'CONVERT_SIZE'		        => $convert_max_size + 6, 
			'CONVERT_DIV_SIZE'		    => $convert_max_size + 64,			
		 
), $res_top_id_2[$rowz['topic_id']], 'change');

}
}
}
// else
// {
// echo '';
// }
}
}
// img or mix mode
// First Topic [pic] on Forum Index  / 4seven / 2010

?>