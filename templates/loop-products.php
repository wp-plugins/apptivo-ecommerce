<?php 
global $apptivo_ecommerce_loop,$wp_query;
$_product = &new apptivo_ecommerce_product( $post->ID ); 
$apptivo_ecommerce_loop['loop'] = 0;
$apptivo_ecommerce_loop['show_products'] = true;
if (!isset($apptivo_ecommerce_loop['columns']) || !$apptivo_ecommerce_loop['columns']) $apptivo_ecommerce_loop['columns'] = apply_filters('loop_products_columns', 4);
?>
<?php
do_action('apptivo_ecommerce_before_products_loop'); 
$count_posts = get_option('apptivo_ecommerce_products_per_page');
if($count_posts == '' ) : $count_posts = 8; endif;
if( $count_posts < 1 )
{
	$products_notfound_message = apply_filters('apptivo_ecommerce_products_error_message','No products found which match your selection');
	echo '<p class="info" style="color:#f00;">'.__($products_notfound_message, 'apptivo_ecommerce').'</p>';
	return;
}

//$cat_slug = $_GET['item_cat'];
//$cat_slug = $term;
$term = get_term_by('slug', $cat_slug, 'item_cat');
$categoryID = get_post_meta($term->term_id, '_apptivo_category_id',$single = true);
	
	 if( $_GET['page'] == '' || $_GET['page'] < 1 )
	   {
	   	 $pageNo = 1;
	   	 if(isset($_SESSION['apptivo_ecommerce_sort_type'])) :
	   	 unset($_SESSION['apptivo_ecommerce_sort_type']);
	   	 endif;
	   	 $sortBy = 0;
	   }else
	   { 
	   	$pageNo = $_GET['page'];
	   	if(isset($_SESSION['apptivo_ecommerce_sort_type'])) :
	   	 $sortBy = trim($_SESSION['apptivo_ecommerce_sort_type']);
	   	 else:
	   	 $sortBy = 0;
	   	 endif;
	   } 
	   $from_index = ($pageNo*$count_posts) - ($count_posts-1);
	   
	  if(isset($_GET['s']) && $_GET['post_type'] == 'item') : //Search Item Ony
	  	list($item_lists,$total_items_in_apptivo) = searchitems($_GET['s'],$count_posts,$pageNo, $sortBy);
	  else: // Get All items and get items by category ID.
	  	list($item_lists,$total_items_in_apptivo) = app_getItemsByCategoryId($categoryID,$count_posts,$pageNo, $sortBy);
	  endif;
	 
	  $items_details = $item_lists;
	  $items_details_cnt = count($items_details); 
	  $total_items = $total_items_in_apptivo;
 if($item_lists[0] != '' || !empty($item_lists[0])) :
 
 //pagination
       $reload = $_SERVER["REQUEST_URI"];
		$pos = strpos($reload, '/?');
		if ($pos === false) {
	        $reload = $_SERVER["REQUEST_URI"]."?type=product";
		} else {
		    $reload = $_SERVER["REQUEST_URI"];
		}
		$reload = explode('&page',$reload);
		if($_GET['page'] == '' || !is_numeric($_GET['page']) || $_GET['page'] == 0) :
			$cur_page = 1;
		else :
			$cur_page = $_GET['page'];
		endif;		
		$total_pages = ceil($total_items/$count_posts);
		
		if($cur_page > $total_pages) :
		$cur_page = $total_pages;
		endif;
//End Pagination
$apptivo_pagination_type = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_products_pagination_type'));	

 ?>
<?php if($total_pages > 1 && ($apptivo_pagination_type == 2 || $apptivo_pagination_type == 3)) :
  $sort_style = "width:40%;float:left;"; 
 else:
  $sort_style = "float:right;"; 
 endif; ?>
<div style="width:100%;">

<!-- Sorting Options -->
<?php if(get_option('apptivo_ecommerce_enable_sortby') == 'yes' && !isset($_GET['s']) ): ?>
<?php 
$list_sorting_type = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_products_sorting_type'));
if(isset($_SESSION['apptivo_ecommerce_sort_type'])) :
switch($_SESSION['apptivo_ecommerce_sort_type'])
{
	case '1':
	$selected1 = 'selected="selected"';break;
	case '2':
	$selected2 = 'selected="selected"';break;
	case '6':
	$selected6 = 'selected="selected"';break;
	case '7':
	$selected7 = 'selected="selected"';break;
}
else:
switch($list_sorting_type)
{
	case 'Default':
	case '1':
	$selected1 = 'selected="selected"';break;
	case '2':
	$selected2 = 'selected="selected"';break;
	case '6':
	$selected6 = 'selected="selected"';break;
	case '7':
	$selected7 = 'selected="selected"';break;
}
endif;
?>
<div style="<?php echo $sort_style; ?>">
<div class="soring_typediv">
<input type="hidden" id="item_category" name="item_category" value="<?php echo $categoryID;?>" />
<input type="hidden" id="item_pageno" name="item_pageno" value="<?php echo $cur_page; ?>" />
<span class="sortby_label">Sort By</span>
<select class="sortby" name="sort_by">
   <option <?php echo $selected1; ?> value="1">Price: Low to High</option>
   <option <?php echo $selected2; ?> value="2">Price: High to Low</option>
   <option <?php echo $selected6; ?> value="6">Name: A to Z</option>
   <option <?php echo $selected7; ?> value="7">Name: Z to A</option>
</select> 
</div>
</div>
<?php endif; ?>
<?php //Top Page Pagination. ?>
<?php	
if($total_pages > 1 && ($apptivo_pagination_type == 2 || $apptivo_pagination_type == 3)) :  
    echo '<div style="width:60%;float:right;">';
    echo products_pagination($reload[0],$cur_page,$total_pages);
    echo '</div>';
endif;
?>
</div>      
<ul class="items" id="item_lists">

	<?php 
	$reagular_price_option = apptivo_ecommerce_clean(get_option('apptivo_ecommerce_enable_regular_price'));
	$apptivo_ecommerce_loop['loop'] = 0;
	 
       foreach($item_lists as $items_details) :      
       
       if( $items_details->trackColors == '' && $items_details->trackSizes == '' )
       {
         $track_size_color = false;
       }else {
        $track_size_color = true;
       }
      
        if ($items_details->qatt <= 0 && $items_details->itemSaleECommInventoryConstrained == 'true') {
        	$qatt = true; 
        }else { $qatt = false; }
       
       
       $product_postid = getIdFromMeta( '_apptivo_item_id', $items_details->itemId );
       if( $product_postid != '') {
       	
       if ($items_details->qatt <= 0 && $items_details->itemSaleECommInventoryConstrained == 'true') {
        	$qatt = true;        	
       }else { $qatt = false; }
        
       	
        $_product = &new apptivo_ecommerce_product( $product_postid );
        $apptivo_ecommerce_loop['loop']++;	
		?>
		<li class="product <?php if ($apptivo_ecommerce_loop['loop']%$apptivo_ecommerce_loop['columns']==0) echo 'last'; if (($apptivo_ecommerce_loop['loop']-1)%$apptivo_ecommerce_loop['columns']==0) echo 'first'; ?>">
			
			<?php do_action('apptivo_ecommerce_before_products_loop_item'); ?>
			
			<div class="apptivo_product_image">
			<a href="<?php echo get_permalink($product_postid); ?>">
			<?php echo get_product_thumbnail($product_postid); ?>
			</a>
			</div>			
			   <span class="price">
			      <?php if($items_details->itemEffectivePrice == '') { $items_details->itemEffectivePrice = '0.00'; }?>
				   <?php echo apptivo_ecommerce_price($items_details->itemEffectivePrice);?>				   
			   </span>
			  <div class="item_name"><a title="<?php echo $items_details->itemName; ?>" href="<?php echo get_permalink($product_postid); ?>"><?php echo apptivo_ecommerce_itemname(trim($items_details->itemName));?></a></div>
			<?php
			//add to cart button.
			$item_lists = $product_postid."+".$items_details->itemId."+".$items_details->itemPrimaryUOMId."+1+".$items_details->itemEffectivePrice; // For posting Purpose... Format : itemID+itemUomID+itemqty+itemPrice
			do_action('apptivo_ecommerce_add_to_cart_and_view_details_btn',$product_postid, $_product,$item_lists,$track_size_color,$qatt);
			?>			
		</li>
		<?php 
       }
		endforeach;
		?></ul><?php		
	endif;	
	if ($apptivo_ecommerce_loop['loop']==0)
	{   
		$products_notfound_message = apply_filters('apptivo_ecommerce_products_error_message','No products found which match your selection');
		echo '<p class="info" style="color:#f00;">'.__($products_notfound_message, 'apptivo_ecommerce').'</p>';
	}
	?>
<div class="clear"></div>
<?php do_action('apptivo_ecommerce_after_products_loop'); ?>
<?php //Bottom Page Pagination. ?>
<?php	if($total_pages > 1 && ($apptivo_pagination_type == 1 || $apptivo_pagination_type == 3)) :
        echo products_pagination($reload[0],$cur_page,$total_pages);endif;