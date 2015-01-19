<?php

class JobsController extends FrontendBaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->templatevars['jobs'] = array();

        $aData = $this->MODEL->fetchJobs();
        foreach ($aData as $k => $v) {
            $v['summary'] = HTML::createSummary($v['content'], 36);
            $v['url'] = $this->mvc['PRE-ROUTER']['CONTROLLER'] . '/view/' . $v['id'] . '/' . HTML::createCleanURL($v['name']);
            $this->templatevars['jobs'][$k] = $v;
        }
    }

    public function view($action) {
        $this->templatevars['row'] = $this->MODEL->fetchJobs($action);
        if (!isset($this->templatevars['row'][0])) {
            header('Location:' . BASE_HREF . 'careers');
            exit;
        }
        $this->templatevars['row'] = $this->templatevars['row'][0];
        // UPDATE BREADCRUMB
        $this->aAdditionalBreadcrumbLinks[] = $this->templatevars['row']['name'];
    }

    public function __destruct() {
        parent::__destruct();
    }

}