                    </div> <!-- end maincol -->
                    <div class="bodyclear">&nbsp;</div>
                </div> <!-- end fullcontent -->
            </div> <!-- end body -->
            <header id="header">
                <?php include 'inc' . DS . 'header.php';?>
            </header>
        </div>
        <footer id="footer">
            <?php include 'inc' . DS . 'footer.php';?>
        </footer>
    </div> <!-- end wrapper -->
</div> <!-- end outterwrapper -->
<?php
foreach($GBL_footerscripts as $script) echo '<script type="text/javascript" src="' . $script . '"></script>' . "\n";
if(isset($GBL_inlinefooterscripts) && (count($GBL_inlinefooterscripts)!=0))
{
	echo "<script type=\"text/javascript\">";
	echo "$(document).ready(function(){\n";
	foreach($GBL_inlinefooterscripts as $script) echo $script . "\n";
	echo "});";
	echo "</script>";
}
if(ENABLE_FIREBUG) echo '<script type="text/javascript" src="http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js"></script>';
?>
</body>
</html>