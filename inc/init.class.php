
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JazzCash_WC_Payment_Gateway extends WC_Payment_Gateway 
{
	public $type = 'MPAY'; 
	public $endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

    public function __construct()
    {
		$this->id = JAZZCASH_ID;
		$this->icon = JAZZCASH_DIR_PATH_IMAGES."jazzcash-logo-200x200.png";
		$this->has_fields = true;
		$this->method_title = "JazzCash";
		$this->method_description = "JazzCash Payment Gateway gets through jazzcash account or any credit card to enter their payment information.	";
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
		// print_r(get_alloptions());
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		add_action( 'wp_enqueue_scripts', array($this, 'jazzcash_wc_adding_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'jazzcash_wc_adding_styles') );
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this,
		'process_admin_options'));
	// add_action('woocommerce_checkout_process', array($this,'jazzcash_wc_checkout_field_validation'));

		// add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
		// add_action('woocommerce_update_options_shipping_methods', array(&$this, 'process_admin_options'));

	}

	public function process_payment( $order_id ) {
 
		global $woocommerce;
    
		$customer_order = new WC_Order($order_id);
		
		$_ActionURL     = $this->actionURL;
		$_MerchantID    = $this->merchant_id;
		$_Password      = $this->password;
		$_ReturnURL     = $this->return_url;
		$_IntegritySalt = $this->integenty_salt;
		$_ExpiryHours   = $this->expiryHours;
		$_PhoneNumber   = $_POST['phone_number'];
		$_CnicNumber	= $_POST['cnic'];
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
			'pp_TxnExpiryDateTime' => $_ExpiryDateTime,
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
		$response = wp_remote_post( $this->endpoint, array(
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
		 
		if ( is_wp_error( $response ) ) 
		{
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else 
		{
			echo 'Response:<pre>';
			print_r( $response );
			echo '</pre>';
		}
		if( !is_wp_error( $response ) ) 
		{
			if (wp_remote_retrieve_response_code($response) ==  200) 
			{
			   $customer_order->payment_complete();
			   $customer_order->reduce_order_stock();
	
			   $customer_order->add_order_note( 'Hey, your order is paid! Thank you!', true );
	
			   $woocommerce->cart->empty_cart();
	
			   return array(
				   'result' => 'success',
				   'redirect' => $this->get_return_url( $customer_order )
			   );
	
			} else {
			   wc_add_notice(   "Something went wrong:". $response->get_error_message(), 'error' );
			   return;
		   }
	   } else {
		   wc_add_notice(  'Connection error.', 'error' );
		   return;
	   }   
	 
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
				'description' => 'This controls the title which the user sees during checkout.',
				'default'     => 'Credit Card',
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => 'Description',
				'type'        => 'textarea',
				'description' => 'This controls the description which the user sees during checkout.',
				'default'     => 'Pay with your JazzCash Mobile account or credit card or Voucher via our super-cool payment gateway.',
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
				'desc_tip'    => true,
				'required'	  => true,
				'description' => 'Place the payment gateway in test mode using test API keys. Make Sure to uncheck if you are using Live, ',
			),
			'password' => array(
				'title'       => 'Password',
				'type'        => 'password',
				'desc_tip'    => true,
				'description' => 'Place the payment gateway in test mode using test API keys. Make Sure to uncheck if you are using Live, ',
			),
			'integenty_salt' => array(
				'title'       => 'Integenty Salt',
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => 'Place the payment gateway in test mode using test API keys. Make Sure to uncheck if you are using Live, ',
			),
			'return_url' => array(
				'title'       => 'Return URL',
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => 'Place the payment gateway in test mode using test API keys. Make Sure to uncheck if you are using Live, ',
			),
			'return_url' => array(
				'title'       => 'Expire Hours',
				'type'        => 'number',
				'desc_tip'    => true,
				'description' => 'Place the payment gateway in test mode using test API keys. Make Sure to uncheck if you are using Live, ',
			),
			'expiryHours' => array(
                'title' => __('Transaction Expiry (Hours)', 'jazzcash'),
                'type' => 'number',
                'desc_tip' => __('Transaction Expiry (Hours)', 'jazzcash'),
				'default' => __('12', 'jazzcash')
                ),
		);
	} 
	public function payment_scripts()
	{

	}
	public function payment_fields(){ // added form for trancastion type (updated)

		if ( $description = $this->get_description() ) {
			echo wpautop( wptexturize( $description ) );
		}
		if($this->testmode)
		{
			echo "<span style='background:red; padding: 5px; color:white;'>Test Mood</span>";
			echo '<table class="gatewayResponseCodesTable">
			<tbody><tr>
				<th>
					Mobile Number
				</th>
				<th>
					Last 6 Digits of CNIC
				</th>
				<th>
					Response
				</th>
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
		</tbody></table>';
		}
		$plugins_url = plugins_url();
		$my_plugin = $plugins_url . '/jazzcash-woocommerce-gateway';
		global $woocommerce;
		include_once(JAZZCASH_DIR_PATH_INC.'partials/front.php');
	}
	
	function jazzcash_wc_adding_scripts() {
		wp_register_script('jazzcash_wc_scripts', JAZZCASH_DIR_PATH_JS.'jazzcash-buttons.js'); 		
		wp_enqueue_script('jazzcash_wc_scripts');
	}

	function jazzcash_wc_adding_styles() {
		wp_register_style('jazzcash_wc_stylesheet', JAZZCASH_DIR_PATH_CSS.'jazzcash-buttons.css');
		wp_enqueue_style('jazzcash_wc_stylesheet');
	}
	function jazzcash_wc_checkout_field_validation() {
		if ( $_POST['payment_method'] === 'jazzcash-wc-payment-gateway' && isset($_POST['phone_number']) && empty($_POST['cnic']) )
			if(!$_POST['phone_number'] || empty($_POST['phone_number']))
			{
				wc_add_notice( __( 'Please enter your jazzcash account phone number.' ), 'error' );
				return false;
			}
			if(!$_POST['cnic'] || $_POST['cnic'] == '')
			{
				wc_add_notice( __( 'Please enter your last 6 digits of CNIC.' ), 'error' );
				return false;
			}
	}
}

?>