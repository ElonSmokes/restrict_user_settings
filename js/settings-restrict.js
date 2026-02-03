/**
 * Restrict user settings: hide personal settings sections for non-admin users.
 * Loaded on every page for logged-in users; only runs on Settings → User page.
 */
(function () {
	'use strict';

	const APP_ID = 'restrict_user_settings';

	// Only run on personal settings page (path contains settings/user or settings/personal)
	function isSettingsUserPage() {
		const path = window.location.pathname || '';
		const hash = window.location.hash || '';
		return /\/settings\/user/i.test(path) || /\/settings\/personal/i.test(path) || /#\/settings\/(user|personal)/i.test(hash);
	}

	function getConfig() {
		return new Promise(function (resolve, reject) {
			const base = window.location.pathname.replace(/\/index\.php\/?$/, '') || '';
			const url = (base + '/index.php/apps/' + APP_ID + '/config/visibility').replace(/\/+/g, '/');
			const xhr = new XMLHttpRequest();
			xhr.open('GET', url, true);
			xhr.setRequestHeader('Requested-With', 'XMLHttpRequest');
			xhr.onreadystatechange = function () {
				if (xhr.readyState !== 4) return;
				if (xhr.status >= 200 && xhr.status < 300) {
					try {
						resolve(JSON.parse(xhr.responseText));
					} catch (e) {
						reject(e);
					}
				} else {
					reject(new Error('Config request failed: ' + xhr.status));
				}
			};
			xhr.onerror = function () { reject(new Error('Network error')); };
			xhr.send();
		});
	}

	// Map our section keys to possible DOM selectors (sidebar + content).
	// Nextcloud may use data-id, data-section, or href with section name.
	const SECTION_SELECTORS = {
		personal_info: ['personal_info', 'personal-info', 'personalinfo', 'profile'],
		notifications: ['notifications'],
		sharing: ['sharing'],
		appearance: ['appearance', 'accessibility', 'accessibility_settings'],
		availability: ['availability', 'outofoffice', 'out-of-office'],
		privacy: ['privacy'],
		security: ['security'],
	};

	function hideBySelectors(sectionKeys) {
		const hidden = Array.isArray(sectionKeys) ? sectionKeys : [];
		hidden.forEach(function (key) {
			const variants = SECTION_SELECTORS[key];
			if (!variants) return;
			variants.forEach(function (id) {
				// Sidebar: nav item or link
				document.querySelectorAll(
					'[data-id="' + id + '"], [data-section="' + id + '"], a[href*="' + id + '"], li[data-id="' + id + '"], .app-navigation-entry[data-id="' + id + '"]'
				).forEach(function (el) {
					el.style.setProperty('display', 'none', 'important');
				});
				// Content area: section or panel
				document.querySelectorAll(
					'[data-id="' + id + '"], [data-section="' + id + '"], #' + id + ', .section[data-id="' + id + '"], .settings-section[data-id="' + id + '"]'
				).forEach(function (el) {
					el.style.setProperty('display', 'none', 'important');
				});
			});
		});
	}

	function hideSecurityExceptDevices() {
		// Find security content container, then hide all subsections except "Devices & sessions"
		var securityContent = document.querySelector('[data-id="security"], [data-section="security"], #security');
		if (!securityContent) return;

		var sections = securityContent.querySelectorAll('[class*="section"], [class*="setting"], [data-id], h3, h2');
		sections.forEach(function (node) {
			var text = (node.textContent || '').toLowerCase();
			// If it's the "Devices & sessions" or "Logged in devices" block, keep it
			if (text.indexOf('device') !== -1 || text.indexOf('session') !== -1 || text.indexOf('logged in') !== -1) {
				return;
			}
			// If it looks like a heading for password, 2FA, etc., hide the following block
			if (text.indexOf('password') !== -1 || text.indexOf('two-factor') !== -1 || text.indexOf('2fa') !== -1 || text.indexOf('second factor') !== -1) {
				var next = node.nextElementSibling;
				if (next) next.style.setProperty('display', 'none', 'important');
				node.style.setProperty('display', 'none', 'important');
			}
		});
	}

	function run() {
		if (!isSettingsUserPage()) return;

		getConfig().then(function (config) {
			if (config.isAdmin) return;

			if (config.hiddenSections && config.hiddenSections.length) {
				hideBySelectors(config.hiddenSections);
			}

			if (config.securityOnlyDevices) {
				hideSecurityExceptDevices();
			}
		}).catch(function () {
			// Silently ignore (e.g. app disabled or not logged in)
		});
	}

	// Run when DOM is ready; also after a short delay for Vue-rendered content
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
	setTimeout(run, 1500);
	setTimeout(run, 3500);
})();
