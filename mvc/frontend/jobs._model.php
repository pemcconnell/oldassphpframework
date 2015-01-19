<?php

class JobsModel extends FrontendBaseModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function fetchJobs($id = 0)
	{
		$aData = array();
		$wId = '';
		if((int)$id > 0) $wId = ' AND id = ' . (int)$id;
		$sql = "SELECT
					*
				FROM
					jobs
				WHERE
					online = 1" . $wId . "
				ORDER BY
					sortOrder";
		$sql = $this->query($sql);
		if($sql)
		{
			while($row = $sql->fetch())
			{
				$aData[] = $row;
			}
		}
		return $aData;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}