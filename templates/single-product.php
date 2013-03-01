<?php get_header(); ?>
<?php do_action('apptivo_ecommerce_after_header'); ?>
<?php do_action('apptivo_ecommerce_before_main_content');  ?>
<div id="content" role="main">	

<?php global $apptivo_ecommerce;  $apptivo_ecommerce->show_messages(); ?>
  
<?php do_action('apptivo_ecommerce_breadcrumb'); ?>

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
	global $_product,$post; 
	$_product = &new apptivo_ecommerce_product( $post->ID ); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php do_action('apptivo_ecommerce_show_product_images', $post, $_product); ?>
			<div class="summary">
				<h1 class="product_title page-title"><?php the_title(); ?></h1>
				<?php do_action( 'apptivo_ecommerce_single_product_summary', $post, $_product ); ?>
			</div>
		</div>
		<?php do_action('apptivo_ecommerce_productdescription', $post, $_product); ?>
	<?php endwhile; ?>

<?php do_action('apptivo_ecommerce_after_description_content'); ?>

<?php do_action('apptivo_ecommerce_brfore_related_products');  ?>

<!--  Related Products Start -->
<?php

if( get_option('apptivo_ecommerce_enable_related_products') == 'yes' )
{
    $tags = wp_get_post_terms($post->ID,'item_tag');

	if ($tags) 
	{
		$tag_ids = array();
		foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
	
		$args=array(
		    'post_type' => 'item',
			'post__not_in' => array($post->ID),
			'post_type'	=> 'item',	    
			'post_status' => 'publish',
			'posts_per_page' => 4,
			'orderby' => 'date',
			'order' => 'title',
		
		);
		
		$args['tax_query'] = array(
					    array(
					        'taxonomy' => 'item_tag',
					        'terms' => $tag_ids,
					        'field' => 'id'
					    ));
		 $related_posts = query_posts($args);
		 if( !empty($related_posts))
		 {
	     echo apply_filters('apptivo_ecommerce_related_products_label','<div class="related_products_label"><h2>Related Products</h2></div><br />');
	     
	     do_action('apptivo_ecommerce_after_relatedproducts');
	     	 	
	     echo '<ul class="items" id="item_lists">';
		 $apptivo_ecommerce_loop['columns'] = apply_filters('loop_products_columns', 4);
		 $apptivo_ecommerce_loop['loop'] = 0;
		 if ( have_posts()) : while (have_posts()) : the_post(); 
		 $post_id = get_the_ID();
	  	$apptivo_ecommerce_loop['loop']++;
			
			?>
			<li class="product <?php if ($apptivo_ecommerce_loop['loop']%$apptivo_ecommerce_loop['columns']==0) echo ' last'; if (($apptivo_ecommerce_loop['loop']-1)%$apptivo_ecommerce_loop['columns']==0) echo ' first'; ?>">
				
				<!-- Item Title and thumbnail Image -->
				<div class="apptivo_product_image">
			<a href="<?php the_permalink(); ?>"><?php echo get_product_thumbnail($post_id); ?></a>
			 </div>
				<!-- Item Sale Price -->
				<?php $sale_price = get_post_meta($post_id, '_apptivo_sale_price', true); ?>
				<span class="price">
				      <?php if($sale_price == '') { $sale_price = '0.00'; }?>
					   <?php echo apptivo_ecommerce_price($sale_price);?>				   
				</span>
				<div class="item_name"><a title="<?php echo get_the_title($post_id); ?>" href="<?php echo get_permalink($post_id); ?>"><?php echo apptivo_ecommerce_itemname(trim(get_the_title($post_id)));?></a></div>
				<!--  Add to cart and More details button -->
				<?php
				apptivo_ecommerce_viewdetails_addtocart_btn($post_id,$sale_price);
				?>				
				<?php 
			
		endwhile; endif;
		 }
		echo '</ul>';
		
		do_action('apptivo_ecommerce_after_relatedproducts');
		
		echo '<div class="clear"></div>';
		
		wp_reset_query();
		
	}
}

?>
<!--  Related Products End -->	

<?php do_action('apptivo_ecommerce_after_related_products');  ?>

</div>
<?php do_action('apptivo_ecommerce_after_main_content');  ?>

<?php  do_action('apptivo_ecommerce_sidebar'); ?>

<?php get_footer(); ?>