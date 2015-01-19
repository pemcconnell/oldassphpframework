<?php
require_once 'ajax.header.php';

$output = array(
		0 => array(), 1 => array(), 2 => array()
);

$resultsonly = false;
if(isset($_GET['qu']) && ($_GET['qu'] != ''))
{
	$resultsonly = true;
	$sTerm = trim($_GET['qu']);
	if($sTerm != '')
	{
		$sTerm = str_replace(' ', '%', $sTerm);
		$aParams = array(
			'wildterm' => '%' . $sTerm . '%',
			'term' => $sTerm
		);
		
		$sql = "SELECT DISTINCT(part) id, name, desc1, part, revision FROM shopproducts WHERE groupedItem = 0 AND (desc1 LIKE :wildterm OR name LIKE :wildterm OR revision = :term OR part = :term) GROUP BY revision ORDER BY desc1";
		
		$sql = $DB->query($sql, $aParams);
		$aData = array();
		if($sql)
		{
			while($row = $sql->fetch())
			{
				if($row['name'] != '') $row['desc1'] = $row['name'];
				$aData[] = $row;
			}
			$output[0] = $aData;
			/*
			$tA = array_chunk($aData, 3);
			foreach($tA as $a)
			{
				foreach($a as $k=>$v)
				{
					$output[$k][] = $v;
				}
			}
			*/
		}
	}
}

function formatCell($row)
{
	return '<a href="#' . $row['id'] . '" onclick="return cms.ajxSearch.additem(' . $row['id'] . ', \'' . str_replace(array("'", '"'), "\'", $row['desc1']) . '\', \'' . str_replace(array("'", '"'), "\'", $row['part']) . '\');">' . $row['desc1'] . ' (' . $row['part'] . ')<span>+</span></a>';
}

if(!$resultsonly) { ?><div id="search_container"><div id="search_results"><?php } ?>
	<div id="search_results_iwrapper">
		<strong>Search Results</strong>
		<a href="#" class="hidebtn" onclick="return cms.ajxSearch.hidesearchwindow();">X</a>
		<div id="search_results_scrollwrapper">
			<ul id="search_results_fcol">
				<?php
				foreach($output[0] as $row)
				{
					echo '<li>' . formatCell($row) . '</li>';
				} ?>
			</ul>
			<?php /*?>
			<ul id="search_results_scol">
				<?php
				foreach($output[1] as $row)
				{
					echo '<li>' . formatCell($row) . '</li>';
				} ?>
			</ul>
			<ul id="search_results_tcol">
				<?php
				foreach($output[2] as $row)
				{
					echo '<li>' . formatCell($row) . '</li>';
				} ?>
			</ul>
			<?php */ ?>
		</div>
	</div>
<?php if(!$resultsonly) { ?>
</div>
<fieldset method="post" id="search_form">
	<ul class="cleanList">
		<li>
			<input type="text" name="search_txt" id="search_txt" value="<?php if(isset($_GET['qu'])) echo $_GET['qu']; ?>" />
		</li>
	</ul>
</fieldset>
<div id="search_store">
<?php 
if(isset($attendees) && is_array($attendees) && (count($attendees)!=0))
{
	echo '<div>';
	foreach($attendees as $row)
	{
		$name = $row['desc1'];
		if($row['name'] != '') $name = $row['name'];
		echo '<input type="hidden" name="search_stored_id[]" value="' . $row['id'] . '">';
		echo '<span>' . $name . ' (' . $row['part'] . ')</span>';
		echo '<a id="search_stored_tmp_' . $row['id'] . '" href="#">Remove</a>';
		echo '<ul style="display:none;">';
			echo '<li class="jsv_id">' . $row['id'] . '</li>';
			echo '<li class="jsv_desc1">' . $name . '</li>';
			echo '<li class="jsv_part">' . $row['part'] . '</li>';
		echo '</ul>';
	}
	echo '</div>';
}
?>
<span style="display:block; clear:both; height:1px;">&nbsp;</span>
</div>
</div>
<?php }