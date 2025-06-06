// OPTIMAZION SEO
document.addEventListener("DOMContentLoaded", function () {
  document
    .querySelectorAll('a.um-modal-close[href="javascript:void(0);"]')
    .forEach(function (el) {
      el.removeAttribute("href");
      el.setAttribute("role", "button");
      el.style.cursor = "pointer";
    });
});
