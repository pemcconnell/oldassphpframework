<?php
echo $pageName;
echo $addButton;?>
<p>Here you can manage the news articles that appear on the website.</p>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view" align="center">View</th>
	        <th align="left">Article Name</th>
	        <th align="center" width="300px">News Date</th>
	        <th class="td_edit" align="center">Edit</th>
	        <th class="td_online" align="center">Online</th>
	        <th class="td_delete" align="center">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $pagedata['html']; ?>
	</tbody>
</table>