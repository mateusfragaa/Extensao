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

    function carrega_itens_venda($dados) {
        $dados = json_encode($dados);
        $subTotal = number_format(array_sum(array_column(json_decode($dados),'PEVI_SUBTOTAL')),2,',','.');
        return <<<JS
            <script>
                const div = document.getElementById("produtos_incluidos_venda");
                let subTotalSpan = document.getElementById("venda_sub_total_span");
                let subTotalInput = document.getElementById("venda_sub_total_input").value;
                const dados = {$dados};
                dados.forEach((x) => {
                    div.innerHTML += `
                        <td>\${x.prd_id}</td>
                        <td>\${x.prd_descricao}</td>
                        <td>\${x.PEVI_QUANTIDADE}</td>
                        <td>R$ \${x.PEVI_PRECO_UNITARIO}</td>
                        <td>R$ \${x.PEVI_SUBTOTAL}</td>
                        <td>
                            <input type="checkbox" value="" name="prd_item_selecionado" id="prd_item_selecionado" class="form-check-input fs-5"/>
                        </td>`
                });

                subTotalSpan.innerHTML = `Subtotal: R$ {$subTotal}`; 
                subTotalInput = `{$subTotal}`; 
            </script>
        JS;
    }
}

if (!function_exists('onChangeTotal')) {

    function onChangeTotal()
    {

        return <<<JS
        <script>

            const desconto = document.getElementById("desconto_venda");
            const acrescimo = document.getElementById("acrescimo_venda");
            const inputTotal = document.getElementById("venda_total_input");
            const spanTotal = document.getElementById("venda_total_span");
            const venda = document.getElementById("venda_id");

            async function calcularValor() {
                const resposta = await fetch('http://fasmicro/Venda/calculaTotalVenda',
                    {
                        method : 'POST',
                        headers : {
                            'Content-Type' : 'application/json'
                        },
                        body : JSON.stringify(
                            {
                                desconto : desconto.value,
                                acrescimo : acrescimo.value,
                                venda : venda.value
                            }
                        )
                    }
                )
                return  await resposta.json();
            };

            async function atualizaValor() {
                const dados = await calcularValor();
                // receber o total e atualiza-ló
                console.log(dados.pev_total);
                spanTotal.innerHTML = 'Total: R$ ' + parseFloat(dados.pev_total).toLocaleString('pt-BR');
            }

            desconto.addEventListener('input', atualizaValor);
            acrescimo.addEventListener('input', atualizaValor);

        </script>
        JS;
    }
}


