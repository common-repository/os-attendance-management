<?php
/*
Plugin Name: 出勤・勤怠管理プラグイン
Plugin URI: http://lp.olivesystem.jp/plugin-am
Description: WordPressで出勤・退勤などの勤怠（勤務状況）が管理できるプラグインです
Version: 1.3.21
Author: OLIVESYSTEM（オリーブシステム）
Author URI: http://lp.olivesystem.jp/
*/
if(!isset($wpdb)){
	global $wpdb;
}
// 現在のプラグインバージョン
define('OSAM_PLUGIN_VERSION','1.3.2');
// DBにデータを保存する項目名
define('OSAM_PLUGIN_DATA_NAME','os_attendance_management_Plugin');
// テーブル名
define('OSAM_PLUGIN_TABLE_NAME', $wpdb->prefix.'os_attendance_plugin_data');
define('OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE', $wpdb->prefix.'os_attendance_plugin_skillsheet_option_data'); // スキルシート設定テーブル
// このファイル
define('OSAM_FILE', __FILE__);
// プラグインのディレクトリ
define('OSAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
// テキストメインのPHPファイルをいれているディレクトリ
define('OSAM_PLUGIN_INCLUDE_FILES', OSAM_PLUGIN_DIR.'include_files');
// 時刻を日本時間に設定
date_default_timezone_set('Asia/Tokyo');
// グローバル変数
$am_plugin_option_data = ''; // オプションデータ用
$am_plugin_user_data = ''; // ログインユーザデータ
$plugin_sqlfile_check = ''; // SQL実行時に使用
// インストールclass
include_once(OSAM_PLUGIN_DIR."class/installClass.php");
// 共通class
include_once(OSAM_PLUGIN_DIR."attendanceClass.php");
//
include_once(OSAM_PLUGIN_DIR."class/sqlClass.php");
include_once(OSAM_PLUGIN_DIR."class/csvClass.php");
// 表示側
include_once(OSAM_PLUGIN_DIR."attendanceMainClass.php");
$attendanceMain = new AttendanceMain();
// 管理画面側
include_once(OSAM_PLUGIN_DIR."attendanceUserClass.php");
$attendanceUser = new AttendanceUser();
include_once(OSAM_PLUGIN_DIR."attendanceProfClass.php");
$attendanceProf = new AttendanceProf();
include_once(OSAM_PLUGIN_DIR."attendanceAdminClass.php");
$attendanceAdmin = new AttendanceAdmin();
?>
