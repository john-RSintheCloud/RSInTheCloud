<?php

namespace Framework; 

/**
 * Script for view generation
 * 
 * Expects content (phtml), data and layout to be injected
 * 
 * Extend me for more complicated pages!
 *
 * @author JohnB
 */
class View extends wwAbstract
{
    protected $content = '404.phtml';
    protected $data;
    protected $layout;


    
    public function render()
    {
        echo $this->compose();
    }
    
    public function getHtml()
    {
        //  return the html
        return $this->compose();
    }
    
        protected function compose()
    {
        ob_start(); 
        
        if (!empty($this->layout->header)){
            include $this->layout->header;
        }
        include($this->content);
        if (!empty($this->layout->footer)){
            include $this->layout->footer;
        }
        
        $template = ob_get_contents(); 
        ob_end_clean(); 
        
        return $template;
    }

}
