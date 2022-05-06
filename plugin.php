<?php
/**
 * Plugin Name:       CPT items
 * Description:       Dynamic block which display custom post types.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Author
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cpt-items
 *
 * @package           block-test
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */

function cpt_items_render_cpt_items_block( $attributes ) {

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
        'posts_per_page' => 8,
        'paged' => $paged,
		'post_status' => 'publish',
		'post_type' => 'cpt_players',
	);

	$recent_posts = new WP_Query( $args );

    $posts = '<div class="wp-block-block-test-cpt-items">';
        $posts .= '<div class="wp-block-block-test-cpt-items__container">';

            if ($recent_posts->have_posts()) {
                while( $recent_posts->have_posts() ): $recent_posts->the_post();

                    $post_id = get_the_ID();
                    $title = get_the_title();
                    $title = $title ? $title : __( '(No title)','cpt-items' );
                    $permalink = get_permalink();
                    $excerpt = get_the_excerpt();
                    $thumb = get_the_post_thumbnail( $post_id, 'full' );
                    $cur_terms = get_the_terms( $post_id, 'cpt_game' );

                    $posts .= '<div class="wp-block-block-test-cpt-items__card">';
                        $posts .= '<div class="wp-block-block-test-cpt-items__card__image">';
                            $posts .= '<a href="' . esc_url($permalink) . '">' . $thumb . '</a>';
                        $posts .= '</div>';
                        $posts .= '<h5 class="wp-block-block-test-cpt-items__card-title">';
                            $posts .= '<a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a>';
                        $posts .= '</h5>';
                        $posts .= '<p class="wp-block-block-test-cpt-items__card-text">' . esc_html( $excerpt ) . '</p>';
                        $posts .= '<div class="wp-block-block-test-cpt-items__card-tags">';
                            if(is_array( $cur_terms )):
                                foreach( $cur_terms as $cur_term ):
                                    $posts .= '<a class="wp-block-block-test-cpt-items__card-tag" . href="' . esc_url( get_term_link( $cur_term->term_id, $cur_term->taxonomy ) ) . '">' . esc_html( $cur_term->name ) . '</a>';
                                endforeach;
                            endif;
                        $posts .= '</div>';
                    $posts .= '</div>';
                endwhile;
            } else {
                $posts .= '<p>' . __( 'No players', 'cpt-items' ) . '</p>';
            }
        $posts .= '</div>';
        $posts .= '<div class="wp-block-block-test-cpt-items__pagination">';
            $posts .= paginate_links( array(
                'base'    => str_replace( 100, '%#%', esc_url( get_pagenum_link( 100 ) ) ),
                'current' => max( 1, get_query_var( 'paged' ) ),
                'total'   => $recent_posts->max_num_pages
            ) );

        $posts .= '</div>';
    $posts .= '</div>';

    wp_reset_postdata();
    return $posts;

}

function cpt_items_block_init() {
	register_block_type( __DIR__,  array(
		'render_callback' => 'cpt_items_render_cpt_items_block'
	) );
}

add_action( 'init', 'cpt_items_block_init' );