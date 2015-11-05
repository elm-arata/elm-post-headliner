<?php
/*
Plugin Name: ELM Post Headliner
Plugin URI: https://bitbucket.org/elmadmin/elm-post-headliner
Description: 記事のヘッドライン表示用ショートコードを提供します。Usage: [headliner] <a href="https://bitbucket.org/elmadmin/elm-post-headliner">&raquo;詳しい説明</a>
Author: Yuki Arata
Version: 1.5.2
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
	private $ver = '1.5.2';

	protected $defaults = array(
		// Query Parameters
		'query'           => '',// Special !!!
		'post_type'       => 'post',
		'category_name'   => '',
		'posts_per_page'  => 5,
		'offset'          => 0,
		'author'          => '',
		'order'           => 'DESC',
		'orderby'         => 'date',
		'ignore_sticky_posts' => false,
		// Taxonomy Parameters
		'tax'                  => null,
		'tax_field'            => 'slug',
		'tax_terms'            => null,
		'tax_include_children' => true,
		// Output Settings
		'id'                => '',
		'class'             => 'headliner-container',
		'container_tag'     => 'ul',
		'item_tag'          => 'li',
		'item_class'        => 'headliner-item',
		'item_inner_class'  => 'headliner-item-inner',
		'date_format'       => 'Y/m/d',
		'thumbnail'         => 'none',
		'size'              => 'thumbnail',
		'no_image'          => '',
		'excerpt'           => 'none',
		'new_label_days'    => 0,
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
				$atts['posts_per_page'] = (int)$atts[0];
			} else {
				$atts['post_type'] = $atts[0];
			}
		}
		$param = shortcode_atts( $this->defaults, $atts );
		// boolean型パラメータを型変換
		if ( isset( $param['ignore_sticky_posts'] ) && is_string( $param['ignore_sticky_posts'] ) ) {
			$param['ignore_sticky_posts'] = ($param['ignore_sticky_posts']==='true')?true:false;
		}
		if ( isset( $param['tax_include_children'] ) && is_string( $param['tax_include_children'] ) ) {
			$param['tax_include_children'] = ($param['tax_include_children']==='false')?false:true;
		}

		$args = array();

		// query set
		if ( empty( $param['query'] ) ) {
			$args = array(
				'post_type'      => $param['post_type'],
				'posts_per_page' => $param['posts_per_page'],
				'offset'         => $param['offset'],
				'category_name'  => $param['category_name'],
				'author'         => $param['author'],
				'order'          => $param['order'],
				'orderby'        => $param['orderby'],
				'ignore_sticky_posts' => $param['ignore_sticky_posts'],
			);
			if ( $param['tax'] && $param['tax_terms'] ) {
				$tax_query = array(
					'taxonomy' => $param['tax'],
					'field' => $param['tax_field'],
					'terms' => explode( ',', $param['tax_terms'].''),
					'include_children' => $param['tax_include_children'],
				);
				// if ( count( $tax_query['terms'] ) <= 1 ) {
				// 	$tax_query['terms'] = $param['tax_terms'];
				// }
				$args['tax_query'] = array($tax_query);
			}
		} else {
			// Decode what encoded by WordPress. "&" <---> "&#038;"
			$args = preg_replace('/&#038;/', '&', $param['query']);
		}

		$loop = new WP_Query( $args );

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
			, $param['id']
			, $param['class']);

		$item_template = $this->get_template_item();
		$item_template = apply_filters('elm-post-headliner-template', $item_template, $param);

		while ( $loop->have_posts() ) {
			$loop->the_post();
			$tmp = $item_template;
			$tmp = apply_filters('elm-post-headliner-textreplace', $tmp, $param, $loop->post);
			$tmp = str_replace('%item_tag%', $param['item_tag'], $tmp);
			$tmp = str_replace('%item_class%', $param['item_class'], $tmp);
			$tmp = str_replace('%item_inner_class%', $param['item_inner_class'], $tmp);
			$tmp = str_replace('%post_date%', get_the_time($param['date_format']), $tmp);
			$tmp = str_replace('%post_url%', get_permalink(), $tmp);
			$tmp = str_replace('%post_title%', get_the_title(), $tmp);
			$tmp = str_replace('%author%', get_the_author(), $tmp);
			$tmp = str_replace('%author_link%', get_the_author_link(), $tmp);

			if ($param['thumbnail'] == 'show' && $thumb_id = get_post_thumbnail_id()) {
				// サムネイル表示指定あり、サムネイル実在する場合
				$src = wp_get_attachment_image_src($thumb_id, $thumb_size);
				$img = sprintf(
					'<a href="%s" class="headliner-item-thumb"><img src="%s" alt="%s" title="%s" /></a>'
					, get_permalink()
					, esc_url($src[0])
					, esc_attr(get_the_title())
					, esc_attr(get_the_title())
				);
				$tmp = str_replace('%post_thumbnail%', $img, $tmp);
				$tmp = str_replace('%post_thumbnail_url%', $src[0], $tmp);
			} elseif ($param['thumbnail'] == 'show' && !empty($param['no_image']) ) {
				// サムネイル表示指定あり、サムネイルが存在せず、no_image指定がある場合
				$img = sprintf(
					'<a href="%s" class="headliner-item-thumb"><img src="%s" alt="%s" title="%s" /></a>'
					, get_permalink()
					, esc_url($param['no_image'])
					, esc_attr(get_the_title())
					, esc_attr(get_the_title())
				);
				$tmp = str_replace('%post_thumbnail%', $img, $tmp);
				$tmp = str_replace('%post_thumbnail_url%', esc_url($param['no_image']), $tmp);
			} else {
				$tmp = str_replace('%post_thumbnail%', '', $tmp);
				$tmp = str_replace('%post_thumbnail_url%', '', $tmp);
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

			//
			// Category (Taxonomy)
			//
			if ( !empty($param['tax']) ) {
				$categories = get_the_terms($loop->post, $param['tax']);
			} else {
				// $categories = get_the_terms($loop->post, 'category');
				$categories = get_the_category();
			}
			// replace
			if ( !empty($categories) && is_array($categories) ) {
				$cat = current($categories);
				$tmp = preg_replace('/%category_id%|%cat_ID%|%term_id%/', $cat->term_id, $tmp);
				$tmp = preg_replace('/%category_name%|%cat_name%|%term_name%/', $cat->name, $tmp);
				$tmp = preg_replace('/%category_nicename%|%category_slug%|%term_slug%/', $cat->slug, $tmp);
			} else {
				$tmp = preg_replace('/%category_id%|%term_id%/', '', $tmp);
				$tmp = preg_replace('/%category_name%|%cat_name%/', '', $tmp);
				$tmp = preg_replace('/%category_nicename%|%category_slug%|%term_slug%/', '', $tmp);
			}

			// author_meta情報 の置換処理
			if ( preg_match_all('/%author_meta\.([a-z0-9_]+)%/', $tmp, $author_meta_matches) ) {
				foreach ( $author_meta_matches[1] as $key=>$meta_field ) {
					$tmp = str_replace($author_meta_matches[0][$key], get_the_author_meta( $meta_field ), $tmp);
				}
			}

			// Newラベル付与
			if ($param['new_label_days'] > 0) {
				$now = time();
				$label_excerpt = get_the_time('U') + ($param['new_label_days'] * 24 * 3600);
				if ( $now < $label_excerpt ) {
					$tmp = str_replace('%new_label%', apply_filters('elm-post-headliner-new-label', '<span class="headliner-new-label label label-important">New</span>'), $tmp);
				}
			}
			$tmp = str_replace('%new_label%', '', $tmp);//未置換なら空文字に置換


			$buff .= $tmp;
		}//endwhile
		$buff .= sprintf('</%s>', $param['container_tag']);
		wp_reset_postdata();
		return $buff;
	}


	function get_template_item()
	{
		return file_get_contents(dirname(__FILE__).'/item.tpl');
	}

}//endclass
new ElmPostHeadliner();
