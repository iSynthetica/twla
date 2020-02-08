<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Synthetica
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div id="page" class="site">
        <header id="masthead" class="site-header" role="banner">
            <div class="title-bar" data-responsive-toggle="responsivehide" data-hide-for="medium">
                <div class="menu-icon-wrapper">
                    <button class="menu-icon" type="button" data-toggle></button>
                </div>
                <div class="title-bar-title">
                    <?php SNTH_Template::site_logo() ?>
                </div>
                <ul class="cart-icon-wrapper">
                    <li class="menu-item menu-item-main-menu menu-item-woo-cart">
                        <?= SNTH_Woo::get_cart_nav_menu_total() ?>
                    </li>
                </ul>
            </div>

            <nav id="site-navigation" class="main-navigation row column" role="navigation">
                <div class="top-bar" id="responsivehide">
                    <div class="top-bar-left">
                        <?php SNTH_Template::site_logo() ?>
                    </div>
                    <div class="top-bar-right">
                        <?php SNTH_Template::primary_menu_advanced(); ?>
                    </div>
                </div>
            </nav>
        </header><!-- #masthead -->

        <div id="content" class="site-content">