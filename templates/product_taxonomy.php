<?php 
/*
 * Products by category taxonomy
 */
get_header(); 
global $wp_query;
?>
<?php do_action('apptivo_ecommerce_after_header'); ?>
<?php do_action('apptivo_ecommerce_before_main_content'); ?>
<div id="content" role="main">

<?php do_action('apptivo_ecommerce_breadcrumb'); ?>

<?php $term = get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']); ?>
	
<h1 class="page-title"><?php echo wptexturize($term->name); ?></h1>
		
<?php echo wpautop(wptexturize($term->description)); ?>
	
<?php apptivo_ecommerce_get_template_part( 'loop', 'products' ); ?>

<?php do_action('apptivo_ecommerce_after_templates');  ?>

</div><!-- #content -->
<?php do_action('apptivo_ecommerce_after_main_content');  ?>
<?php do_action('apptivo_ecommerce_sidebar'); ?>
<?php get_footer(); ?>