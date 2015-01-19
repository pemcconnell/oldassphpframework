<?php

class NewsController extends FrontendBaseController {

    public $iPaginationLimit = 2,
            $iCurrentPage = 1,
            $iTotalPages = 1,
            $iTotalItems = 0,
            $aThumbDimensions;

    public function __construct() {
        parent::__construct();

        // This will create a new version of the image per size change
        $this->aThumbDimensions = array(
            'maxheight' => false,
            'maxwidth' => 200
        );
    }

    public function index() {
        header ('HTTP/1.1 301 Moved Permanently');
        header ('Location:' . BASE_HREF . $this->mvc['PRE-ROUTER']['CONTROLLER'] . '/page/1');
        exit;
    }

    public function page($action = 1) {
        if ((int) $action < 1) {
            $action = 1;
        }
        $this->iCurrentPage = (int) $action;

        $limit = ($this->iPaginationLimit * ($this->iCurrentPage - 1)) . ', ' . $this->iPaginationLimit;

        $this->templatevars['articles'] = $this->organiseNewsRows($this->MODEL->getNewsArticles($limit));
        $this->iTotalItems = $this->MODEL->foundRows();
        $this->iTotalPages = ceil($this->iTotalItems / $this->iPaginationLimit);

        $this->templatevars['pagination'] = $this->createPagination();
    }

    public function read($action) {
        $id = (int) $action;
        $rows = $this->MODEL->getNewsArticles(1, $id);
        if ($rows && (isset($rows[$id]))) {
            $rows = $this->organiseNewsRows($rows);
            $this->templatevars['article'] = $rows[$id];
            $this->templatevars['pageName'] = $this->templatevars['article']['name'];
            $this->templatevars['metaDescription'] = $this->templatevars['article']['metaDescription'];
            $this->templatevars['metaTitle'] = $this->templatevars['article']['metaTitle'];

            // UPDATE BREADCRUMB
            $this->aAdditionalBreadcrumbLinks[] = $this->templatevars['article']['name'];
        } else {
            header('Location:' . BASE_HREF . $this->mvc['PRE-ROUTER']['CONTROLLER']);
            exit;
        }
    }

    private function createPagination() {
        $html = '';
        if ($this->iTotalPages > 1) {
            // PREVIOUS
            $c = '';
            $action = 'href="./news/page/' . ($this->iCurrentPage - 1) . '"';
            if ($this->iCurrentPage < 2) {
                $action = 'href="#" onclick="return false;"';
                $c = ' disabled';
            }
            $html .= '<a ' . $action . ' class="prev' . $c . '">Previous Page</a>';

            // PAGES
            for ($i = 1; $i <= $this->iTotalPages; $i++) {
                $c = '';
                if ($c == $this->iCurrentPage)
                    $c = ' selected';
                $html .= '<a href="./news/page/' . $i . '" class="page' . $c . '">' . $i . '</a>';
            }

            // NEXT
            $c = '';
            $action = 'href="./news/page/' . ($this->iCurrentPage + 1) . '"';
            if ($this->iCurrentPage >= $this->iTotalPages) {
                $action = 'href="#" onclick="return false;"';
                $c = ' disabled';
            }
            $html .= '<a ' . $action . ' class="next' . $c . '">Next Page</a>';
        }
        return $html;
    }

    private function organiseNewsRows(array $rows) {
        foreach ($rows as $k => $row) {
            $row['summary'] = HTML::createSummary($row['content']);
            $row['backlink'] = BASE_HREF . $this->mvc['PRE-ROUTER']['CONTROLLER'];
            $row['url'] = $row['backlink'] . '/read/' . $row['id'] . '/' . HTML::createCleanURL($row['name']);
            if ($row['image'] != '') {
                $row['image'] = HTML::img(BASE_HREF . 'uploads/cms_image/' . $row['image'], $this->aThumbDimensions['maxwidth'], $this->aThumbDimensions['maxheight']);
            }
            else
                $row['image'] = BASE_HREF . 'imgs/default.jpg';
            $rows[$k] = $row;
        }
        return $rows;
    }

    public function __destruct() {
        parent::__destruct();
    }

}