<?php

class CmsusersController extends AdminIndexController
{
	
	public function __construct()
	{
		parent::__construct();
		$this->dbLayout['tbl'] = '_cmsusers';
		$this->dbLayout['sortOrder'] = false;
		$this->dbLayout['parent'] = false;
	}
	
	/** DATA HANDLING **/
	public function setValidation()
	{
		$fields = array(
			'name' => 'minlen'
		);
		return $fields;
	}
	
	protected function getAndSetFields()
	{
		if(isset($_POST['pwd_txt']) && ($_POST['pwd_txt'] != '')) $pwd = Validate::password($_POST['pwd_txt'], 'Please insert your password', 1);
		else $pwd = false;
		$fields = array(
			'name' => $this->getVal('name_txt'),
			'pwd' => $pwd,
			'level' => (int)$this->getVal('level_sel')
		);
		if(isset($_POST['sub_btn']))
		{
			if($fields['level'] == 0) $fields['level'] = 1;
			if(!$pwd) unset($fields['pwd']);
		}
		$fields['online'] = 1;
		#debugA($fields);
		return $fields;
	}
	/** END DATA HANDLING **/
	
	# INDEX
	protected function showCmsUserLevel($row, $aParams)
	{
		$lvl = '';
		switch($row['level'])
		{
			case 9:
				$lvl = 'Developer';
			break;
			case 2:
				$lvl = 'Super Admin';
			break;
			case 1:
				$lvl = 'Admin';
			break;
			default:
				$lvl = '<em style="color:#F00">Undefined</em>';
				$this->console->warning('Undefined level set for cms user ' . $row['name']);
		}
		return $lvl;
	}
	
	public function index()
	{
		$this->templatevars['pageName'] = $this->cmsTitle('manage', 'Administrators');
		$this->templatevars['addButton'] = $this->cmsAddBtn('Administrator');
		
		$layout = array(
			'_@CLASS:indent@__@name@_',
			'_@FUNC:showCmsUserLevel@_',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/edit/_@id@_" class="icon_edit" title="Edit this item">Edit</a>',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/delete/_@id@_" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a>'
		);
		
		$aRows = $this->MODEL->getTblData($this->dbLayout);
		$this->templatevars['pagedata'] = $this->cmsViewTable($this->dbLayout, $aRows, $layout);
	}
	
	public function editExt()
	{
		$this->templatevars['pageName'] = $this->cmsTitle($this->mvc['VIEW'], 'Administrator');
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}
