<?php
if (! defined( 'ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (! class_exists('SNTH_Core')) {
    class SNTH_Core
    {
        /**
         * The single instance of the class.
         *
         * @var SNTH_Core
         */
        protected static $_instance = null;

        public static $sections = null;

        const ADD_TO_CART_NONCE = 'snth-add-to-cart';
        const ADD_TO_CART_NONCE_POST_ID = 'snth_add_to_cart_nonce';

        /**
         * Main SNTH_Child_Core Instance.
         *
         * @return SNTH_Core - Main instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * SNTH_Core Constructor.
         */
        private function __construct()
        {
            spl_autoload_register(array($this, 'autoload'));
            $this->set_sections();
            $this->actions();
        }

        public function autoload($class_name)
        {
            if (class_exists($class_name)) return;

            $class_path = SNTH_INC . DIRECTORY_SEPARATOR . 'class-' . strtolower(str_replace( '_', '-', $class_name )) . '.php';

            if ( file_exists( $class_path ) )  include $class_path;
        }

        /**
         * Set array of sections
         */
        private function set_sections()
        {
            self::$sections = array(
                'blog'  =>  __('Blog', 'snth'),
                'testimonials'  =>  __('Testimonials', 'snth'),
                'shop'  =>  __('Products', 'snth'),
                'contact'  =>  __('Contact', 'snth'),
            );
        }

        /**
         * Hook actions and filters functions
         */
        public function actions()
        {
            add_action('after_setup_theme', array($this, 'setup'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('customize_register', array($this, 'theme_customizer'));
            add_action('wp_head', array($this, 'theme_mod_css_output'));
            add_filter('acf/fields/google_map/api', array($this, 'google_map_api'));

            add_filter('post_gallery', array($this, 'my_gallery_output'), 10, 2);

            // AJAX
            add_action('wp_ajax_load-testimonials', array($this, 'ajax_load_testimonials'));
            add_action('wp_ajax_nopriv_load-testimonials', array($this, 'ajax_load_testimonials'));
            add_action('wp_ajax_load-post', array($this, 'ajax_load_post'));
            add_action('wp_ajax_nopriv_load-post', array($this, 'ajax_load_post'));
            add_action('wp_ajax_load-gallery', array($this, 'ajax_load_gallery'));
            add_action('wp_ajax_nopriv_load-gallery', array($this, 'ajax_load_gallery'));
        }

        /**
         * Change gallery shortcode output
         *
         * @param $output
         * @param $attr
         * @return string
         */
        public function my_gallery_output( $output, $attr ){
            $ids_arr = explode(',', $attr['ids']);
            $ids_arr = array_map('trim', $ids_arr );

            $pictures = get_posts( array(
                'posts_per_page' => -1,
                'post__in'       => $ids_arr,
                'post_type'      => 'attachment',
                'orderby'        => 'post__in',
            ) );

            if( ! $pictures ) return 'Запрос вернул пустой результат.';

            // Вывод
            $out = '<div id="allinone_carousel_charming">';
            $out .=     '<div class="myloader"></div>';
            $out .=     '<ul class="gallery_photos allinone_carousel_list">';

            // Выводим каждую картинку из галереи
            foreach( $pictures as $pic ){
                $src = $pic->guid;
                $t = esc_attr( $pic->post_title );
                $title = ( $t && false === strpos($src, $t)  ) ? $t : '';

                $out .= '<li class="gallery-item">' . wp_get_attachment_image($pic->ID, 'gallery') . '</li>';
            }

            $out .=     '</ul>';
            $out .= '</div>';

            return $out;
        }

        /**
         * Setup Theme
         */
        public function setup()
        {
            // Make theme available for translation.
            load_theme_textdomain('synthetica', get_template_directory() . '/languages');

            // Add default posts and comments RSS feed links to head.
            add_theme_support('automatic-feed-links');

            // Let WordPress manage the document title.
            add_theme_support('title-tag');

            // Enable support for Post Thumbnails on posts and pages.
            add_theme_support('post-thumbnails');
            add_image_size('blog', 600, 555, true);
            add_image_size('testimonials', 350, 350, true);
            add_image_size('shop-one', 375, 500, true);
            add_image_size('gallery', 600, 401, true);

            // Custom logo support
            add_theme_support('custom-logo', array(
                'height'      => 100,
                'width'       => 400,
                'flex-height' => true,
                'flex-width'  => true,
                'header-text' => array( 'site-title', 'site-description' ),
            ));

            /*
             * Switch default core markup for search form, comment form, and comments
             * to output valid HTML5.
             */
            add_theme_support('html5', array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ));

            // Add theme support for selective refresh for widgets.
            add_theme_support( 'customize-selective-refresh-widgets' );

            // Add woocommerce support
            add_theme_support( 'woocommerce' );

            // This theme uses wp_nav_menu() in one location.
            register_nav_menus( array(
                'primary' => esc_html__('Primary', 'snth'),
            ) );
        }

        /**
         * Enqueueing scripts
         */
        public function enqueue_scripts()
        {
            // Font Awesome
            wp_register_style('fontawesome', SNTH_VENDORS . '/font-awesome/css/font-awesome.css', array(), '4.7.0');

            // Google fonts
            wp_register_style('gf-montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700', false);
            wp_register_style('gf-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700', false);

            // Google Map
            wp_register_script('gmap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDBeGNjLt_srVFXjDjduGyHtGu-fzn_Pt4');
            wp_register_script('gmap-infobox', SNTH_VENDORS . '/googlemaps/infobox/infobox.js');

            // Slick
            wp_register_style('slick', SNTH_VENDORS . '/slick/slick.css', array(), '1.6.0');
            wp_register_script('slick', SNTH_VENDORS . '/slick/slick.min.js', array('jquery'), '1.6.0', true);

            // Select2
            wp_register_style('select2', SNTH_VENDORS . '/select2/css/select2.min.css', array(), '4.0.3');
            wp_register_script('select2', SNTH_VENDORS . '/select2/js/select2.min.js', array('jquery'), '4.0.3', true);

            // All in one carousel
            wp_register_style('carousel', SNTH_VENDORS . '/allinone-carousel/css/allinone_carousel.css', array(), '3.4.1');
            wp_register_script('touchSwipe', SNTH_VENDORS . '/allinone-carousel/js/jquery.touchSwipe.min.js', array('jquery'), '3.4.1', true);
            wp_register_script('carousel', SNTH_VENDORS . '/allinone-carousel/js/allinone_carousel.js', array(
                'jquery', 'touchSwipe', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-effects-transfer'
            ), '3.4.1', true);

            // Foundation
            wp_register_style('foundation', SNTH_VENDORS . '/foundation/css/foundation.css', array(), '6.3.1');
            wp_register_script('foundation', SNTH_VENDORS . '/foundation/js/foundation.js', array('jquery'), '6.3.1', true);

            // Custom scripts and styles
            wp_register_script('snth-gmap', SNTH_JS . '/gmap.js', array('jquery', 'gmap', 'gmap-infobox'), SNTH_VERSION, true);
            wp_register_script('snth-woo', SNTH_JS . '/woo.js', array('jquery'), SNTH_VERSION, true);
            wp_register_style('snth-app', SNTH_CSS . '/app.css', array(
                'foundation', 'fontawesome', 'slick', 'carousel', 'gf-montserrat', 'gf-open-sans', 'select2'
            ), SNTH_VERSION);
            wp_register_script('snth-app', SNTH_JS . '/app.js', array(
                'foundation', 'slick', 'carousel', 'snth-woo', 'snth-gmap', 'select2'
            ), SNTH_VERSION, true);

            wp_enqueue_style('snth-app');
            wp_enqueue_script('snth-app');

            wp_localize_script('snth-app', 'SnthObj', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce_post_id' => self::ADD_TO_CART_NONCE_POST_ID,
                'nonce' => wp_create_nonce( self::ADD_TO_CART_NONCE ),

                // checkout
                'ship_to_different_def' => apply_filters(
                    'woocommerce_ship_to_different_address_checked',
                    get_option( 'woocommerce_ship_to_destination' ) === 'shipping' ? 1 : 0
                ),
                'update_order_review_nonce' => wp_create_nonce( "update-order-review" ),
                'apply_coupon_nonce' => wp_create_nonce( "apply-coupon" ),
                'wc_ajaxurl' => WC()->ajax_url(),
                'ajax_loader_url' => apply_filters( 'woocommerce_ajax_loader_url', str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/images/ajax-loader@2x.gif' ),
            ));

            $galleries = SNTH_Lib::get_galleries_for_map();

            wp_localize_script('snth-gmap', 'SnthGmap', array(
                'galleries' =>  $galleries,
            ));
        }

        public function theme_customizer($wp_customize)
        {
            // Add One page settings section
            $wp_customize->add_section( 'snth_one_page_settings' , array(
                'title'      => __('One Page Settings', 'snth'),
                'priority'   => 30,
            ));

            // Banner Section
            $wp_customize->add_setting('snth_banner_thumb', array(
                'default'	=> '',
            ));
            $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'snth_banner_thumb', array (
                'label' => __('Banner image', 'theme_textdomain'),
                'section' => 'snth_one_page_settings',
                'mime_type' => 'image',
            )));

            foreach (self::$sections as $id => $section) {
                $wp_customize->add_setting('snth_'.$id.'_title', array(
                    'default'	=> $section,
                ));
                $wp_customize->add_control('snth_'.$id.'_title', array (
                    'label' => __($section . ' section title', 'snth'),
                    'type' => 'text',
                    'section' => 'snth_one_page_settings',
                ));
                $wp_customize->add_setting('snth_'.$id.'_thumb', array(
                    'default'	=> '',
                ));
                $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'snth_'.$id.'_thumb', array (
                    'label' => __($section . ' section header image', 'snth'),
                    'section' => 'snth_one_page_settings',
                    'mime_type' => 'image',
                )));
                if ('contact' === $id) {
                    $wp_customize->add_setting('snth_address1_text', array(
                        'default'	=> __('', 'snth'),
                    ));
                    $wp_customize->add_control('snth_address1_text', array (
                        'label' => __('Input your organization address', 'snth'),
                        'type' => 'text',
                        'section' => 'snth_one_page_settings',
                    ));
                    $wp_customize->add_setting('snth_address2_text', array(
                        'default'	=> __('', 'snth'),
                    ));
                    $wp_customize->add_control('snth_address2_text', array (
                        'type' => 'text',
                        'section' => 'snth_one_page_settings',
                    ));
                    $wp_customize->add_setting('snth_email_text', array(
                        'default'	=> __('', 'snth'),
                    ));
                    $wp_customize->add_control('snth_email_text', array (
                        'label' => __('Input your email address', 'snth'),
                        'type' => 'text',
                        'section' => 'snth_one_page_settings',
                    ));
                    $wp_customize->add_setting('snth_phone_text', array(
                        'default'	=> __('', 'snth'),
                    ));
                    $wp_customize->add_control('snth_phone_text', array (
                        'label' => __('Input your phone number', 'snth'),
                        'type' => 'text',
                        'section' => 'snth_one_page_settings',
                    ));
                    $wp_customize->add_setting('snth_view_gallery_text', array(
                        'default'	=> __('View', 'snth'),
                    ));
                    $wp_customize->add_control('snth_view_gallery_text', array (
                        'label' => __('View Gallery text', 'snth'),
                        'type' => 'text',
                        'section' => 'snth_one_page_settings',
                    ));
                }

            }
        }

        public function theme_mod_css_output()
        {
            $output = '<style type="text/css" id="theme-mod-css">';
            if (get_theme_mod('snth_banner_thumb')) {
                $output .= '
                #banner {
                    background: url(\'' .wp_get_attachment_url(get_theme_mod('snth_banner_thumb')). '\');
                    background-position: center;
                    background-size: cover;
                }
                ';
            } else {
                $output .= '#banner {display: none;}';
            }
            foreach (self::$sections as $id => $section) {
                if (get_theme_mod('snth_'.$id.'_thumb')) {
                    $output .= '
                    #'.$id.' .section-header {
                        padding: 80px 0;
                        background: url(\'' .wp_get_attachment_url(get_theme_mod('snth_'.$id.'_thumb')). '\');
                        background-position: center;
                        background-size: cover;
                    }
                    #'.$id.' .section-title {
                        color: #fff;
                        text-shadow: 0px 1px 3px #000;
                    }
                ';
                }
            }

            $output .=  '</style>';
            echo $output;
        }

        public function ajax_load_testimonials()
        {
            $char = sanitize_text_field($_POST['char']);

            $result['type'] = 'success';
            $result['html'] = SNTH_Lib::get_template_html('templates/parts/testimonials-carousel', '', array('char' => $char));

            $result = json_encode($result,  JSON_PRETTY_PRINT);

            echo $result;

            wp_die();
        }

        public function ajax_load_post()
        {
            $id = (int) sanitize_text_field($_POST['id']);
            $post_type = (string) sanitize_text_field($_POST['postType']);

            $result['type'] = 'success';
            $result['html'] = SNTH_Lib::get_template_html('templates/parts/post', 'content', array('id' => $id, 'post_type' => $post_type));

            echo json_encode($result,  JSON_PRETTY_PRINT);
            wp_die();
        }

        public function ajax_load_gallery()
        {
            $id = (int) sanitize_text_field($_POST['id']);

            $result['type'] = 'success';
            $result['html'] = SNTH_Lib::get_template_html('templates/parts/gallery', 'carousel', array('id' => $id));

            echo json_encode($result,  JSON_PRETTY_PRINT);
            wp_die();
        }

        public function google_map_api($api)
        {
            $api['key'] = 'AIzaSyDBeGNjLt_srVFXjDjduGyHtGu-fzn_Pt4';
            return $api;
        }
    }

    /**
     * Main instance of SNTH_Core.
     * @return SNTH_Core
     */
    function SNTH_Core() {
        return SNTH_Core::instance();
    }

    // Global for backwards compatibility.
    $GLOBALS['snth_core'] = SNTH_Core();
}
