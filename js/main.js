/*
 * Post Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to custom metadata
 */

jQuery(document).ready(function($) {
  // Populate meta data into quick-edit form (new added)
  var $inline_editor = inlineEditPost.edit;
  inlineEditPost.edit = function(id) {
    //call old copy
    $inline_editor.apply(this, arguments);

    //our custom functionality fields below
    var post_id = 0;
    if (typeof id == "object") {
      post_id = parseInt(this.getId(id));
    }

    //if we have post
    if (post_id != 0) {
      //find our row
      $row = $("#edit-" + post_id);
      $seo_title = $("#post-" + post_id + " .column-wpseo-title");
      seo_title_value = $seo_title.text();
      $row.find("#post_wpsqe_title").val(seo_title_value);

      $seo_desc = $("#post-" + post_id + " .column-wpseo-metadesc");
      seo_desc_value = $seo_desc.text();
      $row.find("#post_wpsqe_desc").val(seo_desc_value);

      $seo_fk = $("#post-" + post_id + " .column-wpseo-focuskw");
      seo_fk_value = $seo_fk.text();
      $row.find("#post_wpsqe_fk").val(seo_fk_value);
    }
  };
});
