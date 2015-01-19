<h1><a href="./forms/report/<?php echo $forminfo['id'];?>">&laquo; Back to Form Report</a> | Form Answers</h1>
<p>Below is listed the answers entered for this form by <strong><?php echo $forminfo['firstname'] . ' ' . $forminfo['lastname'];?></strong>.</p>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th align="left" class="indent" width="48%">Question</th>
	        <th align="left" class="thickcol">Answer</th>
	    </tr>
	</thead>
	<tbody>
	    <?php foreach($formentries as $row): ?>
	    <tr>
	    	<td class="indent"><?php echo $row['question']; ?></td>
	    	<td><strong><?php echo $row['answer']; ?></strong></td>
	    </tr>
	    <?php endforeach; ?>
	</tbody>
</table>