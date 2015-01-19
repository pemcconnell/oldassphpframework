<?php

class GalleriesController extends AdminIndexController {

    private         $tmp_cPageData,
                          $iCurrentInputGeneration = 0,
                          $dbPagesLayout,
                          $aThumbnailDimensions = array(),
                          $aFieldTypeAssignments = array();

    public function __construct() {
        parent::__construct();
        $this->templatevars['parent'] = $this->dbLayout['parent'];
        $this->dbPagesLayout = array(
            'tbl' => 'pages',
            'id' => 'id',
            'name' => 'name',
            'parent' => 'parent',
            'sortOrder' => 'sortOrder',
            'online' => 'online'
        );

        $this->aFieldTypeAssignments = array(
            'id' => 'hidden',
            'fileId' => 'hidden',
            'parent' => false,
            'sortOrder' => false,
            'online' => 'checkbox',
            'edit_icons' => false,
            'filename' => false,
            'height' => false,
            'width' => false,
            'off_x' => false,
            'off_y' => false
        );

        // SET THUMBNAIL DIMENSIONS
        $this->aThumbnailDimensions['height'] = 200;
        $this->aThumbnailDimensions['width'] = 200;
    }

    /**
     * manageSessionData
     * 
     * Takes the data from the form and merges it with the session data which is used for display and saving.
     * 
     * @todo make these input fields dynamic
     * @param array $row _POST and _FILES data sent from form on edit page 
     */
    private function manageSessionData(array $row) {
        $a = &$this->session->get('admin', 'tmp_gallery');
        $a[] = array(
            'id' => '',
            'caption' => $row['name_txt'],
            'url' => $row['url_txt'],
            'fileId' => $row['parent'],
            'parent' => '1',
            'sortOrder' => '1',
            'online' => '1',
            'filename' => '',
            'imginfo' => array
            (
            ),
            'height' => '200',
            'width' => '600',
            'off_x' => '-200',
            'off_y' => '0'
        );
    }

    public function setValidation() {
        $fields = array(
        );
        return $fields;
    }

    public function inputDefaults() {
        $fields = array(
            'online' => 1
        );
        return $fields;
    }

    protected function getAndSetFields() {
        $fields = array(
            'name' => $this->getVal('name_txt'),
            'pageId' => (int) $this->getVal('page_sel'),
            'parent' => (int) $this->getVal('parent_sel'),
            'online' => isset($_POST['online_chk']) ? 1 : 0
        );
        return $fields;
    }

    public function index() {
        $layout = array(
            '_@CLASS:center td_view@__@FUNC:createPageViewLink@_',
            '_@FUNC:createPageName@_',
            '_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/edit/_@id@_" class="icon_edit" title="Edit this item">Edit</a>',
            '_@CLASS:center@_<div class="sortOrderVisible">_@FUNC:createSorts@_</div>',
            '_@CLASS:center@__@FUNC:createOnlineToggle@_',
            '_@CLASS:center@_<a href="./' . $this->mvc['CONTROLLER'] . '/delete/_@id@_" onclick="return cms.deleteItem(this);" class="icon_delete" title="Delete this item">Delete</a>'
        );
        $aRows = $this->MODEL->getTblData($this->dbLayout);
        $this->templatevars['pagedata'] = $this->cmsViewTable($this->dbLayout, $aRows, $layout);
    }

    public function editExt($action) {
        $this->templatevars['GBL_inlinefooterscripts'][] = 'cms.gallery.init();';
        $action = (int) $action;
        $this->templatevars['parent'] = array();

        // COLLECT PAGES DATA
        $aRows = $this->MODEL->getTblData($this->dbPagesLayout);
        $aWrapFunctionParams = array(
            'loopingFunction' => 'wrapOptions',
            'loopingFunctionInlineWrapper' => 'optionPageName',
            'selectedIndex' => $this->templatevars['inputvalue']['pageId'],
            'dbLayout' => $this->dbPagesLayout,
            'dbData' => $aRows
        );
        for ($i = 0; $i > -3; $i--)
            $this->templatevars['inputvalue']['pages'][$i] = $this->recurringWrapIndexesWithFunction($aWrapFunctionParams, $i);
        
        // COLLECT PARENT DATA

        // COLLECT IMAGES
        $aData = $this->MODEL->fetchGalleryImgs($action);
        $this->templatevars['imgs'] = $aData;
        foreach ($this->templatevars['imgs'] as $k => $row) {
            $this->templatevars['imgs'][$k] = $this->organiseThumbRow($row);
        }

        // INIT SESSION
        $this->session->set('admin', 'tmp_gallery', $this->templatevars['imgs']);
    }

    private function organiseThumbRow($row) {
        $row['edit_icons'] = $this->generateThumbnailIcons($row);
        $row['edit_inputs'] = $this->generateEditThumbnailInputs($row);
        $row['imginfo'] = getimagesize(BASE_PATH . 'tmp' . DS . 'uploads' . DS . 'cms_image' . DS . $row['filename']);
        $row = array_merge($row, HTML::getImgProportions($row['imginfo'][1], $row['imginfo'][0], $this->aThumbnailDimensions['height'], $this->aThumbnailDimensions['width']));
        return $row;
    }

    public function refresh($action) {
        // TRANSLATE _SESSION TO $imgs
        $this->templatevars['imgs'] = array();
        $ses = $this->session->get('admin', 'tmp_gallery');
        foreach ($ses as $k => $row) {
            unset($row['edit_icons'], $row['edit_inputs'], $row['imginfo']);
            $this->templatevars['imgs'][$k] = $this->organiseThumbRow($row);
        }
        $this->mvc['TEMPLATE']['BODY'] = BASE_PATH . 'mvc' . DS . 'admin' . DS . 'ajax' . DS . 'gallerywhiteboard.php';
    }

    public function submitimg() {
        $aFields = array();
        foreach ($_FILES as $k => $v) {
            if ($v['error'] != 4) {
                $aFields[$k] = UPLOAD::uploadFile($k);
            }
        }
        foreach ($_POST as $k => $v) {
            $aFields[$k] = $v;
        }
        $this->manageSessionData($aFields);

        // CALL PARENT JS
        $this->generateParentWindowJSfunc('cms.gallery.refreshThumbs();');

        // KILL SCRIPT
        die();
    }

    /**
     * todo: add file saving
     */
    public function quickedit() {
        if (isset($_POST['id']) && $this->session->is_set('admin', 'tmp_gallery', $_POST['id'])) {
            $ses = &$this->session->get('admin', 'tmp_gallery', $_POST['id']);
            foreach ($_POST as $k => $v) {
                if (strpos($k, 'thumbinput_') !== false) {
                    $k = str_replace('thumbinput_', '', $k);
                    $ses[$k] = $v;
                }
            }
        }

        // CALL PARENT JS
        $this->generateParentWindowJSfunc('cms.gallery.refreshThumbs();');

        // KILL SCRIPT
        die();
    }

    public function navnext($id) {
        if ($this->session->is_set('admin', 'tmp_gallery', $id) && $this->session->is_set('admin', 'tmp_gallery', ($id + 1))) {
            $a = $this->session->get('admin', 'tmp_gallery', $id);
            $this->session->set('admin', 'tmp_gallery', $id, $this->session->get('admin', 'tmp_gallery', ($id + 1))
            );
            $this->session->set('admin', 'tmp_gallery', ($id + 1), $a
            );
        }
        // CALL PARENT JS
        $this->generateParentWindowJSfunc('cms.gallery.refreshThumbs();');

        // KILL SCRIPT
        die();
    }

    public function imgonline($id) {
        if ($this->session->is_set('admin', 'tmp_gallery', $id))
            $this->session->set('admin', 'tmp_gallery', $id, 'online', 1);
        
         // CALL PARENT JS
        $this->generateParentWindowJSfunc('cms.gallery.refreshThumbs();');

        // KILL SCRIPT
        die();
    }

    public function imgoffline($id) {
        if ($this->session->is_set('admin', 'tmp_gallery', $id))
            $this->session->set('admin', 'tmp_gallery', $id, 'online', 0);
        
         // CALL PARENT JS
        $this->generateParentWindowJSfunc('cms.gallery.refreshThumbs();');

        // KILL SCRIPT
        die();
    }

    public function navprev($id) {
        if ($this->session->is_set('admin', 'tmp_gallery', $id) && $this->session->is_set('admin', 'tmp_gallery', ($id - 1))) {
            $a = $this->session->get('admin', 'tmp_gallery', $id);
            $this->session->set('admin', 'tmp_gallery', $id, $this->session->get('admin', 'tmp_gallery', ($id - 1))
            );
            $this->session->set('admin', 'tmp_gallery', ($id - 1), $a
            );
        }
        // CALL PARENT JS
        $this->generateParentWindowJSfunc('cms.gallery.refreshThumbs();');

        // KILL SCRIPT
        die();
    }

    private function generateParentWindowJSfunc($sJS) {
        echo '<script type="text/javascript">window.parent.' . $sJS . '</script>';
    }

    public function optionPageName(array $aFunctionParams, array $row) {
        $spacer = '';
        if ($aFunctionParams['params']['stepinc'] > 0) {
            $multiplier = ($aFunctionParams['params']['stepinc'] * 5);
            for ($i = 0; $i < $multiplier; $i++)
                $spacer .= '&nbsp;';
        }
        return $spacer . '&raquo; ' . stripslashes($row['menuName']);
    }

    protected function createPageName(array $row, array $aParams) {
        return $this->tmp_cPageData['menuName'];
    }

    protected function createPageViewLink(array $row, array $aParams) {
        $prow = $this->MODEL->getTblData($this->dbPagesLayout, false, $row['pageId']);
        if (!isset($prow[$row['pageId']]))
            return '';
        $this->tmp_cPageData = $row = $prow[$row['pageId']];
        if ($row['type'] != -1)
            $uri = $this->createPageLink($row, $aParams);
        else
            $uri = stripslashes($row['target']);
        return $this->createViewLink($uri);
    }

    private function generateThumbnailIcons($row) {
        $this->iCurrentInputGeneration++;
        $html = '<a href="#" title="Move Up" class="thumbicon prev" onclick="return cms.gallery.navPrev(' . $this->iCurrentInputGeneration . ', this);">Prev</a>';
        $html .= '<a href="#" title="Move Down" class="thumbicon next" onclick="return cms.gallery.navNext(' . $this->iCurrentInputGeneration . ', this);">Next</a>';
        $html .= '<a href="#" title="Edit" class="thumbicon edit" onclick="return cms.gallery.quickedit(' . $this->iCurrentInputGeneration . ', this);">Edit</a>';
        if($row['online'] == 1) $html .= '<a href="#" title="This item is currently Online" class="thumbicon online" onclick="return cms.gallery.imgonline(' . $this->iCurrentInputGeneration . ', this);">Online</a>';
        else $html .= '<a href="#" title="This item is currently Online" class="thumbicon offline" onclick="return cms.gallery.imgoffline(' . $this->iCurrentInputGeneration . ', this);">Offline</a>';
        return $html;
    }

    private function generateEditThumbnailInputs($row) {
        $aHFields = array();
        $html = '<form method="post" target="ifrm" action="' . BASE_HREF . 'admin/galleries/quickedit?bodyonly" enctype="multipart/form-data">';
        $html .= '<input type="hidden" name="id" value="' . $row['id'] . '" />';
        $html .= '<ul>';
        $html .= '<li class="title"><a href="#" onclick="return cms.gallery.closeQuickedit(this);">x</a>Quick Edit</li>';
        $html .= '<li><label for="' . $this->iCurrentInputGeneration . '_thumbinput_imgfile">Image</label><span class="file-wrapper"><input type="file" name="thumbinput_imgfile" id="' . $this->iCurrentInputGeneration . '_thumbinput_imgfile" /><span class="button">Choose File</span></span><span class="selectedfile"></span></li>';
        foreach ($row as $k => $v) {
            $name = 'thumbinput_' . $k;
            if (isset($this->aFieldTypeAssignments[$k]) && ($this->aFieldTypeAssignments[$k] == false))
                continue;
            $type = 'text';
            if (isset($this->aFieldTypeAssignments[$k]))
                $type = $this->aFieldTypeAssignments[$k];
            $e = '';
            if ($type == 'checkbox') {
                if ($v == 1)
                    $e = ' checked="checked"';
            }
            $input = '<input type="' . $type . '" name="' . $name . '"  id="' . $this->iCurrentInputGeneration . '_' . $name . '" value="' . $v . '"' . $e . ' />';
            if ($type != 'hidden') {
                $html .= '<li>';
                $html .= '<label class="lbl_' . $type . '" for="' . $this->iCurrentInputGeneration . '_' . $name . '">' . ucfirst($k) . '</label>';
                $html .= $input;
                $html .= '</li>';
            } else {
                $aHFields[] = $input;
            }
        }
        $html .= '<li><input type="submit" name="sub_btn" class="savechanges_btn" value="Save Changes" /></li>';
        $html .= '</ul>';
        $html .= '</form>';
        return implode('', $aHFields) . $html;
    }

    public function controllerEnd() {
        $this->templatevars['iCurrentInputGeneration'] = $this->iCurrentInputGeneration;
    }

    public function __destruct() {
        parent::__destruct();
    }

}