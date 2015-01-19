<?php
echo $breadcrumb;

if($showName)
{
	echo '<h1>' . $pageName . '</h1>';
}
echo $content; ?>
<p><strong>Required fields are marked *</strong></p>
<form method="post" action="" class="cmsform validate focus-c_name_txt contactform">
	<fieldset>
		<ul class="cleanList">
			<li>
				<label for="c_name_txt">Full Name:<span class="req">*</span></label>
				<input type="text" name="c_name_txt" id="c_name_txt" class="validate minlen-1" value="" />
				<div id="c_name_txt_validation" class="validationMessage">Required Field</div>
			</li>
			<li>
				<label for="c_email_txt">Email:<span class="req">*</span></label>
				<input type="text" name="c_email_txt" id="c_email_txt" class="validate email-" value="" />
				<div id="c_email_txt_validation" class="validationMessage">Required Field</div>
			</li>
			<li>
				<label for="c_tel_txt">Telephone:<span class="req">*</span></label>
				<input type="text" name="c_tel_txt" id="c_tel_txt" value="" class="validate minlen-1" />
				<div id="c_tel_txt_validation" class="validationMessage">Required Field</div>
			</li>
			<li>
				<label for="c_company_txt">Company Name:</label>
				<input type="text" name="c_company_txt" id="c_company_txt" value="" />
			</li>
			<li>
				<label for="c_msg_txt">Message:<span class="req">*</span></label>
				<textarea name="c_msg_txt" id="c_msg_txt" rows="4" cols="10" class="validate minlen-1"></textarea>
				<div id="c_msg_txt_validation" class="validationMessage">Required Field</div>
			</li>
		</ul>
		<input type="submit" name="sub_btn" value="Send Message" class="sub_btn redbtn" />
	</fieldset>
</form>