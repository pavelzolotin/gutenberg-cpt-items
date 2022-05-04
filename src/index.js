import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import save from './save';

registerBlockType( 'block-test/post-types', {
	edit: Edit,
	save,
} );
