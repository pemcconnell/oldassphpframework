			</div>
		</li>
    </ul>
    <div id="footer">
        <div class="right">CMS v<?php echo $GBL_version . ' | ' . $GBL_lastupdated; ?>) &copy; <?php echo date('Y'); ?></div>
    </div>
</div>
<div id="jspopup"><div id="jspopup_title">title</div><a id="jspopup_close" href="#" onclick="return cms.jspopup.toggle('none');">x</a><div id="jspopup_message">message</div></div>
<?php 
foreach($GBL_footerscripts as $script) echo '<script type="text/javascript" src="' . $script . '"></script>' . "\n";
if(isset($GBL_inlinefooterscripts) && (count($GBL_inlinefooterscripts)!=0))
{
	echo "<script type=\"text/javascript\">";
	foreach($GBL_inlinefooterscripts as $script) echo $script . "\n";
	echo "</script>";
}
?>
</body>
</html>