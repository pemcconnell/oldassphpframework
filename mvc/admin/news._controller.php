<?php
class NewsController extends AdminIndexController {

	public function __construct() {
		parent::__construct();

		$this->dbLayout['parent'] = false;
		$this->dbLayout['sortOrder'] = false;
		$this->dbLayout['sortOrder_custom'] = 'displaydate DESC';
	}

		/** DATA HANDLING **/
	public function setValidation()
	{
		$fields = array(
			'name' => 'minlen',
			'content' => 'minlen'
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
			'displaydate' => $this->getVal('display_date'),
			'online' => (isset($_POST['online_chk']) ? 1 : 0),
			'metaTitle' => $this->getVal('metatitle_txt'),
			'metaDescription' => $this->getVal('metadesc_txt')
		);

		if($fields['displaydate'] == '')
		{
			$fields['displaydate'] = date('Y-m-d');
		}

		if($fields['content'] != '') $fields['content'] = $this->tidyWYSIWYGcontent($fields['content']);

		$imgId = $this->uploadFile('img_file');
		if($imgId) $fields['imageId'] = $imgId;

		return $fields;
	}
	/** END DATA HANDLING **/

	# INDEX
	private function createNewsURL($row)
	{
	    return BASE_HREF . 'news/read/' . $row['id'] . '/' . HTML::createCleanURL($row['name']);
	}

	protected function createNewsViewLink(array $row, array $aParams)
	{
		$uri = $this->createNewsURL($row);
		return $this->createViewLink($uri);
	}

	protected function formatDate(array $row, array $aParams)
	{
		$date = date("D jS F Y", strtotime($row['displaydate']));
		return $date;
	}

	public function index()
	{
		$layout = array(
			'_@CLASS:center td_view@__@FUNC:createNewsViewLink@_',
			'_@name@_',
			'_@CLASS:center td_view@__@FUNC:formatDate@_',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/edit/_@id@_" class="icon_edit" title="Edit this item">Edit</a>',
			'_@CLASS:center@__@FUNC:createOnlineToggle@_',
			'_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/delete/_@id@_" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a>'
		);

		$aRows = $this->MODEL->getTblData($this->dbLayout);

		$this->templatevars['pagedata'] = $this->cmsViewTable($this->dbLayout, $aRows, $layout);

	}

	public function editExt()
	{
		$this->templatevars['imgpreview'] = false;
		// FETCH IMAGE AND ASSIGN TO $inputvalue IF ITS FOUND
		if(isset($this->templatevars['inputvalue']['imageId']) && ($this->templatevars['inputvalue']['imageId'] != 0))
		{
			$filename = $this->MODEL->fetchFilenameById($this->templatevars['inputvalue']['imageId']);
			if($filename) $this->templatevars['imgpreview'] = '<img src="' . BASE_HREF . 'uploads/cms_image/' . $filename . '" alt="Preview" />';
		}
	}

	public function editPostQuery($id)
	{
		// INFORM SEO COMPANIES
		if(LIVEMODE && ($this->mvc['VIEW'] == 'create'))
		{
		    $row = $this->MODEL->simpleFetchRow($id, array(0,1));
		    $url = $this->createNewsURL($row);
		    Seo::tellAllSearchEnginesThereIsANewPage($url);
		}
	}

	public function __destruct() {
		parent::__destruct();
	}
}