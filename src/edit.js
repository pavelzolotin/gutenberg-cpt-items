/* eslint-disable jsx-a11y/anchor-is-valid */
import { __ } from '@wordpress/i18n';
import { RawHTML } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { Spinner, Placeholder } from '@wordpress/components';
import './editor.scss';

export default function Edit() {
	const posts = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'cpt_players', {
			per_page: 8,
			_embed: true,
		} );
	}, [] );

	return (
		<div { ...useBlockProps() }>
			{ posts && posts.length ? (
				<>
					<div className="wp-block-gb-block-gutenberg-cpt-items__container">
						{ posts.map( ( post ) => {
							const terms =
								post._embedded &&
								post._embedded[ 'wp:term' ] &&
								post._embedded[ 'wp:term' ].length > 0 &&
								post._embedded[ 'wp:term' ][ 0 ];

							const featuredImage =
								post._embedded &&
								post._embedded[ 'wp:featuredmedia' ] &&
								post._embedded[ 'wp:featuredmedia' ].length >
									0 &&
								post._embedded[ 'wp:featuredmedia' ][ 0 ];
							return (
								<div
									className="wp-block-gb-block-gutenberg-cpt-items__card"
									key={ post.id }
								>
									<div className="wp-block-gb-block-gutenberg-cpt-items__card__image">
										<a href={ post.link }>
											{ featuredImage && (
												<img
													src={
														featuredImage.source_url
													}
													alt={
														featuredImage.alt_text
													}
												/>
											) }
										</a>
									</div>
									<h5 className="wp-block-gb-block-gutenberg-cpt-items__card-title">
										<a href={ post.link }>
											{ post.title.rendered ? (
												<RawHTML>
													{ post.title.rendered }
												</RawHTML>
											) : (
												__(
													'(No title',
													'gutenberg-cpt-items'
												)
											) }
										</a>
									</h5>
									<div className="wp-block-gb-block-gutenberg-cpt-items__card-text">
										{ post.excerpt.rendered && (
											<RawHTML>
												{ post.excerpt.rendered }
											</RawHTML>
										) }
									</div>
									<div className="wp-block-gb-block-gutenberg-cpt-items__card-tags">
										{ terms &&
											terms.map( ( cat ) => {
												return (
													<>
														<a
															className="wp-block-gb-block-gutenberg-cpt-items__card-tag"
															href={ cat.link }
														>
															#{ cat.name }
														</a>
													</>
												);
											} ) }
									</div>
								</div>
							);
						} ) }
					</div>
				</>
			) : (
				<>
					{ posts ? (
						<p>
							{ __(
								'Sorry, no players found',
								'gutenberg-cpt-items'
							) }
						</p>
					) : (
						<Placeholder
							icon="dashicons-games"
							label={ __(
								'Loading Players',
								'gutenberg-cpt-items'
							) }
						>
							<Spinner />
						</Placeholder>
					) }
				</>
			) }
		</div>
	);
}
