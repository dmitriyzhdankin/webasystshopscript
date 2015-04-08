<?php
class shopCustommenuPlugin extends shopPlugin {
    
    protected static $plugin;
    
    public function addBackendSettings() {
        return array('aux_li' => '<li class="small float-right"><a href="?plugin=custommenu&action=list">Custom Menu</a></li>');
    }
    protected static function getThisPlugin() {
        if (self::$plugin) {
            return self::$plugin;
        } else {
            $info = array(
                'id' => 'custommenu',
                'app_id' => 'shop'
            );
            return new shopCustommenuPlugin($info);
        }
    }
    public static function displayMenu($menu_id) {
        $plugin = self::getThisPlugin();
        $BA = new shopCustommenuPluginBackendActions();
        $items = $BA->getMenuItems($menu_id);
        $html = $plugin->renderMenu($items,0,0);
        return '<ul class="nav navbar-nav">'.$html.'</ul>';
    }
    
    private function renderMenu($items,$parent_id,$level) {
        $html = '';

        if(isset($items[$parent_id])) {
            $col_num = 0;
            foreach($items[$parent_id] as $item) {
                $has_child = false;
                if( isset( $items[$item['id']] ) && $items[$item['id']] ) { // if has child
                    $has_child = true;
                }

                if ($item['parent_id'] == 0) { $level = 0; }

                if( $level == 0 ) {
                    $html .= '<li'. ( $has_child ? ' class="dropdown  yamm-fw"' : '') .'>'.
                                '<a href="'. $item['url'] .'"'. ( $has_child ? ' class="dropdown-toggle"' : '') .' title="'. $item['title'] .'">'. $item['title'] .($has_child ? ' <i class="fa fa-caret-right fa-rotate-45"></i>' : '').'</a>'.
                                ( $has_child ?
                                    '<ul class="dropdown-menu list-unstyled  fadeInUp animated">'.
                                    '<li>'.
                                        '<div class="yamm-content">'.
                                            '<div class="row">'
                                    :''
                                );
                }

                if($level==1) {
                    $product_html = false;
                    if($item['type'] == 'product') {
                        $wa = new shopViewHelper(waSystem::getInstance());
                        $product = $wa->product($item['url']);
                        $product['frontend_url'] = $wa->productUrl($product);
                        $view = wa()->getView();
                        $view->assign('p', $product);
                        $theme = new waTheme('fenix','shop');
                        //var_dump($theme);die('2');
                        //var_dump(wa()->getStorage()->get('blog/test.ganzo.su/theme'));die;
                        //var_dump(wa()->getStorage()->get('shop/'.wa()->getRouting()->getDomain().'/theme'));die;
                        //$theme = new waTheme(wa()->getStorage()->get('shop/'.wa()->getRouting()->getDomain().'/theme'));

                        $product_html = $view->fetch($theme->path . '/product_grid.html');
                    }

                    if(!$col_num) {
                        $col_num = 1;
                        $html .= '<div class="col-md-3">'.
                            '<div class="header-menu">'.
                            ( $product_html ? $product_html : '<a href="'. $item['url'] .'" title="'. $item['title'] .'"><h4>'. $item['title'] .'</h4></a>') .
                            '</div>'.
                            '<ul class="list-unstyled">';
                    } elseif($col_num != $item['column'] ) {
                        $col_num = $item['column'];
                        $html .= '</ul></div><div class="col-md-3">'.
                            '<div class="header-menu">'.
                            ( $product_html ? $product_html : '<a href="'. $item['url'] .'" title="'. $item['title'] .'"><h4>'. $item['title'] .'</h4></a>').
                            '</div>'.
                            '<ul class="list-unstyled">';
                    } else {
                        $html .= '</ul><div class="header-menu">'.
                            ( $product_html ? $product_html : '<a href="'. $item['url'] .'" title="'. $item['title'] .'"><h4>'. $item['title'] .'</h4></a>').
                            '</div>'.
                            '<ul class="list-unstyled">';
                    }
                }

                if($level==2) {
                    $html .= '<li><a href="'. $item['url'] .'" title="'. $item['title'] .'"><i class="fa fa-angle-right"></i>'. $item['title'] .'</a></li>';
                }

                if( $has_child ) {
                    $html .= $this->renderMenu($items,$item['id'],$level+1);
                }

                if( $level == 0 ) {
                    $html .=  ( $has_child ? '</div>'.
                                '</div>'.
                            '</li>'.
                        '</ul>' : '').
                    '</li>';
                }
            }
            if($level==1) {
                $html .= '</ul></div>';
            }
        }
        return $html;
    }
}