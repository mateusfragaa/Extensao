/**
 * filtroLista.js
 * Filtragem AJAX genérica para listas do sistema.
 *
 * Uso: adicionar data-filtro="true" no <form> de filtro
 * e data-tabela="idDoContainer" no elemento que será atualizado.
 *
 * Exemplo:
 *   <form data-filtro="true" data-tabela="tabelaContainer" action="...">
 *   <div id="tabelaContainer"> ... </div>
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[data-filtro="true"]');
        if (!form) return;

        const tabelaId = form.dataset.tabela || 'tabelaContainer';
        const tabela   = document.getElementById(tabelaId);
        if (!tabela) return;

        let timeout = null;

        function executarFiltro() {
            const formData = new FormData(form);
            tabela.style.opacity = '0.5';

            fetch(form.action, {
                method:  'POST',
                body:    formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                const doc     = new DOMParser().parseFromString(html, 'text/html');
                const novoEl  = doc.getElementById(tabelaId);
                if (novoEl) tabela.innerHTML = novoEl.innerHTML;
                tabela.style.opacity = '1';
            })
            .catch(() => { tabela.style.opacity = '1'; });
        }

        // Inputs de texto — debounce 300ms
        form.querySelectorAll('input[type="text"], input[type="search"]').forEach(function (el) {
            el.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(executarFiltro, 300);
            });
        });

        // Selects — imediato
        form.querySelectorAll('select').forEach(function (el) {
            el.addEventListener('change', executarFiltro);
        });

        // Submit normal
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            executarFiltro();
        });
    });
})();
