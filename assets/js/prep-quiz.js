/* PrEP Quiz — no tracking, no cookies, no external requests */
(function () {
	'use strict';

	/* ── Data ── */
	var QUESTIONS = [
		'Are you in a sexual relationship with a partner who is HIV-positive, or do you anticipate having sex with an HIV-positive partner?',
		'Have you had condomless sex with a partner whose HIV status is unknown or positive in the past six months?',
		'Have you been diagnosed with a sexually transmitted infection (STI) in the past year?',
		'Do you use injection drugs and share needles or equipment with others?',
		'Are you involved in sex work, or have you had sexual partners who engage in sex work?',
		'Have you had multiple sexual partners in the past year?',
		'Are you planning to become pregnant, or are you already pregnant and at risk of HIV exposure?',
		'Do you have a partner who is not consistently taking HIV treatment (ART) or has a detectable viral load?'
	];

	var OPTIONS = [
		{ key: 'A', label: 'Yes',      score: 2 },
		{ key: 'B', label: 'No',       score: 0 },
		{ key: 'C', label: 'Not Sure', score: 1 }
	];

	/* Score 1–16 → result 0 (candidate). Score 0 → result 1 (not immediate). */
	var RESULTS = [
		{
			title: 'You may be a candidate for Pre-Exposure Prophylaxis (PrEP)',
			body: [
				'Based on your responses to the quiz, it appears that you may be a candidate for Pre-Exposure Prophylaxis (PrEP). However, please keep in mind that this quiz is just an initial assessment tool, and the final decision should be made after consulting with a healthcare provider.',
				'We strongly recommend finding an AHF Wellness Center or AHF Healthcare Center near you or filling out our contact form to discuss PrEP with our Benefits Counselors. They will provide personalized guidance, answer any questions you may have, and determine if PrEP is the right choice for you.'
			]
		},
		{
			title: 'You may not be an immediate candidate for PrEP',
			body: [
				'Thank you for taking the Pre-Exposure Prophylaxis (PrEP) quiz. Based on your responses, it appears that you may not be an immediate candidate for PrEP. This quiz is an initial screening tool, and your lower-risk answers suggest that you may not currently require PrEP for HIV prevention.',
				'However, it\'s important to remember that individual circumstances and risks can vary. The decision to use PrEP should be made after a comprehensive evaluation by a healthcare provider. Find an AHF Wellness Center or AHF Healthcare Center near you or fill out our contact form to speak to one of our Benefits Counselors. They can provide further guidance and discuss other methods of HIV prevention that may be more suitable for you.'
			]
		}
	];

	/* ── State ── */
	var currentQ   = 0;
	var score      = 0;
	var selected   = null;
	var answerHistory = []; // Track answers for back navigation

	/* ── Elements ── */
	var scQuestion = document.getElementById('pq-question');
	var scResult   = document.getElementById('pq-result');
	var progFill   = document.getElementById('pq-prog-fill');
	var progBar    = progFill.parentElement;
	var elNum      = document.getElementById('pq-num');
	var elText     = document.getElementById('pq-text');
	var elOpts     = document.getElementById('pq-opts');
	var btnOk      = document.getElementById('pq-btn-ok');
	var btnBack    = document.getElementById('pq-btn-back');
	var btnRetake  = document.getElementById('pq-btn-retake');
	var elResTitle = document.getElementById('pq-res-title');
	var elResBody  = document.getElementById('pq-res-body');

	/* ── Progress ── */
	function setProgress(step) {
		var pct = Math.round((step / QUESTIONS.length) * 100);
		progFill.style.width = pct + '%';
		progBar.setAttribute('aria-valuenow', pct);
	}

	/* ── Screen transitions ── */
	function switchScreen(next) {
		var screens = [scQuestion, scResult];
		var current = screens.find(function (s) { return s.classList.contains('pq-active'); });

		if (current === next) return;

		if (current) {
			current.classList.add('pq-out');
			current.addEventListener('animationend', function onOut() {
				current.removeEventListener('animationend', onOut);
				current.classList.remove('pq-active', 'pq-out');
				current.style.display = 'none';
				revealScreen(next);
			}, { once: true });
		} else {
			revealScreen(next);
		}
	}

	function revealScreen(s) {
		s.style.display = 'flex';
		/* Force reflow so animation fires */
		void s.offsetWidth;
		s.classList.add('pq-active');
	}

	/* ── Render question ── */
	function renderQuestion(idx) {
		selected = null;
		elNum.textContent = idx + 1;
		elText.textContent = QUESTIONS[idx];
		elOpts.innerHTML = '';
		btnOk.disabled = true;

		OPTIONS.forEach(function (opt, i) {
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'pq-option';
			btn.setAttribute('role', 'radio');
			btn.setAttribute('aria-checked', 'false');
			btn.dataset.idx = i;
			btn.innerHTML =
				'<span class="pq-badge" aria-hidden="true">' + opt.key + '</span>' +
				'<span class="pq-option-label">' + opt.label + '</span>';
			btn.addEventListener('click', function () { pick(i); });
			elOpts.appendChild(btn);
		});

		// Restore answer from history if it exists
		if (answerHistory[idx] !== undefined) {
			pick(answerHistory[idx]);
		}

		setProgress(idx);
		updateBackBtn();
	}

	/* ── Select answer ── */
	function pick(idx) {
		selected = idx;
		elOpts.querySelectorAll('.pq-option').forEach(function (el, i) {
			el.classList.toggle('pq-selected', i === idx);
			el.setAttribute('aria-checked', i === idx ? 'true' : 'false');
		});
		btnOk.disabled = false;
	}

	/* ── Update back button state ── */
	function updateBackBtn() {
		if (currentQ > 0) {
			btnBack.style.opacity = '1';
			btnBack.style.pointerEvents = 'auto';
		} else {
			btnBack.style.opacity = '0.6';
			btnBack.style.pointerEvents = 'none';
		}
	}

	/* ── Go back to previous question ── */
	function goBack() {
		if (currentQ <= 0) return;
		
		// Restore previous answer and score
		currentQ--;
		var prevAnswer = answerHistory[currentQ];
		if (prevAnswer !== undefined) {
			score -= OPTIONS[prevAnswer].score;
		}
		
		renderQuestion(currentQ);
		switchScreen(scQuestion);
	}

	/* ── Advance ── */
	function advance() {
		if (selected === null) return;
		
		// Store answer in history
		answerHistory[currentQ] = selected;
		score += OPTIONS[selected].score;
		currentQ++;

		if (currentQ >= QUESTIONS.length) {
			showResult();
		} else {
			renderQuestion(currentQ);
			switchScreen(scQuestion);
		}
	}

	/* ── Show result ── */
	function showResult() {
		setProgress(QUESTIONS.length);
		var result = score >= 1 ? RESULTS[0] : RESULTS[1];

		elResTitle.textContent = result.title;
		elResBody.innerHTML = '';
		result.body.forEach(function (para) {
			var p = document.createElement('p');
			p.textContent = para;
			elResBody.appendChild(p);
		});

		switchScreen(scResult);
	}

	/* ── Reset ── */
	function reset() {
		currentQ = 0;
		score    = 0;
		selected = null;
		answerHistory = [];
		renderQuestion(0);
		switchScreen(scQuestion);
	}

	/* Initialize by going directly to first question */
	reset();

	/* ── Keyboard ── */
	document.addEventListener('keydown', function (e) {
		if (!scQuestion.classList.contains('pq-active')) return;
		var k = e.key.toUpperCase();
		if      (k === 'A' || k === '1') pick(0);
		else if (k === 'B' || k === '2') pick(1);
		else if (k === 'C' || k === '3') pick(2);
		else if (k === 'ENTER' && selected !== null) { e.preventDefault(); advance(); }
		else if (k === 'BACKSPACE' && currentQ > 0) { e.preventDefault(); goBack(); }
	});

	/* ── Events ── */
	btnOk.addEventListener('click',     advance);
	btnBack.addEventListener('click',   goBack);
	btnRetake.addEventListener('click', reset);

}());
