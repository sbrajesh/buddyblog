<?php
/**
 * Used for Image field type
 */
class OptionsBuddy_Settings_Field_BuddyDevaccount extends OptionsBuddy_Settings_Field {
    
    
    public function __construct( $field ) {
		
        parent::__construct( $field );
    }
    
    
    public function render( $args ) {
        $value = $args['value'];
       	$value = array_filter( $value );
		$username = $access_key = '';
		if( !empty( $value ) && is_array( $value ) ) {
			//print_r($value);
			$username = $value['username'];
			$access_key = $value['access_key'];
		}
				
		$name = $args['option_key'];
		
		$size = 'normal';
		echo "<h4>BuddyDev Account Details</h3>";
		$valid_message = get_site_option(  'buddydev_account_validated_message', 0 );
		?>
		<?php if( $valid_message ):?>
			<div id='buddydev-account-error' style='background: #FFCD00; padding: 15px; color: #333;'>
				<p><?php echo $valid_message;?></p>
			</div>
		<?php endif;?>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="mailserver_url">BuddyDev Username</label></th>
					<td><?php printf( '<input type="text" class="%1$s-text" id="%2$s" name="%2$s" value="%3$s"/>', $size, $name.'[username]', $username );?></td>
				</tr>
				<tr>
					<th scope="row"><label for="mailserver_url">Access Key</label></th>
					<td><?php printf( '<input type="text" class="%1$s-text" id="%2$s" name="%2$s" value="%3$s"/>', $size, $name.'[access_key]', $access_key ); ?></td>
				</tr>
			</tbody>
		</table>

		<?php
        
    }
}

function buddydevaccount_validate( $data ) {
	
	$empty = array( '', '' );
	
	if( empty( $data['access_key'] ) || empty( $data['username'] ) ){
		return $data;//invalid
	}
	
	$api = buddydev_dashboad() ->get_api_base_url() .'validate-account/';
	
	$options = array(
		'body'	=> array(
			'username'	=> $data['username'],
			'access_key'=> $data['access_key'],
		)
	);
	
	$response = wp_remote_post( $api, $options );
	
	if( is_wp_error( $response  ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
		return $data;
	}
	
	$response = wp_remote_retrieve_body( $response );
	
	if( ! empty( $response ) ) {
		$response = json_decode( $response );
		
		
		update_site_option( 'buddydev_account_validated_message', esc_html( $response->message ) );
		
		if( $response->is_valid == 1 ) {
			return $data;
		}
		
		//buddydev_dashboad()->add_notice( 'account_validate', $response['message'] );
	}
	
	return $data;
}
