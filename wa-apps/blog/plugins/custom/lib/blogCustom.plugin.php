<?php

class blogCustomPlugin extends blogPlugin {
    public function addControls(){
        $this->addCss('css/custom.css',true);
        $this->addJs('js/comment_edit.js',true);
    }
}