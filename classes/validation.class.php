<?php
class apptivo_ecommerce_validation {
	
	function is_email( $email ) {
		return is_email( $email );
	}
	
	function is_phone( $phone ) {	
	if (preg_match('/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/', $phone)) {
      return true;
	} else {
	  return false;
	}
		
	}
	
	function is_password($password){
		if(strlen(trim($password)) < 8)return false;
		return true;
	}
	
	function zipcode_isvalid( $postcode, $country ) {
		
		if (strlen(trim(preg_replace('/[\s\-A-Za-z0-9]/', '', $postcode)))>0) return false;
		
		if ($country == 'US') :
			return $this->validateUSAZip( $postcode );
		endif;
		
		return true;
	}
	
	
	function validateUSAZip($zip_code)
	{
	  if(preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$zip_code))
	    return true;
	  else
	    return false;
	}

	function zipcode_format( $postcode, $country ) {
		$postcode = strtoupper(trim($postcode));
		$postcode = trim(preg_replace('/[\s]/', '', $postcode));
		
		if ($country=='GB') :
			if (strlen($postcode)==7) 
				$postcode = substr_replace($postcode, ' ', 4, 0);
			else 
				$postcode = substr_replace($postcode, ' ', 3, 0);
		endif;
		return $postcode;
	}
}