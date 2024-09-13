<?php
class Vortex_Menu
{
  public static function vortex_admin_menus()
  {
    add_menu_page(
      __('Vortex', 'vortex'),
      __('Vortex', 'vortex'),
      'manage_options',
      'vortex-plugin-options',
      array('Vortex_Channels', 'vortex_plugin_options_page'),
      'dashicons-editor-contract',
    );

    add_submenu_page(
      'vortex-plugin-options',
      __('Vortex Settings', 'vortex'),
      __('Vortex Settings', 'vortex'),
      'manage_options',
      'vortex-plugin-settings',
      array('Vortex_Admin', 'display_channel_settings')
    );
  }
}
