<?php
/*
 * Product visibility & data meta boxes
 * Create or Update Item
 */
function apptivo_ecommerce_product_data_box() {
	?>
	<div class="panel-wrap product_data">
		<div id="general_product_data" class="panel apptivo_ecommerce_options_panel"><?php
		        echo '<p class="form-field _apptivo_featured_field"><strong>Product Visibility</strong></p>';
                // Featured
	            apptivo_ecommerce_wp_checkbox( array( 'id' => '_apptivo_featured', 'label' => __('Featured?', 'apptivothemes') ) );	
	            //Enabled
				apptivo_ecommerce_wp_checkbox( array( 'id' => '_apptivo_enabled', 'label' => __('Enabled?', 'apptivothemes') ) );
				
				echo '<p class="form-field _apptivo_featured_field"><strong>Product Visibility</strong></p>';	
				//Item Code
			    apptivo_ecommerce_wp_text_input( array( 'id' => '_apptivo_item_code','notes' => '&nbsp; &nbsp;(Item Code should not be empty )' , 'label' => __('Item Code <span style="color:#f00;">*</span>', 'apptivothemes') ) );
			    //Suppliers
			    apptivo_ecommerce_wp_select_suppliers(array( 'id' => '_apptivo_suppliers','notes' => 'Select supplier Name' , 'label' => __('Select Supplier', 'apptivothemes') ));
			    //supplier
			    apptivo_ecommerce_wp_text_input( array( 'id' => '_apptivo_supplier','notes' => 'Add new supplier' , 'label' => __('Add New Supplier', 'apptivothemes') ) );
	            //Regular Price		
				apptivo_ecommerce_wp_text_input( array( 'id' => '_apptivo_regular_price', 'notes' => '&nbsp; &nbsp;(Regular price should be numeric )' , 'label' => __('Regular Price', 'apptivothemes') . ' ('.get_apptivo_ecommerce_currency_symbol().')' ) );
				// Sale Price
				apptivo_ecommerce_wp_text_input( array( 'id' => '_apptivo_sale_price', 'notes' => '&nbsp; &nbsp;(Sale price should be numeric)', 'label' => __('Sale Price', 'apptivothemes') . ' ('.get_apptivo_ecommerce_currency_symbol().')' ) );
				// Track Size
				apptivo_ecommerce_wp_text_input( array( 'id' => '_apptivo_track_size', 'notes' => '&nbsp; &nbsp;( Add multiple sizes  for Tracking sizes by (,) Seprating values. for example:<b> XL,XXL,XXXL</b> )', 'label' => __('Track Size  ', 'apptivothemes')  ) );
		    	// Track Color
				apptivo_ecommerce_wp_text_input( array( 'id' => '_apptivo_track_color', 'notes' => '&nbsp; &nbsp;( Add multiple colors  for Tracking colors by (,) Seprating values. for example:<b> R,G,B</b>  )', 'label' => __('Track Color', 'apptivothemes')  ) );
				
				do_action('apptivo_ecommerce_add_ons_apply_payments');
				
			?>
		</div>
		
	</div>
			<script type="text/javascript">
		jQuery(document).ready(function($) {

			var supplierName = $("select#_apptivo_suppliers").val();
			if( supplierName == '' )
			{
				$('p._apptivo_supplier_field').hide();
				$('p._apptivo_regular_price_field').hide();					
			}else{
				if( supplierName != 'add_new_suppliers' )
				{
					$('p._apptivo_supplier_field').hide();
				}
			}
			$('select#_apptivo_suppliers').change(function(){		
				var supplierName = $(this).val();
				
					if( supplierName == '' )
					{
						$('#_apptivo_supplier').val('');
						$('#_apptivo_regular_price').val('');
						$('p._apptivo_supplier_field').hide();
						$('p._apptivo_regular_price_field').hide();					
					}else{
						if( supplierName != 'add_new_suppliers' )
						{
							$('p._apptivo_supplier_field').hide();
							$('p._apptivo_regular_price_field').show();
						}else{
							$('#_apptivo_supplier').val('');
							$('p._apptivo_supplier_field').show();
							$('p._apptivo_regular_price_field').show();	
						}
					}
				
			});

			$('select#_apptivo_suppliers').live('keydown', function(){		
				var supplierName = $(this).val();
				if( supplierName == '' )
				{
					$('#_apptivo_supplier').val('');
					$('#_apptivo_regular_price').val('');
					$('p._apptivo_supplier_field').hide();
					$('p._apptivo_regular_price_field').hide();					
				}else{
					if( supplierName != 'add_new_suppliers' )
					{
						$('p._apptivo_supplier_field').hide();
						$('p._apptivo_regular_price_field').show();
					}else{
						$('#_apptivo_supplier').val('');
						$('p._apptivo_supplier_field').show();
						$('p._apptivo_regular_price_field').show();	
					}
				}
				
			});
			
		});
		
		</script>
		
	<?php
}
/**
 * Product Data Save
 * 
 * Function for processing and storing all product data.
 */
add_action('apptivo_ecommerce_process_item_meta', 'apptivo_ecommerce_process_item_meta', 1, 2);

function apptivo_ecommerce_process_item_meta( $post_id, $post ) {
	$apptivo_ecommerce_errors = array();
    
	//Item Code should not be empty
	$itemcode = get_post_meta($post_id, '_apptivo_item_code', true);
	$item_code = stripslashes( $_POST['_apptivo_item_code'] );
	
		if (!empty($item_code)) :
			update_post_meta( $post_id, '_apptivo_item_code', $item_code );
		else :
		    $apptivo_ecommerce_errors[] = 'Item code should not be empty';
		    if( !empty($itemcode)) {
		    	$item_code = $itemcode;
		    }else { $item_code = $post_id; } 
			update_post_meta( $post_id, '_apptivo_item_code', $item_code );
			$item_code = $item_code;
		endif;
	
	$sale_Price = stripslashes($_POST['_apptivo_sale_price']);
	if( !preg_match('/^[\d]+(\.[\d]+){0,1}$/',$sale_Price) )
	{
		if( $sale_Price != '') {
		$apptivo_ecommerce_errors[] = 'Sale Price Should be Numeric.';
		}
		$sale_Price = 0.00;
	}
	
	$regulat_Price = stripslashes($_POST['_apptivo_regular_price']);
    if( !preg_match('/^[\d]+(\.[\d]+){0,1}$/', $regulat_Price) )
	{
		if( $regulat_Price != '') {
		$apptivo_ecommerce_errors[] = 'Regular Price Should be Numeric.';
		}
		$regulat_Price = 0.00;
	}
	
    //Supplier Name
    $suppliers = stripslashes(trim($_POST['_apptivo_suppliers'])); //Apptivo Supplier Lists
    $supplierName = stripslashes($_POST['_apptivo_supplier']);
    if( $suppliers != '' && $suppliers != 'add_new_suppliers')
    {
    	$supplierName = $suppliers;
    }else if( $suppliers == 'add_new_suppliers' && $supplierName == '')
    {
    	$supplierName = 'Default';
    }else{ 
    $supplierName = stripslashes($_POST['_apptivo_supplier']);
    }
    
	// Update post meta
	update_post_meta( $post_id, '_apptivo_supplier', $supplierName);
	update_post_meta( $post_id, '_apptivo_regular_price', stripslashes( $regulat_Price ) );
	update_post_meta( $post_id, '_apptivo_sale_price', stripslashes( $sale_Price) );
	update_post_meta( $post_id, '_apptivo_track_color', stripslashes( $_POST['_apptivo_track_color'] ) );
	update_post_meta( $post_id, '_apptivo_track_size', stripslashes( $_POST['_apptivo_track_size'] ) );
	
	
	if ($_POST['_apptivo_featured']) update_post_meta( $post_id, '_apptivo_featured', 'yes' ); else update_post_meta( $post_id, '_apptivo_featured', 'no' );
	if ($_POST['_apptivo_enabled']) update_post_meta( $post_id, '_apptivo_enabled', 'yes' ); else update_post_meta( $post_id, '_apptivo_enabled', 'no' );

	
		
	apply_filters('apptivo_ecommerce_add_ons_save_post',$post_id);
	

	// Save errors
	update_option('apptivo_ecommerce_errors', $apptivo_ecommerce_errors);
	$itemId = get_post_meta($post_id, '_apptivo_item_id', true);	
	if($itemId == ''): 
		$webMethod = 'createItem'; //Item Creation
	else :           
		$webMethod = 'updateItem';  //Item Updation
		$itemManufacturerId = get_post_meta($post_id, '_apptivo_item_manufactured_id', true); //get item manufacturer ID
	endif;
		
    $itemName = $post->post_title;
    $itemDescription = $post->post_content;
    
    $regularPrice = $regulat_Price;
    $salePrice = $sale_Price;
    
    $itemShortDescription = $post->post_excerpt;
    $skuNumber = ''; 
    
    $trackSizes = $_POST['_apptivo_track_size'];
    $trackColors = $_POST['_apptivo_track_color'];
    $enabledForSales = ($_POST['_apptivo_enabled'] == 'on')?true:false;
    $isFeatured =   ($_POST['_apptivo_featured'] == 'on')?true:false;  
    $SSubscription = true; 
    $currencyCode = 'USD';
    $taxonomy = 'item_cat';
    $terms = array();
    $terms=get_the_terms($post->ID,$taxonomy);
    $item_categoryID = array();
    if( !empty($terms)) :
    foreach($terms as $terms_category)
    {
    	$item_category_id =  get_post_meta($terms_category->term_id, '_apptivo_category_id',true);    	
    	if($item_category_id != ''){
    		$item_categoryID[] = $item_category_id;
    	}
    	
    }
    endif;
    $response = createorupdateitem($webMethod,$itemId,$itemManufacturerId,$item_code,$itemName,$itemDescription,$regularPrice,$salePrice,$itemShortDescription,
    							   $skuNumber,$trackSizes,$trackColors,$enabledForSales,$SSubscription,$currencyCode,$item_categoryID,$supplierName,$isFeatured,$post_id);
    if($response->return->itemId != '' )
	{
		update_post_meta($post_id, '_apptivo_item_id', $response->return->itemId);
		update_post_meta($post_id, '_apptivo_item_uom_id', $response->return->itemPrimaryUOMId);
		update_post_meta($post_id, '_apptivo_item_manufactured_id', $response->return->itemManufacturerId);		
	}    
}