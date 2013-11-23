<?php
class login extends Module {
    
    public function index() {
        if($this->user->uid == 0) {
            $login = $this->request->post('login');
            $password = $this->request->post('password');

            if($login !== false && $password !== false) {
                $granted = $this->user->authenticate($login, $password);
                if($granted) {
                    $redirect_uri = $this->get_ini_var('redirect_after_login');
                    if(!empty($redirect_uri)) {
                        redirect($redirect_uri);
                    }
                    return confirm(t('Vous êtes maintenant connecté'));
                }
            }

            error(t('Identifiants incorrects'), Error::WARNING);
        }
        else {
            error(t('Vous êtes déjà connecté'), Error::WARNING);
        }
    }
    
    public function block_userbar($parameters) {
        $data = array();

        $tpl = 'userbar_notconnected';
        
        if($this->user->uid != 0) {
            $tpl = 'userbar_connected';
            
            $data['admin_menu'] = build_menu(1)->render(SpeedcodeBox::getApp()->breadcrumb);
        }
        
        $this->load_tpl($tpl);
        
        $data['user.label'] = $this->user->label;
        
        return $this->render($data);
    }
}
?>
