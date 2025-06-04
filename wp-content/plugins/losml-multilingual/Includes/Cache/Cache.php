<?php
namespace Losml\Includes\Cache;

/**
 * Cache-related utilities for product and term data.
 */
class Cache
{
    protected $losml;

    public function __construct($losml)
    {
        $this->losml = $losml;
    }

    /**
     * Delete all cache entries related to a product.
     *
     * @param int|string $productId The product ID.
     */
    public function deleteProductAllCache($productId)
    {
        $cacheGroups = [
            'posts',
            'post_meta',
            'product_shipping_class_relationships',
            'product_cat_relationships',
            'post_tag_relationships',
            'product_visibility_relationships',
            'category_relationships',
            'product_tag_relationships',
            'product_type_relationships'
        ];

        foreach ($cacheGroups as $group) {
            wp_cache_delete($productId, $group);
        }

        $transientGroup = 'transient';
        $transientKeys = [
            "wc_var_prices_{$productId}",
            "wc_child_has_dimensions_{$productId}",
            "wc_child_has_weight_{$productId}",
            "wc_product_children_{$productId}",
            "wc_related_{$productId}"
        ];

        foreach ($transientKeys as $key) {
            wp_cache_delete($key, $transientGroup);
        }
    }

    /**
     * Delete product price-related cache.
     *
     * @param int|string $productId The product ID.
     */
    public function deletePriceCache($productId)
    {
        $cacheGroups = [
            'posts',
            'post_meta',
            'product_cat_relationships',
            'product_tag_relationships',
            'product_visibility_relationships',
            'product_shipping_class_relationships',
            'product_type_relationships'
        ];

        foreach ($cacheGroups as $group) {
            wp_cache_delete($productId, $group);
        }

        // Optionally flush a whole group:
        // wp_cache_flush_group('post_meta');
    }

    /**
     * Delete term relationships cache for a given term ID.
     *
     * @param int|string $termId The term ID.
     */
    public function deleteTermRelationshipsCache($termId)
    {
        $relationshipGroups = [
            'product_shipping_class',
            'category',
            'post_tag',
            'product_cat',
            'product_tag',
            'product_type',
            'product_visibility'
        ];

        foreach ($relationshipGroups as $group) {
            wp_cache_delete($termId, "{$group}_relationships");
        }

        // Additional meta-related cache
        wp_cache_delete($termId, 'post_meta');
    }

    /**
     * Get RocketCDN URLs for language-specific cache invalidation.
     *
     * @param array $urls Default URL list.
     * @param string $lang Current language code.
     * @return array Updated URL list for RocketCDN.
     */
    public function getRocketUrls($urls, $lang)
    {
        $hosts = array_column($this->losml->active_language_data, 'host');
        return !empty($hosts) ? $hosts : $urls;
    }
}
