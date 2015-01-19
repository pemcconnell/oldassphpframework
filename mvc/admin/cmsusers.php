<?php
echo $pageName;
echo $addButton;?>
<p>Here you can control all of the websites administrators.</p>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th align="left" class="indent">Name</th>
	        <th align="left">Level</th>
	        <th class="td_edit" align="center">Edit</th>
	        <th class="td_delete" align="center">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $pagedata['html']; ?>
	</tbody>
</table>