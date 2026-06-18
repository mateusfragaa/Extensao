<?php
use Core\Library\Csrf;

$action_form = formDadosInput($data, 'pessoa');
$errors      = \Core\Library\Session::get('formErrors');
?>
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pessoa/" class="btn btn-light border me-3"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold m-0">Cadastro de Pessoa <?= formSubTitulo($action_form) ?></h4>
            <p class="text-muted small m-0">Pessoa Física (CPF) ou Pessoa Jurídica (CNPJ).</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <?php echo exibeAlerta(); ?>

        <form class="row g-4" action="/pessoa/<?= $action_form ?>" method="POST">

            <?= Csrf::getHiddenField() ?>

            <?php if ($action_form !== 'insert'): ?>
                <input type="hidden" name="PES_ID" value="<?= setValue('PES_ID') ?>">
            <?php endif; ?>

            <!-- ── Tipo de Pessoa ──────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">Tipo de Pessoa <span class="text-danger">*</span></label>
                <select name="TIPO_PESSOA" id="tipo_pessoa"
                        class="form-select <?= isset($errors['TIPO_PESSOA']) ? 'is-invalid' : '' ?>"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>
                    <option value="F" <?= (setValue('TIPO_PESSOA', 'F') === 'F') ? 'selected' : '' ?>>Pessoa Física</option>
                    <option value="J" <?= (setValue('TIPO_PESSOA') === 'J')       ? 'selected' : '' ?>>Pessoa Jurídica</option>
                </select>
                <?php if (isset($errors['TIPO_PESSOA'])): ?><div class="invalid-feedback"><?= $errors['TIPO_PESSOA'] ?></div><?php endif; ?>
            </div>

            <!-- ── Nome / Razão Social ─────────────────────────────── -->
            <div class="col-md-9">
                <label class="form-label" id="label_nome_pfpj">Nome Completo <span class="text-danger">*</span></label>
                <input type="text" name="PES_NOME" id="nome_pfpj"
                    class="form-control <?= isset($errors['PES_NOME']) ? 'is-invalid' : '' ?>"
                    placeholder="Digite o nome completo"
                    value="<?= htmlspecialchars(setValue('PES_NOME')) ?>"
                    maxlength="100"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['PES_NOME'])): ?><div class="invalid-feedback"><?= $errors['PES_NOME'] ?></div><?php endif; ?>
            </div>

            <!-- ── CPF / CNPJ ─────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label" id="label_cpf_cnpj">CPF <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="CPF_CNPJ" id="cpf_cnpj"
                        class="form-control <?= isset($errors['CPF_CNPJ']) ? 'is-invalid' : '' ?>"
                        placeholder="000.000.000-00"
                        value="<?php
                            $rawDoc = preg_replace('/\D/', '', setValue('CPF_CNPJ'));
                            if (strlen($rawDoc) === 11) {
                                echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $rawDoc);
                            } elseif (strlen($rawDoc) === 14) {
                                echo preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $rawDoc);
                            } else {
                                echo htmlspecialchars(setValue('CPF_CNPJ'));
                            }
                        ?>"
                        maxlength="18"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>

                    <?php if ($action_form === 'insert' || $action_form === 'update'): ?>
                        <button class="btn btn-outline-secondary" type="button"
                                id="btn_validar_receita" title="Verificar na Receita Federal">
                            <i class="bi bi-shield-check text-success"></i>
                            <span id="btn_verificar_label">Verificar</span>
                        </button>
                    <?php endif; ?>

                    <?php if (isset($errors['CPF_CNPJ'])): ?>
                        <div class="invalid-feedback"><?= $errors['CPF_CNPJ'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Seletor de dataset (apenas para CNPJ) -->
                <div id="bloco_dataset" class="mt-1" style="display:none;">
                    <select id="sel_dataset" class="form-select form-select-sm">
                        <option value="receita">Receita Federal</option>
                        <option value="cno">CNO — Cadastro Nacional de Obras</option>
                        <option value="rntrc">RNTRC — Transportadores</option>
                    </select>
                    <small class="text-muted">Selecione a fonte de consulta. Se não encontrar, tente outra.</small>
                </div>
                <small id="cpf_cnpj_fonte" class="text-muted"></small>
            </div>

            <!-- ── E-mail ──────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                <input type="email" name="EMAIL"
                    class="form-control <?= isset($errors['EMAIL']) ? 'is-invalid' : '' ?>"
                    placeholder="exemplo@email.com"
                    value="<?= htmlspecialchars(setValue('EMAIL')) ?>"
                    maxlength="50"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if (isset($errors['EMAIL'])): ?><div class="invalid-feedback"><?= $errors['EMAIL'] ?></div><?php endif; ?>
            </div>

            <!-- ── Telefone ────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="text" name="TELEFONE" id="input_telefone"
                    class="form-control"
                    placeholder="(00) 00000-0000"
                    value="<?php
                        $tel = preg_replace('/\D/', '', setValue('TELEFONE'));
                        if (strlen($tel) === 11) {
                            echo '(' . substr($tel,0,2) . ') ' . substr($tel,2,5) . '-' . substr($tel,7,4);
                        } elseif (strlen($tel) === 10) {
                            echo '(' . substr($tel,0,2) . ') ' . substr($tel,2,4) . '-' . substr($tel,6,4);
                        } else {
                            echo htmlspecialchars(setValue('TELEFONE'));
                        }
                    ?>"
                    maxlength="15"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── CEP ────────────────────────────────────────────── -->
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="CEP" id="cep"
                    class="form-control" placeholder="00000-000"
                    value="<?php
                        $cep = preg_replace('/\D/', '', setValue('CEP'));
                        echo strlen($cep) === 8
                            ? substr($cep,0,5) . '-' . substr($cep,5,3)
                            : htmlspecialchars(setValue('CEP'));
                    ?>"
                    maxlength="9"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <small id="cep_status" class="text-muted small"></small>
            </div>

            <!-- ── Endereço ───────────────────────────────────────── -->
            <div class="col-md-7">
                <label class="form-label">Endereço</label>
                <input type="text" name="ENDERECO" id="endereco"
                    class="form-control" placeholder="Rua, Av., Logradouro..."
                    value="<?= htmlspecialchars(setValue('ENDERECO')) ?>"
                    maxlength="50"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── Número ─────────────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">Número</label>
                <input type="text" name="NUMERO" id="numero_casa"
                    class="form-control" placeholder="123"
                    value="<?= htmlspecialchars(setValue('NUMERO')) ?>"
                    maxlength="6"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
                <?php if ($action_form !== 'view'): ?>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" id="sem_numero"
                            <?= (setValue('NUMERO') === 'S/N') ? 'checked' : '' ?>>
                        <label class="form-check-label small" for="sem_numero">Sem número</label>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── Bairro ─────────────────────────────────────────── -->
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" name="BAIRRO" id="bairro"
                    class="form-control"
                    value="<?= htmlspecialchars(setValue('BAIRRO')) ?>"
                    maxlength="40"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── Cidade ─────────────────────────────────────────── -->
            <div class="col-md-5">
                <label class="form-label">Cidade</label>
                <input type="text" name="CIDADE" id="cidade"
                    class="form-control"
                    value="<?= htmlspecialchars(setValue('CIDADE')) ?>"
                    maxlength="40"
                    <?= $action_form === 'view' ? 'disabled' : '' ?>>
            </div>

            <!-- ── UF ─────────────────────────────────────────────── -->
            <div class="col-md-3">
                <label class="form-label">UF</label>
                <select name="UF" id="uf" class="form-select"
                        <?= $action_form === 'view' ? 'disabled' : '' ?>>
                    <option value="">Selecione...</option>
                    <?php
                    $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS',
                            'MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC',
                            'SP','SE','TO'];
                    foreach ($ufs as $sigla): ?>
                        <option value="<?= $sigla ?>" <?= (setValue('UF') === $sigla) ? 'selected' : '' ?>>
                            <?= $sigla ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ── Botões ─────────────────────────────────────────── -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-light border px-4" href="/pessoa/">Cancelar</a>
                <?php if ($action_form !== 'view'): ?>
                    <button type="submit" class="btn btn-primary px-5">
                        <?= $action_form === 'delete' ? 'Confirmar Exclusão' : 'Salvar' ?>
                    </button>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>


<?php \Core\Library\Session::destroy('formErrors'); ?>

<script src="/assests/js/formPessoa.js"></script>

<?php \Core\Library\Session::destroy('formErrors'); ?>
