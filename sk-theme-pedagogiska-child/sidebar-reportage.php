<div class="sk-sidebar-panel-wrapper">

  <nav class="of-sidebar-menu">
    <?php $panels = get_field('sk_panels', $post->ID); ?>
    <?php if(! empty( $panels ) ) : ?>
    	<h5><?php _e('Fördjupning', 'sk'); ?></h5>
    <?php foreach( $panels as $panel ) : ?>
    	<div class="sk-sidebar-panel">
    	<div class="sk-sidebar-panel-title"><?php echo $panel['sk_panel_header']; ?></div>
      <div class="sk-sidebar-panel-list">
    	<?php echo $panel['sk_panel_content']; ?>
      </div>
    	</div>
    <?php endforeach; ?>
  <?php endif; ?>
  </nav>
</div>