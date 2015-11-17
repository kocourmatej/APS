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
	'RETURN_PROFILE_INFO'		=> '%sReturn to UCP: Edit profile%s' // %s will be replaced by <a> tags
));
