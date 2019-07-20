/*
   Medium Editor https://yabwe.github.io/medium-editor/
*/

var edit_title = null;
var edit_content = null;
var orig_title = "";
var orig_content = "";
var selector_title = "#gigamediumeditor-post-%post_ID%-title";
var selector_content = "#gigamediumeditor-post-%post_ID%-content";

function gigamediumeditorEdit(post_id)
{
    orig_title = gigamediumeditorGetElement(selector_title, post_id).html();
    orig_content = gigamediumeditorGetElement(selector_content, post_id).html();
    jQuery("#button-edit-post-" + post_id).hide();
    jQuery("#button-save-post-" + post_id).html("Save");
    jQuery("#button-save-post-" + post_id).attr("disabled", "disabled");
    jQuery("#button-save-post-" + post_id).show();
    jQuery("#button-cancel-post-" + post_id).removeAttr("disabled");
    jQuery("#button-cancel-post-" + post_id).show();
    var ahref = gigamediumeditorGetElement(selector_title, post_id).closest("a");
    if (ahref)
    {
        ahref.on("click.gigamediumeditor", function(e)
        {
            e.preventDefault();
        });
    }
    jQuery.each(jQuery("#post-" + post_id + " .gigamediumeditor-shortcode"), function(index, element)
    {
        jQuery(this).attr("data-gigamediumeditor-color", jQuery(this).css("color"));
        jQuery(this).attr("data-gigamediumeditor-background-color", jQuery(this).css("background-color"));
        jQuery(this).css("color", "white").css("background-color", "lightgrey");
    });
    edit_title = new MediumEditor(gigamediumeditorGetElement(selector_title, post_id));
    edit_title.subscribe("editableInput", function(event, editable)
    {
        jQuery("#button-save-post-" + post_id).removeAttr("disabled");
    });
    edit_content = new MediumEditor(gigamediumeditorGetElement(selector_content, post_id));
    edit_content.subscribe("editableInput", function(event, editable)
    {
        jQuery("#button-save-post-" + post_id).removeAttr("disabled");
    });
    gigamediumeditorGetElement(selector_content, post_id).focus();
}

function gigamediumeditorSave(post_id)
{
    jQuery("#button-save-post-" + post_id).html("Saving...");
    jQuery("#button-save-post-" + post_id).attr("disabled", "disabled");
    jQuery("#button-cancel-post-" + post_id).attr("disabled", "disabled");
    var title = jQuery(gigamediumeditorGetElement(selector_title, post_id));
    var content = jQuery(gigamediumeditorGetElement(selector_content, post_id)).clone();
    gigamediumeditorCleanHelpers(post_id, content);
    if (edit_title) edit_title.destroy();
    if (edit_content) edit_content.destroy();
    jQuery.ajax(
    {
        type: "POST",
        url: wp_context.ajax_url,
        data: {
            action: "update",
            post_ID: post_id,
            post_title: jQuery.trim(title.html()),
            post_content: jQuery.trim(content.html())
        }
    })
    .done(function(msg)
    {
        jQuery.each(jQuery("#post-" + post_id + " .gigamediumeditor-shortcode"), function(index, element)
        {
            jQuery(this).css("color", jQuery(this).attr("data-gigamediumeditor-color")).css("background-color", jQuery(this).attr("data-gigamediumeditor-background-color"));
        });
        jQuery("#button-save-post-" + post_id).hide();
        jQuery("#button-cancel-post-" + post_id).hide();
        jQuery("#button-edit-post-" + post_id).show();
    })
    .fail(function(jqXHR, textStatus)
    {
        jQuery("#button-save-post-" + post_id).removeAttr("disabled");
        jQuery("#button-cancel-post-" + post_id).removeAttr("disabled");
    });
}

function gigamediumeditorCancel(post_id)
{
    var ahref = gigamediumeditorGetElement(selector_title, post_id).closest("a");
    if (ahref) ahref.off("click.gigamediumeditor");
    if (edit_title) edit_title.destroy();
    if (edit_content) edit_content.destroy();
    jQuery(gigamediumeditorGetElement(selector_title, post_id)).html(orig_title);
    jQuery(gigamediumeditorGetElement(selector_content, post_id)).html(orig_content);
    orig_title = "";
    orig_content = "";
    jQuery("#button-save-post-" + post_id).hide();
    jQuery("#button-cancel-post-" + post_id).hide();
    jQuery("#button-edit-post-" + post_id).show();
}

function gigamediumeditorCleanHelpers(post_id, content)
{
    var ahref = gigamediumeditorGetElement(selector_title, post_id).closest("a");
    if (ahref) ahref.off("click.gigamediumeditor");
    content.find(".gigamediumeditor-remove").remove();
    jQuery.each(content.find(".gigamediumeditor-strip"), function(index, element)
    {
        var shortcode = jQuery(this).attr("data-gigamediumeditor-shortcode");
        if (shortcode) jQuery(this).replaceWith(shortcode.replace(/%5B/g, "[").replace(/%5D/g, "]").replace(/%25/g, "%"));
    });
}

function gigamediumeditorGetElement(selector, post_id)
{
    return jQuery(selector.replace("%post_ID%", post_id));
}