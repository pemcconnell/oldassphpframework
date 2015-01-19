<?php
echo $pageName;
echo $addButton;?>
<p>From this section you can manage each of the jobs listed on this website.</p>
<h2>Job Listing</h2>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view center">View</th>
	        <th align="left">Name</th>
	        <th class="td_edit" align="center">Edit</th>
	        <th class="td_sorts" align="center">Sort Order</th>
	        <th class="td_online" align="center">Online</th>
	        <th class="td_delete" align="center">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $pagedata['html']; ?>
	</tbody>
</table>