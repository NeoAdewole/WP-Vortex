<?php

function vortex_plugin_settings_page()
{
?>
  <div class="wrap">
    <form method="POST" action="options.php">
      <?php
      settings_fields('vortex_settings_group');
      do_settings_sections('vortex-settings-page');
      submit_button();
      ?>

    </form>
  </div>
<?php
}
