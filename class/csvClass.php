<?php
// CSVを操作するclass
class AttendanceCsvClass extends AttendanceClass {

	public function __construct(){

		parent::__construct();

	}
	// CSV出力
	public function csv_export(){

		// データ取得
		if(isset($_GET) && isset($_GET['csv_dl'])){
			$data_arr = self::get_list_sql($_GET);
			$now = date_i18n("Y-m-d"); // 現在時刻
			$csv_word = str_replace(array(": ", " / ", " ", "～"), array("", "_", "_", "から"), $data_arr['word']); // ファイル名に使用

			switch($_GET['csv_dl']){
				case '1':
					$csv_file = "勤怠一覧_".$now."_".$csv_word.'.csv';
					$csv_data = self::csv_data($data_arr['data']);
					break;
				case '2':
					$csv_file = "勤怠合計_".$now."_".$csv_word.'.csv';
					$csv_data = self::csv_data_total($data_arr['data']);
					break;
			}

			// エンコード
			$csv_data = mb_convert_encoding($csv_data, "sjis-win", 'UTF-8');
			// ヘッダー
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename={$csv_file}");
			// データの出力
			echo($csv_data);
			exit();
		}

	}
	// CSVデータ生成(一覧)
	private function csv_data($data=''){

		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		$options_list = (isset($options['csv'])) ? $options['csv'] : '';
		$user_arr = array();
		$csv_data = '"ユーザ名","勤務日",';
		// 業務
		if(!empty($options_list['work_start'])){ $csv_data .= '"業務開始時刻",'; }
		if(!empty($options_list['work_end'])){ $csv_data .= '"業務終了時刻",'; }
		if(!empty($options_list['work_time'])){ $csv_data .= '"業務時間",'; }
		// 休憩
		if(!empty($options_list['break_start'])){ $csv_data .= '"休憩開始時刻",'; }
		if(!empty($options_list['break_end'])){ $csv_data .= '"休憩終了時刻",'; }
		if(!empty($options_list['break_time'])){ $csv_data .= '"休憩時間",'; }
		// 残業
		if(!empty($options_list['overtime_start'])){ $csv_data .= '"残業開始時刻",'; }
		if(!empty($options_list['overtime_end'])){ $csv_data .= '"残業終了時刻",'; }
		if(!empty($options_list['overtime_time'])){ $csv_data .= '"残業時間",'; }
		// etc
		if(!empty($options_list['daywork'])){ $csv_data .= '"実働時間",'; }
		if(!empty($options_list['message'])){ $csv_data .= '"メッセージ",'; }
		$csv_data .= "\n";
		// foreach
		foreach($data as $d){
			$uid = $d->user_id;
			// ユーザデータ取得
			if(empty($user_arr[$uid])){
				$user = get_users(array('orderby'=>'ID','order'=>'ASC', 'include'=>$uid));
				$user_arr[$uid] = $user;
			}else{
				$user = $user_arr[$uid];
			}
			//
			$start_time = self::work_point(1, $d->start_time, $d->start_i_time);
			$finish_time = self::work_point(1, $d->finish_time, $d->finish_i_time);
			$work_time = self::time_plus(0, self::time_minus($finish_time, $start_time));
			// 休憩処理
			$break_start = self::work_point($d->break_point, $d->break_start_time, $d->break_start_i_time);
			$break_finish = self::work_point($d->break_point, $d->break_finish_time, $d->break_finish_i_time);
			$break_time = self::time_plus(0, self::time_minus($break_finish, $break_start));
			// 残業処理
			$over_start = self::work_point($d->over_point, $d->over_start_time, $d->over_start_i_time);
			$over_finish = self::work_point($d->over_point, $d->over_finish_time, $d->over_finish_i_time);
			$overtime_time = self::time_plus(0, self::time_minus($over_finish, $over_start));
			//
			$daywork = $work_time - $break_time - $overtime_time;
			$date_ex = explode(" ", $d->date);
			$csv_data .= '"'.$user[0]->data->display_name.'","'.$date_ex[0].'",';
			// 業務
			if(!empty($options_list['work_start'])){ $csv_data .= '"'.$start_time.'",'; }
			if(!empty($options_list['work_end'])){ $csv_data .= '"'.$finish_time.'",'; }
			if(!empty($options_list['work_time'])){ $csv_data .= '"'.$work_time.'H",'; }
			// 休憩
			if(!empty($options_list['break_start'])){ $csv_data .= '"'.$break_start.'",'; }
			if(!empty($options_list['break_end'])){ $csv_data .= '"'.$break_finish.'",'; }
			if(!empty($options_list['break_time'])){ $csv_data .= '"'.$break_time.'H",'; }
			// 残業
			if(!empty($options_list['overtime_start'])){ $csv_data .= '"'.$over_start.'",'; }
			if(!empty($options_list['overtime_end'])){ $csv_data .= '"'.$over_start.'",'; }
			if(!empty($options_list['overtime_time'])){ $csv_data .= '"'.$overtime_time.'H",'; }
			// etc
			if(!empty($options_list['daywork'])){ $csv_data .= '"'.$daywork.'H",'; }
			if(!empty($options_list['message'])){ $csv_data .= '"'.self::h($d->text).'",'; }
			$csv_data .= "\n";
		}

		return trim($csv_data);

	}
	// CSVデータ生成(合計)
	private function csv_data_total($data=''){

		$total_time = 0;
		$break_total_time = 0;
		$over_total_time = 0;
		$user_arr = array();
		$csv_data = '"ユーザ名","勤務時間","休憩時間","残業時間","実働時間"'."\n";

		// 加算処理
		foreach($data as $d){
			$uid = $d->user_id;
			// 個別計算用
			if(empty($user_arr[$uid])){
				$user = get_users(array('orderby'=>'ID','order'=>'ASC', 'include'=>$uid));
				$user_arr[$uid] = array(
					'name'=>$user[0]->data->display_name, 'work'=>'0', 'break'=>'0', 'over'=>'0',
				);
			}
			// 勤怠時間の加算
			$start_time = $d->start_time.":".$d->start_i_time;
			$finish_time = $d->finish_time.":".$d->finish_i_time;
			$work_hi = self::time_minus($finish_time, $start_time); // 時分をだす
			$user_arr[$uid]['work'] = self::time_plus($user_arr[$uid]['work'], $work_hi); // 個別
			$total_time = self::time_plus($total_time, $work_hi); // 総数
			// 休憩処理
			$break_start = self::work_point($d->break_point, $d->break_start_time, $d->break_start_i_time);
			$break_finish = self::work_point($d->break_point, $d->break_finish_time, $d->break_finish_i_time);
			$break_hi = self::time_minus($break_finish, $break_start);
			$user_arr[$uid]['break'] = self::time_plus($user_arr[$uid]['break'], $break_hi); // 個別
			$break_total_time = self::time_plus($break_total_time, $break_hi); // 総数
			// 残業処理
			$over_start = self::work_point($d->over_point, $d->over_start_time, $d->over_start_i_time);
			$over_finish = self::work_point($d->over_point, $d->over_finish_time, $d->over_finish_i_time);
			$over_hi = self::time_minus($over_finish, $over_start);
			$user_arr[$uid]['over'] = self::time_plus($user_arr[$uid]['over'], $over_hi); // 個別
			$over_total_time = self::time_plus($over_total_time, $over_hi); // 総数

		}

		// CSVデータ化
		foreach($user_arr as $u){

			$user_total_work = ($u['work'] + $u['over']) - $u['break'];
			$user_total_work = self::floor_point($user_total_work); // 小数第二位を切り捨て
			$csv_data .= '"'.$u['name'].'","'.$u['work'].'","'.$u['break'].'","'.$u['over'].'","'.$user_total_work.'"'."\n";

		}

		// 総数
		$total_work = ($total_time + $over_total_time) - $break_total_time;
		$total_work = self::floor_point($total_work); // 小数第二位を切り捨て
		$csv_data .= '"合計","'.$total_time.'","'.$break_total_time.'","'.$over_total_time.'","'.$total_work.'"';

		return trim($csv_data);

	}
	// 休憩、残業の処理
	private function work_point($point='0', $htime='00', $itime='00'){

		$_return = '';

		if($point=='1'){
			$_return = $htime.':'.$itime;
		}else{
			$_return = 0;
		}

		return $_return;

	}
	
}