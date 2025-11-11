( function () {
	'use strict';

	const config = window.SpectreElementorIconsConfig || {};
	const libraries = config.libraries || {};
	const observers = [];

	const libraryPromises = {};
	const iconCache = {};

	const hasLibraries = Object.keys( libraries ).length > 0;

	const addLoadedClass = ( element ) => {
		element.classList.add( 'spectre-icon--rendered' );
	};

	const loadLibrary = ( libraryId ) => {
		if ( libraryPromises[ libraryId ] ) {
			return libraryPromises[ libraryId ];
		}

		const settings = libraries[ libraryId ];
		if ( ! settings || ! settings.json ) {
			return Promise.resolve( {} );
		}

		libraryPromises[ libraryId ] = fetch( settings.json, { credentials: 'same-origin' } )
			.then( ( response ) => ( response.ok ? response.json() : {} ) )
			.then( ( payload ) => ( payload && payload.icons ? payload.icons : payload ) )
			.catch( () => ( {} ) );

		return libraryPromises[ libraryId ];
	};

	const injectSvg = ( element, svgString ) => {
		if ( ! svgString ) {
			return;
		}

		element.innerHTML = svgString;
		addLoadedClass( element );
	};

	const renderIcon = ( element, libraryId ) => {
		const settings = libraries[ libraryId ];

		if ( ! settings ) {
			return;
		}

		const slugClass = Array.from( element.classList ).find( ( className ) =>
			className.startsWith( settings.prefix )
		);

		if ( ! slugClass ) {
			return;
		}

		const slug = slugClass.replace( settings.prefix, '' );

		if ( ! slug || element.dataset.spectreIconProcessed === libraryId ) {
			return;
		}

		element.dataset.spectreIconProcessed = libraryId;

		const cacheKey = `${ libraryId }::${ slug }`;

		if ( iconCache[ cacheKey ] ) {
			injectSvg( element, iconCache[ cacheKey ] );
			return;
		}

		loadLibrary( libraryId ).then( ( icons ) => {
			iconCache[ cacheKey ] = icons[ slug ] || '';
			injectSvg( element, iconCache[ cacheKey ] );
		} );
	};

	const processElement = ( node ) => {
		if ( node.nodeType !== Node.ELEMENT_NODE ) {
			return;
		}

		Object.keys( libraries ).forEach( ( libraryId ) => {
			const settings = libraries[ libraryId ];

			if ( ! settings ) {
				return;
			}

			if ( node.matches && node.matches( settings.selector ) ) {
				renderIcon( node, libraryId );
			}

			const matches = node.querySelectorAll ? node.querySelectorAll( settings.selector ) : [];
			matches.forEach( ( match ) => renderIcon( match, libraryId ) );
		} );
	};

	const startObserver = () => {
		const observer = new MutationObserver( ( mutations ) => {
			mutations.forEach( ( mutation ) => {
				mutation.addedNodes.forEach( processElement );
			} );
		} );

		observer.observe( document.body, { childList: true, subtree: true } );
		observers.push( observer );
	};

	const init = () => {
		if ( ! hasLibraries ) {
			return;
		}

		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', init );
			return;
		}

		startObserver();

		// Process any existing nodes (e.g., icon control default preview).
		processElement( document.body );
	};

	init();
} )();
