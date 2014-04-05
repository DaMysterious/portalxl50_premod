<?php
/**
*
* @package Topic short description and READ MORE  v.0.0.2
* @version $Id: topic_short_description.php 2356 2010-10-28 08:15:36Z 4seven $
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

// Topic short description and READ MORE / 4seven / 2010	

// CONFIG
// -------------------------------------------
// teaser length in signs 180
$teaser_length = 280;
// -------------------------------------------
// enter here the affected forum id's  (7,8,9) 
$foru_id = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65); 
// -------------------------------------------
// CONFIG

// don't touch this part!
if (in_array($forum_id, $foru_id)){
$for_id = true;}
else{
$for_id = false;}

$sql = 'SELECT post_id, topic_id, post_text 
  FROM '. POSTS_TABLE.'
  WHERE ' . $db->sql_in_set('topic_id', $topic_ids) . '
  GROUP BY topic_id';
$result = $db->sql_query_limit($sql, sizeof($topic_ids));	

$linkformat = append_sid($phpbb_root_path . 'viewtopic.' . $phpEx, array('f' => '%1$s', 't' => '%2$s', 'p' => '%3$s', '#' => 'p' . '%3$s'));

$topic_ids = array_flip($topic_ids);

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

    while ($row = $db->sql_fetchrow($result)){
        $rownr = $topic_ids[$row['topic_id']];
        $url = sprintf($linkformat, $forum_id, $row['topic_id'], $row['post_id']);

        $template->alter_block_array('topicrow', array(
				 'TOPIC_TITLE_SHORT'      => ((utf8_strlen($row['post_text'])) && $for_id) ? utf8_substr(bbcode_strip($row['post_text']), 0, $teaser_length) . ' ... ' . '<a href="' . $url . '" title="">' . $user->lang['READ_MORE'] . '</a>' : ''	 
        ), $rownr, 'change');}

?>