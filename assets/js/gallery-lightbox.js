(function () {
	'use strict';

	if ( ! window.LiteLight || typeof window.LiteLight.init !== 'function' ) {
		return;
	}

	window.LiteLight.init( {
		imageSelector: '.gallery .gallery-thumb a[data-lightbox]',
	} );
})();
