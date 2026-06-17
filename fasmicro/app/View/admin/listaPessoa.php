<?php

use Core\Library\Csrf;
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Pessoas</h4>
            <p class="text-muted small m-0">Gerenciamento de clientes e contatos registrados.</p>
        </div>
        <a href="/pessoa/formPessoa/insert" class="btn btn-primary-custom shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i> Cadastrar Pessoa
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <?php exibeAlerta(); ?>
            <form id="formFiltroPessoa" class="row g-2" action="/pessoa/filtroListagemPessoa" method="POST">
                <?= Csrf::getHiddenField() ?>
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="filtroNomePessoa" name="filtroNomePessoa"
                            class="form-control border-start-0"
                            placeholder="Buscar por nome ou CPF/CNPJ..."
                            value="<?= htmlspecialchars($_POST['filtroNomePessoa'] ?? '') ?>"
                            autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="ordemPessoa" name="ordemPessoa" class="form-select">
                        <option value="PES_NOME" <?= (($_POST['ordemPessoa'] ?? '') === 'PES_NOME') ? 'selected' : '' ?>>Ordem Alfabética</option>
                        <option value="CIDADE" <?= (($_POST['ordemPessoa'] ?? '') === 'CIDADE')   ? 'selected' : '' ?>>Por Cidade</option>
                        <option value="UF" <?= (($_POST['ordemPessoa'] ?? '') === 'UF')       ? 'selected' : '' ?>>Por UF</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th style="width:90px;">Cód.</th>
                        <th>Nome / Razão Social</th>
                        <th>CPF / CNPJ</th>
                        <th>Tipo</th>
                        <th>Cidade/UF</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pessoas)): ?>
                        <?php foreach ($pessoas as $p): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= str_pad($p['PES_ID'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder me-3 ">
                                            <?= strtoupper(substr($p['PES_NOME'], 0, 2)) ?>
                                        </div>
                                        <span class="fw-bold">
                                            <?= htmlspecialchars(strlen($p['PES_NOME']) > 30 ? substr($p['PES_NOME'], 0, 40)
                                                . '...' : $p['PES_NOME']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    // Aplica máscara conforme tipo e tamanho
                                    $doc = preg_replace('/\D/', '', $p['CPF_CNPJ'] ?? '');
                                    if (strlen($doc) === 11) {
                                        echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
                                    } elseif (strlen($doc) === 14) {
                                        echo preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
                                    } else {
                                        echo htmlspecialchars($p['CPF_CNPJ'] ?? '');
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?= ($p['TIPO_PESSOA'] ?? 'F') === 'J' ? 'bg-primary' : 'bg-secondary' ?>">
                                        <?= ($p['TIPO_PESSOA'] ?? 'F') === 'J' ? 'Jurídica' : 'Física' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(($p['CIDADE'] ?? '') . '/' . ($p['UF'] ?? '')) ?></td>
                                <td class="text-end">
                                    <a href="/pessoa/formPessoa/view/<?= $p['PES_ID'] ?>" class="btn btn-sm btn-light border" title="Visualizar"><i class="bi bi-eye text-primary"></i></a>
                                    <a href="/pessoa/formPessoa/update/<?= $p['PES_ID'] ?>" class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <a href="/pessoa/formPessoa/delete/<?= $p['PES_ID'] ?>" class="btn btn-sm btn-light border text-danger" title="Excluir"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Nenhuma pessoa encontrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3 text-center border-top">
            <small class="text-muted">Total de <?= count($pessoas ?? []) ?> registros encontrados</small>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formFiltro = document.getElementById('formFiltroPessoa');
        const inputBusca = document.getElementById('filtroNomePessoa');
        const selectOrdem = document.getElementById('ordemPessoa');
        const tabelaContainer = document.querySelector('.card.overflow-hidden');
        let timeoutBusca = null;

        function executarFiltro() {
            const formData = new FormData(formFiltro);
            tabelaContainer.style.opacity = '0.5';

            fetch(formFiltro.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const novaTab = doc.querySelector('.card.overflow-hidden');
                    if (novaTab) tabelaContainer.innerHTML = novaTab.innerHTML;
                    tabelaContainer.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Erro ao filtrar:', err);
                    tabelaContainer.style.opacity = '1';
                });
        }

        inputBusca.addEventListener('input', function() {
            clearTimeout(timeoutBusca);
            timeoutBusca = setTimeout(executarFiltro, 300);
        });

        selectOrdem.addEventListener('change', executarFiltro);

        formFiltro.addEventListener('submit', function(e) {
            e.preventDefault();
            executarFiltro();
        });
    });
</script>