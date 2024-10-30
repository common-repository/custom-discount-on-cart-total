<?php
/**
 * Plugin Name: Custom Discount on Cart Total
 * Plugin URI: https://www.beeplugin.com/
 * Description: Discount rules on cart total amount.
 * Version: 1.21
 * Author: BeePlugin
 * Author URI: https://www.beeplugin.com
 */
// Add a custom setting tab to Woocommerce > Settings section
add_action( 'woocommerce_settings_tabs', 'wc_settings_tabs_beeplug_wctdr_cart_total_discounts' );
function wc_settings_tabs_beeplug_wctdr_cart_total_discounts() {
    $current_tab = ( isset($_GET['tab']) && $_GET['tab'] === 'beeplug_wctdr_cart_total_discounts' ) ? 'nav-tab-active' : '';
    ?>
    <a href="admin.php?page=wc-settings&tab=beeplug_wctdr_cart_total_discounts" class="nav-tab <?php echo sanitize_text_field($current_tab);?>">Cart Total Discount Rule</a>
<?php
}

// The setting tab content
add_action( 'woocommerce_settings_beeplug_wctdr_cart_total_discounts', 'beeplug_wctdr_manage_cart_total_discount_content' );
function beeplug_wctdr_manage_cart_total_discount_content() {
    global $wpdb;

    woocommerce_admin_fields( beeplug_wctdr_get_settings() );

}

function beeplug_wctdr_get_settings() {

  	$settings = array(
  		'section_title' => array(
  			'name'     => __( 'Manage Discount on Cart Totals', 'bee-plugin' ),
  			'type'     => 'title',
  			'desc'     => '',
  			'id'       => 'wc_cart_total_discount_section_title'
  		),
  		'cart_label' => array(
  			'name' => __( 'Discount Label Text', 'bee-plugin' ),
  			'type' => 'text',
  			'desc' => __( 'Text to display discount type name', 'bee-plugin' ),
  			'id'   => 'wc_cart_total_discount_title'
  		),
  		'cart_amount' => array(
  			'name' => __( 'Cart Amount', 'bee-plugin' ),
  			'type' => 'number',
  			'desc' => __( 'Minimun cart total amount to avail discount', 'bee-plugin' ),
  			'id'   => 'wc_cart_total_discount_description'
  		),
  		'cart_discount' => array(
  			'name' => __( 'Discount Amount', 'bee-plugin' ),
  			'type' => 'number',
  			'desc' => __( 'Discount amount to given', 'bee-plugin' ),
  			'id'   => 'wc_cart_total_discount_amount'
  		),
  		'section_end' => array(
  			'type' => 'sectionend',
  			'id' => 'wc_cart_total_discount_section_end'
  		)
  	);

  	return apply_filters( 'wc_cart_total_discount_settings', $settings );
  }
add_action( 'woocommerce_update_options_cart_total_discount', 'beeplug_wctdr_update_settings' );
  function beeplug_wctdr_update_settings() {
  	woocommerce_update_options( bee_get_settings() );
  }  
function beeplug_wctdr_apply_cart_total_discount() {
  global $woocommerce;
  $excluded_amount = $discount_percent = 0;
  $working_total   = $woocommerce->cart->cart_contents_total;

  $discount_text = get_option('wc_cart_total_discount_title');
  $max_cart_amount = get_option('wc_cart_total_discount_description');
  $discount_price = get_option('wc_cart_total_discount_amount');

  // Only apply manual discount if no coupons are applied
  //if (!$woocommerce->cart->applied_coupons) {

    // Logic to determine WHICH discount to apply based on subtotal
    if ($working_total >= $max_cart_amount) 
    	{ 
    		$discount_percent = $discount_price; 
    	}
    else {
      		$discount_percent = 0;
    }

    // Make sure cart total is eligible for discount
    if ($discount_percent > 0) {
      //$discount_amount  = ( ( ($discount_percent/100) * $working_total ) * -1 );
      $discount_amount  = ( $discount_percent * -1 );
      $woocommerce->cart->add_fee($discount_text, $discount_amount);
    }
  //}
}
add_action('woocommerce_cart_calculate_fees', 'beeplug_wctdr_apply_cart_total_discount');

?>