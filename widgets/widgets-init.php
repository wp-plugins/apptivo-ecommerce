<?php
/**
 * Widgets init
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */ 
include_once('widget-cart.php');
include_once('widget-product_categories.php');
include_once('widget-product_tags.php');
include_once('widget-product_search.php');
include_once('widget-recent-products.php');

function apptivo_ecommerce_register_widgets() {
	register_widget('apptivo_ecommerce_Widget_Product_Categories');
	register_widget('apptivo_ecommerce_Widget_Product_Tags');
	register_widget('apptivo_ecommerce_Widget_Cart');
	register_widget('apptivo_ecommerce_Widget_Product_Search');	
	register_widget('apptivo_ecommerce_Widget_Recent_Products');		
}
add_action('widgets_init', 'apptivo_ecommerce_register_widgets');