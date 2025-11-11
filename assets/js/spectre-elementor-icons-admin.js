(function () {
	'use strict';

	const config = window.SpectreElementorIconsConfig || {};
	const libraries = config.libraries || {};
	const observers = [];

	const libraryPromises = {};
	const iconCache = {};

	const hasLibraries = Object.keys(libraries).length > 0;

	const addLoadedClass = (element) => {
		element.classList.add('spectre-icon--rendered');
	};

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
		addLoadedClass(element);

		if (style) {
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
		if (node.nodeType !== Node.ELEMENT_NODE) {
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

	const processIconPickerModal = () => {
		// Target the Elementor icons panel specifically
		const iconsPanels = document.querySelectorAll('.elementor-icons-manager__tab__item__content, .elementor-control-icons-list');
		iconsPanels.forEach((panel) => processElement(panel));
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
		observers.push(observer);

		// Additional observer for modals/dialogs that might be outside body initially
		const dialogObserver = new MutationObserver(() => {
			processIconPickerModal();
		});

		// Observe dialog container if it exists
		setTimeout(() => {
			const dialogContainer = document.querySelector('.dialog-widget-content, .elementor-templates-modal, #elementor-panel');
			if (dialogContainer) {
				dialogObserver.observe(dialogContainer, {
					childList: true,
					subtree: true,
				});
			}
		}, 500);
	};

	const init = () => {
		if (!hasLibraries) {
			return;
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init);
			return;
		}

		startObserver();

		// Process any existing nodes (e.g., icon control default preview).
		processElement(document.body);

		// Wait for Elementor to fully initialize
		const waitForElementor = setInterval(() => {
			if (window.elementor && elementor.config) {
				clearInterval(waitForElementor);

				// Hook into Elementor's panel rendering for icon picker
				elementor.on('panel:init', () => {
					setTimeout(() => {
						processElement(document.body);
						processIconPickerModal();
					}, 100);
				});

				// When icon library modal opens
				if (typeof jQuery !== 'undefined') {
					jQuery(document).on('elementor:init', () => {
						elementor.channels.editor.on('section:activated', () => {
							setTimeout(() => {
								processElement(document.body);
								processIconPickerModal();
							}, 100);
						});
					});
				}
			}
		}, 100);

		// Failsafe: stop checking after 10 seconds
		setTimeout(() => clearInterval(waitForElementor), 10000);
	};

	init();
})();
