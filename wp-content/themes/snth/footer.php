<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Synthetica
 */

?>

    </div><!-- #content -->
    
    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="site-info row column">
            &copy; Copyrights <?= date('Y') ?> <a href="#">SpecialOne</a> all rights reserved
        </div><!-- .site-info -->
    </footer><!-- #colophon -->
    </div><!-- #page -->
    
    <?php wp_footer(); ?>

    <!-- Scroll Up - Start -->
    <div id="scroll_up" style="line-height: 34px"><i class="fa fa-chevron-up"></i></div>
    <!-- Scroll Up - End -->
    
</body>
</html>