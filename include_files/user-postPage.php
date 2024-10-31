<?php
if(class_exists('AttendanceUser') || class_exists('AttendanceAdmin')){

	$post_page = (isset($_GET) && isset($_GET['page'])) ? esc_html($_GET['page']) : "attendance-management-user-post.php";
	$post_url = "admin.php?page=".$post_page;

$user_page_view=<<<_EOD_
	<script type="text/javascript">
	var j = jQuery.noConflict();
	j(document).ready(function(){
		// 今月
		j('#work #now').click(function() {
			month_view(0);
		});
		if(j('#work #now').attr('checked')) {
			month_view(0);
		}
		// 先月
		j('#work #back').click(function() {
			month_view(101);
		});
		if(j('#work #back').attr('checked')) {
			month_view(101);
		}
		// 来月
		j('#work #next').click(function() {
			month_view(1);
		});
		if(j('#work #next').attr('checked')) {
			month_view(1);
		}
		// その他
		j('#work #etc').click(function() {
			month_view(999);
		});
		if(j('#work #etc').attr('checked')) {
			month_view(999);
		}
		// 日付のセレクトボックス表示
		function month_view(str){
			var select_html = '';
			if(str==0){
				select_html = '<p>選択：<select name="work_day">{$select_options[0]}</select>';
			}else if(str==101){
				select_html = '<p>選択：<select name="work_day">{$select_options[1]}</select>';
			}else if(str==1){
				select_html = '<p>選択：<select name="work_day">{$select_options[2]}</select></p>';
			}else if(str==999){
				select_html = '<p>年月日：<input type="text" name="work_day" value="" /><br /><small style="padding-left:65px;">[ 2013-01-01 ]形式で入力</small></p>';
			}
			j('#work_day').text("");
			j('#work_day').append(select_html);
		}
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
	if($post_page=='attendance-management-post.php'){
		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php");
	}
	//
$nonce = wp_nonce_field( 'wp_nonce', '_wpnonce', false, false );
$user_page_view=<<<_EOD_
		<div class="wrap">
			<h2>出勤・勤怠の新規作成</h2>
			<div style="color:red;">{$message}</div>
			<form action="{$post_url}" method="POST" class="silver">
				<table>
					<tr>
						<th scope="row">ユーザ</th>
						<td>
							{$form_html[0]}
						</td>
					</tr>
					<tr>
						<th scope="row">勤務日</th>
						<td>
							<div id="work"><input type="radio" name="month" value="101" id="back" /><label for="back">先月</label>　<input type="radio" name="month" value="0" id="now" checked /><label for="now">今月</label>　<input type="radio" name="month" value="1" id="next" /><label for="next">来月</label>　<input type="radio" name="month" value="2" id="etc" /><label for="etc">それ以外</label></div>
							<div id="work_day"></div>
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
							<p id="break"><input type="radio" name="break_point" value="1" id="ok" checked /><label for="ok">あり</label>　<input type="radio" name="break_point" value="0" id="ng" /><label for="ng">なし</label></p>
							<div id="break_time"></div>
						</td>
					</tr>
					<tr>
						<th scope="row">残業時間</th>
						<td>
							<p id="over"><input type="radio" name="over_point" value="1" id="over_ok" /><label for="over_ok">あり</label>　<input type="radio" name="over_point" value="0" id="over_ng" checked /><label for="over_ng">なし</label></p>
							<div id="over_time"></div>
						</td>
					</tr>
					<tr class="end">
						<th scope="row">メッセージ</th>
						<td>
							<textarea name="text" class="message"></textarea>
						</td>
					</tr>
				</table>
				<input type="hidden" name="new" value="new" />
				{$nonce}
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="作成する" />
				</p>
			</form>
		</div>
	</div>

_EOD_
;
echo $user_page_view;
}
?>