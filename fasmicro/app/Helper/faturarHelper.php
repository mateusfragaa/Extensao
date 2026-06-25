<?php

if (!function_exists('aumenta_quantidade')) {
    function aumenta_quantidade()
    {
        return <<<JS
            <script>
                function aumenta_quantidade()
                {
                    let quantidade = document.getElementById('quantidade');
                     let formaPagamento = document.getElementById('forma_pagamento');
                    if (formaPagamento.value === '1' ||formaPagamento.value === '2') {
                        quantidade.value = 1;
                        return;
                    }
                    quantidade.value = Number(quantidade.value) + 1;
                }
            </script>
        JS;
    }
}

if (!function_exists('diminui_quantidade')) {
    function diminui_quantidade()
    {
        return <<<JS
            <script>
                function diminui_quantidade()
                {
                    let quantidade = document.getElementById('quantidade');
                     let formaPagamento = document.getElementById('forma_pagamento');

                    if (formaPagamento.value === '1' ||formaPagamento.value === '2') {
                        quantidade.value = 1;
                        return;
                    }

                    if (Number(quantidade.value) === 0) {
                        alert("Quantidade já está zerada");
                        return;
                    }

                    quantidade.value = Number(quantidade.value) - 1;
                }
            </script>
        JS;
    }
}


// // Exclui itens na venda
// if(!function_exists('excluir_recebimento_venda')){
//     function excluir_recebimento_venda($id_venda)
//     {
//         return <<<JS
//     <script>

//         const botao_excluir = document.getElementById('excluir_recebimento');

//         botao_excluir.addEventListener('click', async () => {

//             const selecionados = document.querySelectorAll('.item-recebimento:checked');

//             const ids = [...selecionados]
//                 .map(item => item.value);

//             await fetch('http://fasmicro/faturarVenda/excluirRecebimento', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json'
//                 },
//                 body: JSON.stringify({
//                     recebimentos_excluir: ids,
//                     id_pedido : {$id_venda}
//                 })
//             });

//             selecionados.forEach(item => {
//                 item.closest('tr').remove();
//             });

//             // await atualizaTotalSubtotalExclusao({$id_venda});
//         });

//     </script>
//     JS;
//     }
// }

// Atualiza os valores em tela  quando um item e excluido 
// if(!function_exists('atualiza_total_subtotal_exclusao')){
//     function atualiza_total_subtotal_exclusao()
//     {
//         return <<<JS
//     <script>

//         async function atualizaTotalSubtotalExclusao(id) {

//             const resposta = await fetch(
//                 'http://fasmicro/Venda/carregaValorExclusao',
//                 {
//                     method: 'POST',
//                     headers: {
//                         'Content-Type': 'application/json'
//                     },
//                     body: JSON.stringify({
//                         pedido_id: id
//                     })
//                 }
//             );

//             const dados = await resposta.json();

//             document.getElementById("venda_total_span").innerHTML =
//                 'Total: R$ ' + parseFloat(dados.PEV_TOTAL).toLocaleString('pt-BR');

//             document.getElementById("venda_sub_total_span").innerHTML =
//                 'Subtotal: R$ ' + parseFloat(dados.PEV_SUB_TOTAL).toLocaleString('pt-BR');
//         }

//     </script>
//     JS;
//     }
// }


