<?php
Redux::set_section( Minimog_Redux::OPTION_NAME, array(
	'title'      => esc_html__( 'Performance', 'minimog' ),
	'id'         => 'performance',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'          => 'disable_emoji',
			'type'        => 'switch',
			'title'       => esc_html__( 'Disable Emojis', 'minimog' ),
			'description' => 'Remove WordPress Emojis functionality.',
			'default'     => true,
		),
		array(
			'id'          => 'disable_embeds',
			'type'        => 'switch',
			'title'       => esc_html__( 'Disable Embeds', 'minimog' ),
			'description' => 'Remove WordPress Embeds functionality.',
			'default'     => true,
		),
	),
) );
