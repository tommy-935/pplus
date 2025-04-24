<?php if (!defined('ABSPATH')) {
  die;
} // Cannot access directly.

//
// Set a unique slug-like ID
//
if (!is_admin()) {
  return;
}

$prefix     = _OPTIONS_PRE;
//
// Create options
//
CSF::createOptions($prefix, array(
  'menu_title' => '二开设置',
  'menu_slug'  => 'ireceivesms-theme-setting',
));

//
// Create a section
//
CSF::createSection($prefix, array(
  'title'  => '基础设置',
  'icon'   => 'fas fa-home',
  'fields' => array(


    array(
      'id'     => 'sms_api',
      'type' => 'text',
      'title'  => '短信接口API',
    ),
    array(
      'id'     => 'wallet_consumer_key',
      'type' => 'text',
      'title'  => '钱包KEY',
    ),
    array(
      'id'     => 'wallet_consumer_secret',
      'type' => 'text',
      'title'  => '钱包密钥',
    ),
    array(
      'id'     => 'Loaded_words',
      'type'   => 'text',
      'title'  => '加载词',
    ),
    array(
      'id'     => 'Loaded_dec',
      'type'   => 'text',
      'title'  => '加载词介绍',
    ),
    array(
      'id'     => 'footer-podcast-faq',
      'type'   => 'group',
      'title'  => '短信页面底部问题设置',
      'fields' => array(
        array(
          'id'    => 'podcast-text',
          'type'  => 'text',
          'title' => '问题标题',
        ),
        array(
          'id'    => 'podcast-content',
          'type'  => 'text',
          'title' => '问题内容',
        ),

      ),

    ),



  )
));
