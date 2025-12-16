# Laravel PWA Installation Specification

## Objective
Convert the existing **Laravel web application** into a **Progressive Web App (PWA)** that can be installed via **Google Chrome** (desktop and mobile).

**Critical Requirement:**
When the PWA is **installed and launched**, it **MUST always open first** on the following route:

```
https://roshs.co.zw/logins
```

This behavior must be consistent regardless of:
- User device (desktop, Android, tablet)
- Browser (Chrome-based)
- Whether the user is logged in or not

---

## Non‑Negotiable Constraints

- ❌ Do NOT change any existing Laravel routes
- ❌ Do NOT rename controllers or views
- ❌ Do NOT break authentication flow
- ❌ Do NOT hardcode redirects in controllers

The solution **must be implemented using PWA configuration only**.

---

## Technical Requirements

### 1. PWA Type
- Use **Progressive Web App (PWA)** standards
- Must be installable via Chrome's **Install App** button

---

### 2. Manifest Configuration (MANDATORY)

Create or update the PWA manifest so that:

```json
{
  "name": "ROSHS Portal",
  "short_name": "ROSHS",
  "start_url": "/logins",
  "scope": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#0f172a"
}
```

✅ **Important:**
- `start_url` **must be `/logins` only** (not `/`)
- Do NOT append query parameters

---

### 3. Service Worker Rules

- Service worker must **not override** the `start_url`
- No redirect logic inside the service worker
- Cache static assets only (CSS, JS, images)

❌ The service worker must NOT:
- Redirect to `/`
- Redirect to `/dashboard`
- Force offline fallback to another route

---

### 4. Laravel Integration

- PWA assets must be publicly accessible
- Manifest must be linked in the main Blade layout

```html
<link rel="manifest" href="/manifest.json">
```

- Service worker must be registered globally

```html
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/serviceworker.js');
}
</script>
```

---

### 5. Authentication Compatibility

- `/logins` route must remain publicly accessible
- If authentication middleware exists, it must not block the PWA launch
- If user is already authenticated, Laravel may redirect AFTER login (not on install)

---

## Installation & Launch Behavior

### Expected Result

| Scenario | First Screen Shown |
|--------|-------------------|
| Fresh install | `/logins` |
| Re-open installed app | `/logins` |
| User logged out | `/logins` |
| User logged in | `/logins` (then app logic applies) |

---

## Hosting & Browser Requirements

- Application **MUST run on HTTPS**
- Valid SSL certificate required
- Chrome must detect:
  - Manifest
  - Service worker
  - Installability criteria

---

## Validation Checklist

- [ ] Chrome shows **Install App** option
- [ ] App installs successfully
- [ ] App launches in standalone window
- [ ] First screen is `/logins`
- [ ] No route or controller was modified

---

## Final Instruction to AI Coding Agent

> Implement a PWA for this Laravel application using standard web app manifest and service worker techniques. The installed PWA must always start on `/logins` without modifying existing Laravel routes, controllers, or authentication logic.

---

**Failure to enforce `start_url: /logins` is considered a failed implementation.**

