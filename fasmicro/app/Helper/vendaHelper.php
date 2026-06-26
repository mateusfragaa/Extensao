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

if (!function_exists('linhas_itens_venda')) {
    /**
     * Monta as linhas <tr> da tabela de itens do pedido em PHP puro.
     * Substitui a antiga renderização via JavaScript (carrega_itens_venda).
     *
     * @param array $dados Itens do pedido (vindos de select_produto_venda)
     * @return string HTML das linhas da tabela
     */
    function linhas_itens_venda($dados)
    {
        if (empty($dados)) {
            return '';
        }

        $html = '';
        foreach ($dados as $item) {
            $html .= '<tr>'
                . '<td>' . htmlspecialchars($item['prd_id'], ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars($item['prd_descricao'], ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars($item['PEVI_QUANTIDADE'], ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>R$ ' . number_format($item['PEVI_PRECO_UNITARIO'], 2, ',', '.') . '</td>'
                . '<td>R$ ' . number_format($item['PEVI_SUBTOTAL'], 2, ',', '.') . '</td>'
                . '<td>'
                . '<input type="checkbox" name="produtos_excluir[]" value="' . htmlspecialchars($item['PEVI_ID'], ENT_QUOTES, 'UTF-8') . '" class="form-check-input fs-5 item-venda">'
                . '</td>'
                . '</tr>';
        }

        return $html;
    }
}

if (!function_exists('subtotal_itens_venda')) {
    /**
     * Calcula o subtotal (soma de PEVI_SUBTOTAL) dos itens do pedido, formatado em R$.
     *
     * @param array $dados Itens do pedido
     * @return string Valor formatado, ex: "1.234,56"
     */
    function subtotal_itens_venda($dados)
    {
        return number_format(
            array_sum(array_column($dados, 'PEVI_SUBTOTAL')),
            2,
            ',',
            '.'
        );
    }
}

