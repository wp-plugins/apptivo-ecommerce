<?php
/**
 * Products Tags ( Taxonomy ) 
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
get_header(); ?>
<?php do_action('apptivo_ecommerce_after_header'); ?>
<?php do_action('apptivo_ecommerce_before_main_content'); ?>
<div id="content" role="main">

<?php do_action('apptivo_ecommerce_breadcrumb'); ?>

<?php 
global $wp_query;
$term = get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']); ?>
	
<h1 class="page-title"><?php echo wptexturize($term->name); ?></h1>
		
<?php echo wpautop(wptexturize($term->description)); ?>
<?php 

global $apptivo_ecommerce_loop;
$apptivo_ecommerce_loop['item_tag_url'] = get_term_link($term->slug, 'item_tag');
$apptivo_ecommerce_loop['paged'] =  get_query_var('page');
$apptivo_ecommerce_loop['pagination_type'] =  get_option('apptivo_ecommerce_products_pagination_type');

     $args = array(
		'post_type'	=> 'item',
	    'paged'          => $apptivo_ecommerce_loop['paged'],	
		'post_status' => 'publish',
		'posts_per_page' => get_option('apptivo_ecommerce_products_per_page'),
		'orderby' => 'date',
		'order' => 'asc',
	    'meta_query' => array(
			array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
		)
	    );
	    
	    $args['tax_query'] = array(
				    array(
				        'taxonomy' => 'item_tag',
				        'terms' => array($term->term_id),
				        'field' => 'id'
				    ));
		
        query_posts($args);
	    apptivo_ecommerce_get_template_part( 'tags', 'products');
	    wp_reset_query(); 
?>

<?php do_action('apptivo_ecommerce_after_templates');  ?>

</div>
<?php do_action('apptivo_ecommerce_after_main_content');  ?>
<?php do_action('apptivo_ecommerce_sidebar'); ?>
<?php get_footer(); ?>