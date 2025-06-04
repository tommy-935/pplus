<?php

namespace Losml\Includes\Filter;

/**
 * Multilingual Post Handler
 */
class PostManager
{

    public $losml;
    public $wpdb;
    const AJAX_DUPLICATE_KEY = 'losmlUIxjue83jfDUeof=948k.[]/d';

    public function __construct($losml)
    {
        global $wpdb;
        $this->losml = $losml;
        $this->wpdb = $wpdb;
        $this->addHooks();
    }

    public function addHooks(){
        add_filter( 'wp_unique_post_slug', [$this, 'postnameFilter'], 100, 6);
    }

    public function losml_clear_product_cache()
    {
        check_ajax_referer('losml_clear_product_cache', 'security');

        $product_id = intval($_POST['product_id']);
        if (!$product_id) {
            wp_send_json_error(['error' => 'Invalid product ID']);
        }

        $variations = $this->wpdb->get_col(
            $this->wpdb->prepare(
                "SELECT ID FROM %i WHERE post_type = 'product_variation' AND post_parent = %d",
                $this->wpdb->posts,
                $product_id
            )
        );

        foreach ($variations as $variation_id) {
            $this->losml->obj_cache->deleteProductAllCache($variation_id);
        }

        $this->losml->obj_cache->deleteProductAllCache($product_id);
        wp_send_json_success(['message' => 'Cache cleared successfully']);
    }

    public function losmlSync()
    {
        check_ajax_referer('losml_sync', 'security');

        $product_id = intval($_POST['product_id']);
        if (!$product_id) {
            wp_send_json_error(['error' => 'Invalid product ID']);
        }

        $sync_fields = [
            'image_on' => '_syncImage',
            'stock_on' => '_syncStock',
            'price_on' => '_syncPrice',
            'bp_on'    => '_syncBundleAndLinkProduct'
        ];

        foreach ($sync_fields as $field => $method) {
            if (isset($_POST[$field]) && $_POST[$field] === 'on') {
                $this->$method($product_id);
            }
        }

        wp_send_json_success(['message' => 'Sync completed successfully']);
    }

    public function losml_sync_private()
    {
        check_ajax_referer('losml_sync_private', 'security');

        $product_id = intval($_POST['product_id']);
        $visibility = sanitize_text_field($_POST['visibility']);

        if (!$product_id || !$visibility) {
            wp_send_json_error(['error' => 'Missing required parameters']);
        }

        $query = $this->wpdb->prepare(
            "SELECT d.element_id, d.language_id 
             FROM %i AS a
             JOIN %i AS d ON d.source_element_id = a.ID
             WHERE a.ID = %d AND d.language_id != 1",
            $this->wpdb->posts,
            $this->losml->losml_strings_table,
            $product_id
        );

        $results = $this->wpdb->get_results($query, ARRAY_A);

        foreach ($results as $row) {
            $lang_product_id = intval($row['element_id']);
            if ($lang_product_id) {
                $status = $visibility === 'public' ? 'publish' : $visibility;

                $this->wpdb->update(
                    $this->wpdb->posts,
                    ['post_status' => $status],
                    ['ID' => $lang_product_id]
                );

                $this->losml->obj_cache->deleteProductAllCache($lang_product_id);
            }
        }

        wp_send_json_success(['message' => 'Visibility updated successfully']);
    }

    private function _syncBundleAndLinkProduct($product_id)
    {
        // Handle link products
        $link_query = $this->wpdb->prepare(
            "SELECT e.meta_value AS link_product, f.element_id AS lang_product_id, f.language_id 
             FROM %i AS a
             JOIN %i AS d ON d.source_element_id = a.ID
             JOIN %i AS f ON f.source_element_id = d.element_id
             JOIN %i AS e ON e.post_id = a.ID
             WHERE a.ID = %d 
               AND d.language_id = 1 
               AND f.id IS NOT NULL
               AND e.meta_key = %s",
            $this->wpdb->posts,
            $this->losml->losml_strings_table,
            $this->losml->losml_strings_table,
            $this->wpdb->postmeta,
            $product_id,
            '_upsell_ids3'
        );

        $link_results = $this->wpdb->get_results($link_query, ARRAY_A);

        foreach ($link_results as $row) {
            if (empty($row['link_product'])) continue;

            $ids = unserialize($row['link_product']);
            if (!is_array($ids)) continue;

            $placeholders = implode(',', array_fill(0, count($ids), '%d'));

            $query = $this->wpdb->prepare(
                "SELECT b.element_id 
                 FROM %i AS a
                 JOIN %i AS b ON a.element_id = b.source_element_id
                 WHERE a.language_id = 1 
                   AND b.language_id = %d
                   AND a.element_type = %s
                   AND b.element_type = %s
                   AND a.element_id IN ($placeholders)",
                $this->losml->losml_strings_table,
                $this->losml->losml_strings_table,
                $row['language_id'],
                'post_product',
                'post_product',
                $ids
            );

            $new_ids = $this->wpdb->get_col($query);

            $old = get_post_meta($row['lang_product_id'], '_upsell_ids3', true);
            if ($old === false) {
                add_post_meta($row['lang_product_id'], '_upsell_ids3', $new_ids, true);
            } else {
                update_post_meta($row['lang_product_id'], '_upsell_ids3', $new_ids);
            }

            $this->losml->obj_cache->deleteProductAllCache($row['lang_product_id']);
        }

        // Handle bundles
        $bundle_query = $this->wpdb->prepare(
            "SELECT DISTINCT c.element_id, c.language_id 
             FROM %i AS a
             JOIN %i AS b ON b.element_id = a.post_id
             JOIN %i AS c ON b.element_id = c.source_element_id
             WHERE a.post_id = %d",
            $this->losml->cart_product_boudle_table,
            $this->losml->losml_strings_table,
            $this->losml->losml_strings_table,
            $product_id
        );

        $bundle_results = $this->wpdb->get_results($bundle_query, ARRAY_A);

        $bundle_items = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM %i WHERE post_id = %d",
                $this->losml->cart_product_boudle_table,
                $product_id
            ),
            ARRAY_A
        );

        foreach ($bundle_results as $bundle) {
            $this->wpdb->delete(
                $this->losml->cart_product_boudle_table,
                ['post_id' => $bundle['element_id']]
            );

            foreach ($bundle_items as $item) {
                $lang_p_id = $this->_getLangPid($bundle['language_id'], $item['post_id_re']);

                if (!$lang_p_id) continue;

                $data = [
                    'post_id' => $bundle['element_id'],
                    'post_id_re' => $lang_p_id,
                    'order' => $item['order'],
                    'discount_rate' => $item['discount_rate']
                ];

                $this->wpdb->insert($this->losml->cart_product_boudle_table, $data);
            }
        }
    }

    private function _getLangPid($langid, $pid)
    {
        $query = $this->wpdb->prepare(
            "SELECT c.element_id 
             FROM %i AS b
             JOIN %i AS c ON b.element_id = c.source_element_id
             WHERE b.element_type = %s
               AND b.element_id = %d
               AND c.language_id = %d",
            $this->losml->losml_strings_table,
            $this->losml->losml_strings_table,
            'post_product',
            $pid,
            $langid
        );

        return $this->wpdb->get_var($query);
    }

    private function _syncPrice($product_id)
    {
        $query = $this->wpdb->prepare(
            "SELECT a.ID AS product_id, 
                    b.ID AS variation_id, 
                    c.meta_value AS price, 
                    c.meta_key AS price_key, 
                    d.element_id AS lang_product_id, 
                    d.language_id, 
                    cc.meta_value AS sku, 
                    f.ID AS lang_variation_id  
             FROM %i AS a
             LEFT JOIN %i AS b ON a.ID = b.post_parent
             LEFT JOIN %i AS c ON b.ID = c.post_id
             LEFT JOIN %i AS cc ON b.ID = cc.post_id
             LEFT JOIN %i AS d ON d.source_element_id = a.ID
             LEFT JOIN %i AS e ON e.ID = d.element_id
             LEFT JOIN %i AS f ON f.post_parent = e.ID
             LEFT JOIN %i AS g ON g.post_id = f.ID
             WHERE a.ID = %d 
               AND d.language_id != 1 
               AND d.id IS NOT NULL 
               AND g.meta_value = cc.meta_value
               AND c.meta_key IN (%s, %s, %s)
               AND cc.meta_key = %s
               AND g.meta_key = %s",
            $this->wpdb->posts,
            $this->wpdb->posts,
            $this->wpdb->postmeta,
            $this->wpdb->postmeta,
            $this->losml->losml_strings_table,
            $this->wpdb->posts,
            $this->wpdb->posts,
            $this->wpdb->postmeta,
            $product_id,
            '_regular_price',
            '_sale_price',
            '_price',
            '_sku',
            '_sku'
        );

        $results = $this->wpdb->get_results($query, ARRAY_A);
        $variations = [];

        foreach ($results as $row) {
            $variations[$row['lang_variation_id']] = $row['lang_variation_id'];
            update_post_meta($row['lang_variation_id'], $row['price_key'], $row['price']);
        }

        foreach ($variations as $variation_id) {
            $this->losml->obj_cache->deleteProductAllCache($variation_id);
        }
    }

    private function _syncPrivate($product_id, $visibility)
    {
        $query = $this->wpdb->prepare(
            "SELECT d.element_id, d.language_id 
             FROM %i AS a
             JOIN %i AS d ON d.source_element_id = a.ID
             JOIN %i AS e ON e.ID = d.element_id
             WHERE a.ID = %d 
               AND d.language_id != 1",
            $this->wpdb->posts,
            $this->losml->losml_strings_table,
            $this->wpdb->posts,
            $product_id
        );

        $results = $this->wpdb->get_results($query, ARRAY_A);

        foreach ($results as $row) {
            $lang_product_id = intval($row['element_id']);
            if ($lang_product_id) {
                $status = $visibility === 'public' ? 'publish' : $visibility;

                $this->wpdb->update(
                    $this->wpdb->posts,
                    ['post_status' => $status],
                    ['ID' => $lang_product_id]
                );

                $this->losml->obj_cache->deleteProductAllCache($lang_product_id);
            }
        }
    }

    private function _syncStock($product_id)
    {
        $query = $this->wpdb->prepare(
            "SELECT a.ID AS product_id, 
                    b.ID AS variation_id, 
                    c.meta_key AS stock_key, 
                    c.meta_value AS stock_val, 
                    d.element_id AS lang_product_id, 
                    d.language_id, 
                    cc.meta_value AS sku, 
                    f.ID AS lang_variation_id  
             FROM %i AS a
             LEFT JOIN %i AS b ON a.ID = b.post_parent
             LEFT JOIN %i AS c ON b.ID = c.post_id
             LEFT JOIN %i AS cc ON b.ID = cc.post_id
             LEFT JOIN %i AS d ON d.source_element_id = a.ID
             LEFT JOIN %i AS e ON e.ID = d.element_id
             LEFT JOIN %i AS f ON f.post_parent = e.ID
             LEFT JOIN %i AS g ON g.post_id = f.ID
             WHERE a.ID = %d 
               AND d.language_id != 1 
               AND d.id IS NOT NULL 
               AND g.meta_value = cc.meta_value
               AND (c.meta_key LIKE %s OR c.meta_key LIKE %s)
               AND cc.meta_key = %s
               AND g.meta_key = %s",
            $this->wpdb->posts,
            $this->wpdb->posts,
            $this->wpdb->postmeta,
            $this->wpdb->postmeta,
            $this->losml->losml_strings_table,
            $this->wpdb->posts,
            $this->wpdb->posts,
            $this->wpdb->postmeta,
            $product_id,
            '%stock%',
            '%price_method%',
            '_sku',
            '_sku'
        );

        $results = $this->wpdb->get_results($query, ARRAY_A);
        $variations = [];

        foreach ($results as $row) {
            $variations[$row['lang_variation_id']] = $row['lang_variation_id'];
            update_post_meta($row['lang_variation_id'], $row['stock_key'], $row['stock_val']);
        }

        foreach ($variations as $variation_id) {
            $this->losml->obj_cache->deleteProductAllCache($variation_id);
        }
    }

    private function _syncImage($product_id)
    {
        $query = $this->wpdb->prepare(
            "SELECT a.ID AS product_id, 
                    b.ID AS variation_id, 
                    c.meta_value AS _thumbnail_id, 
                    aa.meta_value AS product_main_img_id, 
                    aaa.meta_value AS gallery_ids, 
                    d.element_id AS lang_product_id, 
                    d.language_id, 
                    cc.meta_value AS sku, 
                    f.ID AS lang_variation_id  
             FROM %i AS a
             LEFT JOIN %i AS b ON a.ID = b.post_parent
             LEFT JOIN %i AS c ON b.ID = c.post_id
             LEFT JOIN %i AS cc ON b.ID = cc.post_id
             LEFT JOIN %i AS d ON d.source_element_id = a.ID
             LEFT JOIN %i AS e ON e.ID = d.element_id
             LEFT JOIN %i AS f ON f.post_parent = e.ID
             LEFT JOIN %i AS g ON g.post_id = f.ID
             LEFT JOIN %i AS aa ON a.ID = aa.post_id
             LEFT JOIN %i AS aaa ON a.ID = aaa.post_id
             WHERE a.ID = %d 
               AND d.language_id != 1 
               AND d.id IS NOT NULL 
               AND g.meta_value = cc.meta_value
               AND c.meta_key = %s
               AND cc.meta_key = %s
               AND aa.meta_key = %s
               AND aaa.meta_key = %s",
            $this->wpdb->posts,
            $this->wpdb->posts,
            $this->wpdb->postmeta,
            $this->wpdb->postmeta,
            $this->losml->losml_strings_table,
            $this->wpdb->posts,
            $this->wpdb->posts,
            $this->wpdb->postmeta,
            $this->wpdb->postmeta,
            $this->wpdb->postmeta,
            $product_id,
            '_thumbnail_id',
            '_sku',
            '_thumbnail_id',
            '_product_image_gallery'
        );

        $results = $this->wpdb->get_results($query, ARRAY_A);

        foreach ($results as $row) {
            update_post_meta($row['lang_variation_id'], '_thumbnail_id', $row['_thumbnail_id']);
            $this->losml->obj_cache->deleteProductAllCache($row['lang_variation_id']);

            update_post_meta($row['lang_product_id'], '_thumbnail_id', $row['product_main_img_id']);
            update_post_meta($row['lang_product_id'], '_product_image_gallery', $row['gallery_ids']);
            $this->losml->obj_cache->deleteProductAllCache($row['lang_product_id']);
        }
    }

    public function displayMetaBox()
    {
        global $post;
        if ($post->post_type !== "product" || LOSML_MULTI_LANGUAGE_ID != 1) {
            return;
        }

        $product_id = $post->ID;

        $html = '<div class="postbox-container" style="margin-top:40px;">';
        $html .= '<div class="meta-box-sortables ui-sortable">';
        $html .= '<div class="postbox">';
        $html .= '<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">切换面板：Product Bundle</span></button>';
        $html .= '<h2 class="hndle ui-sortable-handle"><span>Multi Language Synchronous</span></h2>';
        $html .= '<div class="meta-box-sortables ui-sortable">';
        $html .= '<div class="adisc-list" id="adisc-list">';
        $html .= '<form class="layui-form" action="javascript:;">';

        // Form fields...
        $html .= $this->buildMetaBoxFormFields();

        // Scripts and styles
        $html .= $this->buildMetaBoxScripts($product_id);
        $html .= $this->buildMetaBoxStyles();

        echo $html;
    }

    private function buildMetaBoxFormFields()
    {
        $fields = [
            'sync_images' => 'sync main images',
            'sync_stock' => 'sync stocks',
            'sync_price' => 'sync prices',
            'sync_bp' => 'sync bundle & Link Product'
        ];

        $output = '';
        foreach ($fields as $id => $title) {
            $output .= "<div class=\"layui-form-item\">";
            $output .= "<label class=\"layui-form-label losml-label\">$title</label>";
            $output .= "<div class=\"layui-input-block losml-checkbox\">";
            $output .= "<input type=\"checkbox\" id=\"$id\" name=\"$id\" title=\"$title\" lay-skin=\"primary\">";
            $output .= "</div></div>";
        }

        $output .= "<div class=\"layui-form-item\">";
        $output .= "<div class=\"layui-input-block\">";
        $output .= "<button class=\"layui-btn layui-btn-sm\" lay-submit lay-filter=\"form-sync-losml\">Save</button>";
        $output .= "<button type=\"button\" class=\"layui-btn layui-btn-sm\" id=\"btn-clear-redis\">clear redis cache</button>";
        $output .= "<button type=\"button\" class=\"layui-btn layui-btn-sm\" id=\"btn-build-sql\">build sql</button>";
        $output .= "</div></div>";

        return $output;
    }

    private function buildMetaBoxStyles()
    {
        return '
        <style>
        .losml-label { width: 120px; }
        .losml-checkbox { padding-top: 14px; }
        .adisc_discount_code { width: 300px; }
        .adisc-item { display: flex; margin-bottom: 10px; }
        .adisc-name { width: 200px; line-height: 2; }
        .adisc-list { padding-left: 12px; padding-top: 20px; }
        </style>';
    }

    private function buildMetaBoxScripts($product_id)
    {
        return <<<JS
        <script>
        jQuery(function($) {
            var product_id = {$product_id};
            var saving = false;
            
            var $lypd_loading = {
                show: function() { $("#lypd-loading").show(); },
                hide: function() { $("#lypd-loading").hide(); }
            };
            
            layui.use("form", function(){
                var form = layui.form;
                form.on("submit(form-sync-losml)", function(data){
                    if(saving) return;
                    
                    saving = true;
                    data = data.field;
                    data.action = "losml_sync";
                    data.product_id = product_id;
                    
                    $lypd_loading.show();
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: data,
                        dataType: "json",
                        success: function(res){
                            $lypd_loading.hide();
                            alert(res.msg);
                            saving = false;
                        }
                    });
                    return false;
                });
            });
            
            $("#btn-clear-redis").on("click", function(){
                $lypd_loading.show();
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {action: "losml_clear_product_cache", product_id: product_id},
                    dataType: "json",
                    success: function(res){
                        $lypd_loading.hide();
                        alert(res.msg);
                    }
                });
            });
        });
        </script>
        JS;
    }

    public function losml_product_build_sql()
    {
        $product_id = intval($_POST['product_id']);
        if (!$product_id) {
            wp_send_json_error(['error' => 'Invalid product ID']);
        }

        // Main product SQL
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM %i WHERE ID = %d LIMIT 1",
                $this->wpdb->posts,
                $product_id
            ),
            ARRAY_A
        );

        $columns = [];
        foreach ($row as $field => $value) {
            if ($field !== 'ID') {
                $columns[] = $this->wpdb->prepare("`%s` = '%s'", $field, $value);
            }
        }

        $sql = "INSERT INTO losml_posts SET " . implode(', ', $columns) . ";";
        $meta_sql = $this->buildPostMetaSQL($product_id);

        echo $sql . "\r\n" . $meta_sql . "\r\n\r\n";

        // Variations SQL
        $variations = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM %i WHERE post_type = 'product_variation' AND post_parent = %d",
                $this->wpdb->posts,
                $product_id
            ),
            ARRAY_A
        );

        foreach ($variations as $variation) {
            $variation_id = $variation['ID'];
            unset($variation['ID']);

            $columns = [];
            foreach ($variation as $field => $value) {
                $columns[] = $this->wpdb->prepare("`%s` = '%s'", $field, $value);
            }

            $sql = "INSERT INTO losml_posts SET " . implode(', ', $columns) . ";";
            $meta_sql = $this->buildPostMetaSQL($variation_id);

            echo $sql . "\r\n" . $meta_sql . "\r\n\r\n";
        }

        exit;
    }

    private function buildPostMetaSQL($post_id)
    {
        $meta = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT meta_key, meta_value FROM %i WHERE post_id = %d",
                $this->wpdb->postmeta,
                $post_id
            ),
            ARRAY_A
        );

        $values = [];
        foreach ($meta as $meta_row) {
            $values[] = $this->wpdb->prepare(
                "(%d, %s, %s)",
                $post_id,
                $meta_row['meta_key'],
                addslashes($meta_row['meta_value'])
            );
        }

        return "INSERT INTO losml_postmeta (post_id, meta_key, meta_value) VALUES " . implode(',', $values);
    }

    public function afterInsertPost($post_id, $post = [], $update = false)
    {
        if ($update || !isset($_POST['data']['wp_autosave'])) {
            return;
        }

        $post_type = $_POST['data']['wp_autosave']['post_type'];
        return $this->addTrans($post_id, $post_id, LOSML_MULTI_LANGUAGE_ID, $post_type);
    }

    public function makeDuplicatePost($id = 0, $langid = 0, $post_type = '')
    {
        if (!$id) {
            check_ajax_referer(self::AJAX_DUPLICATE_KEY, 'nonce');
            $post_id = intval($_POST['id']);
            $langid = intval($_POST['langid']);
            $post_type = sanitize_text_field($_POST['post_type']);
        } else {
            $post_id = $id;
        }

        if (!current_user_can('edit_post', $post_id) && !$id) {
            wp_send_json_error(['error' => 'Permission denied']);
        }

        if ($post_type == 'product') {
            $terms = $this->checkTermIsTrans($post_id, $langid);
            if (!$terms) {
                if ($id) {
                    return ['status' => false, 'error' => 'The category of the corresponding product has not been translated, please translate it first'];
                }
                wp_send_json_error(['error' => '对应产品的分类还未翻译，请先翻译']);
            }
        }

        $origin_post = $this->getPost($post_id, $langid);
        $this->losml->transBegin();

        $new_id = $this->addPost($origin_post);
        if (!$new_id) {
            $this->losml->transRollback();
            if ($id) {
                return ['status' => false, 'error' => '添加主产品失败'];
            }
            wp_send_json_error(['error' => '添加主产品失败']);
        }

        if ($post_type == 'product') {
            if ($this->updatePostTerm($new_id, $terms) === false) {
                $this->losml->transRollback();
                if ($id) {
                    return ['status' => false, 'error' => '更新产品的分类失败'];
                }
                wp_send_json_error(['error' => '更新产品的分类失败']);
            }
        }

        if ($new_id) {
            $origin_meta = $this->getPostmeta($post_id);
            $ret_meta = $this->addPostmeta($new_id, $origin_meta);

            if ($ret_meta) {
                $ret = $this->addTrans($new_id, $post_id, $langid, $origin_post->post_type);
                if ($ret) {
                    $data['url'] = $this->getTransUrl($new_id, $langid, $this->findCode($langid));
                    $this->losml->transCommit();
                    return wp_send_json_success(['url' => $data['url']]);
                }
            }

            $this->losml->transRollback();
            if ($id) {
                return ['status' => false, 'error' => 'update meta error'];
            }
            wp_send_json_error(['error' => 'update meta error']);
        }
    }

    public function findCode($id)
    {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT [code](file://e:\www\esrgear.com\blog\wp-content\plugins\sitepress-multilingual-cms\res\js\content-translation.js#L103-L103) FROM losml_losml_langs WHERE id = %d LIMIT 1",
                $id
            )
        );
    }

    public function postnameFilter($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug)
    {
        return $original_slug;
    }

    public function updatePostTerm($id, $term)
    {
        if (!is_array($term)) {
            return false;
        }

        $term = array_filter($term);
        if (!$term) {
            return false;
        }

        $taxonomy = $this->wpdb->prefix . 'term_taxonomy';
        $relationships = $this->wpdb->prefix . 'term_relationships';

        $existing = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT a.object_id, a.term_taxonomy_id 
                 FROM $relationships AS a
                 LEFT JOIN $taxonomy AS b ON a.term_taxonomy_id = b.term_taxonomy_id
                 WHERE b.taxonomy = 'product_cat' AND a.object_id = %d",
                $id
            ),
            ARRAY_A
        );

        if ($existing === false || count($existing) > 1) {
            return false;
        }

        if (!$existing) {
            $query = "INSERT INTO $relationships (object_id, term_taxonomy_id, term_order) VALUES ";
            $values = [];

            foreach ($term as $term_id) {
                $values[] = $this->wpdb->prepare("(%d, %d, %d)", $id, $term_id, 0);
            }

            $values[] = $this->wpdb->prepare("(%d, %d, %d)", $id, 4, 0);
            $query .= implode(',', $values);
        } else {
            $query = $this->wpdb->prepare(
                "UPDATE $relationships 
                 SET term_taxonomy_id = %d 
                 WHERE term_taxonomy_id = %d AND object_id = %d",
                $term[0],
                $existing[0]['term_taxonomy_id'],
                $existing[0]['object_id']
            );
        }

        return $this->wpdb->query($query);
    }

    public function checkTermIsTrans($post_id, $langid)
    {
        $taxonomy = $this->wpdb->prefix . 'term_taxonomy';
        $relationships = $this->wpdb->prefix . 'term_relationships';
        $losml_strings = $this->wpdb->prefix . 'losml_strings';

        $query = $this->wpdb->prepare(
            "SELECT d.term_taxonomy_id 
             FROM $relationships AS a
             LEFT JOIN $taxonomy AS b ON a.term_taxonomy_id = b.term_taxonomy_id
             LEFT JOIN $losml_strings AS c ON c.source_element_id = b.term_id
             LEFT JOIN $taxonomy AS d ON d.term_id = c.element_id
             WHERE b.taxonomy = 'product_cat' 
               AND c.id IS NOT NULL 
               AND a.object_id = %d
               AND c.element_type = %s
               AND c.language_id = %d",
            $post_id,
            'tax_product_cat',
            $langid
        );

        return $this->wpdb->get_col($query);
    }

    public function dealChildPost($post_id, $new_id, $post_type = '')
    {
        $ids = $this->getChildPostIds($post_id);
        if ($ids) {
            foreach ($ids as $id) {
                $this->dealOnePost($id, $new_id, $post_type);
            }
        }
    }

    public function deletePostTransient($post_id)
    {
        $children_transient_name = 'wc_product_children_' . $post_id;
        return delete_transient($children_transient_name);
    }

    public function dealOnePost($id, $new_id, $post_type = '')
    {
        $origin_post = $this->getPost($id);
        $origin_post->post_parent = $new_id;
        $new_id = $this->addPost($origin_post);

        if ($new_id === false) {
            $this->losml->transRollback();
            return wp_send_json_error(['error' => 'Error inserting child post']);
        }

        if ($new_id) {
            $origin_meta = $this->getPostmeta($id);
            $ret_meta = $this->addPostmeta($new_id, $origin_meta);

            if ($ret_meta === false) {
                $this->losml->transRollback();
                return wp_send_json_error(['error' => 'Error inserting child post']);
            }

            if ($post_type == 'product') {
                $this->losml->obj_cache->deleteTermRelationshipsCache($new_id);
            }
        }
    }

    public function getChildPostIds($post_id)
    {
        return $this->wpdb->get_col(
            $this->wpdb->prepare(
                "SELECT ID FROM {$this->wpdb->posts} WHERE post_type != 'revision' AND post_parent = %d",
                $post_id
            )
        );
    }

    public function getTransUrl($id, $langid, $code = '')
    {
        $url = get_edit_post_link($id);
        return $this->losml->obj_url->transUrl($url, $langid, $code);
    }

    public function getPost($post_id, $langid = 0)
    {
        $post = get_post($post_id);
        if (!$post) {
            return null;
        }

        // Create a new post object with sanitized properties
        $new_post = new \stdClass();
        $new_post->ID = '';
        $new_post->post_author = get_current_user_id();
        $new_post->post_date = current_time('mysql');
        $new_post->post_date_gmt = current_time('mysql', 1);
        $new_post->post_modified = $new_post->post_date;
        $new_post->post_modified_gmt = $new_post->post_date_gmt;
        $new_post->post_content = $post->post_content;

        // Handle product ID translation for pages
        if ($post->post_type === 'page') {
            $this->contentProductIdTrans($new_post, $langid);
        }

        return $new_post;
    }

    /**
     * Translate product IDs in content for multilingual support
     */
    public function contentProductIdTrans(&$post, $langid)
    {
        if (empty($post->post_content)) {
            return;
        }

        // Find all product IDs in ux_products shortcodes
        $regx = '/$$ux_products[^\]]+ids\="([^"\$$^\\s]+)"/i';
        preg_match_all($regx, $post->post_content, $matches);

        if (empty($matches[1])) {
            return;
        }

        // Extract unique product IDs
        $ids = [];
        foreach ($matches[1] as $v) {
            $ids = array_merge($ids, explode(',', $v));
        }

        $ids = array_unique(array_filter($ids));

        if (empty($ids)) {
            return;
        }

        // Get translated IDs
        $trans_results = $this->losml->getTransElementIdArr($ids, 'post_product', $langid);
        if (empty($trans_results)) {
            return;
        }

        $translations = array_column($trans_results, 'to_element_id', 'element_id');

        // Replace original IDs with translations in content
        foreach ($matches[1] as $original_ids) {
            $original_array = explode(',', $original_ids);
            $translated_array = [];

            foreach ($original_array as $val) {
                $translated_array[] = $translations[$val] ?? $val;
            }

            if (!empty($translated_array)) {
                $post->post_content = str_replace(
                    'ids="' . $original_ids . '"',
                    'ids="' . implode(',', $translated_array) . '"',
                    $post->post_content
                );
            }
        }
    }

    /**
     * Get post meta data securely
     */
    public function getPostmeta($post_id)
    {
        if (!$post_id) {
            return [];
        }

        $query = $this->wpdb->prepare(
            "SELECT meta_key, meta_value FROM %s WHERE post_id = %d",
            $this->wpdb->postmeta,
            $post_id
        );

        return $this->wpdb->get_results($query);
    }

    /**
     * Get translation data for an element
     */
    public function getHasTrans($id, $post_type)
    {
        if (!$id || empty($post_type)) {
            return [];
        }

        $element_type = $this->setTransElementType($post_type);

        $query = $this->wpdb->prepare(
            "SELECT b.language_id, b.element_id, b.source_element_id 
         FROM %s AS a
         LEFT JOIN %s AS b ON a.source_element_id = b.source_element_id
         WHERE a.element_id = %d 
           AND a.element_type = %s
           AND b.element_type = %s",
            $this->wpdb->prefix . 'losml_strings',
            $this->wpdb->prefix . 'losml_strings',
            $id,
            $element_type,
            $element_type
        );

        $results = $this->wpdb->get_results($query);

        // Organize results by language ID
        $data = [];
        foreach ($results as $row) {
            $data[$row->language_id] = $row;
        }

        return $data;
    }

    /**
     * Insert a new post into the database
     */
    public function addPost($data)
    {
        if (!$data) {
            return 0;
        }

        $post_id = wp_insert_post($data);
        return intval($post_id);
    }

    /**
     * Add post meta data efficiently
     */
    public function addPostmeta($new_id, $meta)
    {
        if (!$new_id || empty($meta)) {
            return true;
        }

        $values = [];
        $ignore_keys = ['_edit_lock', '_edit_last'];

        foreach ($meta as $obj) {
            if (in_array($obj->meta_key, $ignore_keys)) {
                continue;
            }

            $values[] = $this->wpdb->prepare(
                "(%d, %s, %s)",
                $new_id,
                $obj->meta_key,
                addslashes($obj->meta_value)
            );
        }

        if (empty($values)) {
            return true;
        }

        $query = "INSERT INTO {$this->wpdb->postmeta} (post_id, meta_key, meta_value) VALUES ";
        $query .= implode(',', $values);

        return $this->wpdb->query($query);
    }

    /**
     * Get translated block ID or fallback to original
     */
    public function getBlocksFilter($post_id)
    {
        $translated_id = $this->losml->getTransElementId($post_id, 'post_blocks');
        return $translated_id ?: $post_id;
    }

    /**
     * Setup admin UI elements conditionally
     */
    public function maybeSetupPostEdit()
    {
        global $pagenow;

        $is_admin_page = in_array($pagenow, ['post.php', 'post-new.php', 'edit.php'], true);
        $is_ajax = defined('DOING_AJAX');

        if ($is_admin_page || $is_ajax) {
            add_action('admin_head', [$this, 'postEditLanguageOptions']);
        }
    }

    /**
     * Display language options meta box
     */
    public function postEditLanguageOptions()
    {
        global $post;

        $excluded_types = [
            'shop_order',
            'shop_coupon',
            'sidebar',
            'gift_card',
            'ufaq',
            'aiosrs-schema'
        ];

        if ($post && in_array($post->post_type, $excluded_types)) {
            return;
        }

        add_meta_box(
            'icl_div',
            __('Language', 'losml'),
            [$this, 'showMetaBox'],
            $post->post_type,
            'side',
            'high'
        );
    }

    /**
     * Normalize post type for translation
     */
    public function setTransElementType($type)
    {
        if (!$type) {
            return '';
        }

        switch ($type) {
            case 'product':
            case 'blocks':
            case 'page':
                return "post_{$type}";
            case 'product_cat':
            case 'nav_menu':
            case 'product_tag':
                return "tax_{$type}";
            default:
                return $type;
        }
    }

    /**
     * Generate meta box HTML for language selection
     */
    public function showMetaBox($post)
    {
        global $pagenow;

        $trans_data = $this->getHasTrans($post->ID, $post->post_type);
        $nonce = wp_create_nonce(self::AJAX_DUPLICATE_KEY);

        $html = '<div class="losml-edit-item">';
        $html .= '<a href="/wp-admin/post-edit-all-languge.php?post=' . $post->ID . '">Multi Language Edit</a>';
        $html .= '</div><div>';

        foreach ($this->losml->active_language_data as $lang) {
            $html .= '<div class="big-loading" id="big-loading"></div>';
            $html .= '<div class="losml-edit-item">';
            $html .= '<div class="lang-name">' . esc_html($lang->name) . '</div><div>';

            if (
                $lang->id == LOSML_MULTI_LANGUAGE_ID &&
                (isset($trans_data[$lang->id]) || $pagenow == 'post-new.php')
            ) {
                $html .= '<span>' . __('current language', 'losml') . '</span>';
            } elseif (isset($trans_data[$lang->id])) {
                $edit_url = $this->getTransUrl($trans_data[$lang->id]->element_id, $lang->id, $lang->code);
                $html .= '<a href="' . esc_url($edit_url) . '">' . __('Edit', 'losml') . '</a>';
            } elseif (
                $pagenow != 'post-new.php' &&
                isset($trans_data[LOSML_MULTI_LANGUAGE_ID]->source_element_id) &&
                $post->ID == $trans_data[LOSML_MULTI_LANGUAGE_ID]->source_element_id
            ) {

                $html .= '<button class="button button-small btn-losml-duplicate" type="button" ';
                $html .= 'data-id="' . $post->ID . '" ';
                $html .= 'data-langid="' . $lang->id . '" ';
                $html .= 'data-nonce="' . $nonce . '" ';
                $html .= 'data-post_type="' . $post->post_type . '"';
                $html .= '>' . __('duplicate', 'losml') . '</button>';
            }

            $html .= '</div></div>';
        }

        $html .= '</div>';
        echo $html;
    }

    /**
     * Create translation entry for an element
     */
    public function addTrans($new_id, $post_id, $langid, $post_type)
    {
        if (!$new_id || !$post_id || !$langid || !$post_type) {
            return false;
        }

        $element_type = $this->setTransElementType($post_type);

        $query = $this->wpdb->prepare(
            "INSERT IGNORE INTO {$this->wpdb->prefix}losml_strings 
        (element_type, element_id, source_element_id, language_id) 
        VALUES (%s, %d, %d, %d)",
            $element_type,
            $new_id,
            $post_id,
            $langid
        );

        return $this->wpdb->query($query);
    }

    /**
     * Get translated checkout page ID
     */
    public function getTransCheckoutPageId($page_id = 0)
    {
        return $this->losml->getTransElementId($page_id, 'post_page');
    }
}
