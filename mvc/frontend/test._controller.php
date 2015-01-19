<?php
/**
 * Test Controller
 *
 * @created 31-Jan-2013 23:39:47
 * @author Peter
 */
class TestController extends FrontendBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $val = 'hello';

        $Form = new Form;

        $Form->item (
            $Form->lbl('Name'), $Form->input($val)
        );

        $Form->item (
            $Form->lbl('Email'), $Form->input($val)
        );

        $opts = array(
            1 => 'Yellow',
            2 => 'Red',
            3 => 'Blue'
        );
        $Form->item (
            $Form->lbl('Choices'), $Form->select($opts, $val)
        );

        $Form->item (
            $Form->lbl('Comments'), $Form->textarea($val)
        );

        $opts = array(
            'Newsletter',
            'SMS marketing',
            'Telephone marketing'
        );
        $Form->item (
            $Form->lbl('Subscribe To'), $Form->checkboxes($opts, $val)
        );

        $this->templatevars['form'] = $Form->display();
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}