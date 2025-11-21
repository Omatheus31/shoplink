<?php
require_once 'includes/header_admin.php';
$titulo_pagina = 'Adicionar Cliente';
?>

<h1 class="h3 mb-3">Adicionar Cliente</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="salvar_cliente.php" method="post">
            <input type="hidden" name="acao" value="adicionar">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome_loja" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="cliente">Cliente</option>
                    <option value="admin_loja">Admin Loja</option>
                    <option value="admin_master">Admin Master</option>
                </select>
            </div>
            <button class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer_admin.php';
