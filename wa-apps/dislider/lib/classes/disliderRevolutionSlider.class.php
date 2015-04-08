<?php

class disliderRevolutionSlider extends disliderSlider
{
    private $id = '';
    private $class = '';

    public function __construct($width = null, $height = null) {
        parent::__construct($width, $height);
        $this->type = 'Revolution';
        $this->name = 'Revolution Slider';
        if(!isset(self::$options[$this->type]) || !count(self::$options[$this->type])){
            $path = wa()->getAppPath('lib/config/', 'dislider');
            self::$options[$this->type] = include $path.'revolutionType.php';
        }
        $this->fields['title'] = 1;
        $this->fields['title2'] = 1;
        $this->fields['link'] = 1;
        $this->fields['desc'] = 1;
        $this->js = array(
            array(
                'path' => 'third-party/revolution/',
                'file' => 'jquery.revolution.min.js'
            )
        );
        $this->css = array(
            array(
                'path' => 'third-party/revolution/',
                'file' => 'revolution.css'
            )
        );
        $this->id = 'dislider-revolutionSlider-'.rand();
        $this->class = 'atSlider';
    }

    public function getSliderJS($params = array(), $e_js = array()){

        $this->js[] = array(
            'path' => 'third-party/revolution/',
            'file' => 'jquery.plugins.min.js'
        );
        $this->js[] = array(
            'path' => 'third-party/revolution/',
            'file' => 'jquery.scrollTo-1.4.2-min.js'
        );

        return parent::getSliderJS($params, $e_js);
    }

    public function getSliderCSS($params = array(), $e_css = array()){
        $new_css = array();
//        $this->css[] = array(
//            'path' => 'third-party/anything/',
//            'file' => 'theme-'.$params['theme'].'.css'
//        );
        foreach ($this->css as $css){
            if(!in_array($css['file'], $e_css)){
                $new_css[] = $css['path'].$css['file'];
            }
        }

        return $new_css;
    }

    public function getSliderHTML($params = array(), $slides = array()){
        $html = array();

        if(count($slides)){
            $html[] = '<div class="revolution-container">';
            $html[] = '<div class="revolution">';
            $html[] = '<ul class="list-unstyled">';

            foreach ($slides as $slide){
                $line = '<img data-src="'. $slide['url'] .'" src=""  alt="slidebg1"  data-bgfit="100% auto" data-bgposition="left top" data-bgrepeat="no-repeat">';
                if(isset($slide['title']) && $slide['title']){
                    $line .= '<div class="tp-caption skewfromrightshort customout"'.
                     'data-x="20"'.
                     'data-y="274"'.
                     'data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"'.
                     'data-speed="500"'.
                     'data-start="500"'.
                     'data-easing="Power4.easeOut"'.
                     'data-endspeed="500"'.
                     'data-endeasing="Power4.easeIn"'.
                     'data-captionhidden="on"'.
                     'style="z-index: 4">'.
                        '<div class="slide-title">'.$slide['title'].'</div>'.
                    '</div>';
                }
                if(isset($slide['title2']) && $slide['title2']){
                    $line .= '<div class="tp-caption skewfromrightshort customout"'.
                     'data-x="20"'.
                     'data-y="345"'.
                     'data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;"'.
                     'data-speed="500"'.
                     'data-start="700"'.
                     'data-easing="Power4.easeOut"'.
                     'data-endspeed="500"'.
                     'data-endeasing="Power4.easeIn"'.
                     'data-captionhidden="on"'.
                     'style="z-index: 4">'.
                        '<div class="slide-title2">'.$slide['title2'].'</div>'.
                     '</div>';
                }
                if( isset($slide['link']) && $slide['link'] && isset($slide['description']) && $slide['description'] ){
                    $line .= '<div class="tp-caption customin customout" '.
                     'data-x="20" '.
                     'data-y="450" '.
                     'data-customin="x:0;y:100;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:1;scaleY:3;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:0% 0%;" '.
                     'data-customout="x:0;y:0;z:0;rotationX:0;rotationY:0;rotationZ:0;scaleX:0.75;scaleY:0.75;skewX:0;skewY:0;opacity:0;transformPerspective:600;transformOrigin:50% 50%;" '.
                     'data-speed="500" '.
                     'data-start="1000" '.
                     'data-easing="Power4.easeOut" '.
                     'data-endspeed="500" '.
                     'data-endeasing="Power4.easeIn" '.
                     'data-captionhidden="on" '.
                     'style="z-index: 2">'.
                    '<a href="'. $slide['link'] .'" class="btn-home">'. $slide['description'] .'</a>'.
                '</div>';
                }
                $html[] = '<li data-transition="fade" data-slotamount="7" data-masterspeed="1500" >'.$line.'</li>';
            }
            $html[] = '</ul>';
            $html[] = '<div class="revolutiontimer"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
        }else{
            $html[] = 'No Slides found.';
        }

        return $html;
    }

    public function getSliderJSCall($params = array()){
        $html = array();
        return $html;
    }

}