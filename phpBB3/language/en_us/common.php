<?php
/**
*
* common [American English]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'TRANSLATION_INFO'	=> '',
	'DIRECTION'			=> 'ltr',
	'DATE_FORMAT'		=> '|F jS, Y|',	// January 1st, 2007 (with Relative days enabled)
	'USER_LANG'			=> 'en-us',

	'1_DAY'			=> '1 day',
	'1_MONTH'		=> '1 month',
	'1_YEAR'		=> '1 year',
	'2_WEEKS'		=> '2 weeks',
	'3_MONTHS'		=> '3 months',
	'6_MONTHS'		=> '6 months',
	'7_DAYS'		=> '7 days',

	'ACCOUNT_ALREADY_ACTIVATED'		=> 'Your account has already been activated.',
	'ACCOUNT_DEACTIVATED'			=> 'Your account has been manually deactivated and is only able to be reactivated by an administrator.',
	'ACCOUNT_NOT_ACTIVATED'			=> 'Your account has not been activated yet.',
	'ACP'							=> 'Administration Control Panel',
	'ACTIVE'						=> 'active',
	'ACTIVE_ERROR'					=> 'The specified username is currently inactive. If you have problems activating your account, please contact a board administrator.',
	'ADMINISTRATOR'					=> 'Administrator',
	'ADMINISTRATORS'				=> 'Administrators',
	'AGE'							=> 'Age',
	'AIM'							=> 'AIM',
	'ALLOWED'						=> 'Allowed',
	'ALL_FILES'						=> 'All files',
	'ALL_FORUMS'					=> 'All forums',
	'ALL_MESSAGES'					=> 'All messages',
	'ALL_POSTS'						=> 'All posts',
	'ALL_TIMES'						=> 'All times are %1$s %2$s',
	'ALL_TOPICS'					=> 'All Topics',
	'AND'							=> 'And',
	'ARE_WATCHING_FORUM'			=> 'You have subscribed to be notified of new posts in this forum.',
	'ARE_WATCHING_TOPIC'			=> 'You have subscribed to be notified of new posts in this topic.',
	'ASCENDING'						=> 'Ascending',
	'ATTACHMENTS'					=> 'Attachments',
	'ATTACHED_IMAGE_NOT_IMAGE'		=> 'The image file you tried to attach is invalid.',
	'AUTHOR'						=> 'Author',
	'AUTH_NO_PROFILE_CREATED'		=> 'The creation of a user profile was unsuccessful.',
	'AVATAR_DISALLOWED_CONTENT'		=> 'The upload was rejected because the uploaded file was identified as a possible attack vector.',
	'AVATAR_DISALLOWED_EXTENSION'	=> 'This file cannot be displayed because the extension <strong>%s</strong> is not allowed.',
	'AVATAR_EMPTY_REMOTE_DATA'		=> 'The specified avatar could not be uploaded because the remote data appears to be invalid or corrupted.',
	'AVATAR_EMPTY_FILEUPLOAD'		=> 'The uploaded avatar file is empty.',
	'AVATAR_INVALID_FILENAME'		=> '%s is an invalid filename.',
	'AVATAR_NOT_UPLOADED'			=> 'Avatar could not be uploaded.',
	'AVATAR_NO_SIZE'				=> 'The width or height of the linked avatar could not be determined. Please enter them manually.',
	'AVATAR_PARTIAL_UPLOAD'			=> 'The specified file was only partially uploaded.',
	'AVATAR_PHP_SIZE_NA'			=> 'The avatar’s filesize is too large.<br />The maximum allowed filesize set in php.ini could not be determined.',
	'AVATAR_PHP_SIZE_OVERRUN'		=> 'The avatar’s filesize is too large. The maximum allowed upload size is %1$d %2$s.<br />Please note this is set in php.ini and cannot be overridden.',
	'AVATAR_REMOTE_UPLOAD_TIMEOUT'		=> 'The specified avatar could not be uploaded because the request timed out.',
	'AVATAR_URL_INVALID'			=> 'The URL you specified is invalid.',
	'AVATAR_URL_NOT_FOUND'			=> 'The file specified could not be found.',
	'AVATAR_WRONG_FILESIZE'			=> 'The avatar’s filesize must be between 0 and %1$d %2$s.',
	'AVATAR_WRONG_SIZE'				=> 'The submitted avatar is %5$d pixels wide and %6$d pixels high. Avatars must be at least %1$d pixels wide and %2$d pixels high, but no larger than %3$d pixels wide and %4$d pixels high.',

	'BACK_TO_TOP'			=> 'Top',
	'BACK_TO_PREV'			=> 'Back to previous page',
	'BAN_TRIGGERED_BY_EMAIL'=> 'A ban has been issued on your e-mail address.',
	'BAN_TRIGGERED_BY_IP'	=> 'A ban has been issued on your IP address.',
	'BAN_TRIGGERED_BY_USER'	=> 'A ban has been issued on your username.',
	'BBCODE_GUIDE'			=> 'BBCode guide',
	'BCC'					=> 'BCC',
	'BIRTHDAYS'				=> 'Birthdays',
	'BOARD_BAN_PERM'		=> 'You have been <strong>permanently</strong> banned from this board.<br /><br />Please contact the %2$sBoard Administrator%3$s for more information.',
	'BOARD_BAN_REASON'		=> 'Reason given for ban: <strong>%s</strong>',
	'BOARD_BAN_TIME'		=> 'You have been banned from this board until <strong>%1$s</strong>.<br /><br />Please contact the %2$sBoard Administrator%3$s for more information.',
	'BOARD_DISABLE'			=> 'Sorry but this board is currently unavailable.',
	'BOARD_DISABLED'		=> 'This board is currently disabled.',
	'BOARD_UNAVAILABLE'		=> 'Sorry but the board is temporarily unavailable, please try again in a few minutes.',
	'BROWSING_FORUM'		=> 'Users browsing this forum: %1$s',
	'BROWSING_FORUM_GUEST'	=> 'Users browsing this forum: %1$s and %2$d guest',
	'BROWSING_FORUM_GUESTS'	=> 'Users browsing this forum: %1$s and %2$d guests',
	'BYTES'					=> 'Bytes',

	'CANCEL'				=> 'Cancel',
	'CHANGE'				=> 'Change',
	'CHANGE_FONT_SIZE'		=> 'Change font size',
	'CHANGING_PREFERENCES'	=> 'Changing board preferences',
	'CHANGING_PROFILE'		=> 'Changing profile settings',
	'CLICK_VIEW_PRIVMSG'	=> '%sGo to your inbox%s',
	'COLLAPSE_VIEW'			=> 'Collapse view',
	'CLOSE_WINDOW'			=> 'Close window',
	'COLOUR_SWATCH'			=> 'Color swatch',
	'COMMA_SEPARATOR'		=> ', ',	// Used in pagination of ACP & prosilver, use localized comma if appropriate, eg: Ideographic or Arabic
	'CONFIRM'				=> 'Confirm',
	'CONFIRM_CODE'			=> 'Confirmation code',
	'CONFIRM_CODE_EXPLAIN'	=> 'Enter the code exactly as it appears. All letters are case insensitive.',
	'CONFIRM_CODE_WRONG'	=> 'The confirmation code you entered was incorrect.',
	'CONFIRM_OPERATION'		=> 'Are you sure you wish to carry out this operation?',
	'CONGRATULATIONS'		=> 'Congratulations to',
	'CONNECTION_FAILED'		=> 'Connection failed.',
	'CONNECTION_SUCCESS'	=> 'Connection was successful!',
	'COOKIES_DELETED'		=> 'All board cookies successfully deleted.',
	'CURRENT_TIME'			=> 'It is currently %s',

	'DAY'					=> 'Day',
	'DAYS'					=> 'Days',
	'DELETE'				=> 'Delete',
	'DELETE_ALL'			=> 'Delete all',
	'DELETE_COOKIES'		=> 'Delete all board cookies',
	'DELETE_MARKED'			=> 'Delete marked',
	'DELETE_POST'			=> 'Delete post',
	'DELIMITER'				=> 'Delimiter',
	'DESCENDING'			=> 'Descending',
	'DISABLED'				=> 'Disabled',
	'DISPLAY'				=> 'Display',
	'DISPLAY_GUESTS'		=> 'Display guests',
	'DISPLAY_MESSAGES'		=> 'Display messages from previous',
	'DISPLAY_POSTS'			=> 'Display posts from previous',
	'DISPLAY_TOPICS'		=> 'Display topics from previous',
	'DOWNLOADED'			=> 'Downloaded',
	'DOWNLOADING_FILE'		=> 'Downloading file',
	'DOWNLOAD_COUNT'		=> 'Downloaded %d time',
	'DOWNLOAD_COUNTS'		=> 'Downloaded %d times',
	'DOWNLOAD_COUNT_NONE'	=> 'Not downloaded yet',
	'VIEWED_COUNT'			=> 'Viewed %d time',
	'VIEWED_COUNTS'			=> 'Viewed %d times',
	'VIEWED_COUNT_NONE'		=> 'Not viewed yet',

	'EDIT_POST'							=> 'Edit post',
	'EMAIL'								=> 'E-mail', // Short form for EMAIL_ADDRESS
	'EMAIL_ADDRESS'						=> 'E-mail address',
	'EMAIL_INVALID_EMAIL'				=> 'The e-mail address you entered is invalid.',
	'EMAIL_SMTP_ERROR_RESPONSE'			=> 'Ran into problems sending e-mail at <strong>Line %1$s</strong>. Response: %2$s.',
	'EMPTY_SUBJECT'						=> 'You must specify a subject when posting a new topic.',
	'EMPTY_MESSAGE_SUBJECT'				=> 'You must specify a subject when composing a new message.',
	'ENABLED'							=> 'Enabled',
	'ENCLOSURE'							=> 'Enclosure',
	'ENTER_USERNAME'					=> 'Enter username',
	'ERR_CHANGING_DIRECTORY'			=> 'Unable to change directory.',
	'ERR_CONNECTING_SERVER'				=> 'Error connecting to the server.',
	'ERR_JAB_AUTH'						=> 'Could not authorize on Jabber server.',
	'ERR_JAB_CONNECT'					=> 'Could not connect to Jabber server.',
	'ERR_UNABLE_TO_LOGIN'				=> 'The specified username or password is incorrect.',
	'ERR_UNWATCHING'					=> 'An error occured while trying to unsubscribe.',
	'ERR_WATCHING'						=> 'An error occured while trying to subscribe.',
	'ERR_WRONG_PATH_TO_PHPBB'			=> 'The phpBB path specified appears to be invalid.',
	'EXPAND_VIEW'						=> 'Expand view',
	'EXTENSION'							=> 'Extension',
	'EXTENSION_DISABLED_AFTER_POSTING'	=> 'The extension <strong>%s</strong> has been deactivated and can no longer be displayed.',

	'FAQ'					=> 'FAQ',
	'FAQ_EXPLAIN'			=> 'Frequently Asked Questions',
	'FILENAME'				=> 'Filename',
	'FILESIZE'				=> 'File size',
	'FILEDATE'				=> 'File date',
	'FILE_COMMENT'			=> 'File comment',
	'FILE_NOT_FOUND'		=> 'The requested file could not be found.',
	'FIND_USERNAME'			=> 'Find a member',
	'FOLDER'				=> 'Folder',
	'FORGOT_PASS'			=> 'I forgot my password',
	'FORM_INVALID'			=> 'The submitted form was invalid. Try submitting again.',
	'FORUM'					=> 'Forum',
	'FORUMS'				=> 'Forums',
	'FORUMS_MARKED'			=> 'Forums have been marked read.',
	'FORUM_CAT'				=> 'Forum category',
	'FORUM_INDEX'			=> 'Board index',
	'FORUM_LINK'			=> 'Forum link',
	'FORUM_LOCATION'		=> 'Forum location',
	'FORUM_LOCKED'			=> 'Forum locked',
	'FORUM_RULES'			=> 'Forum rules',
	'FORUM_RULES_LINK'		=> 'Please click here to view the forum rules',
	'FROM'					=> 'from',
	'FSOCK_DISABLED'		=> 'The operation could not be completed because the <var>fsockopen</var> function has been disabled or the server being queried could not be found.',
	'FSOCK_TIMEOUT'			=> 'A timeout occurred while reading from the network stream.',

	'FTP_FSOCK_HOST'				=> 'FTP host',
	'FTP_FSOCK_HOST_EXPLAIN'		=> 'FTP server used to connect your site.',
	'FTP_FSOCK_PASSWORD'			=> 'FTP password',
	'FTP_FSOCK_PASSWORD_EXPLAIN'	=> 'Password for your FTP username.',
	'FTP_FSOCK_PORT'				=> 'FTP port',
	'FTP_FSOCK_PORT_EXPLAIN'		=> 'Port used to connect to your server.',
	'FTP_FSOCK_ROOT_PATH'			=> 'Path to phpBB',
	'FTP_FSOCK_ROOT_PATH_EXPLAIN'	=> 'Path from the root to your phpBB board.',
	'FTP_FSOCK_TIMEOUT'				=> 'FTP timeout',
	'FTP_FSOCK_TIMEOUT_EXPLAIN'		=> 'The amount of time, in seconds, that the system will wait for a reply from your server.',
	'FTP_FSOCK_USERNAME'			=> 'FTP username',
	'FTP_FSOCK_USERNAME_EXPLAIN'	=> 'Username used to connect to your server.',

	'FTP_HOST'					=> 'FTP host',
	'FTP_HOST_EXPLAIN'			=> 'FTP server used to connect your site.',
	'FTP_PASSWORD'				=> 'FTP password',
	'FTP_PASSWORD_EXPLAIN'		=> 'Password for your FTP username.',
	'FTP_PORT'					=> 'FTP port',
	'FTP_PORT_EXPLAIN'			=> 'Port used to connect to your server.',
	'FTP_ROOT_PATH'				=> 'Path to phpBB',
	'FTP_ROOT_PATH_EXPLAIN'		=> 'Path from the root to your phpBB board.',
	'FTP_TIMEOUT'				=> 'FTP timeout',
	'FTP_TIMEOUT_EXPLAIN'		=> 'The amount of time, in seconds, that the system will wait for a reply from your server.',
	'FTP_USERNAME'				=> 'FTP username',
	'FTP_USERNAME_EXPLAIN'		=> 'Username used to connect to your server.',

	'GENERAL_ERROR'				=> 'General Error',
	'GB'						=> 'GB',
	'GIB'						=> 'GiB',
	'GO'						=> 'Go',
	'GOTO_PAGE'					=> 'Go to page',
	'GROUP'						=> 'Group',
	'GROUPS'					=> 'Groups',
	'GROUP_ERR_TYPE'			=> 'Inappropriate group type specified.',
	'GROUP_ERR_USERNAME'		=> 'No group name specified.',
	'GROUP_ERR_USER_LONG'		=> 'Group names cannot exceed 60 characters. The specified group name is too long.',
	'GUEST'						=> 'Guest',
	'GUEST_USERS_ONLINE'		=> 'There are %d guest users online',
	'GUEST_USERS_TOTAL'			=> '%d guests',
	'GUEST_USERS_ZERO_ONLINE'	=> 'There are 0 guest users online',
	'GUEST_USERS_ZERO_TOTAL'	=> '0 guests',
	'GUEST_USER_ONLINE'			=> 'There is %d guest user online',
	'GUEST_USER_TOTAL'			=> '%d guest',
	'G_ADMINISTRATORS'			=> 'Administrators',
	'G_BOTS'					=> 'Bots',
	'G_GUESTS'					=> 'Guests',
	'G_REGISTERED'				=> 'Registered users',
	'G_REGISTERED_COPPA'		=> 'Registered COPPA users',
	'G_GLOBAL_MODERATORS'		=> 'Global moderators',
	'G_NEWLY_REGISTERED'		=> 'Newly registered users',

	'HIDDEN_USERS_ONLINE'			=> '%d hidden users online',
	'HIDDEN_USERS_TOTAL'			=> '%d hidden',
	'HIDDEN_USERS_TOTAL_AND'		=> '%d hidden and ',
	'HIDDEN_USERS_ZERO_ONLINE'		=> '0 hidden users online',
	'HIDDEN_USERS_ZERO_TOTAL'		=> '0 hidden',
	'HIDDEN_USERS_ZERO_TOTAL_AND'	=> '0 hidden and ',
	'HIDDEN_USER_ONLINE'			=> '%d hidden user online',
	'HIDDEN_USER_TOTAL'				=> '%d hidden',
	'HIDDEN_USER_TOTAL_AND'			=> '%d hidden and ',
	'HIDE_GUESTS'					=> 'Hide guests',
	'HIDE_ME'						=> 'Hide my online status this session',
	'HOURS'							=> 'Hours',
	'HOME'							=> 'Home',

	'ICQ'						=> 'ICQ',
	'ICQ_STATUS'				=> 'ICQ status',
	'IF'						=> 'If',
	'IMAGE'						=> 'Image',
	'IMAGE_FILETYPE_INVALID'	=> 'Image file type %d for mimetype %s not supported.',
	'IMAGE_FILETYPE_MISMATCH'	=> 'Image file type mismatch: expected extension %1$s but extension %2$s given.',
	'IN'						=> 'in',
	'INDEX'						=> 'Index page',
	'INFORMATION'				=> 'Information',
	'INTERESTS'					=> 'Interests',
	'INVALID_DIGEST_CHALLENGE'	=> 'Invalid digest challenge.',
	'INVALID_EMAIL_LOG'			=> '<strong>%s</strong> possibly an invalid e-mail address?',
	'IP'						=> 'IP',
	'IP_BLACKLISTED'			=> 'Your IP %1$s has been blocked because it is blacklisted. For details please see <a href="%2$s">%2$s</a>.',

	'JABBER'				=> 'Jabber',
	'JOINED'				=> 'Joined',
	'JUMP_PAGE'				=> 'Enter the page number you wish to go to',
	'JUMP_TO'				=> 'Jump to',
	'JUMP_TO_PAGE'			=> 'Click to jump to page…',

	'KB'					=> 'KB',
	'KIB'					=> 'KiB',

	'LAST_POST'							=> 'Last post',
	'LAST_UPDATED'						=> 'Last updated',
	'LAST_VISIT'						=> 'Last visit',
	'LDAP_NO_LDAP_EXTENSION'			=> 'LDAP extension not available.',
	'LDAP_NO_SERVER_CONNECTION'			=> 'Could not connect to LDAP server.',
	'LDAP_SEARCH_FAILED'				=> 'An error occured while searching the LDAP directory.',
	'LEGEND'							=> 'Legend',
	'LOCATION'							=> 'Location',
	'LOCK_POST'							=> 'Lock post',
	'LOCK_POST_EXPLAIN'					=> 'Prevent editing',
	'LOCK_TOPIC'						=> 'Lock topic',
	'LOGIN'								=> 'Login',
	'LOGIN_CHECK_PM'					=> 'Log in to check your private messages.',
	'LOGIN_CONFIRMATION'				=> 'Confirmation of login',
	'LOGIN_CONFIRM_EXPLAIN'				=> 'To prevent brute forcing accounts the board requires you to enter a confirmation code after a maximum amount of failed logins. The code is displayed in the image you should see below. If you are visually impaired or cannot otherwise read this code please contact the %sBoard Administrator%s.', // unused
	'LOGIN_ERROR_ATTEMPTS'				=> 'You exceeded the maximum allowed number of login attempts. In addition to your username and password you now also have to solve the CAPTCHA below.',
	'LOGIN_ERROR_EXTERNAL_AUTH_APACHE'	=> 'You have not been authenticated by Apache.',
	'LOGIN_ERROR_PASSWORD'				=> 'You have specified an incorrect password. Please check your password and try again. If you continue to have problems please contact the %sBoard Administrator%s.',
	'LOGIN_ERROR_PASSWORD_CONVERT'		=> 'It was not possible to convert your password when updating this bulletin board’s software. Please %srequest a new password%s. If you continue to have problems please contact the %sBoard Administrator%s.',
	'LOGIN_ERROR_USERNAME'				=> 'You have specified an incorrect username. Please check your username and try again. If you continue to have problems please contact the %sBoard Administrator%s.',
	'LOGIN_FORUM'						=> 'To view or post in this forum you must enter its password.',
	'LOGIN_INFO'						=> 'In order to login you must be registered. Registering takes only a few moments but gives you increased capabilities. The board administrator may also grant additional permissions to registered users. Before you register please ensure you are familiar with our terms of use and related policies. Please ensure you read any forum rules as you navigate around the board.',
	'LOGIN_VIEWFORUM'					=> 'The board requires you to be registered and logged in to view this forum.',
	'LOGIN_EXPLAIN_EDIT'				=> 'In order to edit posts in this forum you have to be registered and logged in.',
	'LOGIN_EXPLAIN_VIEWONLINE'			=> 'In order to view the online list you have to be registered and logged in.',
	'LOGOUT'							=> 'Logout',
	'LOGOUT_USER'						=> 'Logout [ %s ]',
	'LOG_ME_IN'							=> 'Log me on automatically each visit',

	'MARK'					=> 'Mark',
	'MARK_ALL'				=> 'Mark all',
	'MARK_FORUMS_READ'		=> 'Mark forums read',
	'MARK_SUBFORUMS_READ'	=> 'Mark subforums read',
	'MB'					=> 'MB',
	'MIB'					=> 'MiB',
	'MCP'					=> 'Moderator Control Panel',
	'MEMBERLIST'			=> 'Members',
	'MEMBERLIST_EXPLAIN'	=> 'View complete list of members',
	'MERGE'					=> 'Merge',
	'MERGE_POSTS'			=> 'Move posts',
	'MERGE_TOPIC'			=> 'Merge topic',
	'MESSAGE'				=> 'Message',
	'MESSAGES'				=> 'Messages',
	'MESSAGE_BODY'			=> 'Message body',
	'MINUTES'				=> 'Minutes',
	'MODERATE'				=> 'Moderate',
	'MODERATOR'				=> 'Moderator',
	'MODERATORS'			=> 'Moderators',
	'MODULE_NOT_ACCESS'		=> 'Module not accessible',
	'MODULE_NOT_FIND'		=> 'Cannot find module %s',
	'MODULE_FILE_INCORRECT_CLASS'	=> 'Module file %s does not contain correct class [%s]',
	'MONTH'					=> 'Month',
	'MOVE'					=> 'Move',
	'MSNM'					=> 'MSNM/WLM',

	'NA'						=> 'N/A',
	'NEWEST_USER'				=> 'Our newest member <strong>%s</strong>',
	'NEW_MESSAGE'				=> 'New message',
	'NEW_MESSAGES'				=> 'New messages',
	'NEW_PM'					=> '<strong>%d</strong> new message',
	'NEW_PMS'					=> '<strong>%d</strong> new messages',
	'NEW_POST'					=> 'New post',	// Not used anymore
	'NEW_POSTS'					=> 'New posts',	// Not used anymore
	'NEXT'						=> 'Next',		// Used in pagination
	'NEXT_STEP'					=> 'Next',
	'NEVER'						=> 'Never',
	'NO'						=> 'No',
	'NOT_ALLOWED_MANAGE_GROUP'	=> 'You are not allowed to manage this group.',
	'NOT_AUTHORISED'			=> 'You are not authorized to access this area.',
	'NOT_WATCHING_FORUM'		=> 'You are no longer subscribed to updates on this forum.',
	'NOT_WATCHING_TOPIC'		=> 'You are no longer subscribed to this topic.',
	'NOTIFY_ADMIN'				=> 'Please notify the board administrator or webmaster.',
	'NOTIFY_ADMIN_EMAIL'		=> 'Please notify the board administrator or webmaster: <a href="mailto:%1$s">%1$s</a>',
	'NO_ACCESS_ATTACHMENT'		=> 'You are not allowed to access this file.',
	'NO_ACTION'					=> 'No action specified.',
	'NO_ADMINISTRATORS'			=> 'There are no administrators.',
	'NO_AUTH_ADMIN'				=> 'Access to the Administration Control Panel is not allowed as you do not have administrative permissions.',
	'NO_AUTH_ADMIN_USER_DIFFER'	=> 'You are not able to re-authenticate as a different user.',
	'NO_AUTH_OPERATION'			=> 'You do not have the necessary permissions to complete this operation.',
	'NO_CONNECT_TO_SMTP_HOST'	=> 'Could not connect to smtp host : %1$s : %2$s',
	'NO_BIRTHDAYS'				=> 'No birthdays today',
	'NO_EMAIL_MESSAGE'			=> 'E-mail message was blank.',
	'NO_EMAIL_RESPONSE_CODE'	=> 'Could not get mail server response codes.',
	'NO_EMAIL_SUBJECT'			=> 'No e-mail subject specified.',
	'NO_FORUM'					=> 'The forum you selected does not exist.',
	'NO_FORUMS'					=> 'This board has no forums.',
	'NO_GROUP'					=> 'The requested usergroup does not exist.',
	'NO_GROUP_MEMBERS'			=> 'This group currently has no members.',
	'NO_IPS_DEFINED'			=> 'No IP addresses or hostnames defined',
	'NO_MEMBERS'				=> 'No members found for this search criterion.',
	'NO_MESSAGES'				=> 'No messages',
	'NO_MODE'					=> 'No mode specified.',
	'NO_MODERATORS'				=> 'There are no moderators.',
	'NO_NEW_MESSAGES'			=> 'No new messages',
	'NO_NEW_PM'					=> '<strong>0</strong> new messages',
	'NO_NEW_POSTS'				=> 'No new posts',	// Not used anymore
	'NO_ONLINE_USERS'			=> 'No registered users',
	'NO_POSTS'					=> 'No posts',
	'NO_POSTS_TIME_FRAME'		=> 'No posts exist inside this topic for the selected time frame.',
	'NO_FEED_ENABLED'			=> 'Feeds are not available on this board.',
	'NO_FEED'					=> 'The requested feed is not available.',
	'NO_STYLE_DATA'				=> 'Could not get style data',
	'NO_SUBJECT'				=> 'No subject specified',								// Used for posts having no subject defined but displayed within management pages.
	'NO_SUCH_SEARCH_MODULE'		=> 'The specified search backend doesn’t exist.',
	'NO_SUPPORTED_AUTH_METHODS'	=> 'No supported authentication methods.',
	'NO_TOPIC'					=> 'The requested topic does not exist.',
	'NO_TOPIC_FORUM'			=> 'The topic or forum no longer exists.',
	'NO_TOPICS'					=> 'There are no topics or posts in this forum.',
	'NO_TOPICS_TIME_FRAME'		=> 'No topics exist inside this forum for the selected time frame.',
	'NO_UNREAD_PM'				=> '<strong>0</strong> unread messages',
	'NO_UNREAD_POSTS'			=> 'No unread posts',
	'NO_UPLOAD_FORM_FOUND'		=> 'Upload initiated but no valid file upload form found.',
	'NO_USER'					=> 'The requested user does not exist.',
	'NO_USERS'					=> 'The requested users do not exist.',
	'NO_USER_SPECIFIED'			=> 'No username was specified.',

	// Nullar/Singular/Plural language entry. The key numbers define the number range in which a certain grammatical expression is valid.
	'NUM_POSTS_IN_QUEUE'		=> array(
		0			=> 'No posts in queue',		// 0
		1			=> '1 post in queue',		// 1
		2			=> '%d posts in queue',		// 2+
	),

	'OCCUPATION'				=> 'Occupation',
	'OFFLINE'					=> 'Offline',
	'ONLINE'					=> 'Online',
	'ONLINE_BUDDIES'			=> 'Online friends',
	'ONLINE_USERS_TOTAL'		=> 'In total there are <strong>%d</strong> users online :: ',
	'ONLINE_USERS_ZERO_TOTAL'	=> 'In total there are <strong>0</strong> users online :: ',
	'ONLINE_USER_TOTAL'			=> 'In total there is <strong>%d</strong> user online :: ',
	'OPTIONS'					=> 'Options',

	'PAGE_OF'				=> 'Page <strong>%1$d</strong> of <strong>%2$d</strong>',
	'PASSWORD'				=> 'Password',
	'PIXEL'					=> 'px',
	'PLAY_QUICKTIME_FILE'	=> 'Play Quicktime file',
	'PM'					=> 'PM',
	'PM_REPORTED'			=> 'Click to view report',
	'POSTING_MESSAGE'		=> 'Posting message in %s',
	'POSTING_PRIVATE_MESSAGE'	=> 'Composing private message',
	'POST'					=> 'Post',
	'POST_ANNOUNCEMENT'		=> 'Announce',
	'POST_STICKY'			=> 'Sticky',
	'POSTED'				=> 'Posted',
	'POSTED_IN_FORUM'		=> 'in',
	'POSTED_ON_DATE'		=> 'on',
	'POSTS'					=> 'Posts',
	'POSTS_UNAPPROVED'		=> 'At least one post in this topic has not been approved.',
	'POST_BY_AUTHOR'		=> 'by',
	'POST_BY_FOE'			=> 'This post was made by <strong>%1$s</strong> who is currently on your ignore list. %2$sDisplay this post%3$s.',
	'POST_DAY'				=> '%.2f posts per day',
	'POST_DETAILS'			=> 'Post details',
	'POST_NEW_TOPIC'		=> 'Post new topic',
	'POST_PCT'				=> '%.2f%% of all posts',
	'POST_PCT_ACTIVE'		=> '%.2f%% of user’s posts',
	'POST_PCT_ACTIVE_OWN'	=> '%.2f%% of your posts',
	'POST_REPLY'			=> 'Post a reply',
	'POST_REPORTED'			=> 'Click to view report',
	'POST_SUBJECT'			=> 'Post subject',
	'POST_TIME'				=> 'Post time',
	'POST_TOPIC'			=> 'Post a new topic',
	'POST_UNAPPROVED'		=> 'This post is waiting for approval',
	'POWERED_BY'			=> 'Powered by %s',
	'PREVIEW'				=> 'Preview',
	'PREVIOUS'				=> 'Previous',		// Used in pagination
	'PREVIOUS_STEP'			=> 'Previous',
	'PRIVACY'				=> 'Privacy policy',
	'PRIVATE_MESSAGE'		=> 'Private message',
	'PRIVATE_MESSAGES'		=> 'Private messages',
	'PRIVATE_MESSAGING'		=> 'Private messaging',
	'PROFILE'				=> 'User Control Panel',

	'RANK'						=> 'Rank',
	'READING_FORUM'				=> 'Viewing topics in %s',
	'READING_GLOBAL_ANNOUNCE'	=> 'Reading global announcement',
	'READING_LINK'				=> 'Following forum link %s',
	'READING_TOPIC'				=> 'Reading topic in %s',
	'READ_PROFILE'				=> 'Profile',
	'REASON'					=> 'Reason',
	'RECORD_ONLINE_USERS'		=> 'Most users ever online was <strong>%1$s</strong> on %2$s',
	'REDIRECT'					=> 'Redirect',
	'REDIRECTS'					=> 'Total redirects',
	'REGISTER'					=> 'Register',
	'REGISTERED_USERS'			=> 'Registered users:',
	'REG_USERS_ONLINE'			=> 'There are %d registered users and ',
	'REG_USERS_TOTAL'			=> '%d registered, ',
	'REG_USERS_TOTAL_AND'		=> '%d registered and ',
	'REG_USERS_ZERO_ONLINE'		=> 'There are 0 registered users and ',
	'REG_USERS_ZERO_TOTAL'		=> '0 registered, ',
	'REG_USERS_ZERO_TOTAL_AND'	=> '0 registered and ',
	'REG_USER_ONLINE'			=> 'There is %d registered user and ',
	'REG_USER_TOTAL'			=> '%d registered, ',
	'REG_USER_TOTAL_AND'		=> '%d registered and ',
	'REMOVE'					=> 'Remove',
	'REMOVE_INSTALL'			=> 'Please delete, move or rename the install directory before you use your board. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.',
	'REPLIES'					=> 'Replies',
	'REPLY_WITH_QUOTE'			=> 'Reply with quote',
	'REPLYING_GLOBAL_ANNOUNCE'	=> 'Replying to global announcement',
	'REPLYING_MESSAGE'			=> 'Replying to message in %s',
	'REPORT_BY'					=> 'Report by',
	'REPORT_POST'				=> 'Report this post',
	'REPORTING_POST'			=> 'Reporting post',
	'RESEND_ACTIVATION'			=> 'Resend activation e-mail',
	'RESET'						=> 'Reset',
	'RESTORE_PERMISSIONS'		=> 'Restore permissions',
	'RETURN_INDEX'				=> '%sReturn to the index page%s',
	'RETURN_FORUM'				=> '%sReturn to the forum last visited%s',
	'RETURN_PAGE'				=> '%sReturn to the previous page%s',
	'RETURN_TOPIC'				=> '%sReturn to the topic last visited%s',
	'RETURN_TO'					=> 'Return to',
	'FEED'						=> 'Feed',
	'FEED_NEWS'					=> 'News',
	'FEED_TOPICS_ACTIVE'		=> 'Active Topics',
	'FEED_TOPICS_NEW'			=> 'New Topics',
	'RULES_ATTACH_CAN'			=> 'You <strong>can</strong> post attachments in this forum',
	'RULES_ATTACH_CANNOT'		=> 'You <strong>cannot</strong> post attachments in this forum',
	'RULES_DELETE_CAN'			=> 'You <strong>can</strong> delete your posts in this forum',
	'RULES_DELETE_CANNOT'		=> 'You <strong>cannot</strong> delete your posts in this forum',
	'RULES_DOWNLOAD_CAN'		=> 'You <strong>can</strong> download attachments in this forum',
	'RULES_DOWNLOAD_CANNOT'		=> 'You <strong>cannot</strong> download attachments in this forum',
	'RULES_EDIT_CAN'			=> 'You <strong>can</strong> edit your posts in this forum',
	'RULES_EDIT_CANNOT'			=> 'You <strong>cannot</strong> edit your posts in this forum',
	'RULES_LOCK_CAN'			=> 'You <strong>can</strong> lock your topics in this forum',
	'RULES_LOCK_CANNOT'			=> 'You <strong>cannot</strong> lock your topics in this forum',
	'RULES_POST_CAN'			=> 'You <strong>can</strong> post new topics in this forum',
	'RULES_POST_CANNOT'			=> 'You <strong>cannot</strong> post new topics in this forum',
	'RULES_REPLY_CAN'			=> 'You <strong>can</strong> reply to topics in this forum',
	'RULES_REPLY_CANNOT'		=> 'You <strong>cannot</strong> reply to topics in this forum',
	'RULES_VOTE_CAN'			=> 'You <strong>can</strong> vote in polls in this forum',
	'RULES_VOTE_CANNOT'			=> 'You <strong>cannot</strong> vote in polls in this forum',

	'SEARCH'					=> 'Search',
	'SEARCH_MINI'				=> 'Search…',
	'SEARCH_ADV'				=> 'Advanced search',
	'SEARCH_ADV_EXPLAIN'		=> 'View the advanced search options',
	'SEARCH_KEYWORDS'			=> 'Search for keywords',
	'SEARCHING_FORUMS'			=> 'Searching forums',
	'SEARCH_ACTIVE_TOPICS'		=> 'View active topics',
	'SEARCH_FOR'				=> 'Search for',
	'SEARCH_FORUM'				=> 'Search this forum…',
	'SEARCH_NEW'				=> 'View new posts',
	'SEARCH_POSTS_BY'			=> 'Search posts by',
	'SEARCH_SELF'				=> 'View your posts',
	'SEARCH_TOPIC'				=> 'Search this topic…',
	'SEARCH_UNANSWERED'			=> 'View unanswered posts',
	'SEARCH_UNREAD'				=> 'View unread posts',
	'SEARCH_USER_POSTS'			=> 'Search user’s posts',
	'SECONDS'					=> 'Seconds',
	'SELECT'					=> 'Select',
	'SELECT_ALL_CODE'			=> 'Select all',
	'SELECT_DESTINATION_FORUM'	=> 'Please select a destination forum',
	'SELECT_FORUM'				=> 'Select a forum',
	'SEND_EMAIL'				=> 'E-mail',				// Used for submit buttons
	'SEND_EMAIL_USER'			=> 'E-mail',				// Used as: {L_SEND_EMAIL_USER} {USERNAME} -> E-mail UserX
	'SEND_PRIVATE_MESSAGE'		=> 'Send private message',
	'SETTINGS'					=> 'Settings',
	'SIGNATURE'					=> 'Signature',
	'SKIP'						=> 'Skip to content',
	'SMTP_NO_AUTH_SUPPORT'		=> 'SMTP server does not support authentication.',
	'SORRY_AUTH_READ'			=> 'You are not authorized to read this forum.',
	'SORRY_AUTH_VIEW_ATTACH'	=> 'You are not authorized to download this attachment.',
	'SORT_BY'					=> 'Sort by',
	'SORT_JOINED'				=> 'Joined date',
	'SORT_LOCATION'				=> 'Location',
	'SORT_RANK'					=> 'Rank',
	'SORT_POSTS'				=> 'Posts',
	'SORT_TOPIC_TITLE'			=> 'Topic title',
	'SORT_USERNAME'				=> 'Username',
	'SPLIT_TOPIC'				=> 'Split topic',
	'SQL_ERROR_OCCURRED'		=> 'An SQL error occurred while fetching this page. Please contact the %sBoard Administrator%s if this problem persists.',
	'STATISTICS'				=> 'Statistics',
	'START_WATCHING_FORUM'		=> 'Subscribe forum',
	'START_WATCHING_TOPIC'		=> 'Subscribe topic',
	'STOP_WATCHING_FORUM'		=> 'Unsubscribe forum',
	'STOP_WATCHING_TOPIC'		=> 'Unsubscribe topic',
	'SUBFORUM'					=> 'Subforum',
	'SUBFORUMS'					=> 'Subforums',
	'SUBJECT'					=> 'Subject',
	'SUBMIT'					=> 'Submit',

	'TB'				=> 'TB',
	'TERMS_USE'			=> 'Terms of use',
	'TEST_CONNECTION'	=> 'Test connection',
	'THE_TEAM'			=> 'The team',
	'TIB'				=> 'TiB',
	'TIME'				=> 'Time',
	
	'TOO_LARGE'						=> 'The value you entered is too large.',
	'TOO_LARGE_MAX_RECIPIENTS'		=> 'The value of <strong>Maximum number of allowed recipients per private message</strong> setting you entered is too large.',

	'TOO_LONG'						=> 'The value you entered is too long.',

	'TOO_LONG_AIM'					=> 'The screenname you entered is too long.',
	'TOO_LONG_CONFIRM_CODE'			=> 'The confirm code you entered is too long.',
	'TOO_LONG_DATEFORMAT'			=> 'The date format you entered is too long.',
	'TOO_LONG_ICQ'					=> 'The ICQ number you entered is too long.',
	'TOO_LONG_INTERESTS'			=> 'The interests you entered is too long.',
	'TOO_LONG_JABBER'				=> 'The Jabber account name you entered is too long.',
	'TOO_LONG_LOCATION'				=> 'The location you entered is too long.',
	'TOO_LONG_MSN'					=> 'The MSNM/WLM name you entered is too long.',
	'TOO_LONG_NEW_PASSWORD'			=> 'The password you entered is too long.',
	'TOO_LONG_OCCUPATION'			=> 'The occupation you entered is too long.',
	'TOO_LONG_PASSWORD_CONFIRM'		=> 'The password confirmation you entered is too long.',
	'TOO_LONG_USER_PASSWORD'		=> 'The password you entered is too long.',
	'TOO_LONG_USERNAME'				=> 'The username you entered is too long.',
	'TOO_LONG_EMAIL'				=> 'The e-mail address you entered is too long.',
	'TOO_LONG_EMAIL_CONFIRM'		=> 'The e-mail address confirmation you entered is too long.',
	'TOO_LONG_WEBSITE'				=> 'The website address you entered is too long.',
	'TOO_LONG_YIM'					=> 'The Yahoo! Messenger name you entered is too long.',

	'TOO_MANY_VOTE_OPTIONS'			=> 'You have tried to vote for too many options.',

	'TOO_SHORT'						=> 'The value you entered is too short.',

	'TOO_SHORT_AIM'					=> 'The screenname you entered is too short.',
	'TOO_SHORT_CONFIRM_CODE'		=> 'The confirm code you entered is too short.',
	'TOO_SHORT_DATEFORMAT'			=> 'The date format you entered is too short.',
	'TOO_SHORT_ICQ'					=> 'The ICQ number you entered is too short.',
	'TOO_SHORT_INTERESTS'			=> 'The interests you entered is too short.',
	'TOO_SHORT_JABBER'				=> 'The Jabber account name you entered is too short.',
	'TOO_SHORT_LOCATION'			=> 'The location you entered is too short.',
	'TOO_SHORT_MSN'					=> 'The MSNM/WLM name you entered is too short.',
	'TOO_SHORT_NEW_PASSWORD'		=> 'The password you entered is too short.',
	'TOO_SHORT_OCCUPATION'			=> 'The occupation you entered is too short.',
	'TOO_SHORT_PASSWORD_CONFIRM'	=> 'The password confirmation you entered is too short.',
	'TOO_SHORT_USER_PASSWORD'		=> 'The password you entered is too short.',
	'TOO_SHORT_USERNAME'			=> 'The username you entered is too short.',
	'TOO_SHORT_EMAIL'				=> 'The e-mail address you entered is too short.',
	'TOO_SHORT_EMAIL_CONFIRM'		=> 'The e-mail address confirmation you entered is too short.',
	'TOO_SHORT_WEBSITE'				=> 'The website address you entered is too short.',
	'TOO_SHORT_YIM'					=> 'The Yahoo! Messenger name you entered is too short.',
	
	'TOO_SMALL'						=> 'The value you entered is too small.',
	'TOO_SMALL_MAX_RECIPIENTS'		=> 'The value of <strong>Maximum number of allowed recipients per private message</strong> setting you entered is too small.',

	'TOPIC'				=> 'Topic',
	'TOPICS'			=> 'Topics',
	'TOPICS_UNAPPROVED'	=> 'At least one topic in this forum has not been approved.',
	'TOPIC_ICON'		=> 'Topic icon',
	'TOPIC_LOCKED'		=> 'This topic is locked, you cannot edit posts or make further replies.',
	'TOPIC_LOCKED_SHORT'=> 'Topic locked',
	'TOPIC_MOVED'		=> 'Moved topic',
	'TOPIC_REVIEW'		=> 'Topic review',
	'TOPIC_TITLE'		=> 'Topic title',
	'TOPIC_UNAPPROVED'	=> 'This topic has not been approved',
	'TOTAL_ATTACHMENTS'	=> 'Attachment(s)',
	'TOTAL_LOG'			=> '1 log',
	'TOTAL_LOGS'		=> '%d logs',
	'TOTAL_NO_PM'		=> '0 private messages in total',
	'TOTAL_PM'			=> '1 private message in total',
	'TOTAL_PMS'			=> '%d private messages in total',
	'TOTAL_POSTS'		=> 'Total posts',
	'TOTAL_POSTS_OTHER'	=> 'Total posts <strong>%d</strong>',
	'TOTAL_POSTS_ZERO'	=> 'Total posts <strong>0</strong>',
	'TOPIC_REPORTED'	=> 'This topic has been reported',
	'TOTAL_TOPICS_OTHER'=> 'Total topics <strong>%d</strong>',
	'TOTAL_TOPICS_ZERO'	=> 'Total topics <strong>0</strong>',
	'TOTAL_USERS_OTHER'	=> 'Total members <strong>%d</strong>',
	'TOTAL_USERS_ZERO'	=> 'Total members <strong>0</strong>',
	'TRACKED_PHP_ERROR'	=> 'Tracked PHP errors: %s',

	'UNABLE_GET_IMAGE_SIZE'	=> 'It was not possible to determine the dimensions of the image. Please verify that the URL you entered is correct.',
	'UNABLE_TO_DELIVER_FILE'=> 'Unable to deliver file.',
	'UNKNOWN_BROWSER'		=> 'Unknown browser',
	'UNMARK_ALL'			=> 'Unmark all',
	'UNREAD_MESSAGES'		=> 'Unread messages',
	'UNREAD_PM'				=> '<strong>%d</strong> unread message',
	'UNREAD_PMS'			=> '<strong>%d</strong> unread messages',
	'UNREAD_POST'			=> 'Unread post',
	'UNREAD_POSTS'			=> 'Unread posts',
	'UNWATCH_FORUM_CONFIRM'		=> 'Are you sure you wish to unsubscribe from this forum?',
	'UNWATCH_FORUM_DETAILED'	=> 'Are you sure you wish to unsubscribe from the forum “%s”?',
	'UNWATCH_TOPIC_CONFIRM'		=> 'Are you sure you wish to unsubscribe from this topic?',
	'UNWATCH_TOPIC_DETAILED'	=> 'Are you sure you wish to unsubscribe from the topic “%s”?',
	'UNWATCHED_FORUMS'			=> 'You are no longer subscribed to the selected forums.',
	'UNWATCHED_TOPICS'			=> 'You are no longer subscribed to the selected topics.',
	'UNWATCHED_FORUMS_TOPICS'	=> 'You are no longer subscribed to the selected entries.',
	'UPDATE'				=> 'Update',
	'UPLOAD_IN_PROGRESS'	=> 'The upload is currently in progress.',
	'URL_REDIRECT'			=> 'If your browser does not support meta redirection %splease click HERE to be redirected%s.',
	'USERGROUPS'			=> 'Groups',
	'USERNAME'				=> 'Username',
	'USERNAMES'				=> 'Usernames',
	'USER_AVATAR'			=> 'User avatar',
	'USER_CANNOT_READ'		=> 'You cannot read posts in this forum.',
	'USER_POST'				=> '%d Post',
	'USER_POSTS'			=> '%d Posts',
	'USERS'					=> 'Users',
	'USE_PERMISSIONS'		=> 'Test out user’s permissions',

	'USER_NEW_PERMISSION_DISALLOWED'	=> 'We are sorry, but you are not authorized to use this feature. You may have just registered here and may need to participate more to be able to use this feature.',

	'VARIANT_DATE_SEPARATOR'	=> ' / ',	// Used in date format dropdown, eg: "Today, 13:37 / 01 Jan 2007, 13:37" ... to join a relative date with calendar date
	'VIEWED'					=> 'Viewed',
	'VIEWING_FAQ'				=> 'Viewing FAQ',
	'VIEWING_MEMBERS'			=> 'Viewing member details',
	'VIEWING_ONLINE'			=> 'Viewing who is online',
	'VIEWING_MCP'				=> 'Viewing moderator control panel',
	'VIEWING_MEMBER_PROFILE'	=> 'Viewing member profile',
	'VIEWING_PRIVATE_MESSAGES'	=> 'Viewing private messages',
	'VIEWING_REGISTER'			=> 'Registering account',
	'VIEWING_UCP'				=> 'Viewing user control panel',
	'VIEWS'						=> 'Views',
	'VIEW_BOOKMARKS'			=> 'View bookmarks',
	'VIEW_FORUM_LOGS'			=> 'View Logs',
	'VIEW_LATEST_POST'			=> 'View the latest post',
	'VIEW_NEWEST_POST'			=> 'View first unread post',
	'VIEW_NOTES'				=> 'View user notes',
	'VIEW_ONLINE_TIME'			=> 'based on users active over the past %d minute',
	'VIEW_ONLINE_TIMES'			=> 'based on users active over the past %d minutes',
	'VIEW_TOPIC'				=> 'View topic',
	'VIEW_TOPIC_ANNOUNCEMENT'	=> 'Announcement: ',
	'VIEW_TOPIC_GLOBAL'			=> 'Global Announcement: ',
	'VIEW_TOPIC_LOCKED'			=> 'Locked: ',
	'VIEW_TOPIC_LOGS'			=> 'View logs',
	'VIEW_TOPIC_MOVED'			=> 'Moved: ',
	'VIEW_TOPIC_POLL'			=> 'Poll: ',
	'VIEW_TOPIC_STICKY'			=> 'Sticky: ',
	'VISIT_WEBSITE'				=> 'Visit website',

	'WARNINGS'			=> 'Warnings',
	'WARN_USER'			=> 'Warn user',
	'WATCH_FORUM_CONFIRM'	=> 'Are you sure you wish to subscribe to this forum?',
	'WATCH_FORUM_DETAILED'	=> 'Are you sure you wish to subscribe to the forum “%s”?',
	'WATCH_TOPIC_CONFIRM'	=> 'Are you sure you wish to subscribe to this topic?',
	'WATCH_TOPIC_DETAILED'	=> 'Are you sure you wish to subscribe to the topic “%s”?',
	'WELCOME_SUBJECT'	=> 'Welcome to %s forums',
	'WEBSITE'			=> 'Website',
	'WHOIS'				=> 'Whois',
	'WHO_IS_ONLINE'		=> 'Who is online',
	'WRONG_PASSWORD'	=> 'You entered an incorrect password.',

	'WRONG_DATA_COLOUR'			=> 'The colour value you entered is invalid.',
	'WRONG_DATA_ICQ'			=> 'The number you entered is not a valid ICQ number.',
	'WRONG_DATA_JABBER'			=> 'The name you entered is not a valid Jabber account name.',
	'WRONG_DATA_LANG'			=> 'The language you specified is not valid.',
	'WRONG_DATA_WEBSITE'		=> 'The website address has to be a valid URL, including the protocol. For example http://www.example.com/.',
	'WROTE'						=> 'wrote',

	'YEAR'				=> 'Year',
	'YEAR_MONTH_DAY'	=> '(YYYY-MM-DD)',
	'YES'				=> 'Yes',
	'YIM'				=> 'YIM',
	'YOU_LAST_VISIT'	=> 'Last visit was: %s',
	'YOU_NEW_PM'		=> 'A new private message is waiting for you in your Inbox.',
	'YOU_NEW_PMS'		=> 'New private messages are waiting for you in your Inbox.',
	'YOU_NO_NEW_PM'		=> 'No new private messages are waiting for you.',

	'datetime'			=> array(
		'TODAY'		=> 'Today',
		'TOMORROW'	=> 'Tomorrow',
		'YESTERDAY'	=> 'Yesterday',
		'AGO'		=> array(
			0		=> 'less than a minute ago',
			1		=> '%d minute ago',
			2		=> '%d minutes ago',
			60		=> '1 hour ago',
		),

		'Sunday'	=> 'Sunday',
		'Monday'	=> 'Monday',
		'Tuesday'	=> 'Tuesday',
		'Wednesday'	=> 'Wednesday',
		'Thursday'	=> 'Thursday',
		'Friday'	=> 'Friday',
		'Saturday'	=> 'Saturday',

		'Sun'		=> 'Sun',
		'Mon'		=> 'Mon',
		'Tue'		=> 'Tue',
		'Wed'		=> 'Wed',
		'Thu'		=> 'Thu',
		'Fri'		=> 'Fri',
		'Sat'		=> 'Sat',

		'January'	=> 'January',
		'February'	=> 'February',
		'March'		=> 'March',
		'April'		=> 'April',
		'May'		=> 'May',
		'June'		=> 'June',
		'July'		=> 'July',
		'August'	=> 'August',
		'September' => 'September',
		'October'	=> 'October',
		'November'	=> 'November',
		'December'	=> 'December',

		'Jan'		=> 'Jan',
		'Feb'		=> 'Feb',
		'Mar'		=> 'Mar',
		'Apr'		=> 'Apr',
		'May_short'	=> 'May',	// Short representation of "May". May_short used because in English the short and long date are the same for May.
		'Jun'		=> 'Jun',
		'Jul'		=> 'Jul',
		'Aug'		=> 'Aug',
		'Sep'		=> 'Sep',
		'Oct'		=> 'Oct',
		'Nov'		=> 'Nov',
		'Dec'		=> 'Dec',
	),

	'tz'				=> array(
		'-12'	=> 'UTC - 12 hours',
		'-11'	=> 'UTC - 11 hours',
		'-10'	=> 'UTC - 10 hours',
		'-9.5'	=> 'UTC - 9:30 hours',
		'-9'	=> 'UTC - 9 hours',
		'-8'	=> 'UTC - 8 hours',
		'-7'	=> 'UTC - 7 hours',
		'-6'	=> 'UTC - 6 hours',
		'-5'	=> 'UTC - 5 hours',
		'-4.5'	=> 'UTC - 4:30 hours',
		'-4'	=> 'UTC - 4 hours',
		'-3.5'	=> 'UTC - 3:30 hours',
		'-3'	=> 'UTC - 3 hours',
		'-2'	=> 'UTC - 2 hours',
		'-1'	=> 'UTC - 1 hour',
		'0'		=> 'UTC',
		'1'		=> 'UTC + 1 hour',
		'2'		=> 'UTC + 2 hours',
		'3'		=> 'UTC + 3 hours',
		'3.5'	=> 'UTC + 3:30 hours',
		'4'		=> 'UTC + 4 hours',
		'4.5'	=> 'UTC + 4:30 hours',
		'5'		=> 'UTC + 5 hours',
		'5.5'	=> 'UTC + 5:30 hours',
		'5.75'	=> 'UTC + 5:45 hours',
		'6'		=> 'UTC + 6 hours',
		'6.5'	=> 'UTC + 6:30 hours',
		'7'		=> 'UTC + 7 hours',
		'8'		=> 'UTC + 8 hours',
		'8.75'	=> 'UTC + 8:45 hours',
		'9'		=> 'UTC + 9 hours',
		'9.5'	=> 'UTC + 9:30 hours',
		'10'	=> 'UTC + 10 hours',
		'10.5'	=> 'UTC + 10:30 hours',
		'11'	=> 'UTC + 11 hours',
		'11.5'	=> 'UTC + 11:30 hours',
		'12'	=> 'UTC + 12 hours',
		'12.75'	=> 'UTC + 12:45 hours',
		'13'	=> 'UTC + 13 hours',
		'14'	=> 'UTC + 14 hours',
		'dst'	=> '[ <abbr title="Daylight Saving Time">DST</abbr> ]',
	),

	'tz_zones'	=> array(
// AdvancedBlockMOD 1.0.6						
		'-19'	=> '[UTC - 199] Pluto Time',
// AdvancedBlockMOD 1.0.6						
		'-12'	=> '[UTC - 12] Baker Island Time',
		'-11'	=> '[UTC - 11] Niue Time, Samoa Standard Time',
		'-10'	=> '[UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time',
		'-9.5'	=> '[UTC - 9:30] Marquesas Islands Time',
		'-9'	=> '[UTC - 9] Alaska Standard Time, Gambier Island Time',
		'-8'	=> '[UTC - 8] Pacific Standard Time',
		'-7'	=> '[UTC - 7] Mountain Standard Time',
		'-6'	=> '[UTC - 6] Central Standard Time',
		'-5'	=> '[UTC - 5] Eastern Standard Time',
		'-4.5'	=> '[UTC - 4:30] Venezuelan Standard Time',
		'-4'	=> '[UTC - 4] Atlantic Standard Time',
		'-3.5'	=> '[UTC - 3:30] Newfoundland Standard Time',
		'-3'	=> '[UTC - 3] Amazon Standard Time, Central Greenland Time',
		'-2'	=> '[UTC - 2] Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time',
		'-1'	=> '[UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time',
		'0'		=> '[UTC] Western European Time, Greenwich Mean Time',
		'1'		=> '[UTC + 1] Central European Time, West African Time',
		'2'		=> '[UTC + 2] Eastern European Time, Central African Time',
		'3'		=> '[UTC + 3] Moscow Standard Time, Eastern African Time',
		'3.5'	=> '[UTC + 3:30] Iran Standard Time',
		'4'		=> '[UTC + 4] Gulf Standard Time, Samara Standard Time',
		'4.5'	=> '[UTC + 4:30] Afghanistan Time',
		'5'		=> '[UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time',
		'5.5'	=> '[UTC + 5:30] Indian Standard Time, Sri Lanka Time',
		'5.75'	=> '[UTC + 5:45] Nepal Time',
		'6'		=> '[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time',
		'6.5'	=> '[UTC + 6:30] Cocos Islands Time, Myanmar Time',
		'7'		=> '[UTC + 7] Indochina Time, Krasnoyarsk Standard Time',
		'8'		=> '[UTC + 8] Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time',
		'8.75'	=> '[UTC + 8:45] Southeastern Western Australia Standard Time',
		'9'		=> '[UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time',
		'9.5'	=> '[UTC + 9:30] Australian Central Standard Time',
		'10'	=> '[UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time',
		'10.5'	=> '[UTC + 10:30] Lord Howe Standard Time',
		'11'	=> '[UTC + 11] Solomon Island Time, Magadan Standard Time',
		'11.5'	=> '[UTC + 11:30] Norfolk Island Time',
		'12'	=> '[UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time',
		'12.75'	=> '[UTC + 12:45] Chatham Islands Time',
		'13'	=> '[UTC + 13] Tonga Time, Phoenix Islands Time',
		'14'	=> '[UTC + 14] Line Island Time',
// AdvancedBlockMOD 1.0.6						
		'19'	=> '[UTC + 199] Mercury Time',
// AdvancedBlockMOD 1.0.6						
	),

	// The value is only an example and will get replaced by the current time on view
	'dateformats'	=> array(
		'd M Y, H:i'			=> '01 Jan 2007, 13:37',
		'd M Y H:i'				=> '01 Jan 2007 13:37',
		'M jS, \'y, H:i'		=> 'Jan 1st, \'07, 13:37',
		'D M d, Y g:i a'		=> 'Mon Jan 01, 2007 1:37 pm',
		'F jS, Y, g:i a'		=> 'January 1st, 2007, 1:37 pm',
		'|d M Y|, H:i'			=> 'Today, 13:37 / 01 Jan 2007, 13:37',
		'|F jS, Y|, g:i a'		=> 'Today, 1:37 pm / January 1st, 2007, 1:37 pm'
	),

	// The default dateformat which will be used on new installs in this language
	// Translators should change this if a the usual date format is different
	'default_dateformat'	=> 'F jS, Y, g:i a', // January 1st, 2007, 1:37 pm

));

/*
* Portal XL related language definitions
*/

// Portal
$lang = array_merge($lang, array(
    'PORTAL_MODS'			=> 'Mods Database',	
));

// [img] Resize Width Images
$lang = array_merge($lang, array(
	'IMG_CLICK_HERE'	=> 'Click here to view full size of this image!',
));

// Event Calendar
$lang = array_merge($lang, array(
	'CALENDAR'		=> 'Calendar',
	// minical short day names	
	'mini_datetime'	=> array(
		  'Su'		=> 'Su',
		  'Mo'		=> 'Mo',
		  'Tu'		=> 'Tu',
		  'We'		=> 'We',
		  'Th'		=> 'Th',
		  'Fr'		=> 'Fr',
		  'Sa'		=> 'Sa',
	),
));

// Animate Digits IP Tracking Counter
$lang = array_merge($lang, array(
	'COUNTER' 			=> 'Visit counter',
	'COUNTER_STARTDATE' => 'Counted from %s',
	'HITS_PER_DAY'		=> 'Hits per day',
	'HITS_PER_HOUR'		=> 'Hits per hour',
	'HITS_PER_MONTH'	=> 'Hits per month',
	'HITS_PER_USER'		=> 'Hits per user',
	'HITS_PER_WEEK'		=> 'Hits per week',
	'HITS_PER_YEAR'		=> 'Hits per year',
	'IP_TRACKING_NO' 	=> '[No IP Tracking]',
	'IP_TRACKING_YES' 	=> '[IP Tracking]',
));

// Knowledge Base
$lang = array_merge($lang, array(
	'KNOWLEDGE_BASE'	=> 'Knowledge Base',
	'KBASE'				=> 'Knowledge Base',
));

// Anti Bot Question
$lang = array_merge($lang, array(
	'AB_QUESTION_EXPLAIN'	=> 'For protection against spam, Answer the above question.',
));

// start Thank Post MOD
$lang = array_merge($lang, array(
	'REMOVE_THANKS'			=> 'Remove your thanks for ',
	'THANK_POST1'			=> 'Thank ',
	'THANK_POST2'			=> '\'s post',
	'THANK_TEXT_1'			=> 'The following',
	'THANK_TEXT_2'			=> 'user would like to thank',
	'THANK_TEXT_2pl'		=> 'users would like to thank',
	'THANK_GENDER_F'		=> 'for her post',
	'THANK_GENDER_M'		=> 'for his post',
	'THANK_GENDER_U'		=> 'for his or her post',
	'RECEIVED'				=> 'Received',
	'THANKS'				=> 'thanks',
	'GIVEN'					=> 'Given',
	'TP_UPDATED'			=> 'Your Thank Post MOD installation has been updated!',
));
// end Thank Post MOD

// Posts per day
$lang = array_merge($lang, array(
	'POSTS_PER_DAY_OTHER'	=> 'Posts per day <strong>%.2f</strong>',
	'POSTS_PER_DAY_ZERO'	=> 'Posts per day <strong>None</strong>',
));

// Announcements Centre
$lang = array_merge($lang, array(
	'ANNOUNCEMENT_TITLE_GUESTS'		=> 'Guest Announcements local',
	'ANNOUNCEMENT_TITLE'			=> 'Site Announcements local',
));

// Portal FAQ
$lang = array_merge($lang, array(
	'FAQ_PORTAL'				=> 'Portal FAQ',
	'FAQ_PORTAL_EXPLAIN'		=> 'Portal Frequently Asked Questions',
));

// Rules page
$lang = array_merge($lang, array(
	'RULES_PAGE'				=> 'Board Rules',
	'RULES'						=> 'Rules',
));

// Similar Topics 1.0.0
$lang = array_merge($lang, array(
	'SIMILAR_TOPICS'			=> 'Similar topics',
));

/*
 * Welcome PM on First Login (WPM)
 * By DualFusion
 */
$lang = array_merge($lang, array(
	'ACP_WELCOME_PM'		=> 'Welcome PM on First Login',
	'LOG_CONFIG_WELCOME_PM'	=> '<strong>Altered Welcome PM settings</strong>',
));
/* End WPM */

//-- mod : Contact board administration ------------------------------------------------------------
//-- add
$lang = array_merge($lang, array(
	'CONTACT_BOARD_ADMIN'		=> 'Contact board administration',
	'CONTACT_BOARD_ADMIN_SHORT'	=> 'Contact',
));
//-- fin mod : Contact board administration --------------------------------------------------------

// start mod view or mark unread posts
$lang = array_merge($lang, array(
	'LOGIN_EXPLAIN_VIEWUNREADS'	=> 'You must be logged in to view your unread post list',
	'MARK_PM_UNREAD'			=> 'Mark pm as unread',
	'MARK_POST_UNREAD'			=> 'Mark post as unread',
	'NO_UNREADS'				=> 'You have no unread posts',
	'PM_MARKED_UNREAD'			=> 'Private message marked as unread',
	'POST_MARKED_UNREAD'		=> 'Post marked as unread',
	'RETURN_INBOX'				=> 'Return to pm inbox',
	'VIEW_UNREAD_PMS'			=> 'View unread pms',
	'VIEW_UNREADS'				=> 'View unread posts',
));
// end mod view or mark unread posts

// Character Countdown
$lang = array_merge($lang, array(
	'CHARACTERS_COUNT_DOWN'			=> 'Characters typed:',
));

// www.phpBB-SEO.com SEO TOOLKIT BEGIN - TITLE
$lang['Page'] = 'Page ';
// www.phpBB-SEO.com SEO TOOLKIT END - TITLE
// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> GYM Sitemaps
$lang = array_merge($lang, array(
	'GYM_LINKS' => 'Links',
	'GYM_LINK' => 'Link',
	'GYM_RSS_SLIDE_START' => 'Start scroller',
	'GYM_RSS_SLIDE_STOP' => 'Stop scroller',
	'GYM_RSS_SOURCE' => 'Source',
));
// www.phpBB-SEO.com SEO TOOLKIT END -> GYM Sitemaps
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - Related Topics
$lang['RELATED_TOPICS'] = 'Related topics';
// www.phpBB-SEO.com SEO TOOLKIT END - Related Topics

// Radio Mod
$lang = array_merge($lang, array(
	'RADIO' => 'Radio',
));

// phpbb Calendar Version 0.1.0
$lang = array_merge($lang, array(
	'VIEWING_CALENDAR'			=> 'Viewing calendar',
	'VIEWING_CALENDAR_DAY'		=> 'Viewing calendar day',
	'VIEWING_CALENDAR_EVENT'	=> 'Viewing calendar event',
	'VIEWING_CALENDAR_MONTH'	=> 'Viewing calendar month',
	'VIEWING_CALENDAR_WEEK'		=> 'Viewing calendar week',
	'EDITING_CALENDAR_EVENT'	=> 'Editing calendar event',
	'CREATING_CALENDAR_EVENT'	=> 'Creating calendar event',
));

// Country Flags Version 3.0.6
$lang = array_merge($lang, array(
	'COUNTRY'			=> 'Country',
	'COUNTRY_FLAGS'		=> 'Country flags',
	'TOO_SHORT_FLAG'	=> 'You must select your country flag',
	'GROUP_FLAG'		=> 'Group country flag',
	'SELECT_FLAG'		=> 'Select your country flag',
	'SORT_FLAG'			=> 'Country flag',
	'USER_FLAG'			=> 'User country flag',
));

// -- Gender MOD 1.0.1
$lang = array_merge($lang, array(
	'GENDER'			=> 'Gender',
	'GENDER_EXPLAIN'	=> 'Please enter your gender here.',
	'GENDER_X'			=> 'None specified',
	'GENDER_M'			=> 'Male',
	'GENDER_F'			=> 'Female',
));

// Google Search
$lang = array_merge($lang, array(
	'SEARCH_GOOGLE' 	=> 'Google search?',
));

// AdvancedBlockMOD 1.0.6						
$lang = array_merge($lang, array(
	'WRONG_TIMEZONE'	=> 'You entered an incorrect timezone. Please stay on earth!',
));

// Share On 1.2.0 MOD
$lang = array_merge($lang, array(
	'SHARE_ON'				=> 'Share on ...',
	'SHARE_FACEBOOK'		=> 'Facebook',
	'SHARE_TWITTER'			=> 'Twitter',
	'SHARE_TUENTI'			=> 'Tuenti',
	'SHARE_SONICO'			=> 'Sonico',
	'SHARE_FRIENDFEED'		=> 'FriendFeed',
	'SHARE_ORKUT'			=> 'Orkut',
	'SHARE_DIGG'			=> 'Digg',
	'SHARE_MYSPACE'			=> 'MySpace',
	'SHARE_DELICIOUS'		=> 'Delicious',
	'SHARE_TECHNORATI'		=> 'Technorati',

	'SHARE_ON_FACEBOOK'		=> 'Share on Facebook',
	'SHARE_ON_TWITTER'		=> 'Share on Twitter',
	'SHARE_ON_TUENTI'		=> 'Share on Tuenti',
	'SHARE_ON_SONICO'		=> 'Share on Sonico',
	'SHARE_ON_FRIENDFEED'	=> 'Share on FriendFeed',
	'SHARE_ON_ORKUT'		=> 'Share on Orkut',
	'SHARE_ON_DIGG'			=> 'Share on Digg',
	'SHARE_ON_MYSPACE'		=> 'Share on MySpace',
	'SHARE_ON_DELICIOUS'	=> 'Share on Delicious',
	'SHARE_ON_TECHNORATI'	=> 'Share on Technorati',	
));

// PhpBB3 Knowledge Base Mod 1.0.2				
$lang = array_merge($lang, array(
	'KNOWLEDGE_BASE'		=> 'Knowledge Base',
	'KB_EXPLAIN'			=> 'Knowledge Base',
	'ARTICLES'				=> 'Articles',
));

// Topic solved
$lang = array_merge($lang, array(
	'SEARCH_UNSOLVED'				=> 'View unsolved topics',
	'SEARCH_YOUR_UNSOLVED'			=> 'View your unsolved topics',
	'SEARCH_SOLVED'					=> 'Search only in solved topics',
	'TOPIC_SOLVED'					=> 'Topic is solved',
	'SET_TOPIC_SOLVED'				=> 'Set topic as solved',
	'SET_TOPIC_NOT_SOLVED'			=> 'Set topic as unsolved',
));

// Mod browser, os & screen
$lang = array_merge($lang, array(
	'BROWSER_USE'			=> 'using',
	'BROWSER_NAME'			=> 'Browser: ',
	'OS_NAME'				=> 'Platform: ',
	'RESOLUTION_TYPE'		=> 'Screen Resolution: ',
	'BROWSER_UNKNOW'		=> 'Unknown',
	'OS_UNKNOW'				=> 'Unknown',
));

// Visit counter by forum 1.0.0
$lang = array_merge($lang, array(
	'FORUM_VISIT'	=> '%s user',
	'FORUM_VISITS'	=> '%s users',
));

// Users and Bots on Seperate Lines 1.0.0
$lang = array_merge($lang, array(
   'BOT_USERS_ONLINE' => '%d bots online',
   'BOT_USERS_TOTAL' => ', %d bots and ',
   'BOT_USERS_ZERO_ONLINE' => '0 bots online',
   'BOT_USERS_ZERO_TOTAL' => ', 0 bots and ',
   'BOT_USER_ONLINE' => '%d bot online',
   'BOT_USER_TOTAL' => ', %d bot and ',
   'BOTS_ONLINE' => 'Bots: ',
   'BOTS_ZERO_ONLINE' => '0 bots',
));

// Start Poster IP
$lang = array_merge($lang, array(
	'VIEWTOPIC_SEE_WHOIS' => 'Whois on:',
));	
	
// Frontpage
$lang = array_merge($lang, array(
	'FRONTPAGE'		=> 'Site Frontpage',
	'FRONTPAGE_SHORT'	=> 'Frontpage',
));

?>