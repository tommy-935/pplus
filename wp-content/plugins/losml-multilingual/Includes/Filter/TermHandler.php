<?php
namespace Losml\Includes\Filter;

/**
 * Term-related operations for multilingual support.
 */
class TermHandler {
    public $losml;
    public $wpdb;
    public $has_term_data = [];

    public function __construct($losml) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->losml = $losml;
        $this->addHooks();
    }

    public function addHooks(){
        add_filter( 'terms_clauses', array( $this, 'filterTermClauses' ), 10, 3 );
    }

    /**
     * Filter SQL clauses for term queries.
     */
    public function filterTermClauses($clauses, $taxonomies, $args) {
        if (empty($taxonomies)) {
            return $clauses;
        }

        // Skip filtering for these taxonomies
        $excluded_taxonomies = ['product_visibility', 'product_shipping_class', 'ufaq-category'];
        foreach ($excluded_taxonomies as $excluded) {
            if (in_array($excluded, $taxonomies)) {
                return $clauses;
            }
        }

        // Skip if called from specific functions
        if (
            $this->losml->isFunctionInCallStack('wp_get_object_terms') ||
            $this->losml->isFunctionInCallStack('_get_term_hierarchy')
        ) {
            return $clauses;
        }

        $taxonomy = $taxonomies[0] ?? 'product_cat';
        $clauses['join']  .= ' LEFT JOIN ' . $this->wpdb->prefix . 'losml_translations AS mt_losml ON mt_losml.element_id = tt.term_id AND mt_losml.element_type = "tax_' . esc_sql($taxonomy) . '"';
        $clauses['where'] .= ' AND mt_losml.language_id = ' . intval(LOSML_MULTI_LANGUAGE_ID);

        return $clauses;
    }

    /**
     * Retrieve term translation (placeholder).
     */
    public function getTermTranslation($term, $taxonomy) {
        return $term;
    }

    /**
     * Mark the translated parent as selected in the dropdown.
     */
    public function setSelectedParent($output, $args) {
        if (isset($_GET['source_element_id']) && $args['name'] === 'parent') {
            $term_id = intval($_GET['source_element_id']);
            $translation = $this->getParentTranslation($term_id);
            if (!empty($translation[0]->element_id)) {
                $parent_id = intval($translation[0]->element_id);
                $output = str_replace('value="' . $parent_id . '"', 'value="' . $parent_id . '" selected="selected"', $output);
            }
        }
        return $output;
    }

    /**
     * Ensure the parent term is translated before creating a new translation.
     */
    public function ensureParentTranslated($term, $taxonomy) {
        if (
            $taxonomy === 'product_cat' &&
            $_POST['action'] === 'add-tag' &&
            isset($_POST['source_element_id'])
        ) {
            $term_id = intval($_POST['source_element_id']);
            $translation = $this->getParentTranslation($term_id);

            if (empty($translation) || !$translation[0]->id) {
                return new WP_Error('invalid_trans', __('Translate the parent term first.', 'losml'));
            }
        }
        return $term;
    }

    /**
     * Retrieve parent term translation data.
     */
    public function getParentTranslation($term_id) {
        $prefix = $this->wpdb->prefix;
        $sql = "
            SELECT c.id, a.term_id, c.element_id
            FROM {$prefix}terms AS a
            LEFT JOIN {$prefix}term_taxonomy AS b ON a.term_id = b.parent
            LEFT JOIN {$prefix}losml_strings AS c ON c.source_element_id = a.term_id
                AND c.element_type = 'tax_product_cat'
                AND c.language_id = " . intval(LOSML_MULTI_LANGUAGE_ID) . "
            WHERE b.term_id = " . intval($term_id);

        return $this->wpdb->get_results($sql);
    }

    /**
     * Called after a term is created.
     */
    public function onTermCreated($term_id, $tt_id, $taxonomy) {
        $source_element_id = intval($_POST['source_element_id'] ?? 0);
        $this->linkTermTranslation($term_id, $source_element_id, $taxonomy);
    }

    /**
     * Link a term with its translation.
     */
    public function linkTermTranslation($term_id, $source_element_id = 0, $taxonomy = '') {
        $source_element_id = $source_element_id ?: $term_id;
        $element_type = 'tax_' . esc_sql($taxonomy);

        $sql = "
            INSERT INTO {$this->wpdb->prefix}losml_strings (element_type, element_id, source_element_id, language_id)
            VALUES ('{$element_type}', " . intval($term_id) . ", " . intval($source_element_id) . ", " . intval(LOSML_MULTI_LANGUAGE_ID) . ")";

        return $this->wpdb->query($sql);
    }

    /**
     * Hook for editing term form.
     */
    public function editTermForm($term) {
        if (in_array($term, ['videos_cat'])) {
            return;
        }
        $this->renderMetaBox($term);
    }

    /**
     * Render multilingual meta box.
     */
    public function renderMetaBox($term) {
        $source_element_id = intval($_GET['source_element_id'] ?? 0);

        if ($term === 'product_cat' && $source_element_id) {
            $translations = $this->losml->obj_post->getHasTrans($source_element_id, $term);
        } else {
            $translations = $this->losml->obj_post->getHasTrans($term->term_id, $term->taxonomy);
        }

        echo '<div class="losml-term-switch-box">';
        echo '<input type="hidden" name="source_element_id" value="' . esc_attr($source_element_id) . '">';

        foreach ($this->losml->active_language_data as $lang) {
            echo '<div class="losml-edit-item"><div class="lang-name">' . esc_html($lang->name) . '</div><div>';

            if ($lang->id == LOSML_MULTI_LANGUAGE_ID) {
                echo '<span>' . __('Current language', 'losml') . '</span>';
            } elseif (isset($translations[$lang->id])) {
                $url = $this->getTranslationUrl($translations[$lang->id]->element_id, $lang->id, $term);
                echo '<a href="' . esc_url($url) . '&losml=' . esc_attr($lang->code) . '">' . __('Edit', 'losml') . '</a>';
            } elseif (
                isset($translations[LOSML_MULTI_LANGUAGE_ID]) &&
                $translations[LOSML_MULTI_LANGUAGE_ID]->source_element_id == $term->term_id
            ) {
                $url = $this->getTranslationUrl(0, $lang->id, $term);
                echo '<a href="' . esc_url($url) . '&losml=' . esc_attr($lang->code) . '" data-id="' . esc_attr($term->term_id) . '" data-langid="' . esc_attr($lang->id) . '">' . __('Add', 'losml') . '</a>';
            }

            echo '</div></div>';
        }

        echo '</div>';
    }

    /**
     * Generate translation edit/add URL.
     */
    public function getTranslationUrl($id = 0, $lang_id = 0, $term = null) {
        if (!$id) {
            global $pagenow;

            if ($pagenow === 'nav-menus.php') {
                $url = admin_url('nav-menus.php?action=edit&menu=0');
            } else {
                $taxonomy = $_GET['taxonomy'] ?? 'product_cat';
                $url = admin_url("edit-tags.php?taxonomy={$taxonomy}&post_type=product");
            }

            if (!empty($term->term_id)) {
                $url .= '&source_element_id=' . intval($term->term_id);
            }
        } else {
            $taxonomy = $term->taxonomy ?? 'product_cat';

            if ($taxonomy === 'nav_menu') {
                $url = admin_url("nav-menus.php?action=edit&menu={$id}");
            } else {
                $url = get_edit_tag_link($id, $taxonomy);
            }
        }

        return $this->losml->obj_url->transUrl($url, $lang_id);
    }
}
