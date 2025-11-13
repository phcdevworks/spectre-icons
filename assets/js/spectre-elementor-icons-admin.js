(function () {
	'use strict';

	const config = window.SpectreIconsElementorConfig || window.SpectreElementorIconsConfig || {};
	const libraries = config.libraries || {};

	const libraryIds = Object.keys(libraries);

	if (!libraryIds.length) {
		return;
	}

	const libraryPromises = {};
	const iconCache = {};
	const observedRoots = new WeakSet();
	const scopedRefreshTimers = new WeakMap();

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

	const isScopeActive = (scope) => {
		if (!scope) {
			return false;
		}

		if (scope.nodeType === Node.DOCUMENT_NODE) {
			return true;
		}

		if (typeof scope.isConnected === 'boolean') {
			return scope.isConnected;
		}

		return document.body ? document.body.contains(scope) : true;
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
		const hasExistingSvg = element.querySelector('svg');

		if (element.dataset.spectreIconKey === cacheKey && hasExistingSvg) {
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

	const observeRoot = (root) => {
		if (!root || observedRoots.has(root)) {
			return;
		}

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

		observer.observe(root, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['class'],
		});

		observedRoots.add(root);
	};

	const processElement = (node) => {
		if (!node) {
			return;
		}

		if (node.nodeType === Node.DOCUMENT_FRAGMENT_NODE) {
			node.childNodes.forEach(processElement);
			return;
		}

		if (node.nodeType !== Node.ELEMENT_NODE) {
			return;
		}

		if ('elementor-icons-manager-modal' === node.id || (node.matches && node.matches('#elementor-icons-manager-modal'))) {
			ensureModalRefreshLoop();
		} else if (node.querySelector && node.querySelector('#elementor-icons-manager-modal')) {
			ensureModalRefreshLoop();
		}

		if ('elementor-panel' === node.id || (node.matches && node.matches('#elementor-panel'))) {
			ensurePanelRefreshLoop();
		} else if (node.closest && node.closest('#elementor-panel')) {
			ensurePanelRefreshLoop();
		}

		if (node.shadowRoot) {
			observeRoot(node.shadowRoot);
			processElement(node.shadowRoot);
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

	const refreshLibrariesInScope = (scope) => {
		if (!scope || !scope.querySelectorAll) {
			return;
		}

		libraryIds.forEach((libraryId) => {
			const settings = libraries[libraryId];

			if (!settings || !settings.selector) {
				return;
			}

			const matches = scope.querySelectorAll(settings.selector);
			matches.forEach((match) => renderIcon(match, libraryId));
		});
	};

	const startScopedRefresh = (scope, interval = 400) => {
		if (!scope || scopedRefreshTimers.has(scope)) {
			return;
		}

		const timer = setInterval(() => {
			if (!isScopeActive(scope)) {
				clearInterval(timer);
				scopedRefreshTimers.delete(scope);
				return;
			}

			refreshLibrariesInScope(scope);
		}, interval);

		scopedRefreshTimers.set(scope, timer);
	};

	const ensureModalRefreshLoop = () => {
		const modal = document.getElementById('elementor-icons-manager-modal');

		if (modal) {
			startScopedRefresh(modal, 200);
		}
	};

	const ensurePanelRefreshLoop = () => {
		const panel = document.getElementById('elementor-panel');

		if (panel) {
			startScopedRefresh(panel, 500);
		}
	};

	const init = () => {
		observeRoot(document.body);
		processElement(document.body);
		startScopedRefresh(document, 1500);
		ensurePanelRefreshLoop();
		refreshLibrariesInScope(document);
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	document.addEventListener('elementor/icons-manager/open', ensureModalRefreshLoop);
	ensureModalRefreshLoop();
})();
