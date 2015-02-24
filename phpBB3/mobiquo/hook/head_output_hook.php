<?php
if(!defined('IN_PHPBB')) exit;
if(!isset($tapatalk_hook_run)) $tapatalk_hook_run = true;
if($tapatalk_hook_run)
{
	$user->add_lang('mods/info_acp_mobiquo');
	if(file_exists($phpbb_root_path.$tapatalk_dir.'/hook/function_hook.php'))
	{
		require_once $phpbb_root_path.$tapatalk_dir.'/hook/function_hook.php';
		$tapatalk_location_url = get_tapatalk_location();
	}
	else 
	{
		$tapatalk_location_url = '';
	}
	if(file_exists($phpbb_root_path.$tapatalk_dir . '/smartbanner/head.inc.php'))
	{
		$api_key = isset($config['tapatalk_push_key']) ? $config['tapatalk_push_key'] : '';
    	//$app_ads_enable = isset($config['tapatalk_app_ads_enable']) ? $config['tapatalk_app_ads_enable'] : 1;
    	//$app_banner_enable = isset($config['tapatalk_app_banner_enable']) ? $config['tapatalk_app_banner_enable'] : 1;
		$app_forum_name = $config['sitename'];
	    $tapatalk_dir_url = $board_url . $tapatalk_dir;
	    $is_mobile_skin = 0;
	    $app_location_url = $tapatalk_location_url;
	    
	    preg_match('/location=(\w+)/is', $app_location_url,$matches);
	    if(!empty($matches[1]))
	    {
	    	if($matches[1] == 'message')
	    	{
	    		$matches[1] = 'pm';
	    	}
	    	$page_type = $matches[1];
	    }
	    
	    $app_banner_message = isset($config['tapatalk_app_banner_msg']) ? $config['tapatalk_app_banner_msg'] : '';
	    $app_ios_id = isset($config['tapatalk_app_ios_id']) ? $config['tapatalk_app_ios_id'] : '';
	    $app_android_id = isset($config['tapatalk_android_url']) ? $config['tapatalk_android_url'] : '';
	    $app_kindle_url = isset($config['tapatalk_kindle_url']) ? $config['tapatalk_kindle_url'] : '';
	     
		include $phpbb_root_path.$tapatalk_dir . '/smartbanner/head.inc.php';
		$app_head_include = $app_head_include ."\n" . (isset($template->_rootref['META']) ? $template->_rootref['META'] : '');
		
	}
	$body_js_hook = '<!-- Tapatalk Detect body start --> 
	<script type="text/javascript">
	if(typeof(tapatalkDetect) == "function") {
		tapatalkDetect();
	}
	</script>
	<!-- Tapatalk Detect banner body end -->';
	if(isset($user->lang['POWERED_BY']))
	{
		$user->lang['POWERED_BY'] .= $body_js_hook;
	}
}
$tapatalk_hook_run = false;
	