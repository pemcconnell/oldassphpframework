<?php

class Form {

    public $sLblPostfix = ':';
    private $_sLastId = '',
            $_sOutput = '',
            $_sMethod = 'post',
            $_sAction = '';

    public function __construct() {
        
    }

    public function method($s = 'post') {
        $s = trim(strtolower($s));
        if (in_array($s, array('post', 'get'))) {
            $this->_sMethod = $s;
        }
    }

    public function action($s) {
        $this->_sAction = $s;
    }

    public function formHeader() {
        $html = '<form method="' . $this->_sMethod . '" action="' . $this->_sAction . '">';
        $html .= '<fieldset><ul>';
        return $html;
    }

    public function formFooter() {
        $html = '</ul></fieldset>';
        $html .= '</form>';
        return $html;
    }

    public function item($s) {
        return '<li>' . $s . '</li>';
    }

    public function display() {
        return $this->formHeader() . $this->_sOutput . $this->formFooter();
    }

    public function lbl($sName, $id = '') {
        if ($id == '')
            $id = HTML::createId(($id == '') ? $sName : $id);
        $this->_sLastId = $id;
        $html = '<label for="' . $this->_sLastId . '">' . $sName . $this->sLblPostfix . '</label>';
        $this->_sOutput .= $html;
        return $html;
    }

    public function input($val) {
        $html = '<input type="text" name="' . $this->_sLastId . '" id="' . $this->_sLastId . '" value="' . $val . '" />';
        $this->_sOutput .= $html;
        return $html;
    }

    public function submit($val = 'Submit') {
        $html = '<input type="submit" name="' . $this->_sLastId . '" id="' . $this->_sLastId . '" value="' . $val . '" />';
        $this->_sOutput .= $html;
        return $html;
    }

    public function textarea() {
        
    }

    public function select() {
        
    }

    public function checkboxes() {
        
    }

}