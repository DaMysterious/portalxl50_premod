<?php
/**
*
* @package First Topic [pic] on Forum Index  v.0.0.6
* @version $Id: first_x_in_forum_index.php 2356 2010-11-04 08:15:36Z 4seven $
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

if (!isset($topic_att))
{
   $topic_att[] = 0;
}

if (!isset($topic_img))   
{
	$topic_img[] = 0;
}

// First Topic [pic] on Forum Index  / 4seven / 2010	
// Control Section
$forumid = request_var('f', 0);

if (in_array($forumid, $affected_forum_img))
{
$affected_thumb_img = true;
}
else
{
$affected_thumb_img = false;
}

if (in_array($forumid, $affected_forum_att))
{
$affected_thumb_att = true;
}
else
{
$affected_thumb_att = false;
}

if (!isset($topic_att) || empty($topic_att))    
{
$topic_att = array(0);
}

if (!isset($topic_img) || empty($topic_img))    
{
$topic_img = array(0);
}  
		
$template->assign_vars(array(
	'EMPTY_IMG'				    => (($affected_thumb_att || $affected_thumb_img) && ($no_pic_img == 'yes')) ? $phpbb_root_path . 'images/' . 'no_img.jpg' : false,
	'BORDER_COLOR'				=> (isset($border_color)) ? $border_color : '',
	'PLACEHOLDER'				=> (($affected_thumb_att || $affected_thumb_img) && ($no_pic_img == 'no') && ($no_img_placeholder == 'yes')) ? true : false,
	'CONVERT_SIZE'		        => $convert_max_size + 6, 
	'CONVERT_DIV_SIZE'		    => $convert_max_size + 64,	
));

if ($width_or_height_img == 'mix')
{
if (!class_exists('cropimage'))
{
include_once($phpbb_root_path . 'includes/thumb_crop.' . $phpEx);
}
}
if (!function_exists('simpleresize'))
{
include_once($phpbb_root_path . 'includes/thumb_resize.' . $phpEx);
}
// Control Section
// First Topic [pic] on Forum Index  / 4seven / 2010

// First Topic [pic] on Forum Index  / 4seven / 2010	
// attachment or mix mode
if (($function_mode == 'attachment') || ($function_mode == 'mix'))
{
if ($affected_thumb_att)
{
$sqlss = 'SELECT a.post_msg_id, a.topic_id, a.attach_id, a.physical_filename, a.real_filename, p.forum_id 
FROM    ' . ATTACHMENTS_TABLE . ' a, ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
WHERE (mimetype = "image/jpeg" OR mimetype = "image/png" OR mimetype = "image/gif")
AND   in_message = 0
AND   ' . $db->sql_in_set('p.topic_id', $topic_att) . '
AND   ' . $db->sql_in_set('p.forum_id', $affected_forum_att) . '
AND   post_id = topic_first_post_id
AND   a.topic_id = p.topic_id
ORDER BY post_msg_id DESC';

$resultss = $db->sql_query_limit($sqlss, sizeof($topic_att));
$rows_att = $db->sql_fetchrowset($resultss);
$db->sql_freeresult($resultss);

$link_att  = $phpbb_root_path . 'images/att_thumbs/%1s_%2s%3s';
$link_pat = append_sid($phpbb_root_path . 'viewtopic.' . $phpEx, array('f' => '%1$s', 't' => '%2$s', 'p' => '%3$s', '#' => 'p' . '%3$s'));

$topic_att = array_flip($topic_att);

if (isset($resultss))
{
foreach($rows_att as $rowss)
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
if (!file_exists($phpbb_root_path . 'images/att_thumbs/' . $rowss['post_msg_id'] . '_' . $rowss['physical_filename'] . substr(strtolower($rowss_real_filename), -4)))
{
if (@getimagesize($phpbb_root_path . 'files/' . $rowss['physical_filename']))
{
$convert_only_size = @getimagesize($phpbb_root_path . 'files/' . $rowss['physical_filename']);

if (($convert_only_size[0] >= $convert_max_size) && ($convert_only_size[1] >= $convert_max_size))
{
if ($width_or_height_img == 'width')
{
$convert_only_sizing = $convert_only_size[0];
}
else
{
$convert_only_sizing = $convert_only_size[1];
}
if ($width_or_height_img == 'mix')
{
copy($phpbb_root_path . 'files/' . $rowss['physical_filename'], $phpbb_root_path . 'images/att_thumbs/' . $rowss['post_msg_id'] . '_' . $rowss['physical_filename'] . substr(strtolower($rowss_real_filename), -4));
$crop = new cropimage;
$crop->source_image = $phpbb_root_path . 'images/att_thumbs/' . $rowss['post_msg_id'] . '_' . $rowss['physical_filename'] . substr(strtolower($rowss_real_filename), -4);
$crop->new_image_name = $rowss['post_msg_id'] . '_' . $rowss['physical_filename'];
$crop->save_to_folder = $phpbb_root_path . 'images/att_thumbs/';
$process = $crop->crop('center');
$image = new simpleresize();
$image->load($phpbb_root_path . 'images/att_thumbs/' . $rowss['post_msg_id'] . '_' . $rowss['physical_filename'] . substr(strtolower($rowss_real_filename), -4));
$image->resizeToWidth($convert_max_size);
$image->save($phpbb_root_path . 'images/att_thumbs/' . $rowss['post_msg_id'] . '_' . $rowss['physical_filename'] . substr(strtolower($rowss_real_filename), -4));
}
else
{
$image = new simpleresize();
$image->load($phpbb_root_path . 'files/' . $rowss['physical_filename']);
if ($width_or_height_img  == 'width')
{
$image->resizeToWidth($convert_max_size);
}
else
{
$image->resizeToHeight($convert_max_size);
}
$image->save($phpbb_root_path . 'images/att_thumbs/' . $rowss['post_msg_id'] . '_' . $rowss['physical_filename'] . substr(strtolower($rowss_real_filename), -4));
}
}
}
}
$row_att = sprintf($link_att, $rowss['post_msg_id'], $rowss['physical_filename'], substr(strtolower($rowss_real_filename), -4));
$row_lin = sprintf($link_pat, $rowss['forum_id'], $rowss['topic_id'], $rowss['post_msg_id']);

$template->alter_block_array('topicrow', array(

			'PRE_ATTACH'				=> (file_exists($row_att)) ? $row_att : '',	
			'ORIG_ATTACH_LINK'			=> $row_lin,	
			'CONVERT_SIZE'		        => $convert_max_size + 6, 
			'CONVERT_DIV_SIZE'		    => $convert_max_size + 64,			
			 
), $topic_att[$rowss['topic_id']], 'change');

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
if (($function_mode == 'img') || ($function_mode == 'mix'))
{
if ($affected_thumb_img)
{
$sqlz = 'SELECT min(p.post_id) as post_id, p.topic_id, p.forum_id, p.post_text 
FROM    ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
WHERE   ' . $db->sql_in_set('p.topic_id', $topic_img) . '
AND   ' . $db->sql_in_set('p.forum_id', $affected_forum_img) . '
AND     post_id = topic_first_post_id
AND    (post_text LIKE "%[img:%]%JPG[/img:%]%"
OR      post_text LIKE "%[img:%]%jpg[/img:%]%"
OR      post_text LIKE "%[img:%]%PNG[/img:%]%"
OR      post_text LIKE "%[img:%]%png[/img:%]%"
OR      post_text LIKE "%[img:%]%GIF[/img:%]%"
OR      post_text LIKE "%[img:%]%gif[/img:%]%")
GROUP   BY topic_id';

$resultz  = $db->sql_query_limit($sqlz, sizeof($topic_img));
$rows_img = $db->sql_fetchrowset($resultz);
$db->sql_freeresult($resultz);

$link_img  = $phpbb_root_path . 'images/img_thumbs/%1s_%2s';
$link_path = append_sid($phpbb_root_path . 'viewtopic.' . $phpEx, array('t' => '%1$s', 'p' => '%2$s', '#' => 'p' . '%2$s'));

$topic_img = array_flip($topic_img);

if (isset($rows_img))
{
foreach($rows_img as $rowz)
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

$remote_clr_pic_w_e = substr($remote_clr_pic, 0, -4);

if (!file_exists($phpbb_root_path . 'images/img_thumbs/' . $rowz['topic_id'] . '_' . $remote_clr_pic))
{

if (!empty($remote_plr_pic) && @getimagesize($remote_raw_pic))
{

$convert_only_size = getimagesize($remote_raw_pic);

if (($convert_only_size[0] >= $convert_max_size) && ($convert_only_size[1] >= $convert_max_size))
{
if ($width_or_height_img == 'width')
{
$convert_only_sizing = $convert_only_size[0];
}
else 
{
$convert_only_sizing = $convert_only_size[1];
}
if ($width_or_height_img == 'mix')
{
copy($remote_raw_pic, $phpbb_root_path . 'images/img_thumbs/' . $rowz['topic_id'] . '_' . $remote_clr_pic);
$crop = new cropimage;
$crop->source_image = $phpbb_root_path . 'images/img_thumbs/' . $rowz['topic_id'] . '_' . $remote_clr_pic;
$crop->new_image_name = $rowz['topic_id'] . '_' . $remote_clr_pic_w_e;
$crop->save_to_folder = $phpbb_root_path . 'images/img_thumbs/';
$process = $crop->crop('center');
$image = new simpleresize();
$image->load($phpbb_root_path . 'images/img_thumbs/' . $rowz['topic_id'] . '_' . $remote_clr_pic);
$image->resizeToWidth($convert_max_size);
$image->save($phpbb_root_path . 'images/img_thumbs/' . $rowz['topic_id'] . '_' . $remote_clr_pic);

}
else
{
$image = new simpleresize();
$image->load($remote_raw_pic);
if ($width_or_height_img  == 'width')
{
$image->resizeToWidth($convert_max_size);
}
else
{
$image->resizeToHeight($convert_max_size);
}
$image->save($phpbb_root_path . 'images/img_thumbs/' . $rowz['topic_id'] . '_' . $remote_clr_pic);
}
}
}
}

$row_img  = sprintf($link_img, $rowz['topic_id'], $remote_clr_pic);
$row_link = sprintf($link_path, $rowz['topic_id'], $rowz['post_id']);

$template->alter_block_array('topicrow', array(
		
			'PRE_IMG'				    => (file_exists($row_img)) ? $row_img : '',
			'ORIG_LINK'				    => $row_link,
			'CONVERT_SIZE'		        => $convert_max_size + 6, 
			'CONVERT_DIV_SIZE'		    => $convert_max_size + 64,			
			 
), $topic_img[$rowz['topic_id']], 'change');

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