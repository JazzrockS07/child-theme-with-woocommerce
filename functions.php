<?php

/*
 *  start sessions
 */

add_action('init', 'start_session', 1);

function start_session() {
    if(!session_id()) {
        session_start();
    }
}


/*
 * add themes and child themes styles
 */

function true_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
}

add_action( 'wp_enqueue_scripts', 'true_enqueue_styles' );

/*
 * add my style and js to child theme
 */

add_action('wp_enqueue_scripts','my_scripts');

function my_scripts() {
    wp_enqueue_style('main',get_stylesheet_directory_uri(). '/style/my_style.css',false,null);
    wp_enqueue_script('myscript',get_stylesheet_directory_uri(). '/js/my_scripts_v1.js', 'jquery', null, true);
}


/*
 *  add meta field sale-date with date of sale to product after order completed
 */

function custom_fields_sale_date($order_id) {
    $order = new WC_Order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {
        $product_id = $item['product_id'];
        if(!update_post_meta ($product_id,'sale-date',date('Y-m-d'))) {
            add_post_meta($product_id, 'sale-date',date('Y-m-d') , true);
        }
    }
}

add_action ('woocommerce_order_status_completed','custom_fields_sale_date',15);

/*
 * add meta field views to category page with products
 */

function add_product_views_to_cat() {
    global $post;
    $product_id = (int)$post->ID;
    if (get_post_meta ($product_id,'views',true)) {
        ?>
        <div class="woocommerce-product-details__views">
            Количество просмотров: <?php echo (int)get_post_meta ($product_id,'views',true); ?>
        </div>
        <?php
    }
}

add_action( 'woocommerce_shop_loop_item_title','add_product_views_to_cat',15 );