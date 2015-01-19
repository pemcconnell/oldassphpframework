<?php

class LoginController extends AdminBaseController {

    public function __construct() {
        parent::__construct(false);

        if ($this->session->is_set('admin')) { // SESSION LOGIN
            header('Location:' . BASE_HREF . 'admin/pages');
            exit;
        } elseif (isset($_COOKIE[BASE_HREF . '-mg'])) { // COOKIE LOGIN
            $this->authLoginCookie();
        }

        $this->templatevars['GBL_stylesheets'][] = BASE_HREF . 'admin/css/login.css';
    }

    public function index() {
        if (isset($_POST['sub_btn'])) {
            $uname = isset($_POST['uname_txt']) ? $_POST['uname_txt'] : '';
            $pwd = isset($_POST['pwd_txt']) ? $_POST['pwd_txt'] : '';


            $uname = Validate::minlen($uname, 3, 'Please insert your username');
            $pwd = Validate::password($pwd, 3, 'Please insert your password');
            $rem_chk = isset($_POST['remember_chk']) ? 1 : 0;

            if ($this->console->iFormErrCount === 0) {
                $row = $this->MODEL->cmslogin($uname, $pwd);

                if (is_array($row)) {
                    if ($rem_chk) {
                        $this->generateLoginCookie($uname, $row['id']);
                    }
                    $this->processCMSLogin($row);
                } else {
                    $this->console->formerror('It appears the login information you entered is incorrect.');
                }
            }
        }
    }

    private function generateLoginCookie($uname, $id) {
        $fulllen = strlen($uname);
        $ulen = ceil($fulllen / 2);
        $halfChar = str_split($uname);
        $halfChar = $halfChar[$ulen];
        $enc = base64_encode($this->settings['auth']['salt'] . strrev(base64_encode(strrev($uname) . '_:_' . ($id + 4121))) . $this->settings['auth']['pepper'] . '_::_' . $fulllen . '<>' . strrev(base64_encode($this->settings['auth']['salt'] . $halfChar)));
        setcookie($_COOKIE[BASE_HREF . '-mg'], $enc, time() + (3600 * 24 * 60)); // 60 days
        return;
    }

    public function __destruct() {
        parent::__destruct();
    }

}
