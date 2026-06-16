<?php
$produtos = (isset($data['data']['produtos'])) ? $data['data']['produtos'] : [];
$pessoas = (isset($data['data']['pessoas'])) ? $data['data']['pessoas'] : [];
$info_venda = (isset($data['data']['info_venda'])) ? $data['data']['info_venda'] : [];
$status_venda = (isset($data['data']['status_venda'])) ? $data['data']['status_venda'] : [];
$id_venda = isset($data['data']['produtos_pedidos'][0]['PEVI_VENDA_ID']) ? $data['data']['produtos_pedidos'][0]['PEVI_VENDA_ID'] : $data['data']['id_venda'];
$acao_venda = $data['data']['acao_venda'] ?? '';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Pedido de Venda<?= formSubTitulo($acao_venda) ?></h4>
        <a class="btn btn-outline-secondary btn-sm" href="/venda/">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?= exibeAlerta() ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-custom p-4">
                <form class="row g-3" method="post" action="/venda/<?= $data['data']['action_form'] ?>/form/<?= $id_venda ?>">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Cliente</label>
                        <select class="form-select bg-light border-0" name="cliente_venda">
                            <?php foreach ($pessoas as $key => $pessoa): ?>
                                <option
                                    <?= !empty($info_venda) ? ($pessoa['PES_ID'] == $info_venda['PEV_CLIENTE_ID']) ? 'selected' : '' : '' ?>
                                    value="<?= $pessoa['PES_ID'] ?>">
                                    <?= $pessoa['PES_NOME'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Data Venda</label>
                        <input type="date" class="form-control bg-light border-0"
                            value="<?= isset($info_venda['PEV_DATA_VENDA']) ? $info_venda['PEV_DATA_VENDA'] : date("Y-m-d") ?>" name="data_venda">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Status</label>
                        <!-- Definir o array statico para selecionar o status $info_venda['PEV_STATUS'] -->
                        <select class="form-select bg-light border-0" name="status_venda">
                            <?php foreach ($status_venda as $key => $status): ?>
                                <?php if ($key == 'A' || $key == 'O'): ?>
                                    <option
                                        <?= !empty($info_venda) ? ($key == $info_venda['PEV_STATUS']) ? 'selected' : '' : '' ?>
                                        value="<?= $key ?>">
                                        <?= $status_venda[$key] ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <?php if (count($info_venda) > 0 ) : ?>
                            <label class="form-label small fw-bold">Acréscimo</label>
                            <input type="number" class="form-control bg-light border-0" placeholder="0,00" min="0" name="acrescimo_venda" id="acrescimo_venda"
                                value="<?= $info_venda['PEV_ACRESCIMO'] ?? '' ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <?php if (count($info_venda) > 0) : ?>
                            <label class="form-label small fw-bold">Desconto</label>
                            <input type="number" class="form-control bg-light border-0" placeholder="0,00" min="0" name="desconto_venda" id="desconto_venda"
                                value="<?= $info_venda['PEV_DESCONTO'] ?? '' ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-12 mt-4 p-3 bg-light rounded text-end">
                        <div class="small text-muted">
                            <!-- E adicionado por meio do JS -->
                            <span id="venda_sub_total_span">Subtotal: R$ 0,00</span>
                            <input type="hidden" name="venda_sub_total" value="" id="venda_sub_total_input">
                        </div>
                        <div class="fw-bold fs-4 text-primary">
                            <span id="venda_total_span">
                                <?= isset($info_venda['PEV_TOTAL']) ?
                                    'Total: R$ ' . number_format($info_venda['PEV_TOTAL'], '2', ',', '.') :
                                    'Total: R$ 0.00'  ?>
                            </span>
                            <!-- <input type="hidden" name="venda_total_input" id="venda_total_input" value=""> -->
                        </div>
                        <input type="hidden" name="venda_id" value="<?= $id_venda ?>" id="venda_id">
                    </div>
                    <?php if (count($data['data']['produtos_pedidos']) > 0 && $acao_venda == 'update' || $acao_venda != 'view') : ?>
                        <button type="submit" class="btn btn-primary-custom w-100 py-2 mt-3">Salvar Pedido</button>
                    <?php else: ?>
                        <p class="text-center text-muted">Acrescente algum item para iniciar o pedido.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom p-4">
                <h6 class="fw-bold mb-3">Produtos do Pedido</h6>
                <div class="mb-3 d-flex justify-content-between">
                    <div>
                        <?php if ($id_venda) : ?>
                            <p class="text-muted h4">Número do Pedido <?= $id_venda ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($acao_venda != 'delete' && $acao_venda != 'view') : ?>
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Incluir Produto
                        </button>
                    <?php endif; ?>
                </div>

                <div class="scroll-div">
                    <table class="table align-middle m-0">
                        <thead class="sticky-top bg-white border-bottom">
                            <tr class="small text-muted">
                                <th>Cód</th>
                                <th>Descrição</th>
                                <th>Qtd</th>
                                <th>Preço Unitário</th>
                                <th>Sub-Total</th>
                            </tr>
                        </thead>
                        <tbody id="produtos_incluidos_venda">
                            <tr>
                                <!-- Preenchido com JavaScript -->
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-row gap-2 mt-2">
                    <!-- <a href="/faturarVenda/" class="btn btn-primary w-100 py-2">Faturar Pedido</a> -->
                    <?php if (count($data['data']['produtos_pedidos']) > 0 && $acao_venda == 'update' || $acao_venda == 'insert') : ?>
                        <a href="/faturarVenda/<?= $action ?? 'faturar' ?>/<?= $id_venda ?>" class="btn btn-primary w-100 py-2">Faturar Pedido</a>
                        <button type="button" class="btn btn-danger w-100 py-2" id="excluir_produto">Excluir Produto</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Pesquisa de produtos</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Formulário pesquisa de produto -->
                <div class="row mb-4">
                    <form class="d-flex gap-3" id="form_filtro_modal_venda">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Pesquisar Produto</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bi bi-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 rounded" placeholder="Descrição do Produto" name="filtroNomeProduto" id="prd_filtro_descricao_venda">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Categoria</label>
                            <select class="form-select" name="filtroCategoriaProduto" id="prd_filtro_categoria_venda">
                                <option value="">Todas as Categorias</option>
                                <?php foreach ($produtos as $key => $produto) : ?>
                                    <option value="<?= $produto['PRD_CATEGORIA'] ?>"><?= $produto['PRD_CATEGORIA'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Estoque</label>
                            <select class="form-select" name="filtroEstoqueProduto" id="prd_filtro_estoque_venda">
                                <option value="">Todos</option>
                                <option value="sem">Sem Estoque</option>
                                <option value="min">Abaixo do Mínimo</option>
                                <option value="disp">Em Estoque</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-outline-primary w-100 fw-bold" type="submit">Filtrar</a>
                        </div>
                    </form>
                </div>

                <form action="/venda/<?= $data['data']['action_form_modal'] ?? "inicioVenda/modal" ?>/<?= $id_venda
                                                                                                        ?>"
                    method="post" id="form_escolha_prd_modal_venda">
                    <div class="table-responsive tabela-scroll">
                        <table class="table table-custom m-0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Preço Venda</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabela_produtos_modal">
                                <?php foreach ($produtos as $key => $produto) : ?>
                                    <tr
                                        data-descricao="<?= mb_strtolower($produto['PRD_DESCRICAO'], 'UTF-8') ?>"
                                        data-categoria="<?= mb_strtolower($produto['PRD_CATEGORIA'], 'UTF-8') ?>"
                                        data-estoque="<?= $produto['PRD_ESTOQUE'] ?>"
                                        data-minimo="<?= $produto['PRD_ESTOQUE_MIN'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="img-produto d-flex align-items-center justify-content-center me-3 shadow-sm">
                                                    <i class="bi bi-laptop"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= $produto['PRD_DESCRICAO'] ?></div>
                                                    <small class="text-muted">COD: <?= $produto['PRD_ID'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-secondary"><?= $produto['PRD_CATEGORIA'] ?></span>
                                        </td>
                                        <td class="fw-bold">
                                            R$ <?= number_format($produto['PRD_PRECO_VENDA'], 2, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?= $produto['PRD_ESTOQUE'] ?>
                                        </td>

                                        <?php if ($produto['PRD_ESTOQUE'] == 0) : ?>
                                            <td>
                                                <span class="badge text-bg-danger">Sem Estoque</span>
                                            </td>
                                        <?php elseif ($produto['PRD_ESTOQUE'] <= $produto['PRD_ESTOQUE_MIN']): ?>
                                            <td>
                                                <span class="badge text-bg-warning">Reabastecer</span>
                                            </td>
                                        <?php else : ?>
                                            <td>
                                                <span class="badge text-bg-success">Disponível</span>
                                            </td>
                                        <?php endif; ?>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-3">
                                                <input
                                                    type="number"
                                                    name="produto[<?= $produto['PRD_ID'] ?>][qtd]"
                                                    class="form-control w-25 fs-5"
                                                    value=""
                                                    min="0">

                                                <input
                                                    type="checkbox"
                                                    name="produto[<?= $produto['PRD_ID'] ?>][selecionado]"
                                                    value=""
                                                    class="form-check-input fs-5 m-0">

                                                <input
                                                    type="hidden"
                                                    name="produto[<?= $produto['PRD_ID'] ?>][valorVenda]"
                                                    value="<?= $produto['PRD_PRECO_VENDA'] ?>">
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-basket2"></i> Escolher</button>
                    </div>
                </form>
            </div>
        </div>
        <?= jsFormHandler() ?>
        <?= carrega_itens_venda($data['data']['produtos_pedidos'] ?? []); ?>
        <?= onChangeTotal(); ?>
        <?= atualiza_total_subtotal_exclusao() ?>
        <?= excluir_item_venda($id_venda); ?>
    </div>