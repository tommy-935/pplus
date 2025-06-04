<?php
namespace Losml\Includes\Widget;

/**
 * Widget related functionality
 */
class Widget {

    public $losml;

    public function __construct($losml) {
        $this->losml = $losml;
    }

    /**
     * Display filter
     *
     * @param array $instance
     * @return array|bool
     */
    public function displayFilter($instance) {
        // If instance is empty or the widget should be displayed
        if (!$instance || $this->shouldDisplay($instance)) {
            return $instance;
        }

        return false;
    }

    /**
     * Returns display status of the widget as a boolean.
     *
     * @param array $instance
     * @return bool
     */
    private function shouldDisplay($instance) {
        // If 'wpml_language' is not set or matches the current language code or set to 'all'
        return !array_key_exists('wpml_language', $instance)
            || $instance['wpml_language'] === LOSML_MULTI_LANGUAGE_CODE
            || 'all' === $instance['wpml_language'];
    }
}
