<?php
/*
*  インストールClass
*/
class AttendanceInstall{

	public function activationPlugin(){

		// テーブルが存在するか確認
		$table_exists = self::show_table(OSAM_PLUGIN_TABLE_NAME);
		if(!$table_exists){
			self::newTable();
		}
		$table_exists = self::show_table(OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE);
		if(!$table_exists){
			self::newTable2();
		}
		// オプションがなければ作成
		if(!get_option(OSAM_PLUGIN_DATA_NAME)){
			self::firstOption();
		}

	}
	// 初期設定
	public function firstOption(){

		// 設定を初期化
		$arr = array(
				'time_view' => '0', 'time_write' => 'admin', 'license' => 'free', 'view-list' => '1', 'admin-list' => '2', 'clock'=>'1',
			);
		update_option(OSAM_PLUGIN_DATA_NAME, $arr);

	}
	// テーブルの存在チェック
	public function show_table($tbl){

		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl));

	}
	// プラグイン用のテーブルを新規作成
	public function newTable(){

		global $wpdb;
		// テーブルを作成
		/*
		*  data_id データid、 user_id WPの登録ユーザid、 text テキスト、 status 状態 0=削除　1=実働時間　2＝予定時間　3＝休み、
		*  start_time 開始時間（24時間表記）、 start_i_time 開始分、 finish_time 終了時間（24時間表記）、 finish_i_time 終了分、
		*  break_～ 休憩時間、 break_point 休憩ありなし
		*  over_～ 残業時間、over_point 残業ありなし
		*  date 稼動する年月日、 create_time 作成日、 update_time 更新日、
		*/
		$charset = defined("DB_CHARSET") ? DB_CHARSET : "utf8";
		$sql = "CREATE TABLE " .OSAM_PLUGIN_TABLE_NAME. " (\n".
				"`data_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n".
				"`user_id` bigint(20) UNSIGNED DEFAULT '0' NOT NULL,\n".
				"`text` text,\n".
				"`status` int(2) UNSIGNED DEFAULT '0' NOT NULL,\n".
				"`start_time` int(2),\n".
				"`start_i_time` int(2),\n".
				"`finish_time` int(2),\n".
				"`finish_i_time` int(2),\n".
				"`break_start_time` int(2),\n".
				"`break_start_i_time` int(2),\n".
				"`break_finish_time` int(2),\n".
				"`break_finish_i_time` int(2),\n".
				"`break_point` int(1) UNSIGNED DEFAULT '1' NOT NULL,\n".
				"`over_start_time` int(2),\n".
				"`over_start_i_time` int(2),\n".
				"`over_finish_time` int(2),\n".
				"`over_finish_i_time` int(2),\n".
				"`over_point` int(1) UNSIGNED DEFAULT '0' NOT NULL,\n".
				"`date` DATETIME,\n".
				"`create_time` DATETIME,\n".
				"`update_time` TIMESTAMP,\n".
				"UNIQUE(`data_id`)\n".
			") ENGINE = MyISAM DEFAULT CHARSET = ".$charset;
		self::sql_performs($sql);

	}
	public function newTable2(){

		global $wpdb;
		/*
		*  sop_id スキルオプションid  ||  sop_name 項目名
		*  sop_type 0=1行テキスト、1=改行テキスト（textarea）、2=複数テキスト  ||  sop_order  昇順（番号が若いものを上に表示）
		*  sop_group_flag 0=しない、1=グループ化 || sop_group_name  グループ名
		*  sop_input_inline 管理画面側のinput表示。0=ブロック表示、1=インライン表示
		*  sop_width  入力の幅  ||  sop_height  入力の高さ
		*  sop_view_inline 公開側のテキスト表示。0=ブロック表示、1=インライン表示
		*  delete_flag 削除フラグ、0=表示、1=削除
		*/
		$charset = defined("DB_CHARSET") ? DB_CHARSET : "utf8";
		$sql = "CREATE TABLE " .OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE. " (\n".
			"`sop_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n".
			"`sop_name` text,\n".
			"`sop_type` int(2) UNSIGNED DEFAULT '0' NOT NULL,\n".
			"`sop_order` int(3) UNSIGNED DEFAULT '0' NOT NULL,\n".
			"`sop_group_flag` int(2) UNSIGNED DEFAULT '0' NOT NULL,\n".
			"`sop_group_name` text,\n".
			"`sop_input_inline` int(2) UNSIGNED DEFAULT '0' NOT NULL,\n".
			"`sop_width` int(5) UNSIGNED DEFAULT '130' NOT NULL,\n".
			"`sop_height` int(5) UNSIGNED DEFAULT '25' NOT NULL,\n".
			"`sop_view_inline` int(2) UNSIGNED DEFAULT '0' NOT NULL,\n".
			"`sop_view_width` int(5) UNSIGNED DEFAULT '130' NOT NULL,\n".
			"`sop_view_height` int(5) UNSIGNED DEFAULT '25' NOT NULL,\n".
			"`delete_flag` int(1) UNSIGNED DEFAULT '0' NOT NULL,\n".
			"`create_time` DATETIME,\n".
			"`update_time` TIMESTAMP,\n".
			"UNIQUE(`sop_id`)\n".
		") ENGINE = MyISAM DEFAULT CHARSET = ".$charset;
		self::sql_performs($sql);

	}
	// sqlを操作するファイルを読み込み、sqlを実行
	public function sql_performs($sql=''){

		global $plugin_sqlfile_check;
		// 既に読み込まれていないければファイル読み込み
		if($plugin_sqlfile_check!='1'){
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$GLOBALS['plugin_sqlfile_check'] = 1; // 読み込みチェック
		}

		return dbDelta($sql);

	}

}
/*
*  下記はプラグインのインストール時に使用する関数など ==============
*/
// プラグイン有効化がなされたら
if(function_exists('register_activation_hook')){
	register_activation_hook(OSAM_FILE, array('AttendanceInstall', 'activationPlugin'));
}