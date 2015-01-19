<?php

class AdminBaseController extends BaseController
{
	public		$aPageData;

	public function __construct($bRequireLogin = true)
	{
		parent::__construct();

        $this->session->start();

		if($bRequireLogin)
		{
			// CHECK LOGIN STATUS
			if(!$this->session->is_set('admin'))
			{
				$bAutoLoginFail = true;
				// ATTEMPT TO REDEM LOGIN USING COOKIE
				if(isset($_COOKIE[BASE_HREF . '-mg']))
				{
					$bAutoLoginFail = !$this->authLoginCookie(false);
				}
				if($bAutoLoginFail)
				{
					header('Location:' . BASE_HREF . 'admin/login');
					exit;
				}
			}
		}

		// CHECK 404
		if($this->isControllerFourOhFour())
		{
			header('Location:' . BASE_HREF . 'admin/404');
			exit;
		}

		$this->initGbls();
		$this->initDbLayout();
	}

	public function controllerEnd()
	{
		$this->templatevars['GBL_formreport'] = $this->generateFormReport();
	}

	/**
	 * initDbLayout()
	 *
	 * Sets the default database layout for the CMS sections. Keys should be made false on individual
	 * contoller pages where necessary. 'tbl' is set to controller name by default so should also be overwritten
	 * when necessary
	 */
	private function initDbLayout()
	{
		$this->dbLayout = array(
			'tbl' => $this->mvc['CONTROLLER'],
			'id' => 'id',
			'name' => 'name',
			'online' => 'online',
			'parent' => 'parent',
			'sortOrder' => 'sortOrder'
		);
	}

	private function initGbls()
	{
		$this->templatevars['metaTitle'] = '';
		$this->templatevars['metaKeywords'] = '';
		$this->templatevars['metaDescription'] = '';
		$this->templatevars['pageName'] = $this->cmsTitle();
		$this->templatevars['addButton'] = $this->cmsAddBtn();
		$this->templatevars['inputvalue'] = array();
		$this->templatevars['GBL_stylesheets'][] = BASE_HREF . 'admin/css/screen.css';
		$this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'scripts/jquery.js';
		$this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'scripts/fckeditor/fckeditor.js';
		$this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'scripts/base.js';
		$this->templatevars['GBL_footerscripts'][] = BASE_HREF . 'admin/scripts/cms.js';
	}

	# LOG IN / OUT METHODS
	protected function authLoginCookie($bRedirectIfSuccessful = true)
	{
		$denc = $_COOKIE[BASE_HREF . '-mg'];
		$denc = base64_decode($denc);
		if(strpos($denc, '_::_')!==false)
		{
			$denc = explode('_::_', $denc);
			if(isset($denc[1]) && ($denc[1] != ''))
			{
				$unAndLen = $denc[1];
				$aUnAndLen = explode('<>', $unAndLen);
				$halfChar = str_replace($this->settings['auth']['salt'], '', base64_decode(strrev($aUnAndLen[1])));
				$len = $aUnAndLen[0];
				$denc = $denc[0];
				$denc = str_replace(array($this->settings['auth']['salt'], $this->settings['auth']['pepper']), '', $denc);
				$denc = strrev($denc);
				$denc = base64_decode($denc);
				if($denc != '')
				{
					$name = strrev(preg_replace('/(.*)?_:_[0-9]+/', '\1', $denc));
					$eHalfChar = str_split($name);
					$eHalfChar = $eHalfChar[ceil($len/2)];
					if((strlen($name) == (int)$len) && ($halfChar == $eHalfChar))
					{
						$id = preg_replace('/.*?_:_([0-9]+)/', '\1', $denc) - 4121;
						$row = $this->MODEL->cmslogin($name, false, $id);
						if($row)
						{
							$this->processCMSLogin($row, $bRedirectIfSuccessful);
							return true;
						}
					}
				}
			}
		}
		setcookie($_COOKIE[BASE_HREF . '-mg'], '', time() - 3600); // KILL COOKIE : not valid
		return false;
	}

	public function processCMSLogin(array $dbRow, $bRedirect = true)
	{
		$this->session->set('admin', array(
				'tStamps' => array(),
				'id' => $dbRow['id'],
				'name' => stripslashes($dbRow['name']),
				'level' => $dbRow['level']
		));
        // FCKEditor Auth Data
		$_SESSION['bp'] = base64_encode(strrev(BASE_PATH));
		$_SESSION['bh'] = base64_encode(strrev(HREF));
		if($bRedirect)
		{
			header('Location:' . BASE_HREF . 'admin/pages');
			exit;
		}
	}

	public function processCMSLogout()
	{
		if($this->session->is_set('admin'))
		{
			$this->session->set('admin', null);
			$_COOKIE[$_COOKIE[BASE_HREF . '-mg']] = false;
			setcookie($_COOKIE[BASE_HREF . '-mg'], '', time() - 3600);
            $this->session->reset('admin');
			unset($_COOKIE[BASE_HREF . '-mg']);
		}
		header('Location:' . BASE_HREF . 'admin/login');
		exit;
	}

	protected function cmsAddBtn($ihtml = false)
	{
		if(!$ihtml) $ihtml = ucfirst(Inflection::singularize($this->mvc['CONTROLLER']));
    	return '<a class="btn_add viewPageAddBtn" href="./' . $this->mvc['CONTROLLER'] . '/create">Add ' . $ihtml . '</a>';
    }

	protected function cmsTitle($sType = 'manage', $ihtml = false)
    {
    	if($this->mvc['VIEW'] == 'edit' || $this->mvc['VIEW'] == 'create') $sType = $this->mvc['VIEW'];
    	$title = '';
		if(!$ihtml)
		{
			if($sType == 'edit' || $sType == 'create') $ihtml = ucfirst(Inflection::singularize($this->mvc['CONTROLLER']));
			else $ihtml = ucfirst($this->mvc['CONTROLLER']);
		} else {
			if($sType == 'edit' || $sType == 'create') $ihtml = ucfirst(Inflection::pluralize($ihtml));
		}
    	if($sType == 'edit')
    	{
    		$title = '<h1><a href="./' . $this->mvc['CONTROLLER'] . '">&laquo; Back to ' . Inflection::pluralize($ihtml) . '</a> | Edit ' . Inflection::singularize($ihtml) . '</h1>';
    	} elseif($sType == 'create') {
    		$title = '<h1><a href="./' . $this->mvc['CONTROLLER'] . '">&laquo; Back to ' . Inflection::pluralize($ihtml) . '</a> | Add ' . Inflection::singularize($ihtml) . '</h1>';
    	} else {
    		$title = '<h1>Manage ' . $ihtml . '</h1>';
    	}
    	return $title;
    }

    public function tidyWYSIWYGcontent($str)
    {
    	require_once (BASE_PATH . DS . 'lib' . DS . 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php');

    	// Make YouTube links opaque to drop z-index problem
    	if(stripos($str, 'youtube.com/embed')!==false)
    	{
    		$str = preg_replace('/youtube\.com\/embed\/([^?|"]+)"/', 'youtube.com/embed/\1?wmode=opaque"', $str);
    	}

    	// HTML purifier
    	$bIframe = false;
		if(stripos($str, '<iframe')!==false)
    	{
    		$bIframe = true;
    		$str = preg_replace('#<iframe#i', '<img alt="CustTempIframe"', $str);
        	$str = preg_replace('#</iframe>#i', '</img>', $str);
    	}
    	$config = HTMLPurifier_Config::createDefault();
    	$config->set('URI.Base', BASE_HREF);
		$config->set('URI.MakeAbsolute', true);
		$config->set('HTML.ForbiddenAttributes', '*@action,*@background,*@codebase,*@dynsrc,*@lowsrc,*@class,*@on*');
		#$config->set('Core.Encoding', 'ISO-8859-1');
		$config->set('Core.AggressivelyFixLt', true);
		$config->set('AutoFormat.AutoParagraph', true);
		$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
		$config->set('AutoFormat.RemoveEmpty', true);
    	$config->set('HTML.Doctype', 'XHTML 1.0 Strict');
    	$config->set('HTML.TidyLevel', 'heavy');
		$purifier = new HTMLPurifier($config);
		$str = $purifier->purify($str);
		if($bIframe)
    	{
    		$post_regex = '#<img alt="CustTempIframe"([^>]+?)>#';
        	$str = preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $str);
    	}
    	return $str;
    }

	protected function postFilterCallback($matches)
    {
        // Domain Whitelist for Iframes
        $youTubeMatch = preg_match('#src="https?://www.youtube(-nocookie)?.com/#i', $matches[1]);
        $vimeoMatch = preg_match('#src="http://player.vimeo.com/#i', $matches[1]);
        if ($youTubeMatch || $vimeoMatch) {
            $extra = ' frameborder="0"';
            if ($youTubeMatch) {
                $extra .= ' allowfullscreen';
            } elseif ($vimeoMatch) {
                $extra .= ' webkitAllowFullScreen mozallowfullscreen allowFullScreen';
            }
            return '<iframe ' . $matches[1] . $extra . '></iframe>';
        } else {
            return '';
        }
    }

    public function edit($action)
    {
    	$fields = NULL;
    	$id = (int)$action;
    	if($id < 1) return false;
    	if(isset($_POST['sub_btn']))
		{
			$fields = $this->getAndSetFields();
			$this->crossValidate($fields);
			if($this->console->iFormErrCount == 0)
			{
				$update = $this->MODEL->dbDataUpdate($id, $this->dbLayout, $fields);
				if($update)
				{
					$this->editPostQuery($id);
					$url = BASE_HREF . 'admin/' . $this->mvc['CONTROLLER'] . '/success/update';
					header('Location:' . $url);
					exit;
				} else {
					$this->console->formerror('It appears there was a problem updating that information.');
				}
			}
		} else {
			// DISPLAY
			$id = (int)$action;
			$fields = $this->MODEL->getTblData($this->dbLayout, 0, $id);
			if(isset($fields[$id])) $fields = $fields[$id];
			else {
				$this->console->warning('Edit ID (' . $id . ') was not found');
			}
		}
		$this->assignInputValues($fields);
    }

    public function editPostQuery($id) {}

    public function create($action)
    {
    	$this->mvc['TEMPLATE']['BODY'] = BASE_PATH . 'mvc' . DS . 'admin' . DS . $this->mvc['CONTROLLER'] . '.edit.php';
    	$fields = $this->getAndSetFields();
    	if(isset($_POST['sub_btn']))
    	{
    		$this->crossValidate($fields);
			if($this->console->iFormErrCount == 0)
			{
				$insert = $this->MODEL->dbDataInsert($this->dbLayout, $fields);
				if($insert)
				{
					$id = $this->MODEL->lastId();
					$this->editPostQuery($id);
					$url = BASE_HREF . 'admin/' . $this->mvc['CONTROLLER'] . '/success/create';
					header('Location:' . $url);
					exit;
				} else {
					$this->console->formerror('It appears there was a problem updating that information.');
				}
			}
    	}
    	$this->assignInputValues($fields);
    	if((int)is_callable(array($this->mvc['CONTROLLERCLASSNAME'], 'editExt')) === 1)
	{
		$this->editExt($action);
	}
    }

    private function assignInputValues(array $fields = NULL)
    {
    	if(!$fields) $fields = $this->getAndSetFields();
    	foreach($fields as $k => $v)
		{
			$this->templatevars['inputvalue'][$k] = $v;
		}
    	$defaults = $this->inputDefaults();
		foreach($defaults as $k => $v)
		{
			if(isset($this->templatevars['inputvalue'][$k]) && ($this->templatevars['inputvalue'][$k] != ''))
			{
				continue;
			}
			$this->templatevars['inputvalue'][$k] = $v;
		}
    }

	public function __destruct()
	{
		parent::__destruct();
	}
}

require_once ('adminindex._controller.php');