import './bootstrap';

function dismissLoader() {
  const loader = document.getElementById('loader');
  if (!loader) return;
  loader.style.transition = 'opacity 0.35s ease';
  loader.style.opacity = '0';
  setTimeout(() => loader.remove(), 350);
}

function revealAll() {
  document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
}

document.addEventListener('DOMContentLoaded', () => {
  // Loader: remove quickly so content never stays hidden
  dismissLoader();
  setTimeout(dismissLoader, 800);
  window.addEventListener('load', dismissLoader);

  // Mobile menu
  const menuBtn = document.getElementById('menu-btn');
  const mobileNav = document.getElementById('mobile-nav');
  const menuOpen = document.getElementById('menu-open');
  const menuClose = document.getElementById('menu-close');

  menuBtn?.addEventListener('click', () => {
    const willShow = mobileNav?.classList.contains('hidden');
    mobileNav?.classList.toggle('hidden');
    // willShow === true means the nav is about to open: hide the
    // hamburger icon and reveal the close (X) icon in that case.
    menuOpen?.classList.toggle('hidden', willShow);
    menuClose?.classList.toggle('hidden', !willShow);
    menuBtn.setAttribute('aria-expanded', willShow ? 'true' : 'false');
  });

  // Header scroll (class OR id)
  const header = document.getElementById('site-header') || document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });
  }

  // Soft press feedback
  document.querySelectorAll('.home-page-shell a, .home-page-shell button, .home-page-shell .grade-link, .home-page-shell .btn-sm')
    .forEach((el) => {
      el.addEventListener('click', () => {
        el.classList.add('is-pressed');
        setTimeout(() => el.classList.remove('is-pressed'), 180);
      });
    });

  // Scroll reveal — only enhance, never leave content hidden
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08, rootMargin: '0px 0px -20px 0px' }
    );
    revealEls.forEach((el) => observer.observe(el));
    // True last-resort failsafe only — long enough that it never fires
    // during normal scrolling, so it can't pre-empt the reveal animation
    // for content the user hasn't reached yet. This only protects
    // against the observer genuinely failing to fire at all.
    setTimeout(revealAll, 8000);
  } else {
    revealAll();
  }

  // Flash auto-hide
  document.querySelectorAll('[data-flash]').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-8px)';
      setTimeout(() => el.remove(), 400);
    }, 4000);
  });

  // Homepage mini game
  const gameQuestion = document.getElementById('game-question');
  const gameFeedback = document.getElementById('game-feedback');
  const gameScore = document.getElementById('game-score');
  const gameReset = document.getElementById('game-reset');
  const gameAnswers = document.querySelectorAll('.home-game-answer');

  if (gameQuestion && gameFeedback && gameScore && gameReset && gameAnswers.length) {
    let currentAnswer = null;
    let score = 0;

    const getRandomQuestion = () => {
      const a = Math.floor(Math.random() * 18) + 2;
      const b = Math.floor(Math.random() * 18) + 2;
      return { text: `${a} + ${b} = ?`, answer: a + b };
    };

    const shuffle = (array) => array.sort(() => Math.random() - 0.5);

    const renderQuestion = () => {
      const question = getRandomQuestion();
      currentAnswer = question.answer;
      gameQuestion.textContent = question.text;
      gameFeedback.textContent = 'Зөв хариуг сонгоод оноогоо ахиулна.';
      gameFeedback.classList.remove('text-emerald-500', 'text-rose-500');
      gameFeedback.classList.add('text-slate-500');
      gameAnswers.forEach((button) => {
        button.disabled = false;
        button.classList.remove('correct', 'wrong');
      });
      const options = [currentAnswer, currentAnswer + 1, currentAnswer - 1, currentAnswer + 2];
      shuffle(options).forEach((value, index) => {
        const button = gameAnswers[index];
        if (!button) return;
        button.textContent = value;
        button.dataset.answer = value;
      });
    };

    gameAnswers.forEach((button) => {
      button.addEventListener('click', () => {
        const selected = Number(button.dataset.answer);
        gameAnswers.forEach((btn) => {
          btn.disabled = true;
          btn.classList.remove('correct', 'wrong');
        });

        if (selected === currentAnswer) {
          button.classList.add('correct');
          score += 1;
          gameScore.textContent = String(score);
          gameFeedback.textContent = 'Зөв! Дараагийн бодлогыг үзнэ үү.';
          gameFeedback.classList.add('text-emerald-500');
        } else {
          button.classList.add('wrong');
          gameAnswers.forEach((btn) => {
            if (Number(btn.dataset.answer) === currentAnswer) btn.classList.add('correct');
          });
          gameFeedback.textContent = `Буруу. Зөв хариулт: ${currentAnswer}.`;
          gameFeedback.classList.add('text-rose-500');
        }
      });
    });

    gameReset.addEventListener('click', renderQuestion);
    renderQuestion();
  }

  // Billing cancel confirm
  document.querySelector('[data-confirm-cancel]')?.addEventListener('click', (e) => {
    if (!confirm('Захиалгыг цуцлахдаа итгэлтэй байна уу?')) {
      e.preventDefault();
    }
  });
});

