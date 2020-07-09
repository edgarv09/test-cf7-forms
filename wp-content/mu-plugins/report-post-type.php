<?php

function university_post_types() {
  register_post_type('report', array(
    'public' => true,
    'labels' => array(
      'name' => 'Reports',
      'add_new_item' => 'Add New Report',
      'edit_item' => 'Edit Report',
      'all_items' => 'All Reports',
      'singular_name' => 'Report'
    ),
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-feedback'
  ));
}

add_action('init', 'university_post_types');
