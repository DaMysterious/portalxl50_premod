<?php
/**
*
* ucp [American English]
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

global $config;

// Privacy policy and T&C
$lang = array_merge($lang, array(
	'TERMS_OF_USE_CONTENT'	=> 'By accessing “%1$s” (hereinafter “we”, “us”, “our”, “%1$s”, “%2$s”), you agree to be legally bound by the following terms. If you do not agree to be legally bound by all of the following terms then please do not access and/or use “%1$s”. We may change these at any time and we’ll do our utmost in informing you, though it would be prudent to review this regularly yourself as your continued usage of “%1$s” after changes mean you agree to be legally bound by these terms as they are updated and/or amended.<br />
	<br />
	Our forums are powered by phpBB (hereinafter “they”, “them”, “their”, “phpBB software”, “www.phpbb.com”, “phpBB Group”, “phpBB Teams”) which is a bulletin board solution released under the “<a href="http://opensource.org/licenses/gpl-license.php">General Public License</a>” (hereinafter “GPL”) and can be downloaded from <a href="https://www.phpbb.com/">www.phpbb.com</a>. The phpBB software only facilitates internet based discussions, the phpBB Group are not responsible for what we allow and/or disallow as permissible content and/or conduct. For further information about phpBB, please see: <a href="https://www.phpbb.com/">https://www.phpbb.com/</a>.<br />
	<br />
	You agree not to post any abusive, obscene, vulgar, slanderous, hateful, threatening, sexually-orientated or any other material that may violate any laws be it of your country, the country where “%1$s” is hosted or International Law. Doing so may lead to you being immediately and permanently banned, with notification of your Internet Service Provider if deemed required by us. The IP address of all posts are recorded to aid in enforcing these conditions. You agree that “%1$s” have the right to remove, edit, move or close any topic at any time should we see fit. As a user you agree to any information you have entered to being stored in a database. While this information will not be disclosed to any third party without your consent, neither “%1$s” nor phpBB shall be held responsible for any hacking attempt that may lead to the data being compromised.
	',

	'PRIVACY_POLICY'		=> 'This policy explains in detail how “%1$s” along with its affiliated companies (hereinafter “we”, “us”, “our”, “%1$s”, “%2$s”) and phpBB (hereinafter “they”, “them”, “their”, “phpBB software”, “www.phpbb.com”, “phpBB Group”, “phpBB Teams”) use any information collected during any session of usage by you (hereinafter “your information”).<br />
	<br />
	Your information is collected via two ways. Firstly, by browsing “%1$s” will cause the phpBB software to create a number of cookies, which are small text files that are downloaded on to your computer’s web browser temporary files. The first two cookies just contain a user identifier (hereinafter “user-id”) and an anonymous session identifier (hereinafter “session-id”), automatically assigned to you by the phpBB software. A third cookie will be created once you have browsed topics within “%1$s” and is used to store which topics have been read, thereby improving your user experience.<br />
	<br />
	We may also create cookies external to the phpBB software whilst browsing “%1$s”, though these are outside the scope of this document which is intended to only cover the pages created by the phpBB software. The second way in which we collect your information is by what you submit to us. This can be, and is not limited to: posting as an anonymous user (hereinafter “anonymous posts”), registering on “%1$s” (hereinafter “your account”) and posts submitted by you after registration and whilst logged in (hereinafter “your posts”).<br />
	<br />
	Your account will at a bare minimum contain a uniquely identifiable name (hereinafter “your user name”), a personal password used for logging into your account (hereinafter “your password”) and a personal, valid e-mail address (hereinafter “your e-mail”). Your information for your account at “%1$s” is protected by data-protection laws applicable in the country that hosts us. Any information beyond your user name, your password, and your e-mail address required by “%1$s” during the registration process is either mandatory or optional, at the discretion of “%1$s”. In all cases, you have the option of what information in your account is publicly displayed. Furthermore, within your account, you have the option to opt-in or opt-out of automatically generated e-mails from the phpBB software.<br />
	<br />
	Your password is ciphered (a one-way hash) so that it is secure. However, it is recommended that you do not reuse the same password across a number of different websites. Your password is the means of accessing your account at “%1$s”, so please guard it carefully and under no circumstance will anyone affiliated with “%1$s”, phpBB or another 3rd party, legitimately ask you for your password. Should you forget your password for your account, you can use the “I forgot my password” feature provided by the phpBB software. This process will ask you to submit your user name and your e-mail, then the phpBB software will generate a new password to reclaim your account.<br />
 
	<br />
	<h2>Dear visitor</h2>
	<br />
	<p>Since the new European Cokkie law on June 1, 2012 came in to force, every website is legally obliged to inform the user about the nature of the delivered cookies and permission for its use. We realize that this is difficult and unfriendly. We\'re sorry.</p>
	<br />
	<p><strong>Privacy and Cookie Policy</strong><br />
	This Privacy and Cookie Policy explains how the domain ' . $config['sitename'] . ' uses personal information we collect and how you can limit our use of that personal data. </p>
	<p><strong>What information do we gather?</strong><br />
	We gather information that relates to, and can identify you (e.g. your name and address) and information that does not identify you (e.g. a visitor’s behaviour patterns when they visit our website). We collect information that does not identify you to evaluate our site, ensure that our site and services run properly. </p>
	<p><strong>How do we gather information?</strong><br />
	Information is gathered in two ways:
	indirectly (e.g. using website technology or technology when you use a  application for a mobile device); and
	directly (e.g. when you sign a subscription, sign up to create a ' . $config['sitename'] . ' profile, download a attachment or file.</p>
	<p><strong>Use of your information</strong><br />
	We might use your personal information to:<br />
	<ul>
	<li>remember you when you visit our website and track your browsing patterns by using cookies. </li>
	<li>ensure any products or services you receive are correctly administered (including subscriptions).</li>
	<li>contact you by email, mail or telephone to let you know about any of our products, services or promotions (which, in some cases may be provided by third parties) if you give us permission.</li>
	<li>ensure materials on the website are presented in the most effective manner for you and your computer.</li>
	<li>collate and log numeric internet addresses to improve the website and to monitor website usage.</li>
	</ul>
	<p>If you have subscribed to any of or services, we will use the information you provide to keep you updated on those services unless you tell us otherwise. </p>
	<p><strong>Accessing and updating your personal information</strong><br />
	You can update your details and  preferences by accessing your account pages on the website, or by emailing ' . $config['board_contact'] . '.
	You have the right to access the personal information held about you. To obtain a copy of the personal information we hold about you, please send an email to ' . $config['board_contact'] . '. </p>
	<p><strong>Cookies</strong><br />
	A cookie is a small piece of information that is placed on your computer when you visit certain websites. </p>
	<p>We use cookies to:<br />
	<ul>
	<li>remember you when you visit this website to keep track of your browsing patterns and to build up a profile of how you and other users use the website.</li>
	<li>make our website work as efficiently as possible.</li>
	<li>administer services to you and to advertisers.</li>
	</ul>
	Most browsers allow you to turn off cookies. To do this look at the “help” menu on your browser. Switching off cookies may restrict your use of the website and/or delay or affect the way in which it operates.
	</p>
	<p><strong>Third party advertising cookies</strong><br />
	Most of the advertisements you see on the website are generated by third parties.
	Some of these third parties generate their own cookies (or web beacons) to track how many people have seen a particular advertisement (or use the services of third parties to do this), and to track how many people have seen it more than once. These cookies cannot be used to identify an individual; they can only be used for statistical purposes, for example, in providing you with advertisements that are more relevant to your interests. Some of the information gleaned from third party cookies will be aggregated with other anonymous information for statistical purposes. The third party companies that generate these cookies have their own, very strict, privacy policies but we don\'t have access to these cookies; other than allowing them to be served, we have no role to play in these cookies at all (although we may use statistical information arising from these third party cookies and provided to us by third parties, to improve the targeting of advertisements to users of the website).</p>
	<p><strong>Security</strong><br />
	The password you provide when registering with the website is encrypted to ensure protection against unauthorised access to your personal information.
	We invest in high-quality security and do our utmost to protect user privacy. No data transmission over the Internet can be entirely secure, and therefore we cannot guarantee the security of your personal information and/or use of the website. Any information that you send is at your own risk and may be read by others. However once we have received your personal information we use strict procedures to protect the security of your personal information. </p>
	<p><strong>Disclosing your information</strong><br />
	We may disclose your personal information if we are approached by a potential buyer of our business, if we required to do so by law or in good-faith believing such action is necessary to comply with the law.
	We sometimes share your personal information with third parties who are our subcontractors or providers of various products and services on our behalf.
	We never sell personal information, or pass it to third parties for marketing purposes without your permission. </p>
	<p><strong>Where we store your personal information</strong></p>
	<p>' . $config['sitename'] . ' respects the privacy of any person and shall ensure that personal data confidentially and with the utmost care be treated. All personal details are stored securely in the databse of ' . $config['sitename'] . '. This database is accessible only to administrators of the domain ' . $config['sitename'] . '. ' . $config['sitename'] . ' further reasonable endeavors to secure its systems against loss and/or any form of unlawful use or processing, making use of appropriate technical and organizational measures.</span></p>
	<p><strong>Changes to this Policy</strong><br />
	If we change our Policy, we will post the changes on this page. Please check the website regularly for any changes to this Policy.
	If you don\'t feel we\'re adhering to this Policy, what should you do?
	If you believe that we have not adhered to this Policy, please notify us by email at ' . $config['board_contact'] . ' and we will try to solve the problem promptly. </p>
	<p>This Policy was last updated on June 8, 2012. </p>
	<p><strong>More information about cookies </strong> <br />
	</p>
	<div class="item"> &raquo; <a href="http://nl.wikipedia.org/wiki/Cookie_(internet)" target="_blank">Wikipedia</a> </div>
	<div class="item"> &raquo; <a href="http://www.leerwiki.nl/Wat_zijn_cookies" target="_blank">Leerwiki</a> </div>
	<div class="item"> &raquo; Google Chrome &raquo; <a href="http://support.google.com/chrome/bin/answer.py?hl=en-GB&answer=95647" target="_blank">Manage cookies</a>. </div>
	<div class="item"> &raquo; Internet Explorer &raquo; <a href="http://windows.microsoft.com/en-US/windows-vista/Block-or-allow-cookies" target="_blank">Block or allow cookies</a>. </div>
	<div class="item"> &raquo; Firefox &raquo; <a href="http://support.mozilla.org/en-US/kb/enable-and-disable-cookies-website-preferences" target="_blank">Cookies enable/disable</a>. </div>
	<div class="item"> &raquo; Safari &raquo; <a href="http://support.apple.com/kb/PH5042" target="_blank">Manage cookies</a>. </div>
	<br /><div class="green"><i>Cookies can not </b> be used to spy on private data from your computer or to read or intercept passwords. Also, they can not infect a computer with a virus or trojan. So they are perfectly safe and have been since the 90\'s without incident used on almost ALL websites in the world.</i></div>
    ',		
));

// Common language entries
$lang = array_merge($lang, array(
	'ACCOUNT_ACTIVE'				=> 'Your account has now been activated. Thank you for registering.',
	'ACCOUNT_ACTIVE_ADMIN'			=> 'The account has now been activated.',
	'ACCOUNT_ACTIVE_PROFILE'		=> 'Your account has now been successfully reactivated.',
	'ACCOUNT_ADDED'					=> 'Thank you for registering, your account has been created. You may now login with your username and password.',
	'ACCOUNT_COPPA'					=> 'Your account has been created but has to be approved, please check your e-mail for details.',
	'ACCOUNT_EMAIL_CHANGED'			=> 'Your account has been updated. However, this board requires account reactivation on e-mail changes. An activation key has been sent to the new e-mail address you provided. Please check your e-mail for further information.',
	'ACCOUNT_EMAIL_CHANGED_ADMIN'	=> 'Your account has been updated. However, this board requires account reactivation by the administrators on e-mail changes. An e-mail has been sent to them and you will be informed when your account has been reactivated.',
	'ACCOUNT_INACTIVE'				=> 'Your account has been created. However, this board requires account activation, an activation key has been sent to the e-mail address you provided. Please check your e-mail for further information.',
	'ACCOUNT_INACTIVE_ADMIN'		=> 'Your account has been created. However, this board requires account activation by the administrator group. An e-mail has been sent to them and you will be informed when your account has been activated.',
	'ACTIVATION_EMAIL_SENT'			=> 'The activation e-mail has been sent to your e-mail address.',
	'ACTIVATION_EMAIL_SENT_ADMIN'	=> 'The activation e-mail has been sent to the administrators e-mail addresses.',
	'ADD'							=> 'Add',
	'ADD_BCC'						=> 'Add [BCC]',
	'ADD_FOES'						=> 'Add new foes',
	'ADD_FOES_EXPLAIN'				=> 'You may enter several usernames each on a different line.',
	'ADD_FOLDER'					=> 'Add folder',
	'ADD_FRIENDS'					=> 'Add new friends',
	'ADD_FRIENDS_EXPLAIN'			=> 'You may enter several usernames each on a different line.',
	'ADD_NEW_RULE'					=> 'Add new rule',
	'ADD_RULE'						=> 'Add rule',
	'ADD_TO'						=> 'Add [To]',
	'ADD_USERS_UCP_EXPLAIN'			=> 'Here you can add new users to the group. You may select whether this group becomes the new default for the selected users. Please enter each username on a separate line.',
	'ADMIN_EMAIL'					=> 'Administrators can e-mail me information',
	'AGREE'							=> 'I agree to these terms',
	'ALLOW_PM'						=> 'Allow users to send you private messages',
	'ALLOW_PM_EXPLAIN'				=> 'Note that administrators and moderators will always be able to send you messages.',
	'ALREADY_ACTIVATED'				=> 'You have already activated your account.',
	'ATTACHMENTS_EXPLAIN'			=> 'This is a list of attachments you have made in posts to this board.',
	'ATTACHMENTS_DELETED'			=> 'Attachments successfully deleted.',
	'ATTACHMENT_DELETED'			=> 'Attachment successfully deleted.',
	'AVATAR_CATEGORY'				=> 'Category',
	'AVATAR_EXPLAIN'				=> 'Maximum dimensions; width: %1$d pixels, height: %2$d pixels, file size: %3$.2f KiB.',
	'AVATAR_FEATURES_DISABLED'		=> 'The avatar functionality is currently disabled.',
	'AVATAR_GALLERY'				=> 'Local gallery',
	'AVATAR_GENERAL_UPLOAD_ERROR'	=> 'Could not upload avatar to %s.',
	'AVATAR_NOT_ALLOWED'			=> 'Your avatar cannot be displayed because avatars have been disallowed.',
	'AVATAR_PAGE'					=> 'Page',
	'AVATAR_TYPE_NOT_ALLOWED'		=> 'Your current avatar cannot be displayed because its type has been disallowed.',

	'BACK_TO_DRAFTS'			=> 'Back to saved drafts',
	'BACK_TO_LOGIN'				=> 'Back to login screen',
	'BIRTHDAY'					=> 'Birthday',
	'BIRTHDAY_EXPLAIN'			=> 'Setting a year will list your age when it is your birthday.',
	'BOARD_DATE_FORMAT'			=> 'My date format',
	'BOARD_DATE_FORMAT_EXPLAIN'	=> 'The syntax used is identical to the PHP <a href="http://www.php.net/date">date()</a> function.',
	'BOARD_DST'					=> 'Summer Time/<abbr title="Daylight Saving Time">DST</abbr> is in effect',
	'BOARD_LANGUAGE'			=> 'My language',
	'BOARD_STYLE'				=> 'My board style',
	'BOARD_TIMEZONE'			=> 'My timezone',
	'BOOKMARKS'					=> 'Bookmarks',
	'BOOKMARKS_EXPLAIN'			=> 'You can bookmark topics for future reference. Select the checkbox for any bookmark you wish to delete, then press the <em>Remove marked bookmarks</em> button.',
	'BOOKMARKS_DISABLED'		=> 'Bookmarks are disabled on this board.',
	'BOOKMARKS_REMOVED'			=> 'Bookmarks removed successfully.',

	'CANNOT_EDIT_MESSAGE_TIME'	=> 'You can no longer edit or delete that message.',
	'CANNOT_MOVE_TO_SAME_FOLDER'=> 'Messages cannot be moved to the folder you want to remove.',
	'CANNOT_MOVE_FROM_SPECIAL'	=> 'Messages cannot be moved from the outbox.',
	'CANNOT_RENAME_FOLDER'		=> 'This folder cannot be renamed.',
	'CANNOT_REMOVE_FOLDER'		=> 'This folder cannot be removed.',
	'CHANGE_DEFAULT_GROUP'		=> 'Change default group',
	'CHANGE_PASSWORD'			=> 'Change password',
	'CLICK_GOTO_FOLDER'			=> '%1$sGo to your “%3$s” folder%2$s',
	'CLICK_RETURN_FOLDER'		=> '%1$sReturn to your “%3$s” folder%2$s',
	'CONFIRMATION'				=> 'Confirmation of registration',
	'CONFIRM_CHANGES'			=> 'Confirm changes',
	'CONFIRM_EMAIL'				=> 'Confirm e-mail address',
	'CONFIRM_EMAIL_EXPLAIN'		=> 'You only need to specify this if you are changing your e-mail address.',
	'CONFIRM_EXPLAIN'			=> 'To prevent automated registrations the board requires you to enter a confirmation code. The code is displayed in the image you should see below. If you are visually impaired or cannot otherwise read this code please contact the %sBoard Administrator%s.',
	'VC_REFRESH'				=> 'Refresh confirmation code',
	'VC_REFRESH_EXPLAIN'		=> 'If you cannot read the code you can request a new one by clicking the button.',

	'CONFIRM_PASSWORD'			=> 'Confirm password',
	'CONFIRM_PASSWORD_EXPLAIN'	=> 'You only need to confirm your password if you changed it above.',
	'COPPA_BIRTHDAY'			=> 'To continue with the registration procedure please tell us when you were born.',
	'COPPA_COMPLIANCE'			=> 'COPPA compliance',
	'COPPA_EXPLAIN'				=> 'Please note that clicking submit will create your account. However it cannot be activated until a parent or guardian approves your registration. You will be emailed a copy of the necessary form with details of where to send it.',
	'CREATE_FOLDER'				=> 'Add folder…',
	'CURRENT_IMAGE'				=> 'Current image',
	'CURRENT_PASSWORD'			=> 'Current password',
	'CURRENT_PASSWORD_EXPLAIN'	=> 'You must enter your current password if you wish to alter your email address or username.',
	'CURRENT_CHANGE_PASSWORD_EXPLAIN' => 'To change your password, your email address, or your username, you must enter your current password.',
	'CUR_PASSWORD_EMPTY'		=> 'You did not enter your current password.',
	'CUR_PASSWORD_ERROR'		=> 'The current password you entered is incorrect.',
	'CUSTOM_DATEFORMAT'			=> 'Custom…',

	'DEFAULT_ACTION'			=> 'Default action',
	'DEFAULT_ACTION_EXPLAIN'	=> 'This action will be triggered if none of the above is applicable.',
	'DEFAULT_ADD_SIG'			=> 'Attach my signature by default',
	'DEFAULT_BBCODE'			=> 'Enable BBCode by default',
	'DEFAULT_NOTIFY'			=> 'Notify me upon replies by default',
	'DEFAULT_SMILIES'			=> 'Enable smilies by default',
	'DEFINED_RULES'				=> 'Defined rules',
	'DELETED_TOPIC'				=> 'Topic has been removed.',
	'DELETE_ATTACHMENT'			=> 'Delete attachment',
	'DELETE_ATTACHMENTS'		=> 'Delete attachments',
	'DELETE_ATTACHMENT_CONFIRM'	=> 'Are you sure you want to delete this attachment?',
	'DELETE_ATTACHMENTS_CONFIRM'=> 'Are you sure you want to delete these attachments?',
	'DELETE_AVATAR'				=> 'Delete image',
	'DELETE_COOKIES_CONFIRM'	=> 'Are you sure you want to delete all cookies set by this board?',
	'DELETE_MARKED_PM'			=> 'Delete marked messages',
	'DELETE_MARKED_PM_CONFIRM'	=> 'Are you sure you want to delete all marked messages?',
	'DELETE_OLDEST_MESSAGES'	=> 'Delete oldest messages',
	'DELETE_MESSAGE'			=> 'Delete message',
	'DELETE_MESSAGE_CONFIRM'	=> 'Are you sure you want to delete this private message?',
	'DELETE_MESSAGES_IN_FOLDER'	=> 'Delete all messages within removed folder',
	'DELETE_RULE'				=> 'Delete rule',
	'DELETE_RULE_CONFIRM'		=> 'Are you sure you want to delete this rule?',
	'DEMOTE_SELECTED'			=> 'Demote selected',
	'DISABLE_CENSORS'			=> 'Enable word censoring',
	'DISPLAY_GALLERY'			=> 'Display gallery',
	'DOMAIN_NO_MX_RECORD_EMAIL'	=> 'The entered e-mail domain has no valid MX record.',
	'DOWNLOADS'					=> 'Downloads',
	'DRAFTS_DELETED'			=> 'All selected drafts were successfully deleted.',
	'DRAFTS_EXPLAIN'			=> 'Here you can view, edit and delete your saved drafts.',
	'DRAFT_UPDATED'				=> 'Draft successfully updated.',

	'EDIT_DRAFT_EXPLAIN'		=> 'Here you are able to edit your draft. Drafts do not contain attachment and poll information.',
	'EMAIL_BANNED_EMAIL'		=> 'The e-mail address you entered is not allowed to be used.',
	'EMAIL_REMIND'				=> 'This must be the e-mail address associated with your account. If you have not changed this via your user control panel then it is the e-mail address you registered your account with.',
	'EMAIL_TAKEN_EMAIL'			=> 'The entered e-mail address is already in use.',
	'EMPTY_DRAFT'				=> 'You must enter a message to submit your changes.',
	'EMPTY_DRAFT_TITLE'			=> 'You must enter a draft title.',
	'EXPORT_AS_XML'				=> 'Export as XML',
	'EXPORT_AS_CSV'				=> 'Export as CSV',
	'EXPORT_AS_CSV_EXCEL'		=> 'Export as CSV (Excel)',
	'EXPORT_AS_TXT'				=> 'Export as TXT',
	'EXPORT_AS_MSG'				=> 'Export as MSG',
	'EXPORT_FOLDER'				=> 'Export this view',

	'FIELD_REQUIRED'					=> 'The field “%s” must be completed.',
	'FIELD_TOO_SHORT'					=> 'The field “%1$s” is too short, a minimum of %2$d characters is required.',
	'FIELD_TOO_LONG'					=> 'The field “%1$s” is too long, a maximum of %2$d characters is allowed.',
	'FIELD_TOO_SMALL'					=> 'The value of “%1$s” is too small, a minimum value of %2$d is required.',
	'FIELD_TOO_LARGE'					=> 'The value of “%1$s” is too large, a maximum value of %2$d is allowed.',
	'FIELD_INVALID_CHARS_NUMBERS_ONLY'	=> 'The field “%s” has invalid characters, only numbers are allowed.',
	'FIELD_INVALID_CHARS_ALPHA_ONLY'	=> 'The field “%s” has invalid characters, only alphanumeric characters are allowed.',
	'FIELD_INVALID_CHARS_SPACERS_ONLY'	=> 'The field “%s” has invalid characters, only alphanumeric, space or -+_[] characters are allowed.',
	'FIELD_INVALID_DATE'				=> 'The field “%s” has an invalid date.',
	'FIELD_INVALID_VALUE'				=> 'The field “%s” has an invalid value.',

	'FOE_MESSAGE'				=> 'Message from foe',
	'FOES_EXPLAIN'				=> 'Foes are users which will be ignored by default. Posts by these users will not be fully visible. Personal messages from foes are still permitted. Please note that you cannot ignore moderators or administrators.',
	'FOES_UPDATED'				=> 'Your foes list has been updated successfully.',
	'FOLDER_ADDED'				=> 'Folder successfully added.',
	'FOLDER_MESSAGE_STATUS'		=> '%1$d from %2$d messages stored',
	'FOLDER_NAME_EMPTY'			=> 'You must enter a name for this folder.',
	'FOLDER_NAME_EXIST'			=> 'Folder <strong>%s</strong> already exists.',
	'FOLDER_OPTIONS'			=> 'Folder options',
	'FOLDER_RENAMED'			=> 'Folder successfully renamed.',
	'FOLDER_REMOVED'			=> 'Folder successfully removed.',
	'FOLDER_STATUS_MSG'			=> 'Folder is %1$d%% full (%2$d from %3$d messages stored)',
	'FORWARD_PM'				=> 'Forward PM',
	'FORCE_PASSWORD_EXPLAIN'	=> 'Before you may continue browsing the board you are required to change your password.',
	'FRIEND_MESSAGE'			=> 'Message from friend',
	'FRIENDS'					=> 'Friends',
	'FRIENDS_EXPLAIN'			=> 'Friends enable you quick access to members you communicate with frequently. If the template has relevant support any posts made by a friend may be highlighted.',
	'FRIENDS_OFFLINE'			=> 'Offline',
	'FRIENDS_ONLINE'			=> 'Online',
	'FRIENDS_UPDATED'			=> 'Your friends list has been updated successfully.',
	'FULL_FOLDER_OPTION_CHANGED'=> 'The action to take when a folder is full has been changed successfully.',
	'FWD_ORIGINAL_MESSAGE'		=> '-------- Original Message --------',
	'FWD_SUBJECT'				=> 'Subject: %s',
	'FWD_DATE'					=> 'Date: %s',
	'FWD_FROM'					=> 'From: %s',
	'FWD_TO'					=> 'To: %s',

	'GLOBAL_ANNOUNCEMENT'		=> 'Global announcement',

	'HIDE_ONLINE'				=> 'Hide my online status',
	'HIDE_ONLINE_EXPLAIN'		=> 'Changing this setting won’t become effective until your next visit to the board.',
	'HOLD_NEW_MESSAGES'			=> 'Do not accept new messages (New messages will be held back until enough space is available)',
	'HOLD_NEW_MESSAGES_SHORT'	=> 'New messages will be held back',

	'IF_FOLDER_FULL'			=> 'If folder is full',
	'IMPORTANT_NEWS'			=> 'Important announcements',
	'INVALID_USER_BIRTHDAY'			=> 'The entered birthday is not a valid date.',
	'INVALID_CHARS_USERNAME'	=> 'The username contains forbidden characters.',
	'INVALID_CHARS_NEW_PASSWORD'=> 'The password does not contain the required characters.',
	'ITEMS_REQUIRED'			=> 'The items marked with * are required profile fields and need to be filled out.',

	'JOIN_SELECTED'				=> 'Join selected',

	'LANGUAGE'					=> 'Language',
	'LINK_REMOTE_AVATAR'		=> 'Link off-site',
	'LINK_REMOTE_AVATAR_EXPLAIN'=> 'Enter the URL of the location containing the avatar image you wish to link to.',
	'LINK_REMOTE_SIZE'			=> 'Avatar dimensions',
	'LINK_REMOTE_SIZE_EXPLAIN'	=> 'Specify the width and height of the avatar, leave blank to attempt automatic verification.',
	'LOGIN_EXPLAIN_UCP'			=> 'Please login in order to access the User Control Panel.',
	'LOGIN_REDIRECT'			=> 'You have been successfully logged in.',
	'LOGOUT_FAILED'				=> 'You were not logged out, as the request did not match your session. Please contact the board administrator if you continue to experience problems.',
	'LOGOUT_REDIRECT'			=> 'You have been successfully logged out.',

	'MARK_IMPORTANT'				=> 'Mark/Unmark as important',
	'MARKED_MESSAGE'				=> 'Marked message',
	'MAX_FOLDER_REACHED'			=> 'Maximum number of allowed user defined folders reached.',
	'MESSAGE_BY_AUTHOR'				=> 'by',
	'MESSAGE_COLOURS'				=> 'Message colors',
	'MESSAGE_DELETED'				=> 'Message successfully deleted.',
	'MESSAGE_EDITED'				=> 'Message successfully edited.',
	'MESSAGE_HISTORY'				=> 'Message history',
	'MESSAGE_REMOVED_FROM_OUTBOX'	=> 'This message was deleted by its author.',
	'MESSAGE_SENT_ON'				=> 'on',
	'MESSAGE_STORED'				=> 'This message has been sent successfully.',
	'MESSAGE_TO'					=> 'To',
	'MESSAGES_DELETED'				=> 'Messages successfully deleted',
	'MOVE_DELETED_MESSAGES_TO'		=> 'Move messages from removed folder to',
	'MOVE_DOWN'						=> 'Move down',
	'MOVE_MARKED_TO_FOLDER'			=> 'Move marked to %s',
	'MOVE_PM_ERROR'					=> 'An error occurred while moving the messages to the new folder, only %1d from %2d messages were moved.',
	'MOVE_TO_FOLDER'				=> 'Move to folder',
	'MOVE_UP'						=> 'Move up',

	'NEW_EMAIL_CONFIRM_EMPTY'		=> 'You did not enter a confirm e-mail address.',
	'NEW_EMAIL_ERROR'				=> 'The e-mail addresses you entered do not match.',
	'NEW_FOLDER_NAME'				=> 'New folder name',
	'NEW_PASSWORD'					=> 'New password',
	'NEW_PASSWORD_CONFIRM_EMPTY'	=> 'You did not enter a confirm password.',
	'NEW_PASSWORD_ERROR'			=> 'The passwords you entered do not match.',
	'NOTIFY_METHOD'					=> 'Notification method',
	'NOTIFY_METHOD_BOTH'			=> 'Both',
	'NOTIFY_METHOD_EMAIL'			=> 'E-mail only',
	'NOTIFY_METHOD_EXPLAIN'			=> 'Method for sending messages sent via this board.',
	'NOTIFY_METHOD_IM'				=> 'Jabber only',
	'NOTIFY_ON_PM'					=> 'Notify me on new private messages',
	'NOT_ADDED_FRIENDS_ANONYMOUS'	=> 'You cannot add the anonymous user to your friends list.',
	'NOT_ADDED_FRIENDS_BOTS'		=> 'You cannot add bots to your friends list.',
	'NOT_ADDED_FRIENDS_FOES'		=> 'You cannot add users to your friends list who are on your foes list.',
	'NOT_ADDED_FRIENDS_SELF'		=> 'You cannot add yourself to the friends list.',
	'NOT_ADDED_FOES_MOD_ADMIN'		=> 'You cannot add administrators and moderators to your foes list.',
	'NOT_ADDED_FOES_ANONYMOUS'		=> 'You cannot add the anonymous user to your foes list.',
	'NOT_ADDED_FOES_BOTS'			=> 'You cannot add bots to your foes list.',
	'NOT_ADDED_FOES_FRIENDS'		=> 'You cannot add users to your foes list who are on your friends list.',
	'NOT_ADDED_FOES_SELF'			=> 'You cannot add yourself to the foes list.',
	'NOT_AGREE'						=> 'I do not agree to these terms',
	'NOT_ENOUGH_SPACE_FOLDER'		=> 'The destination folder “%s” seems to be full. The requested action has not been taken.',
	'NOT_MOVED_MESSAGE'				=> 'You have 1 private message currently on hold because of full folder.',
	'NOT_MOVED_MESSAGES'			=> 'You have %d private messages currently on hold because of full folder.',
	'NO_ACTION_MODE'				=> 'No message action specified.',
	'NO_AUTHOR'						=> 'No author defined for this message',
	'NO_AVATAR_CATEGORY'			=> 'None',

	'NO_AUTH_DELETE_MESSAGE'		=> 'You are not authorized to delete private messages.',
	'NO_AUTH_EDIT_MESSAGE'			=> 'You are not authorized to edit private messages.',
	'NO_AUTH_FORWARD_MESSAGE'		=> 'You are not authorized to forward private messages.',
	'NO_AUTH_GROUP_MESSAGE'			=> 'You are not authorized to send private messages to groups.',
	'NO_AUTH_PASSWORD_REMINDER'		=> 'You are not authorized to request a new password.',
	'NO_AUTH_READ_HOLD_MESSAGE'		=> 'You are not authorized to read private messages that are on hold.',
	'NO_AUTH_READ_MESSAGE'			=> 'You are not authorized to read private messages.',
	'NO_AUTH_READ_REMOVED_MESSAGE'	=> 'You are not able to read this message because it was removed by the author.',
	'NO_AUTH_SEND_MESSAGE'			=> 'You are not authorized to send private messages.',
	'NO_AUTH_SIGNATURE'				=> 'You are not authorized to define a signature.',

	'NO_BCC_RECIPIENT'			=> 'None',
	'NO_BOOKMARKS'				=> 'You have no bookmarks.',
	'NO_BOOKMARKS_SELECTED'		=> 'You have selected no bookmarks.',
	'NO_EDIT_READ_MESSAGE'		=> 'Private message cannot be edited because it has already been read.',
	'NO_EMAIL_USER'				=> 'The e-mail/username information submitted could not be found.',
	'NO_FOES'					=> 'No foes currently defined',
	'NO_FRIENDS'				=> 'No friends currently defined',
	'NO_FRIENDS_OFFLINE'		=> 'No friends offline',
	'NO_FRIENDS_ONLINE'			=> 'No friends online',
	'NO_GROUP_SELECTED'			=> 'No group specified.',
	'NO_IMPORTANT_NEWS'			=> 'No important announcements present.',
	'NO_MESSAGE'				=> 'Private message could not be found.',
	'NO_NEW_FOLDER_NAME'		=> 'You have to specify a new folder name.',
	'NO_NEWER_PM'				=> 'No newer messages.',
	'NO_OLDER_PM'				=> 'No older messages.',
	'NO_PASSWORD_SUPPLIED'		=> 'You cannot login without a password.',
	'NO_RECIPIENT'				=> 'No recipient defined.',
	'NO_RULES_DEFINED'			=> 'No rules defined.',
	'NO_SAVED_DRAFTS'			=> 'No drafts saved.',
	'NO_TO_RECIPIENT'			=> 'None',
	'NO_WATCHED_FORUMS'			=> 'You are not subscribed to any forums.',
	'NO_WATCHED_SELECTED'		=> 'You have not selected any subscribed topics or forums.',
	'NO_WATCHED_TOPICS'			=> 'You are not subscribed to any topics.',

	'PASS_TYPE_ALPHA_EXPLAIN'	=> 'Password must be between %1$d and %2$d characters long, must contain letters in mixed case and must contain numbers.',
	'PASS_TYPE_ANY_EXPLAIN'		=> 'Must be between %1$d and %2$d characters.',
	'PASS_TYPE_CASE_EXPLAIN'	=> 'Password must be between %1$d and %2$d characters long and must contain letters in mixed case.',
	'PASS_TYPE_SYMBOL_EXPLAIN'	=> 'Password must be between %1$d and %2$d characters long, must contain letters in mixed case, must contain numbers and must contain symbols.',
	'PASSWORD'					=> 'Password',
	'PASSWORD_ACTIVATED'		=> 'Your new password has been activated.',
	'PASSWORD_UPDATED'			=> 'A new password was sent to your registered e-mail address.',
	'PERMISSIONS_RESTORED'		=> 'Successfully restored original permissions.',
	'PERMISSIONS_TRANSFERRED'	=> 'Successfully transferred permissions from <strong>%s</strong>, you are now able to browse the board with this user’s permissions.<br />Please note that admin permissions were not transferred. You are able to revert to your permission set at any time.',
	'PM_DISABLED'				=> 'Private messaging has been disabled on this board.',
	'PM_FROM'					=> 'From',
	'PM_FROM_REMOVED_AUTHOR'	=> 'This message was sent by a user no longer registered.',
	'PM_ICON'					=> 'PM icon',
	'PM_INBOX'					=> 'Inbox',
	'PM_NO_USERS'				=> 'The requested users to be added do not exist.',
	'PM_OUTBOX'					=> 'Outbox',
	'PM_SENTBOX'				=> 'Sent messages',
	'PM_SUBJECT'				=> 'Message subject',
	'PM_TO'						=> 'Send to',
	'PM_USERS_REMOVED_NO_PM'	=> 'Some users couldn’t be added as they have disabled private message receipt.',
	'POPUP_ON_PM'				=> 'Pop up window on new private message',
	'POST_EDIT_PM'				=> 'Edit message',
	'POST_FORWARD_PM'			=> 'Forward message',
	'POST_NEW_PM'				=> 'Compose message',
	'POST_PM_LOCKED'			=> 'Private messaging is locked.',
	'POST_PM_POST'				=> 'Quote post',
	'POST_QUOTE_PM'				=> 'Quote message',
	'POST_REPLY_PM'				=> 'Reply to message',
	'PRINT_PM'					=> 'Print view',
	'PREFERENCES_UPDATED'		=> 'Your preferences have been updated.',
	'PROFILE_INFO_NOTICE'		=> 'Please note that this information may be viewable to other members. Be careful when including any personal details. Any fields marked with a * must be completed.',
	'PROFILE_UPDATED'			=> 'Your profile has been updated.',

	'RECIPIENT'							=> 'Recipient',
	'RECIPIENTS'						=> 'Recipients',
	'REGISTRATION'						=> 'Registration',
	'RELEASE_MESSAGES'					=> '%sRelease all on-hold messages%s… they will be re-sorted into the appropriate folder if enough space is made available.',
	'REMOVE_ADDRESS'					=> 'Remove address',
	'REMOVE_SELECTED_BOOKMARKS'			=> 'Remove selected bookmarks',
	'REMOVE_SELECTED_BOOKMARKS_CONFIRM'	=> 'Are you sure you want to delete all selected bookmarks?',
	'REMOVE_BOOKMARK_MARKED'			=> 'Remove marked bookmarks',
	'REMOVE_FOLDER'						=> 'Remove folder',
	'REMOVE_FOLDER_CONFIRM'				=> 'Are you sure you want to remove this folder?',
	'RENAME'							=> 'Rename',
	'RENAME_FOLDER'						=> 'Rename folder',
	'REPLIED_MESSAGE'					=> 'Replied to message',
	'REPLY_TO_ALL'						=> 'Reply to sender and all recipients.',
	'REPORT_PM'							=> 'Report private message',
	'RESIGN_SELECTED'					=> 'Resign selected',
	'RETURN_FOLDER'						=> '%1$sReturn to previous folder%2$s',
	'RETURN_UCP'						=> '%sReturn to the User Control Panel%s',
	'RULE_ADDED'						=> 'Rule successfully added.',
	'RULE_ALREADY_DEFINED'				=> 'This rule was defined previously.',
	'RULE_DELETED'						=> 'Rule successfully removed.',
	'RULE_LIMIT_REACHED'				=> 'You cannot add more PM rules. You have reached the maximum number of rules.',
	'RULE_NOT_DEFINED'					=> 'Rule not correctly specified.',
	'RULE_REMOVED_MESSAGE'				=> 'One private message had been removed due to private message filters.',
	'RULE_REMOVED_MESSAGES'				=> '%d private messages were removed due to private message filters.',

	'SAME_PASSWORD_ERROR'		=> 'The new password you entered is the same as your current password.',
	'SEARCH_YOUR_POSTS'			=> 'Show your posts',
	'SEND_PASSWORD'				=> 'Send password',
	'SENT_AT'					=> 'Sent',			// Used before dates in private messages
	'SHOW_EMAIL'				=> 'Users can contact me by e-mail',
	'SIGNATURE_EXPLAIN'			=> 'This is a block of text that can be added to posts you make. There is a %d character limit.',
	'SIGNATURE_PREVIEW'			=> 'Your signature will appear like this in posts',
	'SIGNATURE_TOO_LONG'		=> 'Your signature is too long.',
	'SORT'						=> 'Sort',
	'SORT_COMMENT'				=> 'File comment',
	'SORT_DOWNLOADS'			=> 'Downloads',
	'SORT_EXTENSION'			=> 'Extension',
	'SORT_FILENAME'				=> 'Filename',
	'SORT_POST_TIME'			=> 'Post time',
	'SORT_SIZE'					=> 'File size',

	'TIMEZONE'					=> 'Timezone',
	'TO'						=> 'To',
	'TOO_MANY_RECIPIENTS'		=> 'You tried to send a private message to too many recipients.',
	'TOO_MANY_REGISTERS'		=> 'You have exceeded the maximum number of registration attempts for this session. Please try again later.',

	'UCP'						=> 'User Control Panel',
	'UCP_ACTIVATE'				=> 'Activate account',
	'UCP_ADMIN_ACTIVATE'		=> 'Please note that you will need to enter a valid e-mail address before your account is activated. The administrator will review your account and if approved you will receive an e-mail at the address you specified.',
	'UCP_AIM'					=> 'AOL Instant Messenger',
	'UCP_ATTACHMENTS'			=> 'Attachments',
	'UCP_COPPA_BEFORE'			=> 'Before %s',
	'UCP_COPPA_ON_AFTER'		=> 'On or after %s',
	'UCP_EMAIL_ACTIVATE'		=> 'Please note that you will need to enter a valid e-mail address before your account is activated. You will receive an e-mail at the address you provide that contains an account activation link.',
	'UCP_ICQ'					=> 'ICQ number',
	'UCP_JABBER'				=> 'Jabber address',

	'UCP_MAIN'					=> 'Overview',
	'UCP_MAIN_ATTACHMENTS'		=> 'Manage attachments',
	'UCP_MAIN_BOOKMARKS'		=> 'Manage bookmarks',
	'UCP_MAIN_DRAFTS'			=> 'Manage drafts',
	'UCP_MAIN_FRONT'			=> 'Front page',
	'UCP_MAIN_SUBSCRIBED'		=> 'Manage subscriptions',

	'UCP_MSNM'					=> 'WL/MSN Messenger',
	'UCP_NO_ATTACHMENTS'		=> 'You have posted no files.',

	'UCP_PREFS'					=> 'Board preferences',
	'UCP_PREFS_PERSONAL'		=> 'Edit global settings',
	'UCP_PREFS_POST'			=> 'Edit posting defaults',
	'UCP_PREFS_VIEW'			=> 'Edit display options',

	'UCP_PM'					=> 'Private messages',
	'UCP_PM_COMPOSE'			=> 'Compose message',
	'UCP_PM_DRAFTS'				=> 'Manage PM drafts',
	'UCP_PM_OPTIONS'			=> 'Rules, folders &amp; settings',
	'UCP_PM_POPUP'				=> 'Private messages',
	'UCP_PM_POPUP_TITLE'		=> 'Private message popup',
	'UCP_PM_UNREAD'				=> 'Unread messages',
	'UCP_PM_VIEW'				=> 'View messages',

	'UCP_PROFILE'				=> 'Profile',
	'UCP_PROFILE_AVATAR'		=> 'Edit avatar',
	'UCP_PROFILE_PROFILE_INFO'	=> 'Edit profile',
	'UCP_PROFILE_REG_DETAILS'	=> 'Edit account settings',
	'UCP_PROFILE_SIGNATURE'		=> 'Edit signature',

	'UCP_USERGROUPS'			=> 'Usergroups',
	'UCP_USERGROUPS_MEMBER'		=> 'Edit memberships',
	'UCP_USERGROUPS_MANAGE'		=> 'Manage groups',

	'UCP_REGISTER_DISABLE'			=> 'Creating a new account is currently not possible.',
	'UCP_REMIND'					=> 'Send password',
	'UCP_RESEND'					=> 'Send activation e-mail',
	'UCP_WELCOME'					=> 'Welcome to the User Control Panel. From here you can monitor, view and update your profile, preferences, subscribed forums and topics. You can also send messages to other users (if permitted). Please ensure you read any announcements before continuing.',
	'UCP_YIM'						=> 'Yahoo Messenger',
	'UCP_ZEBRA'						=> 'Friends &amp; Foes',
	'UCP_ZEBRA_FOES'				=> 'Manage foes',
	'UCP_ZEBRA_FRIENDS'				=> 'Manage friends',
	'UNDISCLOSED_RECIPIENT'			=> 'Undisclosed Recipient',
	'UNKNOWN_FOLDER'				=> 'Unknown folder',
	'UNWATCH_MARKED'				=> 'Unwatch marked',
	'UPLOAD_AVATAR_FILE'			=> 'Upload from your machine',
	'UPLOAD_AVATAR_URL'				=> 'Upload from a URL',
	'UPLOAD_AVATAR_URL_EXPLAIN'		=> 'Enter the URL of the location containing the image. The image will be copied to this site.',
	'USERNAME_ALPHA_ONLY_EXPLAIN'	=> 'Username must be between %1$d and %2$d chars long and use only alphanumeric characters.',
	'USERNAME_ALPHA_SPACERS_EXPLAIN'=> 'Username must be between %1$d and %2$d chars long and use alphanumeric, space or -+_[] characters.',
	'USERNAME_ASCII_EXPLAIN'		=> 'Username must be between %1$d and %2$d chars long and use only ASCII characters, so no special symbols.',
	'USERNAME_LETTER_NUM_EXPLAIN'	=> 'Username must be between %1$d and %2$d chars long and use only letter or number characters.',
	'USERNAME_LETTER_NUM_SPACERS_EXPLAIN'=> 'Username must be between %1$d and %2$d chars long and use letter, number, space or -+_[] characters.',
	'USERNAME_CHARS_ANY_EXPLAIN'	=> 'Length must be between %1$d and %2$d characters.',
	'USERNAME_TAKEN_USERNAME'		=> 'The username you entered is already in use, please select an alternative.',
	'USERNAME_DISALLOWED_USERNAME'	=> 'The username you entered has been disallowed or contains a disallowed word. Please choose a different name.',
	'USER_NOT_FOUND_OR_INACTIVE'	=> 'The usernames you specified could either not be found or are not activated users.',

	'VIEW_AVATARS'				=> 'Display avatars',
	'VIEW_EDIT'					=> 'View/Edit',
	'VIEW_FLASH'				=> 'Display Flash animations',
	'VIEW_IMAGES'				=> 'Display images within posts',
	'VIEW_NEXT_HISTORY'			=> 'Next PM in history',
	'VIEW_NEXT_PM'				=> 'Next PM',
	'VIEW_PM'					=> 'View message',
	'VIEW_PM_INFO'				=> 'Message details',
	'VIEW_PM_MESSAGE'			=> '1 message',
	'VIEW_PM_MESSAGES'			=> '%d messages',
	'VIEW_PREVIOUS_HISTORY'		=> 'Previous PM in history',
	'VIEW_PREVIOUS_PM'			=> 'Previous PM',
	'VIEW_SIGS'					=> 'Display signatures',
	'VIEW_SMILIES'				=> 'Display smilies as images',
	'VIEW_TOPICS_DAYS'			=> 'Display topics from previous days',
	'VIEW_TOPICS_DIR'			=> 'Display topic order direction',
	'VIEW_TOPICS_KEY'			=> 'Display topics ordering by',
	'VIEW_POSTS_DAYS'			=> 'Display posts from previous days',
	'VIEW_POSTS_DIR'			=> 'Display post order direction',
	'VIEW_POSTS_KEY'			=> 'Display posts ordering by',

	'WATCHED_EXPLAIN'			=> 'Below is a list of forums and topics you are subscribed to. You will be notified of new posts in either. To unsubscribe mark the forum or topic and then press the <em>Unwatch marked</em> button.',
	'WATCHED_FORUMS'			=> 'Watched forums',
	'WATCHED_TOPICS'			=> 'Watched topics',
	'WRONG_ACTIVATION'			=> 'The activation key you supplied does not match any in the database.',

	'YOUR_DETAILS'				=> 'Your activity',
	'YOUR_FOES'					=> 'Your foes',
	'YOUR_FOES_EXPLAIN'			=> 'To remove usernames select them and click submit.',
	'YOUR_FRIENDS'				=> 'Your friends',
	'YOUR_FRIENDS_EXPLAIN'		=> 'To remove usernames select them and click submit.',
	'YOUR_WARNINGS'				=> 'Your warning level',

	'PM_ACTION' => array(
		'PLACE_INTO_FOLDER'	=> 'Place into folder',
		'MARK_AS_READ'		=> 'Mark as read',
		'MARK_AS_IMPORTANT'	=> 'Mark message',
		'DELETE_MESSAGE'	=> 'Delete message'
	),
	'PM_CHECK' => array(
		'SUBJECT'	=> 'Subject',
		'SENDER'	=> 'Sender',
		'MESSAGE'	=> 'Message',
		'STATUS'	=> 'Message status',
		'TO'		=> 'Sent To'
	),
	'PM_RULE' => array(
		'IS_LIKE'		=> 'is like',
		'IS_NOT_LIKE'	=> 'is not like',
		'IS'			=> 'is',
		'IS_NOT'		=> 'is not',
		'BEGINS_WITH'	=> 'begins with',
		'ENDS_WITH'		=> 'ends with',
		'IS_FRIEND'		=> 'is friend',
		'IS_FOE'		=> 'is foe',
		'IS_USER'		=> 'is user',
		'IS_GROUP'		=> 'is in usergroup',
		'ANSWERED'		=> 'answered',
		'FORWARDED'		=> 'forwarded',
		'TO_GROUP'		=> 'to my default usergroup',
		'TO_ME'			=> 'to me'
	),


	'GROUPS_EXPLAIN'	=> 'Usergroups enable board admins to better administer users. By default you will be placed in a specific group, this is your default group. This group defines how you may appear to other users, for example your username colouration, avatar, rank, etc. Depending on whether the administrator allows it you may be allowed to change your default group. You may also be placed in or allowed to join other groups. Some groups may give you additional permissions to view content or increase your capabilities in other areas.',
	'GROUP_LEADER'		=> 'Leaderships',
	'GROUP_MEMBER'		=> 'Memberships',
	'GROUP_PENDING'		=> 'Pending memberships',
	'GROUP_NONMEMBER'	=> 'Non-memberships',
	'GROUP_DETAILS'		=> 'Group details',

	'NO_LEADER'		=> 'No group leaderships',
	'NO_MEMBER'		=> 'No group memberships',
	'NO_PENDING'	=> 'No pending memberships',
	'NO_NONMEMBER'	=> 'No non-member groups',
));

// Anti Bot Question
$lang = array_merge($lang, array(
	'AB_QUESTION_ERROR'	=> 'The Answer to the Anti-Bot Question you entered is incorrect, Please check your answer and try again.',
	'AB_NO_ANSWER' => 'No Anti-Bot Question Answer Entered.',
));

// phpbb Calendar Version 0.1.0
$lang = array_merge($lang, array(
	'DISPLAY_EVENTS_DAYCOUNT' => 'How many days into the future do you want to display?',
	'UCP_MAIN_CALENDAR_MYEVENTS' => 'Manage my events',
	'UCP_MAIN_CALENDAR_REGISTRATION'=> 'Manage event registration',
));

?>