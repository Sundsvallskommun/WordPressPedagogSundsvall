<?php

/**
 * Calendar box class.
 *
 * @since 1.0.0
 *
 * @package sk-theme
 *
 */

	class Class_SK_Calendar {  
     
    private $day_labels = array("Mån","Tis","Ons","Tor","Fre","Lör","Sön");
    private $current_year=0;
    private $current_month=0;
    private $current_day=0;
    private $current_date=null;
    private $days_in_month=0;
    private $navi_href= null;
    private $occupied_days = array();


    public function __construct(){     
        $this->navi_href = htmlentities($_SERVER['PHP_SELF']);
    }
     
    public function show() {
    	$this->fetch_events();
			
			$year = date( "Y" , time() );
			$month = date( "m" , time() );
       
      $this->current_year = $year;
      $this->current_month = $month;
      $this->days_in_month = $this->calculate_days_in_month( $month, $year ); 
       
      $content='<div id="calendar">'.
                  '<div class="box">'.
                  $this->create_navigation().
                  '</div>'.
                  '<div class="box-content">'.
                    '<ul class="label">'.$this->create_labels().'</ul>';
                    $content.='<div class="clear"></div>';     
                    $content.='<ul class="dates">';    
                     
                    $weeks_in_month = $this->calculate_weeks_in_month( $month, $year );
                    // Create weeks in a month
                    for( $i=0; $i<$weeks_in_month; $i++ ) {
                         
                        //Create days in a week
                        for($j=1;$j<=7;$j++){
                            $content.=$this->show_day( $i * 7 + $j );
                        }
                    }
                     
                    $content.='</ul>';
        
                    $content .= '<div class="link-to-calendar"><a href="' . get_bloginfo('url') . '/' . Tribe__Settings_Manager::instance()->get_option( 'eventsSlug', 'events' ) . '/">' . __('Öppna kalender', 'sk') . '</a></div>';                  
                    $content.='<div class="clear"></div>';     
       
                  $content.='</div>';
               
      $content.='</div>';

      return $content;   
    }
     

    private function show_day( $cell_number ) {


        if( $this->current_day == 0 ) {
             
            $first_day_of_the_week = date( 'N', strtotime( $this->current_year . '-' . $this->current_month . '-01' ) );
                     
            if( intval( $cell_number ) == intval( $first_day_of_the_week) ) {
                 
                $this->current_day = 1;
                 
            }
        }
         
        if( ( $this->current_day != 0 ) && ( $this->current_day <= $this->days_in_month ) ) {
             
            $this->current_date = date( 'Y-m-d', strtotime( $this->current_year . '-' . $this->current_month . '-' . ( $this->current_day ) ) );
             
            $cell_content = $this->current_day;

            if( in_array( $this->current_day, $this->occupied_days ) ) {
            	$cell_content = '<a href="'. get_bloginfo( 'url' ) . '/' . Tribe__Settings_Manager::instance()->get_option( 'eventsSlug', 'events' ) . '/' . $this->current_date . '/">' . $cell_content . '</a>';
              $lastest_day = $this->current_day;
            } 
            $this->current_day++;   
            
             
        } else{
             
            $this->current_date = null;
            $cell_content = null;
        }

        if( !empty( $lastest_day ) && in_array( $lastest_day, $this->occupied_days ) ) {
        	return '<li id="li-'.$this->current_date.'" class="'.($cell_number%7==1?' start ':($cell_number%7==0?' end ':' ')).($cell_content==null?'mask':'').' occupied">'.$cell_content.'</li>';
        } else {
        	return '<li id="li-'.$this->current_date.'" class="'.($cell_number%7==1?' start ':($cell_number%7==0?' end ':' ')).($cell_content==null?'mask':'').'">'.$cell_content.'</li>';
        }
    }
     

    private function create_navigation(){
        $next_month = $this->current_month == 12 ? 1 : intval( $this->current_month) + 1;
        $next_year = $this->current_month == 12 ? intval( $this->current_year) + 1 : $this->current_year;
        $pre_month = $this->current_month == 1 ? 12 : intval( $this->current_month ) - 1;
        $pre_year = $this->current_month == 1 ? intval( $this->current_year) - 1 : $this->current_year;
         
        return
            '<div class="header">'.
                '<a href="' . get_bloginfo('url') . '/' . Tribe__Settings_Manager::instance()->get_option( 'eventsSlug', 'events' ) . '/"><span class="title">'.ucfirst( date_i18n('F Y', strtotime( $this->current_year . '-' . $this->current_month . '-1' ) ) ) . '</span></a>'.
            '</div>';
    }



    private function create_labels(){  
        $content = '';
        foreach( $this->day_labels as $index=>$label ) {
          $content.='<li class="'.( $label==6?'end title':'start title' ).' title">'.$label.'</li>';
        }
         
        return $content;
    }
     
    private function calculate_weeks_in_month( $month=null, $year=null ){
         
        if( null == ($year) ) {
            $year = date( "Y", time() ); 
        }
         
        if( null == ( $month ) ) {
            $month = date( "m", time() );
        }
         
        // find number of days in this month
        $days_in_months = $this->calculate_days_in_month( $month, $year );
        $num_of_weeks = ( $days_in_months % 7 == 0 ? 0 : 1 ) + intval( $days_in_months / 7 );
        $month_ending_day= date( 'N', strtotime( $year.'-'.$month.'-'.$days_in_months ) );
        $month_start_day = date( 'N', strtotime( $year.'-'.$month.'-01' ) );

        if($month_ending_day < $month_start_day ) {
             
            $num_of_weeks++;
         
        }
         
        return $num_of_weeks;
    }
 


    private function calculate_days_in_month( $month=null, $year=null ){
         
        if( null == ( $year ) )
            $year =  date( "Y", time() ); 
 
        if( null == ( $month ) )
            $month = date( "m", time() );
             
        return date( 't', strtotime( $year . '-' . $month . '-01' ) );
    }



	public function fetch_events( $cats = array() ) {
		
		/**
		 * Check if events calendar plugin method exists
		 */
		if ( !function_exists( 'tribe_get_events' ) ) {
			return;
		}

		global $wp_query, $post;

		$args = array(
			'post_type' => 'tribe_events',
			'posts_per_page' => -1,
			'meta_key' => '_EventEndDate',
			'orderby' => 'meta_value',
			'meta_query' => array(
				array(
						'key' => '_EventEndDate',
						'value' => date('Y-m-d'),
						'compare' => '>=',
						'type' => 'DATETIME'
					)
				)
		);

		if( count( $cats ) > 0 ) {
			
			$event_tax = array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' => $cats
				)
			);

			$args['tax_query'] = $event_tax;

		}

		$posts = get_posts( $args );

		if ($posts) {

			foreach( $posts as $post ) {

				$startdate = get_post_meta( $post->ID, '_EventStartDate', true );
				$enddate = get_post_meta( $post->ID, '_EventEndDate', true );
        

        $dates = $this->date_range( $startdate, $enddate );
				$month = date('m', strtotime( $startdate ));

				$todays_month = date('m');

          foreach ( $dates as $date ) {
            if( $todays_month == date( 'm', strtotime( $date ) ) && strtotime( date('Y-m-d') ) <= strtotime( $date ) ) {
               $this->occupied_days[] = date('d', strtotime( $date ) );
            } 
          }
					
				

			}

		}

		wp_reset_query();

	}

  /**
   * Takes two dates as YYYY-MM-DD and creates an array of the dates between
   * 
   * @param  string date
   * @param  string date
   * @return array dates
   */
  public function date_range( $date_from, $date_to ){

    $range = array();

    $i_date_from = mktime( 1, 0, 0, substr( $date_from, 5, 2 ), substr( $date_from, 8, 2 ), substr( $date_from, 0, 4 ) );
    $i_date_to = mktime( 1, 0, 0, substr( $date_to, 5, 2 ), substr( $date_to, 8, 2 ), substr( $date_to, 0, 4 ) );

    if( $i_date_to >= $i_date_from ){
        array_push( $range, date( 'Y-m-d', $i_date_from ) ); // first entry
        while ( $i_date_from < $i_date_to ){
            $i_date_from += 86400; // add 24 hours
            array_push( $range, date( 'Y-m-d', $i_date_from ) );
        }
    }
    return $range;
}
     
}