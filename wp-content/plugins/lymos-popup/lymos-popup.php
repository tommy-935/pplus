<?php
/*
Plugin Name: lym popup
Plugin URI: lymos.com/lym-popup.html
Description: a popup for wordpress
Version: 1.0
Author: lymos
License: GPLv2
*/
namespace lymPopup;
class lymPopup{
    private static $instance = null;
    const lym_popup_assets_ver = '1.15';

    public function __construct(){
        if(is_admin()){
            return ;
        }
        add_action('wp_footer', [$this, 'showPopup']);
    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function showPopup(){
        wp_enqueue_style('', plugins_url('assets/css/lym-popup.css', __FILE__), [], self::lym_popup_assets_ver);
        wp_enqueue_script('', plugins_url('assets/js/lym-popup.js', __FILE__), [], self::lym_popup_assets_ver);
        $site_url = get_option('siteurl');
        $url = $site_url . '/vip/';
        if(function_exists('wcpbc_get_woocommerce_country')){
            $country = wcpbc_get_woocommerce_country();
            if($country == 'JP'){
                $url = $site_url . '/jp/become-a-vip-and-start-saving-esr/';
            }
        }
        $html = '<div id="lym-popup-m" onclick="location.href=\'' . $url . '\'" class="lym-popup-m ';
        $html .= 'lym-popup-hide-m';
        $html .= '"><div id="lym-popup-toggle-m" class="lym-popup-toggle-m ';
        $html .= 'lym-popup-toggle-m-expend';
        $html .= '"></div></div>';
        echo $html;
    }

}

lymPopup::getInstance();
