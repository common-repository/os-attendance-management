<?php
class AttendanceMain extends AttendanceClass {

	public function __construct(){

		parent::__construct();
		// CSS
		add_action('plugins_loaded', array('AttendanceMain', 'public_css_read'));
		// 表示のショートコード
		add_shortcode('attendance', array('AttendanceMain', 'viewMode'));

	}
	// ショートコードの処理
	public static function viewMode($atts, $content=null){


		extract(shortcode_atts(array(
			'uid' => '', 'type' => '1', 'day' => '',
		), $atts));
		// 処理用に配列にする
		$arr = array(
			'uid'=>$uid, 'type'=>$type, 'day'=>$day,
		);
		$content = do_shortcode(self::shortcode_view($content, $arr));

		return $content;

	}
	//
	private function shortcode_view($content='', $arr=array()){

		global $am_plugin_option_data; // オプションデータ
		global $am_plugin_user_data; // ユーザデータ

		// 時間表示
		$cl = self::clockText($am_plugin_option_data['clock']);

		// 初期値
		$view = '';
		$sql_arr = array();
		$uid = trim($arr['uid']);
		$user_data = self::member_data();

		// ユーザ指定
		if(empty($uid)){

			$where = "`user_id`!='0'";

		}else{

			$user_where = '';
			$uids = explode(",", rtrim($uid, ","));
			$uid_count = count($uids);

			foreach($uids as $u){
				$user_where .= "`user_id`= %d OR ";
				$sql_arr[] = $u;
			}

			$where = "( ".rtrim($user_where, " OR ")." )";

		}

		// 期間指定の処理
		if(empty($arr['day'])){
			$now = date_i18n("Y-m-d");
		}else{
			$now = self::sql_escape($arr['day']);
		}

		switch($am_plugin_option_data['view-list']){
			case '2':
				$st_time = $now.' 00:00:00';
				$end = date("Y-m-d", strtotime($st_time." +2 week"));
				$end_time = $end.' 00:00:00';
				break;
			case '3':
				$st_time = $now.' 00:00:00';
				$end = date("Y-m-d", strtotime($st_time." +3 week"));
				$end_time = $end.' 00:00:00';
				break;
			case 'm':
				$st_time = $now.' 00:00:00';
				$end = date("Y-m-d", strtotime($st_time." +1 month"));
				$end_time = $end.' 00:00:00';
				break;
			default:
				$st_time = $now.' 00:00:00';
				$end = date("Y-m-d", strtotime($st_time." +1 week"));
				$end_time = $end.' 00:00:00';
		}

		$where .= " AND '".$st_time."' <= `date` AND `date` < '".$end_time."'";

		// データ取得
		$sql = "SELECT * FROM `".OSAM_PLUGIN_TABLE_NAME."` WHERE ".$where." AND `status`='0' ORDER by `date` DESC";
		$data = self::sql_get($sql, $sql_arr);

		if(!empty($data[0])){

			if($am_plugin_user_data['level']=='administrator'){
				$php_url = 'attendance-management-write.php';
			}else{
				$php_url = 'attendance-management-user-write.php';
			}

			// まずはデータ整理
			$view_arr = array();
			//
			foreach($data as $d){

				$did = $d->data_id;
				$uid = $d->user_id;
				$name = $user_data[$uid]['name'];
				$date = explode(" ", $d->date);
				$now_day = $date[0];

				if(isset($uid_count) && $uid_count=='1'){
					$view_html = '<div class="person">';
				}else{
					$view_html = '<div class="person"><p class="name">'.$name.'</p>';
				}

				if(($am_plugin_user_data['ID']==$uid && $am_plugin_option_data['time_write']=='user') || $am_plugin_user_data['level']=='administrator'){
					$write_tag = '<p class="write"><a href="'.admin_url('/').'admin.php?page='.$php_url.'&did='.$did.'" title="この勤怠を編集">編集</a></p>';
				}else{
					$write_tag = '';
				}

				$view_html .= '<p class="time">'.self::hour_format($d->start_time, $d->start_i_time, $cl).' -- '.self::hour_format($d->finish_time, $d->finish_i_time, $cl).'</p>'.$write_tag.'<p class="clear"> </p></div>';

				$view_arr[$now_day][] = $view_html;

			}

			// 勤怠が入っていない曜日も処理する
			$view = '';
			$time = $now." 00:00:00";
			//
			for($i=0; $i<32; $i++){

				if($i==0){
					$check_day = date("Y-m-d", strtotime($time));
					$week = date("N", strtotime($time));
				}else{
					$check_day = date("Y-m-d", strtotime($time." +".$i." day"));
					$week = date("N", strtotime($time." +".$i." day"));
				}

				$w_arr = self::dayOfTheWeek($week);

				if($check_day==$end){
					break;
				}

				// 存在しなければ空のデータ作成
				if(empty($view_arr[$check_day])){
					$_view = '<div class="person"><p> - </p></div>';
				}else{
					$_view = '';
					// 処理
					$values = $view_arr[$check_day];
					//
					foreach($values as $v){
						$_view .= $v;
					}
				}

				$day_ex = explode("-", $check_day);
				$view .= "<div class=\"at_date\">{$day_ex[0]}年{$day_ex[1]}月{$day_ex[2]}日".$w_arr[2]."</div>".$_view;

			}

			$view = "<div class=\"schedule\">".$view."</div>";

		}

		return $content.$view;

	}
	// 時間表示
	// 基本設定が12時間表示に設定されているとき、表示変更
	private function hour_format($htime='', $itime='', $comma=array(':', '')){

		global $am_plugin_option_data; // オプションデータ

		if($am_plugin_option_data['time_view']=='1' || $am_plugin_option_data['time_view']=='2'){
			$change_time = $htime.":".$itime;
			$return_time = date("A g".$comma[0]."i", strtotime($change_time)).$comma[1];
			if($am_plugin_option_data['time_view']=='1'){
				$return_time = str_replace(array('AM','PM'), array('午前','午後'), $return_time);
			}
		}else{
			$return_time = sprintf('%02d', $htime).$comma[0].sprintf('%02d', $itime).$comma[1];
		}

		return $return_time;

	}
	// 全ユーザ取得
	private function member_data(){

		$data = array();
		$users = get_users(array('orderby'=>'ID'));

		foreach($users as $u){

			$id = $u->ID;
			$data[$id]['url'] = $u->data->user_url;
			$data[$id]['login'] = $u->data->user_login;
			$data[$id]['nicename'] = $u->data->user_nicename;
			$data[$id]['email'] = $u->data->user_email;
			$data[$id]['registered'] = $u->data->user_registered;
			$data[$id]['status'] = $u->data->user_status;
			$data[$id]['name'] = $u->data->display_name;
			$data[$id]['roles'] = $u->roles[0];

		}

		return $data;

	}
	// 公開用のCSSを割り当て
	public static function public_css_read(){

		if(!stristr($_SERVER["REQUEST_URI"], "wp-admin")){

			add_action('wp_head', array('AttendanceMain', 'public_css_action'));

		}

	}
	public static function public_css_action(){

		$src = plugins_url('attendance.css', __FILE__);
		wp_register_style('attendance', $src);
		wp_enqueue_style('attendance');

	}

}
?>
