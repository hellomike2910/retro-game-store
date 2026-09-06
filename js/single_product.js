'use strict';
const mainImage = document.getElementById('main-product-image');
document.querySelectorAll('.small-img-col').forEach(link => {
  link.addEventListener('click', event => {
    if (!mainImage) return;
    event.preventDefault();
    mainImage.src = link.href;
    mainImage.alt = link.querySelector('img').alt;
  });
});
