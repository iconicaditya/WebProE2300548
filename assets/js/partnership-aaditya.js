(function () {
	var section = document.getElementById('partnershipSection');
	if (!section) return;

	var cards = Array.prototype.slice.call(section.querySelectorAll('.partner-card'));
	if (!cards.length) return;
	var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	cards.forEach(function (card, index) {
		if (reducedMotion) {
			card.style.setProperty('--logo-delay', '0ms');
			return;
		}
		card.style.setProperty('--logo-delay', String(index * 45) + 'ms');
	});
})();
