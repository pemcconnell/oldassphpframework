<?php
echo $pageName; ?>
<form method="post" action="" class="no-auto cmsform validate focus-name_txt">
	<fieldset>
		<ul>
			<li>
				<div class="help_icon"><span>Insert the title as it will appear on this page</span></div>
				<label for="name_txt">Job Title:</label>
				<input type="text" name="name_txt" id="name_txt" class="validate minlen-1 maxchars-75" value="<?php echo $inputvalue['name'];?>" />
			</li>
			<li>
				<div class="help_icon"><span>Insert the content for your page</span></div>
				<label for="content_txt">Content:</label>
				<div class="wysiwyg_wrapper"><textarea name="content_txt" id="content_txt" class="wysiwyg" rows="5" cols="16"><?php echo htmlspecialchars($inputvalue['content']);?></textarea></div>
				<a class="viewtagclouds" href="#" onclick="return cms.viewkeytags('content_txt');">View the key words from this content</a>
			</li>
			<li>
				<div class="help_icon"><span>Set the meta title for this page</span></div>
				<label for="metatitle_txt">Meta Title:</label>
				<input type="text" name="metatitle_txt" id="metatitle_txt" class="validate minlen-1 maxchars-70" value="<?php echo $inputvalue['metaTitle'];?>" />
			</li>
			<li>
				<div class="help_icon"><span>Set the meta description for this page</span></div>
				<label for="metadesc_txt">Meta Description:</label>
				<textarea name="metadesc_txt" id="metadesc_txt" rows="5" cols="16" class="validate minlen-1 maxchars-150"><?php echo $inputvalue['metaDescription'];?></textarea>
			</li>
			<li>
				<div class="help_icon"><span>Toggle the visibility of this page on your website</span></div>
				<label for="online_chk">Online:</label>
				<input type="checkbox" name="online_chk" id="online_chk" value="1"<?php if($inputvalue['online'] == 1) echo ' checked="checked"';?> />
			</li>
		</ul>
		<input type="submit" name="sub_btn" class="orange" value="Save &raquo;" />
	</fieldset>
</form>