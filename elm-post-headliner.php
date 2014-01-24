<?php
/*
Plugin Name: ELM Post Headliner
Plugin URI: http://www.element-system.co.jp
Description: 記事のヘッドライン表示用ショートコードを提供します。Usage: [headliner]
Author: Yuki Arata
Version: 1.0
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
	private $ver = 1.0;

	protected $defaults = array(
		// Query Parameters
		'query'           => '',
		'post_type'       => 'post',
		'category_name'   => '',
		'posts_per_page'  => '5',
		'container_tag'   => 'ul',
		'container_id'    => '',
		'container_class' => 'headliner-container',
		'item_tag'        => 'li',
		'item_class'      => 'headliner-item',
		'item_inner_class' => 'headliner-item-inner',
		'date_format'     => 'Y/m/d',
		'thumbnail'       => 'none', // 'show' or 'none'
		'size'            => 'thumbnail',
		'excerpt'         => 'none', // 'show' or 'none'
		'template_function' => '',
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
		$param = shortcode_atts( $this->defaults, $atts );

		// query set
		if ( empty( $param['query'] ) ) {
			$tmp = array(
				'post_type' => $param['post_type'],
				'posts_per_page' => $param['posts_per_page'],
				'category_name' => $param['category_name'],
			);
			$param['query'] = http_build_query($tmp, '', '&');
		} else {
			// Decode what encoded by WordPress. "&" <---> "&#038;"
			$param['query'] = preg_replace('/&#038;/', '&', $param['query']);
		}

		$loop = new WP_Query( $param['query'] );

		// output
		if ( false === $loop->have_posts() )
			return '';

		//thumbnail size
		if (preg_match('/^[0-9]+,[0-9]+$/', $param['size'])) {
			$thumb_size = explode(',', $param['size']);
			$thumb_size[0] = (int)$thumb_size[0];
			$thumb_size[1] = (int)$thumb_size[1];
		} else {
			$thumb_size = $param['size'];
		}


		$buff = '';
		$buff .= sprintf(
			'<%s id="%s" class="%s">'
			, $param['container_tag']
			, $param['container_id']
			, $param['container_class']);
		$item_template = $this->get_template_item($param['thumbnail']);
		$item_template = apply_filters('elm-post-headliner-item-template', $item_template);
		if ($param['template_function'] && function_exists($param['template_function'])) {
			$item_template = $param['template_function']($item_template);
		}
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$tmp = $item_template;
			$tmp = str_replace('%item_tag%', $param['item_tag'], $tmp);
			$tmp = str_replace('%item_class%', $param['item_class'], $tmp);
			$tmp = str_replace('%item_inner_class%', $param['item_inner_class'], $tmp);
			$tmp = str_replace('%post_date%', get_the_time($param['date_format']), $tmp);
			$tmp = str_replace('%post_url%', get_permalink(), $tmp);
			$tmp = str_replace('%post_title%', get_the_title(), $tmp);

			if ($param['thumbnail'] == 'show' && $thumb_id = get_post_thumbnail_id()) {
				$src = wp_get_attachment_image_src($thumb_id, $thumb_size);
				$img = sprintf(
					'<a href="%s" class="headliner-item-thumb"><img src="%s" alt="%s" title="%s" /></a>'
					, get_permalink()
					, esc_attr($src[0])
					, esc_attr(get_the_title())
					, esc_attr(get_the_title())
				);
				$tmp = str_replace('%post_thumbnail%', $img, $tmp);
			} else {
				$tmp = str_replace('%post_thumbnail%', '', $tmp);
			}

			if ($param['excerpt'] == 'show') {
				$excerpt = sprintf(
					'<span class="headliner-item-excerpt">%s</span>'
					, get_the_excerpt()
				);
				$tmp = str_replace('%post_excerpt%', $excerpt, $tmp);
			} else {
				$tmp = str_replace('%post_excerpt%', '', $tmp);
			}

			$buff .= $tmp;
		}//endwhile
		$buff .= sprintf('</%s>', $param['container_tag']);
		wp_reset_postdata();
		return $buff;
	}


	function get_template_item()
	{
		$html = '';
		$html .= '<%item_tag% class="%item_class%">';
			$html .= '<div class="%item_inner_class%">';
		$html .= '<span class="headliner-item-date">%post_date%</span>';
		$html .= '<a href="%post_url%" class="headliner-link">%post_title%</a>';
		$html .= '%post_thumbnail%';
		$html .= '%post_excerpt%';
			$html .= '</div>';
		$html .= '</%item_tag%>';
		return apply_filters('elm-post-headliner-template-item', $html);
	}

}//endclass
new ElmPostHeadliner();
