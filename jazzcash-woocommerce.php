<?php 

/**
 * Plugin Name:       JazzCash - WC Payment GateWay
 * Plugin URI:        https://github.com/meetsohail/jazzcash-wc-payment-gateway
 * Description:       JazzCash WordPress Payment Gateway plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Sohail Ahmed
 * Author URI:        https://github.com/meetsohail
 * License:           MIT
 * License URI:       https://choosealicense.com/licenses/mit/
 * Text Domain:       jazzcash-wc-payment-gateway
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JazzCash_Woocommerce_Payment_Gateway
{
    public function __construct()
    {
        define('JAZZCASH_VERSION', '1.0');
        define('JAZZCASH_ID', 'jazzcash-wc-payment-gateway');
        define("JAZZCASH_DIR_PATH", plugin_dir_path(__FILE__));
        define("JAZZCASH_DIR_PATH_INC", plugin_dir_path(__FILE__)."inc/");
        define("JAZZCASH_DIR_PATH_IMAGES", plugin_dir_url( __FILE__ )."images/");
        define("JAZZCASH_DIR_PATH_CSS", plugin_dir_url( __FILE__ )."css/");
        define("JAZZCASH_DIR_PATH_JS", plugin_dir_url( __FILE__ )."js/");
        define("JAZZCASH_URL", plugins_url('', __FILE__));
        
        /**
            * Check if WooCommerce is active
        **/
        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
        {
            add_action('admin_notices', array($this, 'jazzcash_wc_woocommerce_check'));
        }
        /**
            * Check if Currency is PKR 
        **/
        else if(apply_filters( 'woocommerce_currency', get_option( 'woocommerce_currency' ) ) != 'PKR')
        {
            add_action('admin_notices', array($this, 'jazzcash_wc_currency_check'));
        }
        /**
            * Activate Plugin Successfully 
        **/
        else
        {  
            add_action( 'plugins_loaded', array($this,'jazzcash_wc_gateway_class') );
            add_filter( 'woocommerce_payment_gateways', array($this, 'jazzcash_wc_payment_gateway') );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'jazzcash_wc_action_links') );
            add_action('admin_notices', array($this, 'jazzcash_wc_ssl_check'));
            add_action( 'woocommerce_before_thankyou', array($this,'jazzcash_wc_success_message_after_payment') );
        }
    }

    function jazzcash_wc_woocommerce_check()
    {
        echo "<div class=\"error\"><p>" . __("Make Sure WooCommerce is installed to use JazzCash WooCommerce Payment GateWay Plugin")."</p></div>";
    }
    
    function jazzcash_wc_currency_check()
    {
        echo "<div class=\"error\"><p>" . __("JazzCash only Proccessess Pakistani PKR currency.")."</p></div>";
    }
    function jazzcash_wc_gateway_class()
    {
        require_once(JAZZCASH_DIR_PATH_INC."init.class.php");
    }
    
    function jazzcash_wc_payment_gateway($methods)
    {
        $methods[] = 'JazzCash_WC_Payment_Gateway'; 
        return $methods;
    }

    /**
        * Add Setting Link on plugins page.
    **/
    function jazzcash_wc_action_links( $links ) 
    {
	    $plugin_links = array(
		    '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'jazzcash' ) . '</a>',
	    );
        return array_merge( $plugin_links, $links );	
    }
    
    /**
        * Check SSL.
    **/
    public function jazzcash_wc_ssl_check()
    {
        if ($this->enabled == "yes") 
        {
            if (get_option('woocommerce_force_ssl_checkout') == "no") 
            {
                echo "<div class=\"error\"><p>" . sprintf(__("<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>"),
                    $this->method_title, admin_url('admin.php?page=wc-settings&tab=checkout')) .
                    "</p></div>";
            }
        }
    }

    /**
        * Shows success message on thank you page.
    **/
    function jazzcash_wc_success_message_after_payment( $order_id )
    {
        // Get the WC_Order Object
        $order = wc_get_order( $order_id );
        if ( $order->has_status('processing') ){
            wc_print_notice( __("Your payment has been successful!", "woocommerce"), "success" );
        }
    }
    
}
new JazzCash_Woocommerce_Payment_Gateway();
