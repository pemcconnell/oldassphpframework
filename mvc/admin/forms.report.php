<h1><a href="./forms">&laquo; Back to Forms</a> | Form Report</h1>
<p>There have been <strong><?php echo $totalsubmissions; ?></strong> entries for this form. To export this data as a CSV file please <a href="./forms/export/<?php echo $forminfo['id']; ?>">click here</a>.</p>

<form method="post" action="<?php echo BASE_HREF . 'admin/forms/report/' . $this->mvc['ACTION'];?>">
	<fieldset>
		<ul class="cleanList">
			<li>
				<label for="sort_sel">
					Order By:
				</label>
				<select name="sort_sel" id="sort_sel">
					<?php
					foreach($aOrderByOpts as $k => $ignore)
					{
						$s = '';
						if($k == $sOrderBy) $s = ' selected="selected"';
						echo '<option value="' . $k . '"' . $s . '>' . ucfirst($k) . '</option>';
					}?>
				</select>
				<input type="submit" name="sort_btn" value="Change Sort Order" />
			</li>
		</ul>
	</fieldset>
</form>

<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	    <tr>
	        <th class="td_view">Answers</th>
	        <th align="left" class="indent">Stakeholder</th>
	        <th align="left" class="indent thickcol">Submitted</th>
	        <th class="center">Delete</th>
	    </tr>
	</thead>
	<tbody>
	    <?php 
	    global $MVC;
	    foreach($formsubmissions as $row): ?>
	    <tr>
	    	<td><a href="<?php echo BASE_HREF; ?>admin/forms/answers/<?php echo $row['id']; ?>" class="btn_view">View</a></td>
	    	<td class="indent"><a title="View Member Report" href="./members/report/<?php echo $row['memberId'];?>"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></a></td>
	    	<td class="indent"><?php echo $row['datetime']; ?></td>
	    	<td class="td_delete"><a href="./forms/delsubmission/<?php echo $row['id']; ?>-<?php echo $formid; ?>" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a></td>
	    </tr>
	    <?php endforeach; ?>
	</tbody>
</table>
