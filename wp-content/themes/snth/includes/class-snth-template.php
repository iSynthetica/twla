<?php

if (!class_exists('SNTH_Template')) {
    /**
     * Class SNTH_Template
     */
    class SNTH_Template
    {
        /**
         * Display Primary menu template
         */
        public static function primary_menu_advanced()
        {
            wp_nav_menu( array( 
                'theme_location' => 'primary',
                'container' => false,
                'depth' => 0,
                'items_wrap' => '<ul class="vertical medium-horizontal menu" data-responsive-menu="drilldown medium-dropdown">%3$s</ul>',
                'walker' => new Foundation_Nav_Walker( array(
                    'in_top_bar' => true,
                    'item_type' => 'li',
                    'menu_type' => 'main-menu' )
                ),
            ));
        }

        /**
         * Display Site logo
         */
        public static function site_logo()
        {
            if ( function_exists( 'the_custom_logo' ) ) {
                the_custom_logo();
            }
        }

        /**
         * Output section header
         *
         * @param $section
         */
        public static function section_header($section)
        {
            $sections_array = array(
                'blog'  =>  __('Blog', 'snth'),
                'testimonials'  =>  __('Testimonials', 'snth'),
                'shop'  =>  __('Products', 'snth'),
                'contact'  =>  __('Contact', 'snth'),
            );

            $output = '<h2 class="text-center section-title">' . get_theme_mod('snth_' . $section . '_title', $sections_array[$section]) . '</h2>';

            echo $output;
        }

        /**
         * Output section title
         *
         * @param $section
         */
        public static function section_title($section)
        {
            $sections_array = array(
                'blog'  =>  __('Blog', 'snth'),
                'testimonials'  =>  __('Testimonials', 'snth'),
                'shop'  =>  __('Products', 'snth'),
                'contact'  =>  __('Contact', 'snth'),
            );

            $output = '<h2 class="text-center section-title">' . get_theme_mod('snth_' . $section . '_title', $sections_array[$section]) . '</h2>';

            echo $output;
        }

        /**
         * Output Category image
         * 
         * @param $category
         * @param string $size
         */
        public static function the_category_thumbnail($category, $size = '')
        {
            echo wp_get_attachment_image(get_field('category_thumbnail', 'category_'.$category), $size);
        }

        /**
         * Output Category image
         *
         * @param $category
         * @param string $size
         */
        public static function the_author_avatar($author, $size = '')
        {
            echo wp_get_attachment_image(get_field('avatar', 'user_'.$author), $size);
        }
    }
}