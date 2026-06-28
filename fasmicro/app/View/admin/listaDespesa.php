<?= exibeAlerta() ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Contas a Pagar</h4>
            <p class="text-muted small m-0">Mais controle sobre despesas e compromissos financeiros.</p>
        </div>
        <div>
            <button id="add-filtros" class="btn btn-secondary-custom shadow-sm me-3">
                Adicionar filtros
            </button>
            <a
                class="btn btn-primary-custom shadow-sm"
                href="<?= baseUrl() . "{$controller}/form/insert" ?>"
            >
                <i class="bi bi-plus-lg me-2"></i> Nova Despesa
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom stat-card p-3">
                <small class="text-muted fw-bold">TOTAL A PAGAR (MÊS)</small>
                <h3 class="fw-bold m-0"><?= formatNumber($resumo['totalPagarMes']) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card vence-hoje p-3">
                <small class="text-muted fw-bold">VENCE HOJE</small>
                <h3 class="fw-bold m-0 text-warning"><?= formatNumber($resumo['venceHoje']) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom stat-card atrasado p-3">
                <small class="text-muted fw-bold">DÉBITOS EM ATRASO</small>
                <h3 class="fw-bold m-0 text-danger"><?= formatNumber($resumo['debitosAtraso']) ?></h3>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-4 d-none" id="div-filtros">
        <div class="card-body p-4">
            <form class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted" for="idFornecedor">
                        Código fornecedor
                    </label>
                    <input type="number" class="form-control" name="idFornecedor" id="idFornecedor">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted" for="nomeFornecedor">
                        Nome fornecedor
                    </label>
                    <input type="text" class="form-control" placeholder="Ex: Totvs..." name="nomeFornecedor" id="nomeFornecedor">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted" for="vencimento">Vencimento</label>
                    <select class="form-select" name="vencimento" id="vencimento">
                        <option selected value="Todos">Todos</option>
                        <option value="vencidos">Vencidos</option>
                        <option value="aVencer">A vencer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted" for="status">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="" disabled hidden selected></option>
                        <option value="Todos">Todos os Status</option>
                        <option value="A">Aberto</option>
                        <option value="P">Pago</option>
                        <option value="C">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom card-ajuste-footer">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Vencimento</th>
                        <th>Fornecedor / Descrição</th>
                        <th>Valor</th>
                        <th class="text-center">Saldo</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista as $v): ?>
                        <tr>
                            <td>
                                <a href="<?= baseUrl() . "pagamento?despesa={$v['PAG_ID']}" ?>">
                                    <i class="bi bi-card-list"></i>
                                </a>
                            </td>
                            <td><?= formatDate($v['PAG_DATA_VENCIMENTO']) ?></td>
                            <td>
                                <strong>
                                    <?= "{$v['CPF_CNPJ']} - {$v['PES_NOME']}" ?>
                                </strong>
                                <br>
                                <?= $v['PAG_DESCRICAO'] ?>
                            </td>
                            <td class="fw-bold text-primary text-end"><?= formatNumber($v['PAG_VALOR']) ?></td>
                            <td class="text-end"><?= formatNumber($v['PAG_VALOR_ABERTO']) ?></td>
                            <td><?= $aStatus[$v['PAG_STATUS']] ?? '' ?></td>
                            <td>
                                <a
                                    class="btn btn-sm btn-light border" 
                                    href="<?= baseUrl() . "{$controller}/form/view/" . $v['PAG_ID'] ?>"
                                    title="Visualizar"
                                >
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <a
                                    class="btn btn-sm btn-light border" 
                                    href="<?= baseUrl() . "{$controller}/form/update/" . $v['PAG_ID'] ?>"
                                    title="Editar"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top ">
            <small class="text-muted">Total de <?= count($lista) ?> registros encontrados</small>
        </div>
    </div>
</div>

<script>
    let exibindoFiltros = false

    document.querySelector('#add-filtros').addEventListener('click', () => {
        const divFiltros = document.querySelector('#div-filtros')

        if (exibindoFiltros) {
            divFiltros.classList.add('d-none')
        } else {
            divFiltros.classList.remove('d-none')
        }

        exibindoFiltros = !exibindoFiltros
    })
</script>