<?php
/** 
*
* @package Breizh Shoutbox
* @version $Id: shout_js.php 140 20:07 31/12/2010 Sylver35 Exp $ 
* @copyright (c) 2010, 2011 Sylver35    http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
define('AJAX_DEBUG', false);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './'; 
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include ($phpbb_root_path . 'common.' . $phpEx);
include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
if (!function_exists('decode_message'))
{
	include($phpbb_root_path . 'includes/functions_content.' . $phpEx);
}

// Start session management
$user->session_begin(false);
$auth->acl($user->data);
$user->setup(array('mods/shout', 'mods/info_acp_shoutbox'));

//Disable error reporting, can be bad for our headers ;)
error_reporting(0);

$shout 	= request_var('s', -1);

// Parameters for differents shoutbox
// Normal shoutbox
if ($shout == -1)
{
	$sort = $sort_p = '';
	$param = '&s=-1';
	$sort_auth = 'view';
}
// Private shoutbox
elseif ($shout == 0)
{
	$param = '&s=0';
	$sort = $sort_p = '_priv';
	$sort_auth = 'priv';
}
// Popup shoutbox
elseif ($shout == 1)
{
	$sort = '';
	$param = '&s=1';
	$sort_p = '_pop';
	$sort_auth = 'view';
}

// The number of messages to display
if (strpos(strtolower($user->data['session_browser']), 'msie') !== false)
{
	$shout_number = $config['shout_ie_nr' .$sort_p];
}
else
{
	$shout_number = $config['shout_non_ie_nr' .$sort_p];
}

if (!isset($shout_number))
{
	die;
}

// This detect what sort of style is in use, prosilver or subsilver2...
$is_prosilver = (file_exists($phpbb_root_path . 'styles/' .$user->theme['theme_path']. '/theme/common.css')) ? true : false;

// Load the user's preferences
if ($user->data['is_registered'] && !$user->data['is_bot'])
{
	list($correct, $new_sound, $error_sound, $del_sound) = explode(', ', $user->data['user_shout']);
	list($shout_bar, $shout_pagin, $shout_bar_pop, $shout_pagin_pop, $shout_bar_priv, $shout_pagin_priv) = explode(',', $user->data['user_shoutbox']);
	$new_sound		= ($new_sound == '') ? 		$config['shout_sound_new'] : 	((!$correct) ? $new_sound : '');
	$error_sound	= ($error_sound == '') ? 	$config['shout_sound_error'] : 	(($error_sound == '0') ? '' : $error_sound);
	$del_sound		= ($del_sound == '') ? 		$config['shout_sound_del'] : 	(($del_sound == '0') ? '' : $del_sound);
	$config['shout_bar_option']			= ($shout_bar != 'N') ? 		$shout_bar : 		$config['shout_bar_option'];
	$config['shout_bar_option_pop']		= ($shout_bar_pop != 'N') ? 	$shout_bar_pop : 	$config['shout_bar_option_pop'];
	$config['shout_bar_option_priv']	= ($shout_bar_priv != 'N') ? 	$shout_bar_priv : 	$config['shout_bar_option_priv'];
	$config['shout_pagin_option']		= ($shout_pagin != 'N') ? 		$shout_pagin : 		$config['shout_pagin_option'];
	$config['shout_pagin_option_pop']	= ($shout_pagin_pop != 'N') ? 	$shout_pagin_pop : 	$config['shout_pagin_option_pop'];
	$config['shout_pagin_option_priv']	= ($shout_pagin_priv != 'N') ? 	$shout_pagin_priv : $config['shout_pagin_option_priv'];
}
else
{
	$config['shout_sound_on'] = ($user->data['is_bot']) ? false : $config['shout_sound_on']; // No sounds for bots
	$correct		= ($config['shout_sound_on']) ? 0 : 1;
	$new_sound		= ($config['shout_sound_on']) ? $config['shout_sound_new'] : '';
	$error_sound	= ($config['shout_sound_on']) ? $config['shout_sound_error'] : '';
	$del_sound		= ($config['shout_sound_on']) ? $config['shout_sound_del'] : '';
}

//JS header
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT'); 
header('Cache-Control: no-cache, must-revalidate'); 
header('Pragma: no-cache');
header('Content-type: text/javascript; charset=UTF-8');	

?>
// We like ugly fixes.
is_ie				= false;  // Important!
is_pro				= '<?php echo ($is_prosilver) ? 1 : 0; ?>';
var forum_url		= '<?php echo generate_board_url(). '/'; ?>';
var correct			= '<?php echo $correct; ?>';  // Display the sound (0) or correct minutes for new messages (1)
var sound			= '<?php echo ($new_sound) ? 'new/' .$new_sound : ''; ?>';  // The sound for new messages
var error_sound		= '<?php echo ($error_sound) ? 'error/' .$error_sound : ''; ?>';  // The sound for errors
var del_sound		= '<?php echo ($del_sound) ? 'del/' .$del_sound : ''; ?>';  // The sound for delete messages
var rules_ok		= '<?php echo ($config['shout_rules'] && isset($config['shout_rules_' .$user->lang_name])) ? (($config['shout_rules_' .$user->lang_name] != '') ? 1 : 0) : 0; ?>';  // To display the rules
var sort_shout		= '<?php echo ($shout == 0) ? 'priv' : (($shout == 1) ? 'popup' : 'normal'); ?>';  // To enable sort shoutbox
var delete_url		= '<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=delete$sort$param", false); ?>';  // delete url
var edit_url		= '<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=edit$sort$param", false); ?>';  // Edit url
var version_url		= '<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=version$param", false); ?>';  // Version url
var lang_direction	= '<?php echo (($user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right'); ?>';  // To write the shoutbox with ltr or rtl languages
var see_buttons		= '<?php echo ($config['shout_see_buttons']) ? 1 : 0; ?>';  // To see top buttons when no permissions to use it
var see_buttonsleft	= '<?php echo ($config['shout_see_buttons_left']) ? 1 : 0; ?>';  // To see left buttons when no permissions to use it
var bar_haute		= '<?php echo ($config['shout_bar_option' .$sort_p]) ? 1 : 0; ?>';  // 1 to see the post bar in the top, 0 on bottom
var sort_pagin		= '<?php echo ($config['shout_pagin_option' .$sort_p]) ? 1 : 0; ?>';  // 1 to see the pagination in the post bar, 0 on bottom
var post_ok			= '<?php echo ($auth->acl_get('u_shout_post')) ? 1 : 0; ?>';  // Is able to post?
var smilies_ok		= '<?php echo ($auth->acl_get('u_shout_smilies')) ? 1 : 0; ?>';  // Is able to use smilies?
var image_ok		= '<?php echo ($auth->acl_get('u_shout_image')) ? 1 : 0; ?>';  // Is able to post images?
var color_ok		= '<?php echo ($auth->acl_get('u_shout_color')) ? 1 : 0; ?>';  // Is able to use color?
var bbcode_ok		= '<?php echo ($auth->acl_get('u_shout_bbcode')) ? 1 : 0; ?>';  // Is able to use bbcodes?
var chars_ok		= '<?php echo ($auth->acl_get('u_shout_chars')) ? 1 : 0; ?>';  // Is able to use special chars?
var popup_ok		= '<?php echo ($auth->acl_get('u_shout_popup')) ? 1 : 0; ?>';  // Is able to use popup?
var purge_ok		= '<?php echo ($auth->acl_get('u_shout_purge')) ? 1 : 0; ?>';  // Is able to use purge?
var priv_ok			= '<?php echo ($auth->acl_get('u_shout_priv')) ? 1 : 0; ?>';  // Is able to enter in private shoutbox?
var print_ver		= '<?php echo htmlspecialchars(sprintf($user->lang['SHOUTBOX_VER'], $config['shout_version'])); ?>';
var print_ver_alt	= '<?php echo htmlspecialchars(sprintf($user->lang['SHOUTBOX_VER_ALT'], $config['shout_version'])); ?>';
var print_perm		= '<?php echo htmlspecialchars($user->lang['NO_POST_PERM' .((!$user->data['is_registered']) ? '_GUEST' : '')]); ?>';  // Message to display when not able to post

<?php  // Prevent direct entries and exit if not enabled...
if (!$auth->acl_get('u_shout_' .$sort_auth) || !$config['shout_enable'])
{
	?>
	function load_shout()
	{
		return;
	}
	<?php
	exit;
}
?>

function shout_popup(pop_url, larg, haut)
{
	popup(pop_url, larg, haut, '_blank', "toolbar=0,menubar=0,scrollbars=0,statusbar=0,copyhistory=0,directories=0");
	return false;
}

function shout_priv(url)
{
	window.open(url); 
	return false;
}

function more_smilies()
{
	popup('<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx", "m=smilies_popup$param"); ?>', '<?php echo $config['shout_smilies_width']; ?>', '<?php echo $config['shout_smilies_height']; ?>', '_phpbbsmilies');
	return false;
}

function load_shout()
{
	try
	{
		var is_ie = ((clientPC.indexOf('msie') != -1) && (clientPC.indexOf('opera') == -1));
		if (display_shoutbox == false)
		{
			return;
		}
		
		if (document.getElementById('shoutbox') == null)
		{
		
			var ev = err_msg('<?php echo htmlspecialchars($user->lang['MISSING_DIV']); ?>', true);
			ev.name = 'E_CORE_ERROR';
			throw ev; 
			return;
		}
		else
		{
			div = document.getElementById('shoutbox');
			div.innerHTML = '';

			// Display message ;)
			message('<?php echo htmlspecialchars($user->lang['LOADING']); ?>');
			// HTTP vars, required to relead/post things.
			hin = http();
			if (!hin){return;}
			hin2 = http();
			huit = http();	
			hsmilies = http();
			hnr = http();
			<?php
			if ($auth->acl_get('u_shout_delete') || $auth->acl_get('u_shout_delete_s'))
			{
				echo 'hdelete = http();';
			}
			if ($auth->acl_get('u_shout_info') || $auth->acl_get('u_shout_info_s'))
			{
				echo 'hinfo = http();';
			}
			if ($auth->acl_get('u_shout_purge'))
			{
				echo 'hpurge = http();';
			}
			?>
			// Div exists in the html, write it.
			write_main();
		}
	}
	catch (e)
	{
		handle(e);
		return;
	}
}

function write_main()
{
	try
	{
		// Write the base.

		var base = ce('ul');
		base.className = 'topiclist forums';
		base.id = 'base_ul'
		base.style.height = '97%';

		if(is_pro == true){var li = ce('li')}else{var li = ce('table')};
		li.style.display = 'none';
		li.className = 'button_background button_background_<?php echo $config['shout_color_background' . (!$is_prosilver ? '_sub' : '') . $sort_p]; ?>';
		<?php if ($config['shout_title'] == '')$config['shout_title'] = $user->lang['SHOUT_START']; ?>
		if(is_pro == true){var dl = ce('dl')}else{var dl = ce('tr')};
		dl.style.width = '100%';
		if(is_pro == true){var posting_form = ce('dt')}else{var posting_form = ce('td')};
		posting_form.id = 'post_message';
		posting_form.className = 'row';
		posting_form.style.display = 'block';
		posting_form.style.paddingTop = '3px';
		posting_form.style.paddingBottom = '3px';
		posting_form.style.verticalAlign = 'middle';
		posting_form.height = '20px';
		if(post_ok == false && sort_pagin == false){posting_form.style.cssFloat = posting_form.style.styleFloat = 'none';}else{posting_form.style.cssFloat = posting_form.style.styleFloat = lang_direction;}
		if(bar_haute == false && sort_pagin == false && post_ok == true){posting_form.style.width = '100%';}else if(post_ok == false && sort_pagin == false){posting_form.style.width = '100%';}else{posting_form.style.width = 'auto';}
		if(post_ok == false && sort_pagin == true){posting_form.style.paddingLeft = '60px';}
		
		var posting_box = ce('form');
		posting_box.id = 'chat_form';
		posting_box.style.height = 'auto';
		if (sort_pagin == true){posting_box.style.cssFloat = posting_box.style.styleFloat = lang_direction;posting_form.style.marginTop = '1px';posting_form.style.width = 'auto';}
		
		<?php $title_1 = sprintf($user->lang['SHOUTBOX'], $config['shout_source'], $config['shout_title' .$sort]);$title_2 = sprintf($user->lang['SHOUT_COPY'], $config['shout_source'], $config['shout_version_full']);
		if ($auth->acl_get('u_shout_post'))
		{
		?>
			li.style.display = 'block';
            if (is_pro == false){li.style.marginBottom = '30px';}else{li.style.marginBottom = '5px';}
			if (bar_haute == false){li.style.borderTop = 'none';}else{li.style.borderBottom = 'none';}
			
			posting_box.appendChild(tn(' '));
			posting_box.appendChild(tn('<?php echo htmlspecialchars($user->lang['SHOUT_MESSAGE']); ?>: '));

			el = null;
			var el = ce('input');
            el.className = 'inputbox';
            el.name = el.id = 'chat_message';
            el.style.width = '<?php echo $config['shout_width_post' .$sort_p]; ?>px';
            el.autocomplete = 'off';
			
			el.onkeypress = function(evt)
			{
				try
				{
					evt = (evt) ? evt : event;
					var c = (evt.wich) ? evt.wich : evt.keyCode;
					if (c == 13)
					{
						document.getElementById('user').click();
						evt.returnValue = false;
						this.returnValue = false;
						return false;
					}
					return true;
				}
				catch (e)
				{
					handle(e);
					return;
				}
			}	
			posting_box.appendChild(el);
			posting_box.appendChild(tn(' '));

			var el = ce('input');
            var br = ce('br');
            el.name = el.id = 'user';
            el.value = el.defaultValue = '<?php echo htmlspecialchars($user->lang['POST_MESSAGE']); ?>';
            el.title = '<?php echo htmlspecialchars($user->lang['POST_MESSAGE_ALT']); ?>';
            el.type = 'button';
            el.className = 'button1 btnmain';
           
			el.onclick = function()
			{
				try
				{
					if (smilies == true && smilies_ok == true)
					{
						smilies = false;
						document.getElementById('smilies').innerHTML = '';
						document.getElementById('smilies_ul').style.display = 'none';
                        document.getElementById('smilies').style.display = 'none';
						el.title = '<?php echo htmlspecialchars($user->lang['SMILIES_CLOSE']); ?>';
					}
					if (colour == true && color_ok == true)
					{
						colour = false;
						document.getElementById('colour_palette').style.display = 'none';
						colored.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR_CLOSE']); ?>';
					}
					if (chars_view == true && chars_ok == true)
					{
						chars_view = false;
						document.getElementById('chars_view').style.display = 'none';
						chars.title = '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_CLOSE']); ?>';
					}
					if (shout_rules == true && rules_ok == true)
					{
						shout_rules = false;
						document.getElementById('shout_rules').style.display = 'none';
						div_rules.title = '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_CLOSE']); ?>';
					}
				
					// Here we send later the message ;)
					this.disabled = true;
		
					document.getElementById('post_message').style.display = 'none';
					
					this.disabled = false;
					document.getElementById('msg_txt').innerHTML = '';
					document.getElementById('msg_txt').appendChild(tn('<?php echo htmlspecialchars($user->lang['SENDING']); ?>'));

					if (document.getElementById('chat_message').value == '')
					{
						document.getElementById('msg_txt').innerHTML = '';
						throw err_msg('<?php echo htmlspecialchars($user->lang['MESSAGE_EMPTY']); ?>', true);
					}

					if (huit.readyState == 4 || huit.readyState == 0)
					{

						// Lets got some nice things :D
						huit.open('POST','<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=post$sort$param", false); ?>&rand='+Math.floor(Math.random() * 1000000),true);

						huit.onreadystatechange = function()
						{
							try
							{
								if (huit.readyState != 4)
								{
									return;
								}
								if (huit.readyState == 4)
								{
									xml = huit.responseXML;
									
									if (typeof xml != 'object')
									{
										if (error_clear <= 4)
										{
											last = 0;clearTimeout(timer_in);reload_post();reload_page();
										}
										else
										{
											play_sound(error_sound, 2);
											throw err_msg('<?php echo htmlspecialchars($user->lang['SERVER_ERR']); ?>');
											return;
										}
										error_clear++;
									}
									
									if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
									{
										err = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;
										document.getElementById('msg_txt').innerHTML = '';
										document.getElementById('post_message').style.display = 'block';
										last = 0;
										message(err, true);
										return;
									}
									else
									{
										document.getElementById('msg_txt').innerHTML = '';
										document.getElementById('msg_txt').appendChild(tn('<?php echo htmlspecialchars($user->lang['POSTED']); ?>'));
										setTimeout("document.getElementById('msg_txt').innerHTML = ''",500);
										document.getElementById('post_message').style.display = 'block';
										count = 0;// Set count to 0, because otherwise user willn't see his message
										clearTimeout(timer_in);
										timer_in = setTimeout('reload_post();reload_page();', 200);
										setTimeout('last = 0;', 500);
									}
									document.getElementById('chat_message').focus();
								}
							}
							catch (e)
							{
								handle(e);
								return;
							}
						}
						post = 'chat_message=';
						post += encodeURIComponent(document.getElementById('chat_message').value);
						document.getElementById('chat_message').value = '';
						
						if (smilies == true && smilies_ok == true)
						{
							smilies = false;
							document.getElementById('smilies').innerHTML = '';
							document.getElementById('smilies_ul').style.display = 'none';
                            document.getElementById('smilies').style.display = 'none';
							el.title = '<?php echo htmlspecialchars($user->lang['SMILIES_CLOSE']); ?>';
						}
						if (colour == true && color_ok == true)
						{
							colour = false;
							document.getElementById('colour_palette').style.display = 'none';
							colored.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR_CLOSE']); ?>';
						}
						if (chars_view == true && chars_ok == true)
						{
							chars_view = false;
							document.getElementById('chars_view').style.display = 'none';
							chars.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR_CLOSE']); ?>';
						}
						if (shout_rules == true && rules_ok == true)
						{
							shout_rules = false;
							document.getElementById('shout_rules').style.display = 'none';
							div_rules.title = '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_CLOSE']); ?>';
						}
						
						huit.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

						huit.send(post);
					}
					else
					{
						throw err_msg('This should not happen, double request found!');
					}
				}
				catch (e)
				{
					document.getElementById('post_message').style.display = 'inline';
					setTimeout("document.getElementById('msg_txt').innerHTML = ''",3000);
					handle(e);
					return;
				}
			}
			posting_box.appendChild(el);
			posting_box.appendChild(br);
			posting_box.appendChild(tn(' '));
			
			if (smilies_ok == true)
			{
				el = ce('input')
				el.type = 'button';
				el.className = 'button_shout_smile button_shout';
				if (smilies == true){el.title = '<?php echo htmlspecialchars($user->lang['SMILIES_CLOSE']); ?>';}else{el.title = '<?php echo htmlspecialchars($user->lang['SMILIES']); ?>';}

				el.onclick = function()
				{
					try
					{
						if (smilies == true)
						{
							smilies = false;
							document.getElementById('smilies').innerHTML = '';
							document.getElementById('smilies_ul').style.display = 'none';
                            document.getElementById('smilies').style.display = 'none';
							el.title = '<?php echo htmlspecialchars($user->lang['SMILIES']); ?>';
						}
						else
						{
							smilies = true;
							document.getElementById('smilies_ul').style.display = 'block';
                            document.getElementById('smilies').style.display = 'block';
							document.getElementById('smilies').appendChild(tn('<?php echo htmlspecialchars($user->lang['LOADING']); ?>'));
							el.title = '<?php echo htmlspecialchars($user->lang['SMILIES_CLOSE']); ?>';
							if (hsmilies.readyState == 4 || hsmilies.readyState == 0)
							{
								hsmilies.open('GET','<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx" ,  "m=smilies$param", false); ?>&rand='+Math.floor(Math.random() * 1000000),true);
								hsmilies.onreadystatechange = function()
								{
									try
									{
										if (hsmilies.readyState != 4)
										{
											return;
										}
										if (hsmilies.readyState == 4)
										{
											xml = hsmilies.responseXML;
											if (typeof xml != 'object')
											{
												play_sound(error_sound, 2);
												throw err_msg('<?php echo htmlspecialchars($user->lang['SERVER_ERR']); ?>');
												return;
											}
											
											if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
											{
												err = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;
												document.getElementById('smilies').innerHTML = '';
												message(err, true);
											}
											else
											{
												document.getElementById('smilies').innerHTML = '';
												var tmp = xml.getElementsByTagName('smilies');
												for (var i = (tmp.length - 1); i >= 0 ; i--)
												{
													var inh = tmp[i];
													var a = ce('a');
													a.code = inh.getElementsByTagName('code')[0].childNodes[0].nodeValue;
													if(inh.getElementsByTagName('title')[0].childNodes[0].nodeValue){a.title = inh.getElementsByTagName('title')[0].childNodes[0].nodeValue;}
													a.style.cursor = 'pointer';
													a.onclick = function(){document.getElementById('chat_message').value += ' ' + this.code + ' ';}
													
													var img = ce('img');
													img.src = inh.getElementsByTagName('img')[0].childNodes[0].nodeValue;
													img.width = inh.getElementsByTagName('width')[0].childNodes[0].nodeValue;
													if(inh.getElementsByTagName('title')[0].childNodes[0].nodeValue){img.title = inh.getElementsByTagName('title')[0].childNodes[0].nodeValue;}else{img.title = inh.getElementsByTagName('code')[0].childNodes[0].nodeValue;}
													img.alt = inh.getElementsByTagName('code')[0].childNodes[0].nodeValue;
													img.border = 0;
													
													a.appendChild(img);
													document.getElementById('smilies').appendChild(a);
													document.getElementById('smilies').appendChild(tn(' '));
												}
												var a = ce('a');
												a.title = '<?php echo htmlspecialchars($user->lang['SHOUT_MORE_SMILIES_ALT']); ?>';
												a.style.cursor = 'pointer';a.id = 'url_more';a.style.marginBottom = '5px';a.onclick = function(){more_smilies();}
												a.appendChild(tn('<?php echo htmlspecialchars($user->lang['SHOUT_MORE_SMILIES']); ?>'));
												document.getElementById('smilies').appendChild(tn('... '));
												document.getElementById('smilies').appendChild(a);
												document.getElementById('smilies').appendChild(tn(' '));
											}
										}
									}
									catch (e)
									{
										handle(e);
										return;
									}
								}
								hsmilies.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
								hsmilies.send(null);
							}
						}
					}
					catch (e)
					{
						handle(e);
						return;
					}
				}
				posting_box.appendChild(el);
                		posting_box.appendChild(tn(' '));
			}
			else if (see_buttons == true)
			{
				var el = ce('input');el.type = 'button';
                el.className = 'button_shout_smile_no button_shout';
                el.title = '<?php echo htmlspecialchars($user->lang['NO_SMILIES']); ?>';
                el.onclick = function(){alert('<?php echo htmlspecialchars($user->lang['NO_SMILIES']); ?>');}
				posting_box.appendChild(el);
                posting_box.appendChild(tn(' '));
			}
            
			// color button
			if (color_ok == true)
			{
				var colored = ce('input');
                colored.type = 'button';
                colored.className = 'button_shout_color button_shout';
                colored.accesskey = 'c';
                colored.name = colored.id = 'addbbcode10';
				colored.onclick = function()
				{
					try
					{
						if (colour == true)
						{
							colour = false;
							document.getElementById('colour_palette').style.display = 'none';
							colored.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR']); ?>';
						}
						else
						{
							colour = true;
							document.getElementById('colour_palette').style.display = 'block';
							colored.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR_CLOSE']); ?>';
							document.getElementById('colour_palette').appendChild(tn(' '));
						}
					}
					catch (e)
					{
						handle(e);
						return;
					}
				}
				if (colour == true){colored.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR_CLOSE']); ?>';}else{colored.title = '<?php echo htmlspecialchars($user->lang['SHOUT_COLOR']); ?>';}
				posting_box.appendChild(colored);
				posting_box.appendChild(tn(' '));
			}
			else if (see_buttons == true)
			{
				var colored = ce('input');
                colored.type = 'button';
                colored.className = 'button_shout_color_no button_shout';
                colored.accesskey = 'c';
                colored.name = colored.id = 'addbbcode10';
                colored.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_COLOR']); ?>';
                colored.onclick = function(){alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_COLOR']); ?>');} 
		posting_box.appendChild(colored);
                posting_box.appendChild(tn(' '));
			}
			
			// chars button
			if (chars_ok == true)
			{
				var chars = ce('input');chars.type = 'button';chars.className = 'button_shout_chars button_shout';chars.name = chars.id = 'chars01';
				chars.onclick = function()
				{
					try
					{
						if (chars_view == true)
						{
							chars_view = false;
							document.getElementById('chars_view').style.display = 'none';
							chars.title = '<?php echo htmlspecialchars($user->lang['SHOUT_CHARS']); ?>';
						}
						else
						{
							chars_view = true;
							document.getElementById('chars_view').style.display = 'block';
							chars.title = '<?php echo htmlspecialchars($user->lang['SHOUT_CHARS_CLOSE']); ?>';
							document.getElementById('chars_view').appendChild(tn(' '));
						}
					}
					catch (e)
					{
						handle(e);
						return;
					}
				}
				if (chars_view == true){chars.title = '<?php echo htmlspecialchars($user->lang['SHOUT_CHARS_CLOSE']); ?>';}else{chars.title = '<?php echo htmlspecialchars($user->lang['SHOUT_CHARS']); ?>';}
				posting_box.appendChild(chars);
				posting_box.appendChild(tn(' '));
			}
			else
			{
				var chars = ce('input');
                chars.type = 'button';
                chars.className = 'button_shout_chars_no button_shout';
                chars.accesskey = 'o';
                chars.name = chars.id = 'chars01';
                chars.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_CHARS']); ?>';
                chars.onclick = function(){alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_CHARS']); ?>');} 
				posting_box.appendChild(chars);
                posting_box.appendChild(tn(' '));
			}

			// BBcode buttons ;)
			if (bbcode_ok == true)
			{
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_bold button_shout';
                bbcode.accesskey = 'b';
                bbcode.name = bbcode.id = 'addbbcode0';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['SHOUT_BOLD']); ?>';
				bbcode.onclick = function(){var tfn = form_name;form_name = 'chat_form';
                var ttn = text_name;
                text_name = 'chat_message';
                bbstyle(0);form_name = tfn;
                text_name = ttn;}
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));

				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_italic button_shout';
                bbcode.accesskey = 'i';
                bbcode.name = bbcode.id = 'addbbcode2';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['SHOUT_ITALIC']); ?>';
				bbcode.onclick = function(){var tfn = form_name;form_name = 'chat_form';
                var ttn = text_name;text_name = 'chat_message';
                bbstyle(2);form_name = tfn;
                text_name = ttn;}
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));

				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_under button_shout';
                bbcode.accesskey = 'u';
                bbcode.name = bbcode.id = 'addbbcode4';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['SHOUT_UNDERLINE']); ?>';
				bbcode.onclick = function(){var tfn = form_name;
                form_name = 'chat_form';
                var ttn = text_name;
                text_name = 'chat_message';
                bbstyle(4);
                form_name = tfn;
                text_name = ttn;}
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));

				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_url button_shout';
                bbcode.accesskey = 'w';
                bbcode.name = bbcode.id = 'addbbcode8';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['SHOUT_URL']); ?>';
				bbcode.onclick = function(){var tfn = form_name;
                form_name = 'chat_form';
                var ttn = text_name;
                text_name = 'chat_message';
                bbstyle(8);
                form_name = tfn;
                text_name = ttn;}
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
			}
			else if (see_buttons == true)
			{
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_bold_no button_shout';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>';
                bbcode.onclick = function() {alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>');} 
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_italic_no button_shout';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>';
                bbcode.onclick = function() {alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>');} 
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_under_no button_shout';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>';
                bbcode.onclick = function() {alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>');} 
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_url_no button_shout';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>';
                bbcode.onclick = function() {alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>');} 
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
			}
            
			// Images button
			if (image_ok == true)
			{
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_img button_shout';
                bbcode.accesskey = 'p';
                bbcode.name = bbcode.id = 'addbbcode6';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['SHOUT_IMAGE']); ?>';
				bbcode.onclick = function(){var tfn = form_name;
                form_name = 'chat_form';
                var ttn = text_name;
                text_name = 'chat_message';
                bbstyle(6);
                form_name = tfn;
                text_name = ttn;
            }
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
				
				//var bbcode = ce('input');bbcode.type = 'button';bbcode.className = 'button_shout_img button_shout';bbcode.accesskey = 'p';bbcode.name = bbcode.id = 'addbbcode6';bbcode.title = '<?php echo htmlspecialchars($user->lang['SHOUT_IMAGE']); ?>';
				//bbcode.onclick = function() {shout_popup('<?php echo append_sid("{$phpbb_root_path}shout_popup.$phpEx", "m=img"); ?>', '700', '350');}
				//posting_box.appendChild(bbcode);posting_box.appendChild(tn(' '));
			}
			else if (see_buttons == true)
			{
				var bbcode = ce('input');
                bbcode.type = 'button';
                bbcode.className = 'button_shout_img_no button_shout';
                bbcode.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>';
                bbcode.onclick = function() {alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_BBCODE']); ?>');} 
				posting_box.appendChild(bbcode);
                posting_box.appendChild(tn(' '));
			}
            
			if (sort_shout == 'normal')
			{
				if (popup_ok == true)
				{
					var popup = ce('input');
                    popup.type = 'button';
                    popup.className = 'button_shout_popup button_shout';
                    popup.title = '<?php echo htmlspecialchars($user->lang['SHOUT_POP']); ?>';
                    popup.onclick = function() {shout_popup('<?php echo append_sid("{$phpbb_root_path}shout_popup.$phpEx", "s=1"); ?>', '<?php echo $config['shout_popup_width']; ?>', '<?php echo $config['shout_popup_height']; ?>');}
					posting_box.appendChild(popup);
                    posting_box.appendChild(tn(' '));
				}
				else if (see_buttons == true)
				{
					var popup = ce('input');
                    popup.type = 'button';
                    popup.className = 'button_shout_popup_no button_shout';
                    popup.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_POP']); ?>';
                    popup.onclick = function() {alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_POP']); ?>');}
					posting_box.appendChild(popup);
                    posting_box.appendChild(tn(' '));
				}
			}
            
			if (purge_ok == true)
			{
				var purge_r = ce('input');
                purge_r.type = 'button';
                purge_r.name = purge_r.id = 'purge_r';
                purge_r.className = 'button_shout_robot button_shout';
                purge_r.title = '<?php echo htmlspecialchars($user->lang['SHOUT_PURGE_ROBOT_ALT']); ?>';
				purge_r.onclick = function(){this.style.display = 'none';
                if (confirm('<?php echo htmlspecialchars($user->lang['SHOUT_PURGE_ROBOT_BOX']); ?>')) purge_shout('<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=purge_robot$sort$param"); ?>', 'purge_r'); };
				posting_box.appendChild(purge_r);
                posting_box.appendChild(tn(' '));
				
				var purge = ce('input');
                purge.type = 'button';
                purge.name = purge.id = 'purge';
                purge.className = 'button_shout_purge button_shout';
                purge.title = '<?php echo htmlspecialchars($user->lang['SHOUT_PURGE_ALT']); ?>';
				purge.onclick = function(){this.style.display = 'none';
                if (confirm('<?php echo htmlspecialchars($user->lang['SHOUT_PURGE_BOX']); ?>')) purge_shout('<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=purge$sort$param"); ?>', 'purge'); };
				posting_box.appendChild(purge);
                posting_box.appendChild(tn(' '));
			}
            
			if (sort_shout == 'normal')
			{
				if (priv_ok == true)
				{
					var priv = ce('input');
                    priv.type = 'button';
                    priv.className = 'button_shout_priv button_shout';
                    priv.title = '<?php echo htmlspecialchars($user->lang['SHOUT_PRIV']); ?>';
                    priv.onclick = function() {shout_priv('<?php echo append_sid("{$phpbb_root_path}shout_popup.$phpEx", 's=0'); ?>');}
					posting_box.appendChild(priv);
                    posting_box.appendChild(tn(' '));
				}
			}
            
			// config button
			<?php 
			if ($user->data['user_id'] != ANONYMOUS && !$user->data['is_bot'])
			{ ?>
			var button_config = ce('input');
            button_config.type = 'button';
            button_config.className = 'button_shout_config button_shout';
            button_config.title = '<?php echo htmlspecialchars($user->lang['SHOUT_CONFIG_OPEN']); ?>';
            button_config.onclick = function() {shout_popup('<?php echo append_sid("{$phpbb_root_path}shout_popup.$phpEx", "m=config"); ?>', '850', '500');}
			posting_box.appendChild(button_config);
            posting_box.appendChild(tn(' '));
			<?php
			}
			if ($config['shout_rules'] && isset($config['shout_rules_' .$user->lang_name]))
			{ 
				if ($config['shout_rules_' .$user->lang_name])
				{ ?>
				var div_rules = ce('input');
                div_rules.type = 'button';
                div_rules.className = 'button_shout_rules button_shout';
				div_rules.onclick = function()
				{
					if (shout_rules == true)
					{
						shout_rules = false;
						document.getElementById('shout_rules').style.display = 'none';
						div_rules.title = (sort_shout == 'priv') ? '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_PRIV']); ?>' : '<?php echo htmlspecialchars($user->lang['SHOUT_RULES']); ?>';
					}
					else
					{
						shout_rules = true;
						document.getElementById('shout_rules').style.display = 'block';
						div_rules.title = '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_CLOSE']); ?>';
						//div_rules.className = 'button_shout_rules_in button_shout';
						document.getElementById('shout_rules').appendChild(tn(' '));
					}
				}
				if (shout_rules == true){div_rules.title =  '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_CLOSE']); ?>';}else{div_rules.title = (sort_shout == 'priv') ? '<?php echo htmlspecialchars($user->lang['SHOUT_RULES_PRIV']); ?>' : '<?php echo htmlspecialchars($user->lang['SHOUT_RULES']);?>';}
				posting_box.appendChild(div_rules);
				posting_box.appendChild(tn(' '));
				<?php 
				}
			} ?>
			
			if (smilies_ok == true)
			{
				//var smilies = ce('div');
				//smilies.style.display = 'none';
                //smilies.name = smilies.id = 'smilies';
                //smilies.style.backgroundImage = 'none';
                //smilies.style.padding = '8px';
				//base.appendChild(smilies);
			}
			
			if (bar_haute == true)
			{
				posting_form.appendChild(posting_box);
				dl.appendChild(posting_form);
				li.appendChild(dl);
				base.appendChild(li);
			}
            
			if (bar_haute == true && sort_pagin == true)
			{
				if(is_pro == true){var pagin = ce('dd');}
				else{var pagin = ce('td');pagin.align = 'right';};
				pagin.id = 'nr';pagin.style.marginTop = '0px';
                pagin.style.padding = '4px';
                pagin.style.cssFloat = pagin.style.styleFloat = 'right';
				pagin.style.height = 'auto';
                pagin.style.width = 'auto';
                pagin.style.border = '0';
                pagin.className = 'pagination gensmall';
				dl.appendChild(pagin);
			}
		<?php
		}
		elseif (!$auth->acl_get('u_shout_post')) 
		{
		?>
			li.style.display = 'block';li.style.textAlign = 'center';li.style.padding = '3px';li.style.borderBottom = '1px solid #333';
			if (sort_pagin == true)
			{
				if(is_pro == true){var pagin = ce('dd');}
				else{var pagin = ce('td');pagin.align = 'right';};
				pagin.id = 'nr';pagin.className = 'pagination gensmall';pagin.style.marginTop = '0px';pagin.style.paddingTop = '1px';pagin.style.paddingBottom = '1px';
				pagin.style.height = 'auto';pagin.style.width = 'auto';pagin.style.cssFloat = pagin.style.styleFloat = 'right';pagin.style.borderLeft = '0';
				dl.appendChild(pagin);
			}
			posting_form.appendChild(posting_box);
			dl.appendChild(posting_form);
			li.appendChild(dl);
			base.appendChild(li);
			posting_box.appendChild(tn(print_perm));
			posting_box.appendChild(tn(' '));
		<?php
		}
		?>
		
		// The div for infos and error message
		var msg_txt = ce('div');
		msg_txt.id = 'msg_txt';
		msg_txt.appendChild(tn(''));
		base.appendChild(msg_txt);
		
		// var d_shout = document.getElementById('shout1');
		// var t_shout = document.getElementById('shout2');
		// if (lang_direction == 'right'){t_shout.style.styleFloat = t_shout.style.cssFloat = 'left';t_shout.style.paddingLeft = '10px';}else{t_shout.style.styleFloat = t_shout.style.cssFloat = 'right';t_shout.style.paddingRight = '10px';}
		if (lang_direction == 'right');
		// if (!t_shout || !d_shout){throw err_msg(lang['MISSING_DIV']);}
		// d_shout.innerHTML='<?php echo $title_1; ?>';t_shout.innerHTML = '<?php echo $title_2; ?>';

		//In this div, the chats will be placed ;)
		var post = ce('div');
		post.style.display = 'block';
        post.id = 'msg';
        post.style.width = '99.5%';
        post.style.overflowX = 'hidden';
		<?php
		$message = $user->lang['LOADING'];
		if (strpos(strtolower($user->browser), 'firefox') !== false && $message && $shout == -1)
		{ ?>
			post.style.height = 'auto';
			post.style.overflowY = 'hidden';
		<?php
		}
		elseif (strpos(strtolower($user->browser), 'opera') !== false && $message && $shout == -1)
		{ ?>
			post.style.height = 'auto';
			post.style.overflowY = 'hidden';
		<?php
		}
		elseif (strpos(strtolower($user->browser), 'msie') === false && $message)
		{
			echo 'post.style.height = \'' .(($shout == -1) ? $config['shout_height'] : ($config['shout_non_ie_height' .$sort_p])). 'px\';';
			echo "post.style.overflowY = 'auto';";
		}
		elseif (strpos(strtolower($user->browser), 'msie') !== false && $message && $shout == -1)
		{ ?>
			post.style.height = 'auto';
			post.style.overflowY = 'hidden';
		<?php
		}
		elseif (strpos(strtolower($user->browser), 'msie') !== false && $message && $shout != -1)
		{ ?>
			post.style.height = '<?php echo $config['shout_non_ie_height' .$sort_p]; ?>px';
			post.style.overflowY = 'hidden';
		<?php
		}
		?>
		post.appendChild(tn('<?php echo htmlspecialchars($message); ?>'));
		base.appendChild(post);

		if (bar_haute == false && post_ok == true)
		{
			posting_form.appendChild(posting_box);
			if (sort_pagin == true)
			{
				var pagin = ce('form');
				pagin.id = 'nr';pagin.className = 'pagination';
				pagin.style.marginTop = '0px';pagin.style.padding = '4px';pagin.style.height = 'auto';pagin.style.width = 'auto';
				pagin.style.backgroundImage = 'none';pagin.style.marginRight = '3px';pagin.style.cssFloat = pagin.style.styleFloat = 'right';pagin.style.borderLeft = '0';
				posting_form.appendChild(pagin);
			}
			
			dl.style.borderTop = '3px';
			dl.appendChild(posting_form);
			li.appendChild(dl);
			base.appendChild(li);
			
		}
        
		if (sort_pagin == false || post_ok == false && sort_pagin == false)
		{
			var pagindiv = ce('div');
			var pagin = ce('li');
			pagin.id = 'nr';pagin.className = 'button_background button_background_<?php echo $config['shout_color_background' .(!$is_prosilver ? '_sub' : '') . $sort_p]; ?> pagination';
			pagin.style.textAlign = lang_direction;pagin.style.marginTop = '0px';pagin.style.paddingTop = '2px';pagin.style.paddingBottom = '2px';pagin.style.height = '16px';pagin.style.width = '<?php echo ($is_prosilver ? '100%' : '99.5%'); ?>';
			pagindiv.appendChild(pagin);
			base.appendChild(pagindiv);
		}
		
		div.innerHTML = '';
		div.appendChild(base);
		// if (!t_shout || !d_shout){throw err_msg(lang['MISSING_DIV']);}
		// Everyting loaded, lets select posts :)
		reload_post();
		reload_page();
	}
	catch (e)
	{
		handle(e);
		return;
	}
}

function reload_page()
{
	if (hnr.readyState == 4 || hnr.readyState == 0)
	{
	    if (error_number > 6)
	    {
	        return;
		}

		// Lets got some nice things :D
		hnr.open('GET','<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=number$sort_p$param", false); ?>&rand='+Math.floor(Math.random() * 1000000),true);
		// var d_shout = document.getElementById('shout1');
		// var t_shout = document.getElementById('shout2');

		hnr.onreadystatechange = function()
		{
			try
			{
				if (hnr.readyState != 4)
				{
					return;
				}
				if (hnr.readyState == 4)
				{
					xml = hnr.responseXML;
					
					if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
					{
						err = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;
						message(err, true);
						return;
					}

					if (typeof xml != 'object' || xml.getElementsByTagName('nr').length <= 0)
					{
						if (error_clear <= 4)
						{
							last = 0;clearTimeout(timer_in);reload_post();reload_page();
						}
						else
						{
							play_sound(error_sound, 2);
							throw err_msg('<?php echo htmlspecialchars($user->lang['SERVER_ERR']); ?>');
							return;
						}
						error_clear++;
					}
					// if (!t_shout || !d_shout){throw err_msg(lang['MISSING_DIV']);}

					var nr = xml.getElementsByTagName('nr')[0].childNodes[0].nodeValue;
					var f = document.getElementById('nr');f.innerHTML = '';
					var d = ce('div');

					if (nr < <?php echo $shout_number; ?>){return;}

					var per_page = <?php echo $shout_number; ?>;
					var total_pages = Math.ceil(nr / per_page);
					var is_page = '<?php echo htmlspecialchars($user->lang['SHOUT_PAGE']); ?>';
					if (total_pages == 1 || !nr){return;}
					on_page = Math.floor(count / per_page) + 1;

					var p = ce('span');
					var a = ce('a');
					var b = ce('strong');
					p.style.display = 'inline';p.style.verticalAlign = 'middle';p.style.paddingLeft = '8px';

					if (on_page == 1)
					{
						b.appendChild(tn('1'));b.title = is_page+'1';
						p.appendChild(b);
						b = ce('strong');
					}
					else
					{
						a.c = ((on_page - 2) * per_page);a.href = 'javascript:;';a.title = '<?php echo htmlspecialchars($user->lang['PREVIOUS']); ?>';
						a.onclick = function(){count = this.c;last = 0;clearTimeout(timer_in);reload_post();reload_page();}
						a.appendChild(tn('<?php echo htmlspecialchars($user->lang['PREVIOUS']); ?>'));
						p.appendChild(a);p.appendChild(tn(' '));

						a = ce('a');a.c = 0;a.href = 'javascript:;';a.title = is_page+'1';
						a.onclick = function(){count = this.c;last = 0;clearTimeout(timer_in);reload_post();reload_page();}
						a.appendChild(tn('1'));
						p.appendChild(a);
						a = ce('a');
					}

					if (total_pages > 5)
					{
						var start_cnt = Math.min(Math.max(1, on_page - 4), total_pages - 5);
						var end_cnt = Math.max(Math.min(total_pages, on_page + 4), 6);

						p.appendChild((start_cnt > 1) ? tn(' ... ') : cp());

						for (var i = start_cnt + 1; i < end_cnt; i++)
						{
							if (i == on_page)
							{
								b.appendChild(tn(i));b.title = is_page+on_page;
								p.appendChild(b);
								b = ce('strong');
							}
							else
							{
								a.c = (i - 1) * per_page;a.href = 'javascript:;';a.title = is_page+i;
								a.onclick = function(){count = this.c;last = 0;clearTimeout(timer_in);reload_post();reload_page();}
								a.appendChild(tn(i));
								p.appendChild(a);
								a = ce('a');
							}
							if (i < end_cnt - 1)
							{
								p.appendChild(cp());
							}
						}
						p.appendChild((end_cnt < total_pages) ? tn(' ... ') : cp());
					}
					else
					{
						p.appendChild(cp());
						for (var i = 2; i < total_pages; i++)
						{
							if (i == on_page)
							{
								b.appendChild(tn(i));b.title = is_page+on_page;
								p.appendChild(b);
								b = ce('strong');
							}
							else
							{
								a.c = (i - 1) * per_page;a.href = 'javascript:;';a.title = is_page+i;
								a.onclick = function(){count = this.c;last = 0;clearTimeout(timer_in);reload_post();reload_page();}
								a.appendChild(tn(i));
								p.appendChild(a);
								a = ce('a');
							}
							if (i < total_pages)
							{
								p.appendChild(cp());
							}
						}
					}

					if (on_page == total_pages)
					{
						b.appendChild(tn(total_pages));b.title = is_page+on_page;
						p.appendChild(b);
						b = ce('strong');
					}
					else
					{
						a = ce('a');a.c = ((total_pages - 1) * per_page);a.href = 'javascript:;';a.title = is_page+total_pages;
						a.onclick = function(){count = this.c;last = 0;clearTimeout(timer_in);reload_post();reload_page();}
						a.appendChild(tn(total_pages));

						p.appendChild(a);
						a = ce('a');a.c = ((on_page) * per_page);a.href = 'javascript:;';a.title = '<?php echo htmlspecialchars($user->lang['NEXT']); ?>';
						a.onclick = function(){count = this.c;last = 0;clearTimeout(timer_in);reload_post();reload_page();}
						a.appendChild(tn('<?php echo htmlspecialchars($user->lang['NEXT']); ?>'));
						p.appendChild(tn(' '));p.appendChild(a);
					}
					f.appendChild(p);
				}
			}
			catch (e)
			{
				handle(e);
				clearTimeout(timer_in);
				return;
			}
		}
		hnr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

		hnr.send(null);
	}
}

function reload_post()
{
	// If there is a open edit field, we dont reload.
	// If we reload, this edit field will be closed, and data lost.
	if (one_open)
	{
	    timer_in = setTimeout('reload_post();',3000);
		return;
	}
	
    if (error_number > 6)
    {
        return;
	}
	
	// First check if there new posts.
	if (hin2.readyState == 4 || hin2.readyState == 0)
	{
		hin2.open('GET','<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx", "m=check$sort_p$param", false); ?>&last='+last+'&rand='+Math.floor(Math.random() * 1000000),true);
		hin2.onreadystatechange = function()
		{
			try
			{
				if (hin2.readyState != 4)
				{
					return;
				}
				if (hin2.readyState == 4)
				{
					if (!hin2.responseXML)
					{
						if (error_clear <= 6)
						{
							timer_in = setTimeout('reload_post();', 3000);
							return;
						}
						else
						{
							throw err_msg('<?php echo htmlspecialchars($user->lang['XML_ER']); ?>');
							return;
						}
						error_clear++;
					}
					var xml = hin2.responseXML;		
					
					if (typeof xml != 'object')
					{
						if (error_clear <= 4)
						{
							last = 0;clearTimeout(timer_in);reload_post();reload_page();
							return;
						}
						else
						{
							play_sound(error_sound, 2);
							throw err_msg('<?php echo htmlspecialchars($user->lang['SERVER_ERR']); ?>');
							return;
						}
						error_clear++;
					}
					
					if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
					{
						err = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;
						throw err_msg(err, true);
						return;
					}
					
					if (xml.getElementsByTagName('time').length <= 0 || xml.getElementsByTagName('time')[0].childNodes.length <= 0)
					{
						if (error_clear <= 4)
						{
							last = 0;clearTimeout(timer_in);reload_post();reload_page();
							return;
						}
						else
						{
							play_sound(error_sound, 2);
							throw err_msg('<?php echo htmlspecialchars($user->lang['SERVER_ERR']); ?>');
							return;
						}
						error_clear++;
					}
					
					var t = xml.getElementsByTagName('time')[0].childNodes[0].nodeValue;
					if (t == '0')
					{
						// If start is true, we let notice that there are no messages
						if (start == true)
						{
							if (post_ok == true && first)
							{
								document.getElementById('post_message').style.display = 'inline';
								first = false;
							}
							
							var posts = document.getElementById('msg');
							posts.innerHTML = '';
							posts.appendChild(tn('<?php echo htmlspecialchars($user->lang['NO_MESSAGE']); ?>'));
						}
					}
					else
					{
						if (hin.readyState == 4 || hin.readyState == 0)
						{
							last = xml.getElementsByTagName('last')[0].childNodes[0].nodeValue;
							// Lets got some nice things :D
							hin.open('GET','<?php echo append_sid("{$phpbb_root_path}ajax.$phpEx",  "m=view$sort_p$param", false); ?>&start='+count+'&rand='+Math.floor(Math.random() * 1000000),true);
							hin.onreadystatechange = function()
							{
								try
								{
									if (hin.readyState != 4)
									{
										return;
									}
									if (hin.readyState == 4)
									{
										if (!hin.responseXML)
										{
											if (error_clear <= 4)
											{
												last = 0;clearTimeout(timer_in);reload_post();reload_page();
												return;
											}
											else
											{
												throw err_msg('<?php echo htmlspecialchars($user->lang['XML_ER']); ?>');
												return;
											}
											error_clear++;
										}
										var xml = hin.responseXML;
										
										if (typeof xml != 'object')
										{
											if (error_clear <= 4)
											{
												last = 0;clearTimeout(timer_in);reload_post();reload_page();
												return;
											}
											else
											{
												play_sound(error_sound, 2);
												throw err_msg('<?php echo htmlspecialchars($user->lang['SERVER_ERR']); ?>');
												return;
											}
											error_clear++;
										}
										
										if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
										{
											var msg = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;
											throw err_msg(msg, true);
											return;
										}
										else
										{
											start = false;
											var tmp = xml.getElementsByTagName('posts');
											if (tmp.length == 0)
											{
												if (post_ok == true)
												{
													if (first)
													{
														document.getElementById('post_message').style.display = 'inline';
														first = false;
													}
												}
												
												var posts = document.getElementById('msg');
												posts.innerHTML = '';
												posts.appendChild(tn('<?php echo htmlspecialchars($user->lang['NO_MESSAGE']); ?>'));
												timer_in = setTimeout('reload_post();',4000);
												return;
											} 
											var posts = document.getElementById('msg');
											posts.innerHTML = '';
											
											if(last != 0 && correct == false){play_sound(sound, 1);}
											
											var row = false;
											for (var i = 0; i < tmp.length; i++)
											{
												var li = ce('li');
												li.className = (!row) ? 'row row1 bg1' : 'row row2 bg2';li.style.width = '100%';li.style.minHeight = '22px';li.style.padding = '0';
                                                if (is_pro == false){li.style.borderBottom = '1px solid #333';}else{li.style.borderBottom = '1px solid #00608F';}
												row = !row;
												
												var dl = ce('dl');
												var dd = ce('dt');
												var dt = ce('dd');
												var inh = tmp[i];
												
												dd.className = 'button_background<?php echo (($config['shout_button_background' .$sort_p]) ? ' button_background_' .$config['shout_color_background' .((!$is_prosilver) ? '_sub' : '') . $sort_p] : ''); ?>';
												dd.style.width = 'auto';dd.style.styleFloat = dd.style.cssFloat = lang_direction;
												dt.style.width = 'auto';dt.id = 'ddshout' + i;dt.style.display = 'inline';dt.style.styleFloat = dt.style.cssFloat = lang_direction;
												
												if(lang_direction == 'left'){dt.style.paddingLeft = '3px';dt.style.paddingRight = '5px';dt.style.bottom = '0';
												}else{dt.style.paddingLeft = '5px';dt.style.paddingRight = '3px';dt.style.bottom = '0';}

												var s = ce('span');
												var msg = parse_xml_to_html(inh.getElementsByTagName('shout_text')[0]);
												
												if (lang_direction == 'left')
												{
													dt.appendChild(parse_xml_to_html(inh.getElementsByTagName('shout_time')[0]));
													if (inh.getElementsByTagName('avatar').length != 0 &&  inh.getElementsByTagName('avatar')[0].childNodes.length != 0){dt.appendChild(tn(' ¦ '));dt.appendChild(parse_xml_to_html(inh.getElementsByTagName('avatar')[0]));dt.appendChild(tn(' ¦ '));}
													else{dt.appendChild(tn(' ¦ '));}
													dt.appendChild(parse_xml_to_html(inh.getElementsByTagName('username')[0]));
													dt.appendChild(tn(':'));
												}else{
													dt.appendChild(parse_xml_to_html(inh.getElementsByTagName('shout_time')[0]));
													if (inh.getElementsByTagName('avatar').length != 0 &&  inh.getElementsByTagName('avatar')[0].childNodes.length != 0){dt.appendChild(tn(' ¦ '));dt.appendChild(parse_xml_to_html(inh.getElementsByTagName('avatar')[0]));dt.appendChild(tn(' ¦ '));}
													else{dt.appendChild(tn(' ¦ '));}
													dt.appendChild(parse_xml_to_html(inh.getElementsByTagName('username')[0]));
													dt.appendChild(tn(':'));
												}
												
												if (inh.getElementsByTagName('delete').length >= 1 && inh.getElementsByTagName('delete')[0].childNodes.length >= 1 && inh.getElementsByTagName('delete')[0].childNodes[0].nodeValue == 1){var ok_delete = true;}else{var ok_delete = false;}
												if (inh.getElementsByTagName('edit').length >= 1 && inh.getElementsByTagName('edit')[0].childNodes.length >= 1 && inh.getElementsByTagName('edit')[0].childNodes[0].nodeValue == 1){var ok_edit = true;}else{var ok_edit = false;}
												if (inh.getElementsByTagName('show_ip').length >= 1 && inh.getElementsByTagName('show_ip')[0].childNodes.length >= 1 && inh.getElementsByTagName('show_ip')[0].childNodes[0].nodeValue == 1){var ok_info = true;}else{var ok_info = false;}
												delete_button = ce('input');delete_button.post_id = inh.getElementsByTagName('shout_id')[0].childNodes[0].nodeValue;delete_button.type = 'button';
												if (ok_delete == true){
													delete_button.className = 'button_shout_del button_shout_l';delete_button.style.display = 'inline';delete_button.title = '<?php echo htmlspecialchars($user->lang['SHOUT_DEL']); ?>';
													delete_button.onclick = function(){this.style.display = 'none';if (confirm('<?php echo htmlspecialchars($user->lang['DEL_SHOUT']); ?>')){delete_message(this.post_id);}else{this.style.display = 'inline';}}
													dd.appendChild(delete_button);
												}else{if(see_buttonsleft == true){
														delete_button.className = 'button_shout_del_no button_shout_l';delete_button.style.display = 'inline';delete_button.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_DEL']); ?>';
														delete_button.onclick = function(){alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_DEL']); ?>');}
														dd.appendChild(delete_button);
													}else{delete_button.style.display = 'none';}
												}
												
												edit_button = ce('input');edit_button.post_id = inh.getElementsByTagName('shout_id')[0].childNodes[0].nodeValue;edit_button.type = 'button';
												if (ok_edit == true){
													edit_button.style.display = 'inline';edit_button.className = 'button_shout_edit button_shout_l';edit_button.title = '<?php echo htmlspecialchars($user->lang['SHOUT_EDIT']); ?>';
													dd.appendChild(edit_button);
												}else{if(see_buttonsleft == true){
														edit_button.style.display = 'inline';edit_button.className = 'button_shout_edit_no button_shout_l';edit_button.title = '<?php echo htmlspecialchars($user->lang['NO_SHOUT_EDIT']); ?>';edit_button.onclick = function(){alert('<?php echo htmlspecialchars($user->lang['NO_SHOUT_EDIT']); ?>');}
														dd.appendChild(edit_button);
													}else{edit_button.style.display = 'none';}
												}
												
												info_button = ce('input');info_button.post_id = inh.getElementsByTagName('shout_id')[0].childNodes[0].nodeValue;info_button.type = 'button';
												if (ok_info == true){
													var textalerte = '<?php echo htmlspecialchars($user->lang['SHOUT_POST_IP']); ?>';
													info_button.style.display = 'inline';info_button.className = 'button_shout_ip button_shout_l';info_button.title = '<?php echo htmlspecialchars($user->lang['SHOUT_IP']); ?>';info_button.ip = inh.getElementsByTagName('shout_ip')[0].childNodes[0].nodeValue;info_button.onclick = function(){alert(textalerte + '\n' + this.ip);}
													dd.appendChild(info_button);
												}else{if (see_buttonsleft == true){
														var noiptextalerte = '<?php echo htmlspecialchars($user->lang['NO_SHOW_IP_PERM']); ?>';
														info_button.style.display = 'inline';info_button.className = 'button_shout_ip_no button_shout_l';info_button.title = noiptextalerte;info_button.is_ip = inh.getElementsByTagName('is_ip')[0].childNodes[0].nodeValue;info_button.onclick = function(){alert(noiptextalerte + '\n IP: ' + this.is_ip);}
														dd.appendChild(info_button);
													}else{info_button.style.display = 'none';}
												}
												if (ok_info == false && ok_edit == false && ok_delete == false && see_buttonsleft == false){
													var dg = ce('dt');
													dg.style.styleFloat = dg.style.cssFloat = lang_direction;dg.style.paddingLeft = '0px';dg.style.paddingRight = '0px';dg.style.display = 'inline';
													dg.appendChild(tn(''));
													dl.appendChild(dg);
												}else{dl.appendChild(dd);
												}
												dd = ce('dd');
												if (lang_direction == 'left'){dd.style.paddingLeft = '3px';}else{dd.style.paddingRight = '3px';}

												dl.appendChild(dt);
												var msg2 = ce('span');
												msg2.style.display = 'inline';msg2.style.padding = '3px';msg2.id = 'shout' + i;msg2.i = i;
												msg2.appendChild(msg);
												edit_form = ce('span');
												var spa = ce('dd');
												spa.id = 'spa' + i;
												dd.appendChild(msg2);
												
												if (ok_edit == true){
													dd = handle_edit(dd, inh, i);
													edit_form.appendChild(spa);
													dd.appendChild(edit_form);
												}
												
												dd.id = 'msgbody' + i;dd.style.width = 'auto';dd.style.styleFloat = dd.style.cssFloat = 'none';dd.style.borderLeft = 'none';
												if (lang_direction == 'left'){dd.style.paddingLeft = '3px';dd.style.paddingRight = '5px';
												}else{dd.style.paddingLeft = '5px';dd.style.paddingRight = '3px';}

												dl.appendChild(dd);
												li.appendChild(dl);
												posts.appendChild(li);
											}

											if (post_ok == true)
											{
												// Only do this when user is able to post?
												if (first)
												{
													document.getElementById('post_message').style.display = 'inline';
													first = false;
												}
											}
										}
									}
								}
								catch (e)
								{
									timer_in = setTimeout('reload_post();',3000);
									handle(e);
									return;
								}
							}
							hin.send(null);
						}
					}
					timer_in = setTimeout('reload_post();',3000);
				}
			}
			catch (e)
			{
				handle(e);
				return;
			}
		}
		hin2.send(null);
	}
}

<?php
$pr = array(
	'COMMA_SEPARATOR',
	'NO_AJAX',
	'SERVER_ERR',
	'JS_ERR',
	'LINE',
	'FILE',
	'MSG_DEL_DONE',
	'ONLY_ONE_OPEN',
	'EDIT',
	'SHOUT_EDIT',
	'SENDING_EDIT',
	'EDIT_DONE',
	'CANCEL',
	'PURGE_PROCESS',
	'MISSING_DIV',
);

foreach ($pr as $i => $entry)
{
	$value = htmlspecialchars($user->lang[$entry]);
	echo "lang['$entry'] = '$value';\n";
}
$url_board = generate_board_url(). '/';
echo "lang['BOARD_PATH'] = '{$url_board}';\n";
echo "lang['IMAGE'] = '<img src=\"{$url_board}images/shoutbox/loading.gif\" alt=\"\" />';\n";
?>