<?php

function vortex_enqueue()
{
  wp_register_style(
    'vortex_admin_styles',
    get_theme_file_uri('/assets/css/admin_styles.css'),
  );
}

function vortex_enqueue_scripts()
{
  $authURLs = json_encode([
    'signup' => esc_url_raw(rest_url('vortex/v1/signup')),
    'signin' => esc_url_raw(rest_url('vortex/v1/signin'))
  ]);
  wp_add_inline_script(
    'vortex-auth-modal-view-script',
    "const vortex_auth_rest = {$authURLs}",
    'before'
  );

  wp_enqueue_style(
    'vortex_editor'
  );
}
