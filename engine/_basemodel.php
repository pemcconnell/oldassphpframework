<?php

require BASE_PATH . 'engine' . DS . 'db.php';

class BaseModel extends DB {

    protected  $console,
                        $settings,
                        $mvc;

    public function __construct() {
        parent::__construct();

        global $CONSOLE, $SETTINGS, $MVC;
        $this->console = & $CONSOLE;
        $this->settings = & $SETTINGS;
        $this->mvc = & $MVC;
    }

    public function dbDataUpdate($id, array $dbLayout, array $dbData) {
        $id = (int) $id;
        if ($id < 1) {
            $this->console->exception('Tried to update an item with an ID LTE 0');
        }
        if (
                isset($dbLayout['sortOrder']) &&
                isset($dbLayout['parent']) &&
                (gettype($dbLayout['sortOrder']) == 'string') &&
                (gettype($dbLayout['parent']) == 'string')
        ) {
            // FIND OUT INFO ABOVE THIS ITEM - DOES ITS CHANGES EFFECT OTHER ITEMS SORTORDER?
            $sql = "SELECT * FROM " . $dbLayout['tbl'] . " WHERE " . $dbLayout['id'] . " = " . $id . " LIMIT 1";
            $sql = $this->query($sql);
            $row = ($sql) ? $sql->fetch() : false;
            if ($row) {
                if (!isset($row[$dbLayout['parent']])) {
                    $this->console->exception('dbDataUpdate parent data not found');
                }
                if ($dbData[$dbLayout['parent']] != $row[$dbLayout['parent']]) {
                    // NEED TO CHANGE OLD PARENT SORT ORDERS TO ACCOMODATE FOR THIS ITEM BEING REMOVED
                    $sql = "UPDATE " . $dbLayout['tbl'] . " SET " . $dbLayout['sortOrder'] . " = (" . $dbLayout['sortOrder'] . "-1) WHERE " . $dbLayout['sortOrder'] . " >= " . $row[$dbLayout['sortOrder']] . " AND " . $dbLayout['parent'] . " = " . $row[$dbLayout['parent']];
                    $this->query($sql);

                    // NOW NEED TO FIGURE OUT NEW SORT ORDER (FOR NEW PARENT LOCATION)
                    $sql = "SELECT MAX(" . $dbLayout['sortOrder'] . ")+1 AS sO FROM " . $dbLayout['tbl'] . " WHERE " . $dbLayout['parent'] . " = " . (int) $dbData[$dbLayout['parent']] . " LIMIT 1";
                    $sql = $this->query($sql);
                    $row = ($sql) ? $sql->fetch() : false;
                    if ($row && isset($row['sO']) && is_numeric($row['sO'])) {
                        $newSortOrder = $row['sO'];
                    } else {
                        $newSortOrder = 1;
                    }
                    $dbData[$dbLayout['sortOrder']] = $newSortOrder; // ADD SORTORDER TO UPDATE STACK
                }
            } else {
                $this->console->exception('could not find existing info about this item');
            }
        }

        $sql = "UPDATE " . $dbLayout['tbl'] . " SET ";
        $aFields = array();
        foreach ($dbData as $k => $v) {
            if ($v !== false) {
                $aFields[] = "$k = :$k";
            } else {
                unset($dbData[$k]);
            }
        }
        $sql .= implode(', ', $aFields);
        $sql .= " WHERE " . $dbLayout['id'] . " = " . $id;

        return $this->query($sql, $dbData);
    }

    public function dbDataInsert(array $dbLayout, array $dbData) {
        $sortOrder = false;
        if (isset($dbLayout['sortOrder']) && (gettype($dbLayout['sortOrder']) == 'string')) {
            // ASSIGN NEXT AVAILABLE SORT ORDER
            $sql = "SELECT MAX(" . $dbLayout['sortOrder'] . ")+1 AS sO FROM " . $dbLayout['tbl'];
            if (isset($dbLayout['parent']) && (gettype($dbLayout['parent']) == 'string')) {
                if (isset($dbData[$dbLayout['parent']])) {
                    $sql .= " WHERE " . $dbLayout['parent'] . " = " . (int) $dbData[$dbLayout['parent']];
                } else {
                    $this->console->warning('INSERT : Parent specified in dbLayout but doesn\'t exist in fields');
                }
            }
            $osql = $this->query($sql, $dbData);
            if ($osql) {
                $row = $osql->fetch();
                $sortOrder = isset($row['sO']) ? $row['sO'] : 1;
            }
        }
        if ($sortOrder !== false) {
            $dbData[$dbLayout['sortOrder']] = $sortOrder;
        }
        $sql = "INSERT INTO " . $dbLayout['tbl'] . " (";
        $aFields = array();
        $aValues = array();
        foreach ($dbData as $k => $v) {
            $aFields[] = $k;
            $aValues[] = ':' . $k;
        }
        $sql .= implode(', ', $aFields);
        $sql .= ") VALUES (";
        $sql .= implode(', ', $aValues);
        $sql .= ")";

        return $this->query($sql, $dbData);
    }

    public function simpleFetchRow($id, $online = 1, $tbl = false, $idname = 'id', $onlinename = 'online') {
        if (!$tbl) {
            $tbl = $this->mvc['CONTROLLER'];
        }
        $online = '';
        if ($online !== false) {
            $online = " AND " . $onlinename . (is_array($online) ? " IN (" . implode(',', $online) . ")" : " = " . (int) $online);
        }
        $sql = "SELECT * FROM " . $tbl . " WHERE " . $idname . " = " . (int) $id . " LIMIT 1";
        $sql = $this->query($sql);
        return $sql->fetch();
    }

    public function getTblData($dbLayout = false, $parent = 0, $id = 0, $online = false, $idname = 'id', $parentname = 'parent') {
        $defaultDbLayout = array(
            'tbl' => $this->mvc['CONTROLLER'],
            'id' => 'id',
            'name' => 'name',
            'parent' => false,
            'sortOrder' => false,
            'online' => false
        );
        if (!$dbLayout) {
            $dbLayout = $defaultDbLayout;
        } else {
            foreach ($defaultDbLayout as $k => $v) {
                if (!isset($dbLayout[$k])) {
                    $dbLayout[$k] = $v;
                }
            }
        }
        $sql = "SELECT * FROM " . $dbLayout['tbl'];
        $aWheres = array();
        if ($dbLayout['online'] && ($online !== 0)) {
            $sOnline = false;
            if ($online === false) {
                $sOnline = ' IN (0,1)'; // DEFAULT
            } elseif (is_array($online) && !empty($online)) {
                $sOnline = ' IN ' . implode(',', $online);
            } elseif (is_numeric($online)) {
                $sOnline = ' = ' . $online;
            }
            if ($sOnline !== false) {
                $aWheres[] = $dbLayout['online'] . $sOnline;
            }
        }
        if (($parent > 0) && (gettype($parentname) == 'string')) {
            $aWheres[] = $parentname . ' = ' . $parent;
        }
        if (count($aWheres) != 0) {
            $sql .= " WHERE " . implode(' AND ', $aWheres);
        }
        if (!isset($dbLayout['sortOrder_custom'])) {
            if ($dbLayout['sortOrder'] && ($id < 1)) {
                $sql .= " ORDER BY " . $dbLayout['sortOrder'];
            }
            if (isset($dbLayout['sortOrder_direction'])) {
                $sql .= " " . $dbLayout['sortOrder_direction'];
            } elseif ($id > 0) {
                if (count($aWheres) == 0) {
                    $sql .= " WHERE ";
                } else {
                    $sql .= " AND ";
                }
                $sql .= $dbLayout[$idname] . " = " . (int) $id . " LIMIT 1";
            }
        } else {
            if ($id > 0) {
                if (count($aWheres) == 0) {
                    $sql .= " WHERE ";
                } else {
                    $sql .= " AND ";
                }
                $sql .= $dbLayout[$idname] . " = " . (int) $id . " LIMIT 1";
            } else {
                $sql .= " ORDER BY " . $dbLayout['sortOrder_custom'];
            }
        }
        $sql = $this->query($sql);
        if ($sql) {
            $aData = array();
            while ($row = $sql->fetch()) {
                $aData[$row[$dbLayout[$idname]]] = $row;
            }
            return $aData;
        }
        return false;
    }

    public function getLinkedTblData(array $aABcategoryLink, $iParent) {
        $sql = "SELECT
                    item." . $aABcategoryLink['item_layout']['id'] . ", item." . $aABcategoryLink['item_layout']['name'] . ", item." . $aABcategoryLink['item_layout']['online'] . "
                FROM
                    " . $aABcategoryLink['item_layout']['tbl'] . " item
                INNER JOIN
                    " . $aABcategoryLink['link_layout']['tbl'] . " link
                    ON
                    item." . $aABcategoryLink['item_layout']['id'] . " = link." . $aABcategoryLink['link_layout']['itemId'] . "
                WHERE
                    link." . $aABcategoryLink['link_layout']['catId'] . " = " . (int) $iParent;
        if (
                isset($aABcategoryLink['link_layout']['sortOrder']) &&
                gettype($aABcategoryLink['link_layout']['sortOrder']) == 'string'
        ) {
            $sql .= " ORDER BY link." . $aABcategoryLink['link_layout']['sortOrder'];
        }
        $sql = $this->query($sql);
        if ($sql) {
            $aData = array();
            while ($row = $sql->fetch()) {
                $aData[$row[$aABcategoryLink['item_layout']['id']]] = $row;
            }
            return $aData;
        }
        return false;
    }

    public function fetchPage($id = 0, $online = 1, $sController = '') {
        if ($id != 0) {
            $aParams = array('id' => (int) $id);
            $sql = "SELECT * FROM pages WHERE id = :id";
        } else {
            $aParams = array('target' => './' . $sController);
            $sql = "SELECT * FROM pages WHERE target = :target";
        }
        if ($online !== false) {
            $sql .= " AND online " . (is_array($online) ? " IN (" . implode(',', $online) . ")" : " = " . (int) $online);
        }
        $sql .= " LIMIT 1";
        $sql = $this->query($sql, $aParams);
        if ($sql) {
            $row = $sql->fetch();
            if (isset($row['id']))
                return $row;
        }
        $this->console->warning('Tried to fetch unavailable page id ' . $id . ' i/o state ' . (is_array($online) ? implode('|', $online) : (($online === false) ? 'false' : $online)));
        return false;
    }

    public function insertFileRecord($newfilename) {
        $params = array('name' => $newfilename);
        $sql = "INSERT INTO files (name) VALUES (:name)";
        $this->query($sql, $params);
        return $this->lastId();
    }

    public function fetchFilenameById($id) {
        $params = array('id' => (int) $id);
        $qsql = "SELECT * FROM files WHERE id = :id";
        $sql = $this->query($qsql, $params);
        if ($sql) {
            $row = $sql->fetch();
            return $row['name'];
        }
        return false;
    }

    public function __destruct() {
        parent::__destruct();
    }

}