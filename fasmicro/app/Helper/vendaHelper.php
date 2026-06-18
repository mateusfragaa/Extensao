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
    // itens que foram selecionados e adicionados na venda
    function carrega_itens_venda($dados)
    {
        $jsonDados = json_encode($dados);

        $subTotal = number_format(
            array_sum(array_column($dados, 'PEVI_SUBTOTAL')),
            2,
            ',',
            '.'
        );

        return <<<JS
    <script>

        const div = document.getElementById("produtos_incluidos_venda");
        const subTotalSpan = document.getElementById("venda_sub_total_span");
        const subTotalInput = document.getElementById("venda_sub_total_input");

        const dados = {$jsonDados};

        div.innerHTML = '';

        dados.forEach((x) => {

            div.innerHTML += `
                <tr>
                    <td>\${x.prd_id}</td>
                    <td>\${x.prd_descricao}</td>
                    <td>\${x.PEVI_QUANTIDADE}</td>
                    <td>R$ \${parseFloat(x.PEVI_PRECO_UNITARIO).toLocaleString('pt-BR')}</td>
                    <td>R$ \${parseFloat(x.PEVI_SUBTOTAL).toLocaleString('pt-BR')}</td>
                    <td>
                        <input
                            type="checkbox"
                            value="\${x.PEVI_ID}"
                            class="form-check-input fs-5 item-venda"
                        />
                    </td>
                </tr>
            `;
        });

        subTotalSpan.innerHTML = 'Subtotal: R$ {$subTotal}';
        subTotalInput.value = '{$subTotal}';

    </script>
    JS;
    }
}

// Atuliza os valores com acrescimo e desconto em tempo real
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
                spanTotal.innerHTML = 'Total: R$ ' + parseFloat(dados.PEV_TOTAL).toLocaleString('pt-BR');
            }

            desconto.addEventListener('input', atualizaValor);
            acrescimo.addEventListener('input', atualizaValor);

        </script>
        JS;
    }
}

// if (!function_exists('excluir_item_venda'))
// {
//     function excluir_item_venda($id_venda)
//     {
//         return <<<JS
//             <script>
//                 botao_excluir = document.getElementById('excluir_produto');
//                 botao_excluir.addEventListener('click', async (e) => {

//                 const selecionados = document.querySelectorAll('.item-venda:checked');

//                 const ids = [...selecionados]
//                     .map(item => item.value);

//                 await fetch('http://fasmicro/Venda/excluirProduto', {
//                     method: 'POST',
//                     headers: {
//                         'Content-Type': 'application/json'
//                     },
//                     body: JSON.stringify({
//                         produtos_excluir: ids
//                     })
//                 });

//                 selecionados.forEach(item => {
//                     // closest - procura o elemento pai mais próximo
//                     item.closest('tr').remove();
//                 });
//             });
//             </script>
//         JS;
//     }
// }

// function atualiza_total_subtotal_exclusao()
// {
//     return <<<JS
//     <script>

//         async function atualizaTotalSubtotalExclusao(id) {

//             const desconto = document.getElementById("desconto_venda");
//             const acrescimo = document.getElementById("acrescimo_venda");
//             const venda = document.getElementById("venda_id");

//             const resposta = await fetch(
//                 'http://fasmicro/Venda/carregaValorExclusao',
//                 {
//                     method: 'POST',
//                     headers: {
//                         'Content-Type': 'application/json'
//                     },
//                     body: JSON.stringify({
//                         'pedido_id' : id
//                     })
//                 }
//             );

//             const dados = await resposta.json();

//             document.getElementById("venda_total_span").innerHTML =
//                 'Total: R$ ' + parseFloat(dados.pev_total).toLocaleString('pt-BR');

//             document.getElementById("venda_sub_total_span").innerHTML =
//                 'Subtotal: R$ ' + parseFloat(dados.pev_sub_total).toLocaleString('pt-BR');
//         }

//     </script>
//     JS;
// }

// Exclui itens na venda
if(!function_exists('excluir_item_venda')){
    function excluir_item_venda($id_venda)
    {
        return <<<JS
    <script>

        const botao_excluir = document.getElementById('excluir_produto');

        botao_excluir.addEventListener('click', async () => {

            const selecionados = document.querySelectorAll('.item-venda:checked');

            const ids = [...selecionados]
                .map(item => item.value);

            await fetch('http://fasmicro/Venda/excluirProduto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    produtos_excluir: ids
                })
            });

            selecionados.forEach(item => {
                item.closest('tr').remove();
            });

            await atualizaTotalSubtotalExclusao({$id_venda});
        });

    </script>
    JS;
    }
}

// Atualiza os valores em tela  quando um item e excluido 
if(!function_exists('atualiza_total_subtotal_exclusao')){
    function atualiza_total_subtotal_exclusao()
    {
        return <<<JS
    <script>

        async function atualizaTotalSubtotalExclusao(id) {

            const resposta = await fetch(
                'http://fasmicro/Venda/carregaValorExclusao',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        pedido_id: id
                    })
                }
            );

            const dados = await resposta.json();

            document.getElementById("venda_total_span").innerHTML =
                'Total: R$ ' + parseFloat(dados.PEV_TOTAL).toLocaleString('pt-BR');

            document.getElementById("venda_sub_total_span").innerHTML =
                'Subtotal: R$ ' + parseFloat(dados.PEV_SUB_TOTAL).toLocaleString('pt-BR');
        }

    </script>
    JS;
    }
}