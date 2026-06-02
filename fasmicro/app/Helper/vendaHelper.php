<?php

if (!function_exists('jsFormHandler')) {

    function jsFormHandler(): string
    {
        return <<<'JS'
<script>

document
    .getElementById('form_filtro_modal_venda')
    .addEventListener('submit', (e) => {

        e.preventDefault();

        // FILTROS
        const produto = document
            .getElementById('prd_filtro_descricao_venda')
            .value
            .toLowerCase()
            .trim();

        const categoria = document
            .getElementById('prd_filtro_categoria_venda')
            .value
            .toLowerCase()
            .trim();

        const estoque = document
            .getElementById('prd_filtro_estoque_venda')
            .value
            .trim();

        // TODAS AS LINHAS DA TABELA
        const linhas = document.querySelectorAll('#tabela_produtos_modal tr');

        linhas.forEach((linha) => {

            const descricao = linha.dataset.descricao;
            const categoriaLinha = linha.dataset.categoria;

            const estoqueLinha = parseFloat(linha.dataset.estoque);
            const estoqueMinimo = parseFloat(linha.dataset.minimo);

            let mostrar = true;

            // FILTRO DESCRIÇÃO
            if (
                produto &&
                !descricao.includes(produto)
            ) {
                mostrar = false;
            }

            // FILTRO CATEGORIA
            if (
                categoria &&
                categoriaLinha !== categoria
            ) {
                mostrar = false;
            }

            // FILTRO ESTOQUE

            // sem estoque
            if (
                estoque === 'sem' &&
                estoqueLinha > 0
            ) {
                mostrar = false;
            }

            // abaixo do mínimo
            if (
                estoque === 'min' &&
                !(estoqueLinha <= estoqueMinimo)
            ) {
                mostrar = false;
            }

            // disponível
            if (
                estoque === 'disp' &&
                estoqueLinha <= 0
            ) {
                mostrar = false;
            }

            // MOSTRA OU ESCONDE
            linha.style.display = mostrar ? '' : 'none';

        });
    });
</script>
JS;
    }
}

if (!function_exists('carrega_itens_venda')) {

    function carrega_itens_venda( ) {
        return <<<'JS'
            <script>
                alert('teste');
            </script>
        JS;
    }
}
