<?php
/**
 * Apptivo Ecommerce Payment Gateway class
 * 
 * @class 		apptivo_ecommerce_payment_gateway
 * @category	Payment Gateways
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
class apptivo_ecommerce_payment_gateway {
	
	var $id;
	var $title;
	var $enabled;
	var $icon;
	var $description;	
	var $plugin_id = 'apptivo_ecommerce_';
	var $settings = array();	
	var $form_fields = array();
	var $sanitized_fields = array();

	function is_available() {
		if ($this->enabled=="yes") :
			return true;
		endif;	
	 return false;
	}
	
	function icon() {
		global $apptivo_ecommerce;
		if ($this->icon) :
			return '<img src="'. $apptivo_ecommerce->force_ssl($this->icon).'" alt="'.$this->title.'" />';
		endif;
	}
	
	function admin_options() {}
	
	
	function process_payment() {}
	
	
	function validate_fields() { return true; }
	
	
    public function process_admin_options() {
    	$this->validate_settings_fields();
    	update_option( $this->plugin_id . $this->id . '_settings', $this->sanitized_fields );
    }
    
    function init_settings () {
    	if ( ! is_array( $this->settings ) ) { return; }
    	
    	$settings = array();
    	$existing_settings = get_option( $this->plugin_id . $this->id . '_settings' );
    	
    	$defaults = array();
    	
    	foreach ( $this->form_fields as $k => $v ) {
    		if ( isset( $v['default'] ) ) {
    			$defaults[$k] = $v['default'];
    		} else {
    			$defaults[$k] = '';
    		}
    	}
    	
    	if ( ! $existing_settings ) {
    		$existing_settings = $defaults;
    	} else {
    		// Prevent "undefined index" errors.
    		foreach ( $existing_settings as $k => $v ) {
    			if ( ! isset( $existing_settings[$k] ) ) {
    				$existing_settings[$k] = $v;
    			}  
    		}
    	}
    	
    	$this->settings = $existing_settings;
    	
    	if ( isset( $this->settings['enabled'] ) && ( $this->settings['enabled'] == 'yes' ) ) { $this->enabled = 'yes'; }
    } // End init_settings()
    
    
    function generate_settings_html () {
    	$html = '';
    	
    	foreach ( $this->form_fields as $k => $v ) {
    		if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) ) { $v['type'] == 'text'; } // Default to "text" field type.
    		
    		if ( method_exists( $this, 'generate_' . $v['type'] . '_html' ) ) {
    			$html .= $this->{'generate_' . $v['type'] . '_html'}( $k, $v );
    		}
    	}
    	
    	echo $html;
    } 
    
    function generate_text_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<input class="input-text wide-input" type="text" name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" style="min-width:50px;" value="' . esc_attr($this->settings[$key]) . '" />';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' .$data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } 
    
    function generate_textarea_html( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<textarea rows="3" cols="20" class="input-text wide-input" name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" style="width:100%;">'.esc_attr($this->settings[$key]).'</textarea>';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } 
    function generate_checkbox_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) $title = $data['title'];
    	if ( isset( $data['label'] ) && $data['label'] != '' ) $label = $data['label']; else $label = $data['title'];
    	    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<input name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" type="checkbox" value="1" ' . checked( $this->settings[$key], 'yes', false ) . ' /> ' . $label . '</label><br />' . "\n";
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } 
    function validate_settings_fields () {
    	foreach ( $this->form_fields as $k => $v ) {
    		if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) ) { $v['type'] == 'text'; } // Default to "text" field type.
    		
    		if ( method_exists( $this, 'validate_' . $v['type'] . '_field' ) ) {
    			$field = $this->{'validate_' . $v['type'] . '_field'}( $k );
    			$this->sanitized_fields[$k] = $field;
    		} else {
    			$this->sanitized_fields[$k] = $this->settings[$k];
    		}
    	}
    } 
    
    function validate_checkbox_field ( $key ) {
    	$status = 'no';
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) && ( 1 == $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$status = 'yes';
    	}
    	
    	return $status;
    } 
    
    function validate_text_field ( $key ) {
    	$text = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$text = esc_attr( apptivo_ecommerce_clean( $_POST[$this->plugin_id . $this->id . '_' . $key] ) );
    	}
    	
    	return $text;
    }
    
    function validate_textarea_field ( $key ) {
    	$text = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$text = esc_attr( apptivo_ecommerce_clean( $_POST[$this->plugin_id . $this->id . '_' . $key] ) );
    	}
    	
    	return $text;
    } 
}