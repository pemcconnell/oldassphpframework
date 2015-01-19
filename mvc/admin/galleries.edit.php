<?php
echo $pageName;
global $MVC;
?>
<form method="post" action="" class="no-auto cmsform validate focus-name_txt">
        <fieldset>
                <h2>Settings</h2>
                <ul>
                        <li>
                                <div class="help_icon"><span>Select which page you would like this gallery to be assigned to</span></div>
                                <label for="page_sel">Page:</label>
                                <select name="page_sel" id="page_sel">
                                        <optgroup label="&nbsp;&nbsp;&nbsp;Main Menu">
                                                <?php echo $inputvalue['pages'][0]['html']; ?>
                                        </optgroup>
                                        <optgroup label="&nbsp;&nbsp;&nbsp;Footer Menu">
                                                <?php echo $inputvalue['pages'][-1]['html']; ?>
                                        </optgroup>
                                        <optgroup label="&nbsp;&nbsp;&nbsp;Standalone Pages">
                                                <?php echo $inputvalue['pages'][-2]['html']; ?>
                                        </optgroup>
                                </select>
                        </li>
                        <li>
                                <div class="help_icon"><span>Assign a title to this gallery</span></div>
                                <label for="name_txt">Title:</label>
                                <input type="text" name="name_txt" id="name_txt" value="<?php echo $inputvalue['name']; ?>" />
                        </li>
                                <li>
                                        <div class="help_icon"><span>Should you wish to make this item a sub-level gallery select the top level gallery here</span></div>
                                        <label for="parent_sel">Parent:</label>
                                        <select name="parent_sel" id="parent_sel">
                                                        <optgroup label="&nbsp;&nbsp;&nbsp;Main Menu">
                                                                <?php echo $inputvalue['parent'][0]['html']; ?>
                                                        </optgroup>
                                                        <optgroup label="&nbsp;&nbsp;&nbsp;Footer Menu">
                                                                <?php echo $inputvalue['parent'][-1]['html']; ?>
                                                        </optgroup>
                                                        <optgroup label="&nbsp;&nbsp;&nbsp;Standalone Pages">
                                                                <?php echo $inputvalue['parent'][-2]['html']; ?>
                                                        </optgroup>
                                        </select>
                                </li>
                        <li>
                                <div class="help_icon"><span>Toggle the visibility of this page on your website</span></div>
                                <label for="online_chk">Online:</label>
                                <input type="checkbox" name="online_chk" id="online_chk" value="1"<?php if ($inputvalue['online'] == 1) echo ' checked="checked"'; ?> />
                        </li>
                </ul>
                <input type="submit" name="sub_btn" class="orange" value="Save &raquo;" />
        </fieldset>
</form>
<h3 class="h2">Manage Images</h3>
<ul class="cmsform cleanList">
        <li>
                <div id="gallery_whiteboard">
                        <?php include 'ajax' . DS . 'gallerywhiteboard.php'; ?>
                </div>
        </li>
</ul>
<iframe name="ifrm" frameborder="0" height="1" width="1"></iframe>
<form method="post" target="ifrm" action="<?php echo BASE_HREF . 'admin/galleries/submitimg?bodyonly' ?>" class="no-auto cmsform" id="gallery_whiteboard_fields" enctype="multipart/form-data">
        <fieldset>
                <input type="hidden" name="parent" value="<?php echo $parent; ?>" />
                <input type="hidden" name="view" value="<?php echo $MVC['VIEW']; ?>" />
                <input type="hidden" name="mode" value="add" />
                <ul>
                        <li>
                                <div class="help_icon"><span>Upload an image</span></div>
                                <label for="img_file">Image:</label>
                                <input type="file" name="img_file" />
                        </li>
                        <li>
                                <div class="help_icon"><span>Assign a caption to this image</span></div>
                                <label for="name_txt">Caption:</label>
                                <input type="text" name="name_txt" id="name_txt" class="validate minlen-1 maxchars-75" value="" />
                        </li>
                        <li>
                                <div class="help_icon"><span>Assign a link to this image</span></div>
                                <label for="url_txt">Url:</label>
                                <input type="text" name="url_txt" id="url_txt" class="validate minlen-1" value="" />
                        </li>
                        <li>
                                <div class="help_icon"><span>Specify if this image is visible or not</span></div>
                                <label for="online_chk">Online:</label>
                                <input type="checkbox" name="online_chk" id="online_chk" value="1" />
                        </li>
                </ul>
                <input type="submit" name="sub_btn" class="orange" value="Save &raquo;" />
        </fieldset>
</form>