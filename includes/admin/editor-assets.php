<?php

function vortex_enqueue_block_editor_assets()
{
  $current_screen = get_current_screen();
  if ($current_screen->base == 'appearance_page_gutenberg-edit-site') {
    return;
  }

  wp_enqueue_script('vortex_editor');
  wp_enqueue_style('vortex_editor');
}
