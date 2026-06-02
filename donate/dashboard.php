<?php
require_once 'inc/db.php';
require_once 'inc/header.php';
requireLogin();

$login = $_SESSION['user_login'];

// Buscar Saldo
$stmt = $pdo->prepare("SELECT saldo FROM site_balance WHERE account = ?");
$stmt->execute([$login]);
$balance = $stmt->fetchColumn();
$saldo = $balance ? $balance : 0;

// Buscar Histórico
$stmt = $pdo->prepare("SELECT * FROM site_donations WHERE account = ? ORDER BY data DESC LIMIT 10");
$stmt->execute([$login]);
$history = $stmt->fetchAll();
?>

<div class="dashboard-stats">
    <div class="stat-box">
        <div class="stat-label">Conta</div>
        <div class="stat-value"><?php echo htmlspecialchars($login); ?></div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Saldo Atual</div>
        <div class="stat-value"><?php echo number_format($saldo, 0); ?> Coins</div>
    </div>
    <div class="stat-box" style="display:flex; align-items:center;">
        <a href="buy.php" class="btn btn-primary btn-block">COMPRAR COINS</a>
    </div>
</div>

<div class="card">
    <h3 class="card-title">Histórico de Doações</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Qtd. Coins</th>
                    <th>Valor</th>
                    <th>Método</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($history as $row):
                    $metodos = [
                        'mercadopago' => 'Mercado Pago',
                        'paypal' => 'PayPal',
                        'GAME_TRANSFER' => 'Transferência para o Jogo'
                    ];
                ?>
                
                <tr>
                    <td>#<?php echo $row['protocolo']; ?></td>
                    <td><?php echo $row['quant_coins']; ?></td>
                    <td><?php echo $row['currency'] . ' ' . number_format($row['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo isset($metodos[$row['metodo_pgto']]) ? $metodos[$row['metodo_pgto']] : ucfirst($row['metodo_pgto']); ?></td>
                    
                    <td><?php echo date('d/m/Y H:i', $row['data']); ?></td>
                    
                    <td>
                        <?php if($row['status'] == 2): ?>
                            <span class="badge badge-paid">PAGO</span>
                        <?php else: ?>
                            <span class="badge badge-pending">PENDENTE</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($history) == 0): ?>
                    <tr><td colspan="6" class="text-center">Nenhuma doação encontrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>