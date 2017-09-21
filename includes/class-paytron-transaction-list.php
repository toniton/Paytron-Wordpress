<?php
  /**
   * Paytron Payment List
   */
  if ( ! defined( 'ABSPATH' ) ) { exit; }
  
  if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
  }

  if ( ! class_exists( 'PTR_Payment_List' ) ) {
    
    class PTR_Payment_List extends WP_List_Table {
      protected static $instance = null;
      
      public function __construct() {
        parent::__construct( array(
          'singular' => __( 'Payment List', 'ptr-payments' ),
          'plural'   => __( 'Payment Lists', 'ptr-payments' ),
          'ajax'     => false
        ) );
        add_action( 'init', array( $this, 'add_payment_list_post_type' ) );
      }
      
      public function no_items() {
        _e( 'No payments have been made yet.', 'ptr-payments' );
      }
      
      public function column_tx_ref( $item ) {
        $title = '<strong>' . get_post_meta( $item->ID, '_ptr-paytron_payment_tx_ref', true ) . '</strong>';
        $actions = array(
          'delete' => sprintf( '<a href="%s">Delete</a>', get_delete_post_link( absint( $item->ID ) ) )
        );
        return $title . $this->row_actions( $actions );
      }

      public function column_amount( $item ) {
        $amount = get_post_meta( $item->ID, '_ptr-paytron_payment_amount', true );
        return number_format( $amount, 2 );
      }
      
      public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
          case 'customer':
          case 'status':
            return get_post_meta( $item->ID, '_ptr-paytron_payment_' . $column_name, true );
          case 'date':
            return $item->post_date;
          default:
            return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
      }
      
      function get_columns() {
        global $admin_settings;
        $columns = array(
          'cb'      => '<input type="checkbox" />',
          'tx_ref'  => __( 'Transaction Ref', 'ptr-payments' ),
          'customer' => __( 'Customer', 'ptr-payments' ),
          'amount'  => __( 'Amount (' . $admin_settings->get_option_value( 'currency' ) . ')', 'ptr-payments' ),
          'status'  => __( 'Status', 'ptr-payments' ),
          'date'    => __( 'Date', 'ptr-payments' ),
        );
        return $columns;
      }
      
      public function column_cb( $item ) {
        return sprintf(
          '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item->ID
        );
      }
      
      public function prepare_items() {
        $this->_column_headers = array( 
             $this->get_columns(),
             array(),
             $this->get_sortable_columns(),
        );
        $per_page     = $this->get_items_per_page( 'payments_per_page' );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
        $this->set_pagination_args( array(
          'total_items' => $total_items,
          'per_page'    => $per_page
        ) );
        $this->items = self::get_payments( $per_page, $current_page );
      }

      public function set_screen( $status, $option, $value ) {
        return $value;
      }
      
      public static function get_payments( $post_per_page = 20, $page_number = 1 ) {
        $args = array(
          'posts_per_page'   => $post_per_page,
          'offset'           => ( $page_number - 1 ) * $post_per_page,
          'orderby'          => ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby']  : 'date',
          'order'            => ! empty( $_REQUEST['order'] )   ? $_REQUEST['order']    : 'DESC',
          'post_type'        => 'paytron_payment_list',
          'post_status'      => 'publish',
          'suppress_filters' => true
        );
        $payment_list = get_posts( $args );
        return $payment_list;
      }
      
      public static function delete_payment( $payment_id ) {
        wp_delete_post( $payment_id );
      }
      
      public static function record_count() {
        $total_records = wp_count_posts( 'paytron_payment_list' );
        return $total_records->publish;
      }
      
      public function add_payment_list_post_type() {
        $args = array(
          'label'               => __( 'Payment Lists', 'ptr-payments' ),
          'description'         => __( 'Paytron payment lists', 'ptr-payments' ),
          'supports'            => array( 'title', 'author', 'custom-fields', ),
          'hierarchical'        => false,
          'public'              => false,
          'show_ui'             => true,
          'show_in_menu'        => false,
          'show_in_nav_menus'   => false,
          'show_in_admin_bar'   => false,
          'exclude_from_search' => true,
          'capability_type'     => 'post',
        );
        register_post_type( 'paytron_payment_list', $args );
      }
      
      public static function get_instance() {
        if ( self::$instance == null ) {
          self::$instance = new self;
        }
        return self::$instance;
      }
    }
  }
?>
