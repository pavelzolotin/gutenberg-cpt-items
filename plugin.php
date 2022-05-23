<?php
/**
 * Plugin Name:       Gutenberg CPT items
 * Description:       Dynamic block which display custom post types.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Author
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gutenberg-cpt-items
 *
 * @package           gb-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */

function gutenberg_cpt_items_render_recent_posts_block( $attributes ) {

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
        'posts_per_page' => 8,
        'paged' => $paged,
		'post_status' => 'publish',
		'post_type' => 'gutenberg_players',
	);

	$recent_posts = new WP_Query( $args );

    $posts = '<div class="wp-block-gb-block-gutenberg-cpt-items">';
        $posts .= '<div class="wp-block-gb-block-gutenberg-cpt-items__container">';
            if ($recent_posts->have_posts()) {
                while( $recent_posts->have_posts() ): $recent_posts->the_post();

                    $post_id = get_the_ID();
                    $title = get_the_title();
                    $title = $title ? $title : esc_html__( '(No title)','gutenberg-cpt-items' );
                    $permalink = get_permalink();
                    $excerpt = get_the_excerpt();
                    $thumb = get_the_post_thumbnail( $post_id, 'full' );
                    $terms = get_the_terms( $post_id, 'gutenberg_game' );
                    $media_meta = get_post_meta( $post_id, '_gb_sidebar_media_url_meta', true );

                    $posts .= '<div class="wp-block-gb-block-gutenberg-cpt-items__card">';
                        $posts .= '<div class="wp-block-gb-block-gutenberg-cpt-items__media">';
                            $posts .= '<a href="' . esc_url( $permalink ) . '">';
                                if( !empty( $media_meta ) ):
                                    $posts .= '<video src="' . esc_url( $media_meta ) .'" className="wp-block-gb-block-gutenberg-cpt-items__video" autoPlay muted loop></video>';
                                else:
                                    $posts .= $thumb;
                                endif;
                            $posts .= '</a>';
                        $posts .= '</div>';
                        $posts .= '<h5 class="wp-block-gb-block-gutenberg-cpt-items__card-title">';
                            $posts .= '<a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a>';
                        $posts .= '</h5>';
                        $posts .= '<p class="wp-block-gb-block-gutenberg-cpt-items__card-text">' . esc_html( $excerpt ) . '</p>';
                        if(is_array( $terms )):
                        $posts .= '<div class="wp-block-gb-block-gutenberg-cpt-items__card-tags">';
                            if(is_array( $terms )):
                                foreach( $terms as $term ):
                                    $posts .= '<a class="wp-block-gb-block-gutenberg-cpt-items__card-tag" href="' . esc_url( get_term_link( $term->term_id, $term->taxonomy ) ) . '">#' . esc_html( $term->name ) . '</a>';
                                endforeach;
                            endif;
                        $posts .= '</div>';
                    endif;
                    $posts .= '</div>';
                endwhile;
            } else {
                $posts .= '<p>' . esc_html__( 'No players', 'gutenberg-cpt-items' ) . '</p>';
            }
        $posts .= '</div>';
        if ( ($recent_posts->max_num_pages) >= 2 ) :
            $posts .= '<div class="wp-block-gb-block-gutenberg-cpt-items__pagination">';
                $posts .= paginate_links( array(
                    'base'    => str_replace( 100, '%#%', esc_url( get_pagenum_link( 100 ) ) ),
                    'current' => max( 1, get_query_var( 'paged' ) ),
                    'total'   => $recent_posts->max_num_pages
                ) );
            $posts .= '</div>';
        else:
            $posts .= paginate_links( array(
                'base'    => str_replace( 100, '%#%', esc_url( get_pagenum_link( 100 ) ) ),
                'current' => max( 1, get_query_var( 'paged' ) ),
                'total'   => $recent_posts->max_num_pages
            ) );
        endif;
    $posts .= '</div>';

    wp_reset_postdata();
    return $posts;

}

function gutenberg_cpt_items_block_init() {
	register_block_type( __DIR__,  array(
		'render_callback' => 'gutenberg_cpt_items_render_recent_posts_block'
	) );
}

add_action( 'init', 'gutenberg_cpt_items_block_init' );