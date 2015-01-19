<div class="addbox"><a href="#" title="Click here to add a new image" onclick="return cms.jspopup.toggle('block');">Add Image</a></div>
<?php
foreach ($imgs as $row) {
        $s = '';
        if (($row['off_x'] != 0) || ($row['off_y'] != 0)) {
                $s = ' style="position:relative;';
                if ($row['off_x'] != 0)
                        $s .= 'left:' . $row['off_x'] . 'px;';
                if ($row['off_y'] != 0)
                        $s .= 'top:' . $row['off_y'] . 'px;';
                $s .= '"';
        }
        echo '<div class="item">' .
                        '<div class="imgwrapper">' .
                                $row['edit_icons'] .
                                '<img src="' . BASE_HREF . 'uploads/cms_image/' . $row['filename'] . '" height="' . $row['height'] . '" width="' . $row['width'] . '" alt="thumbnail"' . $s . ' />' .
                        '</div>' .
                        '<div class="iptwrapper">' .
                                $row['edit_inputs'] .
                        '</div>' .
                '</div>';
} ?>
<div class="clear noheight">&nbsp;</div>