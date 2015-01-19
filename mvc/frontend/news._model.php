<?php

class NewsModel extends FrontendBaseModel
{
    public function __construct() {
	parent::__construct();
    }

    public function getNewsArticles($limit, $id = 0, $archive = false)
    {
	$sql = "SELECT
		    SQL_CALC_FOUND_ROWS
		    a.id, a.name, a.content, a.displaydate, a.metaTitle, a.metaDescription, a.archive,
		    b.name AS image
		FROM
		    news a
		    LEFT JOIN files b ON a.imageId = b.id
		WHERE
		    a.online = 1";
	if($id > 0)
	{
	    $sql .= " AND a.id = " . (int)$id .
		    " LIMIT 1";
	} elseif(is_numeric($archive)) {
	    $sql .= " AND a.archive = " . (int)$archive .
		    " ORDER BY a.displaydate DESC" .
		    " LIMIT " . $limit;
	} else {
	    $sql .= " ORDER BY a.displaydate DESC" .
		    " LIMIT " . $limit;
	}
	$sql = $this->query($sql);
	$aRet = array();
	while($row = $sql->fetch())
	{
	    $aRet[$row['id']] = $row;
	}
	return $aRet;
    }

    public function __destruct() {
	parent::__destruct();
    }
}