<?php
// directly access denied
defined('ABSPATH') || exit;
?>

<div class="forum-wrap uf-reset">
    <div class="forum-post">
        <form action="" class="add-new-forum" id="add-new-forum-post">
            <input type="text" name="forum_tite" placeholder="Forum Title">
            <textarea name="forum_content" placeholder="Write content"></textarea>
            <?php wp_nonce_field( 'add_new_forum_post', 'forum_nonce' ); ?>
            <div class="text-right">
                <input type="submit" class="button-submit" value="Publish">
            </div>
        </form>
    </div>
    <ul class="forum-items">
        <?php uf_get_forum_posts(); ?>
    </ul>
</div>