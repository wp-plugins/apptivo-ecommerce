<?php
/**
 * Create or Update Category (Apptivo Web Methods:createItemCategory,updateItemCategory)
 */
add_action( 'admin_enqueue_scripts', 'apptivo_ecommerce_enqueue' );
function apptivo_ecommerce_enqueue($hook){
	if($hook == 'edit-tags.php') :
	 $sync_status = get_option('apptivo_ecommerce_sync_status');
	 if( $_GET['taxonomy'] == 'item_cat' && $sync_status == 'yes') :
	  $get_all_terms = get_terms( 'item_cat', 'orderby=count&hide_empty=0' );
      if(!empty($get_all_terms)) {
	  foreach($get_all_terms as $terms)
		{
			$arg = array('description' => $terms->description, 'parent' => $terms->parent,'name'=>$terms->name,'slug'=>$terms->slug);
			remove_action('edited_term', 'apptivo_ecommerce_update_category', 10,3);
		    $update_items = wp_update_term( $terms->term_id, 'item_cat',$arg );
		    update_option('apptivo_ecommerce_sync_status', 'no'); 
		    break;
		}	  
      }
	 endif;
	endif;
}

add_action('created_term', 'apptivo_ecommerce_create_category', 10,3);
add_action('edited_term', 'apptivo_ecommerce_update_category', 10,3);
/*
 * Create category
 */
function apptivo_ecommerce_create_category( $term_id, $tt_id, $taxonomy ) 
{
	if( $taxonomy != 'item_cat' ) return;
	
	$category_Id = '';
	$category_Id = get_post_meta($term_id, '_apptivo_category_id',true);
	$categories = get_cat_slug($term_id);
	$categoryDescription = $categories->description;
	$categoryName = $categories->name;
	$parent_categoryid = get_post_meta($categories->parent, '_apptivo_category_id',true);
		  
	$categoryDescription = $_POST['description'];
	
	$itemCategoryDetails = array("description"=>$categoryDescription,
								"displaySaleInvItemsOnly"=>null,
								"displaySequence"=>0,
								"enabledCheckBox"=>'Y',
								"itemCategoryId"=>$category_Id,
								"itemCategoryName"=>$categoryName,
								"itemTypeCode"=>null,
	                            "parentCategoryId" => $parent_categoryid
									);
									
    $webMethod =  ($category_Id == '')?'createItemCategory':'updateItemCategory';
	$params = array("arg0"=> APPTIVO_ECOMMERCE_API_KEY,"arg1"=> APPTIVO_ECOMMERCE_ACCESSKEY,"arg2"=> $itemCategoryDetails);

	$response = ecommerce_soap_call(ITEM_WSDL,$webMethod,$params);
	if ( $response  == 'E_100')
	{
	    wp_delete_term( $term_id, 'item_cat' ); //Delete Categories
		if( trim($webMethod) == 'createItemCategory'){
			echo 'Error in creating categories.';exit;
		}else if( trim($webMethod) == 'updateItemCategory'){
			echo 'Error in creating categories';exit;
		}
	}
	$categoryID = $response->return->itemCategoryId; //get category from response.
	if($categoryID != '')
	{
		update_post_meta($term_id, '_apptivo_category_id', $categoryID);
	}
	 
}
/**
 * Update category
*/
function apptivo_ecommerce_update_category( $term_id, $tt_id, $taxonomy ) 
{
	if( $taxonomy != 'item_cat' ) return;
    $parent_categoryid = get_post_meta($_POST['parent'], '_apptivo_category_id',true);
	$category_Id = '';
	$category_Id = get_post_meta($term_id, '_apptivo_category_id',true);	
	$categories = get_cat_slug($term_id);
	$categoryDescription = $categories->description;
	$categoryName = $categories->name;	  
	$categoryDescription = $_POST['description'];
	
	$itemCategoryDetails = array("description"=>$categoryDescription,
								 "displaySaleInvItemsOnly"=>null,
								 "displaySequence"=>0,
								 "enabledCheckBox"=>'Y',
								 "itemCategoryId"=>$category_Id,
								 "itemCategoryName"=>$categoryName,
								 "itemTypeCode"=>null,
	                             "parentCategoryId" => $parent_categoryid
								 );
    $webMethod =  ($category_Id == '')?'createItemCategory':'updateItemCategory';
	$params = array("arg0"=> APPTIVO_ECOMMERCE_API_KEY,"arg1"=> APPTIVO_ECOMMERCE_ACCESSKEY, "arg2"=> $itemCategoryDetails);
	$response = ecommerce_soap_call(ITEM_WSDL,$webMethod,$params);
	if( $response == 'E_100') {
		return 'E_100';
	}
	
	$categoryID = $response->return->itemCategoryId; //get category from response.
    if($categoryID != '')
	{
		update_post_meta($term_id, '_apptivo_category_id', $categoryID);
	}
}
/*
 * get slug for category name
 */
function get_cat_slug($value) {
	$term = get_term( (int) $value, 'item_cat');
	return $term;
}