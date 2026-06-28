<?php

if (!function_exists('filtroRecebimetos')) {

    function filtroRecebimetos(): string
    {
        return <<<'JS'
            <script>
                document.getElementById('form_filtro_recebimento').addEventListener('submit', (e) => {
                    e.preventDefault();

                    const nomeFiltro = document.getElementById('filtro_nome').value.toLowerCase().trim();
                    const statusFiltro = document.getElementById('filtro_status').value;
                    const dataFiltro = document.getElementById('filtro_data').value;

                    const linhas = document.querySelectorAll('#tabela_recebimentos tbody tr');

                    linhas.forEach((linha) => {
                        const nomeLinha = linha.dataset.nome.toLowerCase();
                        const statusLinha = linha.dataset.status;
                        const dataLinha = linha.dataset.vencimento;

                        let mostrar = true;

                        if (nomeFiltro && !nomeLinha.includes(nomeFiltro)) {
                            mostrar = false;
                        }

                        if (statusFiltro && statusLinha !== statusFiltro) {
                            mostrar = false;
                        }

                        if (dataFiltro && dataLinha !== dataFiltro) {
                            mostrar = false;
                        }

                        linha.style.display = mostrar ? '' : 'none';
                    });
            });
            </script>
        JS;
    }
}

