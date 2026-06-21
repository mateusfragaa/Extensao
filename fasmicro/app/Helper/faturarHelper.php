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

if (!function_exists('receber_venda')) {

    function receber_venda($id_venda)
    {
        return <<<JS
        <script>
            let quantidade_parcela = document.getElementById('quantidade').value;
            let valor = document.getElementById('valor').value;
            let venda = {$id_venda};
            
            async function envio_recebimento() {
                
            }

            document.getElementById('receber').addEventListener('click',(x) => {
            



            });
        </script>
    JS;
    }
}


