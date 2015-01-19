<?php

class FormsController extends AdminIndexController
{
	public		$sOrderBy = 'surname',
				$aOrderByOpts = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->dbLayout['sortOrder'] = false;
		$this->dbLayout['parent'] = false;
		
		$this->templatevars['delsubmissionkey'] = false;
		
		$this->initSort();
	}
	
	private function initSort()
	{
		$a = array(
				'surname' => 'b.lastname, b.firstname',
				'forename' => 'b.firstname, b.lastname',
				'date' => 'a.datetime DESC'
		);
		
		$this->aOrderByOpts = $a;
		
		if(!isset($_SESSION[BASE_HREF]['admin']['forms']['sortOrder']))
		{
			$_SESSION[BASE_HREF]['admin']['forms']['sortOrder'] = $this->sOrderBy;
		}
		
		if(isset($_POST['sort_btn']))
		{
			$_SESSION[BASE_HREF]['admin']['forms']['sortOrder'] = $this->getVal('sort_sel');
		}
		
		$this->sOrderBy = $_SESSION[BASE_HREF]['admin']['forms']['sortOrder'];
		
		$this->templatevars['aOrderByOpts'] = $this->aOrderByOpts;
		$this->templatevars['sOrderBy'] = $this->sOrderBy;
	}
	
	public function sOrderByOpt($sOrderByKey)
	{
		return $this->aOrderByOpts[$sOrderByKey];
	}
	
	/** DATA HANDLING **/
	 
	public function setValidation()
	{
		$fields = array(
			'name' => 'minlen'
		);
		return $fields;
	}
	
	public function inputDefaults()
	{
		$fields = array(
			'online' => 1
		);
		return $fields;
	}
	
	protected function getAndSetFields()
	{
		$fields = array(
			'pageId' => $this->getVal('page_sel'),
			'name' => $this->getVal('name_txt'),
			'content' => $this->getVal('content_txt'),
			'online' => (isset($_POST['online_chk']) ? 1 : 0)
		);
		
		if($fields['content'] != '') $fields['content'] = $this->tidyWYSIWYGcontent($fields['content']);
		
		return $fields;
	}
	/** END DATA HANDLING **/
	
	# INDEX
	protected function createFormViewLink(array $row, array $aParams)
	{
		$row['target'] = $row['pageTarget'];
		$row['id'] = $row['pageId'];
		$row['menuName'] = $row['pageName'];
		$row['type'] = $row['pageType'];
		$uri = BASE_HREF . $this->createPageLink($row, $aParams);
		return $this->createViewLink($uri);
	}
	
	public function index()
	{
		$layout = array(
			'_@CLASS:center td_view@__@FUNC:createFormViewLink@_',
			'_@name@_',
			'_@pageName@_',
			'<a class="btn_report" href="' . BASE_HREF . 'admin/' . $this->mvc['CONTROLLER'] .'/report/_@id@_">Report</a>',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/edit/_@id@_" class="icon_edit" title="Edit this item">Edit</a>',
			'_@CLASS:center@__@FUNC:createOnlineToggle@_',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/delete/_@id@_" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a>'
		);
		
		$aRows = $this->MODEL->getFormTblData($this->dbLayout);
		
		$this->templatevars['listdata'] = $this->cmsViewTable($this->dbLayout, $aRows, $layout);
	}
	
	# EDIT/CREATE
	public function optionPageName(array $aFunctionParams, array $row)
	{
		$spacer = '';
		if($aFunctionParams['params']['stepinc'] > 0)
		{
			$multiplier = ($aFunctionParams['params']['stepinc'] * 5);
			for($i = 0; $i < $multiplier; $i++) $spacer .= '&nbsp;';
		}
		return $spacer . '&raquo; ' . stripslashes($row['menuName']);
	}
	
	public function editExt($action = 0)
	{
		$_SESSION[BASE_HREF]['admin']['dynform']['options'] = $this->MODEL->fetchExistingOptions($action);
		
		$this->templatevars['inputvalue']['pages'] = array();
		
		$dbl = array('tbl' => 'pages', 'id' => 'id', 'name' => 'name', 'sortOrder' => 'sortOrder', 'parent' => 'parent', 'online' => 'online');
		$aFunctionParams = array(
			'loopingFunction' => 'wrapOptions',
			'loopingFunctionInlineWrapper' => 'optionPageName',
			'selectedIndex' => (int)$this->templatevars['inputvalue']['pageId'],
			'dbLayout' => $dbl,
			'dbData' => $this->MODEL->getTblData($dbl)
		);
		for($i = 0; $i > -4; $i--) $this->templatevars['inputvalue']['pages'][$i] = $this->recurringWrapIndexesWithFunction($aFunctionParams, $i);
	}
	
	public function editPostQuery($id)
	{
		$this->MODEL->clearInputs($id);
		$so = 1;
		foreach($_SESSION[BASE_HREF]['admin']['dynform']['options'] as $row)
		{
			$this->MODEL->addInput($row, $id, $so);
			++$so;
		}
	}
	
	public function export($formId)
	{
		$aHeaders = array();
		$aBody = array();
	
		$sql = "SELECT * FROM form_questions WHERE elemId <> 6 AND formId = " . (int)$formId . " ORDER BY sortOrder";
		$sql = $this->MODEL->query($sql);
		while($row = $sql->fetch())
		{
			$aHeaders[$row['id']] = $row['title'];
		}
		
		$aSubs = $this->MODEL->fetchFormSubmissions($formId, $this->sOrderByOpt($this->sOrderBy));
		
		foreach($aSubs as $sub)
		{
			$e = $this->MODEL->fetchFormEntries($sub['id']);
			$aExisting = array();
			foreach($e as $k => $v)
			{
				if(in_array($v['elemId'], array(3, 4, 5)))
				{
					$v['answer'] = $this->MODEL->getMultiOpt($v['answer']);
				}
				if(!isset($aExisting[$v['inputId']]))
				{
					$e[$k] = $v['answer'];
					$aExisting[$v['inputId']] = $k;
				} else {
					$e[$aExisting[$v['inputId']]] .= ', ' . $v['answer']; 
					unset($e[$k]);
				}
			}
			$aBody[] = $e;
		}
		
		CSV::output($aHeaders, $aBody, 'formexport.csv');
		
		die();
	}
	
	public function delsubmission($action)
	{
		// ADD MESSAGE
		$this->console->formsuccess('Form entry successfully removed from the system.');
		$a = explode('-', $action);
		$this->MODEL->delsubmission((int)$a[0]);
		$this->report($a[1]);
		// CHANGE TEMPLATE
		$this->mvc['TEMPLATE']['BODY'] = BASE_PATH . 'mvc' . DS . 'admin' . DS . 'forms.report.php';
	}
	
	public function report($action)
	{
		$this->templatevars['formid'] = $action;
		
		#$this->templatevars['GBL_footerscripts'][] = 'https://www.google.com/jsapi';
		#$this->templatevars['GBL_footerscripts'][] = './ajax/chart.form.php';
		
		$this->templatevars['forminfo'] = $this->MODEL->fetchFormInfo($action);
		$this->templatevars['formsubmissions'] = $this->MODEL->fetchFormSubmissions($action, $this->sOrderByOpt($this->sOrderBy));
		$this->templatevars['totalsubmissions'] = count($this->templatevars['formsubmissions']);
	}
	
	public function answers($action)
	{
		$this->templatevars['forminfo'] = $this->MODEL->fetchFormInfoBySubmissionId($action);
		$this->templatevars['formentries'] = $this->MODEL->fetchFormEntries($action);
		
		#debugA($this->templatevars['formentries']);
		
		$aExistingAnswers = array();
		foreach($this->templatevars['formentries'] as $k => $row)
		{
			if(in_array($row['elemId'], array(3, 4, 5)))
			{
				$this->templatevars['formentries'][$k]['answer'] = $this->MODEL->getMultiOpt($row['answer']);
			}
			if(isset($aExistingAnswers[$row['inputId']]))
			{
				$this->templatevars['formentries'][$aExistingAnswers[$row['inputId']]]['answer'] .= ', ' . $this->templatevars['formentries'][$k]['answer'];
				unset($this->templatevars['formentries'][$k]);
			} else {
				$aExistingAnswers[$row['inputId']] = $k;
			}
		}
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}