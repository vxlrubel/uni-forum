<?php
// directly access denied
defined('ABSPATH') || exit;

?>

<form id="uf-registration-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
    <input type="hidden" name="action" value="uf_user_registration">
    <label for="uf-user-email">Email Address *</label>
    <input type="email" name="uf_user_email" placeholder="Email" required id="uf-user-email">

    <label for="uf-user-password">Password</label>
    <input type="password" name="uf_user_password" placeholder="Password" required id="uf-user-password">

    <input type="submit" value="Register">

</form>