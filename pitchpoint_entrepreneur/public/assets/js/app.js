// Tiny UX: auto‑dismiss flash messages
window.addEventListener('load', () => {
setTimeout(() => { document.querySelectorAll('.flash').forEach(el => el.remove()); }, 4000);
});