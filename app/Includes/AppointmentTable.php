<?php
/**
 * Appointment Table
 *
 * @link       https://github.com/davidtowoju
 * @since      0.1.0
 *
 * @package    Bookslots
 */

namespace Bookslots\Includes;

use Carbon\Carbon;
use Bookslot as App;
use Bookslots\Appointment;

// WP_List_Table is not loaded automatically so we need to load it in our application
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/Includes/class-wp-list-table.php';
}

/**
 * Appointment Table Class
 *
 * @package    Bookslots
 */
class AppointmentTable extends \WP_List_Table {

	/**
	 * Prepare the items for the table to process
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data        = $this->table_data( $columns, $hidden, $sortable );
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />', // Render a checkbox instead of text.
			'id'         => 'ID',
			// 'title'      => 'UUID',
			'date'       => 'Date',
			'start_time' => 'Start Time',
			'end_time'   => 'End Time',
		);

		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array( 'id' => array( 'id', false ) );
	}

	/**
	 * Get the table data
	 *
	 * @return array
	 */
	private function table_data( $columns, $hidden, $sortable ) {
		$per_page     = 5;
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$appointments = new \WP_Query(
			array(
				'post_type'      => 'bookslot_appointment',
				'posts_per_page' => $per_page,
				'offset'         => $offset,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'order'          => 'DESC',
			)
		);

		$total_items = $appointments->found_posts;
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$appointments = $appointments->get_posts();
		$data         = [];

		foreach ( $appointments as $appointment_id ) {
			$appointment = new Appointment( $appointment_id );
			$data[]      = array(
				'ID'          => $appointment->ID,
				'title'       => $appointment->post_title,
				'employee_id' => $appointment->employee_id,
				'service_id'  => $appointment->service_id,
				'date'        => date_i18n( 'F j, Y', strtotime( $appointment->date ) ),
				'start_time'  => Carbon::createFromTimestamp( $appointment->start_time, wp_timezone_string() )->format( 'g:i a' ),
				'end_time'    => Carbon::createFromTimestamp( $appointment->end_time, wp_timezone_string() )->format( 'g:i a' ),
				// 'end_time'    => $appointment->end_time,
			);
		}
		// dd($appointments);
		return $data;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array  $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				// case 'title':
			case 'date':
			case 'start_time':
			case 'end_time':
				return $item[ $column_name ];

			default:
				return '';
		}
	}


	/**
	 * Get value for checkbox column.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['ID']                // The value of the checkbox should be the record's ID.
		);
	}

	/**
	 * Adds checkbox
	 *
	 * @param array $item array of items.
	 * @return mixed
	 */
	public function column_id( $item ) {
		$edit_link = '#0';
		$view_link = get_permalink( $item['ID'] );
		$output    = '';

		// Title.
		$output .= '<strong><a x-data @click.prevent="$dispatch(\'e-edit-appointment\', { id: $el.dataset.id })" data-id="' . esc_html( $item['ID'] ) . '" href="' . esc_url( $edit_link ) . '" class="row-title">' . esc_html( $item['ID'] ) . '</a></strong><br/>';

		$output .= '<span>' . esc_html( $item['title'] ) . '</span>';

		$delete_nonce = wp_create_nonce( 'delete_appointment' );
		$delete_url   = add_query_arg(
			array(
				'action'      => 'delete_appointment',
				'appointment' => $item['ID'],
				'_wpnonce'    => $delete_nonce,
			),
			admin_url( 'admin.php?page=bookslots' )
		);

		// Get actions.
		$actions = array(
			'edit'  => '<a x-data @click.prevent="$dispatch(\'e-edit-appointment\', { id: $el.dataset.id })" data-id="' . $item['ID'] . '" target="_blank" href="' . esc_url( $edit_link ) . '">' . esc_html__( 'Edit', 'my_plugin' ) . '</a>',
			'trash' => '<a href="' . esc_url( $delete_url ) . '" class="submitdelete">' . esc_html__( 'Trash', 'my_plugin' ) . '</a>',
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$output .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $output;
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @param [type] $a $a.
	 * @param [type] $b $b.
	 * @return mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'id';
		$order   = 'desc';

		// If orderby is set, use this as the sort column
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
		}

		// If order is set use this as the order
		if ( ! empty( $_GET['order'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['order'] ) );
		}

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( $order === 'asc' ) {
			return $result;
		}

		return -$result;
	}
}
