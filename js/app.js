'use strict';
const menuToggle = document.querySelector('.menu-toggle');
const menu = document.getElementById('main-nav');
if (menuToggle && menu) {
  menuToggle.addEventListener('click', () => {
    const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!expanded));
    menu.classList.toggle('is-open', !expanded);
  });
}
// Only prevent repeated submissions after native validation passes.
document.querySelectorAll('form[method="post"]').forEach(form => {
  form.addEventListener('submit', () => {
    const submit = form.querySelector('button[type="submit"]');
    if (submit) { submit.disabled = true; submit.setAttribute('aria-busy', 'true'); }
  });
});
