<?php

class FrontendBaseController extends BaseController {

    public $aPageData,
            $aAdditionalBreadcrumbLinks = array(),
            $aInvolvedPageIds = array(),
            $iCurrentPageId = 1,
            $iDbPageId = 0, // IF CURRENT PAGE IS AN INTERNAL LINKER
            $iTopParentId = 0,
            $additionalRelated = false,
            $aDbPages = array();

    public function __construct() {
        parent::__construct();

        // START SESSION
        $this->session->start();

        $this->initGbls();
        $this->initPageData();

        // CHECK 404
        if (!$this->aPageData) {
            if ($this->isControllerFourOhFour()) {
                require (BASE_PATH . 'plugins' . DS . 'httperror.c.php');
                HttpError::virtualiseFrontendFourOhFour();
            }
        }
    }

    public function controllerEnd() {
        $this->templatevars['breadcrumb'] = '<ul class="breadcrumb"><li>' . implode(' &raquo; </li><li>', $this->createBreadcrumb()) . '</li></ul>';
        $this->templatevars['GBL_formreport'] = $this->generateFormReport();
        $this->templatevars['additionalRelated'] = $this->additionalRelated;
    }

    private function fetchMenus() {
        if (!($pagemenus = APC::fetch(__FUNCTION__ . '_' . $this->mvc['APCPAGEKEY']))) {
            // PAGES
            $dbData = array(
                'tbl' => 'pages',
                'id' => 'id',
                'parent' => 'parent',
                'online' => 'online',
                'sortOrder' => 'sortOrder'
            );
            $aFunctionParams = array(
                'dbLayout' => $dbData,
                'dbData' => $this->aDbPages,
                'selectedIndex' => $this->iCurrentPageId,
                'activeParents' => $this->aInvolvedPageIds,
                'loopingFunction' => 'wrapLIs',
                'loopingFunctionInlineWrapper' => 'createPageLinkWithAnchor',
                'hidesubs' => false
            );

            $pagemenus = array();
            $pagemenus[0] = $this->recurringWrapIndexesWithFunction($aFunctionParams, 0);
            $pagemenus[-1] = $this->recurringWrapIndexesWithFunction($aFunctionParams, -1);
            APC::store(__FUNCTION__ . '_' . $this->mvc['APCPAGEKEY'], $pagemenus);
        }
        $this->templatevars['pagemenus'] = $pagemenus;
    }

    private function initPageData() {
        if (!($apd = APC::fetch('initPageData_' . $this->mvc['APCPAGEKEY']))) {

            $controllerName = $this->mvc['CONTROLLER'];
            if ($controllerName != $this->settings['mvc']['defaults']['pagetarget']) {
                if ($controllerName == $this->settings['mvc']['defaults']['homecontroller'])
                    $controllerName = '';
                $apd = $this->MODEL->fetchPage(0, 1, $controllerName);
            } else {
                $apd = $this->MODEL->fetchPage((int) $this->mvc['VIEW']);
            }
            APC::store('initPageData_' . $this->mvc['APCPAGEKEY'], $apd);
        }
        $this->aPageData = $apd;
        if ($apd) {
            $this->iCurrentPageId = $this->aPageData['id'];
            if (isset($_GET['_tp']) && ((int) $_GET['_tp'] > 0))
                $this->iDbPageId = (int) $_GET['_tp'];
            else
                $this->iDbPageId = $this->aPageData['id'];
            if (!isset($this->templatevars['metaTitle']) || ($this->templatevars['metaTitle'] == ''))
                $this->templatevars['metaTitle'] = $this->aPageData['metaTitle'];
            if (!isset($this->templatevars['metaDescription']) || ($this->templatevars['metaDescription'] == ''))
                $this->templatevars['metaDescription'] = $this->aPageData['metaDescription'];

            $this->templatevars['showName'] = $this->aPageData['showName'];
            $this->templatevars['pageName'] = $this->aPageData['name'];
            if ($this->aPageData['content'] != '')
                $this->templatevars['content'] = '<div id="WYSIWYG_content">' . $this->aPageData['content'] . '</div>';
            else
                $this->templatevars['content'] = '';
        }
        $this->aDbPages = $this->MODEL->getPageData(array(), 0, 0, 1);
        $this->fetchBreadcrumbPageIds();
        $this->fetchMenus();
        $this->fetchRelatedItems();
    }

    private function fetchBreadcrumbPageIds() {
        $this->aInvolvedPageIds[] = $this->iDbPageId;
        if ($this->iDbPageId != 1 && (int) $this->iDbPageId > 0) {
            $pId = $this->aDbPages[$this->iDbPageId]['parent'];
            if ($pId > 0) {
                // THIS ITEM HAS A PAGE AS ITS PARENT
                $p = $this->arrangeByParent($this->aDbPages);
                $looplimit = 20;
                $i = 0;
                while (isset($p[$pId])) {
                    if ($i > $looplimit) {
                        $this->console->error('Loop limit exceeded attempting to get breadcrumb ids');
                        break;
                    }
                    if (!isset($this->aDbPages[$pId]['id']))
                        break;
                    $this->iTopParentId = $this->aDbPages[$pId]['parent'];
                    $this->aInvolvedPageIds[] = $this->aDbPages[$pId]['id'];
                    $pId = $this->aDbPages[$pId]['parent'];
                    $i++;
                }
            }
        }
        #debugA($this->aInvolvedPageIds);
    }

    private function createBreadcrumb() {
        $aParams = array();
        $aPages = $this->aAdditionalBreadcrumbLinks;
        $i = count($aPages);
        foreach ($this->aInvolvedPageIds as $k => $id) {
            if (!isset($this->aDbPages[$id]))
                continue;
            if (($i + $k) !== 0) {
                $aPages[] = $this->createPageLinkWithAnchor($aParams, $this->aDbPages[$id]);
            } else {
                $aPages[] = $this->aDbPages[$id]['menuName'];
            }
        }
        $aPages[] = '<a href="' . BASE_HREF . '">Home</a>';
        return array_reverse($aPages);
    }

    private function fetchRelatedItems() {
        if (!($related = APC::fetch(__FUNCTION__ . '_' . $this->mvc['APCPAGEKEY']))) {
            $this->templatevars['related'] = false;

            $dbData = array(
                'tbl' => 'pages',
                'id' => 'id',
                'parent' => 'parent',
                'online' => 'online',
                'sortOrder' => 'sortOrder'
            );
            $aRows = $this->MODEL->getPageData($dbData, 0, 0, 1);
            $aFunctionParams = array(
                'dbLayout' => $dbData,
                'dbData' => $aRows,
                'selectedIndex' => $this->iCurrentPageId,
                'loopingFunction' => 'wrapLIs',
                'loopingFunctionInlineWrapper' => 'createPageLinkWithAnchor',
                'hidesubs' => false
            );
            $parent = end($this->aInvolvedPageIds);
            if ($parent === false)
                $parent = $this->iCurrentPageId;
            $row = $this->recurringWrapIndexesWithFunction($aFunctionParams, $parent);
            $related = $row['html'];
            APC::store(__FUNCTION__ . '_' . $this->mvc['APCPAGEKEY'], $related);
        }
        $this->templatevars['related'] = $related;
    }

    private function initGbls() {
        $this->templatevars['showName'] = 0;
        if (!isset($this->templatevars['metaTitle']) || ($this->templatevars['metaTitle'] == ''))
            $this->templatevars['metaTitle'] = '';
        if (!isset($this->templatevars['metaDescription']) || ($this->templatevars['metaDescription'] == ''))
            $this->templatevars['metaDescription'] = '';
        if (!isset($this->templatevars['pageName']) || ($this->templatevars['pageName'] == ''))
            $this->templatevars['pageName'] = '';
        $this->templatevars['content'] = '';
        $this->templatevars['GBL_controllerName'] = $this->mvc['CONTROLLER'];
        $this->templatevars['GBL_stylesheets'][] = BASE_HREF . 'css/screen.css';
        $this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'scripts/jquery.js';
        $this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'scripts/base.js';
        $this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'scripts/site.js';
    }

    public function __destruct() {
        parent::__destruct();
    }

}
