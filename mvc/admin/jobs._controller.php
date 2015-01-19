<?php

class JobsController extends AdminIndexController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->dbLayout['parent'] = false;
	}
	
	public function setValidation()
	{
		$fields = array(
				'name' => array('minlen', 'Please insert a name'),
				'metaTitle' => array('minlen', 'Please add a meta title'),
				'metaDescription' => array('minlen', 'Please add a meta description')
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
				'name' => $this->getVal('name_txt'),
				'content' => $this->getVal('content_txt'),
				'online' => (isset($_POST['online_chk']) ? 1 : 0),
				'metaTitle' => $this->getVal('metatitle_txt'),
				'metaDescription' => $this->getVal('metadesc_txt')
		);
	
		if($fields['content'] != '') $fields['content'] = $this->tidyWYSIWYGcontent($fields['content']);
		return $fields;
	}
	
	protected function createJobViewLink(array $row, array $aParams)
	{
		return $this->createViewLink(BASE_HREF . 'jobs/view/' . $row['id'] . '/' . HTML::createCleanURL($row['name']));
	}
	
	public function index()
	{
		$layout = array(
				'_@CLASS:center td_view@__@FUNC:createJobViewLink@_',
				'_@name@_',
				'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/edit/_@id@_" class="icon_edit" title="Edit this item">Edit</a>',
				'_@CLASS:center@_<div class="sortOrderVisible">_@FUNC:createSorts@_</div>',
				'_@CLASS:center@__@FUNC:createOnlineToggle@_',
				'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/delete/_@id@_" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a>'
		);
	
		$aRows = $this->MODEL->getTblData($this->dbLayout);
	
		$this->templatevars['pagedata'] = $this->cmsViewTable($this->dbLayout, $aRows, $layout);
	}
	
	public function editExt($action = 0)
	{
		
	}

	public function __destruct()
	{
		parent::__destruct();
	}
}