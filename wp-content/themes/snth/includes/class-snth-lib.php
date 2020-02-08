<?php

if (!class_exists('SNTH_Lib')) {
    /**
     * Class SNTH_Lib
     */
    class SNTH_Lib
    {
        /**
         * Display Primary menu template
         */
        public static function get_testimonials_by_first_char($char)
        {
            global $wpdb;

            $results = $wpdb->get_results("
                SELECT * FROM $wpdb->posts
                WHERE post_title LIKE '$char%'
                AND post_type = 'testimonial'
                AND post_status = 'publish';
            ");

            return $results;
        }

        /**
         * Get attachment id by url
         *
         * @param $url
         * @return int
         */
        public static function get_attachment_id( $url ) {
            $attachment_id = 0;
            $dir = wp_upload_dir();
            if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
                $file = basename( $url );
                $query_args = array(
                    'post_type'   => 'attachment',
                    'post_status' => 'inherit',
                    'fields'      => 'ids',
                    'meta_query'  => array(
                        array(
                            'value'   => $file,
                            'compare' => 'LIKE',
                            'key'     => '_wp_attachment_metadata',
                        ),
                    )
                );
                $query = new WP_Query( $query_args );
                if ( $query->have_posts() ) {
                    foreach ( $query->posts as $post_id ) {
                        $meta = wp_get_attachment_metadata( $post_id );
                        $original_file       = basename( $meta['file'] );
                        $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
                        if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
                            $attachment_id = $post_id;
                            break;
                        }
                    }
                }
            }
            return $attachment_id;
        }

        /**
         * Get Galleries from DB
         *
         * @param null $id
         * @param bool $last
         * @return WP_Query
         */
        public static function get_galleries($id = null, $last = false)
        {
            $args = array(
                'post_type'             => 'gallery',
                'post_status'           => 'publish',
            );

            if (is_int($id)) {
                $args['p'] = $id;
                $args['posts_per_page'] = '1';
            } elseif($last) {
                $args['posts_per_page'] = '1';
            } else {
                $args['posts_per_page'] = '-1';
            }

            return $gallery = new WP_Query($args);
        }

        public static function get_galleries_for_map()
        {
            $galleries = self::get_galleries();
            $galleries_array = array();
            if ($galleries) {

                while ( $galleries->have_posts() ) : $galleries->the_post();
                    $id = $galleries->post->ID;
                    $location = get_field('shot_location', $id);
                    $address = $location['address'];
                    $address = self::get_formatted_address($address);
                    $permalink = get_the_permalink();
                    $info  = '<div class="info-box-wrapper">';
                    $info .= $address . '<br>';
                    //$info .= get_the_title() . '<br>';
                    $info .=  '<span class="gallery-link" data-gallery=' . $id . '>' . get_theme_mod('snth_view_gallery_text', __('View', 'snth')) . '</span>';
                    $info  .= '</div>';
                    $galleries_array[] = array(
                        'location' => $location,
                        'info' => $info,
                    );
                endwhile;
            }
            wp_reset_query();
            return $galleries_array;
        }

        public static function get_formatted_address($address)
        {
            $address = array_reverse(explode(', ', $address));
            $address = implode('<br>', $address);
            return $address;
        }

        /**
         * Get templates passing attributes and including the file.
         *
         * @param string $template_name
         * @param array $args
         * @param string $template_path
         */
        public static function get_template($slug, $name = null, $args = array())
        {
            if (!empty($args) && is_array($args)) {
                extract($args);
            }

            $name = (string) $name;
            if ( '' !== $name )
                $templates[] = "{$slug}-{$name}.php";

            $templates[] = "{$slug}.php";

            $located = locate_template($templates);

            if (!file_exists($located)) {
                return;
            }

            include($located);
        }

        /**
         * Like get_template, but returns the HTML instead of outputting.
         *
         * @param $template_name
         * @param array $args
         * @param string $template_path
         * @return string
         */
        public static function get_template_html($slug, $name = null, $args = array())
        {
            ob_start();
            self::get_template($slug, $name, $args);
            return ob_get_clean();
        }
    }
}