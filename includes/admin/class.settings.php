<?php
class vortex_Settings
{
  public static function vortex_register_settings()
  {
    register_setting(
      'vortex_options',
      'vortex_options',
      'vortex_callback_validate_options'
    );

    // register channels section?
  }


  public static function vortex_callback_select_options($option)
  {
    $status = array(
      'not_started' => esc_html__('Not Started', 'vortex'),
      'in_progress' => esc_html__('In Progress', 'vortex'),
      'paused' => esc_html__('Paused', 'vortex'),
      'complete' => esc_html__('Complete', 'vortex'),
    );

    $platform = array(
      'rss' => esc_html__('RSS', 'vortex'),
      'tiktok' => esc_html__('Tiktok', 'vortex'),
      'twitch' => esc_html__('Twitch', 'vortex'),
      'patreon' => esc_html__('Patreon', 'vortex'),
      'youtube' => esc_html__('Youtube', 'vortex'),
      'twitter' => esc_html__('Twitter', 'vortex'),
      'instagram' => esc_html__('Instagram', 'vortex'),
      'facebook' => esc_html__('Facebook', 'vortex'),
    );

    if ($option == 'platform') {
      return $platform;
    } else {
      return $status;
    }
  }

  public static function vortex_channel_fields()
  {
    /* 
      return name, label, type, size and default value for each channel setting input field
    */
    return array(
      'id' => array('id', null, 'hidden', 11, ''),
      'channel_name' => array('channel_name', 'Channel Name', 'text', 40, ''),
      'platform' => array('platform', 'Channel Platform', 'select', 20, ''),
      'channel_username' => array('channel_username', 'Channel Username', 'text', 20, ''),
      'channel_id' => array('channel_id', 'Channel ID', 'text', 50, ''),
      'channel_url' => array('channel_url', 'Channel URL', 'text', 50, '')
    );
  }

  // Validate channel inputs
  public static function vortex_callback_validate_channel($input)
  {
    $error = array();
    $warning = array();

    $platfom_options = Vortex_Settings::vortex_callback_select_options('platform');

    // channel name
    if (isset($input['channel_name'])) {
      $input['channel_name'] = sanitize_text_field($input['channel_name']);
    }
    // channel platform supported
    if (isset($input['channel_platform']) && !array_key_exists(sanitize_text_field($input['channel_platform']), $platfom_options)) {
      $error['channel_platform'] = 'Channel platform not supported';
      $input['channel_platform'] = null;
    }
    // channel username
    if (isset($input['channel_username'])) {
      $warning['channel_username'] = 'Channel username not set';
      $input['channel_username'] = sanitize_text_field($input['channel_username']);
    }
    // channel ID
    // ToDo: Check if channel id exists
    if (isset($input['channel_id'])) {
      $input['channel_id'] = sanitize_text_field($input['channel_id']);
    }
    // channel url
    if (isset($input['channel_url'])) {
      $input['channel_url'] = sanitize_url($input['channel_url']);
    }

    if (count($error) > 0) {
      $input['errors'] = $error;
    }

    if (count($warning) > 0) {
      $input['warnings'] = $warning;
    }

    return $input;
  }
}
