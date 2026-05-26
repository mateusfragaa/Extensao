<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Pessoas</h4>
            <p class="text-muted small m-0">Gerenciamento de clientes e contatos registrados.</p>
        </div>
        <button type="button" class="btn btn-primary-custom shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastroPessoa" onclick="carregarFormPessoa('insert')">
            <i class="bi bi-person-plus-fill me-2"></i> Cadastrar Pessoa
        </button>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <?php exibeAlerta(); ?>
            <form class="row g-2" action="/pessoa/filtroListagemPessoa" method="POST">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" name="filtroNomePessoa" class="form-control border-start-0"
                            placeholder="Buscar por nome..." value="<?= $_POST['filtroNomePessoa'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="ordemPessoa" class="form-select">
                        <option value="PES_NOME" <?= (($_POST['ordemPessoa'] ?? '') == 'PES_NOME') ? 'selected' : '' ?>>Ordem Alfabética</option>
                        <option value="CIDADE" <?= (($_POST['ordemPessoa'] ?? '') == 'CIDADE') ? 'selected' : '' ?>>Por Cidade</option>
                        <option value="UF" <?= (($_POST['ordemPessoa'] ?? '') == 'UF') ? 'selected' : '' ?>>Por UF</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom m-0">
                <thead>
                    <tr>
                        <th style="width: 100px;">Cód.</th>
                        <th>Nome Completo</th>
                        <th>CPF / CNPJ</th>
                        <th>Cidade/UF</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($pessoas) && count($pessoas) > 0): ?>
                        <?php foreach ($pessoas as $p): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= str_pad($p['PES_ID'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder me-3">
                                            <?= strtoupper(substr($p['PES_NOME'], 0, 2)) ?>
                                        </div>
                                        <span class="fw-bold"><?= $p['PES_NOME'] ?></span>
                                    </div>
                                </td>
                                <td><?= $p['CPF_CNPJ'] ?></td>
                                <td><?= ($p['CIDADE'] ?? '') . '/' . ($p['UF'] ?? '') ?></td>
                                <td class="text-end">
                                    <a href="/pessoa/formPessoa/view/<?= $p['PES_ID'] ?>" class="btn btn-sm btn-light border" title="Visualizar"><i class="bi bi-eye text-primary"></i></a>
                                    <a href="/pessoa/formPessoa/update/<?= $p['PES_ID'] ?>" class="btn btn-sm btn-light border" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <a href="/pessoa/formPessoa/delete/<?= $p['PES_ID'] ?>" class="btn btn-sm btn-light border text-danger" title="Excluir"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Nenhuma pessoa encontrada.</td>
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

<!-- Modal de Cadastro/Edição -->
<div class="modal fade" id="modalCadastroPessoa" tabindex="-1" aria-labelledby="modalCadastroPessoaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalCadastroPessoaLabel">Cadastro de Pessoa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="conteudoModalPessoa">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando formulário...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function carregarScriptsForm(doc) {
    const scripts = doc.querySelectorAll('script');
    scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        newScript.text = oldScript.text;
        document.body.appendChild(newScript).parentNode.removeChild(newScript);
    });

    // Re-ajustar botão cancelar
    const container = document.getElementById('conteudoModalPessoa');
    const btnCancelar = container.querySelector('a[href="/pessoa/"]');
    if (btnCancelar) {
        btnCancelar.outerHTML = '<button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>';
    }
}

function carregarFormPessoa(acao, id = '') {
    const url = `/pessoa/formPessoa/${acao}/${id}`;
    const container = document.getElementById('conteudoModalPessoa');
    
    // Limpa e mostra loading
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2">Carregando formulário...</p>
        </div>
    `;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Extrair apenas o conteúdo do card para não duplicar containers
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const formContent = doc.querySelector('.card-custom');
            
            if (formContent) {
                // Ajustar o título da modal baseado no conteúdo
                const titulo = doc.querySelector('h4')?.innerText || 'Cadastro de Pessoa';
                document.getElementById('modalCadastroPessoaLabel').innerText = titulo;
                
                container.innerHTML = '';
                container.appendChild(formContent);
                
                // Re-executar scripts e ajustar botões
                carregarScriptsForm(doc);
                if (typeof window.initFormPessoa === 'function') {
                    window.initFormPessoa();
                }

                // Interceptar o envio do formulário para ser via AJAX
                const form = container.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const action = this.getAttribute('action');
                        
                        // Desabilitar botão para evitar cliques duplos
                        const btnSubmit = this.querySelector('button[type="submit"]');
                        const originalText = btnSubmit.innerHTML;
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

                        fetch(action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            // Se o servidor redirecionar (302), o fetch segue o redirecionamento
                            // Precisamos saber se o resultado final é a lista (sucesso) ou o form (erro)
                            return response.text().then(text => ({
                                url: response.url,
                                content: text
                            }));
                        })
                        .then(res => {
                            // Se a URL final contiver "/pessoa/" e NÃO contiver "formPessoa", provavelmente é o sucesso
                            if (res.url.includes('/pessoa/') && !res.url.includes('formPessoa')) {
                                // Sucesso: Recarregar a página principal para mostrar a lista atualizada e o alerta
                                window.location.href = '/pessoa/';
                            } else {
                                // Erro: O conteúdo retornado é o formulário com as mensagens de erro
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(res.content, 'text/html');
                                const newForm = doc.querySelector('.card-custom');
                                if (newForm) {
                                    container.innerHTML = '';
                                    container.appendChild(newForm);
                                    // Re-inicializar scripts (CEP, máscaras, etc)
                                    carregarScriptsForm(doc);
                                    if (typeof window.initFormPessoa === 'function') {
                                        window.initFormPessoa();
                                    }
                                } else {
                                    window.location.reload();
                                }
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = originalText;
                            alert('Erro ao processar a requisição.');
                        });
                    });
                }
            } else {
                container.innerHTML = '<div class="alert alert-danger">Erro ao carregar o formulário.</div>';
            }
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<div class="alert alert-danger">Erro de conexão ao carregar o formulário.</div>';
        });
}

// Ajustar links de editar e visualizar na tabela para abrir modal
document.addEventListener('DOMContentLoaded', function() {
    const linksAcao = document.querySelectorAll('table a[href*="/pessoa/formPessoa/"]');
    linksAcao.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            const partes = href.split('/');
            const acao = partes[partes.length - 2];
            const id = partes[partes.length - 1];
            
            const modal = new bootstrap.Modal(document.getElementById('modalCadastroPessoa'));
            modal.show();
            carregarFormPessoa(acao, id);
        });
    });
});
</script>
