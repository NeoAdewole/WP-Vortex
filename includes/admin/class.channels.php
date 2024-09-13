<?php
defined('ABSPATH') or die('Cant access this file directly');

class Vortex_Channels
{

  public static function ready_channels_table_into_db()
  {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'channels';

    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
      id int(11) NOT NULL Auto_INCREMENT,
      channel_name tinytext NOT NULL,
      platform tinytext NOT NULL,
      channel_username VARCHAR(20),
      channel_id VARCHAR(50),
      channel_url VARCHAR(50),
      PRIMARY KEY  (id),
      KEY channel_id (channel_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    $is_error = empty($wpdb->last_error);

    return $is_error;
  }

  /**
   * Returns registed channels
   * Accepts platform name as a filter
   * @param string $platform [filters the results to only channels on the provided platform]
   */
  public static function get_dbd_channels($platform = null)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'channels';
    if (isset($platform) && !empty($platform)) :
      $channels = $wpdb->get_results("SELECT * FROM {$table_name} WHERE platform = '{$platform}'");
    else :
      $channels = $wpdb->get_results("SELECT * FROM {$table_name}");
    endif;
    return $channels;
  }

  static function get_channel_counts()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'channels';
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    return isset($rowcount) ? $rowcount : 0;
  }

  public static function vortex_plugin_options_page()
  {
    $options = get_option('vortex_options');
?>
    <div class="wrap">
      <h1>Vortex: Content Aggregator</h1>
      <p>Modify the Vortex settings to aggregate content from various sources.</p>
      <form method="POST" action="options.php">
        <?php
        settings_fields('vortex-plugin-options');
        // settings_fields('vortex_channel_section');
        // do_settings_sections('vortex_channel_section');
        // submit_button();
        ?>

      </form>
    </div>
  <?php
  }

  public static function vortex_callback_display_channel($args)
  {
    if (!isset($args['channel']) || empty($args['channel'])) {
      echo '<p>' . esc_html__('Create a new Vortex channel', 'vortex') . '</p>';
    } else {
      $channel = $args['channel'];
      echo '<p>' . esc_html__("Configure " . $channel->channel_name ?? null . " channel settings", 'vortex') . '</p>';
    }
  }

  public static function vortex_register_channel_settings()
  {
    // Register a content channel
    add_settings_section(
      'vortex_channel_section',
      esc_html__('New Channel Settings', 'vortex'),
      array('Vortex_Channels', 'vortex_callback_display_channel'),
      'vortex-plugin-settings'
    );

    add_settings_field(
      'channel_name',
      esc_html__('Channel Name', 'vortex'),
      array('Vortex_Channels', 'vortex_channels_callback_channel_name'),
      'vortex-plugin-settings',
      'vortex_channel_section',
      ['id' => esc_html__('channel_name', 'vortex'), 'label' => esc_html__('Channel Name', 'vortex'), 'channel' => ($channel ?? null)]
    );

    add_settings_field(
      'channel_platform',
      esc_html__('Select Channel Platform', 'vortex'),
      array('Vortex_Channels', 'vortex_channels_callback_select'),
      'vortex-plugin-settings',
      'vortex_channel_section',
      ['id' => esc_html__('channel_platform', 'vortex'), 'label' => esc_html__('Channel platform', 'vortex')]
    );

    add_settings_field(
      'Username',
      esc_html__('Channel Username', 'vortex'),
      array('Vortex_Channels', 'vortex_channels_callback_username'),
      'vortex-plugin-settings',
      'vortex_channel_section',
      ['id' => esc_html__('channel_username', 'vortex'), 'label' => esc_html__('Username', 'vortex')]
    );

    add_settings_field(
      'channel_id',
      esc_html__('Channel ID', 'vortex'),
      array('Vortex_Channels', 'vortex_channels_callback_channel_id'),
      'vortex-plugin-settings',
      'vortex_channel_section',
      ['id' => esc_html__('channel_id', 'vortex'), 'label' => esc_html__('Channel ID', 'vortex')]
    );

    add_settings_field(
      'channel_url',
      esc_html__('Channel URL', 'vortex'),
      array('Vortex_Channels', 'vortex_channels_callback_url'),
      'vortex-plugin-settings',
      'vortex_channel_section',
      ['id' => esc_html__('channel_url', 'vortex'), 'label' => esc_html__('Channel URL', 'vortex')]
    );
  }

  /** 
   * Channel Field Callbacks 
   */

  // callback: channel default options
  public static function vortex_channel_defaults()
  {
    return array(
      'id'   => esc_html__('new channel id', 'vortex'),
      'channel_name'   => esc_html__('new channel', 'vortex'),
      'channel_platform'   => esc_html__('youtube', 'vortex'),
      'channel_username'   => esc_html__('@Username', 'vortex'),
      'channel_id'   => esc_html__('Channel ID', 'vortex'),
      'channel_url'   => esc_html__('Channel URL', 'vortex')
    );
  }

  // callback: channel section
  public static function vortex_callback_add_channel()
  {
    echo '<p>' . esc_html__('Add the details for this channel, load each channel in settings', 'vortex') . '</p>';
  }

  // callback: text field
  public static function dbd_callback_field_text($args)
  {
    // $options = get_option('Vortex_Settings', vortex_options_default());

    $id    = isset($args['id'])    ? $args['id']    : '';
    $label = isset($args['label']) ? $args['label'] : '';

    $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';

    echo '<input id="dbd_options_' . $id . '" name="dbd_options[' . $id . ']" type="text" size="40" value="' . $value . '"><br />';
    echo '<label for="dbd_options_' . $id . '">' . $label . '</label>';
  }

  // callback: new Channel name
  static function vortex_channels_callback_channel_name($args)
  {
    echo '<br>';
    $label = isset($args['label']) ? $args['label'] : '';
    echo '<input id="channel_name" name="channel_name" type="text" size="40" placeholder="' . $label . '" value=""><br />';
  }

  // callback: new Channel ID
  static function vortex_channels_callback_channel_id($args)
  {
    $id = isset($args['id']) ? $args['id'] : '';
    $label = isset($args['label']) ? $args['label'] : '';
    echo '<input id="' . $id . '" name="' . $id . '" type="text" size="40" placeholder="' . $label . '" value=""><br />';
  }

  // callback: new Channel Username
  static function vortex_channels_callback_username($args)
  {
    $id = isset($args['id']) ? $args['id'] : '';
    $label = isset($args['label']) ? $args['label'] : '';
    echo '<input id="' . $id . '" name="channel_username" type="text" size="40" placeholder="' . $label . '" value=""><br />';
  }

  // callback: new Channel URL
  static function vortex_channels_callback_url($args)
  {
    $id = isset($args['id']) ? $args['id'] : '';
    $label = isset($args['label']) ? $args['label'] : '';
    echo '<input id="' . $id . '" name="' . $id . '" type="text" size="40" placeholder="' . $label . '" value=""><br />';
  }

  // callback: new channel platform
  public static function vortex_channels_callback_select($args)
  {
    $options = self::vortex_channel_defaults();
    $id      = isset($args['id']) ? $args['id'] : '';
    $label   = isset($args['label']) ? $args['label'] : '';
    $selected_option = isset($options['platform']) ? sanitize_text_field($options['platform']) : '';
    $select_options = vortex_Settings::vortex_callback_select_options('platform');
    echo '<select id="' . $id . '" name="' . $id . '">';
    foreach ($select_options as $value => $option) {
      $selected = selected($selected_option === $value, true, false);
      echo '<option value="' . $value . '"' . $selected . '>' . $option . '</option>';
    }
  }


  public static function vortex_channel_render_input_field($args)
  {
    $id = $args['id'];
    $label = $args['label'];
    $type = $args['type'];
    $size = $args['size'];
    $value = $args['value'];
    $platform_options = Vortex_Settings::vortex_callback_select_options('platform');

    switch ($type) {
      case 'hidden':
        echo '<input type="' . $type . '" id="' . $id . '" name="' . $id . '" size="' . $size . '" placeholder="' . $label . '" value="' . $value . '">';
        break;
      case 'text':
        echo '<input id="' . $id . '" name="' . $id . '" type="' . $type . '" size="' . $size . '" placeholder="' . $label . '" value="' . $value . '"><br />';
        break;
      case 'select':
        $selected_option = $value;
        echo '<select id="' . $id . '" name="' . $id . '">';
        foreach ($platform_options as $value => $option) {
          $selected = selected($selected_option === $value, true, false);
          echo '<option value="' . $value . '"' . $selected . '>' . $option . '</option>';
        }
        echo '</select><br />';
        break;
      default:
        echo '<input disabled id="' . $id . '" name="' . $id . '" type="' . $type . '" size="' . $size . '" placeholder="' . $label . '" value="' . $value . '"><br />';
    }
  }

  static function vortex_add_channel_field($field_id, $title, $type, $size = 50, $value = '')
  {
    $page = 'vortex_plugin_settings';
    $section = 'vortex_channel_tab';
    // Add hidden class to <tr> if field $type is hidden
    $class = ($type == 'hidden') ? 'hidden' : '';
    // Exclude label for attribute for field types here
    $remove_for = array('hidden');
    $for = (in_array($type, $remove_for)) ? '' : esc_html__($field_id, 'vortex');
    $args = array(
      'id' => esc_html__($field_id, 'vortex'),
      'label' => esc_html__($title, 'vortex'),
      'label_for' => $for,
      'type' => esc_html__($type, 'vortex'),
      'size' => $size,
      'value' => $value,
      'class' => $class
    );
    // check type and dont render table row or <th> if hidden
    add_settings_field(
      $field_id,
      esc_html__($title, 'vortex'),
      array('Vortex_Channels', 'vortex_channel_render_input_field'),
      $page,
      $section,
      $args
    );
  }


  /* Channet view templates */
  // New channel form
  public static function new_channel_form()
  {
    if (isset($_POST['add_channel'])) {
      // Vortex_Channels::add_new_vortex_channel();
    }
  ?>
    <form action="" role="presentation" method="post" class="channel_add">
      <?php
      // output setting sections
      do_settings_sections('vortex-plugin-settings');

      // submit button
      submit_button('Add Channel', 'primary', 'add_channel');
      ?>
      <hr>
    </form>
  <?php
    return;
  }

  // Channel Details Form
  public static function edit_channel_form($channel)
  {
    // update channel details
    if (isset($_POST['edit_vortex_channel'])) {
      Vortex_Channels::edit_vortex_channel();
    }
  ?>
    <form action="" role="presentation" method="post" class="edit_channel">
      <?php
      settings_fields('dbd_channels');
      echo '<table class="form-table">';

      $inputs = Vortex_Settings::vortex_channel_fields();
      foreach ($channel as $key => $field_value) {
        $field_id = $inputs[$key][0];
        $title = $inputs[$key][1];
        $type = $inputs[$key][2];
        $size = $inputs[$key][3];
        $value = $field_value;
        Vortex_Channels::vortex_add_channel_field($field_id, $title, $type, $size, $value);
      }

      // output edit channel setting sections
      do_settings_fields('dbd_channels', 'dbd_channel_tab');
      echo '</table>';

      // submit button
      submit_button('Edit Channel', 'secondary', 'edit_dbd_channel');
      // write_log("Update form loaded for dbd_channel \n");
      ?>
      <hr>
    </form>
<?php
  }

  /* Vortex Channel Validation MODIFY FROM HERE DOWN vvv */
  // Add new channel
  public static function add_new_vortex_channel()
  {
    if (isset($_POST['add_channel'])) {
      Vortex_Settings::vortex_callback_validate_channel($_POST['add_channel']);

      if (!empty($_POST['errors'])) {
        print_r($_POST['errors']);
        wp_die('Channel fields not valid');
      } else {
        global $wpdb;
        $table_name = $wpdb->prefix . 'channels';

        $data_ = array(
          'channel_name' => $_POST['channel_name'],
          'platform' => $_POST['channel_platform'],
          'channel_username' => $_POST['channel_username'],
          'channel_id' => $_POST['channel_id'],
          'channel_url'  => $_POST['channel_url'],
        );

        $result = $wpdb->insert($table_name, $data_, array('%s', '%s', '%s', '%s', '%s'));

        if (false === $result) {
          print_r($wpdb->last_error);
          // wp_die('Failed to add channel');
        } else {
          echo "Channel added";
        }
        // return print_r($_POST['errors']);
      }
    }
  }

  // update channel
  public static function edit_vortex_channel()
  {
    if (isset($_POST['edit_vortex_channel'])) {
      Vortex_Settings::vortex_callback_validate_channel($_POST['edit_vortex_channel']);

      if (!empty($_POST['errors'])) {
        wp_die('Channel fields not valid');
      } else {
        global $wpdb;
        $table_name = $wpdb->prefix . 'channels';
        // Get corresponding id row from table| check against new data
        $id = $_POST['id'];
        $data_ = array(
          'channel_name' => $_POST['channel_name'],
          'platform' => $_POST['platform'],
          'channel_username' => $_POST['channel_username'],
          'channel_id' => $_POST['channel_id'],
          'channel_url'  => $_POST['channel_url'],
        );

        $where = array('id' => $id);
        $updated = $wpdb->update($table_name, $data_, $where, array('%s', '%s', '%s', '%s', '%s'), array('%d'));
        if (false === $updated) {
          wp_die('Failed to add channel');
        } else {
          echo "Channel updated";
          // reload form
        }
      }
    }
  }
}
