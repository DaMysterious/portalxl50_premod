<?php
/**
/*
*
* @name top_referers.php
* @package phpBB3 Portal XL 5.0
* @version $Id: top_referers,v 1.0 2010/10/18 portalxl group Exp $
*
* @copyright (c) 2007, 2010 Portal XL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*

DROP TABLE IF EXISTS phpbb_portal_referer;
CREATE TABLE phpbb_portal_referer (
  referer_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  referer_ip varchar(40) binary NOT NULL DEFAULT '',
  referer_proxy varchar(40) binary NOT NULL DEFAULT '',
  referer_host varchar(255) binary NOT NULL DEFAULT '',
  referer_hits int(10) NOT NULL DEFAULT '1',
  referer_firstvisit int(11) NOT NULL DEFAULT '0',
  referer_lastvisit int(11) NOT NULL DEFAULT '0',
  referer_enabled tinyint(1) DEFAULT NULL,
  PRIMARY KEY (referer_id)
) TYPE=MyISAM ;

*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

/*
* Start session management
*/

/*
* Begin block script here
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
// [+] Portal XL Referer
portal_referers();
// [-] Portal XL Referer

*/

/*
* Get referes for display
*/
$num_refererviews = 10;
$num_characters = 45;

$sql = 'SELECT * 
		FROM '. PORTAL_REFERER_TABLE . ' 
		WHERE referer_ip = referer_ip 
		AND referer_enabled = 1
		ORDER BY referer_hits DESC, referer_lastvisit DESC';
$result = $db->sql_query($sql);
$num_ref = sizeof($db->sql_fetchrowset($result));
$result = $db->sql_query_limit($sql, $num_refererviews);
while($row = $db->sql_fetchrow($result))
{
	$replace = str_replace(array('_','-','.','www.'), ' ', $row['referer_host']);
	$ShortHostName = character_limit($replace, $num_characters);

	$template->assign_block_vars('refererrow', array(
		'HTTP_IP'			=> $row['referer_ip'],
		'HTTP_PROXY'		=> $row['referer_proxy'],
		'HTTP_HOST'			=> $ShortHostName,
		'HHTP_LASTVISIT'	=> $user->format_date($row['referer_lastvisit']),
		'HITS'				=> $row['referer_hits'],
		
		'U_HTTP_HOST'		=> 'http://'.$row['referer_host'],
	));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
		'TOTAL_REF'	=> $num_ref,
));

// Set the filename of the template you want to use for this file.
$template->set_filenames(array(
    'body' => 'portal/block/top_referers.html',
	));

?>