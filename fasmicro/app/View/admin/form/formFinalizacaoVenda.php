<?php
$info_venda = $data['data']['info_venda'] ?? [];
$formas_pagamento = $data['data']['formas_pagamento'] ?? [];
var_dump($_SESSION);
?>
<div class="container-fluid p-5" style="max-height: 100vh; overflow-y: auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><i class="bi bi-box-seam me-2 text-primary"></i>Finalizar Pedido de Venda #<?= $info_venda['PEV_ID'] ?></h4>
        <a class="btn btn-outline-secondary btn-sm px-3" href="/venda/formVenda/update/<?= $info_venda['PEV_ID'] ?>">
            <i class="bi bi-arrow-left"></i> Voltar ao Pedido
        </a>
    </div>
    <?= exibeAlerta() ?>
    <div>
        <div class="row g-4 mb-4 align-items-stretch">

            <div class="col-md-4 d-flex flex-column gap-4">

                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3">
                    <h6 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-person-check me-2 text-primary"></i>Identificação do Cliente
                    </h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label mb-1 small fw-bold text-muted" style="font-size: 0.75rem;">Cliente</label>
                            <input type="text" class="form-control bg-light border-0 fw-bold text-dark" value="<?= $info_venda['PES_NOME'] ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1 small fw-bold text-muted" style="font-size: 0.75rem;">Data do Faturamento</label>
                            <input type="date" disabled class="form-control bg-light border-0" value="<?= $info_venda['PEV_DATA_VENDA'] ?>">
                        </div>
                    </div>
                </div>

                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3 flex-grow-1">
                    <h6 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-credit-card me-2 text-primary"></i>Forma de Pagamento & Ajustes
                    </h6>
                    <div class="row g-3">
                        <form method="post" action="/faturarVenda/formFaturar/insert/<?= $info_venda['PEV_ID'] ?>">
                            <div class="col-12">
                                <label class="form-label mb-1 small fw-bold text-muted" style="font-size: 0.75rem;">Forma de Pagamento</label>
                                <select class="form-select bg-light border-0" id="forma_pagamento" name="forma_pagamento">
                                    <?php foreach ($formas_pagamento as $forma): ?>
                                        <option value="<?= $forma['TDC_ID'] ?>"><?= $forma['TDC_DESCRICAO'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label mb-1 small fw-bold text-muted" style="font-size: 0.75rem;">Condição de Parcelamento</label>
                                <div class="input-group mb-3">
                                    <button type="button" class="input-group-text" onclick="diminui_quantidade()">-</button>
                                    <input type=" text" class="form-control text-center" value="1" aria-label="Quantidade" id="quantidade" name="quantidade">
                                    <button type="button" class="input-group-text" onclick="aumenta_quantidade()">+</button>
                                    <input type=" text" class="form-control text-end" placeholder="0,00" aria-label="Valor" id="valor" name="valor">
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-primary w-100" type="submit" id="receber">Receber</button>
                                </div>
                            </div>
                        </form>

                        <div class="col-12 mt-2 border-top pt-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label mb-1 small fw-bold text-muted" style="font-size: 0.75rem;">Acréscimo (R$)</label>
                                    <input type="text" class="form-control bg-light border-0" disabled placeholder="0,00" value="<?= number_format($info_venda['PEV_ACRESCIMO'], 2, ',', '.') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label mb-1 small fw-bold text-muted" style="font-size: 0.75rem;">Desconto (R$)</label>
                                    <input type="text" class="form-control bg-light border-0" disabled placeholder="0,00" value="<?= number_format($info_venda['PEV_DESCONTO'], 2, ',', '.') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-8 d-flex flex-column gap-4">
                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3 flex-grow-1">
                    <h6 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-list-check me-2 text-primary"></i>Contas a Receber Geradas
                    </h6>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table align-middle table-sm m-0" style="font-size: 0.85rem;">
                            <thead class="bg-white border-bottom sticky-top">
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>Parcela</th>
                                    <th>Vencimento</th>
                                    <th>Forma</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1 / 2</td>
                                    <td>20/07/2026</td>
                                    <td><span class="badge bg-light text-dark border">Cartão de Crédito</span></td>
                                    <td class="text-end fw-bold text-dark">R$ 750,00</td>
                                </tr>
                                <tr>
                                    <td>2 / 2</td>
                                    <td>20/08/2026</td>
                                    <td><span class="badge bg-light text-dark border">Cartão de Crédito</span></td>
                                    <td class="text-end fw-bold text-dark">R$ 750,00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3">
                    <h6 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-cart3 me-2 text-primary"></i>Itens do Pedido para Conferência
                    </h6>
                    <div class="table-responsive" style="max-height: 215px; overflow-y: auto;">
                        <table class="table align-middle table-sm m-0" style="font-size: 0.85rem;">
                            <thead class="bg-white border-bottom sticky-top">
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>Cód</th>
                                    <th>Descrição do Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>042</td>
                                    <td>Notebook Corporate Intel i5 16GB SSD 512GB</td>
                                    <td class="text-center">1</td>
                                    <td class="text-end">R$ 1.200,00</td>
                                    <td class="text-end fw-bold">R$ 1.200,00</td>
                                </tr>
                                <tr>
                                    <td>118</td>
                                    <td>Mouse Sem Fio Ergonômico Premium</td>
                                    <td class="text-center">2</td>
                                    <td class="text-end">R$ 150,00</td>
                                    <td class="text-end fw-bold">R$ 300,00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card card-custom p-4 shadow-sm border-0 bg-white rounded-3">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3 text-start">
                            <div class="text-muted small mb-0" style="font-size: 0.8rem;">Subtotal da Venda</div>
                            <div class="fw-bold fs-5 text-secondary">R$ <?= number_format($info_venda['PEV_SUB_TOTAL'], 2, ',', '.') ?></div>
                        </div>
                        <div class="col-md-4 text-start text-md-end">
                            <div class="text-primary small mb-0 fw-bold" style="font-size: 0.8rem;">Total Geral a Pagar</div>
                            <div class="fw-bold fs-2 text-primary">R$ <?= number_format($info_venda['PEV_TOTAL'], 2, ',', '.') ?></div>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold fs-6 shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Confirmar e Faturar Venda
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <?= aumenta_quantidade() ?>
    <?= diminui_quantidade() ?>
    <?= receber_venda($info_venda['PEV_ID']) ?>
</div>