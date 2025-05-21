<?php

namespace Lopss\License;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Pro
 * 1. Email logs
 * 2. Resend failed emails
 * ==== 3. Connect multiple WordPress sites
 * 4. Open email tracking
 * 5. Auto resend failed emails
 * ==== 6. Email quota scheduling
 * ==== 7. Email Templates
 */
class LicenseManager
{
    protected $licenseKey;
    protected $licenseData;
    protected $pluginSlug;
    protected $remoteUrl;
    protected $licenseFile;
    protected $cacheTimeout = 12 * HOUR_IN_SECONDS;

    public function __construct($pluginSlug, $remoteUrl)
    {
        $this->pluginSlug = $pluginSlug;
        $this->remoteUrl = rtrim($remoteUrl, '/');
       // $this->licenseFile = plugin_dir_path(__FILE__) . 'license_data.json';

        $this->licenseKey = get_option("{$pluginSlug}_license_key", '');
        $this->licenseData = get_option("{$pluginSlug}_license_data", array());
    }

    public function verifyLicense($force = false)
    {
        $cached = get_transient("{$this->pluginSlug}_license_status");
        if ($cached && !$force) {
            return $cached;
        }

        $response = wp_remote_post("{$this->remoteUrl}/verify", [
            'body' => [
                'license_key' => $this->licenseKey,
                'domain' => $_SERVER['SERVER_NAME'],
                'version' => $this->getPluginVersion(),
            ],
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($data['status']) && $data['status'] === 'valid') {
            set_transient("{$this->pluginSlug}_license_status", true, $this->cacheTimeout);
            /**
             * $data['code'] = '';
             */
            $this->saveLicenseData($data);
            return true;
        }

        set_transient("{$this->pluginSlug}_license_status", false, $this->cacheTimeout);
        return false;
    }

    protected function getPluginVersion()
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $pluginFile = WP_PLUGIN_DIR . "/{$this->pluginSlug}/{$this->pluginSlug}.php";
        $data = get_plugin_data($pluginFile, false, false);
        return $data['Version'] ?? '0.0.0';
    }

    protected function saveLicenseData($data)
    {
        // file_put_contents($this->licenseFile, json_encode($data));
        update_option("{$this->pluginSlug}_license_data", $data);
    }

    public function isLicenseValid()
    {
        return $this->verifyLicense();
    }

    public function getLicenseData()
    {
        $data = get_option("{$this->pluginSlug}_license_data");
        if (! $data) {
            return null;
        }
        return json_decode($data, true);
    }

    public function setLicenseKey($key)
    {
        $this->licenseKey = $key;
        update_option("{$this->pluginSlug}_license_key", $key);
        delete_transient("{$this->pluginSlug}_license_status");
    }
}
