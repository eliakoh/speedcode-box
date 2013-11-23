<?php
class modules extends Module {
    
    public function admin_index($parameters) {
        set_title('Administration des modules');
        add_breadcrumb_item($this->request->get_uri(true), 'Administration des modules');
        
        $this->load_tpl('list');
        
        $data = array();
        
        $modules_list = $this->app->get_raw_modules_list();
        foreach($modules_list as $module_name) {
            $module = $this->app->load_module_raw($module_name);
            
            $data['items'][] = array(
                'label'     => $module->label,
                'name'      => $module_name,
                'status'    => 'Unknown',
                'action'    => '',
            );
        }
        
        return $this->render($data);
    }
}
?>
