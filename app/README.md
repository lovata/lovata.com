### Развернуть проект для разработки.

1. Клонируем репозиторий
2. make local action=init key={DeveloperKey}
3. Доступ к админке http://localhost/backend


#### Команды

- `make local action=init key={developerKey}` - Инициализация и сборка с локальными инструкциями
- `make local` - Обновление сборки с локальными инструкциями
- `make workspace  cmd="ls -la"` - Выполнение команды в контейнере workspace
- `make playbooks env=local action=configure` - Выполнение плейбука без шифрования
- `make playbooks-decrypt env=development action=init` - Выполнение плейбука с шифрованием
- `make attach-license project={ProjectName} key={DeveloperKey}` - Обновление авторизации composer


- `./vendor/bin/sail up -d`
- `./vendor/bin/sail artisan vite:watch`
- `./vendor/bin/sail artisan vite:build`




-- Все остальные команды доступны по обращению и документации laravel sail

### Сервисы
#### mySQL
Доступ к базе данных возможен по локальному порту 127.0.0.1:3306.
Стандартные логин и пароль - user:password

not create auth.json, env
