<?php
/**
*
* @package Advanced Profile System
* @copyright (c) 2015 posey
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace posey\aps\event;
/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\controller\helper */
	protected $controller_helper;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\user */
	protected $user;
	/** @var string */
	protected $phpbb_root_path;
	/** @var string */
	protected $phpEx;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\request\request	*/
	protected $request;
	/** @var string */
	protected $wall_table;
	
	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config				Config object
	* @param \phpbb\controller\helper			$helper				Controller helper object
	* @param \phpbb\template					$template			Template object
	* @param \phpbb\db\driver\driver_interface	$db					Database
	* @param \phpbb\user    					$user				User object
	* @param string            					$phpbb_root_path    phpBB root path
    * @param string            					$php_ext    		phpEx
	* @param \phpbb\auth\auth					$auth				Auth object
	* @param \phpbb\request\request 			$request 			phpBB request
	* @param string								$wall_table			Wall Table
	*/
	
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $controller_helper, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, $phpbb_root_path, $phpEx, \phpbb\auth\auth $auth, \phpbb\request\request $request, $wall_table)
	{
        $this->config				= $config;
		$this->controller_helper 	= $controller_helper;
        $this->template 			= $template;
		$this->db 					= $db;
		$this->user 				= $user;
		$this->root_path 			= $phpbb_root_path;
		$this->phpEx 				= $phpEx;
		$this->auth 				= $auth;
		$this->request 				= $request;
		$this->wall_table			= $wall_table;
	}
	
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.memberlist_view_profile'			=> 'advanced_profile_system',
			'core.ucp_profile_modify_profile_info'	=> 'ucp_profile_modify_profile_info'
			);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'posey/aps',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
	
	public function advanced_profile_system($event)
	{
		$member = $event['member']; 
		$user_id = (int) $member['user_id'];  // Get user_id of user we are viewing
		$username = $member['username'];
		
		/****************
		* PROFILE VIEWS *
		****************/
		//	Make sure we have a session				Make sure user is not a bot.	 Do not increase view count if viewing own profile.
		if (isset($this->user->data['session_page']) && !$this->user->data['is_bot'] && ($this->user->data['user_id'] !== $user_id))
		{
			$incr_profile_views = 'UPDATE ' . USERS_TABLE . '
									SET user_profile_views = user_profile_views + 1
									WHERE user_id = '. $user_id;
			$this->db->sql_query($incr_profile_views);
		}
		
		/****************
		* ACTIVITY FEED *
		****************/
		$activity_feed_ary = array(
			'SELECT'    => 'p.*, t.*, u.username, u.user_colour',
			
			'FROM'      => array(
				POSTS_TABLE     => 'p',
			),
			
			'LEFT_JOIN' => array(
				array(
					'FROM'  => array(USERS_TABLE => 'u'),
					'ON'    => 'u.user_id = p.poster_id'
				),
				array(
					'FROM'  => array(TOPICS_TABLE => 't'),
					'ON'    => 'p.topic_id = t.topic_id'
				),
			),
							// Make sure the user viewing the profile is allowed to view the post made by the user we are viewing the profile from
			'WHERE'     => 	$this->db->sql_in_set('t.forum_id', array_keys($this->auth->acl_getf('f_read', true))) . ' 
							AND t.topic_status <> ' . ITEM_MOVED . '
							AND t.topic_visibility = 1
							AND p.poster_id = '. $user_id, // Only get posts from user we are viewing the profile from
				
			'ORDER_BY'  => 'p.post_time DESC', // Show latest posts on top
		);
		
		$activity_feed = $this->db->sql_build_query('SELECT', $activity_feed_ary);
		$activity_feed_result = $this->db->sql_query_limit($activity_feed, 5); // Only get last five posts
		
		while($af_row = $this->db->sql_fetchrow($activity_feed_result))
		{	
			$topic_id	= $af_row['topic_id'];
			$post_id	= $af_row['post_id'];
			$post_date	= $this->user->format_date($af_row['post_time']);
			$post_url 	= append_sid("{$this->phpbb_root_path}viewtopic.{$this->phpEx}", 't=' . $topic_id . '&amp;p=' . $post_id) . '#p' . $post_id;
			
			// Parse the posts
			$af_row['bbcode_options'] = (($af_row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
			(($af_row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
			(($af_row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
			$text = generate_text_for_display($af_row['post_text'], $af_row['bbcode_uid'], $af_row['bbcode_bitfield'], $af_row['bbcode_options']);
			
			// Set a max length for the post to display 
			$cutoff = ' …';
			$text = ((strlen($text)) > 200) ? (mb_substr($text, 0, 200) . $cutoff) : $text;
			
			$this->template->assign_block_vars('af', array(
				'SUBJECT'		=> $af_row['post_subject'],
				'TEXT'			=> $text,
				'TIME'			=> $post_date,
				'URL'			=> $post_url,
			));
		}
		
		$this->db->sql_freeresult($activity_feed_result); // Master gave Dobby a sock, now Dobby is free!
		
		/***************
		* TOTAL TOPICS *
		***************/
		$tt = 'SELECT COUNT(topic_poster) AS topic_author_count
				FROM '. TOPICS_TABLE .'
				WHERE topic_poster = '. $user_id;
		$total_topics_result = $this->db->sql_query($tt);
		$total_topics = (int) $this->db->sql_fetchfield('topic_author_count');
		$this->db->sql_freeresult($total_topics_result); // Master gave Dobby a sock, now Dobby is free!
				
		/***************
		* FRIENDS LIST *
		***************/
		$sql_friend = array(
			'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_colour, MAX(s.session_time) as online_time, MIN(s.session_viewonline) AS viewonline',

			'FROM'		=> array(
				USERS_TABLE		=> 'u',
				ZEBRA_TABLE		=> 'z',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(SESSIONS_TABLE => 's'),
					'ON'	=> 's.session_user_id = z.zebra_id',
				),
			),

			'WHERE'		=> 'z.user_id = ' . $user_id . '
				AND z.friend = 1
				AND u.user_id = z.zebra_id',

			'GROUP_BY'	=> 'z.zebra_id, u.user_id, u.username_clean, u.user_colour, u.username',

			'ORDER_BY'	=> 'u.username_clean ASC',
		);

		$sql_friend_list = $this->db->sql_build_query('SELECT_DISTINCT', $sql_friend);
		$friend_result = $this->db->sql_query($sql_friend_list);

		while ($friend_row = $this->db->sql_fetchrow($friend_result))
		{
			$this->template->assign_block_vars('friends', array(
				'USERNAME'	=> get_username_string('full', $friend_row['user_id'], $friend_row['username'], $friend_row['user_colour'])
			));
		}
		$this->db->sql_freeresult($friend_result); // Master gave Dobby a sock, now Dobby is free!
		
		/*******
		* WALL *
		*******/
		// INSERTING A WALL POST
		add_form_key('postwall');
		$sendwall = (isset($_POST['sendwall'])) ? true : false;
		
		if($sendwall)
		{
			if(check_form_key('postwall'))
			{
				$msg_text = $this->request->variable('msg_text', '', true);
				$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
				$allow_bbcode = $allow_urls = $allow_smilies = true;
				generate_text_for_storage($msg_text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
				$msg_time = time();

				$wall_ary = array(
					'user_id'			=> $user_id,
					'poster_id'			=> $this->user->data['user_id'],
					'msg'				=> $msg_text,
					'msg_time'			=> (int) $msg_time,
					'bbcode_uid'        => $uid,
					'bbcode_bitfield'   => $bitfield,
					'bbcode_options'	=> $options,
				);

				$insertwall = 'INSERT INTO ' . $this->wall_table . ' ' . $this->db->sql_build_array('INSERT', $wall_ary);
								
				$this->db->sql_query($insertwall);
			}
			else
			{
				trigger_error($this->user->lang['FORM_INVALID']);
			}
		}
		
		// DISPLAYING WALL POSTS
		$getwall_ary = array(
				'SELECT'	=> 'w.*, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height',

				'FROM'		=> array(
					$this->wall_table		=> 'w',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(USERS_TABLE => 'u'),
						'ON'	=> 'u.user_id = w.poster_id',
					),
				),

				'WHERE'		=> 'w.user_id = ' . $user_id,

				'ORDER_BY'	=> 'w.msg_id DESC',
		);
		
		$getwall = $this->db->sql_build_query('SELECT_DISTINCT', $getwall_ary);
		$wallresult = $this->db->sql_query_limit($getwall, 10); // Only get latest 10 wall posts
		
		while ($wall = $this->db->sql_fetchrow($wallresult))
		{
			$wall_msg = generate_text_for_display($wall['msg'], $wall['bbcode_uid'], $wall['bbcode_bitfield'], $wall['bbcode_options']); // Parse wall message text
			$msg_id = $wall['msg_id'];
			$msg_time = $this->user->format_date($wall['msg_time']);
			
			$this->template->assign_block_vars('wall', array(
				'MSG'				=> $wall_msg, 
				'MSG_ID'			=> $wall['msg_id'],
				'MSG_TIME'			=> $msg_time,
				'POSTER'			=> get_username_string('full', $wall['poster_id'], $wall['username'], $wall['user_colour']),
				'POSTER_AVATAR'		=> phpbb_get_user_avatar($wall),
				'S_HIDDEN_FIELDS'	=> build_hidden_fields(array(
										'deletewallid'		=> $wall['msg_id'],
										)),
			));
		}
		
		$this->db->sql_freeresult($wallresult); // Master gave Dobby a sock, now Dobby is free!
		
		// DELETE WALL POST
		$deletewall = (isset($_POST['deletewall'])) ? true : false;
		if($deletewall)
		{
			if (confirm_box(true))
			{
				$deletewallid = request_var('deletewallid', 0);
				$delete_msg = 'DELETE FROM '. $this->wall_table .'
								WHERE msg_id = '. $deletewallid;
				
				$this->db->sql_query($delete_msg);
				
				$msg_deleted_redirect = append_sid("{$this->phpbb_root_path}memberlist.{$this->phpEx}", "mode=viewprofile&amp;u=". $user_id ."#wall");
				$message = $this->user->lang['CONFIRM_WALL_DEL'] . '<br /><br />' . sprintf($this->user->lang['RETURN_WALL'], '<a href="' . $msg_deleted_redirect . '">', $username, '</a>');
				meta_refresh(3, $msg_deleted_redirect);
				trigger_error($message);
			}
			else 
			{
				$s_hidden_fields = build_hidden_fields(array(
					'deletewall'		=> true,
					'deletewallid' 	=> request_var('deletewallid', 0),
				));
					
				confirm_box(false, $this->user->lang['CONFIRM_WALL_DEL_EXPLAIN'], $s_hidden_fields);
			}
		}
		
		/***********************
		* Let's set some links *
		***********************/
		$post_wall_action 	= append_sid("{$this->phpbb_root_path}memberlist.{$this->phpEx}", "mode=viewprofile&amp;u=". $user_id); // Needed for wall form
		$total_topics_url	= append_sid("{$this->phpbb_root_path}search.{$this->phpEx}", 'author_id=' . $user_id . '&amp;sr=topics'); // Link to search URL for user's topics

		/****************************
		* ASSIGN TEMPLATE VARIABLES *
		****************************/
		$this->template->assign_vars(array(
			'TOTAL_TOPICS'			=> $total_topics,
			'PROFILE_VIEWS'			=> $member['user_profile_views'],
			'NO_WALL_POSTS'			=> sprintf($this->user->lang['FIRST_POST_WALL'], '<strong>' . $username .'</strong>'),
			'USER_NO_POSTS'			=> sprintf($this->user->lang['USER_NO_POSTS'], '<strong>' . $username .'</strong>'),
			
			'U_SEARCH_USER_TOPICS'	=> $total_topics_url,
			
			'S_POST_WALL' 			=> $post_wall_action,
		));

	}

	public function ucp_profile_modify_profile_info($event)
	{
		$data = $event['data'];
		$submit = $event['submit'];
		
		if ($submit)
		{
			$coverphoto = request_var('coverphoto', '');
			
			if (!preg_match('#^' . get_preg_expression('url') . '$#iu', $coverphoto))
			{
				$coverphoto_error_redirect = append_sid("{$this->phpbb_root_path}ucp.{$this->phpEx}", "i=ucp_profile&amp;mode=profile_info");
				$message = $this->user->lang['NOT_VALID_URL'] . '<br /><br />' . sprintf($this->user->lang['RETURN_PROFILE_INFO'], '<a href="' . $coverphoto_error_redirect . '">', '</a>');
				trigger_error($message);
			}
			else
			{			
				$update_cover_photo = 'UPDATE ' . USERS_TABLE . '
										SET user_coverphoto = "' . $this->db->sql_escape($coverphoto) . '"
										WHERE user_id = ' . $this->user->data['user_id'];
				$this->db->sql_query($update_cover_photo);
			}
		}
			
		$this->template->assign_vars(array(
				'COVERPHOTO'			=> $this->user->data['user_coverphoto'],
		));
	}	
}
