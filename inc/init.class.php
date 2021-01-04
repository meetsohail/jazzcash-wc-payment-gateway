<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JazzCash_WC_Payment_Gateway extends WC_Payment_Gateway 
{
	protected $type = 'MPAY'; 

	protected $test_endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
	
	protected $live_endpoint = 'https://production.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

	public function __construct()
    {
		$this->id = JAZZCASH_ID;
		$this->icon = JAZZCASH_DIR_PATH_IMAGES."jazzcash-logo-200x200.png";
		$this->has_fields = true;
		$this->method_title = "JazzCash";
		$this->method_description = __("JazzCash Payment Gateway gets through jazzcash account or any credit card to enter their payment information.	", 'jazzcash_woocommerce_integration_gateway');
		$this->title = __( "JazzCash Payment Gateway", 'jazzcash_woocommerce_integration_gateway' );
		
		$this->supports = array(
			'products'
		);	
		$this->init_form_fields();

		$this->init_settings();
		
		$this->description = $this->get_option( 'description' );
		$this->enabled = $this->get_option( 'enabled' );
		$this->testmode = 'yes' === $this->get_option( 'testmode' );
		$this->merchant_id = $this->get_option( 'merchant_id' );
		$this->password = $this->get_option( 'password' );
		$this->integenty_salt =  $this->get_option( 'integenty_salt' );
		$this->return_url =  $this->get_option( 'return_url' );
		
		add_action( 'wp_enqueue_scripts', array($this, 'jazzcash_wc_adding_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'jazzcash_wc_adding_styles') );
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this,
		'process_admin_options'));
		add_action('woocommerce_after_checkout_validation', array($this,'jazzcash_wc_checkout_field_validation'));
		add_action( 'woocommerce_admin_order_data_after_order_details', array($this, 'jazzcash_wc_display_order_data_in_admin') );
		add_action('woocommerce_checkout_update_order_meta', array($this, 'jazzcash_wc_custom_checkout_field_update_order_meta'));
					
	}
	// display the extra data in the order admin panel
	function jazzcash_wc_display_order_data_in_admin( $order )
	{  
		if(get_post_meta( $order->get_id(), 'jazzcash_mobile_account', true ))
		{
		?>
		<div class="order_data_column">
			<h4><?php _e( 'Paid Account' ); ?>:</h4>
			<?php 
				echo '<p><strong>' . __( 'Mobile Account' ) . ':</strong> ' . get_post_meta( $order->get_id(), 'jazzcash_mobile_account', true ) . '</p>';
				echo '<p><strong>' . __( 'Cnic' ) . '</strong> <small>(Last 6 Digits)</small>: ' . get_post_meta( $order->get_id(), 'jazzcash_cnic', true ) . '</p>'; ?>
		</div>
		<?php
		} 
	}

	public function process_payment( $order_id ) 
	{
		global $woocommerce;
		$customer_order = new WC_Order($order_id);
		
		$_MerchantID    = $this->merchant_id;
		$_Password      = $this->password;
		$_ReturnURL     = $this->return_url;
		$_IntegritySalt = $this->integenty_salt;
		$_ExpiryHours   = $this->expiryHours;
		$_PhoneNumber   = sanitize_text_field($_POST['phone_number']);
		$_CnicNumber	= sanitize_text_field($_POST['cnic']);
		$items = $customer_order->get_items();
		$product_name  = array();
		foreach ( $items as $item ) {
			array_push($product_name, $item['name']);
		}
		$_Description   = implode(", ", $product_name);
		$_TxnType = get_post_meta( $order_id, $this->type, true );
		$_Language      = 'EN';
		$_Version       = '1.1';
		$_Currency      = 'PKR';
		$_BillReference = $customer_order->get_order_number();
		$_AmountTmp = $customer_order->order_total*100;
		$_AmtSplitArray = explode('.', $_AmountTmp);
		$_FormattedAmount = $_AmtSplitArray[0];
		
		date_default_timezone_set("Asia/karachi");
		$DateTime       = new DateTime();
		$_TxnRefNumber_WM  = "T" . $DateTime->format('YmdHisu');
		$_TxnRefNumber = substr($_TxnRefNumber_WM, 0, -3); // TxnRefNumber with mili seconds (updated)
		
		$_TxnDateTime   = $DateTime->format('YmdHis');
		$ExpiryDateTime = $DateTime;
		$ExpiryDateTime->modify('+' . $_ExpiryHours . ' hours');
		$_ExpiryDateTime = $ExpiryDateTime->format('YmdHis');
		
		$ppmpf1 = '1';
		$ppmpf2 = '2';
		$ppmpf3 = '3';
		$ppmpf4 = '4';
		$ppmpf5 = '5';
		
		 // Populating Sorted Array
		$SortedArrayOld = $_IntegritySalt . '&' . $_FormattedAmount . '&' . $_BillReference . '&' . $_Description . '&' . $_Language . '&' . $_MerchantID . '&' . $_Password;
		$SortedArrayOld = $SortedArrayOld . '&' . $_ReturnURL . '&' . $_Currency . '&' . $_TxnDateTime . '&' . $_ExpiryDateTime  . '&' . $_TxnRefNumber . '&' . $_TxnType . '&' . $_Version;
		$SortedArrayOld = $SortedArrayOld . '&' . $ppmpf1 . '&' . $ppmpf2 . '&' . $ppmpf3 . '&' . $ppmpf4 . '&' . $ppmpf5;
		
		//Calculating Hash
		$_Securehash = hash_hmac('sha256', $SortedArrayOld, $_IntegritySalt);
		
		$TxnType = get_post_meta( $order_id, $this->type, true ); // sending transaction type with the form (updated)

		$jazzcash_args = array(
			'pp_Version' => $_Version,
			'pp_TxnType' => $this->type,
			'pp_Language' => $_Language,
			'pp_MerchantID' => $_MerchantID,
			'pp_SubMerchantID' => '',
			'pp_Password' => $_Password,
			'pp_BankID' => '',
			'pp_ProductID' => '',
			'pp_TxnRefNo' => $_TxnRefNumber,
			'pp_Amount' => $_FormattedAmount,
			'pp_TxnCurrency' => $_Currency,
			'pp_TxnDateTime' => $_TxnDateTime,
			'pp_BillReference' => $_BillReference,
			'pp_Description' => $_Description,
			'pp_TranExpiryDateTime' => $_ExpiryDateTime,
			'pp_ReturnURL' => $_ReturnURL,
			'pp_SecureHash' => $_Securehash,
			'ppmpf_1' => $ppmpf1,
			'ppmpf_2' => $ppmpf2,
			'ppmpf_3' => $ppmpf3,
			'ppmpf_4' => $ppmpf4,
			'ppmpf_5' => $ppmpf5,
			'pp_MobileNumber'=>$_PhoneNumber,
			'pp_CNIC'	=>	$_CnicNumber
		);
		$endpoint = $this->enabled === "yes" ? $this->test_endpoint : $live_endpoint; 
		$response = wp_remote_post( $endpoint, array(
			'method'      => 'POST',
			'timeout'     => 120,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        =>$jazzcash_args,
			'cookies'     => array()
			)
		);
		$jazzcash_response = json_decode($response['body']);
		
		if($jazzcash_response->pp_ResponseCode > 000)
		{
			wc_add_notice(  $jazzcash_response->pp_ResponseMessage, 'error' );
			return;
		}

		if ( is_wp_error( $response )) 
		{
			$error_message = $response->get_error_message();
			wc_add_notice(  __('Something went wrong: ').$response->get_error_message(), 'error' );
			return;
		} 
		
		if( !is_wp_error( $response ) ) 
		{
			if (wp_remote_retrieve_response_code($response) ==  200) 
			{
				
				$customer_order->payment_complete();
				$customer_order->reduce_order_stock();
		
				$customer_order->add_order_note( __('Hey, your order is paid! Thank you!'), true );
				$customer_order->update_meta_data( 'jazzcash_ref_no', $jazzcash_response->pp_TxnRefNo );
				$woocommerce->cart->empty_cart();
		
			   return array(
				   'result' => 'success',
				   'redirect' => $this->get_return_url( $customer_order )
			   );
	
			} 
			else 
			{
				wc_add_notice(   __("Something went wrong:"). $response->get_error_message(), 'error' );
			   return;
		   	}
	   	} 
	   	else 
		{
			wc_add_notice(  __('Connection error.'), 'error' );
			return;
		}   
	}

	function jazzcash_wc_custom_checkout_field_update_order_meta($order_id)
	{
		update_post_meta( $order_id, 'jazzcash_mobile_account', sanitize_text_field($_POST['phone_number']) );
		update_post_meta( $order_id, 'jazzcash_cnic', sanitize_text_field($_POST['cnic']) );
	}

	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled' => array(
				'title'       => 'Enable/Disable',
				'label'       => 'Enable JazzCash Gateway',
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'yes'
			),
			'title' => array(
				'title'       => 'Title',
				'type'        => 'text',
				'default'     => 'JazzCash Mobile Account'
			),
			'description' => array(
				'title'       => 'Description',
				'type'        => 'textarea',
				'default'     => 'Pay with your JazzCash Mobile account via our super-cool payment gateway.',
			),
			'testmode' => array(
				'title'       => 'Enable JazzCash sandbox',
				'label'       => 'Enable Test Mode',
				'type'        => 'checkbox',
				'description' => 'Place the payment gateway in test mode using test API keys. Make Sure to uncheck if you are using Live, ',
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'merchant_id' => array(
				'title'       => 'Merchant ID',
				'type'        => 'text',
				// 'desc_tip'    => true,
				'required'	  => true,
				'description' => 'Make Sure to copy proper merchant id from JazzCash Developer portal. <a href="https://sandbox.jazzcash.com.pk/MerchantDashboard/Login">JazzCash</a>',
			),
			'password' => array(
				'title'       => 'Password',
				'type'        => 'password'
			),
			'integenty_salt' => array(
				'title'       => 'Integenty Salt',
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => 'Copy Integenty Salt from JazzCash developer portal.',
			),
			'return_url' => array(
				'title'       => 'Return URL',
				'type'        => 'text',
				'placeholder'	=>	site_url('checkout/order-received/'),
				'description' => 'Make Sure to add return url based on your setting.',
			),
			'expiryHours' => array(
                'title' => __('Transaction Expiry (Hours)', 'jazzcash'),
                'type' => 'number',
                'desc_tip' => __('Transaction Expiry (Hours)', 'jazzcash'),
				'default' => __('12', 'jazzcash')
                ),
		);
	} 

	public function payment_fields()
	{ // added form for trancastion type (updated)

		if ( $description = $this->get_description() ) 
		{
			echo wpautop( wptexturize( $description ) );
		}
		if($this->testmode)
		{
			if(!empty($this->merchant_id) && !empty($this->password) && !empty($this->integenty_salt))
			{
				echo "<span class='jazzcash_alert jazzcash_alert-danger'>Test Mode has been enabled!</span>";
				echo "<h4>Test JazzCash Mobile Accounts!</h4>";
				echo '<table class="gatewayResponseCodesTable">
					<tbody>
						<tr>
							<th>Mobile Number</th>
							<th>Last 6 Digits of CNIC</th>
							<th>Response</th>
						</tr>
						<tr>
							<td>03123456789</td>
							<td>345678</td>
							<td>Successful</td>
						</tr>
						<tr>
							<td>03123456780</td>
							<td>345678</td>
							<td>Authentication Error</td>
						</tr>
						<tr>
							<td>Others</td>
							<td>345678</td>
							<td>Pending</td>
						</tr>
					</tbody>
				</table>';
				$plugins_url = plugins_url();
				$my_plugin = $plugins_url . '/jazzcash-woocommerce-gateway';
				global $woocommerce;
				include_once(JAZZCASH_DIR_PATH_INC.'partials/front.php');
			}
			else
			{
				echo "<span class='jazzcash_alert jazzcash_alert-danger'>Please Enter Merchant Account details!</span>";
			}
		}
	}
	
	function jazzcash_wc_adding_scripts() 
	{
		wp_register_script('jazzcash_wc_scripts', JAZZCASH_DIR_PATH_JS.'jazzcash-buttons.js'); 		
		wp_enqueue_script('jazzcash_wc_scripts');
	}

	function jazzcash_wc_adding_styles() 
	{
		wp_register_style('jazzcash_wc_stylesheet', JAZZCASH_DIR_PATH_CSS.'jazzcash-buttons.css');
		wp_enqueue_style('jazzcash_wc_stylesheet');
	}

	/* 
		* Validation of Payment Form on Checkout Page 
	*/
	function jazzcash_wc_checkout_field_validation() 
	{
		if ( sanitize_text_field($_POST['payment_method']) == 'jazzcash-wc-payment-gateway')
		{
			if(empty(sanitize_text_field($_POST['phone_number'])))
			{
				wc_add_notice( __( 'Please enter your jazzcash account number.' ), 'error' );
				return false;
			}
			if(empty(sanitize_text_field($_POST['cnic'])))
			{
				wc_add_notice( __( 'Please enter your last 6 digits of CNIC.' ), 'error' );
				return false;
			}
		}
	}
}

?>