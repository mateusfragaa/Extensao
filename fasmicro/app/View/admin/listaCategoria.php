<h1><?= $titulo ?? "Lista Categoria" ?></h1>
<p>
    <a href="/categoria/form/insert" class="btn btn-primary btn-sm" title="Nova Categoria">
        Novo
    </a>
</p>

<?php if (!empty($categorias)): ?>

    <table class="table table-sm">
        <thead>
            <tr>
                <th>Id</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Opções</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?= $categoria['id'] ?></td>
                    <td><?= $categoria['descricao'] ?></td>
                    <td><?= $aStatus[$categoria['statusRegistro']] ?></td>
                    <td>
                        <a href="/categoria/form/view/<?= $categoria['id'] ?>" class="btn btn-secondary btn-sm" title="Visualizar">
                            Visualizar
                        </a>
                        <a href="/categoria/form/update/<?= $categoria['id'] ?>" class="btn btn-warning btn-sm" title="Alterar">
                            Alterar
                        </a>
                        <a href="/categoria/form/delete/<?= $categoria['id'] ?>" class="btn btn-danger btn-sm" title="Excluir">
                            Excluir
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
        
<?php else: ?>
    <p class="text-muted">Nenhuma Categoria encontrada.</p>
<?php endif;?>

<a href="/" class="btn btn-secondary mt-3">Voltar</a>