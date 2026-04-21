# GodFile
A radically minimalist, single-file AI Web Builder and CMS. 

Drop one file (index.php) onto any PHP web server, and you get a fully functional, AI-powered development environment. No Node.js, no Webpack, no vendor lock-in.

Features:
Zero Configuration: Entirely self-contained in a single index.php file. No MySQL setup required (uses a local SQLite database automatically).

Multiple AI Providers:
- Logic/Text generation: Google Gemini (via API) or Local LLMs (via Ollama API).
- Image generation: OpenAI (DALL-E 2/3), Together AI (FLUX.1 Schnell), or Pollinations (Free). Uses pure Base64 encoding to bypass server-side URL download restrictions.
- Visual Inspector: Click on any element in the live iframe preview to automatically insert its CSS selector into the prompt editor.
- Version Control: Automatic backup of modified files with a 1-click restore functionality from the history log.
- Multilingual Interface: Supported languages include English, Czech, Spanish, and Chinese.
- Security Built-In: Includes protections against CSRF, Path Traversal, Brute-force attacks, Session Fixation, and blocks execution of malicious uploads (e.g., SVG scripts).

Installation
1. Upload create.php to an empty directory on your web server.
2. Ensure the web server has write permissions to that directory (the script needs to create an SQLite database and an output folder for your generated website files).
3. Access create.php through your browser.

Getting Started:
1. Login: The default credentials are admin / admin. You will be forced to change this password immediately upon your first login.
2. Configuration: Go to the "Settings" tab.
  - Set up your Text API: Choose Google Gemini and enter your API key, or use a local Ollama instance URL.
  - Set up your Image API: Choose your preferred provider. Pollinations is free. Together AI and OpenAI require API keys.
3. Build: Go to the "Editor" tab. Write a prompt describing the website or specific changes you want to make, and click Execute. The AI will plan the necessary files, generate images, and write the raw code.
4. Iterate: Use the Live Preview to see changes in real-time. If you want to change a specific button or text, click on it in the preview panel, and its HTML selector will be added to your prompt automatically.
5. Restore: If the AI breaks your design, scroll down the Editor page to the History log and click "Restore" on a previous stable step.

Requirements:

- PHP 7.4 or higher (PHP 8.x recommended).
- PHP cURL extension.
- PHP PDO SQLite extension.
- Fileinfo extension (for secure image uploads).

Architecture & Security Notes:

- Web Output: All generated files (HTML, CSS, JS, images) are saved in an output_path directory (default: website_output). This directory is kept separated from the logic.
- Live Preview: The live preview uses a restrictive iframe sandbox to prevent interference with the CMS UI while allowing the Visual Inspector to function via strict postMessage origin checks.
- File Uploads: Image uploads are strictly limited to common, non-executable image formats (jpg, png, webp, gif) to prevent XSS and remote code execution vulnerabilities.
- Rate Limiting: A built-in rate limit prevents abuse of the AI API endpoints.

The GodFile Manifesto
Why use a cannon to kill a fly?
WordPress has become a monster. In 2026, a basic WordPress installation takes up roughly 78 MB of code just to display a five-page business card site or a simple company web. For small projects, that is pure overkill. You have to constantly update plugins, patch security holes, back up the database, and pay for hosting that can actually handle all that dead weight.

GodFile is under 100 KB. And it does the same job.

The brute force of static HTML
The output of GodFile is clean HTML, CSS, and JavaScript. No database on the frontend, no complex PHP routing firing on every page load.

TTFB: A typical WordPress site sits at 200 to 600 ms. Static HTML from GodFile loads in 10 to 20 ms because the web server just throws the file straight onto the wire.

Server load: Static files served through Nginx or Apache need the absolute minimum of disk operations. A five-dollar VPS can handle 10,000+ requests per second. A standard WordPress install without aggressive caching dies at 50 concurrent users.

Zero maintenance: The generated site needs no Redis, no Memcached, no caching plugins. Nothing is faster, more stable, or more secure than a plain static file. That is not optimization. That is physics.

Sovereignty and local AI
GodFile is not a dumb pipe connected to cloud APIs. It natively supports local LLMs through Ollama, meaning the AI runs on your own hardware, with no monthly fees to any corporation and with complete data privacy.

The core principle is ownership. If your website depends on a proprietary no-code subscription, or on a system that can fall apart after one plugin update, you do not actually own it. GodFile gives you 100% sovereignty over your code. One file. No dependencies. No packages to install, nothing that ages out, nothing that breaks.

Vision: AI in your pocket
Today GodFile works with both cloud models and local Ollama setups. But the real potential is one step further.

Hardware limits are moving fast. We are heading toward an era where everyone will have access to a local model running on roughly 200 GB of VRAM, not in a server room but sitting on your desk or in your pocket. When that moment arrives, minimal personal projects like GodFile become something else entirely: fully autonomous web creation tools that run completely offline, with zero dependency on any external service.

One file plus a local model equals a complete web factory in your own hands. That is the future GodFile is built for.

License:
This project is licensed under the MIT License. See the LICENSE file for details.
Created by Martin "Supík" Vorel (Rolid)
