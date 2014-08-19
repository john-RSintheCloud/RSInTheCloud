<?php

namespace Framework; 

/**
 * Injected into a view
 * to provide header and footer (wrapper) round body content.
 * 
 * @todo Allow dynamic (per page) css, js, title, etc
 * 
 * It is designed to be extended and / or have different parts injected.
 *
 * @author JohnB
 */
class layout extends \Framework\View
{
    protected $path = 'application/views/partials/';
    protected $head = 'head.phtml';
    protected $foot = 'foot.phtml';

    
   
    public function getHeader()
    {
        //  return the path to the header
        return $this->path . $this->head;
    }
    public function getfooter()
    {
        //  return the path to the footer
        return $this->path . $this->foot;
    }

}
