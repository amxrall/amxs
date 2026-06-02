<?php
require_once 'inc/db.php';
require_once 'inc/header.php';
requireLogin();

$login = $_SESSION['user_login'];
$msg = [];

// Busca Saldo Atual
$stmt = $pdo->prepare("SELECT saldo FROM site_balance WHERE account = ?");
$stmt->execute([$login]);
$currentBalance = $stmt->fetchColumn() ?: 0;

// Busca Personagens da Conta
// OBS: Ajuste 'characters' e 'account_name' se sua rev usar nomes diferentes (ex: characters / account_name)
$stmt = $pdo->prepare("SELECT char_name, obj_Id, online FROM characters WHERE account_name = ?");
$stmt->execute([$login]);
$chars = $stmt->fetchAll();

// Processar Transferência
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
    $charId = filter_input(INPUT_POST, 'char_id', FILTER_VALIDATE_INT);

    if (!$amount || $amount <= 0) {
        $msg = ['type' => 'error', 'text' => 'Quantidade inválida.'];
    } elseif ($amount > $currentBalance) {
        $msg = ['type' => 'error', 'text' => 'Saldo insuficiente.'];
    } elseif (!$charId) {
        $msg = ['type' => 'error', 'text' => 'Selecione um personagem.'];
    } else {
        // Verificar se o char pertence mesmo a essa conta e status online
        $verify = array_filter($chars, function($c) use ($charId) {
            return $c['obj_Id'] == $charId;
        });
        
        $targetChar = reset($verify);

        if (!$targetChar) {
            $msg = ['type' => 'error', 'text' => 'Personagem inválido.'];
        } elseif (!ALLOW_ONLINE && $targetChar['online'] > 0) {
            $msg = ['type' => 'error', 'text' => 'O personagem deve estar OFFLINE para receber os itens.'];
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Debitar do Site
                $upd = $pdo->prepare("UPDATE site_balance SET saldo = saldo - ? WHERE account = ?");
                $upd->execute([$amount, $login]);

                // 2. Inserir diretamente no inventário (padrão do seu pack)
				
				// Verifica se o item já existe no inventário
				$check = $pdo->prepare("
					SELECT object_id 
					FROM items 
					WHERE owner_id = ? AND item_id = ? AND loc = 'INVENTORY'
					LIMIT 1
				");
				$check->execute([$charId, GAME_ITEM_ID]);
				$item = $check->fetch();
				
				if ($item) {
					// Já existe → soma quantidade
					$updItem = $pdo->prepare("
						UPDATE items 
						SET count = count + ? 
						WHERE object_id = ? AND owner_id = ?
						LIMIT 1
					");
					$updItem->execute([$amount, $item['object_id'], $charId]);
				} else {
					// Não existe → cria novo item
					$lastObj = $pdo->query("SELECT MAX(object_id) FROM items")->fetchColumn();
					$lastLoc = $pdo->prepare("SELECT MAX(loc_data) FROM items WHERE owner_id = ?");
					$lastLoc->execute([$charId]);
				
					$newObjId = ($lastObj ?: 0) + 1;
					$newLoc   = ($lastLoc->fetchColumn() ?: 0) + 1;
				
					$insItem = $pdo->prepare("
						INSERT INTO items 
						(owner_id, object_id, item_id, count, enchant_level, loc, loc_data)
						VALUES (?, ?, ?, ?, 0, 'INVENTORY', ?)
					");
					$insItem->execute([$charId, $newObjId, GAME_ITEM_ID, $amount, $newLoc]);
				}

                // 3. Registrar Histórico (Opcional, mas recomendado)
                // Vamos usar a mesma tabela site_donations com status 2 (pago) mas com metodo 'GAME_TRANSFER'
                $log = $pdo->prepare("
    INSERT INTO site_donations 
    (account, quant_coins, valor, price, currency, metodo_pgto, status, data) 
    VALUES (?, ?, 0, 0, 'BRL', 'GAME_TRANSFER', 2, ?)
");

$log->execute([
    $login,
    -$amount,
    time()
]);

                $pdo->commit();
                
                // Atualiza saldo visualmente
                $currentBalance -= $amount;
                $msg = ['type' => 'success', 'text' => "Sucesso! $amount " . GAME_ITEM_NAME . " enviados para " . $targetChar['char_name'] . "."];

            } catch (Exception $e) {
                $pdo->rollBack();
                $msg = ['type' => 'error', 'text' => 'Erro no sistema: ' . $e->getMessage()];
            }
        }
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h3 class="card-title">Resgatar Coins para o Jogo</h3>
    
    <?php if(!empty($msg)): ?>
        <div class="alert" style="border-color: <?php echo $msg['type'] == 'success' ? 'var(--success)' : 'var(--danger)'; ?>; color: #fff;">
            <?php echo $msg['text']; ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-stats" style="grid-template-columns: 1fr; margin-bottom: 20px;">
        <div class="stat-box">
            <div class="stat-label">Saldo Disponível no Painel</div>
            <div class="stat-value" style="color: var(--primary)"><?php echo number_format($currentBalance, 0); ?> Coins</div>
        </div>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>Selecione o Personagem</label>
            <select name="char_id" required>
                <option value="">-- Escolha o Char --</option>
                <?php foreach($chars as $char): ?>
                    <option value="<?php echo $char['obj_Id']; ?>">
    <?php echo $char['char_name']; ?> 
    (<?php echo $char['online'] == 1 ? 'Online 🟢' : 'Offline 🔴'; ?>)
</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Quantidade a Transferir</label>
            <input type="number" name="amount" min="1" max="<?php echo $currentBalance; ?>" required placeholder="Digite a quantidade">
            <small style="color: var(--text-muted)">Você receberá em: <strong><?php echo GAME_ITEM_NAME; ?></strong></small>
        </div>

        <?php if(!ALLOW_ONLINE): ?>
        <div style="margin-bottom: 15px; font-size: 0.9em; color: #e74c3c;">
            * Deslogue o personagem antes de realizar a transferência.
        </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary btn-block">CONFIRMAR TRANSFERÊNCIA</button>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?>