<div class="row section-topbar shop-topbar">
    <div class="medium-8 medium-offset-2 columns text-center">
        <?php SNTH_Lib::get_template('templates/parts/shop', 'navigation'); ?>
    </div>
    <div class="medium-2 columns carousel-nav text-right">
        <span class="prev-link text-center">
            <i class="fa fa-angle-left" aria-hidden="true"></i>
        </span><span class="nav-divider"></span>
        <span class="next-link text-center">
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </span>
    </div>
</div>

<div class="row column section-content">
    <?php
    $category = SNTH_Woo::get_first_category();
    SNTH_Lib::get_template('templates/parts/shop-carousel', '', array('category' => $category));
    ?>
</div>

<div class="reveal large empty-cart" id="cartModal" data-reveal>
    <h1 class="cart-header" id="cart-header">
        <?php _e( 'Cart', 'woocommerce' ); ?>
    </h1>
    <div class="cart-content" id="cart-content" style="margin-bottom: 35px">
        <?php echo do_shortcode('[woocommerce_cart]'); ?>
    </div>
    <h1 class="checkout-header" id="checkout-header">
        <?php _e( 'Checkout', 'woocommerce' ); ?>
    </h1>
    <div class="checkout-content" id="checkout-content">
        <?php echo do_shortcode('[woocommerce_checkout]'); ?>
    </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
    