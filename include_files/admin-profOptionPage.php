<?php
if(class_exists('AttendanceAdmin')){
?>

	<div id="attendance-plugin">
		<?php include_once(OSAM_PLUGIN_INCLUDE_FILES."/admin-head.php"); ?>
		<div class="wrap">
			<h2>プロフィール（スキルシート）設定</h2>
			<div>
				ここでは、<a href="profile.php">ユーザのプロフィール</a>にスキルシートの枠を設けることができます。<br />
				作成したプロフィールはショートコード [os-am-userprof uid=ユーザid] で呼び出すことができます。ユーザidは各ユーザページでご確認ください。
			</div>
			<div style="color:red;">
			<?php
				if(!empty($message)){
					echo '<p>'.$message.'</p>';
				}
				//
				if(!empty($validation_msg)){
					$msg_ex = explode(",", $validation_msg);
					//
					foreach($msg_ex as $msg){
						echo '<p>'.$msg.'</p>';
					}
				}
			?>
			</div>
			<form action="admin.php?page=attendance-management-prof-option.php" method="POST">
				<table>
					<tr>
						<th scope="row">機能の有無</th>
						<td>
							スキルシートを
							<input type="radio" name="prof_skill_flag" value="0" id="prof_skill_flag0" <?php if(empty($options['prof_skill_flag'])){ echo "checked"; } ?> /><label for="prof_skill_flag0" style="margin-right:15px;">無効にする</label>
							<input type="radio" name="prof_skill_flag" value="1" id="prof_skill_flag1" <?php if(!empty($options['prof_skill_flag'])){ echo "checked"; } ?> /><label for="prof_skill_flag1">有効にする</label>
						</td>
					</tr>
					<tr class="skill-options">
						<th scope="row">スキルシート<br />項目設定</th>
						<td>
						<?php
						$for_ct = 3;
						//
						if(!empty($sop_data) && is_array($sop_data)){
							$for_ct = count($sop_data);
						}
						//
						for($t=0; $t<$for_ct; $t++){
							$i = $t + 1;
							$data = (isset($sop_data[$t])) ? $sop_data[$t]: '';
						?>

							<div>
								<h4 style="margin:5px 0 5px 0;padding:0;">項目<?php echo $i; ?></h4>
							</div>
							<div id="div_sop<?php echo $i; ?>">
								<div style="margin:0 5px 5px 5px;">

									<?php
									$value = (isset($data['sop_name'])) ? $data['sop_name']: '';
									?>
									<div>
										<label for="sop_name<?php echo $i; ?>">項目名</label>
										<input type="text" name="sop_arr[<?php echo $i; ?>][name]" id="sop_name<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:250px;" />

										<?php
										$value = (isset($data['sop_order'])) ? $data['sop_order']: 0;
										?>
										<span style="padding-left:10px;">
											<label for="sop_order<?php echo $i; ?>">昇順</label>
											<input type="text" name="sop_arr[<?php echo $i; ?>][order]" id="sop_order<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:35px;" />
										</span>
									</div>

									<?php
										$value = (isset($data['sop_group_flag'])) ? $data['sop_group_flag']: 0;
									?>
									<div style="margin-top:5px;">
										グループ化
										<input type="radio" name="sop_arr[<?php echo $i; ?>][group_flag]" id="sop_group_flag_off<?php echo $i; ?>" class="group_flag" value="0" <?php if($value==0){ echo "checked"; } ?> /><label for="sop_group_flag_off<?php echo $i; ?>">しない</label>
										<input type="radio" name="sop_arr[<?php echo $i; ?>][group_flag]" id="sop_group_flag_on<?php echo $i; ?>" class="group_flag" value="1" <?php if($value==1){ echo "checked"; } ?> /><label for="sop_group_flag_on<?php echo $i; ?>">する</label>

										<?php
										$value = (isset($data['sop_group_name'])) ? $data['sop_group_name']: '';
										?>
										<div id="div_group_name<?php echo $i; ?>" style="padding:5px 5px 0 10px;">
											<label for="sop_group_name<?php echo $i; ?>">グループ名</label>
											<input type="text" name="sop_arr[<?php echo $i; ?>][group_name]" id="group_name<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:250px;" />
										</div>
									</div>
									<div style="padding:5px;color:green;"><strong>入力設定</strong></div>
									<div style="padding:0 5px 0 10px;">

										<?php
										$value = (isset($data['sop_input_inline'])) ? $data['sop_input_inline']: 0;
										?>
										<label for="sop_input_inline<?php echo $i; ?>">表示</label>
										<select name="sop_arr[<?php echo $i; ?>][input_inline]" id="sop_input_inline<?php echo $i; ?>">
											<option value="0" <?php if($value==0){ echo "selected"; } ?>>ブロック</option>
											<option value="1" <?php if($value==1){ echo "selected"; } ?>>インライン</option>
										</select>
										<?php
										$value = (isset($data['sop_type'])) ? $data['sop_type']: 0;
										?>
										<span style="padding-left:10px;">
											<label for="sop_type<?php echo $i; ?>">タイプ</label>
											<select name="sop_arr[<?php echo $i; ?>][type]" id="sop_type<?php echo $i; ?>">
												<option value="0" <?php if($value==0){ echo "selected"; } ?>>1行テキスト（input）</option>
												<option value="1" <?php if($value==1){ echo "selected"; } ?>>改行テキスト（textarea）</option>
											</select>
										</span>
									</div>
									<div style="padding:0 5px 0 10px;">

										<?php
										$value = (isset($data['sop_width'])) ? $data['sop_width']: 150;
										?>
										<label for="sop_width<?php echo $i; ?>">入力幅</label>
										<input type="text" name="sop_arr[<?php echo $i; ?>][width]" id="sop_width<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:55px;" />px

										<?php
										$value = (isset($data['sop_height'])) ? $data['sop_height']: 25;
										?>
										<span style="padding-left:10px;">
											<label for="sop_height<?php echo $i; ?>">高さ</label>
											<input type="text" name="sop_arr[<?php echo $i; ?>][height]" id="sop_height<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:55px;" />px
										</span>
									</div>
									<div style="padding:5px;color:green;"><strong>公開設定</strong></div>
									<div style="padding:0 5px 0 10px;">

										<?php
										$value = (isset($data['sop_view_inline'])) ? $data['sop_view_inline']: 0;
										?>
										<label for="sop_view_inline<?php echo $i; ?>">表示</label>
										<select name="sop_arr[<?php echo $i; ?>][view_inline]" id="sop_view_inline<?php echo $i; ?>">
											<option value="0" <?php if($value==0){ echo "selected"; } ?>>ブロック</option>
											<option value="1" <?php if($value==1){ echo "selected"; } ?>>インライン</option>
										</select>

										<?php
										$value = (isset($data['sop_view_width'])) ? $data['sop_view_width']: 150;
										?>
										<label for="sop_view_width<?php echo $i; ?>">表示幅</label>
										<input type="text" name="sop_arr[<?php echo $i; ?>][view_width]" id="sop_view_width<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:55px;" />px

										<?php
										$value = (isset($data['sop_view_height'])) ? $data['sop_view_height']: 25;
										?>
										<span style="padding-left:10px;">
											<label for="sop_view_height<?php echo $i; ?>">高さ</label>
											<input type="text" name="sop_arr[<?php echo $i; ?>][view_height]" id="sop_view_height<?php echo $i; ?>" value="<?php echo esc_html($value); ?>" style="width:55px;" />px
										</span>
									</div>

									<?php if(!empty($data['sop_id'])): ?>
									<input type="hidden" name="sop_arr[<?php echo $i; ?>][id]" value="<?php echo $data['sop_id']; ?>" />
									<?php endif; ?>
								</div>
							</div>
						<?php
						}
						?>
							<div id="add_html"></div>
							<input type="hidden" name="sop_ct" id="sop_ct" value="<?php echo $t; ?>" />
							<p><input type="button" id="add_sop" value="項目を追加" /></p>
						</td>
					</tr>
					<tr>
						<th scope="row">スキルシート<br />色設定</th>
						<td>
							<?php
							$value = (isset($options['sop_css_type'])) ? $options['sop_css_type']: 'gray';
							?>
							<select name="sop_css_type">
								<option value="gray" <?php if($value=='gray'){ echo "selected"; } ?>>グレー（デフォルト）</option>
								<option value="black" <?php if($value=='black'){ echo "selected"; } ?>>ブラック</option>
								<option value="orange" <?php if($value=='orange'){ echo "selected"; } ?>>オレンジ</option>
								<option value="pink" <?php if($value=='pink'){ echo "selected"; } ?>>ピンク</option>
							</select>
						</td>
					</tr>

				</table>
				<input type="hidden" name="am_formname" value="prof_option" />
				<?php wp_nonce_field( 'wp_nonce', '_wpnonce', false, true ); ?>
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="更新する" />
				</p>
			</form>
		</div>
	</div>

	<script>
	var j = jQuery.noConflict();
	//
	function skill_options_view(str){
		if(str=='on'){
			j('.skill-options').css('display', 'table-row');
		}
		else{
			j('.skill-options').css('display', 'none');
		}
	}
	//
	function sop_group_view(str, t){
		if(str=='on'){
			j('#div_group_name'+t).css('display', 'block');
		}
		else{
			j('#div_group_name'+t).css('display', 'none');
		}
	}
	//
	function add_sop(){

		sop_ct = parseInt(j('#sop_ct').val());
		i = sop_ct + 1;
		var html = '\
			<div><h4 style="margin:5px 0 5px 0;padding:0;">項目'+i+'</h4></div>\
			<div id="div_sop'+i+'">\
				<div style="margin:0 5px 5px 5px;">\
					<div>\
						<label for="sop_name'+i+'">項目名</label>\
						<input type="text" name="sop_arr['+i+'][name]" id="sop_name'+i+'" value="" style="width:250px;" />\
						<span style="padding-left:10px">\
							<label for="sop_order'+i+'">昇順</label>\
							<input type="text" name="sop_arr['+i+'][order]" id="sop_order'+i+'" value="0" style="width:35px;" />\
						</span>\
					</div>\
					<div style="margin-top:5px;">\
						グループ化\
						<input type="radio" name="sop_arr['+i+'][group_flag]" id="sop_group_flag_off'+i+'" value="0" onclick="sop_group_view(\'off\', '+i+')" checked /><label for="sop_group_flag_off'+i+'">しない</label>\
						<input type="radio" name="sop_arr['+i+'][group_flag]" id="sop_group_flag_on'+i+'" onclick="sop_group_view(\'on\', '+i+')" value="1" /><label for="sop_group_flag_on'+i+'">する</label>\
						<div id="div_group_name'+i+'" style="padding:5px 5px 0 10px;display:none;">\
							<label for="sop_group_name'+i+'">グループ名</label>\
							<input type="text" name="sop_arr['+i+'][group_name]" id="group_name'+i+'" value="" style="width:250px;" />\
						</div>\
					</div>\
					<div style="padding:5px;color:green;"><strong>入力設定</strong></div>\
					<div style="padding:0 5px 0 10px;">\
						<label for="sop_input_inline'+i+'">表示</label>\
						<select name="sop_arr['+i+'][sop_input_inline]" id="sop_input_inline'+i+'">\
							<option value="0" selected>ブロック</option>\
							<option value="1">インライン</option>\
						</select>\
						<span style="padding-left:10px;">\
							<label for="sop_type'+i+'">タイプ</label>\
							<select name="sop_arr['+i+'][type]" id="sop_type'+i+'">\
								<option value="0" selected>1行テキスト（input）</option>\
								<option value="1">改行テキスト（textarea）</option>\
							</select>\
						</span>\
					</div>\
					<div style="padding:0 5px 0 10px;">\
						<label for="sop_width'+i+'">入力幅</label>\
						<input type="text" name="sop_arr['+i+'][width]" id="sop_width'+i+'" value="130" style="width:55px;" />px\
						<span style="padding-left:10px;">\
							<label for="sop_height'+i+'">高さ</label>\
							<input type="text" name="sop_arr['+i+'][height]" id="sop_height'+i+'" value="25" style="width:55px;" />px\
						</span>\
					</div>\
					<div style="padding:5px;color:green;"><strong>公開設定</strong></div>\
					<div style="padding:0 5px 0 10px;">\
						<label for="sop_view_inline'+i+'">表示</label>\
						<select name="sop_arr['+i+'][sop_view_inline]" id="sop_view_inline'+i+'">\
							<option value="0" selected>ブロック</option>\
							<option value="1">インライン</option>\
						</select>\
						<span style="padding-left:10px;">\
							<label for="sop_view_width'+i+'">表示幅</label>\
							<input type="text" name="sop_arr['+i+'][view_width]" id="sop_view_width'+i+'" value="130" style="width:55px;" />px\
						</span>\
						<span style="padding-left:10px;">\
							<label for="sop_view_height'+i+'">高さ</label>\
							<input type="text" name="sop_arr['+i+'][view_height]" id="sop_view_height'+i+'" value="25" style="width:55px;" />px\
						</span>\
					</div>\
				</div>\
			</div>';
		//
		j('#add_html').append(html);
		j('#sop_ct').val(i);

	}
	//
	j(document).ready(function(){
		// スキルシート設定の表示on,off
		if(j('#prof_skill_flag1').attr('checked')){
			skill_options_view('on');
		}
		else{
			skill_options_view('off');
		}
		j('#prof_skill_flag1').click(function() {
			skill_options_view('on');
		});
		j('#prof_skill_flag0').click(function() {
			skill_options_view('off');
		});
		// グループの表示on,off
		sop_ct = parseInt(j('#sop_ct').val());
		for(var i=0; i<sop_ct; ++i){
			t = i + 1;
			if(j('#sop_group_flag_on'+t).attr('checked')){
				sop_group_view('on', t);
			}
			else{
				sop_group_view('off', t);
			}
		}
		//
		j('.group_flag').click(function() {
			var idname = j(this).attr("id");
			str = idname.replace(/sop_group_flag_off/, '');
			d = str.replace(/sop_group_flag_on/, '');
			//
			if(j('#sop_group_flag_on'+d).attr('checked')){
				sop_group_view('on', d);
			}
			else{
				sop_group_view('off', d);
			}
		});
		// 追加
		j('#add_sop').click(function() {
			add_sop();
		});
	});
	</script>

<?php
}
?>