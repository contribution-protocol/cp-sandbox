-- CP Sandbox: Minimal DB Structure
-- Principle: append-only, no UPDATE, no DELETE

CREATE TABLE IF NOT EXISTS reserves (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reserve_amount REAL NOT NULL,         -- 引当残高
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    token_id TEXT NOT NULL UNIQUE,        -- トークン識別子
    issued_amount REAL NOT NULL,          -- 発行量（引当残高以下）
    status TEXT NOT NULL DEFAULT 'active',-- 参照用（active / expired）。制御はledgerで行う
    issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL          -- 失効日時（人は介入しない）
);

CREATE TABLE IF NOT EXISTS ledger (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    token_id TEXT NOT NULL,
    event_type TEXT NOT NULL,             -- issued / expired
    amount REAL NOT NULL,
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP
    -- 追記のみ。UPDATE/DELETE禁止。
);

-- インデックス
CREATE INDEX IF NOT EXISTS idx_tokens_status ON tokens(status);
CREATE INDEX IF NOT EXISTS idx_tokens_expires ON tokens(expires_at);
CREATE INDEX IF NOT EXISTS idx_ledger_token ON ledger(token_id);
