<?php
if(class_exists('AttendanceUser') || class_exists('AttendanceAdmin')){
	$post_page = (isset($_GET) && isset($_GET['page'])) ? esc_html($_GET['page']) : '';
	$head_text = '';
?>
	<div id="attendance-plugin">

<?php
	//
	if($post_page=='attendance-management-list.php'){
		include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php");
		$head_text = '<p>一覧やCSVの出力項目を<a href="admin.php?page=attendance-management-options.php">基本設定</a>で設定できるようになりました。</p>';
	}
	//
$user_page_view=<<<_EOD_
		<div class="wrap">
			<h2>出勤・勤怠の一覧</h2>
			<div style="color:red;">{$message}</div>
			<p class="search-where">検索条件 => {$list_message}</p>
			<form action="admin.php?page={$post_page}" method="POST" id="month-submit">
				{$listHidden}
				<input type="submit" name="search_back_month" value="先月" class="s-click" />　<input type="submit" name="search_now_month" value="今月" class="s-click" />　<input type="submit" name="search_next_month" value="来月" class="s-click" />
_EOD_
;
echo $user_page_view;
//
	if($post_page=='attendance-management-list.php'){
$user_page_view=<<<_EOD_
				CSVダウンロード：<a href="admin.php{$csv_link}&csv_dl=1" style="text-decoration:none;"><input type="button" name="csv_data" value="一覧データ" class="s-click" /></a>　<a href="admin.php{$csv_link}&csv_dl=2" style="text-decoration:none;"><input type="button" name="csv_data" value="合計データ" class="s-click" /></a>
_EOD_
;
echo $user_page_view;
	}
//
$user_page_view=<<<_EOD_
			</form>
			{$head_text}
			<table id="list">
				{$list_html}
			</table><br />
			<form action="admin.php?page={$post_page}" method="POST" id="list-search">
				<p>
					勤務日：
					<select name="start_y">
						{$search_option_html[0]}
					</select>年
					<select name="start_m">
						{$search_option_html[1]}
					</select>月
					<select name="start_d">
						{$search_option_html[2]}
					</select>日 ～
					<select name="end_y">
						{$search_option_html[3]}
					</select>年
					<select name="end_m">
						{$search_option_html[4]}
					</select>月
					<select name="end_d">
						{$search_option_html[5]}
					</select>日
				</p>
				<p>
					{$users_html}
					キーワード：<input type="text" name="keyword" value="" />
				</p>
				<input type="hidden" name="list_search" value="1" />
				<input type="submit" name="Submit" class="button-primary" value="検索" />
			</form>
		</div>
	</div>

_EOD_
;
echo $user_page_view;
}
?>