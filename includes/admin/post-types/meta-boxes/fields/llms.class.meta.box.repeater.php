<?php
/**
 * Metabox Repeater Field
 * @since    [version]
 * @version  [version]
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Metabox_Repeater_Field extends LLMS_Metabox_Field implements Meta_Box_Field_Interface {

	/**
	 * Class constructor
	 * @param array $_field Array containing information about field
	 * @since    [version]
	 * @version  [version]
	 */
	function __construct( $_field ) {

		$button_defaults = array(
			'classes' => '', // array or space seperated string
			'icon' => 'dashicons-plus', // dashicon classname or HTML/String
			'id' => $_field['id'] . '-add-new',
			'size' => 'small',
			'style' => 'primary',
			'text' => __( 'Add New', 'lifterlms' ),
		);

		if ( empty( $_field['button'] ) ) {
			$_field['button'] = $button_defaults;
		} else {
			$_field['button'] = wp_parse_args( $_field['button'], $button_defaults );
		}

		// $_field['group'] = 'llms-collapsible';
		// $_field['desc_class'] = 'llms-collapsible-header';

		$this->field = $_field;

	}

	/**
	 * Retrieve the HTML for the repeater add more button
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	private function get_button() {

		$btn = $this->field['button'];

		// setup class list
		$classes = explode( ' ', $btn['classes'] );
		$classes[] = sprintf( 'llms-button-%s', $btn['style'] );
		$classes[] = $btn['size'];
		$classes[] = 'llms-repeater-new-btn';
		$classes = implode( ' ', $classes );

		// setup icon
		if ( $btn['icon'] && 0 === strpos( $btn['icon'], 'dashicons-' ) ) {
			$icon = '<span class="dashicons ' . $btn['icon'] . '"></span>&nbsp;';
		} else {
			$icon = $btn['icon'];
		}

		return '<button class="' . $classes . '" type="button">' . $icon . $btn['text'] . '</button>';

	}

	private function get_rows() {

		// $i = 0;

		$rows = '';
		// while ( $i < 1 ) {
		// 	$rows .= $this->get_row( $i );
		// 	$i++;
		// }

		return $rows;
	}

	private function get_row( $index ) {

		ob_start();
		?>

		<div class="llms-collapsible llms-repeater-row" data-row-order="<?php echo $index; ?>">

			<header class="llms-collapsible-header">
				<div class="d-2of3">
					<h3 class="llms-repeater-title"><?php echo $this->field['header']['default']; ?></h3>
				</div>
				<div class="d-1of3 d-right">
					<span class="dashicons dashicons-arrow-down"></span>
					<span class="dashicons dashicons-arrow-up"></span>
					<span class="dashicons dashicons-menu llms-drag-handle"></span>
					<span class="dashicons dashicons-no llms-repeater-remove"></span>
				</div>
			</header>

			<section class="llms-collapsible-body">

				<ul class="llms-mb-repeater-fields">

					<?php foreach ( $this->field['fields'] as $field ) : ?>

						<?php echo $this->get_sub_field( $field, $index ); ?>

					<?php endforeach; ?>

				</ul>

			</section>

		</div>

		<?php
		return ob_get_clean();

	}

	private function get_sub_field( $field, $index ) {

		$field['id'] .= '_' . $index;

		if ( isset( $field['controller'] ) ) {
			$field['controller'] .= '_' . $index;
		}

		$field_class_name = str_replace( '{TOKEN}', ucfirst( strtr( preg_replace_callback( '/(\w+)/', create_function( '$m','return ucfirst($m[1]);' ), $field['type'] ),'-','_' ) ), 'LLMS_Metabox_{TOKEN}_Field' );
		$field_class = new $field_class_name( $field );
		ob_start();
		$field_class->output();
		return ob_get_clean();

	}

	/**
	 * Outputs the Html for the given field
	 * @return   string
	 * @since    [version]
	 * @version  [version]
	 */
	public function output() {

		global $post;

		parent::output();

		echo '<div class="llms-repeater-model" id="' . $this->field['id'] . '-model" style="display:none;">' . $this->get_row( 'model' ) . '</div>';

		echo '<div class="llms-collapsible-group llms-repeater-rows">' .  $this->get_rows() . '</div>';

		echo '<footer class="llms-mb-repeater-footer">';
			echo $this->get_button();
		echo '</footer>';

		echo '<input class="llms-repeater-field-handler" type="hidden" value="' . $this->field['handler'] . '">';

		parent::close_output();

	}
}
