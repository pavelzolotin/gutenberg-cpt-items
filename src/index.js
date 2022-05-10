import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import save from './save';

registerBlockType( 'gb-block/gutenberg-cpt-items', {
	edit: Edit,
	save,
} );
