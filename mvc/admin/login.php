<form method="post" action="" id="loginform" class="cmsform no-auto validate focus-uname_txt">
	<h1>CMS login</h1>
	<div class="clear">&nbsp;</div>
	<div class="hr_gray">&nbsp;</div>
	<fieldset>
		<ul>
			<li>
				<div class="help_icon"><span>Enter your login username</span></div>
				<label for="uname_txt">Username:</label>
				<input type="text" name="uname_txt" id="uname_txt" class="validate minlen-2" />
				<div id="uname_txt_validation" class="validationMessage">Please insert your username</div>
			</li>
			<li>
				<div class="help_icon"><span>Enter your password</span></div>
				<label for="pwd_txt">Password:</label>
				<input type="password" name="pwd_txt" id="pwd_txt" class="validate minlen-2" />
				<div id="pwd_txt_validation" class="validationMessage">Please insert your password</div>
			</li>
			<li>
				<div class="help_icon"><span>If checked, this feature will keep you logged in for longer</span></div>
				<label for="remember_chk">Keep me logged in:</label>
				<input type="checkbox" name="remember_chk" id="remember_chk" checked="checked" value="1" />
			</li>
		</ul>
		<div class="clear">&nbsp;</div>
		<input type="submit" class="orange" name="sub_btn" value="Log In &raquo;" />
	</fieldset>
	<div class="hr_gray">&nbsp;</div>
	<a href="./forgot">Forgotten your password? Click here</a>
</form>
