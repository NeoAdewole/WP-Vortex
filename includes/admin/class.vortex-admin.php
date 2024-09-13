<?php
defined('ABSPATH') or die('Cant access this file directly');

class Vortex_Admin
{
  private static $initiated = false;

  public static function init()
  {
    if (!self::$initiated) {
      self::init_hooks();
    }
  }

  public static function init_hooks()
  {
    self::$initiated = true;
    add_action('admin_init', array('Vortex_Channels', 'vortex_register_channel_settings'));
  }

  public static function admin_menu()
  {
    Vortex_Menu::vortex_admin_menus();
  }

  public static function display_configuration_page()
  {
    $api_key      = Vortex_Admin::API_KEY;
    $akismet_user = 'Beta User';
    echo '<h3>DisByDem Admin: Display configuration page</h3>';
    Vortex_Admin::view('settings', compact('api_key', 'akismet_user'));
  }

  public static function display_channel_settings()
  {
    $vortex_user = 'Admin User';
    echo '<p>' . esc_html__('WE are getting the display channel callback here, load each channel in settings', 'vortex') . '</p>';
    Vortex_Admin::view('channels', compact('vortex_user'));
  }

  public static function view($name, array $args = array())
  {
    $args = apply_filters('vortex_view_arguments', $args, $name);
    foreach ($args as $key => $val) {
      $$key = $val;
    }
    load_plugin_textdomain('vortex');
    $file = VORTEXWP__PLUGIN_DIR . 'includes/admin/views/' . $name . '.php';

    include($file);
  }

  public static function display_page()
  {
    echo '<h3>Determined by display_page() in Vortex Admin</h3>';
    // ToDo: rewrite url for vortex admin settings page
    if ((isset($_GET['view']) && $_GET['view'] == 'dbd_admin_menu')) :
      self::display_configuration_page();
    elseif ((isset($_GET['view']) && $_GET['view'] == 'dbd_channels')) :
      self::display_channel_settings();
    else :
      self::display_configuration_page();
    endif;

    Vortex_Admin::view('channels', compact('vortex_user'));
  }
}
