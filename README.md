## docker-laravel11-nginx-mysql8

```bash
cp laravel-project/.env.example laravel-project/.env
docker-compose up -d
#　マイグレーション(初回だけ)
docker-compose exec -T app php artisan migrate
# テストコード実行 ()
docker-compose exec -T app php artisan test tests/Feature/UserAndUserProfileTest.php
```

http://localhost:8000/
