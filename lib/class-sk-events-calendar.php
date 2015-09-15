<?php
/**
 * Class for events calendar
 *
 * @since 1.0.0
 *
 * @package sk-theme
 */
class SK_Events_Calendar {

	private $event_date = '';
	
	public function __construct() {
		add_action( 'wp', array( $this, 'date_url_fix') );
	}

	/**
	 * Bugfix - adding a script when a user list events by date
	 *
	 * @since 1.0.0 
	 * 
	 * @return null
	 */
	public function date_url_fix(){
		$this->event_date = get_query_var( 'eventDate' );
		$this->event_display = get_query_var( 'eventDisplay' );
		
		if( !empty( $this->event_date ) && isset( $this->event_display ) && $this->event_display == 'day' ){
			add_action('wp_footer', array( $this, 'footer_script'), 999 );
		}
	}

	/**
	 * Add script to footer to trigger a date call 
	 *
	 * @since  1.0.0 
	 * 
	 * @return null
	 * 
	 */
	public function footer_script(){ ?>
	<script type="text/javascript">
		(function ($) {
			"use strict";
			$(function() {
				var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

				$('.tribe-events-day-time-slot').hide();
				$('.tribe-events-notices').hide();
				var $loadingImg = $( '.tribe-events-ajax-loading:first' ).clone().addClass( 'tribe-events-active-spinner' );
				$loadingImg.prependTo( '#tribe-events-content' );
				$( this ).addClass( 'tribe-events-loading' ).css( 'opacity', .25 )

				$.post( ajaxurl, {
					action: 'tribe_event_day',
					eventDate: '<?php echo $this->event_date ?>'
				}, function( response ) {
					var $the_content = $.parseHTML( response.html );
					$( '#tribe-events-content' ).replaceWith( $the_content );
				});

    	})
		}(jQuery));	
	</script>
	<?php
	}

}