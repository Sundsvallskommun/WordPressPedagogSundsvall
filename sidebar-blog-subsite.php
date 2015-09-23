<?php require_once( 'lib/class-sk-sidebar-blog-subsite.php' ); ?>

<div class="of-c-sm-4 of-c-md-1 of-omega sidebar-blog">
<?php $sk_sidebar_blog_subsite->get_blog_info_html(); ?>

<div class="sk-sidebar-panel panel-white">
  <div class="sk-blog-categories sk-sidebar-panel-row">
    <h4><?php _e( 'Kategorier', 'sk') ?></h4>
    <ul>
      <?php wp_list_categories( array( 'title_li' => false ) ); ?> 
    </ul>
  </div>  

  <div class="sk-blog-tags sk-sidebar-panel-row">
    <h4><?php _e( 'Etiketter', 'sk') ?></h4>
  	<?php wp_tag_cloud( $args ); ?>
  </div>
  
  <div class="sk-blog-archive sk-sidebar-panel-row">
    <h4><?php _e( 'Arkiv', 'sk') ?></h4>
    <ul>
      <?php wp_get_archives(); ?>


      
    </ul>
  </div>
</div>
</div>