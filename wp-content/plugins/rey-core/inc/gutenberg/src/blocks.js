/**
 * WordPress dependencies
 */
 import {
	registerBlockType,
} from '@wordpress/blocks';

// Categories Helper
import { supportsCollections } from './utils/block-helpers';
import { getCategories, setCategories, registerBlockCollection } from '@wordpress/blocks';
import { SVG, Path } from '@wordpress/components';

// Register Blocks
import * as containerV1 from './blocks/container-v1';
import * as spacerV1 from './blocks/spacer-v1';

const blocksData = {
	slug: 'rey-blocks',
	title: 'REY BLOCKS',
	icon: <SVG height="24" width="24" viewBox="0 0 78 40" version="1.1" xmlns="http://www.w3.org/2000/svg">
		<Path d="M78,0.857908847 L68.673913,0.857908847 L63.5869565,15.1206434 L58.5,0.857908847 L49.173913,0.857908847 L59.4008152,24.9865952 L52.7086216,40 L62.0226252,40 L78,0.857908847 Z M8.47826087,5.63002681 L8.47826087,0.857908847 L0,0.857908847 L0,26.5951743 L8.47826087,26.5951743 L8.47826087,17.1045576 C8.47826087,12.922252 10.7038043,10.1340483 13.1413043,9.43699732 C14.6779891,9.0080429 16.2146739,8.95442359 17.8043478,9.43699732 L17.8043478,0 C13.0353261,0.321715818 10.2269022,1.93029491 8.47826087,5.63002681 Z M35.7146739,19.9463807 C34.7078804,19.9463807 33.701087,19.7855228 33.0652174,19.4101877 L48.1141304,10.2949062 C46.1535326,1.769437 39.6888587,0 36.0326087,0 C27.1834239,0 21.8315217,6.11260054 21.8315217,13.7265416 C21.8315217,21.3404826 27.1834239,27.4530831 36.0326087,27.4530831 C40.1127717,27.4530831 43.6100543,25.9517426 46.4184783,23.2171582 L42.0733696,17.4798928 C40.5366848,18.9276139 38.2581522,19.9463807 35.7146739,19.9463807 Z M36.0326087,7.50670241 C37.4103261,7.50670241 38.3641304,8.20375335 38.7880435,8.90080429 L29.9918478,14.2091153 C29.4619565,10.1876676 32.4293478,7.50670241 36.0326087,7.50670241 Z" fill="#CD2323" fillRule="nonzero"/>
	</SVG>
};

/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */
const registerBlock = ( block ) => {
	if ( ! block ) {
		return;
	}

	let { category } = block;

	const { name, settings } = block;

	category = blocksData.slug;

	registerBlockType( name, {
		category,
		...settings,
	} );

};

/**
 * Function to register blocks.
 */
export const registerReyBlocks = () => {

	setCategories( [
		...getCategories(),
		blocksData
	] );

	[
		containerV1,
		spacerV1,
	].forEach( registerBlock );
};

registerReyBlocks();
