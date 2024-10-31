<?php
class AttendanceUser extends AttendanceClass {

	public function __construct(){

		parent::__construct();

	}
	// プラグインメニュー
	public function menuViews($type=''){

		global $am_plugin_user_data;
		$user_level = (isset($am_plugin_user_data['level'])) ? $am_plugin_user_data['level']: 'guest';

		if($user_level!='guest' && $user_level!='administrator'){
			if($type=='1'){
				// メニュー表示
				add_menu_page('出勤の投稿', '出勤の投稿', $user_level, 'attendance-management-user-post.php', array('AttendanceUser', 'postPage'));
			}else{
				// POST処理
				if(isset($_POST) && isset($_POST['write']) && $_POST['write']=='1'){
					self::submitTime();
				}
				// メニュー表示
				add_menu_page('出勤・勤怠プラグイン', '出勤・勤怠プラグイン', $user_level, 'attendance-management-user-view.php', array('AttendanceUser', 'adminPage'));
				add_submenu_page('attendance-management-user-view.php', '出勤・勤怠の一覧', '出勤・勤怠の一覧', $user_level, 'attendance-management-user-list.php', array('AttendanceUser', 'listPage'));
				add_submenu_page('attendance-management-user-view.php', '出勤・勤怠の新規作成', '出勤・勤怠の新規作成', $user_level, 'attendance-management-user-post.php', array('AttendanceUser', 'postPage'));
				add_submenu_page('attendance-management-user-view.php', 'ヘルプページ', 'ヘルプページ', $user_level, 'attendance-management-user-help.php', array('AttendanceUser', 'helpPage'));
				// メニューに非表示するページ
				add_submenu_page('attendance-management-user-list.php', '出勤・勤怠の編集', null, $user_level, 'attendance-management-user-write.php', array('AttendanceUser', 'writePage'));
			}
		}

	}
	// Page はじめに
	public function adminPage(){

		include_once(OSAM_PLUGIN_INCLUDE_FILES.'/user-adminPage.php');

	}
	// Page　出勤・勤怠の新規作成
	public function postPage(){

		global $am_plugin_user_data;
		$users = self::getMember($am_plugin_user_data['ID']);
		$message = self::updateMessage();
		self::_postPage($message, $users);

	}
	// Page　出勤・勤怠の一覧
	public function listPage(){

		global $am_plugin_user_data;
		$message = '';
		$users_data = parent::getMember($am_plugin_user_data['ID']);
		self::_listPage($message, $users_data, '1');

	}
	// Page　出勤・勤怠の編集
	public function writePage(){

		global $am_plugin_user_data;
		$data = self::working_get_data();
		$message = self::updateMessage();
		$form_html = self::post_form_page('', '1', $data['form_arr']);
		$form_user_html = $data['user_form'];
		$form_day_html = $data['day_form'];
		$form_message = $data['message'];
		$break_selected = $data['break_selected'];
		$over_selected = $data['over_selected'];
		include_once(OSAM_PLUGIN_INCLUDE_FILES."/user-writePage.php");

	}
	// Page　ヘルプページ
	public function helpPage(){

		include_once(OSAM_PLUGIN_INCLUDE_FILES."/user-helpPage.php");

	}
	/*
	*  メッセージ
	*/
	private function updateMessage(){

		$return_data = '';

		if(isset($_GET) && isset($_GET['msg'])){
			switch($_GET['msg']){

				case "ok":
					$return_data .= "更新しました<br />";
					break;
				case "error":
					$return_data .= "更新に失敗しました<br />";
					break;

			}
		}

		$return_data .= self::_updateMessage();

		return $return_data;

	}

}
?>