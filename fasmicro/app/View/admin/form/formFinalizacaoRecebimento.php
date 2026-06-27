<div class="container-fluid p-4" style="max-height: 100vh; overflow-y: hidden;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold m-0">
            <i class="bi bi-collection-check me-2 text-primary"></i>Borderô de Baixa de Recebimentos
        </h4>
        <div>
            <a class="btn btn-outline-secondary btn-sm px-3" href="/financeiro/recebimentos">
                <i class="bi bi-arrow-left"></i> Voltar aos Recebimentos
            </a>
        </div>
    </div>

    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 py-2" role="alert">
        <i class="bi bi-info-circle me-2"></i> Selecione os títulos na lista superior e informe o valor pago abaixo para liquidar o lote.
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <form action="/bordero/processarBaixa" method="post" id="form_bordero_baixa" class="d-flex flex-column gap-3" style="height: calc(100vh - 160px);">
        <input type="hidden" name="csrf_token" value="exemplo_token">

        <div class="card card-custom p-3 shadow-sm border-0 bg-white rounded-3 d-flex flex-column" style="max-height: 220px; min-height: 180px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold m-0 text-dark" style="font-size: 0.9rem;">
                    <i class="bi bi-hourglass-split me-2 text-primary"></i>1. Selecione os Títulos para Baixa
                </h6>
                <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.75rem;">
                    Multi-seleção Ativa
                </span>
            </div>

            <div class="table-responsive flex-grow-1" style="overflow-y: auto;">
                <table class="table align-middle table-sm m-0" style="font-size: 0.85rem;">
                    <thead class="bg-white border-bottom sticky-top">
                        <tr class="text-muted" style="font-size: 0.75rem;">
                            <th width="40" class="text-center">
                                <input type="checkbox" id="selecionar_todos_rec" class="form-check-input" onclick="toggleTodosRecebimentos(this)">
                            </th>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Documento / Origem</th>
                            <th>Vencimento</th>
                            <th class="text-end">Valor Original</th>
                            <th class="text-end">Juros/Multa</th>
                            <th class="text-end">Desconto</th>
                            <th class="text-end">Valor Aberto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="recebimentos_ids[]" class="item-baixa form-check-input fs-5" value="1024" data-valor="1500.00" onclick="calcularTotaisBordero()" />
                            </td>
                            <td>1024</td>
                            <td class="fw-bold text-dark">Mendes & Silva Ltda</td>
                            <td><span class="badge bg-light text-dark border">NF-8952</span></td>
                            <td>15/07/2026</td>
                            <td class="text-end">R$ 1.500,00</td>
                            <td class="text-end text-danger">+ R$ 0,00</td>
                            <td class="text-end text-success">- R$ 0,00</td>
                            <td class="text-end fw-bold text-dark">R$ 1.500,00</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="recebimentos_ids[]" class="item-baixa form-check-input fs-5" value="1025" data-valor="450.50" onclick="calcularTotaisBordero()" />
                            </td>
                            <td>1025</td>
                            <td class="fw-bold text-dark">Carlos Eduardo Souza</td>
                            <td><span class="badge bg-light text-dark border">DUP-451</span></td>
                            <td>18/07/2026</td>
                            <td class="text-end">R$ 450,50</td>
                            <td class="text-end text-danger">+ R$ 0,00</td>
                            <td class="text-end text-success">- R$ 0,00</td>
                            <td class="text-end fw-bold text-dark">R$ 450,50</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="recebimentos_ids[]" class="item-baixa form-check-input fs-5" value="1026" data-valor="2300.00" onclick="calcularTotaisBordero()" />
                            </td>
                            <td>1026</td>
                            <td class="fw-bold text-dark">Ana Beatriz Oliveira</td>
                            <td><span class="badge bg-light text-dark border">PEV-3312</span></td>
                            <td>20/07/2026</td>
                            <td class="text-end">R$ 2.300,00</td>
                            <td class="text-end text-danger">+ R$ 0,00</td>
                            <td class="text-end text-success">- R$ 0,00</td>
                            <td class="text-end fw-bold text-dark">R$ 2.300,00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-3 flex-grow-1" style="overflow-y: auto;">

            <div class="col-md-7 d-flex flex-column justify-content-between">
                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-cash-coin me-2 text-primary"></i>2. Forma de Liquidação & Totais
                        </h6>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label mb-1 small fw-bold text-muted">Conta / Caixa de Destino</label>
                                <select class="form-select bg-light border-0" id="conta_destino" name="conta_destino">
                                    <option value="">Selecione uma conta bancária...</option>
                                    <option value="1">Caixa Geral da Empresa</option>
                                    <option value="2">Banco do Brasil - Conta Corrente</option>
                                    <option value="3">Itaú Unibanco</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label mb-1 small fw-bold text-muted">Forma de Recebimento</label>
                                <select class="form-select bg-light border-0" id="forma_pagamento" name="forma_pagamento">
                                    <option value="1">Dinheiro</option>
                                    <option value="2">PIX</option>
                                    <option value="3">Cartão de Crédito</option>
                                    <option value="4">Boleto Bancário</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label mb-1 small fw-bold text-muted">Valor Informado (R$)</label>
                                <input type="text" class="form-control text-end fw-bold text-primary fs-5" placeholder="0,00" id="valor_pago" name="valor_pago" oninput="calcularTotaisBordero()">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 mt-3 border-top">
                        <div class="d-flex justify-content-between text-center bg-light p-3 rounded-3 border mb-3">
                            <div>
                                <div class="text-muted small mb-0" style="font-size: 0.75rem;">Total Selecionado</div>
                                <div class="fw-bold fs-5 text-dark" id="lbl_total_selecionado">R$ 0,00</div>
                            </div>
                            <div class="border-start mx-2"></div>
                            <div>
                                <div class="text-success small mb-0 fw-bold" style="font-size: 0.75rem;">Total Informado</div>
                                <div class="fw-bold fs-5 text-success" id="lbl_total_informado">R$ 0,00</div>
                            </div>
                            <div class="border-start mx-2"></div>
                            <div>
                                <div class="text-primary small mb-0 fw-bold" style="font-size: 0.75rem;">Falta Pagar</div>
                                <div class="fw-bold fs-5 text-primary" id="lbl_total_restante">R$ 0,00</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold fs-6 shadow-sm" id="btn_confirmar_baixa">
                            <i class="bi bi-check-circle-fill me-2"></i> Confirmar Baixa do Borderô
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-5 d-flex flex-column justify-content-between">
                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-sliders me-2 text-primary"></i>Ações em Lote
                        </h6>
                        <p class="text-muted small">
                            Utilize o botão abaixo se precisar estornar, cancelar ou limpar as provisões financeiras dos títulos que foram marcados na lista acima de uma só vez.
                        </p>
                    </div>

                    <div class="pt-3 border-top">
                        <button type="submit" class="btn btn-outline-danger w-100 py-3 fw-bold" formmethod="post" formaction="/bordero/limparFinanceiroLote" id="btn_limpar_financeiro">
                            <i class="bi bi-trash3 me-2"></i> Limpar Financeiros Selecionados
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    function toggleTodosRecebimentos(master) {
        const checkboxes = document.querySelectorAll('.item-baixa');
        checkboxes.forEach(cb => cb.checked = master.checked);
        calcularTotaisBordero();
    }

    function calcularTotaisBordero() {
        let totalSelecionado = 0;
        const checkboxes = document.querySelectorAll('.item-baixa:checked');

        checkboxes.forEach(cb => {
            totalSelecionado += parseFloat(cb.getAttribute('data-valor')) || 0;
        });

        let inputPagoStr = document.getElementById('valor_pago').value;
        inputPagoStr = inputPagoStr.replace(/\./g, '').replace(',', '.');
        let totalInformado = parseFloat(inputPagoStr) || 0;

        let faltaPagar = totalSelecionado - totalInformado;
        if (faltaPagar < 0) faltaPagar = 0;

        document.getElementById('lbl_total_selecionado').innerText = totalSelecionado.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('lbl_total_informado').innerText = totalInformado.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('lbl_total_restante').innerText = faltaPagar.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });

        const lblRestante = document.getElementById('lbl_total_restante');
        if (faltaPagar === 0 && totalSelecionado > 0) {
            lblRestante.className = 'fw-bold fs-5 text-success';
        } else {
            lblRestante.className = 'fw-bold fs-5 text-primary';
        }
    }
</script>