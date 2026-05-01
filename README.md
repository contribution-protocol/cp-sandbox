# CP Sandbox

▶ Run this now (30 sec)

```bash
git clone https://github.com/contribution-protocol/cp-sandbox.git
cd cp-sandbox
php -S localhost:8000
```

Open in browser:

* http://localhost:8000/mint.php?amount=100
* http://localhost:8000/expire.php

---

This is an experimental sandbox for the Contribution Protocol (CP).

This sandbox **runs the minimal structure of CP**:
[cp-minimal-structure.txt](https://github.com/contribution-protocol/cp-core-spec/blob/main/cp-minimal-structure.txt)

---

## ⚠️ Warning

* Not production-ready
* May contain bugs
* For observing minimal behavior only

---

## What you can do

* Issue a token (`mint.php`)
* Wait and see it expire (`expire.php`)
* Inspect append-only records (`cp.sqlite`)

---

## What to observe

* Does issuance ever exceed the available reserve?
* Do unused tokens expire based on time alone?
* Are all records strictly append-only?

This sandbox is not for validation — it is for observing how these properties behave.

---

## Setup (Zero Setup)

No database setup is required.

The database (`cp.sqlite`) is automatically created on first run.

### Run

```
php -S localhost:8000
```

Then open in your browser:

* http://localhost:8000/mint.php?amount=100
* http://localhost:8000/expire.php

---

## Structure

* `cp.sqlite` → SQLite database (auto-created, append-only)
* `db.sql` → schema definition
* `mint.php` → token issuance
* `expire.php` → expiration process

---

## About

This is a minimal sandbox to observe the core behavior of CP.

---

## Contributing

Issues and PRs are welcome.

---

# 日本語

▶ 今すぐ実行（30秒）

```bash
git clone https://github.com/contribution-protocol/cp-sandbox.git
cd cp-sandbox
php -S localhost:8000
```

ブラウザで開く：

* http://localhost:8000/mint.php?amount=100
* http://localhost:8000/expire.php

---

これはContribution Protocol（CP）の検証用サンドボックスです。

このsandboxは、以下の**CP最小構造を実行するものです**：
[cp-minimal-structure.txt](https://github.com/contribution-protocol/cp-core-spec/blob/main/cp-minimal-structure.txt)

---

## ⚠️ 注意

* 本番利用は想定していません
* バグの可能性があります
* 最小挙動の観察用です

---

## できること

* トークンを発行する（`mint.php`）
* 時間による失効を確認する（`expire.php`）
* 追記のみの記録を確認する（`cp.sqlite`）

---

## 観察ポイント

* 発行が引当残高を超えないか
* 未使用トークンが時間のみで失効するか
* 記録が完全に追記のみか

このsandboxは検証ではなく、挙動の観察のためのものです。

---

## セットアップ（準備不要）

データベースの準備は不要です。
初回実行時に `cp.sqlite` が自動生成されます。

### 実行方法

```
php -S localhost:8000
```

ブラウザでアクセス：

* http://localhost:8000/mint.php?amount=100
* http://localhost:8000/expire.php

---

## 構成

* `cp.sqlite` → SQLite（自動生成・追記型）
* `db.sql` → テーブル定義
* `mint.php` → 発行
* `expire.php` → 失効

---

## 補足

CPのコア挙動を観察するための最小サンドボックスです。

---

## 参加・貢献

Issue / PR 歓迎
