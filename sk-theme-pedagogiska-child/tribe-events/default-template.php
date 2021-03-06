<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header(); ?>
<div class="of-wrap">
  <div class="sk-main-padded of-inner of-clear">
  	<div class="of-c-sm-4 of-c-md-3">
			<div id="tribe-events-pg-template">
				<?php tribe_events_before_html(); ?>
				<div class="sk-entry-content">
				<?php tribe_get_view(); ?>
				</div>
				<?php tribe_events_after_html(); ?>
			</div> <!-- #tribe-events-pg-template -->
			<?php comments_template( '', true );  ?>
	  </div>

		<div class="of-c-sm-4 of-c-md-1 of-omega">
		<!--[sidebar events ...]-->
		</div>
	  <?php //get_sidebar(); ?>
</div>
</div>
<?php get_footer(); ?>