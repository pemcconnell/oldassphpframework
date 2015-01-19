<?php
echo $pageName;?>
<form method="post" action="" class="no-auto cmsform validate focus-name_txt">
	<fieldset>
		<ul>
			<li>
				<div class="help_icon"><span>Insert the login name for this user</span></div>
				<label for="name_txt">Username:</label>
				<input type="text" name="name_txt" id="name_txt" class="validate minlen-1 maxchars-75" value="<?php echo $inputvalue['name'];?>" />
			</li>
			<li>
				<div class="help_icon"><span>Insert the login password for this user</span></div>
				<label for="pwd_txt">Password:</label>
				<input type="password" name="pwd_txt" id="pwd_txt" class="validate maxchars-20" value="" />
			</li>
		</ul>
		<input type="submit" name="sub_btn" class="orange" value="Save &raquo;" />
	</fieldset>
</form>