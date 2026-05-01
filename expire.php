<?php
/**
 * CP Sandbox: expire.php
 * 未使用分は時間で失効（人は介入しない）
 * 全記録は追記のみ（改ざん不可）
 
 * ※重要：
 * トークンは expires_at を過ぎた時点で「すでに失効している」。
 * このスクリプトは失効を発生させるものではなく、
 * 失効済みのトークンを後から ledger に記録する処理である。
 * そのため、実行タイミングによっては expires_at と記録時刻の間に
 * 時間差（ズレ）が生じる。
 *
 * cronで定期実行することを想定:
 * * * * * php /path/to/expire.php
 */

date_default_timezone_set('UTC');

$db = new SQLite3('cp.sqlite');
$db->exec(file_get_contents('db.sql')); // テーブル初期化（IF NOT EXISTS で冪等）

header('Content-Type: application/json');

$now = date('Y-m-d H:i:s');

// 失効対象：ledgerにexpiredが未記録かつexpires_atが過去
$result = $db->query("
    SELECT t.token_id, t.issued_amount
    FROM tokens t
    WHERE t.expires_at <= '$now'
    AND t.token_id NOT IN (
        SELECT token_id FROM ledger WHERE event_type = 'expired'
    )
");

$expired = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $token_id = $row['token_id'];
    $amount   = $row['issued_amount'];

    // 状態遷移（不可逆）：ledgerにexpiredを追記するのみ
    $stmt = $db->prepare("
        INSERT INTO ledger (token_id, event_type, amount)
        VALUES (:token_id, 'expired', :amount)
    ");
    $stmt->bindValue(':token_id', $token_id);
    $stmt->bindValue(':amount',   $amount);
    $stmt->execute();

    $expired[] = [
        'token_id' => $token_id,
        'amount'   => $amount
    ];
}

echo json_encode([
    'status'         => 'done',
    'expired_count'  => count($expired),
    'expired_tokens' => $expired,
    'processed_at'   => $now
]);
