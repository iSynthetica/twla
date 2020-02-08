<?php
if (! defined( 'ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (! class_exists('SNTH_Woo')) {
    class SNTH_Woo
    {
        /**
         * The single instance of the class.
         *
         * @var SNTH_Woo
         */
        protected static $_instance = null;
        protected static $_categories = null;

        /**
         * Main SNTH_Woo Instance.
         *
         * @return SNTH_Woo - Main instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * SNTH_Woo Constructor.
         */
        private function __construct()
        {
            $this->actions();
        }

        /**
         * Hook actions and filters functions
         */
        public function actions()
        {
            add_filter('wp_nav_menu_primary_items', array($this, 'cart_nav_menu_item'));
            add_filter('woocommerce_default_address_fields', array($this, 'override_default_checkout_fields'));
            add_filter('woocommerce_checkout_fields', array($this, 'override_checkout_fields'));
            add_filter('woocommerce_enqueue_styles', '__return_empty_array');

            // AJAX
            add_action('wp_ajax_load-products', array($this, 'ajax_load_products'));
            add_action('wp_ajax_nopriv_load-products', array($this, 'ajax_load_products'));
            add_action('wp_ajax_add-to-cart', array($this, 'ajax_add_to_cart'));
            add_action('wp_ajax_nopriv_add-to-cart', array($this, 'ajax_add_to_cart'));
            add_action('wp_ajax_remove-from-cart', array($this, 'ajax_remove_from_cart'));
            add_action('wp_ajax_nopriv_remove-from-cart', array($this, 'ajax_remove_from_cart'));
            add_action('wp_ajax_update-cart', array($this, 'ajax_update_cart'));
            add_action('wp_ajax_nopriv_update-cart', array($this, 'ajax_update_cart'));
            add_action('wp_ajax_update-checkout', array($this, 'ajax_update_checkout'));
            add_action('wp_ajax_nopriv_update-checkout', array($this, 'ajax_update_checkout'));
            add_action('wp_ajax_proceed-to-checkout', array($this, 'proceed_to_checkout'));
            add_action('wp_ajax_nopriv_proceed-to-checkout', array($this, 'proceed_to_checkout'));
        }

        /**
         * Add cart menu icon
         *
         * @param $items
         * @return string
         */
        public function cart_nav_menu_item($items)
        {
            $url = '<li class="menu-item menu-item-main-menu menu-item-woo-cart">';
            $url .= self::get_cart_nav_menu_total();
            $url .= '</li>';

            $items = $items . $url;
            return $items;
        }

        public static function get_cart_nav_menu_total()
        {
            global $woocommerce;
            $cart_url = $woocommerce->cart->get_cart_url();

            $cart_contents_count = $woocommerce->cart->cart_contents_count;
            $cart_contents = '('.$cart_contents_count.')';
            $cart_total = $woocommerce->cart->get_cart_total();

            if ($cart_contents_count == 0){
                $url = '<a><i class="fa fa-shopping-cart"></i>';
            } else {
                $url = '<a data-open="cartModal"><i class="fa fa-shopping-cart"></i>';
                $url .= '<sup>'.$cart_contents.'</sup>';
                //$url .= '<sup>'.$cart_contents.' - '. $cart_total.'</sup>';
            }
            $url .= '</a>';

            return $url;
        }

        /**
         * Remove useless default checkout fields
         *
         * @param $fields
         * @return mixed
         */
        public function override_default_checkout_fields($fields)
        {
            $fields['first_name']['placeholder'] = __('First name *', 'snth');
            $fields['last_name']['placeholder'] = __('Last name *', 'snth');
            $fields['city']['placeholder'] = __('Town / City *', 'snth');
            $fields['state']['placeholder'] = __('State / County *', 'snth');
            $fields['postcode']['placeholder'] = __('Postcode / ZIP *', 'snth');
            unset($fields['company']);
            return $fields;
        }

        /**
         * Remove useless checkout fields]
         *
         * @param $fields
         * @return mixed
         */
        public function override_checkout_fields($fields)
        {
            $fields['billing']['billing_email']['placeholder'] = __('Email address *', 'snth');
            $fields['billing']['billing_phone']['placeholder'] = __('Phone *', 'snth');

            $fields['billing']['billing_country']['class'] = array('form-row-first');
            $fields['billing']['billing_address_1']['class'] = array('form-row-last');
            $fields['billing']['billing_address_2']['class'] = array('form-row-first');
            $fields['billing']['billing_city']['class'] = array('form-row-last');
            $fields['billing']['billing_state']['class'] = array('form-row-first');
            $fields['billing']['billing_postcode']['class'] = array('form-row-last');

            $fields['shipping']['shipping_country']['class'] = array('form-row-first');
            $fields['shipping']['shipping_address_1']['class'] = array('form-row-last');
            $fields['shipping']['shipping_address_2']['class'] = array('form-row-first');
            $fields['shipping']['shipping_city']['class'] = array('form-row-last');
            $fields['shipping']['shipping_state']['class'] = array('form-row-first');
            $fields['shipping']['shipping_postcode']['class'] = array('form-row-last');

            unset($fields['order']['order_comments']);
            return $fields;
        }

        /**
         * Load Product list to carousel via Ajax
         */
        public function ajax_load_products()
        {
            $category = (int) sanitize_text_field($_POST['category']);

            $result['type'] = 'success';
            $result['html'] = SNTH_Lib::get_template_html('templates/parts/shop', 'carousel', array('category' => $category));

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            echo $result;

            wp_die();
        }

        /**
         * Ajax add to cart
         */
        public function ajax_add_to_cart()
        {
            global $woocommerce;

            $id = (int) sanitize_text_field($_POST['id']);

            if($woocommerce->cart->add_to_cart($id)) {
                $result['type'] = 'success';
                $result['miniCart'] = self::get_cart_nav_menu_total();
                $result['cart'] = do_shortcode('[woocommerce_cart]');
                $result['isEmpty'] = false;

                if(0 == $woocommerce->cart->cart_contents_count) {
                    $result['isEmpty'] = true;
                }
            } else {
                $result['type'] = 'error';
            }

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            echo $result;

            wp_die();
        }

        /**
         * Ajax remove from cart
         */
        public function ajax_remove_from_cart()
        {
            global $woocommerce;

            if($woocommerce->cart->set_quantity(filter_input(INPUT_POST, 'cart_item'), 0)) {

                $result['type'] = 'success';
                $result['miniCart'] = self::get_cart_nav_menu_total();
                $result['cart'] = do_shortcode('[woocommerce_cart]');
                $result['isEmpty'] = false;

                if(0 == $woocommerce->cart->cart_contents_count) {
                    $result['isEmpty'] = true;
                }
            } else {
                $result['type'] = 'error';
                //$result['miniCart'] = self::get_cart_nav_menu_total();
                //$result['cart'] = do_shortcode('[woocommerce_cart]');
            }

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            echo $result;

            wp_die();
        }

        /**
         * Ajax update cart
         */
        public function ajax_update_cart()
        {
            if (!defined( 'WOOCOMMERCE_CART')) {
                define('WOOCOMMERCE_CART', true);
            }

            $this->update_cart(filter_input(INPUT_POST, 'cart', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY));

            global $woocommerce;

            $result['type'] = 'success';
            $result['miniCart'] = self::get_cart_nav_menu_total();
            $result['cart'] = do_shortcode('[woocommerce_cart]');
            $result['isEmpty'] = false;

            if(0 == $woocommerce->cart->cart_contents_count) {
                $result['isEmpty'] = true;
            }

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            echo $result;

            wp_die();
        }

        public function update_cart($cart_totals)
        {
            global $woocommerce;

            if (sizeof( $woocommerce->cart->get_cart() ) > 0) {
                foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {

                    // Skip product if no updated quantity was posted
                    if (!isset( $cart_totals[ $cart_item_key ]['qty'])) {
                        continue;
                    }

                    // Sanitize
                    $quantity = apply_filters(
                        'woocommerce_stock_amount_cart_item',
                        apply_filters(
                            'woocommerce_stock_amount',
                            preg_replace( "/[^0-9\.]/", "", $cart_totals[ $cart_item_key ]['qty'] )
                        ),
                        $cart_item_key
                    );

                    // Update cart validation
                    $passed_validation 	= true;//apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );
                    $_product = $values['data'];

                    // is_sold_individually
                    if ($_product->is_sold_individually() && $quantity > 1) {
                        $woocommerce->add_error( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_title() ) );
                        $passed_validation = false;
                    }

                    if ($passed_validation) {
                        $woocommerce->cart->set_quantity( $cart_item_key, $quantity, false );
                    }

                    $woocommerce->cart->calculate_totals();
                }
            }
        }

        /**
         * Ajax update checkout
         */
        public function ajax_update_checkout()
        {
            if (!defined( 'WOOCOMMERCE_CHECKOUT')) {
                define( 'WOOCOMMERCE_CHECKOUT', true );
            }

            // generate and calculate shipping items
            global $woocommerce;
            $woocommerce->cart->calculate_totals();
            $woocommerce->cart->calculate_shipping();

            $result['type'] = 'success';
            $result['checkout'] = do_shortcode('[woocommerce_checkout]');
            $result['isEmpty'] = false;

            if(0 == $woocommerce->cart->cart_contents_count) {
                $result['isEmpty'] = true;
            }

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            echo $result;

            wp_die();
        }

        public function proceed_to_checkout()
        {
            global $woocommerce;

            $result['type'] = 'success';
            $result['checkout'] = do_shortcode('[woocommerce_checkout]');
            $result['session'] = $woocommerce->session;

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
                define( 'WOOCOMMERCE_CHECKOUT', true );
            }

            echo $result;

            wp_die();
        }
        
        public static function get_categories()
        {
            $args = array(
                'type'         => 'product',
                'hide_empty'   => 1,
                'number'       => 0,
                'taxonomy'     => 'product_cat',
            );

            return get_categories($args);
        }

        public static function get_first_category()
        {
            $args = array(
                'type'         => 'product',
                'hide_empty'   => 1,
                'number'       => 1,
                'taxonomy'     => 'product_cat',
            );

            return get_categories($args)[0];
        }
        
        public static function get_products_by_category($category)
        {
            $args = array(
                'post_type'             => 'product',
                'post_status'           => 'publish',
                'posts_per_page'        => '-1',
            );

            if (is_int($category)) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $category
                    ));
            } elseif (is_object($category)) {
                $args['product_cat'] = $category->slug;
            }

            return $products = new WP_Query($args);
        }
    }

    /**
     * Main instance of SNTH_Woo.
     * @return SNTH_Woo
     */
    function SNTH_Woo() {
        return SNTH_Woo::instance();
    }

    // Global for backwards compatibility.
    $GLOBALS['snth_woo'] = SNTH_Woo();
}
