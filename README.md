# coachtechフリマ

## 環境構築
**Dockerビルド**
1. `git@github.com:nga009/coachtech-flea-market-app.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルをコピーして「.env」ファイルを作成
4. .envの以下の環境変数を次の通り変更
``` text
<DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
MAIL_FROM_ADDRESS="noreply@laravel-app.local"
```

5. stripeパッケージインストール
`composer require stripe/stripe-php`

6. Stripe公式サイトで自アカウントの「公開可能キー」「シークレットキー」を確認し、.envに追加
``` text
STRIPE_KEY=（公開可能キー テスト環境用の場合pk_test_で始まる）
STRIPE_SECRET=（シークレットキー  テスト環境用の場合sk_test_で始まる）
```

7. config/stripe.php追加
ファイル内容は以下の通り
``` text
<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
];
```

8. アプリケーションキーの作成
``` bash
php artisan key:generate
```

9. マイグレーションの実行
``` bash
php artisan migrate
```

10. シーディングの実行
``` bash
php artisan db:seed
```

## Stripe決済テスト用のカード番号
``` text
カード番号: 4242 4242 4242 4242
有効期限: 任意の未来の日付（例：12/28）
CVC: 任意の3桁（例：123）
名前: 任意
郵便番号: 任意
```

## 使用技術(実行環境)
- PHP 7.4.9
- Laravel Framework 8.83.8
- MySQL8.0.26
- Stripe

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- mailhog：http://localhost:8025
- Stripe公式サイト：https://stripe.com/jp
