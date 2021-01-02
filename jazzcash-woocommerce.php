<?php 

/**
 * Plugin Name:       JazzCash - WC Payment GateWay
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       JazzCash WordPress Payment Gateway plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Sohail Ahmed
 * Author URI:        https://github.com/meetsohail
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
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
        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action('admin_notices', array($this, 'jazzcash_wc_woocommerce_check'));
        }
        else
        {  
            add_action( 'plugins_loaded', array($this,'jazzcash_wc_gateway_class') );
            add_filter( 'woocommerce_payment_gateways', array($this, 'jazzcash_wc_payment_gateway') );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'jazzcash_wc_action_links') );
            add_action('admin_notices', array($this, 'jazzcash_wc_ssl_check'));
            add_action('woocommerce_receipt_jazzcash', array($this, 'jazzcash_wc_receipt_page'));
        }
    }

    function jazzcash_wc_woocommerce_check()
    {
        echo "<div class=\"error\"><p>" . __("Make Sure WooCommerce is installed to use JazzCash WooCommerce Payment GateWay Plugin")."</p></div>";
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
     
    function jazzcash_wc_action_links( $links ) 
    {
	    $plugin_links = array(
		    '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'jazzcash' ) . '</a>',
	    );
        return array_merge( $plugin_links, $links );	
    }
    
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

    function jazzcash_wc_receipt_page($order)
    {
        echo '<p>'.__('Please wait while your are being redirected to JazzCash...', 'jazzcash').'</p>';
        $plugins_url = plugins_url();
        $my_plugin = $plugins_url . '/jazzcash-woocommerce-gateway';
        echo '<p><img src="'.$my_plugin.'/assets/jazz-cash.png" /></p>';
        echo $this->generate_jazzcash_form($order);
    }
    public function generate_jazzcash_form($order_id)
    {
           
            
    }
        
}
new JazzCash_Woocommerce_Payment_Gateway();

 