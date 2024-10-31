<?php
// SQLを操作するclass
class AttendanceSqlClass extends AttendanceClass {

	public function __construct(){

		parent::__construct();

	}
	// テーブルの存在チェック
	public function show_table($tbl){

		return AttendanceInstall::show_table($tbl);

	}
	// sqlを操作するファイルを読み込み、sqlを実行
	public function sql_performs($sql=''){

		return AttendanceInstall::sql_performs($sql);

	}

}