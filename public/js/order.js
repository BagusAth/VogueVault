document.addEventListener('DOMContentLoaded', () => {
	const orderCards = document.querySelectorAll('.order-card[data-status-url]');

	if (!orderCards.length) {
		return;
	}

	const STATUS_CLASS_PREFIX = 'status-';
	const POLL_INTERVAL = 30000; // 30 seconds

	const formatRelativeTime = (value) => {
		if (!value) return '';

		try {
			const updatedDate = new Date(value);
			const now = new Date();
			const elapsed = updatedDate.getTime() - now.getTime();
			const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

			const ranges = {
				years: 24 * 60 * 60 * 1000 * 365,
				months: 24 * 60 * 60 * 1000 * 30,
				weeks: 24 * 60 * 60 * 1000 * 7,
				days: 24 * 60 * 60 * 1000,
				hours: 60 * 60 * 1000,
				minutes: 60 * 1000,
				seconds: 1000,
			};

			for (const [unit, ms] of Object.entries(ranges)) {
				if (Math.abs(elapsed) > ms || unit === 'seconds') {
					return rtf.format(Math.round(elapsed / ms), unit);
				}
			}
		} catch (error) {
			console.warn('Failed to format relative time', error);
		}

		return '';
	};

	const updateStatusBadge = (badge, statusText) => {
		if (!badge) return;

		const norm = (statusText || 'unknown').toString().toLowerCase();
		const statusSlug = norm.replace(/[^a-z0-9]+/g, '-');

		badge.textContent = statusText ? statusText.replace(/(^|\s)\S/g, (char) => char.toUpperCase()) : 'Unknown';

		badge.classList.forEach((cls) => {
			if (cls.startsWith(STATUS_CLASS_PREFIX)) {
				badge.classList.remove(cls);
			}
		});

		badge.classList.add(`${STATUS_CLASS_PREFIX}${statusSlug}`);
	};

	const refreshCardStatus = async (card) => {
		const url = card.dataset.statusUrl;
		if (!url) return;

		const badge = card.querySelector('[data-order-status]');
		const paymentLabel = card.querySelector('[data-payment-status]');
		const updatedLabel = card.querySelector('[data-order-updated]');

		try {
			const response = await fetch(url, {
				headers: {
					'Accept': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				cache: 'no-store',
			});

			if (!response.ok) {
				throw new Error(`Request failed with status ${response.status}`);
			}

			const payload = await response.json();

			if (badge && (payload.status !== undefined || payload.payment_status !== undefined)) {
				updateStatusBadge(badge, payload.status || payload.payment_status || 'Unknown');
			}

			if (paymentLabel && payload.payment_status !== undefined) {
				paymentLabel.textContent = `Payment: ${payload.payment_status ? payload.payment_status.replace(/(^|\s)\S/g, (char) => char.toUpperCase()) : 'Unknown'}`;
			}

			if (updatedLabel && payload.updated_at) {
				updatedLabel.textContent = `Updated ${formatRelativeTime(payload.updated_at) || ''}`.trim();
			}
		} catch (error) {
			console.warn('Unable to refresh order status', error);
		}
	};

	const pollStatuses = () => {
		orderCards.forEach((card) => {
			refreshCardStatus(card);
		});
	};

	pollStatuses();
	setInterval(pollStatuses, POLL_INTERVAL);

	orderCards.forEach((card) => {
		const paymentUrl = card.dataset.paymentUrl;
		if (!paymentUrl) {
			return;
		}

		card.addEventListener('click', (event) => {
			const interactive = event.target.closest('a, button');
			if (interactive && interactive.closest('.order-card') === card) {
				return;
			}

			window.location.href = paymentUrl;
		});
	});
});
