<?php
class lymosWidgets{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('widgets_init', [$this, 'widgetInit']);
	}

	public function widgetInit(){
		// Arguments used in all register_sidebar() calls.
		$shared_args = array(
			'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
			'after_title'   => '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></div>',
		);

		// Footer #1.
		register_sidebar(
			array_merge(
				$shared_args,
				array(
					'name'        => __( 'Footer #1', 'lymos-theme' ),
					'id'          => 'footer-1',
					'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'lymos-theme' ),
				)
			)
		);

		// Footer #2.
		register_sidebar(
			array_merge(
				$shared_args,
				array(
					'name'        => __( 'Footer #2', 'lymos-theme' ),
					'id'          => 'footer-2',
					'description' => __( 'Widgets in this area will be displayed in the second column in the footer.', 'lymos-theme' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$shared_args,
				array(
					'name'        => __( 'mytest', 'lymos-theme' ),
					'id'          => 'mytest-1',
					'description' => __( 'my test', 'lymos-theme' ),
				)
			)
		);
	}
}