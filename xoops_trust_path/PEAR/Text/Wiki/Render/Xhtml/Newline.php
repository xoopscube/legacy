<?php

class Text_Wiki_Render_Xhtml_Newline extends Text_Wiki_Render
{
    
    
    public function token($options)
    {
        return "<br />\n";
    }
}
