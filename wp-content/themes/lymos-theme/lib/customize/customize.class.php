<?php
class lymosCustomize{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('customize_register', [$this, 'register']);
	}

	public function register($wp_customize){

		$wp_customize->add_panel(
			'global',
			[
				'title'      => __( 'Global', 'lymostheme' ),
				'priority'   => 22
			]
		);


		$wp_customize->add_section(
			'colors',
			[
				'title'      => __( 'Colors', 'lymostheme' ),
				'priority'   => 10,
				'panel' => 'global',
				'capability' => 'edit_theme_options',
			]
		);

		$wp_customize->add_setting(
				'background_color',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
			//		'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

		$wp_customize->add_control(
				new WP_Customize_Color_Control( $wp_customize, 'colors', array(
			        'label'    => __( 'Background Color', 'lymostheme' ),
			        'section'  => 'colors',
			        'settings' => 'background_color',
			    ) ) 
			);



		$wp_customize->add_section(
			'container_set',
			[
				'title'      => __( 'Container', 'lymostheme' ),
				'priority'   => 20,
				'panel' => 'global',
				'capability' => 'edit_theme_options',
			]
		);

		
		$wp_customize->add_setting(
				'container-value',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
			//		'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

		$wp_customize->add_control(
				'container-value',
				array(
					'label' => __('Layout', 'lymostheme'),
					'description' => __('layout page width', 'lymostheme'),
					'type'     => 'select',
				//	'default'  => 'container',
					'section'  => 'container_set',
					'priority' => 50,
					'choices'  => array(
						'container' => __( 'Container', 'lymostheme' ),
						'full-width'         => __( 'Full Width', 'lymostheme' ),
					)
				),
			);


		// header
		$wp_customize->add_panel( 'header-build' , array(
	        'title'    => __( 'Header Build', 'lymostheme' ),
	        'priority' => 26
	    ) ); 
	    $wp_customize->add_section( 'topbar' , array(
	        'title'    => __( 'Top Bar', 'lymostheme' ),
	        'panel' => 'header-build',
	        'priority' => 9
	    ));   

	    $wp_customize->add_setting( 'topbar-content' , array(
	        'default'   => '',
	        'transport' => 'refresh',
	    ) );
	    $wp_customize->add_control('topbar-content', [
	    	'label' => __('Topbar Content', 'lymostheme'),
	    	'type' => 'text',
	    	'section' => 'topbar'
	    ]);
		$wp_customize->add_section( 'header-title-logo' , array(
	        'title'    => __( 'Site Title & Logo', 'lymostheme' ),
	        'panel' => 'header-build',
	        'priority' => 10
	    ));   

	    $wp_customize->add_setting( 'title-logo-layout' , array(
	        'default'   => '',
	        'transport' => 'refresh',
	    ) );
	    $wp_customize->add_control('title-logo-layout', [
	    	'label' => __('Title&Logo Layout', 'lymostheme'),
	    	'type' => 'select',
	    	'section' => 'header-title-logo',
			'choices' => array( // Optional.
				'vertical' => __('Vertical'),
				'horizonal' => __('Horizonal'),
			 )
	    ]);

	    $wp_customize->add_setting( 'site-logo' , array(
	        'default'   => '',
	        'transport' => 'refresh',
	    ) );
	    $wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize, 'site-logo',
		   array(
		      'label' => __( 'Site Logo', 'lymostheme' ),
		      'section' => 'header-title-logo',
		      'button_labels' => array( // Optional.
		         'select' => __( 'Select Image' , 'lymostheme' ),
		         'change' => __( 'Change Image', 'lymostheme'  ),
		         'remove' => __( 'Remove' , 'lymostheme' ),
		         'default' => __( 'Default', 'lymostheme'  ),
		         'placeholder' => __( 'No image selected', 'lymostheme'  ),
		         'frame_title' => __( 'Select Image', 'lymostheme'  ),
		         'frame_button' => __( 'Choose Image', 'lymostheme'  ),
		      )
		   )
		));

		$wp_customize->add_section( 'header-style' , array(
	        'title'    => __( 'Header Style', 'lymostheme' ),
	        'panel' => 'header-build',
	        'priority' => 20
	    ));   

	    $wp_customize->add_setting( 'header_background_color' , array(
	        'default'   => '',
	        'transport' => 'refresh',
	    ) );
	    $wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'header_background_color', array(
	        'label'    => __( 'Header Background Color', 'lymostheme' ),
	        'section'  => 'header-style',
	    ) ) );
	    $wp_customize->add_setting( 'header-sticky' , array(
	        'default'   => '',
	        'transport' => 'refresh',
	    ) );
	    $wp_customize->add_control('header-sticky',
		   array(
		      'label' => __( 'Header Sticky', 'lymostheme' ),
		      'section'  => 'header-style',
		      'type'=> 'checkbox',
		));

	    // footer
	    $wp_customize->add_panel('footer-build',array(
	        'title'     => 'Footer Build',
	        'priority'  => 30
	    ) );

		$wp_customize->add_section('footer-style',array(
	        'title'     => 'Footer Style',
	        'panel' => 'footer-build',
	        'priority'  => 30
	    ) );
	    $wp_customize->add_setting( 'footer-background-color', array(
	        'default'   => '',
	        "transport" => "refresh",
	    ) );
	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer-background-color', array(
	         'label'     => __('Background Color', 'lymostheme'),
	         'section'   => 'footer-style'
	    ) ) );

	    $wp_customize->add_section('footer-widget',array(
	        'title'     => 'Footer Widget',
	        'panel' => 'footer-build',
	        'priority'  => 30
	    ) );
	    $wp_customize->add_setting( 'footer-widget-1', array(
	        'default'   => '',
	        "transport" => "refresh",
	    ) );
	    $wp_customize->add_control( 'footer-widget-1',
		   array(
		      'label' => __( 'Widget 1', 'lymostheme' ),
		      'section' => 'footer-widget',
		      'priority' => 10, // Optional. Order priority to load the control. Default: 10
		      'type' => 'select',
		      'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
		      'choices' => array( // Optional.
		         'wordpress' => __( 'WordPress' ),
		         'hamsters' => __( 'Hamsters' ),
		      )
		   )
		);
		$wp_customize->add_setting( 'footer-widget-2', array(
	        'default'   => '',
	        "transport" => "refresh",
	    ) );
	    $wp_customize->add_control( 'footer-widget-2',
		   array(
		      'label' => __( 'Widget 2', 'lymostheme' ),
		      'section' => 'footer-widget',
		      'priority' => 20, // Optional. Order priority to load the control. Default: 10
		      'type' => 'select',
		      'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
		      'choices' => array( // Optional.
		         'wordpress' => __( 'WordPress' ),
		         'hamsters' => __( 'Hamsters' ),
		      )
		   )
		);
		$wp_customize->add_setting( 'footer-widget-3', array(
	        'default'   => '',
	        "transport" => "refresh",
	    ) );
	    $wp_customize->add_control( 'footer-widget-3',
		   array(
		      'label' => __( 'Widget 3', 'lymostheme' ),
		      'section' => 'footer-widget',
		      'priority' => 30, // Optional. Order priority to load the control. Default: 10
		      'type' => 'select',
		      'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
		      'choices' => array( // Optional.
		         'wordpress' => __( 'WordPress' ),
		         'hamsters' => __( 'Hamsters' ),
		      )
		   )
		);
		$wp_customize->add_setting( 'footer-widget-4', array(
	        'default'   => '',
	        "transport" => "refresh",
	    ) );
	    $wp_customize->add_control( 'footer-widget-4',
		   array(
		      'label' => __( 'Widget 4', 'lymostheme' ),
		      'section' => 'footer-widget',
		      'priority' => 40, // Optional. Order priority to load the control. Default: 10
		      'type' => 'select',
		      'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
		      'choices' => array( // Optional.
		         'wordpress' => __( 'WordPress' ),
		         'hamsters' => __( 'Hamsters' ),
		      )
		   )
		);


	}
}