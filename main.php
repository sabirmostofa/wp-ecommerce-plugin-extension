<?php

/*
  Plugin Name: WP-Ecommerce-Extension
  Plugin URI: http://sabirul-mostofa.blogspot.com
  Description: Extends wp-ecommerce functionality
  Version: 1.0
  Author: Sabirul Mostofa
  Author URI: http://sabirul-mostofa.blogspot.com
 */


$wpEcomExtension = new wpEcomExtension();

class wpEcomExtension {

    public $table = '';
    public $image_dir = '';
    public $prefix = 'wpcomext_';
    public $meta_box = array();

    function __construct() {
        global $wpdb;
        $this->image_dir = plugins_url('/', __FILE__) . 'images/';
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('admin_menu', array($this, 'CreateMenu'), 50);
        add_action('wp_ajax_com_ext', array($this, 'ajax_admin'));
    }

    function CreateMenu() {
        add_submenu_page('options-general.php', 'E-commerce Stat', 'E-commerce Stat', 'activate_plugins', 'wpEcomExtension', array($this, 'OptionsPage'));
    }

    function OptionsPage() {
        include 'options-page.php';
    }

    function admin_scripts() {
        if ($_GET['page'] == 'wpEcomExtension'):
            wp_enqueue_script('rt_admin_script', plugins_url('/', __FILE__) . 'js/script_admin.js');

            wp_enqueue_script('jquery-ui-datepicker');

            wp_enqueue_style('datepicker', plugins_url('css/ui-lightness/jquery-ui-1.8.16.custom.css', __FILE__));

            wp_register_style('rt_admin_css', plugins_url('/', __FILE__) . 'css/style_admin.css', false, '1.0.0');
            wp_enqueue_style('rt_admin_css');
        endif;
    }

    function ajax_admin() {
        global $wpdb;
        $from = strtotime($_POST['date_from']);
        $to = strtotime($_POST['date_to']);


        // SHOWING COUPON CODES USED

        echo '<h3>Coupon Codes used:</h3>';

        $table_logs = WPSC_TABLE_PURCHASE_LOGS;


        $res = $wpdb->get_results("
                select count(date) as cnt,discount_data
                from     $table_logs 
                where    date>=$from 
                and      date<=$to 
                and      processed=3
                and      discount_value>0
                order by cnt desc
                ");

        //var_dump($res);

        if (is_array($res) && count($res) == 1) {
            if ($res[0]->cnt == 0)
                $res = array();
        }

        else
            $res = array();

        $counter = 0;

        if (empty($res))
            echo '<div class="updated" style="margin: 10px;"> No Coupon was used in this date range </div>';
        $data_coupons = "<table class='widefat'><thead><thead><th>Index</th><th>Coupon Code</th><th>Count</th></thead>";
        foreach ($res as $value):
            $counter++;
            $data_coupons .= "<tr><td>$counter</td><td>{$value->discount_data}</td><td>{$value->cnt}</td></tr>";
        endforeach;

        $data_coupons.= '</table>';
        echo $data_coupons, '<br/><br/>';


        //Showing Product sell amount
        echo '<h3>Products and sold amount:</h3>';
        $table_cart = WPSC_TABLE_CART_CONTENTS;



        $res_cart = $wpdb->get_results("
     select     cart.name name ,sum(cart.quantity) amount
     from       $table_cart cart
                
     inner join $table_logs logs
                on cart.purchaseid=logs.id
     where      logs.date>=$from and logs.date<=$to and logs.processed=3
                
     group by   cart.name
     order by   amount desc
         
     
");

        if (!is_array($res_cart))
            $res_cart = array();


        //

        if (empty($res_cart))
            echo '<div class="updated"  style="margin:10px;"> No Product was sold in this date range </div>';

        $counter = 0;

        $data_prod = "<table class='widefat'><thead><th>Index</th><th>Product name</th><th>Sold Amount</th></thead>";
        foreach ($res_cart as $value):
            $counter++;
            $data_prod .= "<tr><td>$counter</td><td>{$value->name}</td><td>{$value->amount}</td></tr>";
        endforeach;

        $data_prod.= '</table>';
        echo $data_prod, '<br/><br/>';


        // Total amount of Money taken splitting postage and original price
        echo '<h3>Total Money taken and splits:</h3>';
        $res_taken = $wpdb->get_results("
     select     sum(logs.totalprice) price, 
                sum(logs.discount_value) dis,
                sum(logs.wpec_taxes_total) tax,
                sum(logs.base_shipping) ship
     from       $table_logs logs               
     where      logs.date>=$from and logs.date<=$to and logs.processed=3           
     
");

        //var_dump($res_taken);
        $data_total_pay = "<table class='widefat'><thead>
               <th>Total Payment Received</th>
               <th>Original Price</th>
               <th>Discount</th>
               <th>Product Price</th>
               <th>Shipping</th>
               <th>Tax</th>
               </thead>";
        $counter = 0;
        foreach ($res_taken as $value):
            $tot_payment = $value->price + $value->tax + $value->ship - $value->dis;
            $tot_product = $value->price - $value->dis;
            $counter++;
            $data_total_pay .= "<tr>
                      <td>$tot_payment</td>
                      <td>$value->price</td>
                      <td>$value->dis</td>                                                
                      <td>$tot_product</td>
                      <td>$value->ship</td>
                      <td>$value->tax</td>
                     <td></td>
            </tr>";
        endforeach;

        $data_total_pay .= '</table>';
        echo $data_total_pay, '<br/><br/>';





        //showing payments through different modules

        echo '<h3>Payment through different gateways:</h3>';

        $res_pay = $wpdb->get_results("
     select     logs.gateway gw,sum(logs.totalprice) price, 
                sum(logs.discount_value) dis,
                sum(logs.wpec_taxes_total) tax,
                sum(logs.base_shipping) ship
     from       $table_logs logs               
     where      logs.date>=$from and logs.date<=$to and logs.processed=3
                
     group by   logs.gateway 
     order by   price desc
     
");
        //var_dump($res_pay);

        $data_pay = "<table class='widefat'><thead><th>Index</th><th>Gateway name</th><th>Payment Amount</th></thead>";
        $counter = 0;
        foreach ($res_pay as $value):
            $net_payment = $value->price + $value->tax + $value->ship - $value->dis;
            $counter++;
            $data_pay .= "<tr><td>$counter</td><td>{$value->gw}</td><td>
            $net_payment
                    </td></tr>";
        endforeach;

        $data_pay.= '</table>';
        echo $data_pay;


        exit;
    }

}
