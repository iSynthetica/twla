<?php
if($products = SNTH_Woo::get_products_by_category($category)) {
    echo '<div id="shop-items" class="shop-carousel">';
    while ( $products->have_posts() ) : $products->the_post();
        global $product;
        $id = $products->post->ID;
        ?>
        <div class="shop-item">
            <div class="shop-item-inner">
                <?php
                if (has_post_thumbnail($id)) {
                    ?>
                    <div class="shop-item-thumb">
                        <?= get_the_post_thumbnail($id, 'shop-one'); ?>
                        <div class="shop-item-meta">
                            <span data-tooltip aria-haspopup="true" class="has-tip top add-to-cart" data-id="<?= $id ?>" data-disable-hover="false" tabindex="1" title="Add to cart">
                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            </span>
                            <span data-tooltip aria-haspopup="true" class="has-tip top add-to-wishlist" data-disable-hover="false" tabindex="1" title="Add to wishlist">
                                <i class="fa fa-heart-o" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <h3 class="shop-title text-center text-uppercase"><?php the_title(); ?></h3>
                <div class="price text-center"><?php echo $product->get_price_html(); ?></div>
            </div>
        </div>
        <?php
    endwhile;
    echo '</div>';
}
wp_reset_query();
?>