/* eslint-disable jsx-a11y/anchor-is-valid */
import { __ } from '@wordpress/i18n';
import { RawHTML } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { Spinner, Placeholder } from '@wordpress/components';
import './editor.scss';

export default function Edit() {
	const postsPerPage = 8;

	const allPosts = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords(
			'postType',
			'gutenberg_players',
			{
				per_page: -1,
			}
		);
	}, [] );

	const posts = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords(
			'postType',
			'gutenberg_players',
			{
				per_page: postsPerPage,
				_embed: true,
			}
		);
	}, [] );

	const allNumPages =
		allPosts &&
		allPosts.length &&
		Math.ceil( allPosts.length / postsPerPage );

	const postNum = allPosts && allPosts.length;

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

							const featuredVideo =
								post.meta &&
								post.meta._gb_sidebar_media_url_meta;

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
									<div className="wp-block-gb-block-gutenberg-cpt-items__media">
										<a href={ post.link }>
											{ featuredVideo ? (
												<video
													src={ featuredVideo }
													className={
														featuredVideo.id
															? `wp-block-gb-block-gutenberg-cpt-items__video wp-video-${ featuredVideo.id }`
															: null
													}
													autoPlay
													muted
													loop
												/>
											) : (
												<>
													{ featuredImage && (
														<img
															className={
																featuredImage.id
																	? `wp-block-gb-block-gutenberg-cpt-items__img wp-image-${ featuredImage.id }`
																	: null
															}
															src={
																featuredImage.source_url
															}
															alt={
																featuredImage.alt_text
															}
														/>
													) }
												</>
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

					{ ( () => {
						if ( allNumPages > 4 ) {
							<>
								<div className="wp-block-gb-block-gutenberg-cpt-items__pagination">
									<span
										aria-current="page"
										className="page-numbers current"
									>
										1
									</span>
									<a className="page-numbers" href="#">
										2
									</a>
									<a className="page-numbers" href="#">
										3
									</a>
									<span className="page-numbers dots">…</span>
									<a className="page-numbers" href="#">
										{ allNumPages }
									</a>
									<a className="next page-numbers" href="#">
										{ __(
											'Next »',
											'gutenberg-cpt-items'
										) }
									</a>
								</div>
							</>;
						} else if ( postNum > 8 ) {
							return (
								<>
									<div className="wp-block-gb-block-gutenberg-cpt-items__pagination">
										<span
											aria-current="page"
											className="page-numbers current"
										>
											1
										</span>
										{ [ ...Array( allNumPages + 1 ) ].map(
											( el, index ) => {
												return (
													index > 1 && (
														<a
															key={ index }
															className="page-numbers"
															href="#"
														>
															{ index }
														</a>
													)
												);
											}
										) }
										<a
											className="next page-numbers"
											href="#"
										>
											{ __(
												'Next »',
												'gutenberg-cpt-items'
											) }
										</a>
									</div>
								</>
							);
						} else {
							return (
								<div className="wp-block-gb-block-gutenberg-cpt-items__pagination"></div>
							);
						}
					} )() }
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
