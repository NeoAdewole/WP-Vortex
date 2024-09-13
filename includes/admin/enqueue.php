<?php

function vortex_admin_enqueue($hook_suffix)
{
  if (
    $hook_suffix === 'toplevel_page_vortex-plugin-options'
  ) {
    wp_enqueue_media();
    wp_enqueue_style('vortex_admin');
    wp_enqueue_script('vortex_admin');
  }
}
