<?php
/*
Plugin Name: YoastSEO Quick Edit
Plugin URI: https://github.com/programfault/YoastSEO-Quick-Edit-Plugin
Description: Add Yoast SEO fields to quick edit.
Version: 1.0.0
Author: roc
Author URI: https://programfault.com/yoast-seo-quick-edit-plugin-dev/
License: A "Slug" license name e.g. GPL2
 */

class WPSEO_Quick_Edit
{

    private static $instance = null;

    public function __construct()
    {

        //Output elements to quick edit interface.
        add_action('quick_edit_custom_box', array($this, 'display_quick_edit_custom'), 10, 2);

        //Enqueue resources (javascript and css).
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_and_styles'));

        //Save action when click update button.
        add_action('save_post', array($this, 'save_post'), 10, 1);
    }

    //Enqueue javascript used to pre-populate required fields to quick-edit fields
    public function enqueue_admin_scripts_and_styles()
    {

        //Load javascript
        wp_enqueue_script('quick-edit-script', plugin_dir_url(__FILE__) . '/js/main.js', array('jquery', 'inline-edit-post'));
        wp_enqueue_style('quick-edit-script', plugin_dir_url(__FILE__) . '/css/style.css');
    }

    //Display our custom content on the quick-edit interface.
    public function display_quick_edit_custom($column, $post_id)
    {
        $html = '';
        wp_nonce_field('post_metadata', 'post_metadata_field');

        //Checking columns from Yoast SEO columns
        //wpseo-title,wpseo-metadesc,wpseo-focuskw are css class name from Yoast SEO
        if ($column == 'wpseo-title') {

            //Inherit WordPress fieldset style but add new one for ourself named (wpsqe-box) to make width longer.
            $html .= '<fieldset class="inline-edit-col-left wpsqe-box">';
            $html .= '<legend class="inline-edit-legend">Yoast SEO Quick Edit</legend>';
                $html .= '<div class="inline-edit-col">';
                    $html .= '<label>';
                        $html .= '<span class="title">SEO Title</span>';
                        $html .= '<span class="input-text-wrap">';
                            $html .= '<input type="text" name="post_wpsqe_title" id="post_wpsqe_title" class="ptitle" value="">';
                        $html .= '</span>';
                    $html .= '</label>';
                $html .= '</div>';
        } elseif ($column == 'wpseo-metadesc') {
                $html .= '<div class="inline-edit-col">';
                    $html .= '<label>';
                        $html .= '<span class="title">SEO Desc</span>';
                        $html .= '<span class="input-text-wrap">';
                            $html .='<textarea data-wp-taxonomy="post_tag" cols="22" rows="1" id="post_wpsqe_desc" name="post_wpsqe_desc" class="tax_input_post_tag ui-autocomplete-input" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false"></textarea>';
                        $html .= '</span>';
                    $html .= '</label>';
                $html .= '</div>';
        } elseif ($column == 'wpseo-focuskw') {
                $html .= '<div class="inline-edit-col">';
                    $html .= '<label>';
                        $html .= '<span class="title">SEO Focus</span>';
                        $html .= '<span class="input-text-wrap">';
                            $html .= '<input type="text" name="post_wpsqe_fk" id="post_wpsqe_fk" class="ptitle" value="">';
                    $html .= '</span>';
                    $html .= '</label>';
                $html .= '</div>';
            $html .= '</fieldset>';
        }
        echo $html;
    }

    //Saving meta values
    public function save_post($post_id)
    {

        //Initial global post used to YoastSEO plugin filter function. 
        global $post;
        $post = get_post($post_id);

        //check nonce set
        if (!isset($_POST['post_metadata_field'])) {
            return false;
        }

        //verify nonce
        if (!wp_verify_nonce($_POST['post_metadata_field'], 'post_metadata')) {
            return false;
        }

        //if not auto saving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        //all good to save
        $wpsqe_title = isset($_POST['post_wpsqe_title']) ? sanitize_text_field($_POST['post_wpsqe_title']) : '';
        $wpsqe_desc = isset($_POST['post_wpsqe_desc']) ? sanitize_text_field($_POST['post_wpsqe_desc']) : '';
        $wpsqe_fk = isset($_POST['post_wpsqe_fk']) ? sanitize_text_field($_POST['post_wpsqe_fk']) : '';

        //WPwpsqeMeta is the instance from Yoast SEO Plugin, set_value function is used to update meta data.Now we only edit 3 fields.
        WPSEO_Meta::set_value('focuskw_text_input', $wpsqe_fk, $post_id);
        WPSEO_Meta::set_value('focuskw', $wpsqe_fk, $post_id);
        WPSEO_Meta::set_value('title', $wpsqe_title, $post_id);
        WPSEO_Meta::set_value('metadesc', $wpsqe_desc, $post_id);

    }

    //gets singleton instance
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
$WPSEO_Quick_Edit = WPSEO_Quick_Edit::getInstance();
