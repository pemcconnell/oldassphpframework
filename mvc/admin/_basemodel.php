<?php

class AdminBaseModel extends BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function cmslogin($name, $pwd = false, $id = 0)
	{
		$aParams = array('name' => $name, 'pwd' => $pwd, 'id' => (int)$id);
		$query = "SELECT * FROM _cmsusers WHERE name = :name AND ";
		if($pwd != false)
		{
			unset($aParams['id']);
			$query .= "pwd = :pwd";
		} else {
			unset($aParams['pwd']);
			$query .= "id = :id";
		}
		$query .= " LIMIT 1";
		$sql = $this->query($query, $aParams);

		if($sql) return $sql->fetch();
		return false;
	}

	/**
	  * adjustSortOrders()
	  *
	  * used when a 'delete' has been called (be it update online to 2, or a proper delete)
	  * will adjust surrounding items so that their sortOrders will decrement accordingly
	  */
	private function adjustSortOrders($action, array $dbLayout)
	{
		$currentSortOrder = 999;
		$currentParent = false;
		# GET CURRENT SORTORDER SO WE CAN DECREMENT ALL ITEMS ABOVE IT
		$soSql = "SELECT * FROM " . $dbLayout['tbl'] . " WHERE " . $dbLayout['id'] . " = " . (int)$action . " LIMIT 1";
		$soSql = $this->query($soSql);
		if($soSql)
		{
			$soRow = $soSql->fetch();
			if(isset($soRow[$dbLayout['sortOrder']]))
			{
				$currentSortOrder = $soRow[$dbLayout['sortOrder']];
				if(isset($dbLayout['parent']) && ($dbLayout['parent'] != false))
				{
					if(isset($soRow[$dbLayout['parent']])) $currentParent = $soRow[$dbLayout['parent']];
					else {
						$this->console->exception('Could not find parent data');
					}
				}
			} else {
				$this->console->exception('Could not find existing data for update');
			}
		} else {
			$this->console->exception('Could not execute request to find existing data for update');
		}
		# UPDATE OTHER ROWS (?IN PARENT SPACE)
		$soSql = "UPDATE " . $dbLayout['tbl'] . " SET " . $dbLayout['sortOrder'] . " = (" . $dbLayout['sortOrder'] . " - 1)";
		$soSqlWheres = array($dbLayout['sortOrder'] . " >= " . (int)$currentSortOrder);
		if($currentParent !== false) $soSqlWheres[] = $dbLayout['parent'] . " = " . (int)$currentParent;
		$soSql .= " WHERE " . implode(' AND ', $soSqlWheres);
		if(!$this->query($soSql))
		{
			$this->console->exception('Could not update surrounding items for sortorder change');
		}
	}

	public function delete($action, array $dbLayout)
	{
		$action = (int)$action;
		if($action < 1)
		{
			$this->console->exception('Attempted to delete an item with a primary ID of LTE 0');
		}
		if(isset($dbLayout['sortOrder']) && ($dbLayout['sortOrder'] != false))
		{
			$this->adjustSortOrders($action, $dbLayout);
		}
		$sql = "DELETE FROM " . $dbLayout['tbl'] . " WHERE " . $dbLayout['id'] . " = " . $action . " LIMIT 1";
		return $this->query($sql);
	}

	public function onlineUpdate($state = 0, $action, array $dbLayout)
	{
		$action = (int)$action;
		if($action < 1)
		{
			$this->console->exception('Attempted to update an item with a primary ID of LTE 0');
		}
		$sql = "UPDATE " . $dbLayout['tbl'] . " SET " . $dbLayout['online'] . " = " . (int)$state;
		if(($state == 2) && isset($dbLayout['sortOrder']) && ($dbLayout['sortOrder'] != false)) // IF 'DELETED'
		{
			$sql .= ", " . $dbLayout['sortOrder'] . " = 0";
			$this->adjustSortOrders($action, $dbLayout);
		}
		$sql .= " WHERE " . $dbLayout['id'] . " = " . $action . " LIMIT 1";
		return $this->query($sql);
	}

	public function sort($dir, $id, $dbColData)
	{
		$sql  = "SELECT * FROM " . $dbColData['tbl'] . " WHERE " . $dbColData['id'] . " = " . (int)$id . " LIMIT 1";
		$row = $this->query($sql)->fetch();
		if($row)
		{
			$currentId = $row[$dbColData['id']];
			$currentSort = $row[$dbColData['sortOrder']];
			$nextSort = ($dir == 'sortup') ? ($currentSort - 1) : ($currentSort + 1);
			$parentWhere = "";
			if(isset($dbColData['parent']) && isset($row[$dbColData['parent']])) $parentWhere = $dbColData['parent'] . " = " . $row[$dbColData['parent']];

			$orderDir = $dbColData['sortOrder'] . " > " . $currentSort;
			$ascOrDesc = " ASC";
			if($dir == 'sortup')
			{
				$ascOrDesc = " DESC";
				$orderDir = $dbColData['sortOrder'] . " < " . $currentSort;
			}
			if(isset($dbColData['online']) && ($dbColData['online'] != false)) $orderDir .= " AND " . $dbColData['online'] . " IN (0,1)";
			$orderDir .= " ORDER BY " . $dbColData['sortOrder'] . $ascOrDesc . " LIMIT 1";

			$osql = "SELECT * FROM " . $dbColData['tbl'] . " WHERE ";
			if($parentWhere != "") $osql .= $parentWhere . " AND ";
			$osql .= $orderDir;

			$orow = $this->query($osql)->fetch();
			if($orow)
			{
				$tusqlres = false;
				$ousqlres = $this->query("UPDATE " . $dbColData['tbl'] . " SET " . $dbColData['sortOrder'] . " = " . $currentSort . " WHERE " . $dbColData['id'] . " = " . $orow[$dbColData['id']]);
				if($ousqlres) $tusqlres = $this->query("UPDATE " . $dbColData['tbl'] . " SET " . $dbColData['sortOrder'] . " = " . $nextSort . " WHERE " . $dbColData['id'] . " = " . $currentId);
			}
		}
	}

	public function __destruct()
	{
		parent::__destruct();
	}
}