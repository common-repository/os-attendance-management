<?php
if(class_exists('AttendanceUser') || class_exists('AttendanceAdmin')){

	$post_page = (isset($_GET) && isset($_GET['page'])) ? esc_html($_GET['page']) : "attendance-management-user-write.php";
	$post_url = "admin.php?page=".$post_page;

$user_page_view=<<<_EOD_
	<script type="text/javascript">
	var j = jQuery.noConflict();
	j(document).ready(function(){
		// 休憩ボタン
		j('#break #ok').click(function() {
			time_view();
		});
		if(j('#break #ok').attr('checked')) {
			time_view();
		}
		j('#break #ng').click(function() {
			j('#break_time').text("");
		});
		if(j('#break #ng').attr('checked')) {
			j('#break_time').text("");
		}
		// 休憩時間のセレクトボックス表示
		function time_view(){
			var select_html = '';
			select_html = '<select name="break_start_time">{$form_html[5]}</select>時<select name="break_start_i_time">	{$form_html[6]}</select>分　～　<select name="break_finish_time">{$form_html[7]}</select>時<select name="break_finish_i_time">{$form_html[8]}</select>分';
			j('#break_time').text("");
			j('#break_time').append(select_html);
		}
		// 残業ボタン
		j('#over #over_ok').click(function() {
			over_view();
		});
		if(j('#over #over_ok').attr('checked')) {
			over_view();
		}
		j('#over #over_ng').click(function() {
			j('#over_time').text("");
		});
		if(j('#over #over_ng').attr('checked')) {
			j('#over_time').text("");
		}
		// 残業時間のセレクトボックス表示
		function over_view(){
			var select_html = '';
			select_html = '<select name="over_start_time">{$form_html[9]}</select>時<select name="over_start_i_time">	{$form_html[10]}</select>分　～　<select name="over_finish_time">{$form_html[11]}</select>時<select name="over_finish_i_time">{$form_html[12]}</select>分';
			j('#over_time').text("");
			j('#over_time').append(select_html);
		}
	});
	</script>
	<div id="attendance-plugin">

_EOD_
;
echo $user_page_view;
	//
	if($post_page=='attendance-management-write.php'){
		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php");
	}
	//
$nonce = wp_nonce_field( 'wp_nonce', '_wpnonce', false, false );
$user_page_view=<<<_EOD_
		<div class="wrap">
			<h2>出勤・勤怠の編集</h2>
			<div style="color:red;">{$message}</div>
			<form action="{$post_url}" method="POST" class="silver">
				<table>
					<tr>
						<th scope="row">ユーザ</th>
						<td>
							{$form_user_html}
						</td>
					</tr>
					<tr>
						<th scope="row">勤務日</th>
						<td>
							{$form_day_html}
						</td>
					</tr>
					<tr>
						<th scope="row">勤務時間</th>
						<td>
							<select name="start_time">
								{$form_html[1]}
							</select>時
							<select name="start_i_time">
								{$form_html[2]}
							</select>分
							　～　
							<select name="finish_time">
								{$form_html[3]}
							</select>時
							<select name="finish_i_time">
								{$form_html[4]}
							</select>分
						</td>
					</tr>
					<tr>
						<th scope="row">休憩時間</th>
						<td>
							<p id="break"><input type="radio" name="break_point" value="1" id="ok"{$break_selected[1]} /><label for="ok">あり</label>　<input type="radio" name="break_point" value="0" id="ng"{$break_selected[0]} /><label for="ng">なし</label></p>
							<div id="break_time"></div>
						</td>
					</tr>
					<tr>
						<th scope="row">残業時間</th>
						<td>
							<p id="over"><input type="radio" name="over_point" value="1" id="over_ok"{$over_selected[1]} /><label for="over_ok">あり</label>　<input type="radio" name="over_point" value="0" id="over_ng"{$over_selected[0]} /><label for="over_ng">なし</label></p>
							<div id="over_time"></div>
						</td>
					</tr>
					<tr class="end">
						<th scope="row">メッセージ</th>
						<td>
							<textarea name="text" class="message">{$form_message}</textarea>
						</td>
					</tr>
				</table>
				<input type="hidden" name="write" value="write" />
				{$nonce}
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="更新する" />
					<span style="padding-left:50px;"><input type="submit" name="Delete" class="button-primary" value="削除する" /></span>
				</p>
			</form>
		</div>
	</div>

_EOD_
;
echo $user_page_view;
}
?>