<?php

class PagesController extends AdminIndexController
{
    private     $aExcludedValidationFields = array();

	public function __construct()
	{
		parent::__construct();
	}

	/** DATA HANDLING **/
	public function setValidation()
	{
		$fields = array(
			'menuName' => array('minlen', 'Please specify the menu name', 1),
			'name' => array('minlen', 'Please insert a name'),
			'metaTitle' => array('minlen', 'Please add a meta title'),
			'metaDescription' => array('minlen', 'Please add a meta description')
		);

        // REMOVE ANY EXCLUDED FIELDS IF REQUIRED
        foreach($fields as $k => $v)
        {
            if(in_array($k, $this->aExcludedValidationFields)) unset($fields[$k]);
        }

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
			'menuName' => $this->getVal('menuname_txt'),
			'name' => $this->getVal('pagename_txt'),
			'type' => (int)$this->getVal('pagelink_sel'),
			'content' => $this->getVal('content_txt'),
			'showName' => (isset($_POST['showname_chk']) ? 1 : 0),
			'parent' => (int)$this->getVal('location_sel'),
			'online' => (isset($_POST['online_chk']) ? 1 : 0),
			'target' => $this->getVal('customurl_txt'),
			'metaTitle' => $this->getVal('metatitle_txt'),
			'metaDescription' => $this->getVal('metadesc_txt')
		);

		if($fields['content'] != '') $fields['content'] = $this->tidyWYSIWYGcontent($fields['content']);

		if($fields['type'] == -1) // OTHER WEBSITE
		{
			$fields['target'] = HTML::absoluteURL($this->getVal('freelink_txt'));
		}

        if ($fields['type'] !== 0) { // NOT A DEFAULT PAGE (MENU NAME IS THE ONLY FIELD REQUIRED)
            $this->aExcludedValidationFields = array(
                'name', 'metaTitle', 'metaDescription'
            );
        }
		return $fields;
	}
	/** END DATA HANDLING **/

	# INDEX
	protected function createPageViewLink(array $row, array $aParams)
	{
		if($row['type'] != -1) $uri = $this->createPageLink($row, $aParams);
		else $uri = stripslashes($row['target']);
		return $this->createViewLink($uri);
	}

	protected function cmsPagesIndexName(array $row, array $aParams)
	{
		$ext = '';
		if($row['type'] != 0)
		{
			if($row['type'] == -1) $ext = '&nbsp;&nbsp;&nbsp;<em>(external link)</em>';
			else $ext = '&nbsp;&nbsp;&nbsp;<em>(internal page link)</em>';
		}
		return $this->cmsViewTableIndentByStep($row, $aParams) . stripslashes($row['menuName']) . $ext;
	}

	public function index()
	{
		$layout = array(
			'_@CLASS:center td_view@__@FUNC:createPageViewLink@_',
			'_@FUNC:cmsPagesIndexName@_',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/edit/_@id@_" class="icon_edit" title="Edit this item">Edit</a>',
			'_@CLASS:center@_<div class="sortOrderVisible">_@FUNC:createSorts@_</div>',
			'_@CLASS:center@__@FUNC:createOnlineToggle@_',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/delete/_@id@_" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a>'
		);

		$aRows = $this->MODEL->getTblData($this->dbLayout);

		for($i = 0; $i > -3; $i--) $this->templatevars['pagedata'][$i] = $this->cmsViewTable($this->dbLayout, $aRows, $layout, $i);

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

	public function relatedLabel($params, $row)
	{
		$id = 'related_' . $row['id'];
		$s = (in_array($row['id'], $params['selectedIndex'])) ? ' checked="checked"' : '';
		return '<input type="checkbox" value="' . $row['id'] . '" name="related[]" id="' . $id . '"' . $s . ' /><label for="' . $id . '">' . $row['menuName'] . '</label>';
	}

	public function editExt($action = 0)
	{
		$action = (int)$action;

		$aRows = $this->MODEL->getTblData();
		if(isset($aRows[$action]))
		{
			$this->templatevars['inputvalue'] = $aRows[$action];
			unset($aRows[$action]); // REMOVED SO THE USER CAN'T SELECT ITSELF OR A CHILD OF ITSELF
		}

		$aWrapFunctionParams = array(
			'loopingFunction' => 'wrapOptions',
			'loopingFunctionInlineWrapper' => 'optionPageName',
			'selectedIndex' => $this->templatevars['inputvalue']['type'],
			'dbLayout' => $this->dbLayout,
			'dbData' => $aRows
		);

		// PAGE TYPE LIST
		for($i = 0; $i > -3; $i--) $this->templatevars['inputvalue']['pagetype'][$i] = $this->recurringWrapIndexesWithFunction($aWrapFunctionParams, $i);

		// LOCATIONS LIST
		$aWrapFunctionParams['selectedIndex'] = $this->templatevars['inputvalue']['parent'];
		for($i = 0; $i > -3; $i--) $this->templatevars['inputvalue']['locations'][$i] = $this->recurringWrapIndexesWithFunction($aWrapFunctionParams, $i);

		$aWrapFunctionParams = array(
				'loopingFunction' => 'wrapLIs',
				'loopingFunctionInlineWrapper' => 'relatedLabel',
				'selectedIndex' => $this->templatevars['inputvalue']['type'],
				'dbLayout' => $this->dbLayout,
				'dbData' => $aRows
		);

		// RELATED LIST
		$aWrapFunctionParams['selectedIndex'] = $this->MODEL->fetchRelatedPages($action);
		$this->templatevars['related'] = array();
		for($i = 0; $i > -3; $i--) $this->templatevars['related'][$i] = $this->recurringWrapIndexesWithFunction($aWrapFunctionParams, $i);

		// CUSTOM JS
		$this->templatevars['GBL_inlinefooterscripts'][] = "$(document).ready(function(){cms.pageSpecific.pages.typeChange(base.doc.getElementById('pagelink_sel'));});";
	}

	public function editPostQuery($id)
	{
		// RELATED PAGES
		$this->MODEL->clearRelatedPages($id);
		$r = $this->getVal('related');
		if(is_array($r))
		{
			$this->MODEL->insertRelatedPages($id, $r);
		}
		// INFORM SEO COMPANIES
		if(LIVEMODE && ($this->mvc['VIEW'] == 'create'))
		{
		    $row = $this->MODEL->fetchPage($id, array(0,1));
		    $url = $this->createPageLink($row);
		    Seo::tellAllSearchEnginesThereIsANewPage($url);
		}
	}

	public function __destruct()
	{
		parent::__destruct();
	}
}