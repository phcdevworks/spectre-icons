(function () {
	'use strict';

	const config = window.SpectreElementorIconsConfig || {};
	const libraries = config.libraries || {};

	const libraryIds = Object.keys(libraries);

	if (!libraryIds.length) {
		return;
	}

	const libraryPromises = {};
	const iconCache = {};

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

		if (!style) {
			return;
		}

		// Remove prior style markers
		Array.from(element.classList)
			.filter((className) => className.startsWith('spectre-icon--style-'))
			.forEach((className) => element.classList.remove(className));

		element.classList.add('spectre-icon--style-' + style);
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

		const cacheKey = `${libraryId}::${slug}`;

		if (element.dataset.spectreIconKey === cacheKey) {
			return;
		}

		element.dataset.spectreIconKey = cacheKey;

		const finish = (svg) => {
			injectSvg(element, svg, settings.style);
		};

		if (iconCache[cacheKey]) {
			finish(iconCache[cacheKey]);
			return;
		}

		loadLibrary(libraryId).then((icons) => {
			iconCache[cacheKey] = icons[slug] || '';
			finish(iconCache[cacheKey]);
		});
	};

	const processElement = (node) => {
		if (!node || node.nodeType !== Node.ELEMENT_NODE) {
			return;
		}

		libraryIds.forEach((libraryId) => {
			const settings = libraries[libraryId];

			if (!settings || !settings.selector) {
				return;
			}

			if (node.matches && node.matches(settings.selector)) {
				renderIcon(node, libraryId);
			}

			if (!node.querySelectorAll) {
				return;
			}

			const matches = node.querySelectorAll(settings.selector);
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

	const init = () => {
		startObserver();
		processElement(document.body);
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
