<?php

class shopCustomPlugin extends shopPlugin {
    
    protected static $plugin;
    protected static $added_js = false;
    
    public function __construct($system = null) {
        parent::__construct($system);
        if(!self::$added_js) {
            self::$added_js = true;
            if (wa()->getEnv() == 'backend') {
                waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custom/js/custom_backend.js');
                waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custom/js/review_edit.js');
                waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custom/js/mass_image_delete.js');
                waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custom/js/custom_backend_change_price_count_products.js');
                waSystem::getInstance()->getResponse()->addCss('wa-apps/shop/plugins/custom/css/custom_backend.css');
            }
        }
        if(isset($_GET['image_import'])) {
            $url = '/var/www/fenixrussiaru/data/www/fenix-russia.ru/wa-data/public/shop/products/72/19/1972/images/7000/7000.970.jpg';
            //$url = 'http://fenix-russia.ru/wa-data/public/shop/products/72/19/1972/images/7000/7000.970.jpg';
            $url = 'http://fenix-russia.ru/wa-log/error.log';
            $path = '/var/www/fenixrussiaru/data/www/fenix-russia.ru/wa-cache/temp/shop/csv/upload/images/7000.970.jpg';
            var_dump(fopen($url, 'rb'));die;
        }
    }
    
    public function addBackendSettingsScript() {
//        return array(
//            'sidebar_bottom_li' => '<script type="text/javascript" src="/wa-apps/shop/plugins/custom/js/custom_backend.js">'
//        );
    }
    
    public function addCategoriesForProduct() {
        //waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custom/js/custom_backend.js');
        $action = new shopCustomPluginBackendProductCategoriesAction();
        return array('edit_basics' => $action->display(false));
    }
    
    public function addCopyProductsButton() {
//        waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custom/js/custom_backend.js');
        
//        $action = new shopCustomPluginBackendProductCategoriesAction();
        return array('toolbar_section' => '<div class="block"><div class="copy-products" data-action="copy"><a href="#"><i class="icon16 folders"></i>Copy products</a></div></div>');
    }

    protected static function getThisPlugin() {
        if (self::$plugin) {
            return self::$plugin;
        } else {
            $info = array(
                'id' => 'custom',
                'app_id' => 'shop'
            );
            return new shopCustommenuPlugin($info);
        }
    }
    public static function getSameProducts() {
        $plugin = self::getThisPlugin();
        $product_model = new shopProductModel();
        $product = $product_model->getByField('url', waRequest::param('product_url'));
        if ( $product && $product['category_id']) {
            $collection = new shopProductsCollection('category/'.$product['category_id']);
            $same_products = $collection->getProducts('*',1000);
            if( $same_products ) {
                return $same_products;
            }
        }
        return false;
    }
    
    public static function getProductCategory() {
        $plugin = self::getThisPlugin();
        $product_model = new shopProductModel();
        $product = $product_model->getByField('url', waRequest::param('product_url'));
        if ( $product && $product['category_id']) {
            $category_model = new shopCategoryModel();
            $category = $category_model->getById($product['category_id']);
            return $category['name'];
        }
        return false;
    }
    
    public static function getReviewsCount($product_id) {
        $plugin = self::getThisPlugin();
        $reviews_model = new shopProductReviewsModel();
        return $reviews_model->count($product_id, false);
    }

    public static function getLastComments($limit = 2,$page = 0) {
        $reviews_model = new shopProductReviewsModel();
        $product_model = new shopProductModel();
        $options = array(
            'limit'=> $limit,
            'where' => array('parent_id' => 0,'status' => 'approved'),
            'offset' => intval($page) ? (intval($page) - 1) * $limit : 0
        );
        $parent_reviews = $reviews_model->getList('*,is_new,contact,product',$options);
        $count = $reviews_model->count();
        $pages_count = ceil((float)$count / $limit);
        foreach( $parent_reviews as &$review ) {
            $options = array(
                'limit'=> 1,
                'where' => array('parent_id' => $review['id'],'status' => 'approved')
            );
            $review['child_review'] = $reviews_model->getList('*,is_new,contact,product',$options);

            $review['product_data'] = $product_model->getById($review['product_id']);
        }
        $data = array(
            'parent_reviews' => $parent_reviews,
            'pages_count' => $pages_count
        );
        return $data;
    }
}