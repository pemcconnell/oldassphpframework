<?php
echo $pageName;
echo $addButton;?>
<p>Here you can control the bulk of content that appears on your website.</p>
<?php
if(isset($pendingapproval[0]))
{
	echo '<h2>Items Pending Approval</h2>';
	echo '<table width="100%" cellpadding="0" cellspacing="0"><tbody>'; 
	foreach($pendingapproval as $row)
	{
		echo '<tr class="odd">' . 
				'<td class="center td_view">' . $row['edit'] . '</td>' .
				'<td class="indent">' . $row['name'] . '</td>' .
			'</tr>';
	}
	echo '</tbody></table>';
} ?>
<h2>Main Menu</h2>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view" align="center">View</th>
	        <th align="left">Page Name</th>
	        <th class="td_edit" align="center">Edit</th>
	        <th class="td_sorts" align="center">Sort Order</th>
	        <th class="td_online" align="center">Online</th>
	        <th class="td_delete" align="center">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $pagedata[0]['html']; ?>
	</tbody>
</table>

<h2>Footer Menu</h2>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view" align="center">View</th>
	        <th align="left">Page Name</th>
	        <th class="td_edit" align="center">Edit</th>
	        <th class="td_sorts" align="center">Sort Order</th>
	        <th class="td_online" align="center">Online</th>
	        <th class="td_delete" align="center">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $pagedata[-1]['html']; ?>
	</tbody>
</table>

<h2>Standalone Pages</h2>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view" align="center">View</th>
	        <th align="left">Page Name</th>
	        <th class="td_edit" align="center">Edit</th>
	        <th class="td_sorts" align="center">Sort Order</th>
	        <th class="td_online" align="center">Online</th>
	        <th class="td_delete" align="center">Remove</th>
	    </tr>
	</thead>
	<tbody>
	    <?php echo $pagedata[-2]['html']; ?>
	</tbody>
</table>