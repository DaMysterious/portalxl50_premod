<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
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

/**
* valid external constants:
* PHPBB_MSG_HANDLER
* PHPBB_DB_NEW_LINK
* PHPBB_ROOT_PATH
* PHPBB_ADMIN_PATH
*/

// phpBB Version
define('PHPBB_VERSION', '3.0.13-PL1');

// QA-related
// define('PHPBB_QA', 1);

// User related
define('ANONYMOUS', 1);

define('USER_ACTIVATION_NONE', 0);
define('USER_ACTIVATION_SELF', 1);
define('USER_ACTIVATION_ADMIN', 2);
define('USER_ACTIVATION_DISABLE', 3);

define('AVATAR_UPLOAD', 1);
define('AVATAR_REMOTE', 2);
define('AVATAR_GALLERY', 3);

define('USER_NORMAL', 0);
define('USER_INACTIVE', 1);
define('USER_IGNORE', 2);
define('USER_FOUNDER', 3);

define('INACTIVE_REGISTER', 1);
define('INACTIVE_PROFILE', 2);
define('INACTIVE_MANUAL', 3);
define('INACTIVE_REMIND', 4);

// ACL
define('ACL_NEVER', 0);
define('ACL_YES', 1);
define('ACL_NO', -1);

// Login error codes
define('LOGIN_CONTINUE', 1);
define('LOGIN_BREAK', 2);
define('LOGIN_SUCCESS', 3);
define('LOGIN_SUCCESS_CREATE_PROFILE', 20);
define('LOGIN_ERROR_USERNAME', 10);
define('LOGIN_ERROR_PASSWORD', 11);
define('LOGIN_ERROR_ACTIVE', 12);
define('LOGIN_ERROR_ATTEMPTS', 13);
define('LOGIN_ERROR_EXTERNAL_AUTH', 14);
define('LOGIN_ERROR_PASSWORD_CONVERT', 15);

// Maximum login attempts
// The value is arbitrary, but it has to fit into the user_login_attempts field.
define('LOGIN_ATTEMPTS_MAX', 100);

// Group settings
define('GROUP_OPEN', 0);
define('GROUP_CLOSED', 1);
define('GROUP_HIDDEN', 2);
define('GROUP_SPECIAL', 3);
define('GROUP_FREE', 4);

// Forum/Topic states
define('FORUM_CAT', 0);
define('FORUM_POST', 1);
define('FORUM_LINK', 2);
define('ITEM_UNLOCKED', 0);
define('ITEM_LOCKED', 1);
define('ITEM_MOVED', 2);

// Forum Flags
define('FORUM_FLAG_LINK_TRACK', 1);
define('FORUM_FLAG_PRUNE_POLL', 2);
define('FORUM_FLAG_PRUNE_ANNOUNCE', 4);
define('FORUM_FLAG_PRUNE_STICKY', 8);
define('FORUM_FLAG_ACTIVE_TOPICS', 16);
define('FORUM_FLAG_POST_REVIEW', 32);
define('FORUM_FLAG_QUICK_REPLY', 64);

// Forum Options... sequential order. Modifications should begin at number 10 (number 29 is maximum)
define('FORUM_OPTION_FEED_NEWS', 1);
define('FORUM_OPTION_FEED_EXCLUDE', 2);

// Optional text flags
define('OPTION_FLAG_BBCODE', 1);
define('OPTION_FLAG_SMILIES', 2);
define('OPTION_FLAG_LINKS', 4);

// Topic types
define('POST_NORMAL', 0);
define('POST_STICKY', 1);
define('POST_ANNOUNCE', 2);
define('POST_GLOBAL', 3);

// Lastread types
define('TRACK_NORMAL', 0);
define('TRACK_POSTED', 1);

// Notify methods
define('NOTIFY_EMAIL', 0);
define('NOTIFY_IM', 1);
define('NOTIFY_BOTH', 2);

// Notify status
define('NOTIFY_YES', 0);
define('NOTIFY_NO', 1);

// Email Priority Settings
define('MAIL_LOW_PRIORITY', 4);
define('MAIL_NORMAL_PRIORITY', 3);
define('MAIL_HIGH_PRIORITY', 2);

// Log types
define('LOG_ADMIN', 0);
define('LOG_MOD', 1);
define('LOG_CRITICAL', 2);
define('LOG_USERS', 3);

// Private messaging - Do NOT change these values
define('PRIVMSGS_HOLD_BOX', -4);
define('PRIVMSGS_NO_BOX', -3);
define('PRIVMSGS_OUTBOX', -2);
define('PRIVMSGS_SENTBOX', -1);
define('PRIVMSGS_INBOX', 0);

// Full Folder Actions
define('FULL_FOLDER_NONE', -3);
define('FULL_FOLDER_DELETE', -2);
define('FULL_FOLDER_HOLD', -1);

// Download Modes - Attachments
define('INLINE_LINK', 1);
// This mode is only used internally to allow modders extending the attachment functionality
define('PHYSICAL_LINK', 2);

// Confirm types
define('CONFIRM_REG', 1);
define('CONFIRM_LOGIN', 2);
define('CONFIRM_POST', 3);
define('CONFIRM_REPORT', 4);

// Categories - Attachments
define('ATTACHMENT_CATEGORY_NONE', 0);
define('ATTACHMENT_CATEGORY_IMAGE', 1); // Inline Images
define('ATTACHMENT_CATEGORY_WM', 2); // Windows Media Files - Streaming
define('ATTACHMENT_CATEGORY_RM', 3); // Real Media Files - Streaming
define('ATTACHMENT_CATEGORY_THUMB', 4); // Not used within the database, only while displaying posts
define('ATTACHMENT_CATEGORY_FLASH', 5); // Flash/SWF files
define('ATTACHMENT_CATEGORY_QUICKTIME', 6); // Quicktime/Mov files

// BBCode UID length
define('BBCODE_UID_LEN', 8);

// Number of core BBCodes
define('NUM_CORE_BBCODES', 12);

// BBCode hard limit
define('BBCODE_LIMIT', 1511);

// Smiley hard limit
define('SMILEY_LIMIT', 1000);

// Magic url types
define('MAGIC_URL_EMAIL', 1);
define('MAGIC_URL_FULL', 2);
define('MAGIC_URL_LOCAL', 3);
define('MAGIC_URL_WWW', 4);

// Profile Field Types
define('FIELD_INT', 1);
define('FIELD_STRING', 2);
define('FIELD_TEXT', 3);
define('FIELD_BOOL', 4);
define('FIELD_DROPDOWN', 5);
define('FIELD_DATE', 6);

// referer validation
define('REFERER_VALIDATE_NONE', 0);
define('REFERER_VALIDATE_HOST', 1);
define('REFERER_VALIDATE_PATH', 2);

// phpbb_chmod() permissions
@define('CHMOD_ALL', 7);
@define('CHMOD_READ', 4);
@define('CHMOD_WRITE', 2);
@define('CHMOD_EXECUTE', 1);

// Captcha code length
define('CAPTCHA_MIN_CHARS', 4);
define('CAPTCHA_MAX_CHARS', 7);

// Additional constants
define('VOTE_CONVERTED', 127);

// Table names
define('ACL_GROUPS_TABLE',			$table_prefix . 'acl_groups');
define('ACL_OPTIONS_TABLE',			$table_prefix . 'acl_options');
define('ACL_ROLES_DATA_TABLE',		$table_prefix . 'acl_roles_data');
define('ACL_ROLES_TABLE',			$table_prefix . 'acl_roles');
define('ACL_USERS_TABLE',			$table_prefix . 'acl_users');
define('ATTACHMENTS_TABLE',			$table_prefix . 'attachments');
define('BANLIST_TABLE',				$table_prefix . 'banlist');
define('BBCODES_TABLE',				$table_prefix . 'bbcodes');
define('BOOKMARKS_TABLE',			$table_prefix . 'bookmarks');
define('BOTS_TABLE',				$table_prefix . 'bots');
define('CONFIG_TABLE',				$table_prefix . 'config');
define('CONFIRM_TABLE',				$table_prefix . 'confirm');
define('DISALLOW_TABLE',			$table_prefix . 'disallow');
define('DRAFTS_TABLE',				$table_prefix . 'drafts');
define('EXTENSIONS_TABLE',			$table_prefix . 'extensions');
define('EXTENSION_GROUPS_TABLE',	$table_prefix . 'extension_groups');
define('FORUMS_TABLE',				$table_prefix . 'forums');
define('FORUMS_ACCESS_TABLE',		$table_prefix . 'forums_access');
define('FORUMS_TRACK_TABLE',		$table_prefix . 'forums_track');
define('FORUMS_WATCH_TABLE',		$table_prefix . 'forums_watch');
define('GROUPS_TABLE',				$table_prefix . 'groups');
define('ICONS_TABLE',				$table_prefix . 'icons');
define('LANG_TABLE',				$table_prefix . 'lang');
define('LOG_TABLE',					$table_prefix . 'log');
define('LOGIN_ATTEMPT_TABLE',		$table_prefix . 'login_attempts');
define('MODERATOR_CACHE_TABLE',		$table_prefix . 'moderator_cache');
define('MODULES_TABLE',				$table_prefix . 'modules');
define('POLL_OPTIONS_TABLE',		$table_prefix . 'poll_options');
define('POLL_VOTES_TABLE',			$table_prefix . 'poll_votes');
define('POSTS_TABLE',				$table_prefix . 'posts');
define('PRIVMSGS_TABLE',			$table_prefix . 'privmsgs');
define('PRIVMSGS_FOLDER_TABLE',		$table_prefix . 'privmsgs_folder');
define('PRIVMSGS_RULES_TABLE',		$table_prefix . 'privmsgs_rules');
define('PRIVMSGS_TO_TABLE',			$table_prefix . 'privmsgs_to');
define('PROFILE_FIELDS_TABLE',		$table_prefix . 'profile_fields');
define('PROFILE_FIELDS_DATA_TABLE',	$table_prefix . 'profile_fields_data');
define('PROFILE_FIELDS_LANG_TABLE',	$table_prefix . 'profile_fields_lang');
define('PROFILE_LANG_TABLE',		$table_prefix . 'profile_lang');
define('RANKS_TABLE',				$table_prefix . 'ranks');
define('REPORTS_TABLE',				$table_prefix . 'reports');
define('REPORTS_REASONS_TABLE',		$table_prefix . 'reports_reasons');
define('SEARCH_RESULTS_TABLE',		$table_prefix . 'search_results');
define('SEARCH_WORDLIST_TABLE',		$table_prefix . 'search_wordlist');
define('SEARCH_WORDMATCH_TABLE',	$table_prefix . 'search_wordmatch');
define('SESSIONS_TABLE',			$table_prefix . 'sessions');
define('SESSIONS_KEYS_TABLE',		$table_prefix . 'sessions_keys');
define('SITELIST_TABLE',			$table_prefix . 'sitelist');
define('SMILIES_TABLE',				$table_prefix . 'smilies');
define('STYLES_TABLE',				$table_prefix . 'styles');
define('STYLES_TEMPLATE_TABLE',		$table_prefix . 'styles_template');
define('STYLES_TEMPLATE_DATA_TABLE',$table_prefix . 'styles_template_data');
define('STYLES_THEME_TABLE',		$table_prefix . 'styles_theme');
define('STYLES_IMAGESET_TABLE',		$table_prefix . 'styles_imageset');
define('STYLES_IMAGESET_DATA_TABLE',$table_prefix . 'styles_imageset_data');
define('TOPICS_TABLE',				$table_prefix . 'topics');
define('TOPICS_POSTED_TABLE',		$table_prefix . 'topics_posted');
define('TOPICS_TRACK_TABLE',		$table_prefix . 'topics_track');
define('TOPICS_WATCH_TABLE',		$table_prefix . 'topics_watch');
define('USER_GROUP_TABLE',			$table_prefix . 'user_group');
define('USERS_TABLE',				$table_prefix . 'users');
define('WARNINGS_TABLE',			$table_prefix . 'warnings');
define('WORDS_TABLE',				$table_prefix . 'words');
define('ZEBRA_TABLE',				$table_prefix . 'zebra');

// Additional tables

/*
*
* @package define database tables for phpBB3 Portal XL 5.0
* @copyright (c) 2007-2012 PortalXL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
define('PORTAL_CONFIG_TABLE',			$table_prefix . 'portal_config');
define('PORTAL_BLOCK_TABLE',			$table_prefix . 'portal_block');
define('PORTAL_BLOCK_INDEX_TABLE',		$table_prefix . 'portal_block_index');
define('PORTAL_MENU_TABLE',         	$table_prefix . 'portal_menu');
define('PORTAL_QUOTE_TABLE',        	$table_prefix . 'portal_quote');
define('PORTAL_PARTNERS_TABLE',     	$table_prefix . 'portal_partners');
define('PORTAL_BANNER_HO_TABLE',    	$table_prefix . 'portal_banners_ho');
define('PORTAL_BANNER_VE_TABLE',    	$table_prefix . 'portal_banners_ve');
define('PORTAL_LINKS_TABLE',    		$table_prefix . 'portal_links');
define('PORTAL_MODS_TABLE',				$table_prefix . 'portal_mods');
define('PORTAL_FORUMADDS_TABLE',		$table_prefix . 'portal_forumadds');
define('PORTAL_PAGES_TABLE', 	        $table_prefix .	'portal_pages');
define('PORTAL_NEWSFEEDS_TABLE',  		$table_prefix . 'portal_newsfeeds');
define('PORTAL_ACRONYMS_TABLE',  		$table_prefix . 'portal_acronyms');
define('PORTAL_THANKS_TABLE', 			$table_prefix . 'portal_thanks');
define('PORTAL_USER_SETTINGS_TABLE', 	$table_prefix . 'portal_user_settings');
define('PORTAL_REFERER_TABLE', 			$table_prefix . 'portal_referer');
define('PORTAL_PAGE', true);
define('PORTAL_PAGES', true);
define('PORTAL_PAGES_PAGE', true);
define('PORTAL_INDEX', true);

// phpBB log connections integration
define('LOG_LC_EXCLUDE_IP_TABLE',	$table_prefix . 'log_lc_exclude_ip');
define('LOG_CONNECTIONS', 5);
// phpBB log connections integration

// start mod view or mark unread posts
define('PRIVMSGS_UNREADBOX', -5);

// Animate Digits IP Tracking Counter
define('COUNTER_NONE', 0);
define('COUNTER_IMAGE', 1);
define('COUNTER_TEXT', 2);

// KB blocks for Portal Xl 5.0
define('KB_ARTICLE_TABLE', 			$table_prefix . 'articles');

// Download MOD 6
define('DL_AUTH_TABLE',				$table_prefix . 'dl_auth');
define('DL_CAT_TABLE',				$table_prefix . 'downloads_cat');
define('DL_REM_TRAF_TABLE',			$table_prefix . 'dl_rem_traf');
define('DL_CAT_TRAF_TABLE',			$table_prefix . 'dl_cat_traf');
define('DL_EXT_BLACKLIST',			$table_prefix . 'dl_ext_blacklist');
define('DL_RATING_TABLE',			$table_prefix . 'dl_ratings');
define('DOWNLOADS_TABLE',			$table_prefix . 'downloads');
define('DL_STATS_TABLE',			$table_prefix . 'dl_stats');
define('DL_COMMENTS_TABLE',			$table_prefix . 'dl_comments');
define('DL_BANLIST_TABLE',			$table_prefix . 'dl_banlist');
define('DL_FAVORITES_TABLE',		$table_prefix . 'dl_favorites');
define('DL_NOTRAF_TABLE',			$table_prefix . 'dl_notraf');
define('DL_HOTLINK_TABLE',			$table_prefix . 'dl_hotlink');
define('DL_BUGS_TABLE',				$table_prefix . 'dl_bug_tracker');
define('DL_BUG_HISTORY_TABLE',		$table_prefix . 'dl_bug_history');
define('DL_VERSIONS_TABLE',			$table_prefix . 'dl_versions');
define('DL_FIELDS_TABLE',			$table_prefix . 'dl_fields');
define('DL_FIELDS_DATA_TABLE',		$table_prefix . 'dl_fields_data');
define('DL_FIELDS_LANG_TABLE',		$table_prefix . 'dl_fields_lang');
define('DL_LANG_TABLE',				$table_prefix . 'dl_lang');
define('DL_IMAGES_TABLE',			$table_prefix . 'dl_images');

// phpbb Calendar Version 0.1.0
define('CALENDAR_CONFIG_TABLE',				$table_prefix . 'calendar_config');
define('CALENDAR_EVENTS_TABLE',				$table_prefix . 'calendar_events');
define('CALENDAR_EVENT_TYPES_TABLE',		$table_prefix . 'calendar_event_types');
define('CALENDAR_RSVP_TABLE',				$table_prefix . 'calendar_rsvps');
define('CALENDAR_RECURRING_EVENTS_TABLE',	$table_prefix . 'calendar_recurring_events');
define('CALENDAR_EVENTS_WATCH',				$table_prefix . 'calendar_events_watch');
define('CALENDAR_WATCH',					$table_prefix . 'calendar_watch');

// Prime Post Revisions
define('POST_REVISIONS_TABLE',			$table_prefix . 'post_revisions');

// lefty74 Announcement Centre
define('ANNOUNCEMENTS_CENTRE_TABLE',	$table_prefix . 'announcement_centre');
define('GROUPS_ONLY', 0);
define('EVERYONE', 1);
define('GUESTS_ONLY', 2);

// Imprint 0.1.6
define('IMPRESSUM_TABLE',				$table_prefix . 'impressum');

// phpBB Gallery integration
define('LOG_GALLERY', 4);
define('GALLERY_ROOT_PATH', 'gallery/');

define('GALLERY_ALBUMS_TABLE',			$table_prefix . 'gallery_albums');
define('GALLERY_ATRACK_TABLE',			$table_prefix . 'gallery_albums_track');
define('GALLERY_COMMENTS_TABLE',		$table_prefix . 'gallery_comments');
define('GALLERY_CONFIG_TABLE',			$table_prefix . 'gallery_config');
define('GALLERY_CONTESTS_TABLE',		$table_prefix . 'gallery_contests');
define('GALLERY_FAVORITES_TABLE',		$table_prefix . 'gallery_favorites');
define('GALLERY_IMAGES_TABLE',			$table_prefix . 'gallery_images');
define('GALLERY_MODSCACHE_TABLE',		$table_prefix . 'gallery_modscache');
define('GALLERY_PERMISSIONS_TABLE',		$table_prefix . 'gallery_permissions');
define('GALLERY_RATES_TABLE',			$table_prefix . 'gallery_rates');
define('GALLERY_REPORTS_TABLE',			$table_prefix . 'gallery_reports');
define('GALLERY_ROLES_TABLE',			$table_prefix . 'gallery_roles');
define('GALLERY_USERS_TABLE',			$table_prefix . 'gallery_users');
define('GALLERY_WATCH_TABLE',			$table_prefix . 'gallery_watch');

// WPM 2.2.5
define('WPM_CONFIG_ID', 1);
define('WPM_TABLE',					    $table_prefix . 'wpm');

// Country Flags Version 3.0.6
define('COUNTRY_FLAGS_TABLE',			$table_prefix . 'country_flags');

// -- Gender MOD 1.0.1
define('GENDER_F', 2); // Ladies first ;)
define('GENDER_X', 0);
define('GENDER_M', 1);

// Contact Admin version 1.0.7
define('CONTACT_CONFIG_TABLE',          $table_prefix . 'contact_config');

// User Reminder Version 1.0.5
define('ENABLED', 1);
define('AUTOMATIC', 0);
define('OVERRIDE', 1);
define('RETAIN_POSTS', 1);
define('DELETE_POSTS', 0);

// AdvancedBlockMOD 1.0.6
define('DNSBL_TABLE',					$table_prefix . 'dnsbl');
define('LOG_BLOCK', 6);
define('WEIGHT_ZERO', 0);
define('WEIGHT_ONE', 1);
define('WEIGHT_TWO', 2);
define('WEIGHT_THREE', 3);
define('WEIGHT_FOUR', 4);
define('WEIGHT_FIVE', 5);

// DM Video
define('DM_VIDEO_CATS_TABLE',			$table_prefix . 'dm_video_cat');
define('DM_VIDEO_COMMENT_TABLE',    	$table_prefix . 'dm_video_comment');
define('DM_VIDEO_CONFIG_TABLE',     	$table_prefix . 'dm_video_config');
define('DM_VIDEO_TABLE',				$table_prefix . 'dm_video');
define('DM_VIDEO_RATE_TABLE',			$table_prefix . 'dm_video_rating');

// Topic solved
define('TOPIC_SOLVED_YES', 1); // Topic starter and moderators
define('TOPIC_SOLVED_MOD', 2); // Only moderators

// DM Music Charts 1.0.2
define('DM_MUSIC_CHARTS_TABLE',			$table_prefix . 'dm_music_charts');
define('DM_MUSIC_CHARTS_CONFIG_TABLE',	$table_prefix . 'dm_music_charts_config');
define('DM_MUSIC_CHARTS_VOTERS_TABLE',	$table_prefix . 'dm_music_charts_voters');

// PayPal IPN Donation 1.1.0
define('ACCT_HIST_TABLE', 				$table_prefix . 'donation_account_hist');
define('PAYMENT_MANUAL', 0);
define('PAYMENT_RECURRING_W', 1);
define('PAYMENT_RECURRING_M', 2);
define('PAYMENT_RECURRING_Q', 3);
define('PAYMENT_RECURRING_H', 4);
define('PAYMENT_RECURRING_Y', 5);

// Start Breizh Shoutbox
define('SHOUTBOX_TABLE', 			$table_prefix . 'shoutbox');
define('SHOUTBOX_PRIV_TABLE', 		$table_prefix . 'shoutbox_priv');
if (!defined('ROBOT'))
{
	define('ROBOT', 0);
}

// Tapatalk 3.5.0
define('TAPATALK_PUSH_DATA_TABLE',	$table_prefix . 'tapatalk_push_data');
define('TAPATALK_USERS_TABLE',		$table_prefix . 'tapatalk_users');

// phpBB Arcade Start
include($phpbb_root_path . 'arcade/includes/constants.' . $phpEx);
// phpBB Arcade End

// Styles Demo MOD
define('STYLES_DEMO_TABLE', $table_prefix . 'styles_demo');

?>
