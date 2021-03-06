<?php
/**
 * Sidebar menu custom walker.
 *
 * @since 1.0.0
 *
 * @package sk-theme
 */
class SK_Walker_Top_Menu extends Walker_Nav_Menu {

  public function start_el( &$output, $item, $depth = 0, $args = array(), $current_object_id = 0 ) {
    global $wp_query;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;

    $classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item );
    $class_names = '';

    if( is_array( $classes ) ) {
        $class_names = join( ' ', $classes );
        $class_names = ' class="'. esc_attr( $class_names ) . '"';
    }
    
    $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

    $prepend = '';
    $append = '';
    $description  = ! empty( $item->description ) ? '<span>' . esc_attr( $item->description ) .'</span>' : '';

    if($depth != 0) {
       $description = $append = $prepend = "";
    }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';

    $item_output .= $args->link_before . $prepend . apply_filters( 'the_title', $item->title, $item->ID ) . $append;
    $item_output .= $description . $args->link_after;
    
    if( SK_Menus::is_dropdown_activated() && ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
      $item_output .= '<div class="of-icon dropdown-icon of-inner-half"><i><svg viewBox="0 0 512 512"><use xlink:href="#arrow-down"></use></svg></i></div></a>';
    }else{
      $item_output .= '</a>';
    }

    $item_output .= $args->after;

    // The menu item has children. Add "of-expand" from of-sidebar-menu-advanced.
    if( ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
      $item_output .= '<a href="#" class="of-expand"><span class="of-icon of-icon-only"><i>'. __icon( 'plus-circle', 96 ) .'</i><i>'. __icon( 'minus-circle', 96 ) .'</i></span></a>';
    } 

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  /**
   * Adding css class on <ul> tag for a new level.
   * 
   */
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat( "\t", $depth );
    
    if( SK_Menus::is_dropdown_activated() )
      $output .= "\n$indent<ul class=\"sub-menu\">\n";
    else
      $output .= "\n$indent<ul class=\"disable-sub-menu\">\n";
  }

}