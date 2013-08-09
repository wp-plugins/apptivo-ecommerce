<?php
/**
 * Apptivo ecommerce Template functions
 * @package  Apptivo Ecommerce
 * @author Rajkumar <rmohanasundaram[at]apptivo[dot]com>
 */
if (!function_exists('apptivo_ecommerce_output_content_wrapper')) {
	function apptivo_ecommerce_output_content_wrapper() {	
		if ( get_option('template') === 'twentyeleven' ) :
			echo '<div id="primary">';		
		endif;
		
		if ( get_option('template') === 'twentyten' ) :
			echo '<div id="container">';		
		endif;
		
		if ( get_option('template') === 'twentytwelve' ) :
			echo '<div class="site-content" id="primary">';
		endif;
		if ( get_option('template') === 'twentythirteen' ) :
			echo '<div class="content-area" id="primary"><div class="entry-content">';
		endif;
		
	}
}
if (!function_exists('apptivo_ecommerce_output_content_wrapper_end')) {
	function apptivo_ecommerce_output_content_wrapper_end() {
			
		if ( get_option('template') === 'twentyeleven' ) :
			echo  '</div>';
		endif;
		
		if ( get_option('template') === 'twentyten' ) :
			echo  '</div>';		
		endif;
		
		if ( get_option('template') === 'twentytwelve' ) :
			echo  '</div>';
		endif;
		if ( get_option('template') === 'twentythirteen' ) :
			echo  '</div></div>';
		endif;
		
	}
}
/**
 * Products Loop
 **/
if (!function_exists('apptivo_ecommerce_template_loop_add_to_cart')) {
	function apptivo_ecommerce_template_loop_add_to_cart( $product_postid, $_product,$app_itemid_catuomid,$track_size_color = false,$qatt = false) {
		if ($track_size_color || $_product->sale_price() === '' || $_product->sale_price() <= 0 || $qatt) :
		    $details_label 	= apply_filters('apptivo_ecommerce_view_details_button', __('View Details', 'apptivo-ecommerce'));
			echo '<a title="'.get_the_title($product_postid).'" href="'.get_permalink($product_postid).'" class="btn">'.__('<span>'.$details_label.'</span>', 'apptivo-ecommerce').'</a>';
			return;
		else :		
			$link 	= esc_url( add_query_arg('add-items-to-cart',$app_itemid_catuomid)); 
		    $label 	= apply_filters('apptivo_ecommerce_add_to_cart_button', __('Add to cart', 'apptivo-ecommerce'));
		    $label = '<span>'.$label.'</span>';
	        if(get_option('apptivo_ecommerce_redirects_to_cart') == 'yes')
	        {
			   echo sprintf('<a href="%s" rel="%s" class="btn add_item_to_cart_button">%s</a>', $link,$app_itemid_catuomid,$label);				
	        }else{
	           echo sprintf('<a href="%s" rel="%s" class="btn addtocart_button product_type_%s">%s</a>', $link,$app_itemid_catuomid, 'item', $label);	
	        }	    
			return;		
		endif;
	}
}
/*
 * Product add_to_cart_button
 */
function apptivo_ecommerce_viewdetails_addtocart_btn($post_id,$price) {
	
	
	
	$colors = get_post_meta($post_id,'_apptivo_track_color',true);
	$sizes = get_post_meta($post_id,'_apptivo_track_size',true);
	
	if ($colors != '' || $sizes != ''  || $price === '' || $price <= 0 ) {
		    $details_label 	= apply_filters('apptivo_ecommerce_view_details_button', __('View Details', 'apptivo-ecommerce'));
			echo '<a title="'.get_the_title($post_id).'" href="'.get_permalink($product_postid).'" class="btn">'.__('<span>'.$details_label.'</span>', 'apptivo-ecommerce').'</a>';
			return;
    }else {
    	    $item_Id = get_post_meta($post_id,'_apptivo_item_id',true);
    	    $itemPrimary_UOMId = get_post_meta($post_id,'_apptivo_item_uom_id',true);
    	
    	    $app_itemid_catuomid = $post_id."+".$item_Id."+".$itemPrimary_UOMId;
    	    
		    $link 	= esc_url( add_query_arg('add-items-to-cart',$app_itemid_catuomid)); 
		    $label 	= apply_filters('apptivo_ecommerce_add_to_cart_button', __('Add to cart', 'apptivo-ecommerce'));
		    $label = '<span>'.$label.'</span>';
	        if(get_option('apptivo_ecommerce_redirects_to_cart') == 'yes')
	        {
			   echo sprintf('<a href="%s" rel="%s" class="btn add_item_to_cart_button">%s</a>', $link,$app_itemid_catuomid,$label);				
	        }else{
	           echo sprintf('<a href="%s" rel="%s" class="btn addtocart_button product_type_%s">%s</a>', $link,$app_itemid_catuomid, 'item', $label);	
	        }	    
			return;	
    }
}


function apptivo_ecommerce_add_to_cart( $product_postid, $_product,$app_itemid_catuomid,$track_size_color = false,$qatt = false) {
		
		if ($track_size_color || $_product->sale_price() === '' || $_product->sale_price() <= 0 || $qatt) :
		    $details_label 	= apply_filters('apptivo_ecommerce_view_details_button', __('View Details', 'apptivo-ecommerce'));
			echo '<a href="'.get_permalink($product_postid).'" class="btn">'.__('<span>'.$details_label.'</span>', 'apptivo-ecommerce').'</a>';
			return;
		else :
			$target_pageid = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_products_page_id')); 
			$link = add_query_arg('add-items-to-cart', $app_itemid_catuomid,'/?post_type=product&');
			
		    $label 	= apply_filters('apptivo_ecommerce_add_to_cart_button', __('Add to cart', 'apptivo-ecommerce'));
		    $label = '<span>'.$label.'</span>';
	        if(get_option('apptivo_ecommerce_redirects_to_cart') == 'yes')
	        {
			   echo sprintf('<a href="%s" rel="%s" class="btn add_item_to_cart_button">%s</a>', $link,$app_itemid_catuomid,$label);
			
	        }else{
	           echo sprintf('<a href="%s" rel="%s" class="btn addtocart_button product_type_%s">%s</a>', $link,$app_itemid_catuomid, 'item', $label);	
	        }	    
			return;		
		endif;
	}
	

	
if (!function_exists('apptivo_ecommerce_template_loop_price')) {
	function apptivo_ecommerce_template_loop_price( $post, $_product ) {
		?><span class="price"><?php echo $_product->sale_regular_price_html(); ?></span><?php
	}
}


/**
 * Before Single Products Summary Div
 **/
if (!function_exists('apptivo_ecommerce_show_product_images')) {
	function apptivo_ecommerce_show_product_images() {
		
		global $_product, $post, $apptivo_ecommerce;

		echo '<div class="images">';
        $product_postid = $post->ID;
		$thumb_id = 0;
		if (has_post_thumbnail()) :
			$thumb_id = get_post_thumbnail_id();
			$single_image_size = 'product_single';
			
			 $attr = array( 'alt'	=> trim(strip_tags( get_the_title($product_postid))),
			                                           'title'	=> trim(strip_tags( get_the_title($product_postid))) 
									                  );
			if( get_option("apptivo_ecommerce_enable_lightbox") != 'yes') {						                   
			echo '<a target="_blank" href="'.wp_get_attachment_url($thumb_id).'" class="single_image" title="'.get_the_title().'"><div style="height:'.get_option("apptivo_ecommerce_single_image_height").'px;" >';
			the_post_thumbnail($single_image_size,$attr); 
			echo '</div></a>';
			}else{
			echo '<a target="_blank" href="'.wp_get_attachment_url($thumb_id).'" class="pretty_gallery single_image" rel="prettyPhoto[gallery]" title="'.get_the_title().'">';
			the_post_thumbnail($single_image_size,$attr); 
			echo '</a>';
			}
			
			echo '<input type="hidden" id="product_lightbox" name="product_lightbox" value="'.get_option("apptivo_ecommerce_enable_lightbox").'" />'; 
		else : 
		    $singular_noproduct_image = apply_filters('apptivo_ecommerce_sigular_noproduct_img',$apptivo_ecommerce->plugin_url().'/assets/images/no-product-300.jpg');
			echo '<img src="'.$singular_noproduct_image.'" alt="Placeholder" />';			
		endif; 
        do_action('apptivo_ecommerce_product_thumbnails');
		echo '</div>';
		
	}
}
if (!function_exists('apptivo_ecommerce_show_product_thumbnails')) {
	function apptivo_ecommerce_show_product_thumbnails() {
		
		global $_product, $post;
		$lightbox_enable = get_option("apptivo_ecommerce_enable_lightbox");
		echo '<div class="thumbnails">';
		
		$thumb_id = get_post_thumbnail_id();
		$small_thumbnail_size = 'product_thumbnail';
		if($lightbox_enable == 'yes') :
		$args = array( 
			'post_type' 	=> 'attachment', 
			'numberposts' 	=> -1, 
			'post_status' 	=> null, 
			'post_parent' 	=> $post->ID,
			'post_mime_type' => 'image',
		    'post__not_in'	=> array( get_post_thumbnail_id() ),
		    'orderby'         => 'menu_order',
            'order'           => 'ASC',
			'meta_query' 	=> array(
				array(
					'key' 		=> '_apptivo_ecommerce_exclude_image',
					'value'		=> '1',
					'compare' 	=> '!='
				)
			)
		); 
		else:
		$args = array( 
			'post_type' 	=> 'attachment', 
			'numberposts' 	=> -1, 
			'post_status' 	=> null, 
			'post_parent' 	=> $post->ID,
			'post_mime_type' => 'image',
		    'orderby'         => 'menu_order',
            'order'           => 'ASC',
		    'meta_query' 	=> array(
				array(
					'key' 		=> '_apptivo_ecommerce_exclude_image',
					'value'		=> '1',
					'compare' 	=> '!='
				)
			)
		); 
		endif;
		$attachments = get_posts($args);
		if ($attachments) :
			$loop = 0;
			$columns = apply_filters('apptivo_ecommerce_product_thumbnails_columns', 3);
			foreach ( $attachments as $attachment ) : 
				
				$loop++;
				
				$_post = & get_post( $attachment->ID );
				$url = wp_get_attachment_url($_post->ID);
				$post_title = esc_attr($_post->post_title);
				$image = wp_get_attachment_image($attachment->ID, $small_thumbnail_size);
				if($lightbox_enable == 'no')
				{
					$href = 'href="javascript:void(0)" target_url="'.$url.'" ';
					
				}else{
					
					$href = 'href="'.$url.'"';
				}
				echo '<a '.$href.' title="'.$post_title.'" rel="prettyPhoto[gallery]" class="pretty_gallery product_thumbnial ';
				if ($loop==1 || ($loop-1)%$columns==0) echo 'first';
				if ($loop%$columns==0) echo 'last';
				echo '">'.$image.'</a>';
				
			endforeach;
		endif;
		wp_reset_query();
		
		echo '</div>';
		
	}
}

/**
 * Product summary box
 **/
if (!function_exists('apptivo_ecommerce_template_single_price')) {
	function apptivo_ecommerce_template_single_price( $post, $_product ) {       
		?><p class="price"><?php echo $_product->sale_regular_price_html(); ?></p><?php		
	}
}

if (!function_exists('apptivo_ecommerce_template_single_excerpt')) {
	function apptivo_ecommerce_template_single_excerpt( $post, $_product ) {
		if ($post->post_excerpt) echo wpautop(wptexturize($post->post_excerpt));
	}
}


/**
 * Product Add to cart buttons
 **/
if (!function_exists('apptivo_ecommerce_template_single_add_to_cart')) {
	function apptivo_ecommerce_template_single_add_to_cart( $post, $_product ) {
		do_action( 'apptivo_ecommerce_simple_add_to_cart' );
	}
}
if (!function_exists('apptivo_ecommerce_simple_add_to_cart')) {
	function apptivo_ecommerce_simple_add_to_cart() {

		global $_product,$post; 
		
		$sale_Price =  get_post_meta($post->ID,'_apptivo_sale_price',true);
		
		$itemId =  get_post_meta($post->ID,'_apptivo_item_id',true);
		if($itemId)
		{
			$get_apptivo_item = getItemById($itemId);
			if ($get_apptivo_item->qatt <= 0 && $get_apptivo_item->itemSaleECommInventoryConstrained == 'true')
			{
				echo '<br /><span class="no_stack">This product is temporarily unavailable.</span>'; return;
			}		
		}else {
			  echo '<br /><span class="no_stack view_mode">This product is not ready for sale.</span>'; return;
		}
		$tracksizes = app_convertObjectToArray(explode(',',$get_apptivo_item->trackColors));
		$trackcolors = app_convertObjectToArray(explode(',',$get_apptivo_item->trackSizes));
		?>
		<!--  Item Code  -->
		<?php
        $item_code_option = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_enable_item_code'));
		if( isset($get_apptivo_item->itemCode) && $item_code_option == 'yes') : ?>
		<div class="item_code">
		    <label><?php echo apply_filters('apptivo_ecommerce_item_code_label','Item Code :');?></label>
		    <span class="app_item_code"><?php echo $get_apptivo_item->itemCode;?></span>
        </div>
        <?php endif; ?>
        	
		<!-- Price Details -->
		<?php  if(isset($get_apptivo_item->itemMSRP)) :?>		
		<?php
		$reagular_price_option = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_enable_regular_price'));
		if($reagular_price_option == 'yes') {
		if($get_apptivo_item->itemMSRP != '' && ($get_apptivo_item->itemMSRP > 0 )) { ?>
		<div class="regular_price">
		    <label><?php echo apply_filters('apptivo_ecommerce_regular_price_label','Regular Price :');?></label>
		    <span><?php echo apptivo_ecommerce_price($get_apptivo_item->itemMSRP);?></span>
        </div>
        <?php }
		}
        endif; ?>
        
        <!-- Sale Price -->		
       <?php  if($get_apptivo_item->itemPriceListPrice == '') { $get_apptivo_item->itemPriceListPrice = '0.00';  } ?>
        <div class="sale_price">
		    <label><?php echo apply_filters('apptivo_ecommerce_sale_price_label','Price :');?></label>
		    <span><?php echo apptivo_ecommerce_price($get_apptivo_item->itemPriceListPrice);?></span>
        </div>
        
        <?php 
        if($get_apptivo_item->itemPriceListPrice <= 0)
		{
			echo '<br /><span class="no_stack">This product is not ready for sale.</span>';return;
		}		
		?>
       <!-- Price Details -->
            		
		<form action="<?php echo esc_url( $_product->add_to_shopping_cart_url() ); ?>" class="cart" method="post">
		<?php if($tracksizes != '' && $tracksizes[0] != '') :?>
		    <div class="tracking_colors">
		    <span class="track_colors" ><?php echo apply_filters('apptivo_ecommerce_tracking_color_label','Color :');?></span>
		    <select name="colors" id="colors">
		    <?php foreach ($tracksizes as $sizes) : 
		    if( trim($sizes) != '') : ?>
			<option value="<?php echo $sizes; ?>"><?php echo $sizes; ?></option>
			<?php endif; 
			endforeach; ?>    
            </select>
            </div>
            <div class="clear"></div>
           
        <?php endif; ?>
		    
		    <?php if($trackcolors != '' && $trackcolors[0] != '' ) :?>
		    <div class="tracking_sizes">
		    <span class="track_sizes" ><?php echo apply_filters('apptivo_ecommerce_tracking_size_label','Size :');?></span>
		    <select name="sizes" id="sizes">
			<?php foreach ($trackcolors as $colors) : 
			if( trim($colors) != '') : ?>
			<option value="<?php echo $colors; ?>"><?php echo $colors; ?></option>
			<?php endif;
			endforeach; ?>      
            </select>
           </div>
           <?php endif; ?>
		    
		 	<div class="item_quantity">
		 	<label><?php echo apply_filters('apptivo_ecommerce_qty_label','Quantity :');?></label>
		 	<input name="quantity" value="1" size="2" title="Qty" class="input-text itemqty text" maxlength="2" /></div>
		 	<div class="addtocart_button">
		 	 <?php $add_to_cart_label 	= apply_filters('apptivo_ecommerce_add_to_cart_button', __('Add to cart', 'apptivo-ecommerce')); ?>
		 	
		 	</div>
		 	<button type="submit" class="btn alt"><span><?php _e($add_to_cart_label, 'apptivo-ecommerce'); ?></span></button>
		 	
		 	<?php  do_action('apptivo_ecommerce_add_to_cart_form'); ?>
		</form>
		<?php
	}
}


/**
 * Product Add to Cart forms
 **/
if (!function_exists('apptivo_ecommerce_add_to_cart_form_nonce')) {
	function apptivo_ecommerce_add_to_cart_form_nonce() {
		global $apptivo_ecommerce;
		$apptivo_ecommerce->nonce_field('add_to_cart');
	}
}

/**
 * Pagination
 **/
if (!function_exists('apptivo_ecommerce_pagination')) {
	function apptivo_ecommerce_pagination($post_url,$tax="",$cur_page="") {
		
		
		global $wp_query;
	   
	    
	    if( $tax == 'tag')
	    {
	    	
	    	if (  $wp_query->max_num_pages > 1 && $cur_page <= $wp_query->max_num_pages ) : 
		     if($cur_page == '' || $cur_page == 0) { $cur_page = 1; }		     
			?>
			<div class="navigation">
				<?php  echo products_posts_pagination($post_url,$cur_page,$wp_query->max_num_pages,'page'); ?>
			</div>
			<?php 
		endif;
	    	
	    }else {
		if (  $wp_query->max_num_pages > 1 && $cur_page <= $wp_query->max_num_pages ) : 
		$cur_page =  get_query_var('paged');
	     if($cur_page == '' || $cur_page == 0) { $cur_page = 1; }
			?>
			<div class="navigation">
				<?php  echo products_posts_pagination($post_url,$cur_page,$wp_query->max_num_pages); ?>
			</div>
			<?php 
		endif;
	    }
		
	}
}


if (!function_exists('apptivo_ecommerce_product_description_panel')) {
	function apptivo_ecommerce_product_description_panel() {
		     $content = get_the_content($more_link_text, $stripteaser);
		     $content = apply_filters('the_content', $content);
	         $content = str_replace(']]>', ']]&gt;', $content);
	    if(trim($content) != '') :
		echo '<div class="panel">';
		echo apply_filters('apptivo_ecommerce_product_description_heading', __('<h2><b>Product Description</b></h2>', 'apptivo-ecommerce'));
		echo $content;
		echo '</div>';
		endif;
	}
}



if (!function_exists('apptivo_ecommerce_cart_totals')) {
	function apptivo_ecommerce_cart_totals() {
		global $apptivo_ecommerce;		
		$cartInfo = get_baginfo();
		$shoppingCartLines = app_convertObjectToArray($cartInfo->shoppingCartLines);
		foreach($shoppingCartLines as $TaxLines)
		{
			if($TaxLines->lineTypeCode == 'TAX')
			{
				$taxName = $TaxLines->lineTypeName;
				if($taxName == '')
				{
					$taxName = 'Tax';
				}
				$taxAmount = $TaxLines->effectiveTotalPrice;
			}
		}
		$available_methods = $cartInfo->ratedShipment;
		?>
		<div class="totals_cart">
		<?php
		if (!empty($cartInfo) ) : 
			?>
			<?php echo apply_filters('apptivo_ecommerce_cart_totals_label','<h2>Cart Total</h2>'); ?>
			<table cellspacing="0" cellpadding="0">
				<tbody>
				    <!-- Sub total Amount. -->
					<tr>
						<th><?php _e('Subtotal', 'apptivo-ecommerce'); ?></th>
						<td><?php echo apptivo_ecommerce_price($cartInfo->subTotalAmount); //echo $apptivo_ecommerce->cart->get_cart_subtotal(); ?></td>
					</tr>
					<!-- Slaes Tax Amount -->
					<?php if ($taxAmount > 0) : ?>
					<tr>
						<th><?php echo $taxName; ?></th>
						<td><?php echo apptivo_ecommerce_price($taxAmount);  ?></td>
					</tr>
					<?php endif; ?>
					<!--  Shipping Amount  -->
					<?php if ($cartInfo->shippingAmount != '' && $cartInfo->shippingAmount > 0) : ?>
					<tr>
						<th><?php _e('Shipping Amount', 'apptivo-ecommerce'); ?></th>
						<td><?php echo apptivo_ecommerce_price($cartInfo->shippingAmount); ?></td>
					</tr>
					<?php endif;?>
					
					<!--  Shipping Methods. -->
					<?php if ( sizeof($available_methods)>0 ) : ?>
					<tr id="cart_shipping_options" >
						<th><?php _e('Shipping', 'apptivo-ecommerce'); ?> </th>
						<td>
							<?php
									echo '<select name="shipping_method" id="shipping_method">';
									foreach ($available_methods as $method ) :
										echo '<option value="'.$method->firmShippingMethodId.'" '.selected($method->firmShippingMethodId, $cartInfo->shippingOption, false).'>'.$method->serviceName.' &ndash;'.$method->totalCharges;;
										echo '</option>';
									endforeach;
									echo '</select>';
							?>
						</td>
					</tr>
					
					<?php endif; ?>
					<!-- Discount Amount -->
					<?php if ($cartInfo->totalDiscountAmount != '' && $cartInfo->totalDiscountAmount > 0) : ?><tr class="discount">
						<th><?php _e('Discount', 'apptivo-ecommerce'); ?></th>
						<td>-<?php echo apptivo_ecommerce_price($cartInfo->totalDiscountAmount); ?></td>
					</tr>
					<?php endif; ?>
					
					
					<tr>
						<th><strong><?php _e('Total', 'apptivo-ecommerce'); ?></strong></th>
						<td><strong><?php echo apptivo_ecommerce_price($cartInfo->totalPrice);  ?></strong></td>
					</tr>
					
				</tbody>
			</table>
			<?php
		else :
			echo apply_filters('apptivo_ecommerce_cart_empty_message','<p> Your cart is Empty</p>');
		endif;
		?>
		</div>
		<?php
	}
}

if (!function_exists('apptivo_ecommerce_login_form')) {
	function apptivo_ecommerce_login_form( $message = '',$page='checkout') {
		global $apptivo_ecommerce;	 
		
		if (is_apptivo_user_logged_in()) return;
		$register_pageURL = get_permalink(get_option('apptivo_ecommerce_register_page_id'));
		$account_username= apply_filters('apptivo_ecommerce_account_username_label','Email Address');
		?>
		<form method="post" id="login" class="login" <?php if($page == 'checkout') { ?>style="display:none;"<?php } ?> >
		
			<?php if ($message) echo wpautop(wptexturize($message)); ?>
			<p class="login_heading" ><?php echo apply_filters('apptivo_ecommerce_login_heading','Log In'); ?></p>
			<p class="form-row form-row-first" >
				<label for="username"><?php echo $account_username; ?> <span class="required">*</span></label>
				<input value="<?php echo $_POST['username']; ?>" type="text" class="input-text" name="username" id="username"  />
			</p>
			<p class="form-row form-row-last">
				<label for="password"><?php _e('Password', 'apptivo-ecommerce'); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>
			<div class="clear"  ></div>
			<p class="form-row">
				
				<input type="submit" class="btn" name="login" value="<?php echo apply_filters('apptivo_ecommerce_login_submit_button','Login' ); ?>" />
			</p>			
			<?php  if( $page == 'login' )
			{
				echo '<div class="clear" id="register_login" ></div>';
				$login_register = '<p><a class="lost_password" href="javascript:void(0)">'.apply_filters('apptivo_ecommerce_lost_password_label','Forgot Password?').'</a>&nbsp;&nbsp;&nbsp;&nbsp; Not Yet Register?  <a href="'.$register_pageURL.'"> Register Now</a></p>';
				$login_register = apply_filters('apptivo_ecommerce_login_register',$login_register);				
				echo $login_register;
			}
			?>
			
			
				
		</form>
		
		<form method="post" class="reset_pwd" id="reset_pwd" style="display:none;" >
		<p class="reset_pwd_heading" ><?php echo apply_filters('apptivo_ecommerce_forgot_password_heading','Forgot Password');?></p>
		<p class="form-row" >
				<?php $apptivo_ecommerce->nonce_field('last_pwd', 'last_pwd') ?>
				<label for="username"><?php echo $account_username; ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="account_username" id="account_username" />
				<input type="submit" class="reset_btn" name="last_pwd" value="<?php echo apply_filters('apptivo_ecommerce_last_pwd_submit_button','Submit' ); ?>" />				
			</p>
			<p><a class="cancel_forgot_password" href="javascript:void(0)">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;Not Yet Register?  <a href="<?php echo $register_pageURL; ?>"> Register Now</a></p>
		</form>	
		<?php
	}
}

/**
 * apptivo_ecommerce Login Form
 **/
if (!function_exists('apptivo_ecommerce_checkout_login_form')) {
	function apptivo_ecommerce_checkout_login_form() {
		if (is_apptivo_user_logged_in()) return;
		global $apptivo_ecommerce;
	    $apptivo_ecommerce->show_messages();
		?><p class="info" id="checkout_login_reg" ><?php _e('Already registered?', 'apptivo-ecommerce'); ?> <a href="#" class="login_view"><?php _e('Click here to login', 'apptivo-ecommerce'); ?></a></p><?php
		$before_account_username= apply_filters('apptivo_ecommerce_returning_customer_label','<h3>Returning Customer</h3>');		
		apptivo_ecommerce_login_form( $before_account_username );
	}
}

/**
 * apptivo_ecommerce Breadcrumb
 **/


if(!function_exists('apptivo_ecommerce_breadcrumb'))
{
	function apptivo_ecommerce_breadcrumb()
	{
		do_action('apptivo_ecommerce_before_breadcrumb');
		
			do_action('apptivo_ecommerce_breadcrumb_content');
		   
		do_action('apptivo_ecommerce_after_breadcrumb');
		
	}
}

if (!function_exists('apptivo_ecommerce_breadcrumb_content')) {
	function apptivo_ecommerce_breadcrumb_content( $delimiter = ' &rsaquo; ', $wrap_before = '<div id="breadcrumb">', $wrap_after = '</div>', $before = '', $after = '', $home = null ) {
	 	
	 	global $post, $wp_query, $author, $paged;
	 	
	 	if( !$home ) $home = _x('Home', 'breadcrumb', 'apptivo-ecommerce'); 	
	 	
	 	$home_link = home_url();
	 	
	 	$prepend = '';
	 	
	 	
	 	if ( (!is_home() && !is_front_page() && !(is_post_type_archive() && get_option('page_on_front')==get_option('apptivo_ecommerce_products_page_id'))) || is_paged() ) :
	 	
			echo $wrap_before;
	 
			echo $before  . '<a class="home" href="' . $home_link . '">' . $home . '</a> '  . $after . $delimiter ;
	 		
			if ( is_category() ) :
	      
	      		$cat_obj = $wp_query->get_queried_object();
	      		$this_category = $cat_obj->term_id;
	      		$this_category = get_category( $this_category );
	      		if ($thisCat->parent != 0) :
	      			$parent_category = get_category( $this_category->parent );
	      			echo get_category_parents($parent_category, TRUE, $delimiter );
	      		endif;
	      		echo $before . single_cat_title('', false) . $after;
	 		
	 		elseif ( is_tax('item_cat') ) :
	 		
	 			
	 			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				
				$parents = array();
				$parent = $term->parent;
				while ($parent):
					$parents[] = $parent;
					$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
					$parent = $new_parent->parent;
				endwhile;
				if(!empty($parents)):
					$parents = array_reverse($parents);
					foreach ($parents as $parent):
						$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
						echo $before .  '<a href="' . get_term_link( $item->slug, 'item_cat' ) . '">' . $item->name . '</a>' . $after . $delimiter;
					endforeach;
				endif;
	
	 			$queried_object = $wp_query->get_queried_object();
	      		echo $prepend . $before . $queried_object->name . $after;
	      	
	      	elseif ( is_tax('item_tag') ) :
				
	 			$queried_object = $wp_query->get_queried_object();
	      		echo $prepend . $before . __('Products tagged &ldquo;', 'apptivo-ecommerce') . $queried_object->name . '&rdquo;' . $after;
				
	 		
	 		elseif ( is_post_type_archive('item') && get_option('page_on_front') !== get_option('apptivo_ecommerce_products_page_id') ) :
			
	 			$_name = get_option('apptivo_ecommerce_products_page_id') ? get_the_title( get_option('apptivo_ecommerce_products_page_id') ) : ucwords(get_option('apptivo_ecommerce_products_slug'));
	 		
	 			if (is_search()) :				
	 				echo $before . '<a href="' . get_post_type_archive_link('item') . '">' . $_name . '</a>' . $delimiter . __('Search results for &ldquo;', 'apptivo-ecommerce') . get_search_query() . '&rdquo;' . $after;
	 			
	 			else :	 			
	 				echo $before .  $_name . $after;
	 			
	 			endif;
	 		
			elseif ( is_single() && !is_attachment() ) :
				
				if ( get_post_type() == 'item' ) :
					
	       			
	       			echo $prepend;
	       			
	       			if ($terms = wp_get_object_terms( $post->ID, 'item_cat' )) :
	       				$term = end($terms);
						$parents = array();
						$parent = $term->parent;
						while ($parent):
							$parents[] = $parent;
							$new_parent = get_term_by( 'id', $parent, 'item_cat');
							$parent = $new_parent->parent;
						endwhile;
						if(!empty($parents)):
							$parents = array_reverse($parents);
							foreach ($parents as $parent):
								$item = get_term_by( 'id', $parent, 'item_cat');
								echo $before . '<a href="' . get_term_link( $item->slug, 'item_cat' ) . '">' . $item->name . '</a>' . $after . $delimiter;
							endforeach;
						endif;
						echo $before . '<a href="' . get_term_link( $term->slug, 'item_cat' ) . '">' . $term->name . '</a>' . $after . $delimiter;
					endif;
					
	        		echo $before . get_the_title() . $after;
	        		
				elseif ( get_post_type() != 'post' ) :
					$post_type = get_post_type_object(get_post_type());
	        		$slug = $post_type->rewrite;
	       			echo $before . '<a href="' . get_post_type_archive_link(get_post_type()) . '">' . $post_type->labels->singular_name . '</a>' . $after . $delimiter;
	        		echo $before . get_the_title() . $after;
				else :
					$cat = current(get_the_category());
					echo get_category_parents($cat, TRUE, $delimiter);
					echo $before . get_the_title() . $after;
				endif;
	 		
	 		elseif ( is_404() ) :
		    
		    	echo $before . __('Error 404', 'apptivo-ecommerce') . $after;
	
	    	elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) :
				
				$post_type = get_post_type_object(get_post_type());
				if ($post_type) : echo $before . $post_type->labels->singular_name . $after; endif;
	 
			elseif ( is_attachment() ) :
			
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID); $cat = $cat[0];
				echo get_category_parents($cat, TRUE, '' . $delimiter);
				echo $before . '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>' . $after . $delimiter;
				echo $before . get_the_title() . $after;
	 
			elseif ( is_page() && !$post->post_parent ) :
			
				echo $before . get_the_title() . $after;
	 
			elseif ( is_page() && $post->post_parent ) :
			
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) :
					echo $crumb . '' . $delimiter;
				endforeach;
				echo $before . get_the_title() . $after;
	 
			elseif ( is_search() ) :
			
				echo $before . __('Search results for &ldquo;', 'apptivo-ecommerce') . get_search_query() . '&rdquo;' . $after;
	 
			elseif ( is_tag() ) :
			
	      		echo $before . __('Posts tagged &ldquo;', 'apptivo-ecommerce') . single_tag_title('', false) . '&rdquo;' . $after;
	 
			elseif ( is_author() ) :
			
				$userdata = get_userdata($author);
				echo $before . __('Author:', 'apptivo-ecommerce') . ' ' . $userdata->display_name . $after;
	     	
		    endif;
	 
			if ( get_query_var('paged') ) :
			
				echo ' (' . __('Page', 'apptivo-ecommerce') . ' ' . get_query_var('paged') .')';
				
			endif;
	 
	    	echo $wrap_after;
	
		endif;
		
	}
}



/**
 * Order review table for checkout
 **/
function apptivo_ecommerce_order_review() {
    apptivo_ecommerce_get_template('checkout/review_order.php', false);
}

/**
 * Order review table for checkout
 **/
function apptivo_ecommerce_checkout_order_review() {	
	
	apptivo_ecommerce_get_template('checkout/checkout_review_order.php', false);
}

/**
 * Sidebar
 **/
if (!function_exists('apptivo_ecommerce_get_sidebar')) {
	function apptivo_ecommerce_get_sidebar() {

		do_action('apptivo_ecommerce_before_sidebar');
		 get_sidebar();
		do_action('apptivo_ecommerce_after_sidebar');

	}
}