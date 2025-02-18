<?php
require_once DIR_CONFIG . 'revslider/revslideropencart_loader.php';
class ControllerExtensionModulerevslideroutput extends Controller {
	public function index($setting){

		if(!defined('rs_plugin_url'))
		define('rs_plugin_url', REV_DIR_CONFIG.'revslider/');

		if(!defined('rs_admin_css'))
		define('rs_admin_css', rs_plugin_url.'css/');

		if(!defined('rs_admin_js'))
		define('rs_admin_js', rs_plugin_url.'js/');

		if(!defined('rs_admin_img'))
		define('rs_admin_img', rs_plugin_url.'images/');

		if(!defined('rs_rs_plugin'))
		define('rs_rs_plugin', rs_plugin_url.'rs-plugin/');

		static $module = 0;


// start add font 
	$font = new ThemePunch_Fonts();
	$fonts = $font->get_all_fonts();
	if(!empty($fonts)){
		$http = get_http();
		foreach($fonts as $font){
			if($font !== ''){
				$url = $http."fonts.googleapis.com/css?family=".strip_tags($font['url']);
				$this->document->addStyle($url);
			}
		}
	}
// end add font 
// start load media file
	$this->document->addScript(rs_rs_plugin.'js/jquery.themepunch.tools.min.js');
	$this->document->addScript(rs_rs_plugin.'js/jquery.themepunch.revolution.min.js');

	$this->document->addStyle(rs_rs_plugin.'css/settings.css');
	$this->document->addStyle(rs_rs_plugin.'css/static-captions.css');
	$this->document->addStyle(rs_rs_plugin.'css/dynamic-captions.css');
	$this->document->addStyle(rs_rs_plugin.'css/captions.css');
// end load media file
// start revsliderfront setting 
	$sliders = $this->hookCommonCb($setting['slider_id']);   

        $content = '';
        $g_fonts = '';
        
        if (!empty($sliders)) {            
            ob_start();
            foreach ($sliders as $slider):
                $slider = (object)$slider;
                $params = json_decode($slider->params);
                $params->jquery_noconflict = 'off';

                if(isset($params->google_font) && is_array($params->google_font)){
                	foreach ($params->google_font as $google_font){
                		$g_fonts .= html_entity_decode($google_font);
                	}
                }

		RevSliderOutput::putSlider($setting['slider_id'], '');

            endforeach;
            $content = ob_get_contents();
            ob_end_clean();
        }
        $content .= $g_fonts;
        $data['rev_content'] = $content;
// end revsliderfront setting 
        
//		if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/module/revslideroutput.tpl')) {
//			return $this->load->view($this->config->get('config_template') . '/template/extension/module/revslideroutput.tpl', $data);
//		}else{
			return $this->load->view('extension/module/revslideroutput', $data);
//		}
	}
	protected function _prehook() {
        require_once DIR_CONFIG . 'revslider/revslider_front.php'; 
        $revfront = new RevSliderFront(__DIR__);
        return $revfront;
    }
    protected function hookCommonCb($slider_id = ''){
    	$wpdb = rev_db_class::rev_db_instance();
        $revfront = $this->_prehook();
        $sql = "SELECT * FROM " .$wpdb->prefix.GlobalsRevSlider::TABLE_SLIDERS_NAME;
        if(!empty($slider_id)){
        	$sql .= " WHERE id = ".$slider_id; 
        }
        $sliders = $wpdb->get_results($sql);
        return $sliders;
    }
    public function price_format($price = ''){
        $price = $this->currency->format($price);
        return $price;
    }
}