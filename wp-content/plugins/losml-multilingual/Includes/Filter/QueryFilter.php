<?php
namespace Losml\Includes\Filter;
/**
 * Query filter logic for multi-language and SEO integration.
 */
class QueryFilter
{
    protected $losml;

    /**
     * Post types to ignore in filtering.
     *
     * @var array
     */
    protected $ignoredTypes = [
        'product_variation',
        'attachment',
        'shop_order',
        'shop_order_refund',
        'shop_coupon',
        'videos',
        'gift_card',
        'ufaq',
        'sidebar',
        'wpcf7_contact_form',
        'aiosrs-schema',
        'oembed_cache',
    ];

    public function __construct($losml)
    {
        $this->losml = $losml;
        $this->addHooks();
    }

    public function addHooks()
    {
        add_action('parse_query', [$this, 'postsQueryFilter'], 99, 2 );
        
        add_filter( 'posts_join', [$this, 'postsJoinFilter'], 99, 2 );

        
        add_filter( 'posts_where', [$this, 'postsWhereFilter'], 99, 2 );
    }

    /**
     * Filter for main posts JOIN clause.
     */
    public function postsJoinFilter($join, $wp_query)
    {
        global $wpdb;

        $postType = $wp_query->query['post_type'] ?? null;

        if (is_array($postType)) {
            foreach ($postType as $type) {
                if (in_array($type, $this->ignoredTypes, true)) {
                    return $join;
                }
            }
        } elseif (in_array($postType, $this->ignoredTypes, true)) {
            return $join;
        }

        $transTable = $wpdb->prefix . 'losml_translations';
        $postsTable = $wpdb->prefix . 'posts';

        $join .= " LEFT JOIN {$transTable} AS trans_losml ON {$postsTable}.ID = trans_losml.element_id ";
        $join .= "AND trans_losml.element_type = CONCAT('post_', {$postsTable}.post_type) ";

        return $join;
    }

    /**
     * Filter for wpSEO type count JOIN.
     */
    public function wpseoTypecountJoin($postType)
    {
        $transTable = $this->losml->wpdb->prefix . 'losml_translations';
        $postsTable = $this->losml->wpdb->prefix . 'posts';

        return " LEFT JOIN {$transTable} AS trans_losml ON {$postsTable}.ID = trans_losml.element_id " .
               "AND trans_losml.element_type = CONCAT('post_', {$postsTable}.post_type) ";
    }

    /**
     * Filter for wpSEO type count WHERE clause.
     */
    public function wpseoTypecountWhere($postType)
    {
        return ' AND trans_losml.language_id = ' . (int) LOSML_MULTI_LANGUAGE_ID;
    }

    /**
     * Filter for wpSEO post JOIN.
     */
    public function wpseoPostJoin($postType)
    {
        return $this->wpseoTypecountJoin($postType);
    }

    /**
     * Filter for wpSEO post WHERE clause.
     */
    public function wpseoPostWhere($postType)
    {
        return $this->wpseoTypecountWhere($postType);
    }

    /**
     * Filter for page ID in multilingual queries.
     */
    public function postsQueryFilter($query)
    {
        if (! $query->is_home && !empty($query->query_vars['page_id'])) {
            global $wpdb;

            $pageId = (int) $query->query_vars['page_id'];

            $sql = "SELECT element_id FROM {$wpdb->prefix}losml_strings 
                    WHERE element_type = 'post_page' 
                    AND source_element_id = {$pageId} 
                    AND language_id = " . (int) LOSML_MULTI_LANGUAGE_ID;

            $result = $wpdb->get_col($sql);

            if (!empty($result[0])) {
                $query->query_vars['page_id'] = (int) $result[0];
            }
        }
    }

    /**
     * Filter for posts WHERE clause.
     */
    public function postsWhereFilter($where, $wp_query)
    {
        if(! defined('LOSML_MULTI_LANGUAGE_ID')){
            return $where;
        }
        $postType = $wp_query->query['post_type'] ?? null;

        if (is_array($postType)) {
            foreach ($postType as $type) {
                if (in_array($type, $this->ignoredTypes, true)) {
                    return $where;
                }
            }
        } elseif (in_array($postType, $this->ignoredTypes, true)) {
            return $where;
        }

        $where .= ' AND trans_losml.language_id = ' . (int) LOSML_MULTI_LANGUAGE_ID;
        return $where;
    }
}
