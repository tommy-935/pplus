<?php
namespace Losml\Includes\Sso;

/**
 * Single Sign-On (SSO) Login Handler Class
 */
class SsoLogin {

    public $losml;
    public $timeout = MINUTE_IN_SECONDS;
    public $is_sso_action = 'is_sso_action';
    const SSO_NONCE_ACTION = 'losml_iframe_content';
    const TRANSIENT_PREFIX = 'losml_sso_';
    const KEY_TOKEN = 'losml_sso_token';
    const MD5_KEY = 'iufuiufuyfyuhduyyuu783';
    public $hash = '888888888888888fffffffffssssssshujrdiruihjuihjruihifhuifiruir';
    public $logining_key = 'losml_is_logining';
    public $current_user_id;

    public function __construct($losml) {
        $this->losml = $losml;
        $this->addHooks();
    }

    public function addHooks(){
        add_action( 'wp_login', [$this, 'loginAction'], 10, 2 );
        add_action( 'wp_logout', [$this, 'logoutAction'] );
    }

    public function init() {
        $this->getCurrentUserid();
        if ($this->getIsSso()) {
            add_action('init', [$this, 'initAction']);
            add_action('wp_ajax_losml_sign_user', [$this, 'iframeAjaxSignUser']);
            add_action('wp_ajax_nopriv_losml_sign_user', [$this, 'iframeAjaxSignUser']);
        }
    }

    public function initAction() {
        add_action('wp_footer', [$this, 'addIframe']);
        add_action('admin_footer', [$this, 'addIframe']);
        add_action('login_footer', [$this, 'addIframe']);
        if (isset($_GET['sso']) && $_GET['sso'] === $this->hash) {
            $this->genScript();
        }
    }

    /**
     * User login handler through iframe AJAX
     */
    public function iframeAjaxSignUser() {
        $this->getCurrentUserid();
        $userid = intval($_POST['user_id']);
        if ($this->validateAjaxSignUser()) {
            $user_status = isset($_POST['user_status']) ? filter_var($_POST['user_status'], FILTER_SANITIZE_STRING) : null;
            if ('losml_is_user_signed_in' === $user_status) {
                wp_set_auth_cookie($userid, false, '', '', true);
            } else {
                wp_clear_auth_cookie();
            }
            $key = $this->is_sso_action . LOSML_MULTI_LANGUAGE_ID . $this->current_user_id;
            delete_transient($key);
        }
    }

    /**
     * Validate the AJAX request for user sign-in
     */
    public function validateAjaxSignUser() {
        return $this->current_user_id
            && $this->isValidAjax()
            && isset($_POST['nonce'])
            && md5(self::MD5_KEY . $this->current_user_id) === $_POST['state'];
    }

    /**
     * Check if the request is AJAX
     */
    public function isValidAjax() {
        return defined('DOING_AJAX') || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Generate and output the JavaScript to send the SSO request
     */
    public function genScript() {
        $nonce = wp_create_nonce(self::SSO_NONCE_ACTION);
        $deply_time = (LOSML_MULTI_LANGUAGE_ID - 1) * 3000;
        $html = '
        <script>
            function sendXHRHttpRequest(params) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "' . esc_url(admin_url('admin-ajax.php')) . '", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(params);
            }
            window.onmessage = function(e) {
                var payload = JSON.parse(e.data),
                    userId = parseInt(payload.userId),
                    token = payload.token,
                    state = payload.state,
                    userStatus = payload.userStatus;
                var deply_time = ' . $deply_time . ';
                var params = "action=losml_sign_user&nonce=' . esc_attr($nonce) . '&user_id=" + userId + "&user_status=" + userStatus + "&token=" + token + "&state=" + state;
                setTimeout(function(){
                    sendXHRHttpRequest(params);
                }, deply_time);
            };
        </script>';
        echo $html;
        exit;
    }

    /**
     * Get the current user ID, either from session or token
     */
    public function getCurrentUserid($user_id = 0) {
        $this->current_user_id = get_current_user_id();
        if (!$this->current_user_id) {
            $this->current_user_id = $this->getUseridFromToken();
        }
        if (!$this->current_user_id) {
            $this->current_user_id = $user_id;
        }
    }

    /** @return int */
    private function getUseridFromToken() {
        if (isset($_POST['user_id'])) {
            return intval($_POST['user_id']);
        }
        $user_id = 0;
        if (isset($_POST['token']) || isset($_GET['t_noice'])) {
            $token = isset($_GET['t_noice']) ? filter_var($_GET['t_noice'], FILTER_SANITIZE_STRING) : filter_var($_POST['token'], FILTER_SANITIZE_STRING);
            $key = self::TRANSIENT_PREFIX . $token . LOSML_MULTI_LANGUAGE_ID;
            $user_id = (int) get_transient($key);
            delete_transient($key);
        }
        return $user_id;
    }

    /**
     * Add iframe elements to the page
     */
    public function addIframe() {
        $this->getCurrentUserid();
        $is_userlogin = is_user_logged_in() ? 1 : 0;
        $token = $this->createUserToken($this->current_user_id);
        $state = md5(self::MD5_KEY . $this->current_user_id);
        $html = '';

        foreach ($this->losml->active_language_data as $k => $rs) {
            if ($rs->id == LOSML_MULTI_LANGUAGE_ID) {
                continue;
            }

            $iframe_url = $this->losml->getUrlSchema() . '://' . $rs->host . '?sso=' . $this->hash . '&t_noice=' . $token;
            $html_id = 'sso-iframe-' . $rs->code;
            $iframe_name = 'sso_iframe_' . $rs->code;
            $html .= '<iframe id="' . $html_id . '" class="losml_iframe" style="display:none" src="' . esc_url($iframe_url) . '"></iframe>';

            $html .= '
            <script>
                var ' . $iframe_name . ' = document.getElementById("' . $html_id . '");
                ' . $iframe_name . '.onload = function() {
                    var userStatus = "losml_is_user_signed_out";
                    if (' . $is_userlogin . ' === 1) {
                        userStatus = "losml_is_user_signed_in";
                    }
                    this.contentWindow.postMessage(JSON.stringify({userStatus: userStatus, userId: ' . $this->current_user_id . ', token: "' . $token . '", state: "' . $state . '"}), "*");
                }
            </script>
            ';
        }
        echo $html;
    }

    /**
     * Handle login action
     */
    public function loginAction($user_login, \WP_User $user) {
        $this->getCurrentUserid((int)$user->ID);
        $this->setIsSso();
    }

    /**
     * Handle logout action
     */
    public function logoutAction() {
        $this->getCurrentUserid();
        global $wpdb;
        $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'usermeta WHERE meta_key = %s AND user_id = %d', 'session_tokens', $this->current_user_id);
    }

    /**
     * Set SSO action transient
     */
    public function setIsSso() {
        $langs = $this->losml->active_language_data;
        foreach ($langs as $obj) {
            $key = $this->is_sso_action . $obj->id . $this->current_user_id;
            set_transient($key, 1, $this->timeout);
        }
    }

    /**
     * Check if SSO action is set
     */
    public function getIsSso() {
        $key = $this->is_sso_action . LOSML_MULTI_LANGUAGE_ID . $this->current_user_id;
        return get_transient($key);
    }

    /**
     * Create user token
     * @param int $user_id
     * @return string
     */
    private function createUserToken($user_id) {
        $token = wp_create_nonce(self::SSO_NONCE_ACTION);
        foreach ($this->losml->active_language_data as $obj) {
            if ($obj->id == LOSML_MULTI_LANGUAGE_ID) {
                continue;
            }
            set_transient(self::TRANSIENT_PREFIX . $token . $obj->id, $user_id, $this->timeout);
        }
        return $token;
    }
}
