<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Catálogo de Produtos</h4>
        <div class="d-flex gap-2">
            <a class="btn btn-primary-custom shadow-sm" href="/produto/formProduto/criar">
                <i class="bi bi-plus-lg me-2"></i> Novo Produto
            </a>
        </div>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-4">
            <div class="row">
                <form action="/produto/filtroListagemProduto" method="post" class="d-flex gap-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Pesquisar Produto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" list="produtos_lista" class="form-control border-start-0 rounded" placeholder="Descrição do Produto" name="filtroNomeProduto">
                            <datalist id="produtos_lista">
                                <?php foreach ($produtos as $key => $produto) : ?>
                                    <option value="<?= $produto['PRD_DESCRICAO'] ?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Categoria</label>
                        <select class="form-select" name="filtroCategoriaProduto">
                            <option value=" ">Todas as Categorias</option>
                            <?php foreach ($produtos as $key => $produto) : ?>
                                <option value="<?= $produto['PRD_CATEGORIA'] ?>"><?= $produto['PRD_CATEGORIA'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Estoque</label>
                        <select class="form-select" name="filtroEstoqueProduto">
                            <option value=" ">Todos</option>
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
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
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
                <tbody>
                    <?php foreach ($produtos as $key => $produto) : ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="img-produto d-flex align-items-center justify-content-center me-3 shadow-sm">
                                        <i class="bi bi-laptop"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= $produto['PRD_DESCRICAO'] ?></div>
                                        <small class="text-muted">COD: <?= $produto['PRD_ID'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-secondary"><?= $produto['PRD_CATEGORIA'] ?></span></td>
                            <td class="fw-bold">R$ <?= number_format($produto['PRD_PRECO_VENDA'], 2, ',', '.') ?></td>
                            <td><?= $produto['PRD_ESTOQUE'] ?></td>

                            <?php if ($produto['PRD_ESTOQUE'] == 0) : ?>
                                <td><span class="badge text-bg-danger">Sem Estoque</span></td>
                            <?php elseif ($produto['PRD_ESTOQUE'] <= $produto['PRD_ESTOQUE_MIN']): ?>
                                <td><span class="badge text-bg-warning">Reabastecer</span></td>
                            <?php else : ?>
                                <td><span class="badge text-bg-success">Disponível</span></td>
                            <?php endif; ?>

                            <td class="text-end">
                                <a class="btn btn-sm btn-light border" title="Visualizar"
                                    href="/produto/formProduto/visualizar/<?= $produto['PRD_ID'] ?>">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                                <a class="btn btn-sm btn-light border me-1" title="Editar"
                                    href="/produto/formProduto/editar/<?= $produto['PRD_ID'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a class="btn btn-sm btn-light border text-danger" title="Remover"
                                    href="/produto/formProduto/deletar/<?= $produto['PRD_ID'] ?>">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de <?= count($produtos) ?> registros encontrados</small>
        </div>
    </div>
</div>