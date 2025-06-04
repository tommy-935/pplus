<?php
namespace Losml\Includes\Filter;

/**
 * Handles multilingual navigation menu logic.
 */
class MenuManager
{
    protected $losml;

    public function __construct($losml)
    {
        $this->losml = $losml;
    }

    /**
     * Filter navigation menu locations to return the translated menu ID.
     *
     * @param array $themeLocations
     * @return array
     */
    public function filterNavMenuLocations(array $themeLocations): array
    {
        foreach ($themeLocations as $location => $menuId) {
            $translatedMenuId = $this->losml->getTransElementId($menuId, 'tax_nav_menu');
            if ($translatedMenuId) {
                $themeLocations[$location] = $translatedMenuId;
            }
        }

        return $themeLocations;
    }

    /**
     * Filter nav menu arguments to apply translated menu.
     *
     * @param array $args
     * @return array
     */
    public function navMenuArgsFilter(array $args): array
    {
        if (empty($args['menu'])) {
            $locations = get_nav_menu_locations();

            if (!empty($args['theme_location']) && isset($locations[$args['theme_location']])) {
                $translatedMenuId = $this->losml->getTransElementId($locations[$args['theme_location']], 'tax_nav_menu');
                $args['menu'] = $translatedMenuId;
            }
        }

        if (!is_object($args['menu']) && is_numeric($args['menu'])) {
            $args['menu'] = wp_get_nav_menu_object((int) $args['menu']);
        }

        return $args;
    }

    /**
     * Get the recently edited menu ID for the current language.
     *
     * @param mixed  $result
     * @param string $option
     * @param int    $user
     * @return mixed
     */
    public function getRecentlyEditMenuId($result, $option, $user)
    {
        if (!$result) {
            return $result;
        }

        $menuId = (int) $result;
        $sql = sprintf(
            "SELECT id FROM %slosml_strings WHERE element_id = %d AND language_id = %d LIMIT 1",
            $this->losml->wpdb->prefix,
            $menuId,
            (int) LOSML_MULTI_LANGUAGE_ID
        );

        $translatedId = $this->losml->wpdb->get_var($sql);

        return $translatedId ? $result : 0;
    }
}
