<?php
/**
 * @class 		apptivo_ecommerce
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 */
class apptivo_ecommerce {
	
	var $_cache;
	var $errors = array(); 
	var $messages = array();
	
	var $plugin_url;
	var $plugin_path;
	
	// Class instances
	var $cart;
	var $payment_gateways;
	var $countries;
		
	/** constructor */
	function __construct() {
		
		// Load class instances
		$this->cart 			= &new apptivo_ecommerce_cart();				// Cart class, stores the cart contents
		$this->payment_gateways = &new apptivo_ecommerce_payment_gateways();	// Payment gateways class. loads and stores payment methods
		$this->countries 		= &new apptivo_ecommerce_countries();			// Countries class
		// Load messages
		$this->load_messages();
		// Hooks
		add_filter('wp_redirect', array(&$this, 'redirect'), 1, 2);
		//Payment gateways
		add_action('plugins_loaded', array( &$this->payment_gateways, 'init' ), 1); 	// Load payment methods - some may be added by plugins
	}

		/*** Get the plugin url */
		function plugin_url() { 
			if($this->plugin_url) return $this->plugin_url;
			
			if (is_ssl()) :
				return $this->plugin_url = str_replace('http://', 'https://', WP_PLUGIN_URL) . "/" . plugin_basename( dirname(dirname(__FILE__))); 
			else :
				return $this->plugin_url = WP_PLUGIN_URL . "/" . plugin_basename( dirname(dirname(__FILE__))); 
			endif;
		}
		
		/**
		 * Get the plugin path
		 */
		function plugin_path() { 	
			if($this->plugin_path) return $this->plugin_path;
			return $this->plugin_path = WP_PLUGIN_DIR . "/" . plugin_basename( dirname(dirname(__FILE__))); 
		 }
		
        /** Return the URL with https if SSL is on */
		function force_ssl( $url ) { 	
			if (is_ssl()) $url = str_replace('http:', 'https:', $url);
			return $url;
		 }
		
		/*** Get an image size  */
		function get_image_size( $image_size ) {
			$return = '';
			switch ($image_size) :
				case "product_thumbnail_image_width" : $return = get_option('apptivo_ecommerce_thumbnail_image_width'); break;
				case "product_thumbnail_image_height" : $return = get_option('apptivo_ecommerce_thumbnail_image_height'); break;
				case "product_catalog_image_width" : $return = get_option('apptivo_ecommerce_catalog_image_width'); break;
				case "product_catalog_image_height" : $return = get_option('apptivo_ecommerce_catalog_image_height'); break;
				case "product_single_image_width" : $return = get_option('apptivo_ecommerce_single_image_width'); break;
				case "product_single_image_height" : $return = get_option('apptivo_ecommerce_single_image_height'); break;
			endswitch;
			return apply_filters( 'apptivo_ecommerce_get_image_size_'.$image_size, $return );
		}
	
	    /*** Load Messages */
		function load_messages() { 
			if (isset($_SESSION['errors'])) $this->errors = $_SESSION['errors'];
			if (isset($_SESSION['messages'])) $this->messages = $_SESSION['messages'];
			
			unset($_SESSION['messages']);
			unset($_SESSION['errors']);
		}

		/*** Add an error*/
		function add_error( $error,$key='' ) { $this->errors[] = $error.'<input class="'.$key.'" type="hidden" id="ecommerce_error_field" value="'.$key.'" />'; }
		
		/**
		 * Add a message
		 */
		function add_message( $message ) { $this->messages[] = $message; }
		
		/** Clear messages and errors from the session data */
		function clear_messages() {
			$this->errors = $this->messages = array();
			unset($_SESSION['messages']);
			unset($_SESSION['errors']);
		}
		
		/*** Get error count		 */
		function error_count() { return sizeof($this->errors); }
		
		/*** Get message count		 */
		function message_count() { return sizeof($this->messages); }
		
		/** Output the errors and messages */
		function show_messages() {
		
			if (isset($this->errors) && sizeof($this->errors)>0) :
				
			$single_err_msg = get_option('apptivo_ecommerce_single_error_message');
			if( $single_err_msg == 'yes'){
			    echo '<div class="apptivo_ecommerce_error">'.$this->errors[0].'</div>';
			}else {	
				echo '<ul class="apptivo_ecommerce_error">';
				 foreach ( $this->errors as $error ) :
				   echo '<li><span>'.$error.'</span></li>';
			     endforeach; 
			    echo '</ul>';
			}	
				$this->clear_messages();
				return true;
			elseif (isset($this->messages) && sizeof($this->messages)>0) :
				echo '<div class="apptivo_ecommerce_message">'.$this->messages[0].'</div>';
				$this->clear_messages();
				return true;
			else :
				return false;
			endif;
		}

		function redirect( $location, $status ) {
			$_SESSION['errors'] = $this->errors;
			$_SESSION['messages'] = $this->messages;
			return $location;
		}
		
	
		function nonce_field ($action, $referer = true , $echo = true) { return wp_nonce_field('apptivo_ecommerce-' . $action, '_n', $referer, $echo); }
		
		function nonce_url ($action, $url = '') { return add_query_arg( '_n', wp_create_nonce( 'apptivo_ecommerce-' . $action ), $url); }
		
		function verify_nonce($action, $method='_POST', $error_message = false) {
			
			$name = '_n';
			$action = 'apptivo_ecommerce-' . $action;

			if( $error_message === false ) $error_message = __('Action failed. Please refresh the page and retry.', 'apptivo_ecommerce'); 
			
			if(!in_array($method, array('_GET', '_POST', '_REQUEST'))) $method = '_POST';
			
			if ( isset($_REQUEST[$name]) && wp_verify_nonce($_REQUEST[$name], $action) ) return true;
			
			if( $error_message ) $this->add_error( $error_message );
			
			return false;
		}
	
		/* Cache API  */
		function cache ( $id, $data, $args=array() ) {
			if( ! isset($this->_cache[ $id ]) ) $this->_cache[ $id ] = array();
			if( empty($args) ) $this->_cache[ $id ][0] = $data;
			else $this->_cache[ $id ][ serialize($args) ] = $data;
			return $data;			
		}
		function cache_get ( $id, $args=array() ) {
			if( ! isset($this->_cache[ $id ]) ) return null;
			if( empty($args) && isset($this->_cache[ $id ][0]) ) return $this->_cache[ $id ][0];
			elseif ( isset($this->_cache[ $id ][ serialize($args) ] ) ) return $this->_cache[ $id ][ serialize($args) ];
		}
		/* Shortcode cache */
		function shortcode_wrapper($function, $atts=array()) {
			if( $content = $this->cache_get( $function . '-shortcode', $atts ) ) return $content;
			ob_start();
			call_user_func($function, $atts);
			return $this->cache( $function . '-shortcode', ob_get_clean(), $atts);
		}
}