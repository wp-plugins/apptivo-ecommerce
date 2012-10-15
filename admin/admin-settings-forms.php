<?php
/*
 * Update eCommerce order number.
 */
function apptivo_ecommerce_update_order_number(){
	if(!isset($_POST) || !$_POST) return false;

	$prefix = stripslashes($_POST['apptivo_ecommerce_ordernumber_prefix']);
	$startsWidth = stripslashes($_POST['apptivo_ecommerce_ordernumber_startswith']);
	$configure_order = configureOrderNumberGeneration($prefix,$startsWidth);
	if($configure_order->return->responseCode == 1000 )
	{
		update_option('apptivo_ecommerce_ordernumber_prefix', $prefix);
		update_option('apptivo_ecommerce_ordernumber_startswith', $startsWidth);
		
	}
	return true;
}
/*
 * eCommerce update options
 */
function apptivo_ecommerce_update_options($options) {
    if(!isset($_POST) || !$_POST) return false;  
    foreach ($options as $value) {
    	if (isset($value['type']) && $value['type']=='checkbox') :
            if(isset($value['id']) && isset($_POST[$value['id']])) {
            	update_option($value['id'], 'yes');
            } else {
                update_option($value['id'], 'no');
            }
        elseif (isset($value['type']) && $value['type']=='textarea') :            
         if(isset($value['id']) && isset($_POST[$value['id']])) {
         	   	update_option($value['id'], stripslashes($_POST[$value['id']]));
            } else {
                delete_option($value['id']);
            }            
        elseif (isset($value['type']) && $value['type']=='image_width') :
            if(isset($value['id']) && isset($_POST[$value['id'].'_width'])) {
              	update_option($value['id'].'_width', apptivo_ecommerce_clean($_POST[$value['id'].'_width']));
            	update_option($value['id'].'_height', apptivo_ecommerce_clean($_POST[$value['id'].'_height']));
				if (isset($_POST[$value['id'].'_crop'])) :
					update_option($value['id'].'_crop', 1);
				else :
					update_option($value['id'].'_crop', 0);
				endif;
            } else {
                update_option($value['id'].'_width', $value['std']);
            	update_option($value['id'].'_height', $value['std']);
            	update_option($value['id'].'_crop', 1);
            }	
    	else :
    		if(isset($value['id']) && isset($_POST[$value['id']])) {
            	update_option($value['id'], apptivo_ecommerce_clean($_POST[$value['id']]));
            } else {
                delete_option($value['id']);
            }        
        endif;
    }
    return true;
}

/**
 * eCommerce admin fields.
*/
function apptivo_ecommerce_admin_fields($options) {
	  
    foreach ($options as $value) :
        switch($value['type']) :
        
            case 'title':
            	if (isset($value['name']) && $value['name']) echo '<h3>'.$value['name'].'</h3>'; 
            	if (isset($value['desc']) && $value['desc']) echo wpautop(wptexturize($value['desc']));
            	echo '<table class="form-table">'. "\n\n";
            	if (isset($value['id']) && $value['id']) do_action('apptivo_ecommerce_settings_'.sanitize_title($value['id']));
            break;
            
            case 'sectionend':
            	if (isset($value['id']) && $value['id']) do_action('apptivo_ecommerce_settings_'.sanitize_title($value['id']).'_end');
            	echo '</table>';
            break;
            
            case 'text':
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                    <td class="forminp">
                    <input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="<?php echo esc_attr( $value['type'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" value="<?php if ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) { echo esc_attr( stripslashes( get_option($value['id'] ) ) ); } else { echo esc_attr( $value['std'] ); } ?>" /> 
                    <?php if( $value['upload'] == 'yes') :
                    ?>
                    <input id="upload_image_button" class="button-primary upload_image_button" type="button" value="Upload Logo" rel="<?php echo esc_attr( $value['id'] ); ?>" />
                     <?php endif; ?>
                    
                    <span class="description"><?php echo $value['desc']; ?></span></td>
                </tr><?php
            break;
                     
             case 'template_upload' :             	
            	?>
            	<span id="update_template_ver" style="color:#f00;"><?php echo $updateversion; ?></span>
            	<tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                    <td class="forminp">
                    <a href="javascript:void(0);"><span id="apptivo_ecommerce_upload_template"> Copy To Theme </span> </a>&nbsp;&nbsp;&nbsp;<span class="description"><?php echo $value['desc']; ?></span>
                    
                    </td>
                </tr>
                
                <?php
            break;
            
            case 'image_width' :
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                    	
                    	<input title="Width" name="<?php echo esc_attr( $value['id'] ); ?>_width" id="<?php echo esc_attr( $value['id'] ); ?>_width" type="text" size="3" value="<?php if ( $size = get_option( $value['id'].'_width') ) echo stripslashes($size); else echo $value['std']; ?>" /> 
                    	
                    	<input title="Height" name="<?php echo esc_attr( $value['id'] ); ?>_height" id="<?php echo esc_attr( $value['id'] ); ?>_height" type="text" size="3" value="<?php if ( $size = get_option( $value['id'].'_height') ) echo stripslashes($size); else echo $value['std']; ?>" /> 
                    	
                    	<label><?php _e('Hard Crop', 'apptivo_ecommerce'); ?> <input name="<?php echo esc_attr( $value['id'] ); ?>_crop" id="<?php echo esc_attr( $value['id'] ); ?>_crop" type="checkbox" <?php if (get_option( $value['id'].'_crop')!='') checked(get_option( $value['id'].'_crop'), 1); else checked(1); ?> /></label> 
                    	
                    	<span class="description"><?php echo $value['desc'] ?></span></td>
                </tr><?php
            break;
            
            case 'select':
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>">
                        <?php
                        foreach ($value['options'] as $key => $val) {
                        ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php if (get_option($value['id']) == $key) { ?> selected="selected" <?php } ?>><?php echo ucfirst($val) ?></option>
                        <?php
                        }
                        ?>
                       </select> <span class="description"><?php echo $value['desc'] ?></span>
                    </td>
                </tr><?php
            break;
            
            case 'checkbox' :
            
            	if (!isset($value['checkboxgroup']) || (isset($value['checkboxgroup']) && $value['checkboxgroup']=='start')) :
            		?>
            		<tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
					<td class="forminp">
					<?php
            	endif;
            	
            	?>
	            <fieldset><legend class="screen-reader-text"><span><?php echo $value['name'] ?></span></legend>
					<label for="<?php echo $value['id'] ?>">
					<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="checkbox" value="1" <?php checked(get_option($value['id']), 'yes'); ?> />
					<?php echo $value['desc'] ?></label><br>
				</fieldset>
				<?php
				
				if (!isset($value['checkboxgroup']) || (isset($value['checkboxgroup']) && $value['checkboxgroup']=='end')) :
					?>
						</td>
					</tr>
					<?php
				endif;
				
            break;
            
            case 'textarea':
            	?>            	
<tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                    <?php  if($value['editor'] == 'yes' ) : ?>
                    <div style="width:620px;">
                        <?php if (get_option($value['id'])) 
                        { 
                        	$content_textarea =  get_option($value['id']);
                        } else {
                        	$content_textarea =   $value['std'] ;
                        } ?>
                        
					  <?php the_editor($content_textarea, $value['id'] ,'',FALSE);  ?>
					   </div>
					   <?php else: ?>
                        <textarea <?php if ( isset($value['args']) ) echo $value['args'] . ' '; ?>name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>"><?php if (get_option($value['id'])) echo esc_textarea(stripslashes(get_option($value['id']))); else echo esc_textarea( $value['std'] ); ?></textarea> <span class="description"><?php echo $value['desc'] ?></span>
                       <?php endif; ?>					
                    </td>
                </tr><?php
            break;
            
            case 'single_select_page' :
            	$page_setting = (int) get_option($value['id']);
            	
            	$args = array( 'name'	=> $value['id'],
            				   'id'		=> $value['id']. '" style="width: 200px;',
            				   'sort_column' 	=> 'menu_order',
            				   'sort_order'		=> 'ASC',
            				   'selected'		=> $page_setting);
            	
            	if( isset($value['args']) ) $args = wp_parse_args($value['args'], $args);
            	
            	?><tr valign="top" class="single_select_page">
                    <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
			        	<?php wp_dropdown_pages($args); ?> <span class="description"><?php echo $value['desc'] ?></span>        
			        </td>
               	</tr><?php	
            break;
         
			default:
				?>
				<tr valign="top" class="multi_select_countries">
					<th scope="row" class="titledesc"><b><?php echo $value['name'] ?></b></th>
					<td>&nbsp;</td>
			    </tr>
				<?php 
			break;
           
        endswitch;
    endforeach;
}