<?php
defined('IN_MOBIQUO') or exit;
require_once TT_ROOT . "include/classTTJson.php";
require_once TT_ROOT . "include/classTTConnection.php";
include_once TT_ROOT . "include/classTTCipherEncrypt.php";
function set_api_key()
{
    $code = trim($_REQUEST['code']);
    $key = trim($_REQUEST['key']);
    $connection = new classTTConnection();
    $response = $connection->actionVerification($code,'set_api_key');
    if($response === true)
    {
        set_config('tapatalk_push_key', $key);
        echo 1;
    }
    else if($response)
    {
        echo $response;
    }
    else 
    {
        echo 0;
    }
}

function sync_user_func()
{
    global $db,$config;
    $code = trim($_POST['code']);
    $start = intval(isset($_POST['start']) ? $_POST['start'] : 0);
    $limit = intval(isset($_POST['limit']) ? $_POST['limit'] : 1000);
    $format = trim($_POST['format']);
    
    $connection = new classTTConnection();
    $response = $connection->actionVerification($code,'sync_user');
    
    if($response === true)   
    {
        $api_key = $config['tapatalk_push_key'];        
        if(empty($api_key))
        {
            trigger_error('NO_USER');
        }        
        // Get users...
        $users = array();
        $sql = 'SELECT user_id as uid, username ,user_email,user_allow_massemail as allow_email,user_lang as language
            FROM ' . USERS_TABLE . "
            WHERE user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . "," . USER_INACTIVE . ") AND user_id > $start ORDER BY uid ASC LIMIT $limit";
        $result = $db->sql_query($sql);    
        
        $e = new TT_Cipher;
        while ($member = $db->sql_fetchrow($result))
        {
            $member['encrypt_email'] = base64_encode($e->encrypt($member['user_email'],$api_key));
            unset($member['user_email']);
            $users[] = $member;
        }
        $db->sql_freeresult($result);
        $data = array(
            'result' => true,
            'new_encrypt' => true,
            'users' => $users,
        );
    }
    else 
    {
        $data = array(
            'result' => false,
            'result_text' => $response,
        );
    }
    $response = ($format == 'json') ? json_encode($data) : serialize($data);
    echo $response;
    exit;
}


