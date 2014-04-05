<?php
/**
*
* @package SEO URL phpbb gallery
* @version $Id: ht_gallery.php 37 2011-12-06 14:20:19Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB')) {
	exit;
}
/**
* ht_gallery Class
*/
class ht_gallery {
	/**
	* get_tpl
	*/
	function get_tpl() {
		global $config, $phpbb_admin_path, $phpbb_seo;
		$htaccess_tpl = '';
		$htaccess = array();
		if (class_exists('phpbb_gallery_url')) {
			$albumindex = !empty($phpbb_seo->seo_static['albumindex']) ? str_replace('.', '\\.', $phpbb_seo->seo_static['albumindex']) : '';
			$album_path = trim(phpbb_gallery_url::path('gallery'), './ ') . '/';
			$htaccess_tpl = '<b style="color:blue">#####################################################' . "\n";
			$htaccess_tpl .= '# PHPBB GALLERY REWRITE RULES' . "\n";
			$htaccess_tpl .= '# AUTHOR : dcz http://www.phpbb-seo.com/' . "\n";
			$htaccess_tpl .= '# STARTED : 2009/01/15' . "\n";
			// ALBUM INDEX
			$htaccess_tpl .= '# ALBUM INDEX</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . $albumindex . '$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'index.{PHP_EX} [QSA,L,NC]' . "\n";
			// ALBUM
			$htaccess_tpl .= '<b style="color:blue"># ALBUM</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '[a-z0-9_-]*{DELIM_ALBUM}([0-9]+){ALBUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'album.{PHP_EX}?album_id=$1&start=$3 [QSA,L,NC]' . "\n";
			// PERSONAL ALBUMS
			$htaccess_tpl .= '<b style="color:blue"># PERSONAL ALBUMS</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '{STATIC_ALBUM_USER}{ALBUM_USER_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'index.{PHP_EX}?mode=personal&start=$2 [QSA,L,NC]' . "\n";
			// PIC PAGE
			$htaccess_tpl .= '<b style="color:blue"># PIC PAGE</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '[a-z0-9_-]*({DELIM_ALBUM}([0-9]+)/)?[a-z0-9_-]*{DELIM_PIC}([0-9]+){PIC_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'image_page.{PHP_EX}?album_id=$2&image_id=$3&start=$5 [QSA,L,NC]' . "\n";
			// JGP
			$htaccess_tpl .= '<b style="color:blue"># JGP</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '[a-z0-9_-]*({DELIM_ALBUM}([0-9]+)/)?[a-z0-9_-]*{DELIM_IPIC}([0-9]+){EXT_IPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'image.{PHP_EX}?album_id=$2&image_id=$3 [QSA,L,NC]' . "\n";
			// JGP THUMBNAILS
			$htaccess_tpl .= '<b style="color:blue"># JGP THUMBNAILS</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '[a-z0-9_-]*({DELIM_ALBUM}([0-9]+)/)?[a-z0-9_-]*{DELIM_ITHUMB}([0-9]+){EXT_IPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'image.{PHP_EX}?mode=thumbnail&album_id=$2&image_id=$3 [QSA,L,NC]' . "\n";
			// JGP MEDIUM
			$htaccess_tpl .= '<b style="color:blue"># JGP MEDIUM</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '[a-z0-9_-]*({DELIM_ALBUM}([0-9]+)/)?[a-z0-9_-]*{DELIM_IMED}([0-9]+){EXT_IPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'image.{PHP_EX}?mode=medium&album_id=$2&image_id=$3 [QSA,L,NC]' . "\n";
			// GENERIC SEARCHES
			$htaccess_tpl .= '<b style="color:blue"># GENERIC SEARCHES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '{STATIC_GALLERY_SEARCH}{DELIM_GALLERY_SEARCH}?([a-z0-9_-]+)?{GALLERY_SEARCH_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'search.{PHP_EX}?search_id=$1&start=$3 [QSA,L,NC]' . "\n";
			// FEEDS
			$htaccess_tpl .= '<b style="color:blue"># FEEDS</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}' . $album_path . '{STATIC_GALLERY_FEED}$ {DEFAULT_SLASH}{PHPBB_RPATH}' . $album_path . 'feed.{PHP_EX} [QSA,L,NC]' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># END PHPBB GALLERY' . "\n";
			$htaccess_tpl .= '#####################################################</b>' . "\n\n";
			$htaccess['pos1'] = $htaccess_tpl;
		}
		return !empty($htaccess['pos1']) ? $htaccess : false;
	}
}
?>