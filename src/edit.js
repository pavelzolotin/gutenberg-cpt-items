import { __ } from '@wordpress/i18n';
import { RawHTML } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import './editor.scss';

export default function Edit() {
	const posts = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'block-players', {
			per_page: 8,
			_embed: true,
		} );
	}, [] );

	return (
		<>
			<div { ...useBlockProps() }>
				<ul>
					{ posts &&
						posts.map( ( post ) => {
							const featuredImage =
								post._embedded &&
								post._embedded[ 'wp:featuredmedia' ] &&
								post._embedded[ 'wp:featuredmedia' ].length >
									0 &&
								post._embedded[ 'wp:featuredmedia' ][ 0 ];
							return (
								<li key={ post.id }>
									{ featuredImage && (
										<img
											src={ featuredImage.source_url }
											alt={ featuredImage.alt_text }
										/>
									) }
									<h5>
										<a href={ post.link }>
											{ post.title.rendered ? (
												<RawHTML>
													{ post.title.rendered }
												</RawHTML>
											) : (
												__( '(No title', 'post-types' )
											) }
										</a>
									</h5>
									{ post.excerpt.rendered && (
										<RawHTML>
											{ post.excerpt.rendered }
										</RawHTML>
									) }
								</li>
							);
						} ) }
				</ul>
			</div>
		</>
	);
}
