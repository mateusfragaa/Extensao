<?php

if (!function_exists('usuarioFiltro')) {

    function usuarioFiltro(): string
    {
        return <<<'JS'
<script>
document.getElementById('form_filtro_usuario').addEventListener('submit', function(e) {
    e.preventDefault();

    const filtro = document.getElementById('filtro_nome').value.toLowerCase().trim();

    const linhas = document.querySelectorAll('#tabela_usuario tbody tr');

    linhas.forEach(function(linha) {

        const nome = linha.dataset.nome;
        const login = linha.dataset.login;

        if (
            filtro === '' ||
            nome.includes(filtro) ||
            login.includes(filtro)
        ) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }

    });
});
</script>
JS;
    }
}
