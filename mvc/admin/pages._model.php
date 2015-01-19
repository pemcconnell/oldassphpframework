<?php

class PagesModel extends AdminBaseModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function fetchRelatedPages($pageId)
	{
		$aRet = array();
		$sql = "SELECT * FROM pages_related WHERE pageId = :id";
		$sql = $this->query($sql, array('id' => (int)$pageId));
		while($row = $sql->fetch())
		{
			$aRet[] = $row['relatedId'];
		}
		return $aRet;
	}
	
	public function clearRelatedPages($pageId)
	{
		$sql = "DELETE FROM pages_related WHERE pageId = :id";
		$sql = $this->query($sql, array('id' => $pageId));
	}
	
	public function insertRelatedPages($pageId, array $rows)
	{
		foreach($rows as $v)
		{
			$sql = "INSERT INTO pages_related (pageId, relatedId) VALUE (" . (int)$pageId . ", " . (int)$v . ")";
			$this->query($sql);
		}
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}
