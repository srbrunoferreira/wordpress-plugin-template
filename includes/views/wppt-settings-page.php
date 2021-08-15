<div class="wrap">
    <h1>Your Plugin Name</h1>

    <form method="post" action="options.php">
        <?php settings_fields('wppt-setting-group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">WPPT Setting 1</th>
                <td><input type="text" name="wppt-setting1" placeholder="wppt-setting1" value="<?php echo esc_attr(get_option('wppt-setting1')); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">WPPT Setting 2</th>
                <td><input type="text" name="wppt-setting2" placeholder="wppt-setting2" value="<?php echo esc_attr(get_option('wppt-setting2')); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">WPPT Setting 3</th>
                <td><input type="text" name="wppt-setting3" placeholder="wppt-setting3" value="<?php echo esc_attr(get_option('wppt-setting3')); ?>" /></td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>
