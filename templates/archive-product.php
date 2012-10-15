<?php
/*
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
get_header(); ?>
<?php do_action('apptivo_ecommerce_after_header'); ?>
<?php do_action('apptivo_ecommerce_before_main_content'); ?>
<div id="content" role="main">	  
<?php do_action('apptivo_ecommerce_breadcrumb'); ?><!-- Breadcrumb -->
	<?php 
		$products_page_id = get_option('apptivo_ecommerce_products_page_id');
		$products_page = get_post($products_page_id);
		$products_page_title = (get_option('apptivo_ecommerce_products_page_title')) ? get_option('apptivo_ecommerce_products_page_title') : $products_page->post_title;
	?>
	<?php if (is_search()) : ?>		
		<h1 class="page-title"><?php _e( apply_filters('apptivo_ecommerce_search_results_label','Search Results:'), 'apptivo_ecommerce'); ?> &ldquo;<?php the_search_query(); ?>&rdquo; <?php if (get_query_var('paged')) echo ' &mdash; Page '.get_query_var('paged'); ?></h1>
	<?php else : ?>
	    <input type="hidden" id="page-item-products" name="page-item-products" value="<?php echo get_option('apptivo_ecommerce_products_page_id'); ?>" />
		<h1 class="page-title"><?php echo apply_filters('the_title', $products_page_title); ?></h1>
	<?php endif; ?>	
	<?php echo apply_filters('the_content', $products_page->post_content); ?>
<?php  apptivo_ecommerce_get_template_part( 'loop', 'products' ); ?>	
<?php do_action('apptivo_ecommerce_after_templates');  ?>
</div><!-- #content -->
<?php do_action('apptivo_ecommerce_after_main_content');  ?>
<?php do_action('apptivo_ecommerce_sidebar'); ?><!-- Sidebar -->
<?php get_footer(); ?>