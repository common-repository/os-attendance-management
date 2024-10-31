<?php
if(class_exists('AttendanceAdmin')){
?>

	<div id="attendance-plugin">
		<?php include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php"); ?>
		<div class="wrap">
			<h2>はじめに</h2>
			<div>
				<p>出勤・勤怠管理プラグインを導入していただき、ありがとうございます。</p>
				<p>当プラグインのご利用は無料です。再配布は許可しておりません。詳しくは、<a href="?page=attendance-management-agreement.php">こちらの利用規約</a>をご覧ください。</p>
				<p>ご連絡は<a href="http://lp.olivesystem.jp/plugin-am-mail" title="問い合わせ" target="_blank">問い合わせフォーム</a>からお願い致します。</p>
			</div>
			<h2>使い方</h2>
			<div>
				<p>新規作成でユーザごとの勤務時間を作成できます。</p>
				<p>[attendance] というショートコードで記事内に表示できます。</p>
				<p>詳しくは<a href="admin.php?page=attendance-management-help.php">ヘルプページ</a>をご覧ください</p>
				<p>お知らせやプラグインに関する情報など、<a href="http://lp.olivesystem.jp/category/wordpress-plugins">ブログで告知</a>しますので、チェックしてみてください。</p>
			</div>
			<h2>注意</h2>
			<div>
				<p>当プラグインを改造して有料販売している業者がいますが、当方とは無関係です。</p>
				<p>危険性がありますので、<a href="https://wordpress.org/plugins/" target="_blank">WordPress公式サイト</a>に登録されていないものはお勧めしません。自己責任でご利用ください。</p>
			</div>
			<h2>更新履歴</h2>
			<div style="padding:10px;width:450px;height:100px;overflow-y:scroll;border:1px solid #ccc;background-color:white;">
				2017-09-16  バグフィックス、セキュリティ修正。
				<p>2017-09-14  現在のWPバージョン（4.8.1）に合わせた調整、バグフィックス。</p>
				<p>2016-08-09  v1.3.0 バグの修正、プラグイン構成整理</p>
				<p>2015-03-27  バグの修正、スキルシート機能追加</p>
				<p>2015-03-17  CSV設定機能及び出力の修正</p>
				<p>2015-03-17  各箇所の修正、ヘルプページ追加</p>
				<p>2015-03-09  軽微な修正</p>
				<p>2015-02-11  軽微な修正</p>
				<p>2015-02-07  軽微な修正</p>
				<p>2015-02-07  軽微な修正</p>
				<p>2014-07-17  軽微な修正</p>
				<p>2014-07-18  バグの修正</p>
				<p>2013-11-29  バグの修正</p>
				<p>2013-11-25  First release</p>
			</div>
		</div>
	</div>

<?php
}
?>