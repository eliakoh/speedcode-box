<?php
class menus extends Module {
    
    public function block_main() {
        return build_menu(5)->render(SpeedcodeBox::getApp()->breadcrumb);
    }
    
    public function block_admin() {
        if($this->user->uid != 0) {
            return build_menu(1)->render(SpeedcodeBox::getApp()->breadcrumb);
        }
    }
}
?>
