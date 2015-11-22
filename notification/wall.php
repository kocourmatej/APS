<?php
/**
*
* @package Notification Test
* @copyright (c) 2015 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace posey\aps\notification;
/**
* Test notifications class
* This class handles notifications for Notification Test
*
* @package notifications
*/
class wall extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;
	/**
	* Notification Type Boardrules Constructor
	*
	* @param \phpbb\user_loader $user_loader
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\cache\driver\driver_interface $cache
	* @param \phpbb\user $user
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\controller\helper $helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	* @param string $notification_types_table
	* @param string $notifications_table
	* @param string $user_notifications_table
	* @return \phpbb\notification\type\base
	*/
	public function __construct(\phpbb\user_loader $user_loader, \phpbb\db\driver\driver_interface $db, \phpbb\cache\driver\driver_interface $cache, $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper, $phpbb_root_path, $php_ext, $notification_types_table, $notifications_table, $user_notifications_table)
	{
		$this->user_loader = $user_loader;
		$this->db = $db;
		$this->cache = $cache;
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->notification_types_table = $notification_types_table;
		$this->notifications_table = $notifications_table;
		$this->user_notifications_table = $user_notifications_table;
	}
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'posey.aps.notification.type.wall';
	}
	public static $notification_option = array(
		'lang'		=> 'WALL_NOTIFICATION_TYPE_OPTION',
		'group'		=> 'NOTIFICATION_GROUP_MISCELLANEOUS',
	);
	/**
	* Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		return true;
	}
	/**
	* Get the id of the notification
	*
	* @param array $data The data for the updated rules
	* @return int Id of the notification
	*/
	public static function get_item_id($wall_notification_data)
	{
		return (int) $wall_notification_data['msg_id'];
	}
	/**
	* Get the id of the parent
	*
	* @param array $data The data for the updated rules
	* @return int Id of the parent
	*/
	public static function get_item_parent_id($wall_notification_data)
	{
		// No parent
		return 0;
	}
	/**
	* Find the users who will receive notifications
	*
	* @param array $data The type specific data for the updated rules
	* @param array $options Options for finding users for notification
	* @return array
	*/
	public function find_users_for_notification($wall_notification_data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users'      => array(),
		), $options);

		$users = array((int) $wall_notification_data['user_id']);

		return $this->check_user_notification_options($users, $options);
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array();
	}
	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$username = $this->get_data('poster_name');
		$startof_msg_text = $this->get_data('notification_msg');
	
		return $this->user->lang('WALL_NOTIFICATION_TITLE', $username, $startof_msg_text);
	}
	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, "mode=viewprofile&amp;u=". $this->user->data['user_id'] ."#wall");
	}
	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return false;
	}
	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array();
	}
	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param array $data The data for the report
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($wall_notification_data, $pre_create_data = array())
	{
		$this->set_data('msg_id', $wall_notification_data['msg_id']);
		$this->set_data('user_id', $wall_notification_data['user_id']);
		$this->set_data('poster_name', $wall_notification_data['poster_name']);
		$this->set_data('notification_msg', $wall_notification_data['notification_msg']);
		
		return parent::create_insert_array($data, $pre_create_data);
	}
}