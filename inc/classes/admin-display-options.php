<?php

// directly access denied
defined('ABSPATH') || exit;
?>

<div class="wrap uf-reset">
    <h1 class="uni-settings-title">Uni Forum Settings</h1>
    <div class="uf-settings-parent border p-20">
        <div class="options-settings">
            <h2 class="inner-title">Options</h2>
            <form action="javascript:void(0)" novalidate="novalidate" id="up-settings-optios">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="post-per-page">Forum Per Page:</label>
                            </th>
                            <td>
                                <select name="post_per_page" id="post-per-page" class="regular-text">
                                    <?php
                                        $get_option =  get_option( 'uf_settings_post_per_page', 10 );
                                    ?>
                                    <option value="10" <?php selected( 10, $get_option, true ); ?>>--Select Default--</option>
                                    <option value="15" <?php selected( 15, $get_option, true ); ?>>15</option>
                                    <option value="20" <?php selected( 20, $get_option, true ); ?>>20</option>
                                    <option value="25" <?php selected( 25, $get_option, true ); ?>>25</option>
                                    <option value="30" <?php selected( 30, $get_option, true ); ?>>30</option>
                                    <option value="35" <?php selected( 35, $get_option, true ); ?>>35</option>
                                    <option value="40" <?php selected( 40, $get_option, true ); ?>>40</option>
                                    <option value="45" <?php selected( 45, $get_option, true ); ?>>45</option>
                                    <option value="50" <?php selected( 50, $get_option, true ); ?>>50</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="action" value="uf_update_settings_options">
                <?php wp_nonce_field( 'update_settings_options', 'update_forum_nonce' ); ?>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                </p>
            </form>
        </div>
        <div class="options-author">
            <h2 class="inner-title">Author Information</h2>
        </div>
    </div>
</div>