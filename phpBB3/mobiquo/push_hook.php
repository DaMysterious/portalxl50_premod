<?php
/**
 * 
 * push reply
 * @param int $post_id  the current post_id
 * @param array $current_topic_info
 */
if(!defined('IN_PHPBB') && !defined("IN_MOBIQUO")) exit;
function tapatalk_push_reply($data)
{
	global $db, $user, $config,$table_prefix,$phpbb_root_path,$phpEx;
	$is_only_alert = false;
	if(!function_exists('push_table_exists'))
	{
		define('IN_MOBIQUO', 1);
		require_once $phpbb_root_path . $config['tapatalkdir'] . '/xmlrpcresp.' . $phpEx;
	}
	
	if(!push_table_exists())
		return false;
	if(!(function_exists('curl_init') || ini_get('allow_url_fopen')))
	{
		$is_only_alert = true;
	}
	$return_status = false;
    if (!empty($data))// mobi_table_exists('tapatalk_users')
    {
    	$sql = "SELECT t.userid FROM " . $table_prefix . "tapatalk_users AS t  LEFT JOIN " .TOPICS_WATCH_TABLE . " AS w 
    	ON t.userid = w.user_id
    	WHERE w.topic_id = '".$data['topic_id']."' AND t.subscribe=1";
    	$result = $db->sql_query($sql);
    	while($row = $db->sql_fetchrow($result))
    	{
    		if ($row['userid'] == $user->data['user_id']) continue;
    		define("TAPATALK_PUSH".$row['userid'], 1);
    		$ttp_data = array(
                'userid'    => $row['userid'],
                'type'      => 'sub',
                'id'        => $data['topic_id'],
                'subid'     => $data['post_id'],
                'title'     => tt_push_clean($data['topic_title']),
                'author'    => tt_push_clean($user->data['username']),
    			'fid'       => $data['forum_id'],
    			'authorid'  => $user->data['user_id'],
                'dateline'  => time(),
    		);
    		if(!empty($config['tapatalk_push_type']))
    		{
    			$ttp_data['content'] = @tt_push_covert_content($data);
    		}
            $return_status = tt_send_push_data($ttp_data,$is_only_alert);
    	}
    }
    return $return_status;
}

/**
 * 
 * push watch forum
 * @param array $current_topic_info
 */
function tapatalk_push_newtopic($data)
{
	global $db, $user, $config,$table_prefix,$phpbb_root_path,$phpEx;
	$return_status = false;
	$is_only_alert = false;
	if(!function_exists('push_table_exists'))
	{
		define('IN_MOBIQUO', 1);
		require_once $phpbb_root_path . $config['tapatalkdir'] . '/xmlrpcresp.' . $phpEx;
	}
	if(!push_table_exists())
		return false;
	if(!(function_exists('curl_init') || ini_get('allow_url_fopen')))
	{
		$is_only_alert = true;
	}	
    if (!empty($data))// mobi_table_exists('tapatalk_users')
    {
    	$sql = "SELECT t.userid FROM " . $table_prefix . "tapatalk_users AS t  LEFT JOIN " .FORUMS_WATCH_TABLE . " AS w 
    	ON t.userid = w.user_id
    	WHERE w.forum_id = '".$data['forum_id']."' AND t.newtopic = 1";
    	$result = $db->sql_query($sql);
    	while($row = $db->sql_fetchrow($result))
    	{
    		if ($row['userid'] == $user->data['user_id']) continue;
    		define("TAPATALK_PUSH".$row['userid'], 1);
    		$ttp_data = array(
                'userid'    => $row['userid'],
                'type'      => 'newtopic',
                'id'        => $data['topic_id'],
                'subid'     => $data['post_id'],
                'title'     => tt_push_clean($data['topic_title']),
                'author'    => tt_push_clean($user->data['username']),
    			'fid'       => $data['forum_id'],
    			'authorid'  => $user->data['user_id'],
                'dateline'  => time(),
    		);
    		if(!empty($config['tapatalk_push_type']))
    		{
    			$ttp_data['content'] = @tt_push_covert_content($data);
    		}
            $return_status = tt_send_push_data($ttp_data,$is_only_alert);
    	}
    }
    return $return_status;
}
/**
 * 
 * push the private message
 * @param int $userid
 * @param int $pm_id
 * @param string $subject
 */
function tapatalk_push_pm($userid,$data,$subject)
{
    global $db, $user, $config,$table_prefix,$boardurl,$phpbb_root_path,$phpEx;
    $is_only_alert = false;
	if(!function_exists('push_table_exists'))
	{
		define('IN_MOBIQUO', 1);
		require_once $phpbb_root_path . $config['tapatalkdir'] . '/xmlrpcresp.' . $phpEx;
	}
    if(!push_table_exists())
		return false;
	if(!(function_exists('curl_init') || ini_get('allow_url_fopen')))
	{
		$is_only_alert = true;
	}
	$return_status = false;
    if ($userid)
    {         
         $sql = "SELECT userid FROM " . $table_prefix . "tapatalk_users WHERE userid = '".$userid."' and pm =1";
         $result = $db->sql_query($sql);
         $row = $db->sql_fetchrow($result);
         if ($row['userid'] == $user->data['user_id']) return false;
         $db->sql_freeresult($result);
         if(!empty($row))
         {
         	 $ttp_data = array(
                'userid'    => $row['userid'],
                'type'      => 'pm',
                'id'        => $data['msg_id'],
                'subid'     => '',
                'title'     => tt_push_clean($subject),
                'author'    => tt_push_clean($user->data['username']),
    			'authorid'  => $user->data['user_id'],
                'dateline'  => time(),
    		);   
         	if(!empty($config['tapatalk_push_type']))
    		{
    			$ttp_data['content'] = @tt_push_covert_content($data);
    		} 
            $return_status = tt_send_push_data($ttp_data,$is_only_alert);
         }
    }
    return $return_status;     
}
function tapatalk_push_quote($data,$user_name_arr,$type="quote")
{
	global $db, $user, $config,$table_prefix,$phpbb_root_path,$phpEx;
	$return_status = false;
	$is_only_alert = false;
	if(!function_exists('push_table_exists'))
	{
		define('IN_MOBIQUO', 1);
		require_once $phpbb_root_path . $config['tapatalkdir'] . '/xmlrpcresp.' . $phpEx;
	}
	if(!push_table_exists())
		return false;
	if(!(function_exists('curl_init') || ini_get('allow_url_fopen')))
	{
		$is_only_alert = true;
	}
	if(!empty($user_name_arr) && !empty($data))
	{
		foreach ($user_name_arr as $username)
		{			
			$user_id = tt_get_user_id($username);
			if ($user_id == $user->data['user_id']) continue;
			if (empty($user_id)) continue;
			$sql = "SELECT userid FROM " . $table_prefix . "tapatalk_users WHERE userid = '".$user_id."' AND " . $type . " = 1" ;
	        $result = $db->sql_query($sql);
	        $row = $db->sql_fetchrow($result);
	        $db->sql_freeresult($result);
	        if(!empty($row))
	        {
	            $id = empty($data['topic_id']) ? $data['forum_id'] : $data['topic_id'];
	            if(defined("TAPATALK_PUSH".$row['userid']))
	            {
	            	continue;
	            }
	            $ttp_data = array(
	                'userid'    => $row['userid'],
	                'type'      => $type,
	                'id'        => $data['topic_id'],
	                'subid'     => $data['post_id'],
	                'title'     => tt_push_clean($data['topic_title']),
	                'author'    => tt_push_clean($user->data['username']),
	    			'fid'       => $data['forum_id'],
	    			'authorid'  => $user->data['user_id'],
	                'dateline'  => time(),
	    		);
	    		if(!empty($config['tapatalk_push_type']))
	    		{
	    			$ttp_data['content'] = @tt_push_covert_content($data);
	    		}
	            $return_status = tt_send_push_data($ttp_data,$is_only_alert);          
	            define("TAPATALK_PUSH".$row['userid'], 1);
	        }
			
		}
	}
	return $return_status;
}

function tt_do_post_request($data,$is_test = false)
{
	global $config , $phpbb_root_path ,$cache;
	
	if(!isset($config['tapatalk_push_slug']))
	{
		set_config('tapatalk_push_slug', 0);
	}
	
	//Get push_slug from db
    $push_slug = !empty($config['tapatalk_push_slug'])? $config['tapatalk_push_slug'] : 0;
    if(!defined("TT_ROOT"))
	{
		if(!defined('IN_MOBIQUO')) define('IN_MOBIQUO', true);
		if(empty($config['tapatalkdir'])) $config['tapatalkdir'] = 'mobiquo';
		define('TT_ROOT',$phpbb_root_path . $config['tapatalkdir'] . '/');
	}		
	require_once TT_ROOT."include/classTTConnection.php";
	$connection = new classTTConnection();
	$push_resp = $connection->push($data,$push_slug,generate_board_url(),$config['tapatalk_push_key'],$is_test);
    if(is_array($push_resp))
    {        
        if(isset($push_resp['slug'])) set_config('tapatalk_push_slug', $push_resp['slug']);
        return $push_resp['result'];
    }
    return false;
}


function tt_push_clean($str)
{
    $str = strip_tags($str);
    $str = trim($str);
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return $str;
}

function tt_get_user_id($username)
{
    global $db;
    
    if (!$username)
    {
        return false;
    }
    
    $username_clean = $db->sql_escape(utf8_clean_string($username));
    
    $sql = 'SELECT user_id
            FROM ' . USERS_TABLE . "
            WHERE username_clean = '$username_clean'";
    $result = $db->sql_query($sql);
    $user_id = $db->sql_fetchfield('user_id');
    $db->sql_freeresult($result);
    
    return $user_id;
}

function tt_get_tag_list($str)
{
    if ( preg_match_all( '/(?<=^@|\s@)(#(.{1,50})#|\S{1,50}(?=[,\.;!\?]|\s|$))/U', $str, $tags ) )
    {
        foreach ($tags[2] as $index => $tag)
        {
            if ($tag) $tags[1][$index] = $tag;
        }
        
        return array_unique($tags[1]);
    }
    
    return array();
}

function tt_insert_push_data($data)
{
	global $table_prefix,$db;	
	if($data['type'] == 'pm')
	{
		$data['subid'] = $data['id'];
	}
	$data['title'] = $db->sql_escape($data['title']);    	
	$data['author'] = $db->sql_escape($data['author']);
	$sql_data[$table_prefix . "tapatalk_push_data"]['sql'] = array(
        'author' => $data['author'],
		'user_id' => (int) $data['userid'],
		'data_type' => $data['type'],
		'title' => $data['title'],
		'data_id' => (int) $data['subid'],
		'create_time' => $data['dateline']		
    );
	if($data['type'] != 'pm')
    {
    	$sql_data[$table_prefix . "tapatalk_push_data"]['sql']['topic_id'] = $data['id'];
    }
    $sql = 'INSERT INTO ' . $table_prefix . "tapatalk_push_data" . ' ' .
    $db->sql_build_array('INSERT', $sql_data[$table_prefix . "tapatalk_push_data"]['sql']);
	$db->sql_query($sql);	
}

function tt_send_push_data($ttp_data,$is_only_alert=false)
{
	global $config,$db,$user,$phpbb_root_path;
	
	if(!function_exists("tt_get_ignore_users"))
	{
		if(!defined("IN_MOBIQUO"))
		{
			define('IN_MOBIQUO', true);
		}			
		if(!isset($config['tapatalkdir']))
		{
			$config['tapatalkdir'] = 'mobiquo';
		}
		require_once $phpbb_root_path.$config['tapatalkdir'].'/mobiquo_common.php';
	}
	$ignore_users = tt_get_ignore_users($ttp_data['userid']);
	
	if(in_array($user->data['user_id'], $ignore_users))
	{
		return false;
	}
    $boardurl = generate_board_url();
	
    if(push_data_table_exists())
    {
    	tt_insert_push_data($ttp_data);
    }
    if($is_only_alert)
    {
    	return ;
    }
    $return_status = tt_do_post_request($ttp_data);
    return $return_status;
}

function tt_get_user_push_type($userid)
{
	global $table_prefix,$db,$phpbb_root_path,$config,$phpEx;
	if(!function_exists('push_table_exists'))
	{
		define('IN_MOBIQUO', 1);
		require_once $phpbb_root_path . $config['tapatalkdir'] . '/xmlrpcresp.' . $phpEx;
	}
	if(!push_table_exists())
	{
		return array();
	}
	$sql = "SELECT pm,subscribe as sub,quote,newtopic,tag FROM " . $table_prefix . "tapatalk_users WHERE userid = '".$userid."'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    return $row;
}

function tt_push_covert_content($data)
{
	global $user,$config,$phpbb_root_path,$phpEx;
	
	// Define the global bbcode bitfield, will be used to load bbcodes
	$bbcode_bitfield = '';
	$bbcode_bitfield = $bbcode_bitfield | base64_decode($data['bbcode_bitfield']);
	$bbcode = '';
	// Is a signature attached? Are we going to display it?
	if ($data['enable_sig'] && $config['allow_sig'] && $user->optionget('viewsigs'))
	{
		$bbcode_bitfield = $bbcode_bitfield | base64_decode($data['user_sig_bbcode_bitfield']);
	}
	if ($bbcode_bitfield !== '')
	{
		$bbcode = new bbcode(base64_encode($bbcode_bitfield));
	}
	if(!function_exists("censor_text"))
	{
		include_once($phpbb_root_path . 'includes/functions_content.' . $phpEx);
	}
	// Parse the message and subject
	$message = censor_text($data['message']);
    // tapatalk add for bbcode pretreatment
    if(!function_exists("process_bbcode"))
    {
    	if(!defined("IN_MOBIQUO"))
		{
			define('IN_MOBIQUO', true);
		}			
		if(!isset($config['tapatalkdir']))
		{
			$config['tapatalkdir'] = 'mobiquo';
		}
		if(file_exists($phpbb_root_path.$config['tapatalkdir'].'/mobiquo_common.php'))
		{
			require_once $phpbb_root_path.$config['tapatalkdir'].'/mobiquo_common.php';
		}
		else 
		{
			return $data['message'];
		}
    }
    $message = process_bbcode($message, $data['bbcode_uid']);
    
	// Second parse bbcode here
	if ($data['bbcode_bitfield'] && $bbcode)
	{
		if(!class_exists("bbcode"))
		{
			include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
		}
		$bbcode->bbcode_second_pass($message, $data['bbcode_uid'], $data['bbcode_bitfield']);
	}

	$message = bbcode_nl2br($message);
	$message = smiley_text($message);
	$message = post_html_clean($message);
	return $message;
}
?>