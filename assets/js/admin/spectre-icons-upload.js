( function () {
	'use strict';

	var config   = window.SpectreIconsUpload || {};
	var ajaxUrl  = config.ajaxUrl || '';
	var nonce    = config.nonce || '';
	var i18n     = config.i18n || {};
	var limit    = config.limit === null || config.limit === undefined ? null : ( parseInt( config.limit, 10 ) || null );
	var count    = parseInt( config.count, 10 ) || 0;

	var dropZone  = document.getElementById( 'spectre-icons-drop-zone' );
	var fileInput = document.getElementById( 'spectre-icons-file-input' );
	var grid      = document.getElementById( 'spectre-icons-grid' );
	var messages  = document.getElementById( 'spectre-icons-messages' );
	var countEl   = document.querySelector( '.spectre-icons-upload-count' );
	var statusEl  = document.querySelector( '.spectre-icons-upload-status' );

	if ( ! dropZone || ! fileInput || ! ajaxUrl ) {
		return;
	}

	// ---- Helpers --------------------------------------------------------

	function escapeHtml( str ) {
		return String( str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' );
	}

	function showMessage( text, type ) {
		if ( ! messages ) {
			return;
		}
		var msg = document.createElement( 'div' );
		msg.className = 'spectre-icons-message spectre-icons-message--' + type;
		msg.textContent = text;
		messages.appendChild( msg );
		setTimeout( function () {
			if ( msg.parentNode ) {
				msg.parentNode.removeChild( msg );
			}
		}, 5000 );
	}

	function updateCount( newCount, newLimit ) {
		count = parseInt( newCount, 10 ) || 0;
		if ( newLimit !== undefined ) {
			limit = newLimit === null ? null : ( parseInt( newLimit, 10 ) || null );
		}

		if ( countEl ) {
			countEl.textContent = limit === null
				? count + ' icons'
				: count + ' / ' + limit + ' icons';
			countEl.dataset.count = count;
			countEl.dataset.limit = limit === null ? '' : limit;
		}

		var atLimit = limit !== null && count >= limit;
		dropZone.classList.toggle( 'spectre-icons-drop-zone--disabled', atLimit );
		fileInput.disabled = atLimit;

		var badge = statusEl ? statusEl.querySelector( '.spectre-icons-limit-badge' ) : null;
		if ( atLimit && ! badge && statusEl ) {
			badge = document.createElement( 'span' );
			badge.className = 'spectre-icons-limit-badge';
			badge.textContent = i18n.limitReached || 'Limit reached';
			statusEl.appendChild( badge );
		} else if ( ! atLimit && badge ) {
			badge.parentNode.removeChild( badge );
		}
	}

	function addTile( slug, svg ) {
		if ( ! grid ) {
			return;
		}

		// Remove the empty-state paragraph if present.
		var empty = grid.querySelector( '.spectre-icons-empty-state' );
		if ( empty && empty.parentNode ) {
			empty.parentNode.removeChild( empty );
		}
		grid.classList.remove( 'spectre-icons-grid--empty' );

		var tile = document.createElement( 'div' );
		tile.className = 'spectre-icons-tile';
		tile.dataset.slug = slug;
		tile.innerHTML =
			'<div class="spectre-icons-tile__preview">' + svg + '</div>' +
			'<div class="spectre-icons-tile__label">' + escapeHtml( slug ) + '</div>' +
			'<button type="button" class="spectre-icons-tile__delete" data-slug="' + escapeHtml( slug ) + '" aria-label="Remove ' + escapeHtml( slug ) + '">' +
				'<span class="dashicons dashicons-trash" aria-hidden="true"></span>' +
			'</button>';

		grid.appendChild( tile );
	}

	function removeTile( slug ) {
		if ( ! grid ) {
			return;
		}

		var selector = '[data-slug="' + slug.replace( /"/g, '\\"' ) + '"]';
		var tile     = grid.querySelector( selector );
		if ( tile && tile.parentNode ) {
			tile.parentNode.removeChild( tile );
		}

		if ( grid.querySelectorAll( '.spectre-icons-tile' ).length === 0 ) {
			grid.classList.add( 'spectre-icons-grid--empty' );
			var p = document.createElement( 'p' );
			p.className = 'spectre-icons-empty-state';
			p.textContent = 'No icons uploaded yet. Upload an SVG file to get started.';
			grid.appendChild( p );
		}
	}

	// ---- Upload ---------------------------------------------------------

	function uploadFile( file ) {
		if ( ! file.name.toLowerCase().endsWith( '.svg' ) ) {
			showMessage( i18n.svgOnly || 'Only SVG files are supported.', 'error' );
			return;
		}

		if ( file.size > 512 * 1024 ) {
			showMessage( i18n.fileTooLarge || 'File must be smaller than 512 KB.', 'error' );
			return;
		}

		if ( limit !== null && count >= limit ) {
			showMessage( i18n.limitReached || 'Icon limit reached.', 'error' );
			return;
		}

		var data = new FormData();
		data.append( 'action', 'spectre_icons_upload_icon' );
		data.append( 'nonce', nonce );
		data.append( 'svg_file', file );

		fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data } )
			.then( function ( res ) { return res.json(); } )
			.then( function ( body ) {
				if ( body.success ) {
					addTile( body.data.slug, body.data.svg );
					updateCount( body.data.count, body.data.limit );
					showMessage( body.data.slug + ' uploaded.', 'success' );
				} else {
					var msg = ( body.data && body.data.message ) ? body.data.message : ( i18n.uploadError || 'Upload failed.' );
					showMessage( msg, 'error' );
				}
			} )
			.catch( function () {
				showMessage( i18n.uploadError || 'Upload failed. Please try again.', 'error' );
			} );
	}

	// ---- Delete ---------------------------------------------------------

	function deleteIcon( slug ) {
		if ( ! window.confirm( i18n.confirmDelete || 'Remove this icon from your library?' ) ) {
			return;
		}

		var data = new FormData();
		data.append( 'action', 'spectre_icons_delete_icon' );
		data.append( 'nonce', nonce );
		data.append( 'slug', slug );

		fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data } )
			.then( function ( res ) { return res.json(); } )
			.then( function ( body ) {
				if ( body.success ) {
					removeTile( slug );
					updateCount( body.data.count, body.data.limit );
				} else {
					var msg = ( body.data && body.data.message ) ? body.data.message : ( i18n.deleteError || 'Delete failed.' );
					showMessage( msg, 'error' );
				}
			} )
			.catch( function () {
				showMessage( i18n.deleteError || 'Delete failed. Please try again.', 'error' );
			} );
	}

	// ---- Event listeners ------------------------------------------------

	fileInput.addEventListener( 'change', function ( e ) {
		var files = Array.prototype.slice.call( e.target.files || [] );
		files.forEach( uploadFile );
		e.target.value = '';
	} );

	dropZone.addEventListener( 'dragover', function ( e ) {
		e.preventDefault();
		dropZone.classList.add( 'spectre-icons-drop-zone--active' );
	} );

	dropZone.addEventListener( 'dragleave', function () {
		dropZone.classList.remove( 'spectre-icons-drop-zone--active' );
	} );

	dropZone.addEventListener( 'drop', function ( e ) {
		e.preventDefault();
		dropZone.classList.remove( 'spectre-icons-drop-zone--active' );

		if ( fileInput.disabled ) {
			return;
		}

		var files = Array.prototype.slice.call( ( e.dataTransfer && e.dataTransfer.files ) || [] );
		files.forEach( uploadFile );
	} );

	if ( grid ) {
		grid.addEventListener( 'click', function ( e ) {
			var btn  = e.target.closest( '.spectre-icons-tile__delete' );
			if ( ! btn ) {
				return;
			}
			var slug = btn.dataset.slug;
			if ( slug ) {
				deleteIcon( slug );
			}
		} );
	}
} )();
