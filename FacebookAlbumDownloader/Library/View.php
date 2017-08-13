<?php

namespace FacebookAlbumDownloader\Library;

class View
{
    // Path to view
    protected $view;
    // Variables passed in
    protected $vars = array();

    /**      
     *  Creates a templete/view object
     */
    public function __construct($view){
        $this->view = $view;
    }

    /**
     *  Get view variables
     */
    public function __get($key){
        return $this->vars[$key];
    }

    /**
     *  Set view variables
     */
    public function __set($key,$value){
        $this->vars[$key] = $value;
    }

    /**
     *  Convert object to string
     */
    public function __toString(){
        extract($this->vars);
        chdir(dirname($this->view));
        ob_start();
        include basename($this->view);
        return ob_get_clean();
    }
}