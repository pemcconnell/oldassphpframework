<?php

/**
  * AdminIndexController
  * Designed to take the hard work out of default CMS 'index' view HTML generation
  * and to keep AdminBaseController as tidy as possible
  *
  * @author Peter McConnell <pemcconnell@googlemail.com>
  */

class AdminIndexController extends AdminBaseController
{
	private $_defaults;

	public function __construct()
	{
		parent::__construct();

		$this->_defaults = array(
			'params' => array(
				'id' => 'id',
				'parent' => 'parent',
				'sortOrder' => 'sortOrder',
				'online' => 'online'
			),
			'indentSymbol' => '<span class="indentchar">&rarr;</span>',
			'indentMultiplier' => 5
		);
	}

	public function createViewLink($url)
	{
		$html = '<a href="' . $url . '" class="btn_view">View</a>';
		return $html;
	}

	public function error($action)
	{
		$this->index();
		$this->console->formerror(ucfirst($action) . ' failed');
	}

	public function success($action)
	{
		$this->index();
		$this->console->formsuccess(ucfirst($action) . ' completed successfully');
	}

	public function delete($action)
	{
		if(is_numeric($action))
		{
			if(isset($this->dbLayout['online']) && ($this->dbLayout['online'] != false))
			{
				// HIDE
				$this->MODEL->onlineUpdate(2, $action, $this->dbLayout);
			} else {
				// DELETE
				$this->MODEL->delete($action, $this->dbLayout);
			}
		}
		$this->index();
	}

	public function online($action)
	{
		if(is_numeric($action))
		{
			$this->MODEL->onlineUpdate(1, $action, $this->dbLayout);
		}
		$this->index();
	}

	public function offline($action)
	{
		if(is_numeric($action))
		{
			$this->MODEL->onlineUpdate(0, $action, $this->dbLayout);
		}
		$this->index();
	}

	public function sortup($action)
	{
		if(is_numeric($action))
		{
			$this->sqlSort('sortup', $action, $this->dbLayout);
		}
		$this->index();
	}

	public function sortdown($action)
	{
		if(is_numeric($action))
		{
			$this->sqlSort('sortdown', $action, $this->dbLayout);
		}
		$this->index();
	}

	protected function sqlSort($dir, $id, array $dbColData)
	{
		if(($dir == 'sortup' || $dir == 'sortdown') && is_numeric($id) && (is_array($dbColData) && !empty($dbColData))) $this->MODEL->sort($dir, $id, $dbColData);
	}

	/**
	  * cmsViewTable()
	  *
	  * Recursive function that takes a FLAT ARRAY with the array keys set to the ID and works out the rest.
	  *
	  */
	public function cmsViewTable(array $dbLayout, array $dbData, array $tableLayout, $parent = false, $params = false)
	{
		// INITS
		if(!is_array($params))
		{
			$params = array(
				'currentRow' => 0,
				'stepinc' => 0,
				'cellData' => false,
				'colData' => $dbLayout
			);

			$aCellData = array();
			foreach($tableLayout as $cell)
			{
				$class = '';
				if(substr($cell, 0, 8) === '_@CLASS:')
				{
					$cell = substr($cell, 8);
					$class = ' class="' . strstr($cell, '@_', true) . '"';
					$cell = substr(strstr($cell, '@_'), 2);
				}
				$aCellData[] = '<td' . $class . '>' . $cell  . "</td>\n";
			}
			$params['cellData'] = $aCellData;
		}
		$params['step'] = 0;
		$html = '';
		if(($parent !== false) && isset($dbLayout['parent']) && ($dbLayout['parent'] != false) && (gettype($dbLayout['parent']) == 'string'))
		{ // TABLE HAS PARENT SUPPORT
			$dbDataByParent = $this->arrangeByParent($dbData, $dbLayout['parent']);
		} else {
			$dbDataByParent = array();
			if(count($dbData)>0) // IF NOT PASSED if(isset($dbDataByParent[$parent])) WILL FAIL AND SHOW "NO ROWS FOUND" METHOD
			{
				$parent = 0;
				$dbDataByParent[$parent] = $dbData;
			}
		}
		if(isset($dbDataByParent[$parent]) && ($dbDataByParent[$parent] != false))
		{
			$params['maxstep'] = count($dbDataByParent[$parent]);
			$params['currentParentRow'] = 0;
			foreach($dbDataByParent[$parent] as $row)
			{
				$params['currentRow']++; $params['currentParentRow']++; $params['step']++;

				$trclass = 'odd';
				if($params['currentRow']%2==0) $trclass = 'even';

				/* START TR AND TD CREATION */
				$html .= '<tr class="' . $trclass . '">';
				foreach($params['cellData'] as $cell) $html .= $this->cmsViewTableParseCell($row, $cell, $params);
				$html .= '</tr>';
				/* END TR AND TD CREATION */

				if(isset($dbDataByParent[$row[$dbLayout['id']]]))
				{
					// ADD SUB LEVEL
					$subParams = $params;
					$subParams['stepinc']++;
					$sub = $this->{__FUNCTION__}($dbLayout, $dbData, $tableLayout, $row[$dbLayout['id']], $subParams);
					$params['currentRow'] = $sub['params']['currentRow'];
					$html .= $sub['html'];
				}
			}
		} else {
			$html = $this->cmsNoRowsFound($tableLayout);
		}
		return array('html' => $html, 'params' => $params);
	}

	public function cmsItemByCategoryViewTable(array $aABcategoryLink, array $dbLayout, array $htmlLayout, $parent = 0, $params = false)
	{
		if(!is_array($params))
		{
			$params = array(
				'breadCrumb' => array(),
				'currentParentRow' => 0,
				'stepinc' => 0,
				'colData' => $dbLayout
			);
		}
		$aCatDBLayout = $aABcategoryLink['cat_layout'];
		if(!isset($aCatDBLayout['id'])) $aCatDBLayout['id'] = $this->_defaults['params']['id'];
		if(!isset($aCatDBLayout['parent'])) $aCatDBLayout['parent'] = $this->_defaults['params']['parent'];
		if(!isset($aCatDBLayout['sortOrder'])) $aCatDBLayout['sortOrder'] = $this->_defaults['params']['sortOrder'];
		if(!isset($aCatDBLayout['online'])) $aCatDBLayout['online'] = $this->_defaults['params']['online'];

		$aCats = $this->MODEL->getTblData($aCatDBLayout);

		$aCatsByParent = $this->arrangeByParent($aCats, $aCatDBLayout['parent']);

		$aReturn = array();
		$html = '';
		if(isset($aCatsByParent[$parent]))
		{
			$params['maxstep'] = count($aCatsByParent[$parent]);
			foreach($aCatsByParent[$parent] as $row)
			{
				$params['currentParentRow']++;
				$aRows = $this->MODEL->getLinkedTblData($aABcategoryLink, $row['id']);
				$aData = $this->cmsViewTable($this->dbLayout, $aRows, $htmlLayout);

				$aTitle = $params['breadCrumb'];
				$aTitle[] = stripslashes($row['name']);
				$subChar = (isset($params['breadCrumb'][0])) ? '&#x21AA; ' : '';

				$aReturn[] = array('title' => $subChar . implode(' &raquo; ', $aTitle), 'tbody' => $aData['html']);

				// IS SUBCATEGORY AVAILABLE?
				if(isset($aCats[$row['id']]))
				{
					$subParams = $params;
					$subParams['stepinc']++;
					$subParams['breadCrumb'][] = $row['name'];
					$aReturn = array_merge($aReturn, $this->{__FUNCTION__}($aABcategoryLink, $dbLayout, $htmlLayout, $row['id'], $subParams));
				}
			}
		}
		return $aReturn;
	}

	public function createOnlineToggle(array $row, array $aParams)
	{
		$mode = 'offline';
		if($row[$aParams['colData']['online']] == 0) $mode = 'online';
		$class = 'offline';
		if($mode == 'offline') $class = 'online';
		$html = '<a class="icon_' . $class . '" href="./' . $this->mvc['CONTROLLER'] . '/' . $mode . '/' . $row[$aParams['colData']['id']] . '" onclick="return cms.ajaxThis(\'formstateonly\', this, true, \'onlineToggle\');">Toggle ' . $mode . '</a>';
		return $html;
	}

	public function createSorts(array $row, array $aParams)
	{
		if(!isset($aParams['maxstep'])) $aParams['maxstep'] = 9999;
		if($aParams['step'] > 1)
		{
			$html  = '<a class="btn_arrowup" href="./' . $this->mvc['CONTROLLER'] . '/sortup/' . $row[$aParams['colData']['id']] . '" onclick="return cms.ajaxThis(\'bodyonly\', this, true, \'changeSortOrder\');">Sort Up</a>';
		} else {
			$html = '<div class="btn_arrowup_disabled">Sort up</div>';
		}
		if($aParams['step'] < $aParams['maxstep'])
		{
			$html .= '<a class="btn_arrowdown" href="./' . $this->mvc['CONTROLLER'] . '/sortdown/' . $row[$aParams['colData']['id']] . '" onclick="return cms.ajaxThis(\'bodyonly\', this, true, \'changeSortOrder\');">Sort Down</a>';
		} else {
			$html .= '<div class="btn_arrowdown_disabled">Sort Down</div>';
		}
		return $html;
	}

	protected function cmsViewTableParseCell($row, $cell, $aParams)
	{
		if(strpos($cell, '_@')!==false)
		{
			$reg = '/(_@([a-z]+[^@]+)@_)/';
			preg_match_all($reg, $cell, $matches);
			foreach($matches[2] as $key => $col)
			{
				if(isset($row[$col])) $cell = str_replace($matches[1][$key], stripslashes($row[$col]), $cell);
			}
		}
		if(strpos($cell, '_@FUNC:')!==false)
		{
			$reg = '/(_@FUNC:([^@]+)@_)/';
			preg_match_all($reg, $cell, $matches);
			foreach($matches[2] as $key => $col)
			{
				$cell = str_replace($matches[1][$key], $this->$col($row, $aParams), $cell);
			}
		}
		return $cell;
	}

	public function cmsNoRowsFound($layout)
	{
		return '<tr class="noitemsfound"><td colspan="' . count($layout) . '"><em>It appears there are no items in this section yet...</em></td></tr>';
	}

	public function cmsViewTableIndentByStep(array $row, array $aParams)
	{
		$html = '';
		if(!isset($aParams['stepinc']) || ($aParams['stepinc'] == 0)) return $html;
		$indentSymbol = (isset($aParams['indentSymbol'])) ? $aParams['indentSymbol'] : $this->_defaults['indentSymbol'];
		$indentMultiplier = (isset($aParams['indentMultiplier'])) ? $aParams['indentMultiplier'] : $this->_defaults['indentMultiplier'];
		$to = ($aParams['stepinc'] * $indentMultiplier);
		for($i = 0; $i < $to; $i++) $html .= '&nbsp;';
		$html .= $indentSymbol;
		return $html;
	}

	public function __destruct()
	{
		parent::__destruct();
	}
}