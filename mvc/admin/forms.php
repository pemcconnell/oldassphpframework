<?php
echo $pageName;
echo $addButton;?>
<p>Here you can create all of the forms that appear on this website.</p>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view">View</th>
	        <th align="left">Form Name</th>
	        <th align="left">Page</th>
	        <th align="td_edit">Report</th>
	        <th class="td_edit">Edit</th>
	        <th class="td_online">Online</th>
	        <th class="td_delete">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $listdata['html']; ?>
	</tbody>
</table>