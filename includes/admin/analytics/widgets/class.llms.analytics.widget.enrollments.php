<?php
/**
* Enrollments analytics widget
* @since  3.0.0
* @version 3.0.0
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Analytics_Enrollments_Widget extends LLMS_Analytics_Widget {

	public function set_query() {

		global $wpdb;

		$dates = $this->get_posted_dates();

		$student_ids = '';
		$students = $this->get_posted_students();
		if ( $students ) {
			$student_ids .= 'AND user_id IN ( ' . implode( ', ', $students ) . ' )';
		}

		$product_ids = '';
		$products = $this->get_posted_posts();
		if ( $products ) {
			$product_ids .= 'AND post_id IN ( ' . implode( ', ', $products ) . ' )';
		}


		$this->query_function = 'get_var';

		$this->query = "SELECT COUNT(meta_id)
						FROM {$wpdb->prefix}lifterlms_user_postmeta
						WHERE
							    meta_key = '_status'
							AND ( meta_value = 'Enrolled' OR meta_value = 'enrolled' )
							AND updated_date BETWEEN CAST( %s AS DATETIME ) AND CAST( %s AS  DATETIME )
							{$student_ids}
							{$product_ids}
						;";

		$this->query_vars = array(
			$this->format_date( $dates['start'], 'start' ),
			$this->format_date( $dates['end'], 'end' ),
		);

	}

	protected function format_response() {

		if ( ! $this->is_error() ) {

			return intval( $this->get_results() );

		}

	}

}