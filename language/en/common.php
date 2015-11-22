<?php

/**
*
* @package phpBB Extension - Advanced Profile System
* @copyright (c) 2015 posey - http://www.godfathertalks.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'WALL'					=> 'Wall',
	'FRIENDS'				=> 'Friends',
	'ACTIVITY_FEED'			=> 'Activity Feed',

	'AT'					=> 'at', // example: User1 posted "at" Tuesday 10 Nov.
	'COVERPHOTO'			=> 'Cover photo',
	'COVERPHOTO_INVALID_URL' => 'You\'ve entered an invalid url for your cover photo',
	'FIRST_POST_WALL'		=> 'Be the first to post on %s\'s wall!', // %s will be replaced by username
	'NOT_VALID_URL'			=> 'You\'ve entered an invalid url for the cover photo.',
	'PROFILE_VIEWS'			=> 'Profile views',
	'SEARCH_USER_TOPICS'	=> 'Search user\'s topics',
	'TOTAL_TOPICS'			=> 'Total topics',
	'USER_NO_FRIENDS'		=> 'No friends yet',
	'USER_NO_POSTS'			=> '%s hasn\'t made any posts yet..', // %s will be replaced by username
	
	'CONFIRM_WALL_DEL'			=> 'The wall post has successfully been deleted.',
	'CONFIRM_WALL_DEL_EXPLAIN'	=> 'Are you sure you want to delete this wall post?',
	'RETURN_WALL'				=> '%sReturn to %s\'s wall%s', // %s will be replaced by <a> tags and username
	'RETURN_PROFILE_INFO'		=> '%sReturn to UCP: Edit profile%s', // %s will be replaced by <a> tags
	
	'WALL_NOTIFICATION_TYPE_OPTION'		=> 'Someone posts a message on your wall',
	'WALL_NOTIFICATION_TITLE'			=> '%s <strong>posted on your wall!</strong><br />%s', // %s will be replaced by an username, second %s is begin of wall message
	
	'ACP_APS'				=> 'Advanced Profile System',
	'ACP_SETTINGS'			=> 'Settings',
	'ACP_SETTINGS_SAVED'	=> 'Settings for the New Profile System have been saved',
	'ACP_ENABLE_FL'			=> 'Enable the Friends List?',
	'ACP_ENABLE_CP'			=> 'Enable the Cover Photo?',
	'ACP_ENABLE_AF'			=> 'Enable the Activity Feed?',
	'ACP_CP_PANEL_ID'		=> 'Cover Photo Panel ID',
	'ACP_CP_PANEL_ID_EXPL'	=> 'The #th div <em>(panel)</em> that should have the cover photo as background image.<br />Default is <strong>1</strong>. But if you have more extensions installed this could vary..',
	
));
