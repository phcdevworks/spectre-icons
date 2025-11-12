(function () {
	'use strict';

	const config = window.SpectreElementorIconsConfig || {};
	const libraries = config.libraries || {};

	const libraryPromises = {};
	const iconCache = {};

	const hasLibraries = Object.keys(libraries).length > 0;

	if (!hasLibraries) {
		return;
	}

	const loadLibrary = (libraryId) => {
		if (libraryPromises[libraryId]) {
			return libraryPromises[libraryId];
		}

		const settings = libraries[libraryId];
		if (!settings || !settings.json) {
			return Promise.resolve({});
		}

		libraryPromises[libraryId] = fetch(settings.json, { credentials: 'same-origin' })
			.then((response) => (response.ok ? response.json() : {}))
			.then((payload) => (payload && payload.icons ? payload.icons : payload))
			.catch(() => ({}));

		return libraryPromises[libraryId];
	};

	const injectSvg = (element, svgString, style) => {
		if (!svgString) {
			return;
		}

		element.innerHTML = svgString;
		element.classList.add('spectre-icon--rendered');

		if (style) {
			// Remove old style classes
			Array.from(element.classList)
				.filter((className) => className.startsWith('spectre-icon--style-'))
				.forEach((className) => element.classList.remove(className));

			element.classList.add('spectre-icon--style-' + style);
		}
	};

	const renderIcon = (element, libraryId) => {
		const settings = libraries[libraryId];

		if (!settings) {
			return;
		}

		const slugClass = Array.from(element.classList).find((className) =>
			className.startsWith(settings.prefix)
		);

		if (!slugClass) {
			return;
		}

		const slug = slugClass.replace(settings.prefix, '').trim();

		if (!slug) {
			return;
		}

		// Check if already processed
		const previousSlug = element.dataset.spectreIconSlug;
		const previousLibrary = element.dataset.spectreIconLibrary;

		if (previousSlug === slug && previousLibrary === libraryId) {
			return;
		}

		element.dataset.spectreIconSlug = slug;
		element.dataset.spectreIconLibrary = libraryId;

		const cacheKey = `${libraryId}::${slug}`;

		if (iconCache[cacheKey]) {
			injectSvg(element, iconCache[cacheKey], settings.style);
			return;
		}

		loadLibrary(libraryId).then((icons) => {
			iconCache[cacheKey] = icons[slug] || '';
			injectSvg(element, iconCache[cacheKey], settings.style);
		});
	};

	const processElement = (node) => {
		if (!node || node.nodeType !== Node.ELEMENT_NODE) {
			return;
		}

		Object.keys(libraries).forEach((libraryId) => {
			const settings = libraries[libraryId];

			if (!settings) {
				return;
			}

			if (node.matches && node.matches(settings.selector)) {
				renderIcon(node, libraryId);
			}

			const matches = node.querySelectorAll ? node.querySelectorAll(settings.selector) : [];
			matches.forEach((match) => renderIcon(match, libraryId));
		});
	};

	const startObserver = () => {
		const observer = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				if (mutation.type === 'childList') {
					mutation.addedNodes.forEach(processElement);
				}

				if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
					processElement(mutation.target);
				}
			});
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['class'],
		});
	};

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => {
			startObserver();
			processElement(document.body);
		});
	} else {
		startObserver();
		processElement(document.body);
	}
})();
