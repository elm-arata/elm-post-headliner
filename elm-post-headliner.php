<?php
/*
Plugin Name: ELM Post Headliner
Plugin URI: http://www.element-system.co.jp
Description: 記事のヘッドライン表示用ショートコードを提供します。Usage: [headliner]
Author: Yuki Arata
Version: 0.1
Author URI: http://www.element-system.co.jp
License: GPLv2 or later
*/

/*
Copyright 2012 Yuki Arata (webmaster@element-system.co.jp)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class ElmPostHeadliner
{
	private $ver = '0.1';

	protected $defaults = array(
		'query'           => '',
		'post_type'       => 'post',
		'posts_per_page'  => '5',
		'container_tag'   => 'ul',
		'container_id'    => '',
		'container_class' => 'headliner-container',
		'item_tag'        => 'li',
		'item_class'      => 'headliner-item',
		'date_format'     => 'Y/m/d',
	);

	public function __construct()
	{
		add_shortcode('headliner', array($this, 'shortcode'));
		add_action('wp_head', array($this, 'action_wp_head'));
	}

	public function action_wp_head()
	{
		$url = plugins_url("", __FILE__).'/style.css?ver='.$this->ver;
		printf(
		    '<link rel="stylesheet" type="text/css" media="all" href="%s" />'."\n"
		    , apply_filters("elm-post-headliner-css-url", $url)
		);
	}



	/**
	 *
	 * @param mixed $atts
	 */
	public function shortcode( $atts ) {
		if ( isset( $atts[0] ) && !isset( $atts['query'] ) ) {
			if ( (int)$atts[0] > 0 ) {
				$atts['posts_per_page'] = (string)$atts[0];
			} else {
				$atts['post_type'] = $atts[0];
			}
		}
		// query set
		if ( empty( $atts['query'] ) ) {
			$atts['query'] = sprintf( 'post_type=%s&posts_per_page=%d'
			                        , $atts['post_type'], $atts['posts_per_page'] );
		}
		extract( shortcode_atts( $this->defaults, $atts ) );

		$loop = new WP_Query( $query );

		// output
		if ( false === $loop->have_posts() )
			return '';

		$buff = '';
		$buff .= sprintf(
			'<%s id="%s" class="%s">'
			, $container_tag
			, $container_id
			, $container_class);
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$buff .= sprintf(
				'<%s class="%s">'
				, $item_tag
				, $item_class);
			$buff .= sprintf('<span class="hearliner-item-date">%s</span>', get_the_time($date_format) );
			$buff .= sprintf('<a href="%s" class="headliner-link">%s</a>', get_permalink(), get_the_title() );
			$buff .= sprintf('</%s>', $item_tag);
		}//endwhile
		$buff .= sprintf('</%s>', $container_tag);
		wp_reset_postdata();
		return $buff;
	}

}//endclass
new ElmPostHeadliner();
