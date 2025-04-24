<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WOOFC_Admin_Fields {

	/**
	 * Plugin prefix.
	 *
	 * @var string
	 */
	public static $prefix = 'woofc';

	/**
	 * Generate the WordPress feild.
	 *
	 * @param array $args
	 * @return void
	 */
	public static function add( $args = array() ) {
		// Merge user defined arguments into defaults array.
		$a = wp_parse_args( $args, array(
			'type'              => null,
			'id'                => '',
			'title'             => '',
			'css'               => '',
			'class'             => '',
			'placeholder'       => '',
			'desc'              => '',
			'default'           => '',
			'desc_tip'          => false,
			'custom_attributes' => array(),
		) );

		if ( $a['type'] === null ) {
			return;
		}

		if ( ! isset( $a['value'] ) ) {
			$a['value'] = get_option( $a['id'], $a['default'] );
		}

		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $a['custom_attributes'] ) && is_array( $a['custom_attributes'] ) ) {
			foreach ( $a['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
			$a['custom_attributes'] = $custom_attributes;
		}


		// Description
		$description    = self::get_field_description( $a );
		$a['desc']      = $description['description'];
		$a['desc_tip']  = $description['tooltip_html'];

		switch ( $a['type'] ) {
			// Standard text inputs and subtypes like 'number'.
			case 'text':
			case 'password':
			case 'datetime':
			case 'datetime-local':
			case 'date':
			case 'month':
			case 'time':
			case 'week':
			case 'number':
			case 'email':
			case 'url':
			case 'tel':
				self::standard_inputs( $a );
				break;

			case 'select':
			case 'multiselect':
				self::select( $a );
				break;

			case 'checkbox':
				self::checkbox( $a );
				break;

			case 'checkboxgroup':
				self::checkboxgroup( $a );
				break;

			case 'radio':
				self::radio( $a );
				break;

			case 'color':
				self::color( $a );
				break;

			case 'textarea':
				self::textarea( $a );
				break;

			case 'file':
				self::file( $a );
				break;

			case 'link':
				self::link( $a );
				break;

			case 'wp_editor':
				self::wp_editor( $a );
				break;

			case 'dropdown_pages':
				self::dropdown_pages( $a );
				break;

			case 'dropdown_categories':
				self::dropdown_categories( $a );
				break;

			case 'dropdown_roles':
				self::dropdown_roles( $a );
				break;

			default:
				# code...
				break;
		}
	}

	public static function standard_inputs( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<input
					name="<?php echo esc_attr( $args['id'] ); ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					type="<?php echo esc_attr( $args['type'] ); ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					value="<?php echo esc_attr( $args['value'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					/><?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function select( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ) ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<select
					name="<?php echo esc_attr( $args['id'] ); ?><?php echo ( 'multiselect' === $args['type'] ) ? '[]' : ''; ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					<?php echo 'multiselect' === $args['type'] ? 'multiple="multiple"' : ''; ?>
					>
					<?php
					foreach ( $args['options'] as $key => $val ) {
						?>
						<option value="<?php echo esc_attr( $key ); ?>"
							<?php
							if ( is_array( $args['value'] ) ) {
								selected( in_array( (string) $key, $args['value'], true ), true );
							} else {
								selected( $args['value'], (string) $key );
							}
							?>
						><?php echo esc_html( $val ); ?></option>
						<?php
					}
					?>
				</select><?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function checkbox( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<fieldset>
					<label for="<?php echo esc_attr( $args['id'] ); ?>">
						<input
							name="<?php echo esc_attr( $args['id'] ); ?>"
							id="<?php echo esc_attr( $args['id'] ); ?>"
							value="<?php echo esc_attr( $args['value'] ); ?>"
							type="checkbox"
							style="<?php echo esc_attr( $args['css'] ); ?>"
							class="<?php echo esc_attr( $args['class'] ); ?>"
							<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
							<?php checked( 'yes', $args['value'] ); ?>
							/> <?php echo $args['desc']; // WPCS: XSS ok. ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	public static function checkboxgroup( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<fieldset>
					<?php echo $args['desc']; // WPCS: XSS ok. ?>
					<ul>
					<?php
					foreach ( $args['options'] as $val ) {
						if ( ! isset( $val['id'] ) ) {
							$val['id'] = '';
						}
						if ( ! isset( $val['default'] ) ) {
							$val['default'] = '';
						}
						if ( ! isset( $val['desc'] ) ) {
							$val['desc'] = '';
						}
						if ( ! isset( $val['value'] ) ) {
							$val['value'] = get_option( $val['id'], $val['default'] );
						}
						?>
						<li>
							<label>
								<input
									name="<?php echo esc_attr( $val['id'] ); ?>"
									value="<?php echo esc_attr( $val['value'] ); ?>"
									type="checkbox"
									style="<?php echo esc_attr( $args['css'] ); ?>"
									class="<?php echo esc_attr( $args['class'] ); ?>"
									<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
									<?php checked( 'yes', $val['value'] ); ?>
								/> <?php echo esc_html( $val['desc'] ); ?>
							</label>
						</li>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	public static function radio( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<fieldset>
					<?php echo $args['desc']; // WPCS: XSS ok. ?>
					<ul>
					<?php
					foreach ( $args['options'] as $key => $val ) {
						?>
						<li>
							<label><input
								name="<?php echo esc_attr( $args['id'] ); ?>"
								value="<?php echo esc_attr( $key ); ?>"
								type="radio"
								style="<?php echo esc_attr( $args['css'] ); ?>"
								class="<?php echo esc_attr( $args['class'] ); ?>"
								<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
								<?php checked( $key, $args['value'] ); ?>
								/> <?php echo esc_html( $val ); ?></label>
						</li>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	public static function color( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<input
					name="<?php echo esc_attr( $args['id'] ); ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					type="text"
					dir="ltr"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					value="<?php echo esc_attr( $args['value'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?> colorpicker"
					placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					/> <?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function textarea( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<textarea
					name="<?php echo esc_attr( $args['id'] ); ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					><?php echo esc_textarea( $args['value'] ); // WPCS: XSS ok. ?></textarea>
					<?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function wp_editor( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'editor_width'  => '550',
			'media_buttons' => false,
			'editor_height' => '120',
		) );
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<div style="width: <?php echo intval( $args['editor_width'] ) ?>px;">
					<?php wp_editor( $args['value'], $args['id'], $args ) ?>
					<?php echo $args['desc']; // WPCS: XSS ok. ?>
				</div>
			</td>
		</tr>
		<?php
	}

	public static function link( $args = array() ) {
		if ( ! isset( $args['link'] ) ) {
			$args['link'] = '#';
		}
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<a
					id="<?php echo esc_attr( $args['id'] ); ?>"
					href="<?php echo esc_url( $args['link'] ) ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					><?php echo esc_html( $args['value'] ); ?></a><?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function file( $args = array() ) {
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<input
					name="<?php echo esc_attr( $args['id'] ); ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					type="text"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					value="<?php echo esc_attr( $args['value'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					data-upload-url-id="<?php echo esc_attr( $args['id'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					/>
				<input
					type="button"
					data-upload-id="<?php echo esc_attr( $args['id'] ); ?>"
					class="button-secondary"
					value="<?php echo esc_html( 'Upload Image' ) ?>"
					/>
				<?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function dropdown_pages( $args = array() ) {
		$default_args = apply_filters( self::$prefix . '_dropdown_pages', array(
			'sort_order'        => 'asc',
			'sort_column'       => 'post_title',
			'hierarchical'      => 1,
			'exclude'           => '',
			'include'           => '',
			'meta_key'          => '',
			'meta_value'        => '',
			'authors'           => '',
			'child_of'          => 0,
			'parent'            => -1,
			'exclude_tree'      => '',
			'number'            => '',
			'offset'            => 0,
			'post_type'         => 'page',
			'post_status'       => 'publish',
			'show_option_none'  => '',
			'option_none_value' => '',
		) );

		$args = wp_parse_args( $args, $default_args );

		$pages = get_pages( $args );

		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<select
					name="<?php echo esc_attr( $args['id'] ); ?><?php echo ( $args['multiselect'] === true ) ? '[]' : ''; ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					<?php echo $args['multiselect'] === true ? 'multiple="multiple"' : ''; ?>
					>
					<?php
					if ( $args['show_option_none'] ) {
						?>
							<option value="<?php echo esc_attr( $args['option_none_value'] ); ?>"
								<?php
								if ( is_array( $args['value'] ) ) {
									selected( in_array( (string) $args['option_none_value'], $args['value'], true ), true );
								} else {
									selected( $args['value'], (string) $args['option_none_value'] );
								}
								?>
							><?php echo esc_html( $args['show_option_none'] ); ?></option>
						<?php
					}
					foreach ( $pages as $val ) {
						?>
						<option value="<?php echo esc_attr( $val->ID ); ?>"
							<?php
							if ( is_array( $args['value'] ) ) {
								selected( in_array( (string) $val->ID, $args['value'], true ), true );
							} else {
								selected( $args['value'], (string) $val->ID );
							}
							?>
						><?php echo esc_html( $val->post_title ); ?></option>
						<?php
					}
					?>
				</select><?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>

		<?php
	}

	public static function dropdown_categories( $args = array() ) {
		$default_args = apply_filters( self::$prefix . '_dropdown_categories', array(
			'taxonomy'                  => 'category', //empty string(''), false, 0 don't work, and return empty array
			'orderby'                   => 'name',
			'order'                     => 'ASC',
			'hide_empty'                => true, //can be 1, '1' too
			'include'                   => 'all', //empty string(''), false, 0 don't work, and return empty array
			'exclude'                   => 'all', //empty string(''), false, 0 don't work, and return empty array
			'exclude_tree'              => 'all', //empty string(''), false, 0 don't work, and return empty array
			'number'                    => false, //can be 0, '0', '' too
			'offset'                    => '',
			'fields'                    => 'all',
			'name'                      => '',
			'slug'                      => '',
			'hierarchical'              => true, //can be 1, '1' too
			'search'                    => '',
			'name__like'                => '',
			'description__like'         => '',
			'pad_counts'                => false, //can be 0, '0', '' too
			'get'                       => '',
			'child_of'                  => false, //can be 0, '0', '' too
			'childless'                 => false,
			'cache_domain'              => 'core',
			'update_term_meta_cache'    => true, //can be 1, '1' too
			'meta_query'                => '',
			'meta_key'                  => array(),
			'meta_value'                => '',
			'show_option_none'          => '',
			'option_none_value'         => '',
		) );

		$args = wp_parse_args( $args, $default_args );

		$terms = get_terms( $args );

		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ); ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<select
					name="<?php echo esc_attr( $args['id'] ); ?><?php echo ( 'multiselect' === $args['type'] ) ? '[]' : ''; ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					<?php echo $args['multiselect'] === true ? 'multiple="multiple"' : ''; ?>
					>
					<?php
					if ( $args['show_option_none'] ) {
						?>
							<option value="<?php echo esc_attr( $args['option_none_value'] ); ?>"
								<?php
								if ( is_array( $args['value'] ) ) {
									selected( in_array( (string) $args['option_none_value'], $args['value'], true ), true );
								} else {
									selected( $args['value'], (string) $args['option_none_value'] );
								}
								?>
							><?php echo esc_html( $args['show_option_none'] ); ?></option>
						<?php
					}
					foreach ( $terms as $val ) {
						?>
						<option value="<?php echo esc_attr( $val->term_id ); ?>"
							<?php
							if ( is_array( $args['value'] ) ) {
								selected( in_array( (string) $val->term_id, $args['value'], true ), true );
							} else {
								selected( $args['value'], (string) $val->term_id );
							}
							?>
						><?php echo esc_html( $val->name ); ?></option>
						<?php
					}
					?>
				</select><?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	public static function dropdown_roles( $args = array() ) {
		$default_args = apply_filters( self::$prefix . '_dropdown_roles', array(
			'show_option_none'  => '',
			'option_none_value' => '',
		) );

		$args = wp_parse_args( $args, $default_args );

		$roles = array_reverse( get_editable_roles() );
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['title'] ) ?> <?php echo $args['desc_tip']; // WPCS: XSS ok. ?></label>
			</th>
			<td>
				<select
					name="<?php echo esc_attr( $args['id'] ); ?><?php echo ( $args['multiselect'] === true ) ? '[]' : ''; ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					style="<?php echo esc_attr( $args['css'] ); ?>"
					class="<?php echo esc_attr( $args['class'] ); ?>"
					<?php echo implode( ' ', $args['custom_attributes'] ); // WPCS: XSS ok. ?>
					<?php echo $args['multiselect'] === true ? 'multiple="multiple"' : ''; ?>
					>
					<?php
					if ( $args['show_option_none'] ) {
						?>
							<option value="<?php echo esc_attr( $args['option_none_value'] ); ?>"
								<?php
								if ( is_array( $args['value'] ) ) {
									selected( in_array( (string) $args['option_none_value'], $args['value'], true ), true );
								} else {
									selected( $args['value'], (string) $args['option_none_value'] );
								}
								?>
							><?php echo esc_html( $args['show_option_none'] ); ?></option>
						<?php
					}
					foreach ( $roles as $role => $role_name ) {
						?>
						<option value="<?php echo esc_attr( $role ); ?>"
							<?php
							if ( is_array( $args['value'] ) ) {
								selected( in_array( (string) $role, $args['value'], true ), true );
							} else {
								selected( $args['value'], (string) $role );
							}
							?>
						><?php echo esc_html( $role_name['name'] ); ?></option>
						<?php
					}
					?>
				</select><?php echo $args['desc']; // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Helper function to get the formatted description and tip HTML for a
	 * given form field. Plugins can call this when implementing their own custom
	 * settings types.
	 *
	 * @param  array $value The form field value array.
	 * @return array The description and tip as a 2 element array.
	 */
	public static function get_field_description( $args = array() ) {
		$description  = '';
		$tooltip_html = '';

		if ( true === $args['desc_tip'] ) {
			$tooltip_html = $args['desc'];
		} elseif ( ! empty( $args['desc_tip'] ) ) {
			$description  = $args['desc'];
			$tooltip_html = $args['desc_tip'];
		} elseif ( ! empty( $args['desc'] ) ) {
			$description = $args['desc'];
		}

		if ( $description && in_array( $args['type'], array( 'checkbox', 'radio', ), true ) ) {
			$description = '<span style="vertical-align:middle;">' . wp_kses_post( $description ) . '</span>';
		} elseif ( $description && in_array( $args['type'], array( 'checkboxgroup', ), true ) ) {
			$description = '<p>' . wp_kses_post( $description ) . '</p>';
		} else {
			$description = '<p class="description">' . wp_kses_post( $description ) . '</p>';
		}

		if ( $tooltip_html ) {
			$tooltip_html = '<span class="dashicons dashicons-info ' . self::$prefix . '-tippy-tooltip" data-tippy-content="' . esc_attr( $tooltip_html ) . '"></span>';
		}

		return array(
			'description'  => $description,
			'tooltip_html' => $tooltip_html,
		);
	}

}
