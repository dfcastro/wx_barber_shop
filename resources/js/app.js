import './bootstrap';

import Alpine from 'alpinejs';

// Apenas definimos o Alpine na janela global.
// O Livewire irá encontrar esta variável e iniciar o Alpine por conta própria.
window.Alpine = Alpine;
console.log('VERSÃO NOVA DO APP.JS CARREGADA COM SUCESSO!');