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


        // SHOWIN COUPON CODES USED

        $table_logs = WPSC_TABLE_PURCHASE_LOGS;

        $res = $wpdb->get_results("
                select count(date) as cnt,discount_data
                from $table_logs 
                where date>=$from 
                and date<=$to 
                and processed=3
                ");

        if (!is_array($res))
            $res = array();
        //var_dump($res);
        $counter=0;
        $data_coupons = "<table class='widefat'><thead><thead><th>Index</th><th>Coupon Code</th><th>Count</th></thead>";
        foreach ($res as $value):
            $counter++;
            $data_coupons .= "<tr><td>$counter</td><td>{$value->discount_data}</td><td>{$value->cnt}</td></tr>";
        endforeach;

        $data_coupons.= '</table>';
        echo $data_coupons, '<br/><br/>';
        
        
        //Showing Product sell amount
        
 $table_cart = WPSC_TABLE_CART_CONTENTS;
 

 
 $res_cart = $wpdb->get_results("
     select     cart.name name ,sum(cart.quantity) amount
     from       $table_cart cart
                
     inner join $table_logs logs
                on cart.purchaseid=logs.id
     where      logs.date>=$from and logs.date<=$to and logs.processed=3
     group by   cart.name
         
     
");
 
 if(!is_array($res_cart))$res_cart=array();

 
 //
 
 $counter=0;
 
           $data_prod = "<table class='widefat'><thead><th>Index</th><th>Product name</th><th>Sold Amount</th></thead>";
        foreach ($res_cart as $value):
            $counter++;
            $data_prod .= "<tr><td>$counter</td><td>{$value->name}</td><td>{$value->amount}</td></tr>";
        endforeach;

        $data_prod.= '</table>';
        echo $data_prod;


        exit;
    }

    function not_in_table($city) {
        global $wpdb;
        $var = $wpdb->get_var("select city_url from $this->table where city_name='$city'");
        if ($var == null)
            return true;
    }

}
