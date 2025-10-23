// public/tracker.v1.js (1.x series)
(function () {
  const ORIGIN = (function () {
    try {
      const script = document.currentScript || (function() {
        const scripts = document.getElementsByTagName('script');
        return scripts[scripts.length - 1];
      })();
      if (script && script.src) {
        const u = new URL(script.src);
        return u.origin;
      }
    } catch (_) {}
    return window.location.origin;
  })();

  const API_URL = ORIGIN + '/api/v1/visits.php';

  function uuidv4() {
    if (crypto && crypto.randomUUID) return crypto.randomUUID();
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  function getVisitorId() {
    try {
      const key = 'ytt_visitor_id';
      let id = localStorage.getItem(key);
      if (!id) {
        id = uuidv4();
        localStorage.setItem(key, id);
      }
      return id;
    } catch (e) {
      return uuidv4();
    }
  }

  function payload() {
    const page_url = window.location.href;
    const visitor_id = getVisitorId();
    const referer = document.referrer || '';
    return { page_url, visitor_id, referer };
  }

  function post(data) {
    const blob = new Blob([JSON.stringify(data)], { type: 'application/json' });
    if (navigator.sendBeacon) {
      try { navigator.sendBeacon(API_URL, blob); return; } catch (_) {}
    }
    fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
      keepalive: true,
      mode: 'cors',
    }).catch(function () {});
  }

  try { post(payload()); } catch (e) {}
})();
