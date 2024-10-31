<?php
if(class_exists('AttendanceAdmin')){
?>
	<div id="attendance-plugin">
		<?php include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php"); ?>
		<div class="wrap">
			<h2>プラグインの初期化</h2>
			<form action="admin.php?page=attendance-management-format.php" method="POST">
				<p>当プラグインで作成したデータを初期化します。全てのデータは削除され、初期設定に戻ります。<br />よろしいですか？</p>
				<input type="hidden" name="format" value="format" />
				<input type="submit" name="Submit" value="初期化する" />
				<p><a href="admin.php?page=attendance-management-options.php">戻る</a></p>
				<?php wp_nonce_field( 'wp_nonce', '_wpnonce', false, true ); ?>
			</form>
		</div>
	</div>

<?php
}
?>