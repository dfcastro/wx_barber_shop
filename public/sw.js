// public/sw.js

// Um service worker mínimo apenas para escutar o evento 'fetch'.
// Isso é o suficiente para que o navegador reconheça o site como um PWA instalável.
self.addEventListener('fetch', (event) => {
    // Intencionalmente vazio por enquanto.
  });