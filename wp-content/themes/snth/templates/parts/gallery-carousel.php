<?php
$id = $id ? $id : null;
$last = $last ? $last : false;

if($gallery = SNTH_Lib::get_galleries($id, $last)) {
    echo '<div class="gallery">';
    while ( $gallery->have_posts() ) : $gallery->the_post();
        ?>
        <?php the_content(); ?>
        <?php
    endwhile;
    echo '</div>';
}
wp_reset_query();
?>
