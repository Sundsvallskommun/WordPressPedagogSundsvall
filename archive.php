<?php
/**
 * The template for displaying Archive pages.
 */

get_header(); ?>

<div class="of-wrap">
  <?php if( is_main_site() ) :?>
    <div class="sk-main-padded of-inner">
  <?php else : ?>
    <div class="sk-main of-inner">
  <?php endif; ?>
    <div class="of-c-sm-4 of-c-md-3">
  	<?php if ( have_posts() ) : ?>
  		<header>
  			<h1>
          <?php
            if ( is_day() ) :
              printf( __( 'Dagligt arkiv: %s', 'sk' ), get_the_date() );

            elseif ( is_month() ) :
              printf( __( 'Månadsarkiv: %s', 'sk' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'sk' ) ) );

            elseif ( is_year() ) :
              printf( __( 'Årligt arkiv: %s', 'sk' ), get_the_date( _x( 'Y', 'yearly archives date format', 'sk' ) ) );

            else :
              _e( 'Arkiv', 'sk' );

            endif;
          ?>
        </h1>
  		</header>

  		<?php	get_template_part( 'loop', 'index' ); ?>
  	<?php else : ?>
  		<?php get_template_part( 'loop', 'index' ); ?>
  	<?php endif; ?>
    </div>
    <?php if( is_main_site() ) :?>
    <?php get_sidebar(); ?>
  <?php else : ?>
    <?php get_sidebar( 'blog-subsite' ); ?>
  <?php endif; ?>
  </div>
</div>
<?php get_footer(); ?>