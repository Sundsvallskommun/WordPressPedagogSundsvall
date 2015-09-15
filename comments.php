<?php
/**
 * The template for displaying Comments.
 *
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

  if (post_password_required()) {
    return;
  }
?>
<section id="comments" class="comments">
  <?php if (have_comments()) : ?>
    <h3><?php printf( __('<span class="title">Kommentarer </span><span class="badge">%s</span>', 'sk' ), get_comments_number() ); ?></h3>

    <ol class="comment-list clear-float">
      <?php wp_list_comments(array('style' => 'ol', 'short_ping' => true)); ?>
    </ol>

    <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
      <nav>
        <ul class="pager">
          <?php if (get_previous_comments_link()) : ?>
            <li class="previous"><?php previous_comments_link(__('Äldre kommentarer', 'sk')); ?></li>
          <?php endif; ?>
          <?php if (get_next_comments_link()) : ?>
            <li class="next"><?php next_comments_link(__('Nyare kommentarer', 'sk')); ?></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; // have_comments() ?>

  <?php if (!comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments')) : ?>
    <div class="alert alert-warning">
      <?php _e('Kommentering är avstängd.', 'sk'); ?>
    </div>
  <?php endif; ?>

  <?php comment_form(); ?>
</section>
