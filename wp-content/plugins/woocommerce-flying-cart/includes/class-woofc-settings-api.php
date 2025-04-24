<?php
/**
 * WeCreativez settings API.
 *
 * @author  WeCreativez
 * @package WeCreativez/Core
 * @version 1.0.10
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WOOFC_Settings_API' ) ) {

	/**
	 * Admin setting class.
	 *
	 * @package WeCreativez
	 * @version 1.0.0
	 */
	class WOOFC_Settings_API {

		/**
		 * Contains plugin prefix.
		 *
		 * @since 1.0.5
		 *
		 * @var string
		 */
		public static $prefix = 'woofc_';

		/**
		 * Initialize settings
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function init() {
			add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
			add_filter( self::$prefix . 'field_callback', array( __CLASS__, 'field_callback' ), 10, 2 );
			add_filter( self::$prefix . 'field_sanitize', array( __CLASS__, 'field_sanitize' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
			add_action( 'admin_head', array( __CLASS__, 'style' ) );
			add_action( 'admin_footer', array( __CLASS__, 'script' ) );
		}

		/**
		 * Register settings.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function register_settings() {

			foreach ( self::get_settings() as $section ) {
				if ( ! isset( $section['id'] ) || empty( $section['id'] ) ) {
					continue;
				}

				$section_id       = $section['id'];
				$section_title    = isset( $section['title'] ) ? $section['title'] : '';
				$section_callback = isset( $section['description'] ) ? $section['description'] : '';
				$section_page     = isset( $section['page'] ) ? $section['page'] : $section_id;
				$section_fields   = isset( $section['fields'] ) ? $section['fields'] : array();

				// Add settings section.
				add_settings_section(
					$section_id,
					$section_title,
					function () use ( $section_callback ) {
						echo wp_kses_post( $section_callback );
					},
					$section_page
				);

				if ( ! $section_fields ) {
					continue;
				}

				foreach ( $section_fields as $field ) {
					if ( ( ! isset( $field['id'] ) && ! isset( $field['name'] ) ) ) {
						continue;
					}
					if ( isset( $field['visibility'] ) && false === $field['visibility'] ) {
						continue;
					}

					$field_id       = isset( $field['name'] ) ? $field['name'] : $field['id'];
					$field_title    = isset( $field['title'] ) ? $field['title'] : '';
					$field_type     = isset( $field['type'] ) ? $field['type'] : 'text';
					$field_default  = isset( $field['default'] ) ? $field['default'] : '';
					$field_callback = isset( $field['callback'] ) ? $field['callback'] : $field_type;
					$field_sanitize = isset( $field['sanitize'] ) ? $field['sanitize'] : '';
					$field_register = isset( $field['register'] ) ? $field['register'] : true ;


					// Tooltip
					$field_title .= self::field_tooltip( $field );

					// Fiels separator.
					if ( 'separator' === $field_type ) {
						$field_title    = '<hr>';
						$field_register = false;
					}

					/**
					 * Allow third party plugins to add custom field callback.
					 *
					 * @Filtered: self::field_callback  10
					 *
					 * @since 1.0.0
					 *
					 * @param mixed $field_callback.
					 */
					$field_callback = apply_filters( self::$prefix . 'field_callback', $field_callback, $field );

					/**
					 * Allow third party plugins to add custom sanitization callback.
					 *
					 * @Filtered: self::field_sanitize  10
					 *
					 * @since 1.0.0
					 *
					 * @param mixed $field_sanitize.
					 * @param array $field.
					 */
					$field['sanitize_callback'] = apply_filters( self::$prefix . 'field_sanitize', $field_sanitize, $field );

					$field['label_for'] = $field_id;
					$field['class']     = $field_id  . ' ' . self::$prefix . 'row';

					if ( isset( $field['show_if'] ) && is_array( $field['show_if'] ) ) {
						if ( isset( $field['show_if']['id'] ) ) {
							$field['class'] .=  ' ' . self::$prefix . 'show_if--' . $field['show_if']['id'] . '--' . $field['show_if']['value'] . '--end';
						}
					} else if ( isset( $field['hide_if'] ) && is_array( $field['hide_if'] ) ) {
						if ( isset( $field['hide_if']['id'] ) ) {
							$field['class'] .=  ' ' . self::$prefix . 'hide_if--' . $field['hide_if']['id'] . '--' . $field['hide_if']['value'] . '--end';
						}
					}

					// Add setting field.
					add_settings_field( $field_id, $field_title, $field_callback, $section_page, $section_id, $field );

					/**
					 * Unset type offset, so it will not act as register_setting $args['type'].
					 *
					 * @see https://developer.wordpress.org/reference/functions/register_setting/
					 */
					unset( $field['type'] );

					// Do not register setting if register set to false.
					if ( false === $field_register ) {
						continue;
					} else if ( is_array( $field_register ) ) { // Custom register setting args.
						foreach ( $field_register as $fr ) {
							if ( ! isset( $fr['id'] ) || empty( $fr['id'] ) ) {
								continue;
							}

							$fr_sanitize             = isset( $fr['sanitize'] ) ? $fr['sanitize'] : '';
							$fr['sanitize_callback'] = apply_filters( self::$prefix . 'field_sanitize', $fr_sanitize, $fr );

							register_setting( $section_page, $fr['id'], $fr );
						}

						continue;
					}

					// Register setting.
					register_setting( $section_page, $field_id, $field );
				}
			}
		}

		/**
		 * Get all the registered settings.
		 *
		 * @return array
		 */
		public static function get_settings() {
			/**
			 * Filter register setting.
			 *
			 * Allow add sections and fields.
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings
			 */
			return apply_filters( self::$prefix . 'register_settings', array() );
		}

		/**
		 * Add options.
		 *
		 * @since 1.0.0
		 */
		public static function add_options() {
			foreach ( self::get_settings() as $setting ) {
				if ( ! isset( $setting['fields'] ) || empty( $setting['fields'] ) || ! is_array( $setting['fields'] ) ) {
					continue;
				}

				foreach ( $setting['fields'] as $field ) {
					$field_id       = isset( $field['name'] ) ? $field['name'] : $field['id'];
					$field_default  = isset( $field['default'] ) ? $field['default'] : '';

					if ( isset( $field['register'] ) && false === $field['register'] ) {
						continue;
					}

					if ( isset( $field['type'] ) && 'separator' === $field['type'] ) {
						continue;
					}

					if ( isset( $field['register'] ) && is_array( $field['register'] ) ) {
						foreach ( $field['register'] as $fr ) {
							if ( ! isset( $fr['id'] ) || empty( $fr['id'] ) ) {
								continue;
							}

							add_option( $fr['id'], isset( $fr['default'] ) ? $fr['default'] : '' );
						}

						continue;
					} else if ( ! get_option( $field_id ) ) {
						add_option( $field_id, $field_default );
					}

					do_action( self::$prefix . 'add_options', $field_id, $field, $setting );
				}
			}
		}

		/**
		 * Reset options.
		 *
		 * @since 1.0.5
		 */
		public static function reset_options() {
			foreach ( self::get_settings() as $setting ) {
				if ( ! isset( $setting['fields'] ) || empty( $setting['fields'] ) || ! is_array( $setting['fields'] ) ) {
					continue;
				}

				foreach ( $setting['fields'] as $field ) {

					$field_id      = isset( $field['name'] ) ? $field['name'] : $field['id'];
					$field_default = isset( $field['default'] ) ? $field['default'] : '';

					if ( isset( $field['register'] ) && false === $field['register'] ) {
						continue;
					}

					if ( isset( $field['type'] ) && 'separator' === $field['type'] ) {
						continue;
					}

					if ( isset( $field['register'] ) && is_array( $field['register'] ) ) {
						foreach ( $field['register'] as $fr ) {
							if ( ! isset( $fr['id'] ) || empty( $fr['id'] ) ) {
								continue;
							}

							update_option( $fr['id'], isset( $fr['default'] ) ? $fr['default'] : '' );
						}

						continue;
					} else {
						update_option( $field_id, $field_default );
					}

					do_action( self::$prefix . 'reset_options', $field_id, $field, $setting );
				}
			}
		}

		/**
		 * Render text field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_text( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			printf(
				'<input type="text" id="%1$s" class="regular-text %2$s" name="%1$s" value="%3$s" %4$s />',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_attr( $value ),
				$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render hidden field.
		 *
		 * @since 1.0.6
		 *
		 * @param  array $args Field arguments.
		 * @return void
		 */
		public static function field_hidden( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			printf(
				'<input type="hidden" id="%1$s" class="regular-text %2$s" name="%1$s" value="%3$s" %4$s />',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_attr( $value ),
				$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render number field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_number( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$min         = isset( $args['min'] ) ? 'min=' . $args['min'] . '' : '';
			$max         = isset( $args['max'] ) ? 'max=' . $args['max'] . '' : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			printf(
				'<input type="number" id="%1$s" class="%2$s" name="%1$s" value="%3$s" %4$s %5$s %6$s />',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_attr( $value ),
				esc_attr( $min ),
				esc_attr( $max ),
				$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render text field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_url( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			printf(
				'<input type="text" id="%1$s" class="regular-text %2$s" name="%1$s" value="%3$s" %4$s />',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_url( $value ),
				$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render file field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_file( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			echo '<div class="' . self::$prefix . 'upload-file">';

			printf(
				'<input type="text" id="%1$s" class="regular-text %2$s" name="%1$s" value="%3$s" %4$s />',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_url( $value ),
				$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo '<input type="button" class="button" value="Upload File">';

			echo '</div>';

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render textarea field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_textarea( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$rows        = isset( $args['rows'] ) ? $args['rows'] : 6;
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			printf(
				'<textarea id="%1$s" class="regular-text %2$s" name="%1$s" rows="%5$s" %4$s>%3$s</textarea>',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_textarea( $value ),
				$custom_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				absint( $rows ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render color field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_color( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			$classes .= ' ' . self::$prefix . 'color-picker';

			printf(
				'<input type="text" id="%1$s" class="%2$s" name="%1$s" value="%3$s" %4$s />',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_attr( $value ),
				$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render select field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_select( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$multiple    = isset( $args['multiple'] ) ? 'multiple' : false;
			$options     = isset( $args['options'] ) ? $args['options'] : array();
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );

			$name = $multiple ? $name . '[]' : $name;

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<select id="' . esc_attr( $name ) . '" class="' . esc_attr( $classes ) . '" name="' . esc_attr( $name ) . '" ' . $multiple . ' ' . $custom_attr . '>';

			foreach ( $options as $option_value => $option_text ) {
				if ( 'wp_query' === $option_value || 'wp_roles' === $option_value || 'wp_user_query' === $option_value ) {
					continue;
				}

				if ( $multiple ) {
					$selected = in_array( $option_value, $value, true ) ? 'selected' : '';
				} else {
					$selected = selected( $option_value, $value, false );
				}

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<option value="' . esc_attr( $option_value ) . '" ' . $selected . ' >' . esc_html( $option_text ) . '</option>';
			}

			// WP_Query.
			if ( isset( $options['wp_query'] ) ) {
				$query = new WP_Query( $options['wp_query'] );

				if ( $query->get_posts() ) {
					foreach ( $query->get_posts() as $post ) {
						if ( $multiple ) {
							$selected = in_array( $post->ID, $value ) ? 'selected' : ''; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict, WordPress.PHP.StrictInArray.MissingTrueStrict
						} else {
							$selected = selected( $post->ID, $value, false );
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<option value="' . esc_attr( $post->ID ) . '" ' . $selected . ' >' . esc_html( $post->post_title ) . ' (ID: ' . absint( $post->ID ) . ')</option>';
					}
				}
			} elseif ( isset( $options['wp_roles'] ) ) { // WP_Roles.
				$roles = new WP_Roles();

				if ( $roles->roles ) {
					foreach ( $roles->roles as $role_key => $role ) {
						if ( $multiple ) {
							$selected = in_array( $role_key, $value ) ? 'selected' : ''; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						} else {
							$selected = selected( $role_key, $value, false );
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<option value="' . esc_attr( $role_key ) . '" ' . $selected . ' >' . esc_html( $role['name'] ) . '</option>';
					}
				}
			} elseif ( isset( $options['wp_user_query'] ) ) { // WP_User_Query.
				$query = new WP_User_Query( $options['wp_user_query'] );

				if ( $query->get_results() ) {
					foreach ( $query->get_results() as $user ) {
						if ( $multiple ) {
							$selected = in_array( $user->ID, $value ) ? 'selected' : ''; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						} else {
							$selected = selected( $user->ID, $value, false );
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wp_sprintf(
							'<option value="%1$s" %4$s>%2$s (#%1$s - %3$s)</option>',
							absint( $user->ID ),
							esc_html( $user->display_name ),
							esc_html( $user->user_email ),
							$selected
						);
					}
				}
			}

			echo '</select>';

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render checkbox field.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return void
		 */
		public static function field_checkbox( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$value       = self::get_option( $args );
			$checked     = checked( 'yes', $value, false );

			printf(
				'<label><input type="checkbox" id="%1$s" class="%2$s" name="%1$s" value="yes" %4$s %5$s /> %6$s</label>',
				esc_attr( $name ),
				esc_attr( $classes ),
				esc_attr( $value ),
				$custom_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$checked, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				self::field_description( $args ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}

		/**
		 * Render radio group field.
		 *
		 * @since 1.0.2
		 *
		 * @param  array $args Field arguments.
		 * @return void
		 */
		public static function field_radio( $args ) {
			$name        = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
			$custom_attr = self::custom_field_attributes( $args );
			$options     = isset( $args['options'] ) ? $args['options'] : array();
			$value       = self::get_option( $args );

			if ( $options ) {
				echo '<fieldset>';
				echo '<p>';

				foreach ( $options as $option_value => $option_key ) {
					printf(
						'<label><input id="%1$s" name="%1$s" type="radio" value="%3$s" %4$s %5$s > %6$s</label><br>',
						esc_attr( $name ),
						esc_attr( $classes ),
						esc_attr( $option_value ),
						$custom_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						checked( $value, $option_value, false ),
						esc_html( $option_key )
					);
				}

				echo '</p>';

				echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				echo '</fieldset>';
			}

		}

		/**
		 * Render WP Editor field.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public static function field_wp_editor( $args ) {
			$name    = isset( $args['name'] ) ? $args['name'] : $args['id'];
			$classes = isset( $args['classes'] ) ? $args['classes'] : '';
			$width   = isset( $args['width'] ) ? $args['width'] : 800;
			$height  = isset( $args['height'] ) ? $args['height'] : 200;
			$value   = self::get_option( $args );

			echo '<div class="' . esc_attr( $classes ) . '" style="max-width: ' . absint( $width ) . 'px">';
			wp_editor(
				$value,
				$name,
				array(
					'textarea_name' => esc_attr( $name ),
					'editor_height' => absint( $height ),
				)
			);
			echo '</div>';

			echo self::field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render separator field.
		 *
		 * @since 1.0.8
		 *
		 * @param array $args Field arguments.
		 * @return void
		 */
		public static function field_separator( $args ) {
			echo '<hr>';
		}

		/**
		 * Filter field callback.
		 *
		 * @since 1.0.0
		 *
		 * @param  callback|string $field_callback  Callback function to display field.
		 * @return array
		 */
		public static function field_callback( $field_callback ) {

			switch ( $field_callback ) {
				case 'text':
					return array( __CLASS__, 'field_text' );

				case 'color':
					return array( __CLASS__, 'field_color' );

				case 'hidden':
					return array( __CLASS__, 'field_hidden' );

				case 'number':
					return array( __CLASS__, 'field_number' );

				case 'url':
					return array( __CLASS__, 'field_url' );

				case 'file':
					return array( __CLASS__, 'field_file' );

				case 'textarea':
					return array( __CLASS__, 'field_textarea' );

				case 'select':
					return array( __CLASS__, 'field_select' );

				case 'checkbox':
					return array( __CLASS__, 'field_checkbox' );

				case 'radio':
					return array( __CLASS__, 'field_radio' );

				case 'wp_editor':
					return array( __CLASS__, 'field_wp_editor' );

				case 'separator':
					return array( __CLASS__, 'field_separator' );
			}

			// If callback function not found or field type not availabe then display input text.
			if ( ! is_callable( $field_callback ) ) {
				return array( __CLASS__, 'field_text' );
			}

			return $field_callback;
		}

		/**
		 * Filter field sanitization.
		 *
		 * @since 1.0.0
		 *
		 * @param  callback $field_sanitize  Callback function to sanintize input.
		 * @param  array    $field           Field arguments.
		 * @return string
		 */
		public static function field_sanitize( $field_sanitize, $field ) {
			if ( ! isset( $field['type'] ) ) {
				return $field_sanitize;
			}

			if ( isset( $field['sanitize'] ) && ! empty( $field['sanitize'] ) && is_callable( $field['sanitize'] ) ) {
				return $field_sanitize;
			}

			$field_type = trim( $field['type'] );

			switch ( $field_type ) {
				case 'select' && isset( $field['multiple'] ) && true === $field['multiple']:
					return array( __CLASS__, 'sanitize_select_multiple' );

				case 'checkbox':
					return array( __CLASS__, 'sanitize_checkbox' );

				case 'text':
				case 'hidden':
				case 'number':
				case 'select':
				case 'radio':
					return 'sanitize_text_field';

				case 'url':
				case 'file':
					return 'esc_url_raw';

				case 'textarea':
					return 'sanitize_textarea_field';

				case 'color':
					return 'sanitize_hex_color';

				case 'wp_editor':
					return 'wp_kses_post';
			}

			return $field_sanitize;
		}

		/**
		 * Display settings page.
		 *
		 * @since 1.0.0
		 *
		 * @param  string $setting  Registred sections or pages.
		 * @return void
		 */
		public static function do_settings( $setting ) {
			settings_fields( $setting );
			do_settings_sections( $setting );
		}

		/**
		 * Sanitize checkbox field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $input Value send by WordPress options.php for sanitize.
		 * @return string
		 */
		public static function sanitize_checkbox( $input ) {
			return 'yes' === $input ? 'yes' : 'no';
		}

		/**
		 * Sanitizes select multiple field.
		 *
		 * @param array $input Value send by WordPress options.php for sanitize.
		 * @return array $input Sanitized value.
		 */
		public static function sanitize_select_multiple( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}

			return array_map( 'sanitize_text_field', $input );
		}

		/**
		 * Show field description.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return string
		 */
		public static function field_description( $args ) {
			$html = '';

			if ( isset( $args['desc'] ) ) {
				if ( isset( $args['type'] ) && 'checkbox' === $args['type'] ) {
					$html .= wp_kses_post( $args['desc'] );
				}

				if ( is_array( $args['desc'] ) ) {
					foreach ( $args['desc'] as $desc ) {
						$html .= wp_sprintf( '<p class="description">%s</p>', wp_kses_post( $desc ) );
					}
				}

				if ( ! is_array( $args['desc'] ) && isset( $args['type'] ) && 'checkbox' !== $args['type'] ) {
					$html .= wp_sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) );
				}
			}

			return $html;
		}

		/**
		 * Show field tooltip.
		 *
		 * @since 1.0.9
		 *
		 * @param array $args Field arguments.
		 * @return string Tooltip html.
		 */
		public static function field_tooltip( $args ) {
			$html = '';
			$icon = '<svg xmlns="http://www.w3.org/2000/svg" class="' . esc_attr( self::$prefix ) . 'tooltip_icon" viewBox="0 0 20 20" fill="currentColor">
				<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
			</svg>';

			if ( isset( $args['tooltip'] ) ) {
				$html .= wp_sprintf(
					'<span class="%stooltip" data-tooltip="%s">%s</span>',
					esc_attr( self::$prefix ),
					wp_kses_post( $args['tooltip'] ),
					$icon
				);
			}

			return $html;
		}

		/**
		 *  Custom field attributes and custom inline style.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args  Field arguments.
		 * @return string
		 */
		public static function custom_field_attributes( $args ) {
			$attributes = array();

			// Custom attributes.
			if ( isset( $args['attributes'] ) && ! empty( $args['attributes'] ) && is_array( $args['attributes'] ) ) {
				foreach ( $args['attributes'] as $attribute => $attribute_value ) {
					$attributes[] = esc_html( $attribute ) . '="' . esc_html( $attribute_value ) . '"';
				}
			}

			// Style.
			if ( isset( $args['style'] ) && ! empty( $args['style'] ) && is_array( $args['style'] ) ) {
				$inline_css = 'style="';

				foreach ( $args['style'] as $style => $style_value ) {
					$inline_css .= esc_attr( $style ) . ':' . esc_attr( $style_value ) . ';';
				}

				$inline_css .= '"';

				$attributes[] = $inline_css;
			}

			// Required.
			if ( isset( $args['required'] ) && true === $args['required'] ) {
				$attributes[] = 'required';
			}

			return implode( ' ', $attributes );
		}

		/**
		 * Get option for fields.
		 *
		 * @since 1.0.0
		 *
		 * @param  array  $args       Field arguments.
		 * @param  string $default    Default return type.
		 * @return mixed
		 */
		public static function get_option( $args, $default = '' ) {
			if ( is_array( $args ) ) {
				if ( isset( $args['value'] ) && ! empty( $args['value'] ) ) {
					return $args['value'];
				}

				if ( isset( $args['name'] ) && ! empty( $args['name'] ) ) {
					return get_option( $args['name'] );
				}

				if ( isset( $args['id'] ) && ! empty( $args['id'] ) ) {
					return get_option( $args['id'] );
				}
			} else {
				return get_option( $args, $default );
			}

			return $default;
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		public static function enqueue_scripts() {
			// Engueue media.
			wp_enqueue_media();

			// Engueue color picker.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		/**
		 * Style.
		 *
		 * @since 1.0.9
		 *
		 * @return void
		 */
		public static function style() {
			?>
			<style>
				<?php
					echo '.' . esc_html( self::$prefix ) . 'row th {
						width: 240px;
						position: relative;
						padding-right: 24px;
					}';

					echo '.' . esc_html( self::$prefix ) . 'row .' . esc_html( self::$prefix ) . 'tooltip {
						width: 18px;
						height: 18px;
						vertical-align: middle;
						position: absolute;
						right: -2px;
						top: 16px;
						color: #757575;
						padding: 6px;
					}';

					echo '.' . esc_html( self::$prefix ) . 'row .' . esc_html( self::$prefix ) . 'tooltip:hover:after {
						content: attr(data-tooltip);
						background: #23282d;
						color: #ffffff;
						padding: 10px 20px;
						border-radius: 6px;
						position: absolute;
						bottom: 26px;
						left: -90px;
						width: 160px;
						text-align: center;
						font-size: 13px;
						box-shadow: 0 20px 30px rgb(0 0 0 / 20%);
						z-index: 999999;
					}';

					echo '@media ( max-width:782px ) {
						.' . esc_html( self::$prefix ) . 'row .' . esc_html( self::$prefix ) . 'tooltip:hover:after {
							left: -182px;
						}
					}';
				?>
			</style>
			<?php
		}

		/**
		 * Script.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		public static function script() {
			?>
			<script>
				( function( $ ) {
					'use strict';

					$( document ).ready( function() {

						var prefix = '<?php echo esc_attr( self::$prefix ); ?>';

						// Init WordPress color picker
						jQuery( '.' + prefix + 'color-picker' ).wpColorPicker();

						// Upload image.
						jQuery( document ).on( 'click', '.' + prefix + 'upload-file [type=button]', function( event ) {
							event.preventDefault();

							var btn = jQuery( this );

							// Create the media frame.
							var file_frame = wp.media.frames.file_frame = wp.media( {
								title: btn.data('uploader_title'),
								button: {
									text: btn.data('uploader_button_text'),
								},
								multiple: false
							} );

							file_frame.on( 'select', function() {
								var attachment = file_frame.state().get( 'selection' ).first().toJSON();

								btn.closest( '.' + prefix + 'upload-file' ).find( '[type=text]' ).val( attachment.url ).change();
							} );

							// Finally, open the modal
							file_frame.open();
						} );

						// Show and hide option.
						var <?php echo self::$prefix; ?>option_object = {
							init: function() {
								this.show_if();

								jQuery( 'form[action="options.php"] :input' )
									.on( 'change keypress keyup keydown', this.show_if )
									.on( 'change keypress keyup keydown', this.hide_if );

								jQuery( 'form[action="options.php"] [type="radio"]' )
									.on( 'click', this.show_if )
									.on( 'click', this.hide_if );

								jQuery( 'form[action="options.php"] [type="checkbox"]' )
									.on( 'click', this.show_if )
									.on( 'click', this.hide_if );
							},

							show_if: function() {
								jQuery( '[class*="<?php echo self::$prefix ?>show_if--"]' ).each( function( i, e ) {
									jQuery( this ).hide();

									var _string = jQuery( this ).attr( 'class' ).match(/<?php echo esc_attr( self::$prefix ); ?>show_if--.+--end/);

									if ( _string[0] ) {
										var string = _string[0].split( '--' );

										if ( string ) {
											var selector       = string[1];
											var value          = string[2]
											var selector_type  = jQuery( '#' + selector ).prop( 'nodeName' );

											if ( 'SELECT' === selector_type || 'TEXTAREA' === selector_type ) {
												var selector_value = jQuery( '#' + selector ).val();
											} else if ( 'INPUT' === selector_type ) {
												var selecter_input_type = jQuery( '#' + selector ).attr( 'type' );

												if ( 'checkbox' === selecter_input_type || 'radio' === selecter_input_type ) {
													var selector_value = jQuery( '#' + selector + ':checked' ).val();
												} else {
													var selector_value = jQuery( '#' + selector ).val();
												}
											}

											if ( selector_value === value ) {
												jQuery( this ).show();
											}
										}
									}
								} );
							},

							hide_if: function() {
								jQuery( '[class*="<?php echo self::$prefix ?>hide_if--"]' ).each( function( i, e ) {
									jQuery( this ).show();

									var _string = jQuery( this ).attr( 'class' ).match(/<?php echo esc_attr( self::$prefix ); ?>hide_if--.+--end/);

									if ( _string[0] ) {
										var string = _string[0].split( '--' );

										if ( string ) {
											var selector       = string[1];
											var value          = string[2]
											var selector_type  = jQuery( '#' + selector ).prop( 'nodeName' );

											if ( 'SELECT' === selector_type || 'TEXTAREA' === selector_type ) {
												var selector_value = jQuery( '#' + selector ).val();
											} else if ( 'INPUT' === selector_type ) {
												var selecter_input_type = jQuery( '#' + selector ).attr( 'type' );

												if ( 'checkbox' === selecter_input_type || 'radio' === selecter_input_type ) {
													var selector_value = jQuery( '#' + selector + ':checked' ).val();
												} else {
													var selector_value = jQuery( '#' + selector ).val();
												}
											}

											if ( selector_value === value ) {
												jQuery( this ).hide();
											}
										}
									}
								} );
							}
						}

						<?php echo self::$prefix; ?>option_object.init();

					} ); // Doc.ready End.

				} )( jQuery );
			</script>
			<?php
		}

		/**
		 * Render form field.
		 *
		 * @since 1.0.6
		 *
		 * @param array $args Field arguments.
		 */
		public static function form( $args ) {
			echo '<table class="form-table">';

			foreach ( $args as $a ) {
				if ( isset( $a['type'] ) && 'hidden' === $a['type'] ) {
					self::field_hidden( $a );
					continue;
				}

				echo '<tr>';
				echo '<th scope="row">';
				echo '<label for="' . esc_attr( $a['id'] ) . '">' . esc_html( $a['title'] ) . '</label>';
				echo '</th>';
				echo '<td>';

				switch ( $a['type'] ) {
					case 'text':
						self::field_text( $a );
						break;

					case 'hidden':
						self::field_hidden( $a );
						break;

					case 'number':
						self::field_number( $a );
						break;

					case 'url':
						self::field_url( $a );
						break;

					case 'file':
						self::field_file( $a );
						break;

					case 'textarea':
						self::field_textarea( $a );
						break;

					case 'select':
						self::field_select( $a );
						break;

					case 'checkbox':
						self::field_checkbox( $a );
						break;

					case 'radio':
						self::field_radio( $a );
						break;

					case 'wp_editor':
						self::field_wp_editor( $a );
						break;
				}

				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
		}
	}
}
