/**
*
* @package Breizh Shoutbox
* @version $Id: static.php 140 20:08 31/12/2010 Sylver35 Exp $ 
* @copyright (c) 2010, 2011 Sylver35    http://breizh-portal.com
* @copyright (c) 2007 Paul Sohier
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
var div, hin, huit, hin2, hsmilies, hinfo, hpurge, hdelete, hnr;
var is_ie = ((clientPC.indexOf('msie') != -1) && (clientPC.indexOf('opera') == -1));
var config = new Array();
var post_info = false;
var timer_in, last, display_shoutbox = null;
var count = 0;
var start = true;
var first = true;
var smilies = false;
var colour = false;
var chars_view = false;
var shout_rules = false;
var form_name = 'chat_form';
var text_name = 'chat_message';
var bbcode = new Array();
var bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]', '[img]','[/img]', '[url]', '[/url]', '[color]', '[/color]');
var one_open = false;
var lang = new Array();
var edit_button, edit_form = null;
var error_number = 0;
var error_clear = 0;

function err_msg(title, not_reload_complete)
{
	var err = new Error(title);

	if (!err.message)
	{
		err.message = title;
	}

	if (!not_reload_complete)
	{
		load_shout()
	}

	err.name = "E_USER_ERROR";// Php error?!? :D
	return err;
}

function handle(e)
{
	switch (e.name)
	{
		//Is it our error? :)
		case "E_USER_ERROR":
		case "E_CORE_ERROR":
				message(e.message, true);
			return;
		break;

		default:
		{
			tmp = lang['JS_ERR'];
			tmp += e.message;
			if (e.lineNumber)
			{
				tmp += '\n' + lang['LINE'] + ': ';
				tmp += e.lineNumber;
			}
			if (e.fileName)
			{
				tmp += '\n' + lang['FILE'] + ' : ';
				tmp += e.fileName;
			}
			message(tmp, true);
			return;
		}
	}
}

function parse_xml_to_html(xml)
{
	try
	{
		if (xml.childNodes.length == 0)
		{
			return tn('');
		}
		else if (xml.childNodes.length == 1 && xml.childNodes[0].nodeValue != null)
		{
			// With a tag in it, its bigger as 1?
			return tn(xml.childNodes[0].nodeValue);
		}
		else
		{
			var div = ce('span');
			loop:

			for (var i = 0; i < xml.childNodes.length; i++)
			{
				switch (xml.childNodes[i].nodeType)
				{
					case 3:
						div.appendChild(document.createTextNode(xml.childNodes[i].nodeValue));
					break;

					case 8:
					case 9:
					case 10:
					case 11:
						// continue;
					break;

					case 1:
						if (xml.childNodes[i].childNodes.length == 0 && xml.childNodes[i].nodeName != 'br' && xml.childNodes[i].nodeName != 'img' && xml.childNodes[i].nodeName != 'hr')
						{
							break;
						}

						// This is a difficult one :)
						switch (xml.childNodes[i].nodeName)
						{
							case 'blockquote':
								var q = ce('blockquote');
								q.className = 'quote';
								q.appendChild(parse_xml_to_html(xml.childNodes[i]));
								add_style(xml.childNodes[i], q);
								div.appendChild(q);
							break;

							case 'a':
								var a = ce('a');
								a.href = xml.childNodes[i].getAttribute('href');
								if (xml.childNodes[i].getAttribute('title')){a.title = xml.childNodes[i].getAttribute('title');}
								if (xml.childNodes[i].getAttribute('class')){a.className = xml.childNodes[i].getAttribute('class');}
								if (xml.childNodes[i].getAttribute('target')){a.target = xml.childNodes[i].getAttribute('target');}
								if (xml.childNodes[i].getAttribute('onclick')){a.onclick = xml.childNodes[i].getAttribute('onclick');}
								a.appendChild(parse_xml_to_html(xml.childNodes[i]));
								add_style(xml.childNodes[i], a);
								div.appendChild(a);
							break;

							case 'img':
								var img = ce('img');
								img.src = xml.childNodes[i].getAttribute('src');
								img.alt = xml.childNodes[i].getAttribute('alt');
								if (img.title = xml.childNodes[i].getAttribute('title')){img.title = xml.childNodes[i].getAttribute('title');}else{img.title = xml.childNodes[i].getAttribute('alt')}
								if (xml.childNodes[i].getAttribute('class')){img.className = xml.childNodes[i].getAttribute('class');}
								if (xml.childNodes[i].getAttribute('width')){img.width = xml.childNodes[i].getAttribute('width');}
								if (xml.childNodes[i].getAttribute('height')){img.height = xml.childNodes[i].getAttribute('height');}
								img.style.border = 0;
								add_style(xml.childNodes[i], img);
								div.appendChild(img);
							break;

							case 'script':
							case 'vbscript':
							case 'iframe':
							case 'embed':
							case 'applet':
								
								// Bad boys, die.
								return;
							break;

							default:
							{
								try
								{
									var e = ce(xml.childNodes[i].nodeName);
								}
								catch (e)
								{
									break;
								}
								e.appendChild(parse_xml_to_html(xml.childNodes[i]));
								add_style(xml.childNodes[i], e);
								div.appendChild(e)
							}
						}
					break;
				}
			}
		}
		return div;
	}
	catch (e)
	{
		handle(e);
		return div;
	}
}
function add_style(element, html)
{
	var Class = element.getAttribute('class');

	if (Class != null)
	{
		html.className = Class;
	}

	var styles = element.getAttribute('style');

	if (styles == null)
	{
		return;
	}
	if (styles.indexOf(';') == -1)
	{
		styles += ';';
	}
	styles = styles.split(';');
	for (var j = 0; j < styles.length; j++)
	{
		var style = styles[j].split(':');

		if (style[0])
		{
			style[0] = trim(style[0]);
		}

		if (style[1])
		{
			style[1] = trim(style[1]);
		}

		switch (style[0])
		{
			case 'font-style':
				html.style.fontStyle = style[1];
			break;

			case 'font-weight':
				html.style.fontWeight = style[1];
			break;

			case 'font-size':
				try
				{
					html.style.fontSize = style[1];
				}
				catch (e)
				{}
			break;
			
			case 'font-family':
				html.style.fontFamily = style[1];
			break;
			
			case 'line-height':
				html.style.lineHeigt = style[1];
			break;

			case 'color':
				html.style.color = style[1];
			break;
			
			case 'overflow':
				html.style.overflow = style[1];
			break;

			case 'text-decoration':
				html.style.textDecoration = style[1];
			break;
			
			case 'float':
				html.style.cssFloat = style[1];
			break;
		}
	}
}

function trim(value)
{
	value = value.replace(/^\s+/,'');
	value = value.replace(/\s+$/,'');
	return value;
}

function http()
{
	try
	{
		var http_request = false;
		if (window.XMLHttpRequest)
		{
			// Mozilla, Safari,...
			http_request = new XMLHttpRequest();

			if (http_request.overrideMimeType)
			{
				http_request.overrideMimeType('text/xml');
			}
		}
		else if (window.ActiveXObject)
		{ // IE
			try
			{
				http_request = new ActiveXObject('Msxml2.XMLHTTP');
			}
			catch (e)
			{
				try
				{
					http_request = new ActiveXObject('Microsoft.XMLHTTP');
				}
				catch (e)
				{
				}
			}
		}

		if (!http_request)
		{
			throw err_msg(lang['no_ajax']);
		}
		return http_request;

	}
	catch (e)
	{
		handle(e);
		return false;
	}
}

function message(msg, color, no_reload)
{
	try
	{
		if (document.getElementById('msg_txt') != null)
		{
			document.getElementById('msg_txt').innerHTML = '';
			var tmp = ce('p');
			tmp.style.marginTop = '0.5em';tmp.style.marginBottom = '0.5em';tmp.style.textAlign = 'center';tmp.style.color = 'green';
			tmp.appendChild(tn(msg));
			if (color){tmp.style.color = 'red';}
			document.getElementById('msg_txt').appendChild(tmp);
			
		}
		else
		{
			div.innerHTML = '';
			//document.getElementById('msg_txt').style.display = 'none';
			var ul = ce('ul');ul.className = 'topiclist forums';ul.style.height = '40px';
			var li = ce('li');li.style.display = 'block';
			var dl = ce('dl');dl.style.width = '100%';
			var dt = ce('dt');dt.className = 'row';
			var tmp = ce('p');tmp.style.fontSize = '11px';
			if (color){tmp.style.color = 'red';}tmp.appendChild(tn(msg));dt.appendChild(tmp);dl.appendChild(dt);li.appendChild(dl);ul.appendChild(li);div.appendChild(ul);
		}

		// We reload everything after 3 seconds when an error happens, to prevent errors that are happening, and
		// the shoutbox dont work anymore without a reload.
		if (!no_reload)
		{
			last = 0;
			one_open = false;
			hin = http(); // Reset HTTP data.
			if (error_number <= 6)
			{
				timer_in = setTimeout('reload_post();', 3000);
			}
			else
			{
				clearTimeout(timer_in);
				return;
			}
			error_number++;
		}
	}
	catch (e)
	{
		handle(e);
		return false;
	}
}

function cp()
{
	var sep = ce('span');sep.className = 'page-sep';sep.appendChild(tn(lang['COMMA_SEPARATOR']));
	return sep;
}

function play_sound(file, sort_div)
{
	var source = forum_url+'images/shoutbox/'+file;
	var out_div = 'div_sound';
	if (sort_div == 1){on_div = '';} else if (sort_div == 2){on_div = '_error';} else if (sort_div == 3){on_div = '_del';}
	if (is_ie)
	{
		document.getElementById(out_div+on_div).innerHTML = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" height="0" width="0" type="application/x-shockwave-flash"><param name="movie" value="'+source+'"></object>';
	}
	else
	{
		document.getElementById(out_div+on_div).innerHTML = '<embed src="'+source+'" width="0" height="0" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
	}
}

function delete_message(post_id)
{
	if (hdelete.readyState == 4 || hdelete.readyState == 0)
	{
		// Lets got some nice things :D
		hdelete.open('GET',delete_url+'&id='+post_id+'&rand='+Math.floor(Math.random() * 1000000),true);

		hdelete.onreadystatechange = function()
		{
			try
			{
				if (hdelete.readyState != 4)
				{
					return;
				}
				if (hdelete.readyState == 4)
				{
					xml = hdelete.responseXML;

					if (typeof xml != 'object')
					{
						play_sound(error_sound, 2);
						throw err_msg(lang['SERVER_ERR']);
						return;
					}

					if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
					{
						err = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;message(err, true);
						return;
					}
					else
					{
						play_sound(del_sound, 3);
						message(lang['MSG_DEL_DONE']);
					}
					setTimeout("document.getElementById('msg_txt').innerHTML = ''",3000);

					last = 0;// Reset last, because if we delete the last message, the next messages cannot load correctly.
					clearTimeout(timer_in);
					reload_post();
					reload_page();
				}
			}
			catch (e)
			{
				handle(e);
				return;
			}
		}

		hdelete.send(null);
	}
}

function purge_shout(purge_url, purgeSort)
{
	if (hpurge.readyState == 4 || hpurge.readyState == 0)
	{
		// Lets got some nice things :D
		hpurge.open('GET',purge_url+'&rand='+Math.floor(Math.random() * 100000),true);

		hpurge.onreadystatechange = function()
		{
			try
			{
				if (hpurge.readyState != 4)
				{
					return;
				}
				if (hpurge.readyState == 4)
				{
					xml = hpurge.responseXML;

					if (typeof xml != 'object')
					{
						play_sound(error_sound, 2);
						throw err_msg(lang['SERVER_ERR']);
						return;
					}
					if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
					{
						err = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;message(err, true);
						return;
					}
					else
					{
						play_sound(del_sound, 3);
						message(lang['PURGE_PROCESS']);
					}
					setTimeout("document.getElementById('msg_txt').innerHTML = ''",4000);
					document.getElementById(purgeSort).style.display = 'inline';
					last = 0;
					clearTimeout(timer_in);
					reload_post();
					reload_page();
				}
			}
			catch (e)
			{
				handle(e);
				return;
			}
		}

		hpurge.send(null);
	}
}

function handle_edit(dd, inh, i)
{
	msg3 = ce('span');msg3.id = 'text' + i;msg3.style.display = 'none';
	dd.appendChild(msg3);

	edit_form = ce('form');edit_form.id = 'form' + i;edit_form.i = i;edit_form.style.display = 'none';
	edit_form.onsubmit = function(){return false}

	var input = ce('input');input.id = 'input' + i;input.i = i;input.value = input.defaultValue = inh.getElementsByTagName('msg_plain')[0].childNodes[0].nodeValue;input.style.cursor = 'text';input.style.width = '68%';input.style.fontSize = '1.0em';
	input.onkeypress = function(evt)
	{
		try
		{
			evt = (evt) ? evt : event;
			var c = (evt.wich) ? evt.wich : evt.keyCode;
			if (c == 13)
			{
				document.getElementById('submit' + this.i).click();evt.returnValue = false;
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
	edit_form.appendChild(input);

	var input = ce('input');input.type = 'button';input.id = 'submit' + i;input.className = 'button1 btnmain';input.style.marginRight = input.style.marginLeft = '5px';input.value = lang['EDIT'];input.title = lang['SHOUT_EDIT'];input.i = i;
	input.shout_id = inh.getElementsByTagName('shout_id')[0].childNodes[0].nodeValue;
	input.onclick = function()
	{
		//one_open = false;
		i = this.i;
		document.getElementById('form' + i).style.display = 'none';
		document.getElementById('text' + i).style.display = 'block';
		document.getElementById('text' + i).innerHTML = '';
		document.getElementById('text' + i).appendChild(tn(lang['SENDING_EDIT']));

		var hedit = http();
		if (hedit.readyState == 4 || hedit.readyState == 0)
		{
			hedit.open('POST', edit_url+'&last='+last+'&rand='+Math.floor(Math.random() * 300000),true);hedit.i = i;
			hedit.onreadystatechange = function()
			{
				try
				{
					if (hedit.readyState != 4){return;}
					i = hedit.i;
					one_open = false;

					document.getElementById('ddshout' + i).style.display = 'none';
					document.getElementById('text' + i).style.display = 'none';
					document.getElementById('shout' + i).style.display = 'none';
					document.getElementById('edit_button' + i).style.display = 'inline';
					if (document.getElementById('info_button' + i)){document.getElementById('info_button' + i).style.display = 'inline';}
					if (document.getElementById('delete_button' + i)){document.getElementById('delete_button' + i).style.display = 'inline';}
					if (document.getElementById("smilies")){document.getElementById('smilies').style.display = 'none';}
					if (document.getElementById("colour")){document.getElementById('colour').style.display = 'none';}
					if (document.getElementById("chars_view")){document.getElementById('chars_view').style.display = 'none';}

					setTimeout("document.getElementById('msg_txt').innerHTML = ''", 2000);
					last = 0;
					reload_post();

					try
					{
						document.getElementById("post_message").style.display = "block";
					}
					catch(e)
					{}

					var xml = hedit.responseXML;

					if (typeof xml != 'object')
					{
						play_sound(error_sound, 2);
						throw err_msg(lang['SERVER_ERR']);
						return;
					}

					if (xml.getElementsByTagName('error') && xml.getElementsByTagName('error').length != 0)
					{
						var msg = xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;
						throw err_msg(msg, true);
						return;
					}
					else
					{
						message(lang['EDIT_DONE']);
					}
				}
				catch (e)
				{
					handle(e);
					return;
				}
			}
			post = 'chat_message=';
			post += encodeURIComponent(document.getElementById('input' + i).value);
			post += '&shout_id=' + this.shout_id;
			document.getElementById('input' + i).value = '';
			hedit.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			hedit.send(post);
		}
	}
	edit_form.appendChild(input);

	var input = ce('input');input.type = 'button';input.className = 'button1 btnmain';input.value = lang['CANCEL'];input.title = lang['CANCEL'];input.i = i;
	input.onclick = function()
	{
		one_open = false;
		try
		{
			document.getElementById('post_message').style.display = 'block';
		}
		catch(e)
		{}

		i = this.i;
		document.getElementById('form' + i).style.display = 'none';
		document.getElementById('shout' + i).style.display = 'inline';
		document.getElementById('edit_button' + i).style.display = 'inline';
		document.getElementById('ddshout' + i).style.display = 'inline';
		if (document.getElementById('info_button' + i)){document.getElementById('info_button' + i).style.display = 'inline';}
		if (document.getElementById('delete_button' + i)){document.getElementById('delete_button' + i).style.display = 'inline';}
		if (document.getElementById("smilies")){document.getElementById("smilies").style.display = "none";}
		if (document.getElementById("colour")){document.getElementById("colour").style.display = "none";}
		if (document.getElementById("chars_view")){document.getElementById("chars_view").style.display = "none";}
		document.getElementById('spa' + i).innerHTML = '';  // We must erase everything on spa else it multiplies
	}

	edit_form.appendChild(input);edit_button.style.display = 'inline';edit_button.i = i;edit_button.id = 'edit_button' + i;
	delete_button.style.display = 'inline';delete_button.i = i;delete_button.id = 'delete_button' + i;
	info_button.style.display = 'inline';info_button.i = i;info_button.id = 'info_button' + i;
	dd.style.display = 'inline';dd.i = i;dd.id = 'ddshout' + i;
	edit_button.onclick = function()
	{
		if (one_open)
		{
			play_sound(error_sound, 2);
			alert(lang['ONLY_ONE_OPEN']);
			return;
		}
		one_open = true;

		try
		{
			document.getElementById("post_message").style.display = "block";
		}
		catch(e)
		{}

		i = this.i;
		document.getElementById('form' + i).style.display = 'block';
		document.getElementById('form' + i).style.paddingLeft = '5px';
		document.getElementById('shout' + i).style.display = 'none';
		document.getElementById('edit_button' + i).style.display = 'none';
		if (document.getElementById('info_button' + i)){document.getElementById('info_button' + i).style.display = 'none';}
		if (document.getElementById('delete_button' + i)){document.getElementById('delete_button' + i).style.display = 'none';}
		document.getElementById('ddshout' + i).style.display = 'none';
		document.getElementById('spa' + i).style.borderLeft = '0px';
		document.getElementById('spa' + i).appendChild(tn(lang['SHOUT_EDIT'] + ': '));
	}
	return dd;
}

/**
 * Lazyness ftw
 *
 */
function ce(e)
{
	return document.createElement(e);
}
function tn(e)
{
	return document.createTextNode(e);
}