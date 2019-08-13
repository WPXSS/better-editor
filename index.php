<?php  
/* 
Plugin Name: Better Editor
Description: Inline post editor inspired by Medium.com editor
Version: 1.0.0
Author: Stefan Pejcic
Author URI: https://giga.rs
License: GNU/GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: medium-editor
Domain Path: /languages
*/
/* No script kiddies */
defined("ABSPATH") or die("Access Denied.");
/* Initialize */
function GigamediumeditorInit()
{
    if (current_user_can("edit_pages") && current_user_can("edit_posts"))
    {
        wp_enqueue_style("medium-editor", plugins_url("/css/medium-editor.min.css", __FILE__));
        wp_enqueue_style("medium-editor-theme", plugins_url("/css/themes/beagle.min.css", __FILE__));
        if (!is_admin())
        {
            wp_enqueue_script("jquery");
        }
        wp_enqueue_script("medium-editor", plugins_url("/js/medium-editor.min.js", __FILE__));
        wp_register_script("gigamediumeditor", plugins_url("/js/gigamediumeditor.js", __FILE__));
        $translation_array = array(
            "ajax_url" => admin_url("admin-ajax.php")
        );
        wp_localize_script("gigamediumeditor", "wp_context", $translation_array);
        wp_enqueue_script("gigamediumeditor");
    }
}
/* Title Filter */
function GigamediumeditorTitleFilter($post)
{
    // Find and mark title
    return '<div id="gigamediumeditor-post-' . $post->ID . '-title">' . $post->post_title . '</div>';
}
/* Content Filter */
function GigamediumeditorContentFilter($post)
{
    global $shortcode_tags;
    $matches = [];
    // Find and mark content
    $content = '<div id="gigamediumeditor-post-' . $post->ID . '-content">' . $post->post_content . '</div>';
    // Wrap all shortcodes for protection
    $pattern = get_shortcode_regex();
    preg_match_all('/' . $pattern . '/s', $content, $matches);
    foreach ($matches[0] as $instruction)
    {
        $instruction_safe = str_replace('"', "'", str_replace("[", "%5B", str_replace("]", "%5D", str_replace("%", "%25", $instruction))));
        $wrapped = '<div class="gigamediumeditor-shortcode gigamediumeditor-strip" contenteditable="false" data-gigamediumeditor-shortcode="' . $instruction_safe . '">' . $instruction . '</div>';
        $content = str_replace($instruction, $wrapped, $content);
    }
    // Add editing interface
    $theme = '<div class="gigamediumeditor-remove" contenteditable="false" style="user-select: none; -webkit-user-select: none;"><button id="button-edit-post-' . $post->ID . '" onclick="gigamediumeditorEdit(' . $post->ID . ')">Edit</button> <button id="button-save-post-' . $post->ID . '" style="display: none;" disabled onclick="gigamediumeditorSave(' . $post->ID . ')">Save</button> <button id="button-cancel-post-' . $post->ID . '" style="display: none;" onclick="gigamediumeditorCancel(' . $post->ID . ')">Cancel</button></div>';
    return $content . $theme;
}
/* Preparing html of Posts */
function GigamediumeditorPrepare()
{
    if (!is_admin() && current_user_can("edit_pages") && current_user_can("edit_posts"))
    {
        global $wp_query;
        $posts = $wp_query->posts;
        foreach ($posts as $post)
        {
            $post->post_title = GigamediumeditorTitleFilter($post);
            $post->post_content = GigamediumeditorContentFilter($post);
        }
    }
}
/* Update Post */
function GigamediumeditorUpdatePost()
{
    if (current_user_can("edit_pages") && current_user_can("edit_posts"))
    {
        $post_id = sanitize_text_field($_POST["post_ID"]);
        $post_title = sanitize_text_field($_POST["post_title"]);
        $post_content = sanitize_textarea_field($_POST["post_content"]);
        $post = get_post($post_id);
        $post->post_title = $post_title;
        $post->post_content = $post_content;
        wp_update_post($post);
    }
    wp_die();
}
/* Hooks */
add_action("init", "GigamediumeditorInit");
add_action("wp_ajax_update", "GigamediumeditorUpdatePost");
add_action("wp", "GigamediumeditorPrepare");
?>
