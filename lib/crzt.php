<?php
defined('_JEXEC') or die;

class CRZT{
    public $API;
    
    
    public $browser;
    // page config
    public $config;
    // page menu
    public $menu;
    // module styles
    public $module_styles;
    // page suffix
    public $page_suffix;
    // which letters will be used for postion naming
    public $positions_letters = array('0'=>'a', '1'=>'b', '2'=>'c', '3'=>'d', '4'=>'e', '5'=>'f');
    // how many columsn we have
    public $max_columns = '6';


    public function __construct($tpl) {
        // load the mootools
        JHtml::_('behavior.framework', true);
	// put the template handler into API field
        $this->API = $tpl;
        // get the params
    }
	

    public function getParam($key, $default) {
        return $this->API->params->get($key, $default);
    }
    public function modules($rule) {
        return $this->API->countModules($rule);
    }

    public function positions($name, $class_row='', $class_mod=''){


		if($this->modules($name."-a") or $this->modules($name."-b") or $this->modules($name."-c") or $this->modules($name."-d") or $this->modules($name."-e") or $this->modules($name."-f") ){ 	
		echo "<div id=\"outer-$name\" class=\"$class_row\">";
		
		$count = 0;
		$layout = explode(",",$this->getParam('layout_'.$name, 1));
		for($i=0;$i<count($layout);$i++){
			if($this->modules($name.'-'.$this->positions_letters[$i])){
			
				echo '<div class="span'.($layout[$i]*2).' '.$class_mod.'" id="'.$name.'-'.$this->positions_letters[$i].'"><jdoc:include type="modules" name="'.$name.'-'.$this->positions_letters[$i].'" style="xhtml"  /></div>';
			
			$count = $count + $layout[$i];
				if($count>=$this->max_columns){
					break;
				}
			}
		}	
		
		echo "</div>";
		}
    }

    public function mainbody(){
	        $count = 0;
		$name = "sidebar";
		$layout = explode(",",$this->getParam('layout_'.$name, 1));
		
		echo "<div class=\"row\">";
		// left colum
		for($i=0;$i<2;$i++){
			if($this->modules($name.'-'.$this->positions_letters[$i])){
			
				echo '<div class="span'.($layout[$i]*2).' '.$class_mod.'" id="sidebar-'.$this->positions_letters[$i].'"><jdoc:include type="modules" name="'.$name.'-'.$this->positions_letters[$i].'"  style="xhtml"  /></div>';
			
				$count = $count + $layout[$i];
				if($count>=2){
					break;
				}
			}
		}	

		$width=0;
		for($i=0;$i<count($layout);$i++){
			if($this->modules($name.'-'.$this->positions_letters[$i])){
				$width = $layout[$i] + $width;
			}
		}
		$width=6-$width;
		//mainbody
		echo "
		<div class=\"component-content span".($width*2)."\">
		";
		
		$this->positions('maintop', 'row');

		echo"
			<div id=\"component\">
				<jdoc:include type=\"message\" />
				<jdoc:include type=\"component\" />
			</div>";
		
		$this->positions('mainbottom', 'row');
		
		echo"
		</div>";	

		
	        $count = 0;
		//right column
		for($i=2;$i<4;$i++){
			if($this->modules($name.'-'.$this->positions_letters[$i])){
			
				echo '<div class="span'.($layout[$i]*2).' '.$class_mod.'" id="sidebar-'.$this->positions_letters[$i].'"><jdoc:include type="modules" name="'.$name.'-'.$this->positions_letters[$i].'" style="xhtml"  /></div>';
			
			$count = $count + $layout[$i];
				if($count>=2){
					break;
				}
			}
		}	


		echo "</div>";
    }

    public function addtohead(){
	    $document = JFactory::getDocument();

	    $document->addStyleSheet($this->API->baseurl."/templates/".$this->API->template."/lib/bootstrap/css/bootstrap.css");	    
	    $document->addStyleSheet($this->API->baseurl."/templates/".$this->API->template."/css/joomla-core.css");	    
	    $document->addStyleSheet($this->API->baseurl."/templates/".$this->API->template."/css/template.css");	    

	    if($this->getParam('google_analytics', '')){
		$script =  "<script type=\"text/javascript\">var _gaq = _gaq || []; _gaq.push(['_setAccount', '".$this->getParam('google_analytics', '')."']); _gaq.push(['_trackPageview']); (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();</script>";
		$document->addScriptDeclaration($script, $type);
	    }
	    if($this->getParam('google_webmastertools', '')){
		echo '<meta name="google-site-verification" content="'.$this->getParam('google_webmastertools', '').'" />';
	    }

	    $this->cache();

    }

    public function cookie(){
	    if(!isset($_COOKIE['crzt_cookie_note'])) {
		
		if($this->modules("cookie-note")){
	
		   	echo '<div id="cookie-note"><a class="crzt-cookie-note-close" href="#close">×</a><div class="cookie-note-info"><jdoc:include type="modules" name="cookie-note" /></div></div>';
		}

	    }
    }

    public function cache(){

	$document = JFactory::getDocument();	
        $cache_css = $this->getParam('css_cache', 'no');
        $cache_js = $this->getParam('js_cache', 'no');
        $toAddURLs = array();
        $toRemove = array();
        $scripts = array();
        $css_urls = array();

	if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

	if ($cache_css=="yes") {
		foreach ($document->_styleSheets as $strSrc => $strAttr) { 
				if (!preg_match('/\?.{1,}$/', $strSrc) && (!isset($strAttr['media']) || $strAttr['media'] == '')) {
					$break = false;
					if(count($toRemove) > 0) {
						foreach ($toRemove as $remove) {
							$remove = str_replace(' ', '', $remove);
							if(strpos($strSrc, $remove) !== false) {
								$toAddURLs[] = $strSrc;
								$break = true;
								continue;
							}
						}
					}
					if(!$break) {    
						if (!preg_match('/\?.{1,}$/', $strSrc)) {
							$srcurl =$this->cleanUrl($strSrc);
							if (!$srcurl) continue;
							//remove this css and add later
								unset($document->_styleSheets[$strSrc]);
								$path = str_replace('/', DS, $srcurl);
								$css_urls[] = array(JPATH_SITE . DS . $path, $srcurl);
		
							//$document->_styleSheets = array();
						}
					}
				}
		}
	       foreach($toAddURLs as $url) $document->addStylesheet($url);
	       $url = $this->optimizecss($css_urls, 'false');
	       if ($url) {
                    $document->addStylesheet($url);
               } else {
                    foreach ($css_urls as $urls) $document->addStylesheet($url[1]); //re-add stylesheet to head
               }
        }
        
        if ($cache_js=="yes"){
        $js_urls = array();
        $toAddURLs = array();
        $toRemove = array();
        $break = false;
          if($document->params->get('jscss_excluded') != '') {
               $toRemove = explode(',',$document->params->get('jscss_excluded'));
          }
        
         foreach ($document->_scripts as $strSrc => $strAttr) {
            
               if(count($toRemove) > 0) {
                    foreach ($toRemove as $remove){
                         $remove = str_replace(' ', '', $remove);
                         if(strpos($strSrc, $remove) !== false) {
                               $toAddURLs[] = $strSrc;
                               $break = true;
                               continue;
                         }
                    }
               }
               if(!$break) {       
               $srcurl = $this->cleanUrl($strSrc);
                unset($document->_scripts[$strSrc]);   
                     if (!$srcurl){
                          $js_urls[] = array($strSrc, $strSrc);
                     } else {
                         $path = str_replace('/', DS, $srcurl);
                     $js_urls[] = array(JPATH_SITE . DS . $path, JURI::base(true) . '/' . $srcurl);
                     }
               }
                  $break = false;
          }
        
          // clean all scripts
          $document->_scripts = array();
          // optimize or re-add
       	  $url = $this->optimizejs($js_urls, false);
          if ($url) {
            $document->addScript($url);
          } else {
              foreach ($js_urls as $urls) $document->addScript($url[1]); //re-add stylesheet to head
          }
             // re-add external scripts
          foreach($toAddURLs as $url) $document->addScript($url); 
	}
    }

    public function optimizecss($css_urls, $overwrite = false) {
	$content = '';
        $files = '';
       
        foreach ($css_urls as $url) {
            $files .= $url[1];
            
            //join css files into one file
            $content .= "/* FILE: {$url[1]} */\n" . $this->compresscss(JFile::read($url[1]), $url[1]) . "\n\n";
        }
        
        $file = md5($files) . '.css';
		if($this->useGZip()) $file = $file.'.php';

		$expireHeader = (int) 30 * 24 * 60 * 60;
		if($this->useGZip()) {
			$headers = "<?php if(extension_loaded('zlib')){ob_start('ob_gzhandler');} header(\"Content-type: text/css\");";
			$headers .= "header(\"Content-Encoding: gzip\");";
		}
		$headers .= "header('Expires: " . gmdate('D, d M Y H:i:s', strtotime(date('D, d M Y H:i:s')) + $expireHeader) . " GMT');";
		$headers .= "header('Last-Modified: " . gmdate('D, d M Y H:i:s', strtotime(date('D, d M Y H:i:s'))) . " GMT');";
		$headers .= "header('Cache-Control: Public');";
		$headers .= "header('Vary: Accept-Encoding');?>";
		
		$content = $headers . $content;

        $url = $this->store_file($content, $file, $overwrite);
        return $url;
    }	

    public function compresscss($data, $url) {
        global $current_css_url;
        $current_css_url = $url;
        /* remove comments */
        $data = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $data);
        /* remove tabs, spaces, new lines, etc. */
        $data = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $data);
        /* remove unnecessary spaces */
        $data = preg_replace('/[ ]+([{};,:])/', '\1', $data);
        $data = preg_replace('/([{};,:])[ ]+/', '\1', $data);
        /* remove empty class */
        $data = preg_replace('/(\}([^\}]*\{\})+)/', '}', $data);
        /* remove PHP code */
        $data = preg_replace('/<\?(.*?)\?>/mix', '', $data);
        /* replace url*/
        $data = preg_replace_callback('/url\(([^\)]*)\)/', array(&$this,'replaceurl'), $data);
        return $data;
    }

    public function optimizejs($js_urls, $overwrite = false) {
        $content = '';
        $files = '';
        jimport('joomla.filesystem.file');
       
        foreach ($js_urls as $url) {
    
            $files .= $url[1];
               $srcurl = $this->cleanUrl($url[1]);
               if (!$srcurl){
                       if (preg_match('/http/', $url[0])) {
                            $external = file_get_contents($url[0]);
                       } else {
                           $external = file_get_contents('http:'.$url[0]);
                       }
                      $content .= "/* FILE: {$url[0]} */\n" . $external . "\n\n";
                  } else {
                          $content .= "/* FILE: {$url[1]} */\n" . @JFile::read($url[0]) . "\n\n";
                  }
        }
       
     
          $file = md5($files) . '.js';
          if($this->useGZip()) $file = $file.'.php';
                   
          $path = JPATH_SITE . DS . 'cache' . DS . 'gk'. DS . $file;
         
          if (is_file($path) && filesize($path) > 0) {
               // skip compression and leave current URL
          } else {
               $content = $this->compressjs($content);
          }
         
         
          $expireHeader = (int) 30 * 24 * 60 * 60;
         
          if($this->useGZip()) {
               $headers = "<?php if(extension_loaded('zlib')){ob_start('ob_gzhandler');} header(\"Content-type: text/javascript\");";
               $headers .= "header(\"Content-Encoding: gzip\");";
          }
          $headers .= "header('Expires: " . gmdate('D, d M Y H:i:s', strtotime(date('D, d M Y H:i:s')) + $expireHeader) . " GMT');";
          $headers .= "header('Last-Modified: " . gmdate('D, d M Y H:i:s', strtotime(date('D, d M Y H:i:s'))) . " GMT');";
          $headers .= "header('Cache-Control: Public');";
          $headers .= "header('Vary: Accept-Encoding');?>";
         
          $content = $headers.$content;         
        $url = $this->store_file($content, $file, true);
        return $url;
    }

    public function compressjs($data) {
        require_once(dirname(__file__) . DS . 'minify' . DS . 'JSMin.php');
    	$data = JSMin::minify($data);
        return $data;
    } 

    public function cleanUrl($strSrc) {
        if (preg_match('/^https?\:/', $strSrc)) {
            if (!preg_match('#^' . preg_quote(JURI::base()) . '#', $strSrc)) return false; //external css
            $strSrc = str_replace(JURI::base(), '', $strSrc);
        } else {
            if (preg_match('/^\//', $strSrc)) {
                if (!preg_match('#^' . preg_quote(JURI::base(true)) . '#', $strSrc)) return false; //same server, but outsite website
                $strSrc = preg_replace('#^' . preg_quote(JURI::base(true)) . '#', '', $strSrc);
            }
          }
        $strSrc = str_replace('//', '/', $strSrc);
        $strSrc = preg_replace('/^\//', '', $strSrc);
        return $strSrc;
    }

    public  function useGZip() {
          if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
               return false;
          } elseif (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
               return false;
          } else {
               return true;
          }
     }

    public function store_file($data, $filename, $overwrite = false) {
 	$path = 'cache' . DS . 'crzt';
        jimport('joomla.filesystem.folder');
        if (!is_dir($path)) JFolder::create($path);
        $path = $path . DS . $filename;
        $url = JURI::base(true) .DS. 'cache'. DS .'crzt' . DS. $filename;
        if (is_file($path) && !$overwrite) return $url;
        JFile::write($path, $data);
        return is_file($path) ? $url : false;
    }
    public function replaceurl($matches) {
        $url = str_replace(array('"', '\''), '', $matches[1]);
        global $current_css_url;
        $url = $this->converturl($url, $current_css_url);
        return "url('$url')";
    }
    
    public function converturl($url, $cssurl) {
        $base = dirname($cssurl);
        if (preg_match('/^(\/|http)/', $url))
            return $url;
        /*absolute or root*/
        while (preg_match('/^\.\.\//', $url)) {
            $base = dirname($base);
            $url = substr($url, 3);
        }
        $url = $base . '/' . $url;
        return $url;
    }
}
?>
