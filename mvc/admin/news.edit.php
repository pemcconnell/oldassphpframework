<?php
echo $pageName;?>
<form method="post" action="" class="no-auto cmsform validate focus-name_txt" enctype="multipart/form-data">
	<fieldset>
		<ul>
			<li>
				<h3>Content</h3>
			</li>
			<li>
				<div class="help_icon"><span>Insert the name of this news story</span></div>
				<label for="name_txt">News Title</label>
				<input type="text" name="name_txt" id="name_txt" class="validate minlen-1 maxchars-150" value="<?php echo $inputvalue['name'];?>" />
			</li>
			<li>
				<div class="help_icon"><span>Insert the date of this news story</span></div>
				<label for="display_date">Date:</label>
				<input type="text" name="display_date" id="display_date" class="validate minlen-1" value="<?php echo $inputvalue['displaydate'];?>" />
			</li>
			<?php if($imgpreview) { ?>
			<li>
				<div class="help_icon"><span>A preview of the image that is currently assigned to this slide</span></div>
				<label>Preview:</label>
				<div class="imgpreview"><?php echo $imgpreview; ?></div>
				</li>
			<?php } ?>
			<li>
				<div class="help_icon"><span>Assign an image to this slide</span></div>
				<label for="img_file">Image:</label>
				<input type="file" name="img_file" id="img_file" />
			</li>
			<li>
				<div class="help_icon"><span>Insert the content of this news story</span></div>
				<label for="content_txt">Content:</label>
				<textarea name="content_txt" id="content_txt" rows="6" cols="20" class="wysiwyg"><?php echo $inputvalue['content'];?></textarea>
			</li>
			<li>
				<h3>Availability</h3>
			</li>
			<li>
				<div class="help_icon"><span>Toggle the visibility of this news item website</span></div>
				<label for="online_chk">Online:</label>
				<input type="checkbox" name="online_chk" id="online_chk" value="1"<?php if($inputvalue['online'] == 1) echo ' checked="checked"';?> />
			</li>
			<li>
				<h3>Search Engine Optimisation</h3>
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
