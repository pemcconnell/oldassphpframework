<?php
echo $pageName; ?>
<form method="post" action="" class="no-auto cmsform validate focus-menuname_txt">
	<fieldset>
		<ul>
			<li>
				<div class="help_icon"><span>Insert the name of this page as it will appear in the menu</span></div>
				<label for="menuname_txt">Menu Name:</label>
				<input type="text" name="menuname_txt" id="menuname_txt" class="validate minlen-1 maxchars-75" value="<?php echo $inputvalue['menuName'];?>" />
			</li>
			<li>
				<div class="help_icon"><span>If this is just a link to an existing page select it from the list below:</span></div>
				<label for="pagelink_sel">Type:</label>
				<select name="pagelink_sel" id="pagelink_sel" onchange="javascript:cms.pageSpecific.pages.typeChange(this);">
					<option value="0"<?php echo ($inputvalue['type'] != 0) ? '' : ' selected="selected"';?>>&raquo; Page (default)</option>
					<option value="-1"<?php echo ($inputvalue['type'] != -1) ? '' : ' selected="selected"';?>>&raquo; Link to another website</option>
					<?php
					$html = $inputvalue['pagetype'][0]['html'];
					if($html != '') echo '<optgroup label="&nbsp;&nbsp;&nbsp;Link to item on the Main Menu">' . $html . '</optgroup>';
					$html = $inputvalue['pagetype'][-1]['html'];
					if($html != '') echo '<optgroup label="&nbsp;&nbsp;&nbsp;Link to item on the Footer Menu">' . $html . '</optgroup>';
					$html = $inputvalue['pagetype'][-2]['html'];
					if($html != '') echo '<optgroup label="&nbsp;&nbsp;&nbsp;Link to item on Standalone Pages">' . $html . '</optgroup>';
					?>
				</select>
			</li>
			<li>
				<div class="help_icon"><span>Choose where you wish this page to be located on your website</span></div>
				<label for="location_sel">Location:</label>
				<select name="location_sel" id="location_sel">
					<optgroup label="&nbsp;&nbsp;&nbsp;Place this item into the Main Menu">
						<option value="0"<?php echo ($inputvalue['parent'] != 0) ? '' : ' selected="selected"';?>>Top-level item on the main menu</option>
						<?php echo $inputvalue['locations'][0]['html'];?>
					</optgroup>
					<optgroup label="&nbsp;&nbsp;&nbsp;Place this item into the Footer Menu">
						<option value="-1"<?php echo ($inputvalue['parent'] != -12) ? '' : ' selected="selected"';?>>Top-level item on the footer menu</option>
						<?php echo $inputvalue['locations'][-1]['html'];?>
					</optgroup>
					<optgroup label="&nbsp;&nbsp;&nbsp;I don't want to place this item in a menu">
						<option value="-2"<?php echo ($inputvalue['parent'] != -2) ? '' : ' selected="selected"';?>>Standalone Page</option>
						<?php echo $inputvalue['locations'][-2]['html'];?>
					</optgroup>
				</select>
			</li>
			<li>
				<div class="help_icon"><span>Toggle the visibility of this page on your website</span></div>
				<label for="online_chk">Online:</label>
				<input type="checkbox" name="online_chk" id="online_chk" value="1"<?php if($inputvalue['online'] == 1) echo ' checked="checked"';?> />
			</li>
		</ul>
		<ul id="cmspageedit_freelink" style="display:none;">
			<li>
				<div class="help_icon"><span>Type in the URL of the webpage you want this link to point to (e.g. <strong>http://</strong>www.google.co.uk)</span></div>
				<label for="freelink_txt">URL:</label>
				<input type="text" name="freelink_txt" id="freelink_txt" class="validate maxchars-256" value="<?php echo $inputvalue['target'];?>" />
			</li>
		</ul>
		<ul id="cmspageedit_inputs">
			<li>
				<div class="help_icon"><span>Insert the title as it will appear on this page</span></div>
				<label for="pagename_txt">Page Title:</label>
				<input type="text" name="pagename_txt" id="pagename_txt" class="validate minlen-1 maxchars-75" value="<?php echo $inputvalue['name'];?>" />
			</li>
			<li>
				<div class="help_icon"><span>Toggle the name from being visible on this page</span></div>
				<label for="showname_chk">Show title on page:</label>
				<input type="checkbox" name="showname_chk" id="showname_chk" value="1"<?php if($inputvalue['showName'] == 1) echo ' checked="checked"';?> />
			</li>
			<li>
				<div class="help_icon"><span>Insert the content for your page</span></div>
				<label for="content_txt">Content:</label>
				<div class="wysiwyg_wrapper"><textarea name="content_txt" id="content_txt" class="wysiwyg" rows="5" cols="16"><?php echo htmlspecialchars($inputvalue['content']);?></textarea></div>
				<a class="viewtagclouds" href="#" onclick="return cms.viewkeytags('content_txt');">View the key words from this content</a>
			</li>
			<li>
				<div class="help_icon"><span>You may choose a custom URL for this page if you wish</span></div>
				<label for="customurl_txt">Custom URL:</label>
				<input type="text" name="customurl_txt" id="customurl_txt" class="validate maxchars-200" value="<?php echo $inputvalue['target'];?>" />
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
		</ul>
		<input type="submit" name="sub_btn" class="orange" value="Save &raquo;" />
	</fieldset>
</form>
