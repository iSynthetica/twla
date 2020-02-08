<?php
global $post;

if ($results = SNTH_Lib::get_testimonials_by_first_char($char)) {
    echo '<div class="testimonials-carousel">';
    foreach ($results as $post) {
        setup_postdata ($post);
        ?>
        <div class="testimonial-item">
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>" data-id="<?php the_ID(); ?>">
                    <?php SNTH_Template::the_author_avatar(get_the_author_meta('ID'), 'testimonials') ?>
                    <h3 class="testimonial-title text-center text-uppercase"><?php the_title() ?></h3>
                </a>
            </article>
        </div>
        <?php
    }
    wp_reset_postdata();
    echo '</div>';
} else {
    ?>
    <div class="alert">No clubs found for that letter. Please try again, or use the search at the top.</div>
    <?php
}
?>