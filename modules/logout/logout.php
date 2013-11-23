<?php
class logout extends Module {
    
    public function index() {
        $_SESSION = array();
        session_destroy();
        setcookie ('logn', '', time() - 3600);
        setcookie ('pwd', '', time() - 3600);
        redirect($this->app->setting('public.base_uri'));
    }
}
?>
