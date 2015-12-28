<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Slider\Controller;

use \Fennec\Controller\Base;
use \Fennec\Modules\Slider\Model\Banner as BannerModel;

/**
 * Basic banner sliding module
 *
 * @author David Lima
 * @version r1.0
 */
class Index extends Base
{
    
    /**
     * Banner model
     * @var \Fennec\Modules\Slider\Model\Banner
     */
    public $model;

    public function __construct()
    {
        $this->model = new BannerModel();
    }
    
    /**
     * Default action
     * This will echo the slider block
     */
    public function sliderAction()
    {
        echo $this->loadSlider();
    }
    
    /**
     * Return slider complete HTML
     * @return string
     */
    public function loadSlider() {
        $template = <<<SLIDER
        <ul id="slider">
            {{CONTENT}}
		</ul>
SLIDER;
        $banners = $this->model->getAllActive();
        $content = '';
        foreach ($banners as $banner) {
            $content .= '<li>';
            if ($banner->getUrl()) {
                $content .= "<a href=\"{$banner->getUrl()}\">";
            }
            $content .= "<img src=\"/uploads/slider/{$banner->getImage()}\">";            
            if ($banner->getUrl()) {
                $content .= "</a>";
            }
            $content .= '</li>' . PHP_EOL;
		}
		
		return str_replace('{{CONTENT}}', $content, $template);
    }
}
