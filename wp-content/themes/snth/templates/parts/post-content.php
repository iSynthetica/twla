<?php
$id = $id ? $id : null;
$post_type = $post_type ? $post_type : 'post';

$args = array(
    'post_type'         => $post_type,
    'p'                 => $id,
    'posts_per_page'    => '1',
);

$post = new WP_Query($args);

if($post = new WP_Query($args)) {
    echo '<div class="post">';
    while ( $post->have_posts() ) : $post->the_post();
        ?>
        <article id="modal-post-<?php the_ID(); ?>" <?php post_class('modal-post'); ?>>
            <?php if ( has_post_thumbnail()) {
                the_post_thumbnail('gallery');
            } ?>
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <p class="entry-meta">
                <?= __('Published on: ', 'snth') ?> <?= get_the_date(); ?> <?= __(', by ', 'snth') ?> <?= get_the_author(); ?>
            </p>
            <?php the_content(); ?>
        </article>
        <?php
    endwhile;
    echo '</div>';
}
wp_reset_query();
?>