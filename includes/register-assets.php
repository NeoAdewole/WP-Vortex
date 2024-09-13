<?php

function vortex_register_assets()
{
  wp_register_style(
    'vortex_admin',
    plugins_url('/build/admin/index.css', VORTEXWP__PLUGIN_FILE)
  );

  $adminAssets = include(CLEARBLOCKS__PLUGIN_DIR . 'build/admin/index.asset.php');

  wp_register_script(
    'vortex_admin',
    plugins_url('/build/admin/index.js', VORTEXWP__PLUGIN_FILE),
    $adminAssets['dependencies'],
    $adminAssets['version'],
    true
  );

  // $editorAssets = include(VORTEXWP__PLUGIN_DIR . 'build/block-editor/index.asset.php');

  /* wp_register_script(
    'vortex_editor',
    plugins_url('/build/block-editor/index.js', VORTEXWP__PLUGIN_FILE),
    $editorAssets['dependencies'],
    $editorAssets['version'],
    true
  ); */

  /* wp_register_style(
    'vortex_editor',
    plugins_url('/build/block-editor/index.css', VORTEXWP__PLUGIN_FILE)
  ); */
}
