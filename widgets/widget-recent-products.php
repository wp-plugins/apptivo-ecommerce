<?php
/**
 * Recent Products and Features Products.
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
global $apptivo_recent_products;

$recent_products_defaults = array(
    'title' => 'Recent Products',
    'posts_number' => '5',
    'order_by' => 'none',
    'display_title' => 'true',
    'display_content' => 'true',
    'display_featured_image' => 'true',
    'display_read_more' => 'true',
    'display_featured_products' => 'no',
    'content_type' => 'the_excerpt',
    'excerpt_length' => '26',
    'featured_image_width' => '90',
    'featured_image_height' => '60',
    'featured_image_align' => 'alignleft',
    'filter' => 'recent',
    'filter_cats' => ''    
);

$apptivo_recent_products->options['widgets_options']['Products'] = is_array($apptivo_recent_products->options['widgets_options']['Products'])
    ? array_merge($recent_products_defaults, $apptivo_recent_products->options['widgets_options']['Products'])
    : $recent_products_defaults;
        

class apptivo_ecommerce_Widget_Recent_Products extends WP_Widget 
{
    function __construct() 
    {
        $widget_options = array('description' => __('Displaying the recent products or Products from the selected categories.', 'themater') );
        $control_options = array( 'width' => 500);
		$this->WP_Widget('apptivo_recent_products','[Apptivo] Recent Products', $widget_options, $control_options);
    }

    function widget($args, $instance)
    {
    	if( !($instance['display_title'])  && !($instance['display_content']) )
    	{
    		$instance['display_title'] = true;
    	}    	
        global $apptivo_recent_products;
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $post_args = array();       
 
        if ( !empty($instance['title']) ) $title = $instance['title']; else $title = __('Recent Products', 'apptivo_ecommerce');
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		
                switch ($instance['order_by']) {
                    case 'none' :
                    	 $post_args = ''; break;
                    case 'id_asc' :
                    	 $post_args['orderby'] = 'ID';
						 $post_args['order'] = 'ASC';
                    	 break;
                    case 'id_desc' :
                    	  $post_args['orderby'] = 'ID';
						  $post_args['order'] = 'DESC';
                    	  break;
                    case 'date_asc' :
                    	 $post_args['orderby'] = 'date';
						 $post_args['order'] = 'ASC';
                    	 break;
                    case 'date_desc' :
                    	 $post_args['orderby'] = 'date';
						 $post_args['order'] = 'DESC';
                    	 break;                    	
                    case 'title_asc' :
                    	  $post_args['orderby'] = 'title';
						 $post_args['order'] = 'ASC';
                    	 break;
                    case 'title_desc' :
                    	 $post_args['orderby'] = 'title';
						 $post_args['order'] = 'DESC';
                    	 break; 
                    default : 
                    	 $post_args['orderby'] = $instance['order_by'];                    	
                    
                }
                switch ($instance['filter']) {
                    case 'cats' :
					$post_args['tax_query'] = array(
				    array(
				        'taxonomy' => 'item_cat',
				        'terms' => explode(',',(trim($instance['filter_cats']))),
				        'field' => 'id',
				    ));
                    break;
                    case 'category' :
                    $post_args['tax_query'] = array(
				    array(
				        'taxonomy' => 'item_cat',
				        'terms' => array(trim($instance['selected_category'])),
				        'field' => 'id',
				    ));
                    break;
                    default : $filter_query = '';
                }
				$post_args['post_type'] = 'item';
				$post_args['posts_per_page']=$instance['posts_number'];
				if($instance['display_featured_products'] == 'yes')
				{
					$post_args['meta_query'] = array(
					array('key' => '_apptivo_featured','value' => 'yes','compare' => '=','type' => 'CHAR'),
					array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
					);
				}else{
					$post_args['meta_query'] = array(
					array('key' => '_apptivo_enabled','value' => 'yes','compare' => '=','type' => 'CHAR')
					);
				}		
				query_posts($post_args);
        	
	if ( have_posts() ) :
       echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
    ?>
        <ul class="widget-container Products-widget">
        	<?php
				while ( have_posts() ) : the_post(); ?>
                    <li class="clearfix">
                        <?php if ($instance['display_featured_image'] && has_post_thumbnail() ) { ?><a href="<?php the_permalink(); ?>"><?php the_title(). the_post_thumbnail(array($instance['featured_image_width'],$instance['featured_image_height']), array("title" =>  get_the_title(),"class" => "Products-widget-featured-image " . $instance['featured_image_align'])); ?></a> <?php } ?>
                        <?php if ( $instance['display_title'] ) { ?> <h3 class="Products-widgettitle">
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3><?php } ?>
                        <?php
                            
                            if($instance['display_content'] || $instance['display_read_more']) {
                                ?><div class="Products-widget-entry"><?php 
                                    if($instance['display_content'] ) {
                                        if($instance['content_type'] == 'the_content') {
                                            the_content("");
                                        } else {
                                            $get_the_excerpt_length = $instance['excerpt_length'] ? $instance['excerpt_length'] : 16;
                                            echo apptivo_shorten(get_the_excerpt(), $get_the_excerpt_length);                                           
                                        }
                                    }
                                    
                                    if($instance['display_read_more']) {
                                        ?> <a class="Products-widget-more" href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permalink to ', 'themater' ); the_title_attribute(); ?>"><?php _e('Read More &raquo;','themater'); ?></a><?php 
                                    }?>
                                </div><?php
                            }
                        ?>
                    </li>
                <?php
                endwhile;
               
                ?>
            </ul>
      
        <?php
        echo $after_widget;
        
      endif;
   wp_reset_query();
            
    }

    function update($new_instance, $old_instance) 
    {				
    	$instance = $old_instance;
    	$instance['title'] = strip_tags($new_instance['title']);
        $instance['posts_number'] = strip_tags($new_instance['posts_number']);
        $instance['order_by'] = strip_tags($new_instance['order_by']);
        $instance['display_title'] = strip_tags($new_instance['display_title']);
        $instance['content_type'] = strip_tags($new_instance['content_type']);
        $instance['display_content'] = strip_tags($new_instance['display_content']);
        $instance['display_featured_image'] = strip_tags($new_instance['display_featured_image']);
        $instance['display_read_more'] = strip_tags($new_instance['display_read_more']);
        $instance['display_featured_products'] = strip_tags($new_instance['display_featured_products']);
        $instance['excerpt_length'] = strip_tags($new_instance['excerpt_length']);
        $instance['featured_image_width'] = strip_tags($new_instance['featured_image_width']);
        $instance['featured_image_height'] = strip_tags($new_instance['featured_image_height']);
        $instance['featured_image_align'] = strip_tags($new_instance['featured_image_align']);
        $instance['filter'] = strip_tags($new_instance['filter']);
        $instance['filter_cats'] = strip_tags($new_instance['filter_cats']);
        $instance['selected_category'] = strip_tags($new_instance['selected_category']);
        return $instance;
    }
    
    function form($instance) 
    {	
        global $apptivo_recent_products;
		$instance = wp_parse_args( (array) $instance, $apptivo_recent_products->options['widgets_options']['Products'] );        
        ?>
        <div class="tt-widget">
            <table width="100%">
                <tr>
                    <td class="tt-widget-label" width="25%"><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label></td>
                    <td class="tt-widget-content" width="75%"><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" /></td>
                </tr>
                
                <tr>
                    <td class="tt-widget-label"><label for="<?php echo $this->get_field_id('posts_number'); ?>">Number Of Products:</label></td>
                    <td class="tt-widget-content"><input class="widefat" id="<?php echo $this->get_field_id('posts_number'); ?>" name="<?php echo $this->get_field_name('posts_number'); ?>" type="text" value="<?php echo esc_attr($instance['posts_number']); ?>" /></td>
                </tr>
                
                <tr>
                    <td class="tt-widget-label"><label for="<?php echo $this->get_field_id('order_by'); ?>">Order Products By:</label></td>
                    <td class="tt-widget-content">
                        <select id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
                            <option value="none" <?php selected('none', $instance['order_by']); ?> >None (Default)</option>
                            <option value="id_asc" <?php selected('id_asc', $instance['order_by']); ?> >ID ( Ascending ) </option>
                            <option value="id_desc" <?php selected('id_desc', $instance['order_by']); ?> >ID ( Descending ) </option>
                            <option value="date_asc"  <?php selected('date_asc', $instance['order_by']); ?>>Date ( Ascending ) </option>
                            <option value="date_desc"  <?php selected('date_desc', $instance['order_by']); ?>>Date ( Descending ) </option>
                            <option value="title_asc" <?php selected('title_asc', $instance['order_by']); ?>>Title ( Ascending ) </option>
                            <option value="title_desc" <?php selected('title_desc', $instance['order_by']); ?>>Title ( Descending  ) </option>
                            <option value="rand" <?php selected('rand', $instance['order_by']); ?>>Random</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <td class="tt-widget-label">Display Elements:</td>
                    <td class="tt-widget-content">
                        <input type="checkbox" name="<?php echo $this->get_field_name('display_title'); ?>"  <?php checked('true', $instance['display_title']); ?> value="true" />  Product Name
                          <br /><input type="checkbox" name="<?php echo $this->get_field_name('display_content'); ?>"  <?php checked('true', $instance['display_content']); ?> value="true" /> Product Description / The Excerpt
                        <br /><input type="checkbox" name="<?php echo $this->get_field_name('display_featured_image'); ?>"  <?php checked('true', $instance['display_featured_image']); ?> value="true" /> Thumbnail
                        <br /><input type="checkbox" name="<?php echo $this->get_field_name('display_read_more'); ?>"  <?php checked('true', $instance['display_read_more']); ?> value="true" />  "Read More" Link
                        <br /><input type="checkbox" name="<?php echo $this->get_field_name('display_featured_products'); ?>"  <?php checked('yes', $instance['display_featured_products']); ?> value="yes" />  Display Featured Products
                    </td>
                </tr>
                
                <tr>
                    <td class="tt-widget-label">Content Type:</td>
                    <td class="tt-widget-content">
                        <input type="radio" name="<?php echo $this->get_field_name('content_type'); ?>" <?php checked('the_content', $instance['content_type']); ?> value="the_content" /> The Content<br />
                        <input type="radio" name="<?php echo $this->get_field_name('content_type'); ?>" <?php checked('the_excerpt', $instance['content_type']); ?> value="the_excerpt" /> The Excerpt &nbsp; <label for="<?php echo $this->get_field_id('excerpt_length'); ?>">The Excerpt Length:</label> <input style="width: 40px;" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="text" value="<?php echo esc_attr($instance['excerpt_length']); ?>" /> <span class="tt-widget-help">words</span>
                    </td>
                </tr>
                
                <tr>
                    <td class="tt-widget-label">Thumbnail:</td>
                    <td class="tt-widget-content">
                        Width: <input type="text" style="width: 40px;" name="<?php echo $this->get_field_name('featured_image_width'); ?>" value="<?php echo esc_attr($instance['featured_image_width']); ?>" /> &nbsp; Height: <input type="text" style="width: 40px;" name="<?php echo $this->get_field_name('featured_image_height'); ?>" value="<?php echo esc_attr($instance['featured_image_height']); ?>"  />  
                         &nbsp; Float: <select name="<?php echo $this->get_field_name('featured_image_align'); ?>">
                            <option value="alignleft" <?php selected('alignleft', $instance['featured_image_align']); ?> >Left</option>
                            <option value="alignright"  <?php selected('alignright', $instance['featured_image_align']); ?>>Right</option>
                            <option value="aligncenter" <?php selected('aligncenter', $instance['featured_image_align']); ?>>Center</option>
                        </select>
                    </td>
                </tr>
            
                <tr>
                    <td class="tt-widget-label">Filter:</td>
                    <td class="tt-widget-content" style="padding-top: 5px;">
                        <input type="radio" name="<?php echo $this->get_field_name('filter'); ?>" <?php checked('recent', $instance['filter']); ?> value="recent" /> Show Recent Products <br /><br />
                       
                        <input type="radio" name="<?php echo $this->get_field_name('filter'); ?>" <?php checked('category', $instance['filter']); ?> value="category" /> Show Products from a single category:<br />
                        <select name="<?php echo $this->get_field_name('selected_category'); ?>">
                        <?php
                            $args = array( 'hide_empty'=>0,'taxonomy' => 'item_cat' );
                            $categories = get_categories($args);
                            foreach ($categories as $category) {
                                $category_selected = $this->get_field_name('selected_category') == $category->cat_ID ? ' selected="selected" ' : '';
                                ?>
                                <option value="<?php echo $category->cat_ID; ?>" <?php selected($category->cat_ID, $instance['selected_category']); ?> ><?php echo $category->cat_name; ?></option>
                                <?php
                            }
                        ?>
                        </select>
                        <br /><br />
                        
                        <input type="radio" name="<?php echo $this->get_field_name('filter'); ?>" <?php checked('cats', $instance['filter']); ?> value="cats" /> <label for="<?php echo $this->get_field_id('filter_cats'); ?>">Show Products from  selected categories:</label>
                        <br /><span class="tt-widget-help">Category IDs ( e.g: 5,9,24 )</span>
                        <br /><input class="widefat" id="<?php echo $this->get_field_id('filter_cats'); ?>" name="<?php echo $this->get_field_name('filter_cats'); ?>" type="text" value="<?php echo esc_attr($instance['filter_cats']); ?>" />
                        
                        
                        
                        
                    </td>
                </tr>
                
            </table>
          </div>
        <?php 
    }
}