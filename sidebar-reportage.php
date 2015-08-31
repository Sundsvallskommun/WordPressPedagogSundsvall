
  <nav class="of-sidebar-menu">
    <?php $panels = get_field('sk_panels', $post->ID); ?>
    <?php if(! empty( $panels ) ) : ?>
    	
    <?php foreach( $panels as $panel ) : ?>
    	<div class="sk-sidebar-panel">
    	<p><b><?php echo $panel['sk_panel_header']; ?></b></p>
    	<?php echo $panel['sk_panel_content']; ?>
    	</div>
    <?php endforeach; ?>
  <?php endif; ?>
  </nav>
