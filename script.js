// Basic interactivity: mobile nav, contact form submit with fetch, simple client validation
document.addEventListener('DOMContentLoaded', () => {
  // update year
  const year = document.getElementById('year');
  if (year) year.textContent = new Date().getFullYear();

  // mobile nav toggle
  const navToggle = document.getElementById('navToggle');
  const navList = document.getElementById('navList');
  if (navToggle && navList) {
    navToggle.addEventListener('click', () => {
      navList.classList.toggle('show');
    });
  }

  // contact form
  const form = document.getElementById('contactForm');
  const statusEl = document.getElementById('formStatus');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      statusEl.textContent = 'Sending...';

      const name = document.getElementById('name').value.trim();
      const email = document.getElementById('email').value.trim();
      const message = document.getElementById('message').value.trim();

      if (!name || !email || !message) {
        statusEl.textContent = 'Please fill out all fields.';
        return;
      }

      try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('message', message);

        const res = await fetch(form.action, {
          method: 'POST',
          body: formData,
        });

        const json = await res.json();
        if (json.success) {
          statusEl.textContent = 'Message sent. Thank you!';
          form.reset();
        } else {
          statusEl.textContent = json.message || 'Something went wrong.';
        }
      } catch (err) {
        console.error(err);
        statusEl.textContent = 'Error sending message.';
      }
    });
  }
});
