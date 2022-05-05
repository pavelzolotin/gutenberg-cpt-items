<?php
/**
 * Plugin Name:       CPT items block
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

function render_cpt_items_block($attributes) {

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	$args = array(
        'posts_per_page' => 8,
        'paged' => $paged,
		'post_status' => 'publish',
		'post_type'   => 'block-players',
	);

	$recent_posts = new WP_Query($args);

    ob_start();
?>
    <div class="wp-block-block-test-cpt-items">
        <div class="wp-block-block-test-cpt-items__container">
            <?php if ( $recent_posts->have_posts() ):
		        while($recent_posts->have_posts() ): $recent_posts->the_post();
			    $post_id = get_the_ID();
			    $title = get_the_title();
			    $title = $title ? $title : __('(No title)','cpt-items');
			    $permalink = get_permalink( );
			    $excerpt = get_the_excerpt(  );
			    $thumb = get_the_post_thumbnail( $post_id, 'full' );
			    $cur_terms = get_the_terms( $post_id, 'block-game' );
		    ?>
                <div class="wp-block-block-test-cpt-items__card">
                    <div class="wp-block-block-test-cpt-items__card-img-wrapper">
                        <a href="<?php echo esc_url($permalink) ?>">
                            <?php echo $thumb; ?>
                        </a>
                    </div>
                    <h5 class="wp-block-block-test-cpt-items__card-title">
                        <a href="<?php echo esc_url($permalink) ?>">
                            <?php echo esc_html($title); ?>
                        </a>
                    </h5>
                    <p class="wp-block-block-test-cpt-items__card-text"><?php echo esc_html( $excerpt ) ?></p>
                    <div class="wp-block-block-test-cpt-items__card-tags">
                        <?php if(is_array( $cur_terms )): ?>
                            <?php foreach( $cur_terms as $cur_term ): ?>
                                <a class="wp-block-block-test-cpt-items__card-tags--tag" href="<?php echo esc_url(get_term_link( $cur_term->term_id, $cur_term->taxonomy )) ?>"><?php echo esc_attr($cur_term->name) ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
		    <?php endwhile; ?>
        </div>
        <?php
		    wp_reset_postdata();
		    endif;
		?>
        <div class="wp-block-block-test-cpt-items__pagination">
            <?php
                $big = 100;

                echo paginate_links( array(
                    'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'current' => max( 1, get_query_var('paged') ),
                    'total'   => $recent_posts->max_num_pages
                ) );
            ?>
        </div>
    </div>
    <?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

function cpt_items_block_init() {
	register_block_type( __DIR__ , array (
        'render_callback' => 'render_cpt_items_block'
    ));
}
add_action( 'init', 'cpt_items_block_init' );


add_action( 'init', 'cpt_items_create_taxonomy' );
function cpt_items_create_taxonomy(){

	register_taxonomy( 'block-game',  'block-players', [
		'label'                 => 'Game',
		'labels'                => [
			'name'              => 'Games',
			'singular_name'     => 'Game',
			'search_items'      => 'Search Games',
			'all_items'         => 'All Games',
			'view_item '        => 'View Game',
			'parent_item'       => 'Parent Game',
			'parent_item_colon' => 'Parent Game:',
			'edit_item'         => 'Edit Game',
			'update_item'       => 'Update Game',
			'add_new_item'      => 'Add New Game',
			'new_item_name'     => 'New Game Name',
			'menu_name'         => 'Game',
			'back_to_items'     => '← Back to Game',
		],
		'description'           => '',
		'public'                => true,
		'hierarchical'          => false,

		'rewrite' => array('slug' => 'block-game'),
		'show_in_rest' => true,
		'capabilities'          => array(),
		'meta_box_cb'           => null,
		'show_admin_column'     => false,
		'rest_base'             => null,
	] );
}

function cpt_items_create_posttype() {

    register_post_type( 'block-players',
        array(
            'labels' => array(
                'name' => __( 'Players' ),
                'singular_name' => __( 'Player' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'block-players'),
            'show_in_rest' => true,
			'supports' => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', ],
			'taxonomies'          => ['block-game'],
          )
    );
}

add_action( 'init', 'cpt_items_create_posttype', 0 );