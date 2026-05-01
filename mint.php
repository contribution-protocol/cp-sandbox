<?php
/**
 * CP Sandbox: mint.php（append-only）
 * 発行は引当残高以下
 * 全記録は追記のみ（改ざん不可）
 *
 * NOTE:
 * This sandbox allows GET parameters (via $_REQUEST) for easy browser testing.
 * In production, restrict to POST only to prevent unintended issuance via URL.
 *
 * 本サンドボックスではブラウザからの体験を優先し、
 * GETパラメータ（$_REQUEST）を許可しています。
 * 本番環境ではPOSTのみに制限してください。
 *
 * ---
 *
 * NOTE:
 * This sandbox uses minute-based expiration for quick observation.
 *
 * 本サンドボックスでは挙動観察のため、
 * 分単位の短い期限を使用しています。
 *
 * ---
 *
 * TODO (production):
 * Replace minute-based expiration with calendar-based logic.
 */

date_default_timezone_set('UTC');

$db = new SQLite3('cp.sqlite');
$db->exec(file_get_contents('db.sql')); // テーブル初期化（IF NOT EXISTS で冪等）

header('Content-Type: application/json');

// GET / POST 両対応（体験優先）
$amount = floatval($_REQUEST['amount'] ?? 0);
$expire_minutes = intval($_REQUEST['expire_minutes'] ?? 2);

if ($amount <= 0) {
    echo json_encode(['error' => 'amount must be > 0']);
    exit;
}

// 引当残高取得
$reserve = $db->querySingle(
    'SELECT reserve_amount FROM reserves ORDER BY id DESC LIMIT 1'
);

if ($reserve === null) {
    $db->exec("INSERT INTO reserves (reserve_amount) VALUES (1000)");
    $reserve = 1000;
}

// 有効トークン量（ledgerから導出）
$active_total = $db->querySingle("
    SELECT COALESCE(SUM(t.issued_amount), 0)
    FROM tokens t
    WHERE t.token_id NOT IN (
        SELECT token_id FROM ledger WHERE event_type = 'expired'
    )
");

$available = $reserve - $active_total;

// 発行制約
if ($amount > $available) {
    echo json_encode([
        'error'     => 'exceeds reserve',
        'reserve'   => $reserve,
        'active'    => $active_total,
        'available' => $available,
        'requested' => $amount
    ]);
    exit;
}

// トークン発行
$token_id  = bin2hex(random_bytes(8));
$expires_at = date('Y-m-d H:i:s', time() + ($expire_minutes * 60));

$stmt = $db->prepare("
    INSERT INTO tokens (token_id, issued_amount, expires_at)
    VALUES (:token_id, :amount, :expires_at)
");
$stmt->bindValue(':token_id',   $token_id);
$stmt->bindValue(':amount',     $amount);
$stmt->bindValue(':expires_at', $expires_at);
$stmt->execute();

// ledger記録（issued）
$stmt2 = $db->prepare("
    INSERT INTO ledger (token_id, event_type, amount)
    VALUES (:token_id, 'issued', :amount)
");
$stmt2->bindValue(':token_id', $token_id);
$stmt2->bindValue(':amount',   $amount);
$stmt2->execute();

echo json_encode([
    'status'     => 'issued',
    'token_id'   => $token_id,
    'amount'     => $amount,
    'expires_at' => $expires_at,
    'available'  => $available - $amount
]);
