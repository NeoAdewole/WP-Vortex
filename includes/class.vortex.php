<?php

class Vortex
{
  // initialize class constants

  // initialize class variables
  private static $initiated = false;

  public static function init()
  {
    if (!self::$initiated) {
      self::init_hooks();
    }
  }

  /**
   * Initializes WordPress hooks
   */
  private static function init_hooks()
  {
    self::$initiated = true;
  }

  /**
   * Activate Vortex plugin 
   * Checks WP Version compatibility, Create Social Post Type, Creates social rating table.
   */
  public static function vortex_plugin_activation()
  {
    if (version_compare($GLOBALS['wp_version'], CLEARBLOCKS__MINIMUM_WP_VERSION, '<')) {
      load_plugin_textdomain('vortex');

      $message = '<strong>' . esc_html__(
        sprintf(
          'Vortex WP %1$f! requires WordPress %2$f or higher.',
          VORTEXWP_VERSION,
          VORTEXWP__MINIMUM_WP_VERSION
        ),
        'vortex'
      ) . '</strong> ' . __(
        sprintf(
          "Please <a href='%s'>upgrade WordPress</a> to a current version to use this plugin.",
          "https://codex.wordpress.org/Upgrading_WordPress"
        ),
        "vortex"
      );

      vortex::vortex_bail_on_activation($message);
    } elseif (!empty($_SERVER['SCRIPT_NAME']) && false !== strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php')) {
      add_option('Activated_Vortex', true);
    }

    flush_rewrite_rules();

    require_once(ABSPATH . "/wp-admin/includes/upgrade.php");

    $options = get_option('vortex_options');
  }

  public static function vortex_bail_on_activation($message, $deactivate = true)
  {
?>
    <!doctype html>
    <html>

    <head>
      <meta charset="<?php bloginfo('charset'); ?>" />
      <style>
        * {
          text-align: center;
          margin: 0;
          padding: 0;
          font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
        }

        p {
          margin-top: 1em;
          font-size: 18px;
        }
      </style>
    </head>

    <body>
      <p><?php echo esc_html($message); ?></p>
    </body>

    </html>
<?php
    if ($deactivate) {
      $plugins = get_option('active_plugins');
      $vortex = plugin_basename(CLEARBLOCKS__PLUGIN_DIR . 'clearblocks.php');
      $update  = false;
      foreach ($plugins as $i => $plugin) {
        if ($plugin === $vortex) {
          $plugins[$i] = false;
          $update = true;
        }
      }

      if ($update) {
        update_option('active_plugins', array_filter($plugins));
      }
    }
    exit;
  }

  /**
   * Removes all connection options
   * @static
   */
  public static function vortex_plugin_deactivation()
  {
    // Remove any scheduled cron jobs.    
  }
}
