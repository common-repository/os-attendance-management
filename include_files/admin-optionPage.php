<?php
if(class_exists('AttendanceAdmin')){
$admin_page_view=<<<_EOD_
	<div id="attendance-plugin">
_EOD_
;
echo $admin_page_view;
?>
		<?php include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php"); ?>
<?php
$admin_page_view=<<<_EOD_
		<div class="wrap">
			<h2>出勤・勤怠管理の基本設定</h2>
			<p style="color:red;">{$message}</p>
			<form action="admin.php?page=attendance-management-options.php" method="POST">
				<table>
					<tr>
						<th scope="row">時間表示</th>
						<td>
							<select name="time_view">
								<option value="0" {$time_view_selected[0]}>24時間表示</option>
								<option value="1" {$time_view_selected[1]}>12時間表示（午前・午後）</option>
								<option value="2" {$time_view_selected[2]}>12時間表示（AM・PM）</option>
							</select>
							<div style="font-size:11px;">※管理画面ではすべて24時間表示です</div>
						</td>
					</tr>
					<tr>
						<th scope="row">時間区切</th>
						<td>
							<input type="radio" name="clock" value="0" id="clock-ng" {$clock_checked[0]} /><label for="clock-ng">文字</label>　<input type="radio" name="clock" value="1" id="clock-ok" {$clock_checked[1]} /><label for="clock-ok">コロン</label>
							<div style="font-size:11px;">12時30分 or 12:30</div>
						</td>
					</tr>
					<tr>
						<th scope="row">勤怠管理を<br />編集できるユーザ</th>
						<td>
							<select name="time_write">
								<option value="admin" {$time_write_selected[0]}>管理者のみ編集可能</option>
								<option value="user" {$time_write_selected[1]}>登録ユーザは自分の勤怠のみ投稿・編集可能</option>
								<option value="user-post" {$time_write_selected[2]}>登録ユーザは自分の勤怠投稿のみ可能</option>
							</select>
							<div style="font-size:11px;">※管理者はすべてのユーザを操作できます</div>
						</td>
					</tr>
					<tr>
						<th scope="row">一覧の表示設定<br />（公開側）</th>
						<td>
							<input type="radio" name="view-list" value="1" id="view-list-one" {$view_list_checked[0]} /><label for="view-list-one">1週間</label>　<input type="radio" name="view-list" value="2" id="view-list-two" {$view_list_checked[1]} /><label for="view-list-two">2週間</label>　<input type="radio" name="view-list" value="3" id="view-list-three" {$view_list_checked[2]} /><label for="view-list-three">3週間</label>　<input type="radio" name="view-list" value="m" id="view-list-month" {$view_list_checked[3]} /><label for="view-list-month">一ヶ月</label>
						</td>
					</tr>
					<tr>
						<th scope="row">一覧の表示設定<br />（管理画面側）</th>
						<td>
							<input type="radio" name="admin-list" value="1" id="admin-list-one" {$admin_list_checked[0]} /><label for="admin-list-one">1週間</label>　<input type="radio" name="admin-list" value="2" id="admin-list-two" {$admin_list_checked[1]} /><label for="admin-list-two">2週間</label>　<input type="radio" name="admin-list" value="3" id="admin-list-three" {$admin_list_checked[2]} /><label for="admin-list-three">3週間</label>　<input type="radio" name="admin-list" value="m" id="admin-list-month" {$admin_list_checked[3]} /><label for="admin-list-month">一ヶ月</label>
						</td>
					</tr>

_EOD_
;
echo $admin_page_view;
//
	for($i=0; $i<2; $i++){
		switch($i){
			case 1:
				$key_name = 'csv';
				$value = $data_csv;
				$row_title = 'CSV出力の項目<br />（一覧データCSV）';
				break;
			default:
				$key_name = 'list';
				$value = $data_list;
				$row_title = '一覧に表示する項目';
		}
?>

					<tr>
						<th scope="row"><?php echo $row_title; ?></th>
						<td class="list-checked">
							<p>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[work_start]" id="<?php echo $key_name; ?>_work_start" value="1" <?php if(!empty($value['work_start'])){ ?>checked<?php } ?> /><label for="<?php echo $key_name; ?>_work_start">業務開始時刻</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[work_end]" id="<?php echo $key_name; ?>_work_end" value="1" <?php if(!empty($value['work_end'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_work_end">業務終了時刻</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[work_time]" id="<?php echo $key_name; ?>_work_time" value="1" <?php if(!empty($value['work_time'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_work_time">業務時間</label></span>
							</p>
							<p>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[break_start]" id="<?php echo $key_name; ?>_break_start" value="1" <?php if(!empty($value['break_start'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_break_start">休憩開始時刻</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[break_end]" id="<?php echo $key_name; ?>_break_end" value="1" <?php if(!empty($value['break_end'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_break_end">休憩終了時刻</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[break_time]" id="<?php echo $key_name; ?>_break_time" value="1" <?php if(!empty($value['break_time'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_break_time">休憩時間</label></span>
							</p>
							<p>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[overtime_start]" id="<?php echo $key_name; ?>_overtime_start" value="1" <?php if(!empty($value['overtime_start'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_overtime_start">残業開始時刻</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[overtime_end]" id="<?php echo $key_name; ?>_overtime_end" value="1" <?php if(!empty($value['overtime_end'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_overtime_end">残業終了時刻</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[overtime_time]" id="<?php echo $key_name; ?>_overtime_time" value="1" <?php if(!empty($value['overtime_time'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_overtime_time">残業時間</label></span>
							</p>
		<?php
		if($i==0){
		?>
							<p>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[daywork]" id="<?php echo $key_name; ?>_daywork" value="1" <?php if(!empty($value['daywork'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_daywork">日ごとの実働時間</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[total]" id="<?php echo $key_name; ?>_total" value="1" <?php if(!empty($value['total'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_total">各々の合計時間</label></span>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[all_total]" id="<?php echo $key_name; ?>_all_total" value="1" <?php if(!empty($value['all_total'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_all_total">総合時間</label></span>
							</p>
		<?php
		}
		?>
							<p>
								<span><input type="checkbox" name="<?php echo $key_name; ?>[message]" id="<?php echo $key_name; ?>_message" value="1" <?php if(!empty($value['message'])){ ?>checked<?php } ?>  /><label for="<?php echo $key_name; ?>_message">メッセージ</label></span>
							</p>
						</td>
					</tr>

<?php
	}
//
$nonce = wp_nonce_field( 'wp_nonce', '_wpnonce', false, false );
$admin_page_view=<<<_EOD_
					<tr class="end">
						<th scope="row">ライセンス</th>
						<td>
							<input type="text" name="license" size="45" value="{$data['license']}" /><br />
							<div style="font-size:11px;">※ライセンスを取得した方のみ、ご記入ください。デフォルトは「free」です。</div>
						</td>
					</tr>
				</table>
				<input type="hidden" name="option" value="option" />
				{$nonce}
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="更新する" />
				</p>
			</form>
			<p><a href="admin.php?page=attendance-management-format.php">初期化する</a> - <a href="admin.php?page=attendance-management-agreement.php">利用規約</a></p>
		</div>
	</div>

_EOD_
;
echo $admin_page_view;
}
?>