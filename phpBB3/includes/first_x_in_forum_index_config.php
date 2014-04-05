<?php
/**
*
* @package First Topic [pic] on Forum Index  v.0.0.6
* @version $Id: first_x_in_forum_index_config.php 2356 2356 2010-11-04 12:00:17Z 4seven $
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
//----------------SETTINGS - START -------------------
//------------------------------------------------------------
//----------BASIC-SETTINGS----------------

// set function-mode of mod:

$function_mode = 'mix';

// enter as given: img,  attachment or mix (first come - first serve)
// $display_priority = 'img';  // later

//--------------------------------------------------------------

// make your  desired settings here

$width_or_height_img  = 'mix';

// enter as given: width, height or mix 
// explanation of mix: centered cropped  pic with exact squared fit

//--------------------------------------------------------------

// make the setting of max thumb-size in px here 
// counts for width, height or mix

$convert_max_size = 60;

// don't make it to big ;)

//--------------------------------------------------------------

// activate the display of an "no [img] pic" 
// but, before u activate this > make a pic and save it  
// with the size of $convert_max_size and correct fit (given by $width_or_height_img )
// name it no_img.jpg and load it up to images/img_thumbs

$no_pic_img = 'no';

// enter as given: yes or no

//--------------------------------------------------------------

// placeholder with perfect fit, instead of "no [img] pic"
// if $no_pic_img = 'no' and $no_img_placeholder = 'no'  > the topic title fit is shown in standard phpbb3 mode
// hint: only active if $no_pic_img = 'no'

$no_img_placeholder = 'no';

// enter as given: yes or no 

//--------------------------------------------------------------

// colour of border around thumb and "no [img] pic"

$border_color = 'silver';

// enter in HEX eg. '#F6F4D0' (for prosilver) or 'lightgray' or 'transparent'  (no border)

//--------------------------------------------------------------

// activate this mode for showing thumbs in search.php 
// (Search Results,View unanswered posts, View active topics etc.)

$active_4_search = 'yes';

// enter as given: yes or no

//--------------------------------------------------------------

// ----------------[img] SETTINGS--------------------

// ------------[attachment] SETTINGS---------------

// enter here the forum id's that will show thumbing  of [attachment]

$affected_forum_att	= array(1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70); 

// enter as given: (7,8,9) 

//--------------------------------------------------------------

// enter here the forum id's that will show thumbing  of [img]

$affected_forum_img	= array(1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70);

// enter as given: (7,8,9) 

//--------------------------------------------------------------
//----------------SETTINGS - END -------------------
// First Topic [pic] on Forum Index  / 4seven / 2010
?>