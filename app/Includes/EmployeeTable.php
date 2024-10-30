<?php
/**
 * Employee Table
 *
 * @link       https://github.com/davidtowoju
 * @since      0.1.0
 *
 * @package    Bookslots
 */

namespace Bookslots\Includes;

use Bookslots\Employee;
use Bookslots as App;

// WP_List_Table is not loaded automatically so we need to load it in our application
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/Includes/class-wp-list-table.php';
}

/**
 * Employee Table Class
 *
 * @package    Bookslots
 */
class EmployeeTable extends \WP_List_Table {

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
			'cb'           => '<input type="checkbox" />', // Render a checkbox instead of text.
			'id'           => 'ID',
			'title'        => get_label( 'employee' ),
			'availability' => 'Availability',
		);

		return $columns;
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
			$item['id']                // The value of the checkbox should be the record's ID.
		);
	}


	public function column_id( $item ) {
		$edit_link = '#0';
		$view_link = get_permalink( $item['id'] );
		$output    = '';

		// Title.
		$output .= '<strong><a x-data @click.prevent="$dispatch(\'e-edit-employee\', { id: $el.dataset.id })" data-id="' . esc_html( $item['id'] ) . '" href="' . esc_url( $edit_link ) . '" class="row-title">' . esc_html( $item['id'] ) . '</a></strong>';

		$delete_nonce = wp_create_nonce( 'delete_employee' );
		$delete_url   = add_query_arg(
			array(
				'action'   => 'delete_employee',
				'employee' => $item['id'],
				'_wpnonce' => $delete_nonce,
			),
			admin_url( 'admin.php?page=bookslots-employees' )
		);

		// Get actions.
		$actions = array(
			'edit'  => '<a x-data @click.prevent="$dispatch(\'e-edit-employee\', { id: $el.dataset.id })" data-id="' . $item['id'] . '" target="_blank" href="' . esc_url( $edit_link ) . '">' . esc_html__( 'Edit', 'my_plugin' ) . '</a>',
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
		return array( 'title' => array( 'title', false ) );
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_data( $columns, $hidden, $sortable ) {
		$per_page     = 5;
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$employees = new \WP_Query(
			array(
				'post_type'      => 'bookslot_employee',
				'posts_per_page' => $per_page,
				'offset'         => $offset,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'order'          => 'DESC',
			)
		);

		$totalItems = $employees->found_posts;
		$this->set_pagination_args(
			array(
				'total_items' => $totalItems,
				'per_page'    => $per_page,
			)
		);

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$employees = $employees->get_posts();
		$data      = [];

		foreach ( $employees as $key => $employee_id ) {
			$employee = new Employee( $employee_id );
			// dd($employee);
			$data[] = array(
				'user_id'      => $employee->user_id,
				'id'           => $employee->ID,
				'title'        => fullname( $employee->user() ),
				'availability' => ucfirst( $employee->schedule['availability']['datetype'] ),
			);
		}

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
			case 'title':
			case 'availability':
				// case 'year':
				// case 'director':
				// case 'rating':
				return $item[ $column_name ];

			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
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
