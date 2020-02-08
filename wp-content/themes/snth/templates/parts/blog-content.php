                                                                            <?php
//for each category, show all posts
$categories = get_categories(array(
    'orderby' => 'name',
    'number'       => 2,
    'order' => 'ASC'
));

foreach ($categories as $category) {
    $posts = get_posts(array(
        'showposts' => 2,
        'category__in' => array($category->term_id),
        'ignore_sticky_posts' => 1
    ));
    if ($posts) {
        ?>
        <div class="row category-row">
            <div class="large-6 columns category-column">
                <?php
                SNTH_Template::the_category_thumbnail($category->term_id, 'blog');
                echo '<h3 class="category-title">' . $category->name.'</h3> ';
                ?>
            </div>

            <div class="large-6 columns posts-column">
                <?php
                $i = 1;
                foreach($posts as $post) {
                    ?>
                    <div class="row<?= $class = 1 === $i ? ' odd' : ' even' ?>" data-equalizer data-equalize-on="medium">
                        <?php
                        setup_postdata($post); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                            <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>" data-id="<?php the_ID(); ?>">
                                <div class="medium-6 columns<?= $class = 1 === $i ? ' medium-push-6 columns' : '' ?> thumb-column" data-equalizer-watch>
                                    <?php the_post_thumbnail('blog'); ?>
                                </div>

                                <div class="medium-6 columns<?= $class = 1 === $i ? ' medium-pull-6 columns' : '' ?> title-column" data-equalizer-watch>
                                    <div class="entry-title-wrapper">
                                        <h3 class="entry-title"><?php the_title(); ?></h3>
                                        <p class="entry-meta"><?= get_the_date(); ?></p>
                                    </div>
                                </div>
                            </a>
                        </article>
                        <?php
                        ?>
                    </div>
                    <?php
                    ++$i;
                } // foreach($posts
                ?>
            </div>
        </div>
        <?php
    } // if ($posts
} // foreach($categories
?>