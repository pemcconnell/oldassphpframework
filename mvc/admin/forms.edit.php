<?php
echo $pageName;?>
<form method="post" action="" class="no-auto cmsform validate focus-page_sel">
	<fieldset>
		<ul>
			<li>
				<div class="help_icon"><span>Choose which page you wish this form to appear on</span></div>
				<label for="name_txt">Page:</label>
				<select name="page_sel" id="page_sel">
					<optgroup label="&nbsp;&nbsp;&nbsp;Main Menu">
						<?php echo $inputvalue['pages'][0]['html'];?>
					</optgroup>
					<optgroup label="&nbsp;&nbsp;&nbsp;Utility Nav">
						<?php echo $inputvalue['pages'][-1]['html'];?>
					</optgroup>
					<optgroup label="&nbsp;&nbsp;&nbsp;Footer Menu">
						<?php echo $inputvalue['pages'][-2]['html'];?>
					</optgroup>
					<optgroup label="&nbsp;&nbsp;&nbsp;Standalone Pages">
						<?php echo $inputvalue['pages'][-3]['html'];?>
					</optgroup>
				</select>
			</li>
			<li>
				<div class="help_icon"><span>Assign a title for this form</span></div>
				<label for="name_txt">Title:</label>
				<input type="text" name="name_txt" id="name_txt" class="validate minlen-1" value="<?php echo $inputvalue['name']; ?>" />
			</li>
			<li>
				<div class="help_icon"><span>Insert the content for your page</span></div>
				<label for="content_txt">Content:</label>
				<div class="wysiwyg_wrapper"><textarea name="content_txt" id="content_txt" class="wysiwyg" rows="5" cols="16"><?php echo htmlspecialchars($inputvalue['content']);?></textarea></div>
			</li>
			<li>
				<div class="help_icon"><span>Here you build your form</span></div>
				<label>Form Inputs</label>
				<div id="formbuilder_input_btn">
				<a class="btn_add viewPageAddBtn" href="#" onclick="return cms.dynform.show();">Add Input</a>
				</div>
				<?php include 'ajax' . DS . 'formbuilder.inc.php'; ?>
				<div class="clear">&nbsp;</div>
			</li>
			<li>
				<div class="help_icon"><span>Toggle the visibility of this page on your website</span></div>
				<label for="online_chk">Online:</label>
				<input type="checkbox" name="online_chk" id="online_chk" value="1"<?php if($inputvalue['online'] == 1) echo ' checked="checked"';?> />
			</li>
		</ul>
		<div class="clear">&nbsp;</div>
		<input type="submit" name="sub_btn" class="orange" value="Save &raquo;" />
	</fieldset>
</form>