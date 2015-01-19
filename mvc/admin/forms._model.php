<?php

class FormsModel extends AdminBaseModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getFormTblData($dbLayout = false, $parent = 0, $id = 0, $online = false, $idname = 'id', $parentname = 'parent')
	{
		$aRet = array();
		$sql = "SELECT
					a.id, a.name, a.content, a.timestamp, a.created, a.adminId, a.sortOrder, a.online,
					b.id AS pageId, b.target AS pageTarget, b.name AS pageName, b.type AS pageType
				FROM
					forms a
					INNER JOIN pages b ON a.pageId = b.id
				WHERE
					a.online IN (0,1)";
		$sql = $this->query($sql);
		while($row = $sql->fetch())
		{
			$aRet[$row['id']] = $row;
		}
		return $aRet;
	}
	
	public function fetchPages()
	{
		$sql = "SELECT * FROM pages WHERE online <> 2 ORDER BY parent, sortOrder";
		$sql = $this->query($sql);
		$aRet = array();
		while($row = $sql->fetch())
		{
			$aRet[$row['id']] = $row;
		}
		return $aRet;
	}
	
	public function clearInputs($id)
	{
		$sql = "DELETE FROM form_questions WHERE formId = :id";
		$this->query($sql, array('id' => $id));
	}
	
	public function addInput($row, $id, $so)
	{
		$sql = "INSERT INTO
					form_questions
				(title, formId, elemId, valType, valLen, sortOrder, online)
					VALUES
				(:title, :formId, :elemId, :valType, :valLen, :sortOrder, :online)";
		$aParams = array(
			'title' => $row['lbl'], 
			'formId' => $id, 
			'elemId' => $row['type'],  
			'valType' => $row['valtype'], 
			'valLen' => $row['valparam'], 
			'sortOrder' => $so, 
			'online' => 1
		);
		$this->query($sql, $aParams);
		
		if(isset($row['opts']))
		{
			$aExp = explode('|', $row['opts']);
			foreach($aExp as $k => $v)
			{
				if(!is_numeric($v)) unset($aExp[$k]);
			}
			$id = $this->lastId();
			foreach($aExp as $v)
			{
				$sql = "INSERT INTO
							form_options
						(questionId, label, value)
							VALUES
						(:qId, :lbl, :value)";
				$aParam = array('qId' => $id, 'lbl' => $v, 'value' => '');
				$this->query($sql, $aParams);
			}
		}
	}
	
	public function fetchExistingOptions($id)
	{
		$aR = array();
		if((int)$id > 0)
		{
			$sql = "SELECT * FROM form_questions WHERE formId = " . (int)$id . " ORDER BY sortOrder";
			$sql = $this->query($sql);
			while($row = $sql->fetch())
			{
				$aR[] = array(
					'lbl' => $row['title'],
					'type' => $row['elemId'],
					'valtype' => $row['valType'],
					'valparam' => $row['valLen']
				);
			}
		}
		return $aR;
	}
	
	public function fetchFormInfo($formId)
	{
		$sql = "SELECT * FROM forms WHERE id = " . (int)$formId . " LIMIT 1";
		$sql = $this->query($sql);
		$row = $sql->fetch();
		return $row;
	}
	
	public function fetchFormEntries($submissionId)
	{
		$a = array();
		$sql = "SELECT
					a.id, a.submissionId, a.formId, a.memberId, a.inputId, a.value AS answer,
					b.id AS questionId, b.title AS question, b.elemId
				FROM 
					form_answers a
					INNER JOIN
						form_questions b ON a.inputId = b.id
				WHERE 
					a.submissionId = " . (int)$submissionId;
		$sql = $this->query($sql);
		while($row = $sql->fetch())
		{
			$a[] = $row;
		}
		return $a;
	}
	
	public function fetchFormSubmissions($formId, $orderby)
	{
		$a = array();
		$sql = "SELECT 
					a.id, a.memberId, a.datetime, b.firstname, b.lastname 
				FROM 
					form_submissions a
					INNER JOIN members b ON a.memberId = b.id 
				WHERE 
					a.formId = " . (int)$formId . " AND a.online = 1
				ORDER BY
					" . $orderby;
		$sql = $this->query($sql);
		while($row = $sql->fetch())
		{
			$a[] = $row;
		}
		return $a;
	}
	
	public function delsubmission($action)
	{
		$sql = "UPDATE
					form_submissions
				SET
					online = 0
				WHERE 
					id = " . (int)$action . " LIMIT 1";
		$this->query($sql);
	}
	
	public function fetchFormInfoBySubmissionId($submissionId)
	{
		$sql = "SELECT 
					a.id, a.pageId, a.name, a.content, a.timestamp, a.created, a.online,
					c.firstname, c.lastname 
				FROM 
					forms a
					INNER JOIN
						form_submissions b ON a.id = b.formId
					INNER JOIN
						members c ON b.memberId = c.id
				WHERE 
					b.id = " . (int)$submissionId . " LIMIT 1";
		$sql = $this->query($sql);
		$row = $sql->fetch();
		return $row;
	}
	
	public function getMultiOpt($answer)
	{
		$sql = "SELECT * FROM form_options WHERE id = " . (int)$answer . " LIMIT 1";
		$sql = $this->query($sql);
		$row = $sql->fetch();
		return $row['label'];
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}