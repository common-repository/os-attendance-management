<?php
class AttendanceAdmin extends AttendanceClass {

	public function __construct(){

		parent::__construct();
		// まず実行
		add_action('admin_init', array('AttendanceAdmin', 'actionAdminInit'));
		// POST処理
		//add_action('admin_init', self::admin_post());
		add_action('admin_init', array('AttendanceAdmin', 'admin_post'));
		// 管理画面メニュー
		add_action('admin_menu', array('AttendanceAdmin', 'menuViews'));

	}
	// プラグインメニュー
	public static function menuViews(){

		global $am_plugin_option_data; // オプションデータ
		global $am_plugin_user_data; // ユーザデータ

		// CSS
		add_action('admin_init', array('AttendanceClass', 'admin_css_read'));

		// ゲストは管理画面を表示させない。トップページへ
		if(isset($am_plugin_user_data['level']) && $am_plugin_user_data['level']=='guest'){

			wp_safe_redirect(home_url('/'));
			exit;

		// 登録ユーザのみのメニュー表示、処理
		}elseif(isset($am_plugin_option_data['time_write']) && $am_plugin_user_data['level']!='administrator'){

			global $attendanceUser;
			switch($am_plugin_option_data['time_write']){
				case 'user':
					$attendanceUser->menuViews();
					break;
				case 'user-post':
					$attendanceUser->menuViews('1');
					break;
			}

		// 管理者のときのメニュー表示、処理
		}else{

			$page = (isset($_REQUSET['page'])) ? $_REQUSET['page']: '';
			// POST処理
			if(!empty($_POST)){
				if(stristr($page, "attendance-management")){
					if(isset($_POST['format'])){
						self::formatPlugin();
					}elseif(isset($_POST['option'])){
						self::optionPost();
					}
				}
			}

			// メニュー表示
			add_menu_page('出勤・勤怠プラグイン', '出勤・勤怠プラグイン', 'administrator', 'attendance-management-view.php', array('AttendanceAdmin', 'adminPage'));
			add_submenu_page('attendance-management-view.php', '出勤・勤怠管理の基本設定', '基本設定', 'administrator', 'attendance-management-options.php', array('AttendanceAdmin', 'optionPage'));
			add_submenu_page('attendance-management-view.php', '出勤・勤怠の一覧', '出勤・勤怠の一覧', 'administrator', 'attendance-management-list.php', array('AttendanceAdmin', 'listPage'));
			add_submenu_page('attendance-management-view.php', '出勤・勤怠の新規作成', '出勤・勤怠の新規作成', 'administrator', 'attendance-management-post.php', array('AttendanceAdmin', 'postPage'));
			add_submenu_page('attendance-management-view.php', 'プロフ（スキル）設定', 'プロフ（スキル）設定', 'administrator', 'attendance-management-prof-option.php', array('AttendanceProf', 'profOptionPage'));
			add_submenu_page('attendance-management-view.php', 'ヘルプページ', 'ヘルプページ', 'administrator', 'attendance-management-help.php', array('AttendanceAdmin', 'helpPage'));
			// メニューに非表示するページ
			add_submenu_page('attendance-management-list.php', '出勤・勤怠の編集', null, 'administrator', 'attendance-management-write.php', array('AttendanceAdmin', 'writePage'));
			add_submenu_page('attendance-management-options.php', 'プラグインの初期化', null, 'administrator', 'attendance-management-format.php', array('AttendanceAdmin', 'formatPage'));
			add_submenu_page('attendance-management-options.php', '利用規約', null, 'administrator', 'attendance-management-agreement.php', array('AttendanceAdmin', 'agreementPage'));

		}

	}
	/*
	*  ページビュー
	*/
	// Page はじめに
	public function adminPage(){

		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-adminPage.php");

	}
	// Page 基本設定
	public function optionPage(){

		$data = $GLOBALS['am_plugin_option_data'];
		$data_list = (isset($data['list'])) ? $data['list'] : '';
		$data_csv = (isset($data['csv'])) ? $data['csv'] : '';
		$message = self::updateMessage();
		$time_view_selected = parent::html_array_select($data['time_view'], '3', array('0', '1', '2'));
		$time_write_selected = parent::html_array_select($data['time_write'], '3', array('admin', 'user', 'user-post'));
		$view_list_checked = parent::html_array_check($data['view-list'], '4', array('1', '2', '3', 'm'));
		$admin_list_checked = parent::html_array_check($data['admin-list'], '4', array('1', '2', '3', 'm'));
		$clock_checked = parent::html_array_check($data['clock'], '2', array('0', '1'));

		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-optionPage.php");

	}
	// Page　出勤・勤怠の新規作成
	public function postPage(){

		global $am_plugin_user_data;
		$users = self::getMember();
		$message = self::updateMessage();
		self::_postPage($message, $users);

	}
	// Page　ヘルプページ
	public function helpPage(){

		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-helpPage.php");

	}
	// Page　出勤・勤怠の一覧
	public function listPage(){

		$message = '';
		$users_data = parent::getMember();
		self::_listPage($message, $users_data);

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
	// Page 利用規約
	public function agreementPage(){

		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-agreementPage.php");

	}
	// Page　初期化するかどうか確認するページ
	public function formatPage(){

		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-formatPage.php");

	}
	/*
	*  設定ページ
	*/
	// プラグインが初期化されたときに実行する
	private function formatPlugin(){

		delete_option(OSAM_PLUGIN_DATA_NAME);
		// テーブルが存在するか確認
		$table_exists = AttendanceInstall::show_table(OSAM_PLUGIN_TABLE_NAME);
		// テーブルが存在すればデータ削除、なければテーブルを新規作成
		if($table_exists){

			$sql = "DELETE FROM ".OSAM_PLUGIN_TABLE_NAME.";";
			self::sql_query($sql);

		}else{

			AttendanceInstall::newTable();

		}

		AttendanceInstall::firstOption();

		// リダイレクト
		if(get_option(OSAM_PLUGIN_DATA_NAME)){
			wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-options.php&msg=format-ok');
		}else{
			wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-options.php&msg=format-error');
		}
		exit;

	}
	/*
	*  メニューを呼び出す前に実行
	*/
	public static function actionAdminInit(){

		global $am_plugin_user_data;

		// 管理者権限のときのみ実行
		if(isset($am_plugin_user_data['level']) && $am_plugin_user_data['level']=='administrator'){

			if(isset($_GET) && isset($_GET['csv_dl'])){
				self::csv_export();
			}

		}

		// jQuery
		wp_enqueue_script('jquery');

	}
	// CSV出力
	private function csv_export(){

		AttendanceCsvClass::csv_export();

	}
	/*
	*  メッセージ
	*/
	public function updateMessage(){

		$return_data = '';

		if(isset($_GET) && isset($_GET['msg'])){
			switch($_GET['msg']){

				case "format-ok":
					$return_data .= "初期化しました<br />";
					break;
				case "format-error":
					$return_data .= "初期化に失敗しました<br />";
					break;
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
	/*
	*  POST処理
	*/
	// ユーザ、管理者の共通のPOST処理
	public static function admin_post(){

		global $am_plugin_user_data;

		if(isset($_GET) && isset($_GET['page'])){
			$page = (isset($_REQUEST['page'])) ? $_REQUEST['page']: '';
			//
			if(isset($_POST) && is_array($_POST)){
				$post = $_POST;
			}else{
				$post = array();
			}
			//
			if(is_string($page) && stristr($page, "attendance-management")){
				//
				if(!empty($_POST['option']) && $_POST['option']=='option'){
					self::optionPost();
				}
				//
				if(isset($am_plugin_user_data['level']) && $am_plugin_user_data['level']=='guest'){
					wp_safe_redirect(home_url('/'));
					exit;
				}else{
					//
					if(isset($am_plugin_user_data['level']) && $am_plugin_user_data['level']=='administrator'){
						$insert_url = 'attendance-management-post.php';
						$update_url = 'attendance-management-write.php';
					}else{
						$insert_url = 'attendance-management-user-post.php';
						$update_url = 'attendance-management-user-write.php';
					}
					//
					if(!empty($post['new'])){
						if(isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce' )){
							$insert_id = self::post_insert();
							// リダイレクト処理
							if(!empty($insert_id)){
								wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=insert-ok');
								exit;
							}else{
								wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=insert-ng');
								exit;
							}
						}else{
							wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=nonce-error');
							exit;
						}
					//
					}elseif(!empty($post['Delete'])){
						if(isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce' )){
							$delete_id = self::post_delete();
							// リダイレクト処理
							if(!empty($delete_id)){
								wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=delete-ok');
								exit;
							}else{
								wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=delete-ng');
								exit;
							}
						}else{
							wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=nonce-erro');
							exit;
						}
					}elseif(!empty($post['write'])){
						if(isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce' )){
							$update_id = self::post_write();
							// リダイレクト処理
							if(!empty($update_id)){
								wp_safe_redirect(admin_url('/').'admin.php?page='.$update_url.'&did='.$update_id.'&msg=write-ok');
								exit;
							}else{
								wp_safe_redirect(admin_url('/').'admin.php?page='.$update_url.'&msg=write-ng');
								exit;
							}
						}else{
							wp_safe_redirect(admin_url('/').'admin.php?page='.$insert_url.'&msg=nonce-erro');
							exit;
						}
					}elseif(!empty($post['list_search']) || !empty($post['search_back_month']) || !empty($post['search_now_month']) || !empty($post['search_next_month'])){
						self::list_search_redirect();
					}

				}
			}

		}

	}
	// 管理者権限で管理画面のとき、編集を可能にする
	private function admin_post_write(){

		add_action('admin_init', self::post_write());

	}
	// 基本設定、POSTの処理
	private function optionPost(){

		if(isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce' )){
			$update_array = parent::arrayData($_POST);
			update_option(OSAM_PLUGIN_DATA_NAME, $update_array);

			// リダイレクト
			if(get_option(OSAM_PLUGIN_DATA_NAME)){
				wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-options.php&msg=ok');
			}else{
				wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-options.php&msg=error');
			}
		}else{
			wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-options.php&msg=nonce-error');
		}
		exit;

	}

}