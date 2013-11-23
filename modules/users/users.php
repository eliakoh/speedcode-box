<?php
class users extends Module {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function admin_index() {
        return $this->admin_list();
    }
    
    public function admin_list() {
        set_title(t('Liste des utilisateurs'));
        add_breadcrumb_item($this->request->get_uri(), t('Liste des utilisateurs'));
        
        $this->load_tpl('list');
        
        $data = array();
        
        // Fetches all profiles
        $rows = $this->db->query('SELECT * FROM {profiles}')->rows();
        $profiles = array();
        foreach($rows as $value) {
            $profiles[$value['id']] = $value['label'];
        }
        unset($rows);
        // Fetches all languages
        $rows = $this->db->query('SELECT `id`, `label` FROM {languages}')->rows();
        $languages = array();
        foreach($rows as $value) {
            $languages[$value['id']] = $value['label'];
        }
        unset($rows);
        $data['items'] = $this->db_list();
        foreach($data['items'] as &$item) {
            $item['profile'] = $profiles[$item['profile_id']];
            $item['language'] = $languages[$item['language_id']];
            $item['status'] = ($item['status'] == 1 ? 'ok' : 'remove');
        }
        unset($item);
        unset($profiles);
        unset($languages);
        
        return $this->render($data);
    }
    
    public function admin_add() {
        set_title(t('Ajouter un utilisateur'));
        add_breadcrumb_item($this->request->get_uri(), t('Ajouter'));
        
        $this->load_tpl('form');
        
        $data = array();
        
        return $this->render($data);
    }
    
    public function admin_edit($params) {
        if(!isset($params['uid'])) {
            error(t('Utilisateur inexistant'), Error::WARNING);
            return false;
        }
    }
}
?>
