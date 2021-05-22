<?php

/*
 * Plugin Name: Custom WP Profile Image No Gravatar
 * Description: Super simple way to use your own custom images for your profile author images instead of the default WordPress functionality Gravatar
 * Version: 0.1
 * Author: Samuel East
 * Author URI: https://samueleast.com
 * Tested up to: 5.7
 * Text Domain: seap-languages
 * Domain Path: languages
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/ 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 

/**
 * Important needed to allow the profile to upload files
 *
 * @param string #message
 * @return string
 * @access public
 * @since 1.0
 */
function seap_avatar_accept_uploads() {

    echo ' enctype="multipart/form-data"';

}

add_action('user_edit_form_tag', 'seap_avatar_accept_uploads');

/**
 * Replaces the add gravatar text with a file input
 *
 * @param array $user
 * @return string
 * @access public
 * @since 1.0
 */
function seap_avatar_profile_fields( $user ) {

	?>

    <script type="text/javascript">
    	
    	jQuery(function($) {

			$('.user-profile-picture .description').html('<input type="file" name="seap_avatar" accept="image/png, image/jpeg" />');

		});
    	

    </script>

	<?php

}

add_action( 'show_user_profile', 'seap_avatar_profile_fields' );
add_action( 'edit_user_profile', 'seap_avatar_profile_fields' );

/**
 * Saves the uploaded user profile image to the media library
 *
 * @param $user_id
 * @return null
 * @access public
 * @since 1.0
 */
function seap_avatar_save_profile_fields($user_id){

    if (!current_user_can( 'edit_user', $user_id)){

        return false;
    }

    if(!empty($_FILES['seap_avatar']['name'])) {
         
        $supported_types = array('image/jpg', 'image/jpeg', 'image/png');
         
        $arr_file_type = wp_check_filetype(basename($_FILES['seap_avatar']['name']));
        
        $uploaded_type = $arr_file_type['type'];
         
        if(in_array($uploaded_type, $supported_types)) {
 
            $upload = wp_upload_bits($_FILES['seap_avatar']['name'], null, file_get_contents($_FILES['seap_avatar']['tmp_name']));
     
            if(isset($upload['error']) && $upload['error'] != 0) {
 
                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
 
            } else {

                update_user_meta( $user_id, 'seap_avatar', $upload);     
 
            } 
 
        } else {
 
            wp_die("The file type that you've uploaded is not allowed.");
 
        } 
         
    }

}

add_action( 'personal_options_update', 'seap_avatar_save_profile_fields' );
add_action( 'edit_user_profile_update', 'seap_avatar_save_profile_fields' );

/**
 * Switched the default profile image with your uploaded one
 *
 * @param array $args $id_or_email
 * @return $args
 * @access public
 * @since 1.0
 */
function seap_change_avatar($args, $id_or_email) {

	if (filter_var($id_or_email, FILTER_VALIDATE_EMAIL)){
  		
  		$user = get_user_by('email', $id_or_email);

		$user_id = $user->ID;

	}else{

		$user_id = $id_or_email;

	}

    $seap_avatar = get_user_meta($user_id, 'seap_avatar', true);
	
	if(!empty($seap_avatar['url'])){

		$args['url'] = parse_url($seap_avatar['url'], PHP_URL_PATH);

	}

    return $args;

} 

add_filter('get_avatar_data', 'seap_change_avatar', 100, 2);

/**
 * Important needed to allow the profile to upload files
 *
 * @param string #message
 * @return string
 * @access public
 * @since 1.0
 */
function seap_avatar_woo_accept_uploads() {
   
   echo 'enctype="multipart/form-data"';

}

add_action( 'woocommerce_edit_account_form_tag', 'seap_avatar_woo_accept_uploads' );

/**
 * Add a file input to the Woocommerce account profile
 *
 * @param null
 * @return string
 * @access public
 * @since 1.0
 */
function seap_avatar_woo_account_fields() {
   
   		echo get_avatar( get_current_user_id(), 180 );

   ?>
 
   	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">

   		<label for="seap_avatar">Profile Image</label>
   	
   		<input class="woocommerce-Input woocommerce-Input--text input-text" type="file" name="seap_avatar" accept="image/png, image/jpeg" />

   		<span>
   			<em>Hear you can upload a custom profile image to show throughout this site</em>
   		</span>
   		
   	</p>
    
   <?php
       
}

add_action( 'woocommerce_edit_account_form_start', 'seap_avatar_woo_account_fields' );

/**
 * Saves the uploaded proifle file
 *
 * @param null
 * @return string
 * @access public
 * @since 1.0
 */ 
function seap_avatar_woo_save_account_fields( $form_fields ) {

	$user_id = get_current_user_id();

    if(!empty($_FILES['seap_avatar']['name'])) {
         
        $supported_types = array('image/jpg', 'image/jpeg', 'image/png');
         
        $arr_file_type = wp_check_filetype(basename($_FILES['seap_avatar']['name']));
        
        $uploaded_type = $arr_file_type['type'];
         
        if(in_array($uploaded_type, $supported_types)) {
 
            $upload = wp_upload_bits($_FILES['seap_avatar']['name'], null, file_get_contents($_FILES['seap_avatar']['tmp_name']));
     
            if(isset($upload['error']) && $upload['error'] != 0) {
 
                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
 
            } else {

                update_user_meta( $user_id, 'seap_avatar', $upload);     
 
            } 
 
        } else {
 
            wp_die("The file type that you've uploaded is not allowed.");
 
        } 
         
    }

}

add_action( 'woocommerce_save_account_details_required_fields', 'seap_avatar_woo_save_account_fields', 1 );