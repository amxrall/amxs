<?php
// 1. Configurações de Erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Includes Obrigatórios
require_once 'inc/db.php';
require_once 'inc/auth.php';
requireLogin();

// 3. Inicialização de Variáveis
$msgError = '';
$account = $_SESSION['user_login'];

// Verifica se o preço está definido, senão usa 1.00 padrão
$preco_unidade = defined('COIN_PRICE') ? COIN_PRICE : 1.00;

// 4. Processamento do Formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coins = (int)$_POST['coins'];
    $method = $_POST['method'];

    // ALTERADO: Validação para mínimo de 1
    if ($coins < 1) {
        $msgError = "Mínimo de 1 Coin.";
    } else {
        $total_pagar = $coins * $preco_unidade;
        $data_hoje = time();

        try {
            // Prepara a Query
            $sql = "INSERT INTO site_donations (account, quant_coins, valor, price, currency, metodo_pgto, status, data) VALUES (?, ?, ?, ?, 'BRL', ?, 1, ?)";
            $stmt = $pdo->prepare($sql);
            
            // Executa
            if($stmt->execute([$account, $coins, $total_pagar, $preco_unidade, $method, $data_hoje])) {
                $orderId = $pdo->lastInsertId();
                
                // Redireciona
                if ($method === 'mercadopago') {
                    header("Location: pay_mp.php?order_id=" . $orderId);
                    exit;
                } elseif ($method === 'paypal') {
                    header("Location: pay_paypal.php?order_id=" . $orderId);
                    exit;
                }
            } else {
                $msgError = "Erro ao salvar no banco.";
            }

        } catch (PDOException $e) {
            $msgError = "Erro SQL: " . $e->getMessage();
        }
    }
}
?>

<?php require_once 'inc/header.php'; ?>

<div class="card" style="max-width: 600px; margin: 20px auto;">
    <h3 class="card-title">Nova Doação</h3>

    <?php if(!empty($msgError)): ?>
        <div style="background-color: #ff4444; color: white; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
            <?php echo $msgError; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="buy.php">
        
        <div class="form-group">
            <label>Quantidade de Coins</label>
            <input type="number" name="coins" id="coins" value="1" min="1" required style="width: 100%; padding: 10px; color: #fff;">
        </div>

        <div style="margin: 20px 0; text-align: center; font-size: 1.2em;">
            Total: R$ <strong id="totalDisplay"><?php echo number_format(1 * $preco_unidade, 2, ',', '.'); ?></strong>
        </div>

        <div class="form-group">
            <label>Pagamento</label>
            <select name="method" required style="width: 100%; padding: 10px; color: #fff;">
                <option value="mercadopago">Mercado Pago (PIX / Cartão)</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 15px;">GERAR PAGAMENTO</button>
    </form>
</div>

<script>
    var inputCoins = document.getElementById('coins');
    var display = document.getElementById('totalDisplay');
    var preco = <?php echo $preco_unidade; ?>;

    if(inputCoins) {
        inputCoins.addEventListener('input', function() {
            var qtd = parseInt(this.value) || 0;
            var total = qtd * preco;
            display.innerText = total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
        });
    }
</script>

<?php require_once 'inc/footer.php'; ?>