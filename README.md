# Portfolio Website (HTML, CSS, JS) + PHP Backend

This is a simple portfolio website with a contact form backed by PHP. It stores messages in `messages.json` and attempts to send an email via PHP `mail()` (server must be configured).

Files included
- `index.html` — main frontend
- `styles.css` — styles
- `script.js` — client JS (form submission, nav)
- `contact.php` — server-side contact handler
- `messages.json` — message storage (initially empty)

Quick start (local)
1. Ensure you have PHP installed (7.4+ recommended).
2. Put the files into a folder, e.g. `portfolio`.
3. Open a terminal in that folder and run:
   ```
   php -S localhost:8000
   ```
4. Open http://localhost:8000 in your browser and test the site.

Permissions
- Make sure `messages.json` is writable by the web server user. On Unix systems:
  ```
  chmod 664 messages.json
  chown www-data:www-data messages.json   # adjust user/group for your environment
  ```

Configure email
- `contact.php` uses PHP `mail()` by default. On many local dev environments `mail()` is not configured.
- For production, use a proper SMTP library (PHPMailer or SwiftMailer) and an authenticated SMTP (e.g., SendGrid, Mailgun, SMTP from hosting).
- Replace the `mail()` call in `contact.php` with PHPMailer for robust delivery.

Security & production notes
- This is a minimal example. Before deploying publicly:
  - Use HTTPS.
  - Use server-side rate-limiting and spam protection (reCAPTCHA or honeypot).
  - Validate and sanitize more strictly (limit message size, strip tags if needed).
  - Store messages in a database (MySQL/Postgres) rather than a JSON file for scale.
  - Consider adding CSRF protection for the form.
  - Secure the folder where messages are stored so it's not directly served by the web server.
  - Log access and failures and monitor for abuse.

Customization
- Replace placeholder content with your real projects and contact email.
- Update styles to match your branding.

If you want, I can:
- Replace the file-based storage with a MySQL backend and provide SQL schema + code.
- Integrate PHPMailer with SMTP configuration.
- Create a simple admin view to list messages (protected by password).
