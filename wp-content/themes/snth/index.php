<?php
/**
 * The main template file
 *
 * @package Synthetica
 */
?>

<?php get_header(); ?>

<!-- Banner Section -->
<section id="banner"></section>

<!-- Blog Section -->
<section id="blog">
    <header class="section-header">
        <div class="column row">
            <?php SNTH_Template::section_title('blog') ?>
        </div>
    </header>
    <?php get_template_part('templates/parts/blog', 'content'); ?>
</section>

<!-- Testimonials Section -->
<section id="testimonials">
    <header class="section-header">
        <div class="column row">
            <?php SNTH_Template::section_title('testimonials') ?>
            <?php get_template_part('templates/parts/testimonials', 'header'); ?>
        </div>
    </header>
    <?php get_template_part('templates/parts/testimonials', 'content'); ?>
</section>

<!-- Products Section -->
<section id="shop">
    <header class="section-header">
        <div class="column row">
            <?php SNTH_Template::section_title('shop') ?>
        </div>
    </header>
    <?php get_template_part('templates/parts/shop', 'content'); ?>
</section>

<!-- Contacts Section -->
<section id="contact">
    <header class="section-header">
        <div class="column row">
            <?php SNTH_Template::section_title('contact') ?>
        </div>
    </header>
    <?php get_template_part('templates/parts/gallery', 'content'); ?>
</section>

<div class="reveal small" id="postModal" data-reveal>
    <div class="post-content">

    </div>
    <button class="close-button" data-close aria-label="Close modal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php get_footer(); ?>
