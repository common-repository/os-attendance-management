<?php
if(class_exists('AttendanceUser') || class_exists('AttendanceAdmin')){
	$post_page = (isset($_GET) && isset($_GET['page'])) ? esc_html($_GET['page']) : '';
	$user_link = '-user';
	//
	if($post_page=='attendance-management-help.php'){
		$user_link = '';
	}
?>
		<div class="wrap" id="help-page">
			<h2>ヘルプページ</h2>

			<div class="strong">新規作成</div>
			<p><a href="admin.php?page=attendance-management<?php echo $user_link; ?>-post.php">新規作成</a>で出勤・勤怠を作成していきます。</p>
			<p>ユーザは、WordPressの<a href="user-new.php">ユーザ新規作成（管理者のみ）</a>で作成できます。

			<div class="strong">記事内での表示</div>
			<p>[attendance] というショートコードで記事内に表示できます。</p>
			<p>uidを指定することによって、表示するユーザを指定できます。 [attendance uid=1]</p>
			<p>dayを指定することによって、表示の開始時期を指定できます。[attendance day=2015-01-01]　必ず西暦4桁-月2桁-日2桁で指定してください。</p>

			<div class="strong">勤怠管理ができるユーザ設定</div>
			<p>勤怠の投稿や編集が可能なユーザは、<a href="admin.php?page=attendance-management-options.php">基本設定（管理者のみ）</a>で設定できます。</p>
			<p>"管理者のみ編集可能"に設定すると、管理者権限のアカウントのみが操作できます。<br />"登録ユーザは自分の勤怠のみ投稿・編集可能"に設定すると、寄稿者以上の登録ユーザが勤怠の投稿・編集ができます。<br />"登録ユーザは自分の勤怠投稿のみ可能"に設定すると、寄稿者以上の登録ユーザは出勤の投稿のみできます。</p>
			<p>なお、管理者権限のアカウントの場合、どの設定にしても全ての操作が可能です。</p>
		</div>

<?php
}
?>