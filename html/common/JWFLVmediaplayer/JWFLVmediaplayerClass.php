<?php
/*=====================================================================
    (C)2008 BeaBo by Hiroki Seike 
 ======================================================================
    URL       : http://beabo.net/
    Email     : info@beabo.net
    File      : JW FLV Media Player PHP Class Library
    Version   : 0.5
    Date      : 2007-04-17
    Memo      : JW FLV Media Player Ver 3.15
              : http://www.jeroenwijering.com/?item=JW_FLV_Media_Player
=====================================================================*/

class JWFLVmediaplayer
{

    public $playerUrl        = "";    // 
    public $divName           = "placeholder";

    // For embed Flash objects extra Parameters
    public $allowfullscreen   = true;
    public $allowscriptaccess = "always";
    public $menu              = false ;
    public $mode              = "opaque";
// TO DO
// menu (true, false): set this to false to hide most of the rightclick menu.
// wmode (opaque, transparent, window): set this to either transparent or opaque to fix z-index or flickering issues.

    // The basics
    public $height            = "320";
    public $width             = "650";

    public $file              = "";
    public $image             = "";
    public $id                = "";
    public $searchbar         = false;


    // The colors
    public $backcolor         = "";    //  0xFFFFFF ;
    public $frontcolor        = "0x000000";
    public $lightcolor        = "";    // 0x000000
    public $screencolor       = "0x000000";
    // Display appearance
    public $logo              = "";
    public $overstretch       = false ;
    public $showicons         = true;
    public $transition        = "random";
    // Controlbar appearance
    public $shownavigation    = true;
    public $showstop          = false ;
    public $showdigits        = true;
    public $showdownload      = false ;
    public $usefullscreen     = true;
    // Playlist appearance (only for the mediaplayer)
    public $autoscroll        = false ;
    public $displayheight     = "400";
    public $displaywidthembed = false ;   // using  = thumbsinplaylist
    public $displaywidth      = "400";
    public $thumbsinplaylist  = true;
    //Playback behaviour
    public $audio             = "";
    public $autostart         = false ;
    public $bufferlength      = "3";
    public $captions          = "";
    public $fallback          = "";
    public $repeat            = false ;
    public $rotatetime        = "5";
    public $shuffle           = false ;
    public $volume            = "80";
    // External communication
    public $callback          = "";
    public $enablejs          = false ;
    public $javascriptid      = "";
    public $link              = "";
    public $linkfromdisplay   = false ;
    public $linktarget        = "_self";
    public $recommendations   = "";
    public $streamscript      = "";
    public $type              = "";


    // JW FLV Media Player Url
    public function setPlayerUrl($playerUrl)
    {
        $this -> playerUrl = $playerUrl;
    }

    // embed DIV name
    public function setDivName($divName)
    {
        $this -> divName = $divName;
    }

    // True fullscreen only works if you have the Flash Player 9,0,28,0 or higher installed.
    //  H264 video only works from version 9.0.98. 
    // If you use the SWFObject javascript to embed your player, you can use it's auto-update functionality.
    //  Also make sure you have the parameter allowfullscreen set to true in your embed code!
    public function setDisableAllowfullscreen()
    {
        $this -> allowfullscreen = false;
    }

    // --------------------------------------------------------
    // The basics
    // --------------------------------------------------------

    // Sets the overall height of the player/rotator.
    public function setHeight($height)
    {
        $this -> height = $height;
    }

    // Sets the overall width of the player/rotator.
    public function setWidth($width)
    {
        $this -> width = $width;
    }

    // Sets the location of the file to play.
    // The mediaplayer can play a single MP3, FLV, SWF, JPG, GIF, PNG, H264 
    public function setFile($type)
    {
        $this -> file = $type;
    }

    // If you play a sound or movie, set this to the url of a preview image.
    // When using a playlist, you can set an image  for every entry.
    public function setImage($image)
    {
        $this -> image = $image ;
    }
    // Use this to set the RTMP stream identifier (example) with the mediaplayer.
    // The ID will also be sent to statistics callbacks.
    //  If you play a playlist, you can set an id for every entry.
    public function setId($id)
    {
        $this -> id = $id ;
    }

    // Useing searchbar
    public function setEnableSearchbar()
    {
        $this -> searchbar = true;
    }


    // --------------------------------------------------------
    // The colors
    // --------------------------------------------------------

    // Backgroundcolor of the controls, in HEX format.
    // Defult color is 0xFFFFFF
    public function setBackcolor($backcolor)
    {
        $this -> backcolor = $backcolor ;
    }

    // Texts & buttons color of the controls, in HEX format.
    public function setFrontcolor($frontcolor)
    {
        $this -> frontcolor = $frontcolor ;
    }

    // Rollover color of the controls, in HEX format.
    public function setLightcolor($lightcolor)
    {
        $this -> lightcolor = $lightcolor ;
    }

    // Color of the display area, in HEX format.
    //  With the rotator, change this to your HTML page's color make images of different sizes blend nicely
    public function setScreencolor($screencolor)
    {
        $this -> screencolor = $screencolor ;
    }

    // --------------------------------------------------------
    // Display appearance
    // --------------------------------------------------------

    // Set this to an image that can be put as a watermark logo in the top right corner of the display.
    // Transparent PNG files give the best results
    public function setLogo($logo)
    {
        $this -> logo = $logo ;
    }


    public function setEnableOverstretch()
    {
        $this -> overstretch = true;
    }

    // Sets how to stretch images/movies to make them fit the display.
    // The default stretches to fit the display. 
    public function setDisableShowicons()
    {
        $this -> showicons = false;
    }




    // Only for the rotator. Sets the transition to use between images.
    // The default, random, randomly pick a transition.
    public function setTransition($type)
    {
        switch ($type) {
            case 'fade':
                $this -> transition = "fade" ;
                break;
            case 'bgfade':
                $this -> transition = "bgfade" ;
                break;
            case 'blocks':
                $this -> transition = "blocks" ;
                break;
            case 'bubbles':
                $this -> transition = "bubbles" ;
                break;
            case 'circles':
                $this -> transition = "circles" ;
                break;
            case 'flash':
                $this -> transition = "flash" ;
                break;
            case 'fluids':
                $this -> transition = "fluids" ;
                break;
            case 'lines':
                $this -> transition = "lines" ;
                break;
            case 'slowfade':
                $this -> transition = "slowfade" ;
                break;
            case 'random':
            default:
                $this -> transition = "random" ;
                break;
        }
    }

    // --------------------------------------------------------
    // Controlbar appearance
    // --------------------------------------------------------
    public function setDisableShownavigation()
    {
        $this -> shownavigation = false;
    }

    public function setEnableShowstop()
    {
        $this -> showstop = true;
    }

    public function setDisableShowdigits()
    {
        $this -> showdigits = false;
    }

    public function setEnableShowdownload()
    {
        $this -> showdownload = true;
    }

    public function setDisableUsefullscreen()
    {
        $this -> usefullscreen = false;
    }

    // --------------------------------------------------------
    // Playlist appearance (only for the mediaplayer)
    // --------------------------------------------------------
    public function setEnableAutoscroll()
    {
        $this -> autoscroll = true;
    }

    public function setDisplayheight($displayheight)
    {
        $this -> displayheight = $displayheight ;
    }

    public function setDisplaywidth($embed, $displaywidth)
    {
        $this -> displaywidthembed = $embed ;
        $this -> displaywidth      = $displaywidth ;
    }

    public function setDisableThumbsinplaylist()
    {
        $this -> thumbsinplaylist = false;
    }

    // --------------------------------------------------------
    // Playback behaviour
    // --------------------------------------------------------
    public function setAudio($audio)
    {
        $this -> audio = $audio ;
    }

    public function setEnableAutostart()
    {
        $this -> autostart = true;
    }

    public function setBufferlength($bufferlength)
    {
        $this -> bufferlength = $bufferlength ;
    }

    public function setCaptions($captions)
    {
        $this -> captions = $captions ;
    }

    public function setFallback($fallback)
    {
        $this -> fallback = $fallback ;
    }

//	function setEnableRepeat()
//	{
//		$this -> repeat = true;
//	}

    // Not for the wmvplayer. Set this to true to automatically repeat playback of all files.
    //  Set this to list to playback an entire playlist once.
    public function setRepeat($repeat)
    {
        $this -> repeat = $repeat ;
    }


    public function setRotatetime($rotatetime)
    {
        $this -> rotatetime = $rotatetime ;
    }

    public function setEnableShuffle()
    {
        $this -> shuffle = true;
    }

    public function setVolume($volume)
    {
        $this -> volume = $volume ;
    }

    // --------------------------------------------------------
    // External communication
    // --------------------------------------------------------
    public function setCallback($callback)
    {
        $this -> callback = $callback ;
    }

    public function setEnableEnablejs()
    {
        $this -> enablejs = true;
    }

    public function setJavascriptid($javascriptid)
    {
        $this -> javascriptid = $javascriptid ;
    }

    public function setLink($link)
    {
        $this -> link  = $link  ;
    }

    public function setEnableLinkfromdisplay()
    {
        $this -> linkfromdisplay = true;
    }

    public function setLinktarget($linktarget)
    {
        $this -> linktarget = $linktarget ;
    }

    public function setRecommendations($recommendations)
    {
        $this -> recommendations = $recommendations ;
    }

    public function setStreamscript($streamscript)
    {
        $this -> streamscript = $streamscript ;
    }

    public function setType($type)
    {
        $this -> type = $type ;
    }

    // --------------------------------------------------------
    // embed SWFObject with javascript
    // SWFObject
    // A very good, freely available javascript to use for this embed proces, is the SWFObject script by Geoff Stearns.
    // It's used extensively on this site, wherever an SWF file is embedded.
    // To use it, first upload the swfobject.js to your server and include it in the <head> section of your website:
    // --------------------------------------------------------
    // GetPlayerHeader
    public function GetPlayer()
    {
        $_output = '<!-- '. "\n";
        $_output.= 'JW FLVmediaplayer v3.15'. "\n";
        $_output.= 'http://www.jeroenwijering.com/?item=JW_FLV_Media_Player'. "\n";
        $_output.= 'JW FLVmediaplayer.class.php v0.1'. "\n";
        $_output.= 'http://beabo.net/'. "\n";
        $_output.= '-->'. "\n";

        $_output.= '<div id="'.$this -> divName.'"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>'. "\n";
        $_output.= '<script type="text/javascript" src="swfobject.js"></script>'. "\n";
        $_output.= '<script type="text/javascript">'. "\n";
        $_output.= '  var s1 = new SWFObject("mediaplayer.swf","mediaplayer","'.$this -> width.'","'.$this -> height.'","7");'. "\n";
        if ($this -> allowfullscreen) {
            $allowfullscreen = "true";
        } else {
            $allowfullscreen = "false";
        }
        // addParam setting
        $_output.= '  s1.addParam("allowfullscreen","'.$allowfullscreen. '");'. "\n";
        $_output.= '  s1.addParam("allowscriptaccess","always");'. "\n";
        $_output.= '  s1.addParam("wmode","opaque");'. "\n";
        // addVariable setting
        $_output.= '  s1.addVariable("shuffle","'. $this -> shuffle. '");'. "\n";
        if ($this -> linkfromdisplay) {
            $_output.= '  s1.addVariable("linkfromdisplay","true");'. "\n";
        }
        if ($this -> displaywidthembed) {
            $_output.= '  s1.addVariable("displaywidth","'. $this -> displaywidth.'"); '. "\n";
        }
        if ($this -> autoscroll) {
            $_output.= '  s1.addVariable("autoscroll","true");'. "\n";
        }
        if ($this -> repeat) {
            $_output.= '  s1.addVariable("repeat","'. $this -> repeat . '");'. "\n";
        }
        if ($this -> image) {
            $_output.= '  s1.addVariable("image","'.$this -> image.'");'. "\n";
        }
        if ($this -> logo) {
            $_output.= '  s1.addVariable("logo","'.$this -> logo.'");'. "\n";
        }
        if ($this -> recommendations) {
            $_output.= '  s1.addVariable("recommendations","'. $this -> recommendations. '");'. "\n";
        }
        if ($this -> lightcolor) {
            $_output.= '  s1.addVariable("lightcolor","'. $this -> lightcolor . '");'. "\n";
        }
        if ($this -> backcolor) {
            $_output.= '  s1.addVariable("bgcolor","'. $this -> backcolor . '");'. "\n";
        }
        $_output.= '  s1.addVariable("file","'.$this -> file.'");'. "\n";
        $_output.= '  s1.addVariable("width","'.$this -> width.'");'. "\n";
        $_output.= '  s1.addVariable("height","'.$this -> height.'");'. "\n";

        $_output.= '  s1.write("'.$this -> divName.'");'. "\n";
//		$_output.= '  alert(player.getPlaylist()[1].title);'. "\n";

        $_output.= '</script>'. "\n";
        return $_output;
    }


/*
<script type="text/javascript">
  document.getElementById("mediaspace").style.paddingTop = '0px';
  var so = new SWFObject('<{$xoops_url}>/modules/<{$mydirname}>/mediaplayer.swf','player','600','300','8');
  so.addParam("allowfullscreen","true");
  so.addParam("allowscriptaccess","always");
  so.addParam("wmode","opaque");
  so.addVariable("shuffle","false");
  so.addParam("bgcolor","#000000");
  so.addVariable('file','lecture.xml');
  so.addVariable('linkfromdisplay','true');
  so.addVariable('displaywidth','320');
  so.addVariable('autoscroll','true');
  so.addVariable('lightcolor','0x0099CC');
  so.addVariable("repeat","list");
  so.addVariable("width","600");
  so.addVariable("height","300");
  so.addVariable("searchbar","false");
  so.write('mediaspace');
</script>
*/

    // --------------------------------------------------------
    // embed SWFObject Flashvars
    // --------------------------------------------------------
    public function embedPlayer()
    {
        $_output = '<embed '. "\n";

        if ($this -> playerUrl) {
            $_output.= 'src="'.$this -> playerUrl.'mediaplayer.swf" '. "\n";
        } else {
            $_output.= 'src="mediaplayer.swf" '. "\n";
        }
        $_output.= 'width="'. $this -> width.'" '. "\n";
        $_output.= 'height="'. $this -> height.'"'. "\n";
        $_output.= 'allowscriptaccess="'. $this ->allowscriptaccess. '" '. "\n";
        if ($this -> allowfullscreen) {
            $allowfullscreen = "true";
        } else {
            $allowfullscreen = "false";
        }
        $_output.= 'allowfullscreen="'.$allowfullscreen. '" '. "\n";
        $_output.= 'flashvars="width='.$this -> width.'&height='.$this -> height.'&file='.$this -> file ;
        // preview image
        if ($this -> image) {
            $_output.= '&image='.$this -> image ;
        }
        // set display height
//		if ($this -> displayheight) {
//			$_output.= '&displayheight='.$this -> displayheight ;
//		}
        // user searchbar
        if ($this -> searchbar) {
            $_output.= '&searchbar='.$this -> searchbar ;
        }
        //&backcolor=0x112200&frontcolor=0xffffff&lightcolor=0x88BB00"
        $_output.= '" />'. "\n";
//		$_output.= '<noembed>You need Flash plugin</noembed>'. "\n";

 
        return $_output;
    }
}
