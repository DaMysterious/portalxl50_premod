/**
*
* @package arcade
* @version $Id: arcade.js 1663 2011-09-22 12:09:30Z killbill $
* @copyright (c) 2010-2011 http://www.phpbbarcade.com
* @copyright (c) 2011 http://jatek-vilag.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

	function arcade(type, game_id, key, cat_id, mode)
	{
		var htar = http_arcade();
		var id = type;
		var params = '';

		switch (type)
		{
			case 'rating':
				params = 'g=' + game_id + '&mode=' + mode + '&c=' + cat_id + '&r=' + key;
				id = 'star_' + game_id;
			break;

			case 'fav':
				params = 'g=' + game_id + '&mode=' + mode + '&f=' + key;
				id = 'fav_' + game_id;
			break;

			case 'games_list':
				params  = 'g=1&mode=' + ((game_id == 1) ? 'quick_jump' : 'game_jump');
				id = (game_id == 1) ? 'header_loading' : 'stat_loading';
			break;

			case 'users_list':
				params  = 'g=1&mode=users_list';
				id = 'user_loading';
			break;
		}

		arcade_html(id, '');
		arcade_loading_img(type, id, mode);

		try
		{
			if (htar.readyState == 4 || htar.readyState == 0)
			{
				htar.open('POST', root_path + 'arcade/includes/ajax.' + phpex + '?rand='+Math.floor(Math.random() * 99999999), true);

				htar.onreadystatechange = function()
				{
					try
					{
						if (htar.readyState != 4)
						{
							return;
						}

						if (htar.readyState == 4)
						{
							arcade_xml = htar.responseXML;

							if (typeof arcade_xml != 'object')
							{
								arcade_error(id, 'arcade_error', arcade_lang['XML_ERROR']);
								return;
							}

							if (arcade_xml.getElementsByTagName('error') && arcade_xml.getElementsByTagName('error').length)
							{
								var error = arcade_xml.getElementsByTagName('error')[0].childNodes[0].nodeValue;

								if (error == 'Exit')
								{
									arcade_html(id, '');
									return false;
								}

								if (error == 'session_time_end')
								{
									timer_info = setTimeout('arcade_reload_page();', 5000);
									error = arcade_lang['SESSION_TIME_END'];
								}

								arcade_error(id, 'arcade_error', error);
							}
							else
							{
								switch (type)
								{
									case 'rating':
										document.getElementById(id).align = ((!mode) ? 'center' : 'left');
										document.getElementById(id).style.width = 'auto';
									break;

									case 'fav':
									break;

									case 'games_list':
										var games_list  = (arcade_xml.getElementsByTagName('gameslist').length)  ? arcade_xml.getElementsByTagName('gameslist')[0].childNodes[0].nodeValue  : '';

										if (!games_list)
										{
											arcade_error(id, 'arcade_error', arcade_lang['EMPTY_DATA']);
											return false;
										}
										else
										{
											if (arcade_xml.getElementsByTagName('listenable').length)
											{
												arcade_display(id, 'none');
												arcade_display(((game_id == 1) ? 'all_games' : 'stat_all_games'), 'block');
												arcade_html(((game_id == 1) ? 'header_quick_jump' : 'stat_game_jump'), games_list);
											}
											else
											{
												arcade_html(id, games_list);
											}

										}
									break;

									case 'users_list':
										var users_list  = (arcade_xml.getElementsByTagName('userslist').length)  ? arcade_xml.getElementsByTagName('userslist')[0].childNodes[0].nodeValue  : '';

										if (!users_list)
										{
											arcade_error(id, 'arcade_error', arcade_lang['EMPTY_DATA']);
											return false;
										}
										else
										{
											arcade_display(id, 'none');
											arcade_display('stat_all_users', 'block');
											arcade_html('stat_user_jump', users_list);
										}
									break;

									default:
										return false;
									break;
								}

								if (type == 'rating' || type == 'fav')
								{
									arcade_replace_image(type, id, mode);
								}
							}
						}
					}
					catch (e)
					{
						arcade_error_file(e, id);
						return;
					}
				}

				htar.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				htar.send(params);
			}
			else
			{
				arcade_error(id, 'arcade_error', arcade_lang['DOUBLE_REQUEST_FOUND']);
				return;
			}
		}
		catch (e)
		{
			arcade_error_file(e, id);
			return;
		}
	}

	function http_arcade()
	{
		try
		{
			var http_request = false;

			if (window.XMLHttpRequest)
			{	// Mozilla, Safari,...
				http_request = new XMLHttpRequest();

				if (http_request.overrideMimeType)
				{
					http_request.overrideMimeType('text/xml');
				}
			}
			else if (window.ActiveXObject)
			{	// IE
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
				arcade_error(id, 'arcade_error', arcade_lang['REQUEST_ERROR']);
			}

			return http_request;
		}
		catch (e)
		{
			arcade_error_file(e, id);
			return false;
		}
	}

	function arcade_error_file(e, id)
	{
		var tmp = '<span class="arcade_error">'+ arcade_lang['ERROR'] +'</span><br /><br />';

		if (e.fileName && e.lineNumber)
		{
			tmp += e.fileName + ' <b>(' + e.lineNumber + ')</b><br /><br />';
		}

		tmp += '<span class="arcade_error">' + e.message + '</span>';

		document.getElementById(id).style.width = '98%';
		document.getElementById(id).style.height = 'auto';
		arcade_error(id, 'arcade_error_file', tmp);

		timer_info = setTimeout(arcade_error_close, 5000);

		function arcade_error_close()
		{
			arcade_html(id, '');
			arcade_display(id, 'none');
			clearTimeout(timer_info);
		}
	}

	function arcade_error(id, cn, msg)
	{
		document.getElementById(id).style.width = '100%';
		document.getElementById(id).style.height = 'auto';
		document.getElementById(id).className = cn;
		arcade_html(id, msg);
	}

	function arcade_replace_image(type, id, mode)
	{
		var image = (arcade_xml.getElementsByTagName('image').length) ? arcade_xml.getElementsByTagName('image')[0].childNodes[0].nodeValue : '';
		var info  = (arcade_xml.getElementsByTagName('info').length)  ? arcade_xml.getElementsByTagName('info')[0].childNodes[0].nodeValue  : '';

		if (!image)
		{
			arcade_error(id, 'arcade_error', arcade_lang['EMPTY_DATA']);
			return false;
		}

		if (mode && type == 'fav' && document.getElementById('fav_games') != undefined)
		{
			var fav_data = (arcade_xml.getElementsByTagName('favdata').length) ? arcade_xml.getElementsByTagName('favdata')[0].childNodes[0].nodeValue : '';

			if (fav_data)
			{
				arcade_display('fav_games', 'block');
				arcade_display('no_fav_game', 'none');
				arcade_html('arcade_fav_games', fav_data);
			}
			else
			{
				arcade_display('fav_games', 'none');
				arcade_display('no_fav_game', 'block');
			}
		}

		arcade_html(id, info);
		var timer_info = '';
		timer_info.id = setTimeout(arcade_add_image, 3000);

		function arcade_add_image()
		{
			if (type != 'fav' && mode)
			{
				document.getElementById(id).style.width = '80px';
			}

			arcade_html(id, image);
			clearTimeout(timer_info.id);
		}
	}

	function arcade_refresh_page(page_url)
	{
		if (window.opener && !window.opener.closed)
		{
			window.opener.location.reload(true);
			window.opener.location.href = page_url.replace(/&amp;/g, '&');
			window.self.close();
		}
	}

	function arcade_resize_screen(w, h)
	{
		window.self.resizeTo(screen.width * w, screen.height * h);
		window.moveTo((screen.width / 2) - ((screen.width * w) / 2), (screen.height / 2) - ((screen.height * h) / 2));
	}

	function arcade_popup_game(pop_page, w, h)
	{
		var lp = (screen.width)  ? (screen.width  - w) / 2 : 0;
		var tp = (screen.height) ? (screen.height - h) / 2 : 0;
		settings = 'height=' + h + ', width=' + w + ', top=' + tp + ', left=' + lp + ', scrollbars=no, resizable=yes';

		window.open(pop_page, '_phpbbarcadegame', settings);

		return false;
	}

	function arcade_play_info_block()
	{
		if (document.getElementById('arcade_play_info_block').style.display == 'none')
		{
			arcade_display('arcade_play_info_block', 'block');
			document.getElementById('arcade_right_block_co').title = arcade_lang['INFO_BLOCK_CLOSE'];
			document.getElementById(gametop).style.width = '75%';
		}
		else
		{
			arcade_display('arcade_play_info_block', 'none');
			document.getElementById('arcade_right_block_co').title = arcade_lang['INFO_BLOCK_OPEN'];
			document.getElementById(gametop).style.width = '100%';
		}

		arcade_swf_object_resize('auto');
	}

	function arcade_swf_object()
	{
		var width  = arcade_game_width;
		var height = arcade_game_height;

		if (arcade_game_popup == 'no')
		{
			var x = document.getElementById(gametop);

			if (arcade_game_width > x.offsetWidth && x.offsetWidth > 0)
			{
				var resize = x.offsetWidth / arcade_game_width - .05
				var width  = parseInt(resize * arcade_game_width);
				var height = parseInt(resize * arcade_game_height);
			}
		}
		else
		{
			width = height = '100%';
		}

		if (arcade_game_ibprov3 == 'yes')
		{
			var flashvars	= {ibpro_gameid: arcade_gid_encoded};
		}
		else
		{
			var flashvars	= {game_sid: arcade_game_sid, g: arcade_game_id};
		}

			var params		= {menu: "false", quality: "high"};

		swfobject.embedSWF(arcade_game_swf, 'phpbb_arcade_flash_game', width, height, arcade_flash_version, arcade_flash_player_up.replace(/&amp;/g, '&'), flashvars, params);
	}

	function arcade_swf_object_resize(size)
	{
		var container = document.getElementById(gametop);
		var flash	  = document.getElementById('phpbb_arcade_flash_game');

		if (size == 'reduce')
		{
			flash.width  = .75 * flash.offsetWidth;
			flash.height = .75 * flash.offsetHeight;
		}
		else if (size == 'increase')
		{
			if (container.offsetWidth > (1.25 * flash.offsetWidth))
			{
				flash.width  = 1.25 * flash.offsetWidth;
				flash.height = 1.25 * flash.offsetHeight;
			}
		}
		else
		{
			size = container.offsetWidth / arcade_game_width - .05;
			flash.width  = size * arcade_game_width;
			flash.height = size * arcade_game_height;
		}
	}

	function arcade_add_head()
	{
		var value = ".arcade-rate{background-image:url('" + star_img + "');}.arcade-rate li a:hover{background-image:url('" + star_img + "');}.arcade-rate li.arcade-current-rate{background-image:url('" + star_img + "');}";
		var arcade_head = document.getElementsByTagName("head")[0];
		var arcade_image = arcade_textnode(value);

			arcade_style_node = arcade_element("style");
			arcade_style_node.setAttribute("type", "text/css");

			if (arcade_style_node.styleSheet)
			{
				arcade_style_node.styleSheet.cssText = arcade_image.nodeValue;
			}
			else
			{
				arcade_style_node.appendChild(arcade_image);
			}

			arcade_head.appendChild(arcade_style_node);

		return false;
	}

	function arcade_full_screen()
	{
		var flash_Game = document.getElementById('phpbb_arcade_flash_game');
		flash_Game.setAttribute('width', '0');
		flash_Game.setAttribute('height', '0');

		document.write('<html><style type="text/css">html{width: 100%; height: 100%; overflow: hidden;}body{font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif; font-size:15 px; width: 100%; height: 100%; margin: 0;padding: 0;background-color: #000;text-align:center;}#flashcontent{width: 100%; height: 100%;}</style><body><div id="' + gametop + '" style="display: none;">' + document.getElementById('flash_game_window').innerHTML + '</div><div id="arcade_refresh" style="display: block; font-weight: bold; color: #ff0000;">F5 - ' + arcade_lang['REQUEST_ERROR'] + '</div></body></html>');
		document.close();

		document.getElementById('arcade_refresh').setAttribute('style', 'display: none;');
		var flash_game = document.getElementById('phpbb_arcade_flash_game');
		document.getElementById(gametop).setAttribute('style', 'width: 100%; height: 100%; display: block;');
		flash_game.setAttribute('width', '100%');
		flash_game.setAttribute('height', '100%');
	}

	function arcade_get_width()
	{
		var x = 0;

		if (self.innerWidth)
		{
			x = self.innerWidth;
		}
		else if (document.documentElement && document.documentElement.clientHeight)
		{
			x = document.documentElement.clientWidth;
		}
		else if (document.body)
		{
			x = document.body.clientWidth;
		}

		return x;
	}

	function arcade_get_height()
	{
		var y = 0;

		if (self.innerHeight)
		{
			y = self.innerHeight;
		}
		else if (document.documentElement && document.documentElement.clientHeight)
		{
			y = document.documentElement.clientHeight;
		}
		else if (document.body)
		{
			y = document.body.clientHeight;
		}

		return y;
	}

	function arcade_fit_screen()
	{
		if (arcade_get_width() != arcade_game_width || arcade_get_height() != arcade_game_height)
		{
			var w = 0
			var h = 0;
			w = arcade_game_width - arcade_get_width() ;
			h = arcade_game_height - arcade_get_height() ;
			window.self.resizeBy(w, h);
			window.self.moveTo((screen.width / 2) - (arcade_game_width / 2), (screen.height / 2) - (arcade_game_height / 2));
		}

		return false;
	}

	function arcade_loading_img(type, id, mode)
	{
		if (mode)
		{
			document.getElementById(id).align = 'center';
			document.getElementById(id).style.width = '80px';
		}

		var load_img = arcade_element('img');

		if (type == 'games_list' || type == 'users_list')
		{
			load_img.src = loading_img2;
		}
		else
		{
			var img_px = (type == 'fav') ? '13px' : '16px';

			load_img.src = loading_img1;
			load_img.style.width  = img_px;
			load_img.style.height = img_px;
		}

			load_img.style.verticalAlign = 'middle';
			document.getElementById(id).appendChild(load_img);
	}

	function arcade_element(e)
	{
		return document.createElement(e);
	}

	function arcade_textnode(e)
	{
		return document.createTextNode(e);
	}

	function arcade_html(id, value)
	{
		document.getElementById(id).innerHTML = value;
	}

	function arcade_display(id, value)
	{
		document.getElementById(id).style.display = value;
	}

	function arcade_reload_page()
	{
		window.parent.location.reload();
	}