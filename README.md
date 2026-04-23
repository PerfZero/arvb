# WordPress Docker для СПБ АУ

## Что в репозитории
- Локальный Docker-стенд WordPress + MariaDB.
- Тема сайта: `wp-content/themes/spb-au`.
- Скрипт и workflow для деплоя только темы на `arb.devdenis.ru`.

## Локальный запуск
```bash
docker compose up -d
```

Открыть сайт: `http://localhost:8088`

## Где редактировать тему
- `wp-content/themes/spb-au/style.css`
- `wp-content/themes/spb-au/functions.php`
- `wp-content/themes/spb-au/index.php`

## Остановка локального стенда
```bash
docker compose down
```

## Автодеплой темы на arb.devdenis.ru
Workflow: `.github/workflows/deploy-theme.yml`

Он срабатывает на `push` в `main`, если изменились файлы в:
- `wp-content/themes/spb-au/**`

### Нужные GitHub Secrets
В репозитории GitHub открой `Settings -> Secrets and variables -> Actions` и добавь:
- `ARB_DEPLOY_HOST` = `109.172.46.96`
- `ARB_DEPLOY_PORT` = `22`
- `ARB_DEPLOY_USER` = `root`
- `ARB_DEPLOY_PASSWORD` = пароль от сервера

### Как работает деплой
1. GitHub Actions архивирует `spb-au`.
2. Загружает архив + `deploy/theme/deploy-theme.sh` на сервер в `/tmp`.
3. Скрипт на сервере:
   - делает бэкап текущей темы в `/opt/backups/wp-themes/spb-au/`
   - заменяет тему внутри контейнера `arb_wordpress-wordpress-1`
   - выставляет права (`www-data`, `755/644`)

### Ручной запуск деплоя
В Actions можно запустить workflow кнопкой `Run workflow` (`workflow_dispatch`).
