<?php

class FrontendBaseModel extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getPageData() {
        $aRet = array();

        // PAGE DATA
        $sql = "SELECT
                        *
                FROM
                        pages
                WHERE
                        online = 1
                ORDER BY
                        sortOrder ASC";
        $sql = $this->query($sql);
        if ($sql) {
            while ($row = $sql->fetch()) {
                $aRet[$row['id']] = $row;
            }
        }
        return $aRet;
    }

    public function __destruct() {
        parent::__destruct();
    }

}
