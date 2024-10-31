<?php
class AttendanceProf extends AttendanceClass {

	public function __construct(){

		parent::__construct();
		// POST処理
		add_action('admin_init', array('AttendanceProf', 'admin_post'));
		// プロフィール編集画面
		add_filter('personal_options', array('AttendanceProf', 'custom_profile_fields'), 20, 1);
		// プロフィール更新時
		add_action('profile_update', array('AttendanceProf', 'update_custom_profile_fields'));
		add_action('edit_user_profile_update', array('AttendanceProf', 'update_custom_profile_fields'));
		// ショートコード表示
		add_shortcode('os-am-userprof', array('AttendanceProf', 'viewProf'));

	}
	/*
	*  ショートコード
	*/
	public function viewProf($atts, $content=null){

		extract(shortcode_atts(array(
			'uid'=>0,
		), $atts));
		//
		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		// 有効なら
		if(!empty($options['prof_skill_flag'])){
			self::sop_sheet_css();
			$content .= "\t\t<div id=\"os-am-skillsheet\">\n";
			$content .= self::sop_sheet_view($uid, 'shortcode');
			$content .= "\t\t</div>\n";
		}

		return $content;

	}
	//
	public function sop_sheet_css(){
		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		$css_type = (isset($options['sop_css_type'])) ? $options['sop_css_type']: 'gray';
		$font_color = '#000';
		$border_color = '#ccc';
		//
		switch($css_type){
			case 'orange':
				$color = '#F2BF72';
				$border_color = 'orange';
				break;
			case 'black':
				$color = '#000';
				$font_color = 'white';
				$border_color = '#696969';
				break;
			case 'pink':
				$color = '#FFE4E1';
				$border_color = 'pink';
				break;
			// gray
			default:
				$color = '#eee';
		}
		print '<style type="text/css">';
		print '#os-am-skillsheet .os-tbl .os-row .os-th, #os-am-skillsheet .os-tbl .os-row .os-td .label{';
		print 'background-color:'.$color.';color:'.$font_color.';';
		print '}';
		print '#os-am-skillsheet .os-tbl .os-row .os-th{';
		print 'border:1px solid '.$border_color.';';
		print '}';
		print '#os-am-skillsheet .os-tbl .os-row .os-td{';
		print 'border:1px solid '.$border_color.';';
		print '}';
		print '</style>';

	}
	/*
	*  管理者権限側
	*/
	// Page プロフ（スキル）ページ
	public function profOptionPage(){

		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		$sop_data = self::skillsheet_op_data();
		$message = AttendanceAdmin::updateMessage();
		$validation_msg = self::get_validation_msg();
		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-profOptionPage.php");

	}
	// POST処理
	public static function admin_post(){

		global $am_plugin_user_data;
		$user_data = $am_plugin_user_data;

		if(isset($_GET) && isset($_GET['page'])){
			if(stristr($_GET['page'], "attendance-management")){
				if(isset($user_data['level']) && $user_data['level']=='guest'){
					wp_safe_redirect(home_url('/'));
					exit;
				}elseif(isset($_POST)){
					if(isset($_POST['am_formname'])){
						if(isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wp_nonce' )){
							switch($_POST['am_formname']){
								case 'prof_option':
									$sop_post = (isset($_POST['sop_arr'])) ? $_POST['sop_arr']: '';
									$validation_msg = self::post_validation($sop_post);
									// POSTの検証ok
									if(empty($validation_msg)){
										$_return = self::prof_option_post($_POST);
										$msg = (!empty($_return)) ? 'ok': 'error';
										wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-prof-option.php&msg='.$msg);
										exit;
									}else{ // NG
										wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-prof-option.php&validation_error=1');
										exit;
									}
									break;
							}
						}else{
							wp_safe_redirect(admin_url('/').'admin.php?page=attendance-management-prof-option.php&msg=nonce-error');
							exit;
						}
					}
				}
			}
		}

	}
	// プロフ設定のPOST時の処理
	private function prof_option_post($post=''){

		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		$return = '';
		$skill_flag = 0;
		//
		if(is_array($post)){
			// まずはテーブルの存在を確認
			if(!empty($post['prof_skill_flag'])){
				$table_exists = AttendanceInstall::show_table(OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE);
				// 存在しなければ新規作成
				if(!$table_exists){
					self::add_prof_tbl();
				}
				//
				$skill_flag = 1;
			}
			// POSTの処理
			foreach($post as $key => $p){
				switch($key){
					case 'prof_skill_flag': case 'sop_css_type':
						$options[$key] = $p;
						break;
					// 新規作成もしくは更新
					case 'sop_arr':
						if(!empty($skill_flag)){
							$return_ins = self::sop_insert($p);
						}
						break;
				}
			}
			// 成功もしくは無効ならオプションアップデート
			if(!empty($return_ins) || empty($skill_flag)){
				update_option(OSAM_PLUGIN_DATA_NAME, $options);
				$return = 1;
			}
		}

		return $return;

	}
	/*
	*  管理画面プロフ側
	*/
	public function custom_profile_fields($profileuser){

		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		// 有効なら
		if(!empty($options['prof_skill_flag'])){
			$uid = 0;
			//
			if(isset($_GET) && isset($_GET['user_id'])){
				$uid = esc_html($_GET['user_id']);
			}else{
				if($user = wp_get_current_user()){
					$uid = $user->ID;
				}
			}
?>

		</table>
		<hr />
		<h3>スキルシート</h3>
		<div id="os-admin-skillsheet">
			<div class="os-block">
				<label for="shortcode" class="name-label">ショートコード</label>
				<input type="text" id="shortcode" value="[os-am-userprof uid=<?php echo $uid; ?>]" style="width:250px;" readonly /> <span class="description">こちらは変更できません。</span>
			</div>

<?php
			self::sop_sheet_view($uid, 'admin');
?>

		</div>
		<hr />
		<table>
<?php
		}

	}
	// スキルシート表示、admin=管理画面側、shortcode=公開側（ショートコード）
	public function sop_sheet_view($uid='', $type='admin'){

		$_return = '';
		$op_data = self::skillsheet_op_data('asc');
		$group = self::sop_grouping($op_data);
		$back_inline_check = 'block';
		$tab = "\t\t\t";
		// データがあれば
		if(!empty($op_data) && is_array($op_data)){
			// ユーザメタの値
			$s_umeta_values = get_user_meta($uid , 'os_am_skill', true);
			//
			if(!empty($s_umeta_values)){
				$umeta_values = unserialize($s_umeta_values);
			}else{
				$umeta_values = array();
			}
			//
			foreach($op_data as $d){
				$group_arr = array();
				$group_run = 0;
				// グループ化していれば
				if(!empty($d['sop_group_flag']) && isset($d['sop_group_flag'])){
					$group_i = self::group_search($d['sop_group_name'], $group);
					// データがあれば
					if($group_i!==FALSE && isset($group[$group_i])){
						$group_arr = (isset($group[$group_i]['data'])) ? $group[$group_i]['data']: '';
						unset($group[$group_i]);
						$group_run = 1;
					}
				}else{ // グループ化されていなければ
					$group_arr[] = $d;
				}
				// データがあれば
				if(!empty($group_arr) && is_array($group_arr)){
					// グループ化していれば
					if(!empty($group_run)){
						$group_name = (isset($d['sop_group_name'])) ? $d['sop_group_name']: '不明グループ';
						//
						switch($type){
							// ショートコード表示
							case 'shortcode':
								$_return .= $tab."<!-- group start -->\n";
								$_return .= $tab."<div class=\"os-tbl\">\n";
								$_return .= $tab."\t<div class=\"os-row group\">\n";
								$_return .= $tab."\t\t<div class=\"os-th\">".esc_html($group_name)."</div>\n";
								break;
							// 管理画面表示
							default:
								echo $tab."<!-- group start -->\n";
								echo $tab."<div class=\"os-group-block\">\n";
								echo $tab."\t<div class=\"os-inline\">\n";
								echo $tab."\t\t".'<label class="name-label">'.esc_html($group_name)."</label>\n";
								echo $tab."\t</div>\n";
								echo $tab."\t<div class=\"os-inline\">\n";
						}
					}else{
						switch($type){
							// ショートコード表示
							case 'shortcode':
								$_return .= $tab."<div class=\"os-tbl\">\n";
								break;
							// 管理画面表示
							default:
								echo $tab."<div>\n";
						}
					}
					//
					foreach($group_arr as $g){
						$id = (isset($g['sop_id'])) ? $g['sop_id']: 0;
						$label = (isset($g['sop_name'])) ? $g['sop_name']: '';
						$input_inline = (isset($g['sop_input_inline']) && $g['sop_input_inline']==1) ? 'inline': 'block';
						$view_inline = (isset($g['sop_view_inline']) && $g['sop_view_inline']==1) ? 'inline': 'block';
						$inp_type = (isset($g['sop_type']) && $g['sop_type']==1) ? 'textarea': 'input';
						// 値
						$meta_value = (isset($umeta_values[$id])) ? $umeta_values[$id]: '';
						$width = '';
						$height = '';
						$inline = '';
						//
						switch($type){
							// ショートコード表示
							case 'shortcode':
								$width = (isset($g['sop_view_width'])) ? $g['sop_view_width']: 130;
								$height = (isset($g['sop_view_height'])) ? $g['sop_view_height']: 25;
								$inline = $view_inline;
								break;
							default:
								$width = (isset($g['sop_width'])) ? $g['sop_width']: 130;
								$height = (isset($g['sop_height'])) ? $g['sop_height']: 25;
								$inline = $input_inline;
						}
						// 入力欄
						switch($inp_type){
							case 'textarea':
								$input_html = '<textarea name="os_am_skill['.$id.']" id="os_am_skill'.$id.'" style="width:'.$width.'px;height:'.$height.'px;">'.esc_textarea($meta_value).'</textarea>';
								break;
							default:
								$input_html = '<input type="text" name="os_am_skill['.$id.']" id="os_am_skill'.$id.'" value="'.esc_html($meta_value).'" style="width:'.$width.'px;height:'.$height.'px;" />';
						}
						//
						switch($inline){
							// inline
							case 'inline':
								switch($type){
									// ショートコード表示
									case 'shortcode':
										$_return .= $tab."\t\t<div class=\"os-td\" style=\"width:{$width}px;height:{$height}px;\">\n";
										$_return .= $tab."\t\t\t<div class=\"label\">".esc_html($label)."</div>\n";
										$_return .= $tab."\t\t\t<div class=\"value\">".esc_html($meta_value)."</div>\n";
										$_return .= $tab."\t\t</div>\n";
										break;
									// 管理画面表示
									default:
										echo $tab."\t\t<div class=\"os-inline\">\n";
										echo $tab."\t\t\t".'<label for="os_am_skill'.$id.'">'.esc_html($label)."</label>\n";
										echo $tab."\t\t\t<div class=\"input\">".$input_html."</div>\n";
										echo $tab."\t\t</div>\n";
								}
								break;
							// block
							default:
								switch($type){
									// ショートコード表示
									case 'shortcode':
										$_return .= $tab."\t\t<div class=\"os-row block\" style=\"width:{$width}px;height:{$height}px;\">\n";
										$_return .= $tab."\t\t\t<div class=\"os-th\">".esc_html($label)."</div>\n";
										$_return .= $tab."\t\t\t<div class=\"os-td\">".esc_html($meta_value)."</div>\n";
										$_return .= $tab."\t\t</div>\n";
										break;
									// 管理画面表示
									default:
										echo $tab."\t\t<div class=\"os-block\">\n";
										echo $tab."\t\t\t".'<label for="os_am_skill'.$id.'" class="name-label">'.esc_html($label)."</label>\n";
										echo $tab."\t\t\t<div class=\"input\">".$input_html."</div>\n";
										echo $tab."\t\t</div>\n";
								}
						}
					}
					// グループ化していれば
					if(!empty($group_run)){
						switch($type){
							// ショートコード表示
							case 'shortcode':
								$_return .= $tab."\t</div>\n";
								$_return .= $tab."</div>\n";
								$_return .= $tab."<!-- group end -->\n";
								break;
							// 管理画面表示
							default:
								echo $tab."\t</div>\n";
								echo $tab."</div>\n";
								echo $tab."<!-- group end -->\n";
						}
					}else{
						switch($type){
							// ショートコード表示
							case 'shortcode':
								$_return .= $tab."</div>\n";
								break;
							// 管理画面表示
							default:
								echo $tab."</div>\n";
						}
					}
				}
				//
				$back_inline_check = $input_inline;
			}
		}

		return $_return;

	}
	// スキルシート設定をグループ化
	public function sop_grouping($data=array()){

		$group_arr = array();
		//
		if(!empty($data) && is_array($data)){
			$check_arr = array();
			$group_ct = 0;
			//
			foreach($data as $i => $d){
				if(!empty($d['sop_group_flag'])){
					$group_name = (isset($d['sop_group_name'])) ? $d['sop_group_name']: '不明グループ';
					//
					if(array_search($group_name, $check_arr)!==false && array_search($group_name, $check_arr)!==null){
						$key = array_search($group_name, $check_arr);
						$group_arr[$key]['data'][] = $d;
						$group_ct++;
						$group_arr[$key]['ct'] = $group_ct;
					}else{
						$now_data = array('name'=>$group_name, 'data'=>array(), 'ct'=>0);
						$now_data['data'][] = $d;
						$group_arr[] = $now_data;
						$check_arr[] = $group_name;
						$group_ct++;
					}
				}
			}
		}

		return $group_arr;

	}
	//
	private function group_search($name='', $group_arr=''){

		$_return = FALSE;

		if(is_array($group_arr)){
			foreach($group_arr as $i => $g){
				if(!empty($g['name']) && $g['name']==$name){
					$_return = $i;
					break;
				}
			}
		}

		return $_return;

	}
	// プロフィール更新
	public function update_custom_profile_fields($user_id){

		global $am_plugin_option_data;
		$options = $am_plugin_option_data;
		// 有効なら
		if(!empty($options['prof_skill_flag'])){
			if(current_user_can('edit_user', $user_id)){
				if(!empty($_POST) && isset($_POST['os_am_skill']) && is_array($_POST['os_am_skill'])){
					$s_data = serialize($_POST['os_am_skill']);
					// 更新
					if($meta_value = get_user_meta($user_id , 'os_am_skill', true)){
						update_user_meta($user_id , 'os_am_skill', $s_data);
					}else{
						add_user_meta($user_id, 'os_am_skill', $s_data, true);
					}
				}
			}
		}

	}
	/*
	*  SQL
	*/
	// 新規作成もしくは更新
	public function sop_insert($data=''){

		$return_ins = 0;

		if(is_array($data)){
			global $wpdb;
			$error = 0;
			$ins_values = '';
			$ins_arr = array();
			$now = date_i18n("Y-m-d H:i:s");
			//
			foreach($data as $d){
				$id = (!empty($d['id'])) ? $d['id']: 0;
				$name = (isset($d['name'])) ? $d['name']: '';
				$order = (isset($d['order'])) ? $d['order']: 0;
				$group_flag = (isset($d['group_flag'])) ? $d['group_flag']: 0;
				$group_name = '';
				// グループフラグがたっていれば
				if(!empty($group_flag)){
					$group_name = (isset($d['group_name'])) ? $d['group_name']: '';
				}
				$input_inline = (isset($d['input_inline'])) ? $d['input_inline']: 0;
				$type = (isset($d['type'])) ? $d['type']: 0;
				$width = (isset($d['width'])) ? $d['width']: 130;
				$height = (isset($d['height'])) ? $d['height']: 25;
				$view_inline = (isset($d['view_inline'])) ? $d['view_inline']: 0;
				$view_width = (isset($d['view_width'])) ? $d['view_width']: 130;
				$view_height = (isset($d['view_height'])) ? $d['view_height']: 25;
				// 新規作成
				if(empty($id)){
					$ins_values .= "(%s, %d, %d, %d, %s, %d, %d, %d, %d, %d, %d, %s),";
					$ins_arr[] = $name; $ins_arr[] = $type; $ins_arr[] = $order; $ins_arr[] = $group_flag; $ins_arr[] = $group_name; $ins_arr[] = $input_inline; $ins_arr[] = $width; $ins_arr[] = $height; $ins_arr[] = $view_inline; $ins_arr[] = $view_width; $ins_arr[] = $view_height; $ins_arr[] = $now;
				}else{ // idがあれば更新
					$result = $wpdb->update(OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE, array('sop_name'=>$name, 'sop_type'=>$type, 'sop_order'=>$order, 'sop_group_flag'=>$group_flag, 'sop_group_name'=>$group_name, 'sop_input_inline'=>$input_inline, 'sop_width'=>$width, 'sop_height'=>$height, 'sop_view_inline'=>$view_inline, 'sop_view_width'=>$view_width, 'sop_view_height'=>$view_height, 'update_time'=>$now), array('sop_id'=>$id), array('%s', '%d', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s'), array('%d'));
					//
					if($result===FALSE){
						$error = 1;
						break;
					}
				}
			}
			//
			if(empty($error)){
				if(!empty($ins_values)){
					$sql = "INSERT INTO `".OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE."` (`sop_name`, `sop_type`, `sop_order`, `sop_group_flag`, `sop_group_name`, `sop_input_inline`, `sop_width`, `sop_height`, `sop_view_inline`, `sop_view_width`, `sop_view_height`, `create_time`) VALUES ".rtrim($ins_values, ',');
					$wpdb->query($wpdb->prepare($sql, $ins_arr));
					// 最後のidが取得できれば
					if($ins_id = $wpdb->insert_id){
						$return_ins = 1;
					}else{ // 失敗なら返りを0にする
						$return_ins = 0;
					}
				}else{
					$return_ins = 1;
				}
			}else{
				$return_ins = 0;
			}
		}

		return $return_ins;

	}
	// スキルシート設定データのチェック
	public function skillsheet_op_data($order_type=''){

		$return_arr = array();
		$table_exists = AttendanceInstall::show_table(OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE);
		// 存在すれば
		if($table_exists){
			global $wpdb;
			$params = array();
			$sql = "SELECT * FROM `".OSAM_PLUGIN_SKILLSHEET_OPTION_TABLE."` WHERE `delete_flag`=%d";
			//
			if(!empty($order_type)){
				$order_type = strtolower($order_type);
				// 順序決定
				switch($order_type){
					case 'asc': // 昇順
						$sql .= ' ORDER by `sop_order` ASC';
						break;
					case 'desc': // 降順
						$sql .= ' ORDER by `sop_order` DESC';
						break;
				}
			}
			//
			$params[] = 0;
			$data = $wpdb->get_results($wpdb->prepare($sql, $params));
			// データがあれば
			if(!empty($data)){
				foreach($data as $i => $d){
					//
					foreach($d as $key => $str){
						$return_arr[$i][$key] = $str;
					}
				}
			}
		}

		return $return_arr;

	}
	// プロフ（スキルシート）設定テーブルを作成
	public function add_prof_tbl(){
		AttendanceInstall::newTable2();
	}
	/*
	*  バリデーション
	*/
	// POST時のバリデーション
	public function post_validation($sop_post=''){

		$message = '';

		if(is_array($sop_post)){
			if(empty($sop_post['prof_skill_flag'])){

			}else{
				foreach($sop_post as $i => $sopp){
					$keyname = '項目'.$i;
					//
					foreach($sopp as $key => $p){
						switch($key){
							case 'name':
								$message .= self::_empty($p, $keyname.'の項目名を入力してください。');
								break;
							case 'sop_order':
								if(preg_match('/^([0-9０-９]+)$/', $p, $matches)){
									// ok
								}else{
									$message .= $keyname.'の昇順を数字で入力してください。,';
								}
								break;
						}
					}
				}
			}
			// エラーメッセージをメタキーにして格納
			if(!empty($message)){
				if($user = wp_get_current_user()){
					if(isset($user->ID)){
						add_user_meta($user->ID, 'os_am_error_text', $message, true);
					}
				}
			}
		}

		return $message;

	}
	// 空の場合
	private function _empty($str='', $msg=''){

		if(empty($str)){
			if(empty($msg)){
				$msg = 'この項目は入力必須です';
			}
			//
			return $msg.',';
		}else{
			return '';
		}

	}
	// バリデーションメッセージ取得
	public function get_validation_msg(){

		$validation_msg = '';

		if(isset($_GET) && !empty($_GET['validation_error'])){
			if($user = wp_get_current_user()){
				if(isset($user->ID)){
					$validation_msg = get_user_meta($user->ID, 'os_am_error_text', true);
				}
			}
		}

		return $validation_msg;

	}

}
?>