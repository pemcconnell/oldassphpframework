<?php

class GalleriesModel extends AdminBaseModel {

	public function __construct() {
		parent::__construct();
	}

	public function fetchGalleryImgs($id) {
		$aRows = array();
		$sql = "SELECT
					a.*,
					b.name AS filename
				FROM
					galleriesimgs a
					INNER JOIN
						files b ON a.fileId = b.id
				WHERE
					a.parent = :id
				ORDER BY
					a.sortOrder";
		$sql = $this->query($sql, array('id' => (int) $id));
		while ($row = $sql->fetch()) {
			$aRows[$row['id']] = $row;
		}
		return $aRows;
	}
	
	public function getGalleryPages()
	{
		$sql = "SELECT
			a.id, a.name, a.pageId, a.parent,
			b.id AS pageId, b.parent AS pageParent, b.name AS pageName, b.targetAS pageTarget
		FROM
			galleries a
		INNER JOIN
			pages b ON a.pageId = b.id
		WHERE
			a.online IN (0,1)
		ORDER BY
			a.sortOrder";
		$sql = $this->query($sql);
		
	}

	public function __destruct() {
		parent::__destruct();
	}

}