<?php
namespace Losml\Backend\Lib\Menu;

class BackendMenu{

    public $losml;
    
    public function __construct($losml){
        $this->losml = $losml;
    }

    public function losmlAdminMenu(){
        add_menu_page('Losml Multilingual', 'Losml Multilingual', 'edit_posts', 'losml_langs', [$this->losml->language, 'languageList']);
        add_submenu_page('losml_langs', __( 'Dashboard', 'losml-multilingual' ), __( 'Dashboard', 'losml-multilingual' ), 'manage_options', 'losml_dashboard', [$this->losml->language, 'dashboard']);
        add_submenu_page('losml_langs', 'All Languges', 'All Languges', 'manage_options', 'losml_langs', [$this->losml->language, 'languageList']);
        add_submenu_page('losml_langs', 'Add New', 'Add New', 'manage_options', 'losml_new_lang', [$this->losml->language, 'showAddNewLanguage']);
        add_submenu_page('losml_langs', 'Strings Translations', 'Strings Translations', 'edit_posts', 'losml_string_translations', [$this->losml->string, 'stringTrans']);

        add_filter( 'plugin_action_links', [$this, 'losml_plugin_action_links'], 10, 2 );
    }

    public function losml_plugin_action_links( $links, $file ) {
        /* Static so we don't call plugin_basename on every plugin row. */
        if ( $file == $this->losml->plugin ) {
            $settings_link = '<a href="admin.php?page=losml_langs">' . __( 'Settings', 'losml-multilingual' ) . '</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }
    
}
