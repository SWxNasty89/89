# Инструкция по входу в админ-панель

1. **Откройте браузер**
   - Используйте любой современный браузер (Chrome, Firefox, Edge и т.д.).

2. **Перейдите на сайт**
   - Если сайт на локальном сервере: `http://localhost/`
   - Если на хостинге: ваш домен, например, `http://ваш_домен.ru/`

3. **Откройте админ-панель**
   - В адресной строке добавьте `/admin`
   - Пример: `http://localhost/admin/` или `http://ваш_домен.ru/admin/`

4. **Вход в админ-панель**
   - Введите данные администратора:
     - **Логин:** Nastya
     - **Пароль:** 123456789
   - Нажмите кнопку "Войти". После успешного входа вы попадёте на главную страницу админки.

5. **Если не удаётся войти**
   - Проверьте правильность логина и пароля (учитывайте регистр).
   - Если забыли пароль — восстановите через базу данных (таблица `users`, поле `is_admin` должно быть `1`).

---

# Карта сайта

- **Главная страница** — `/index.php`
- **О нас** — `/about.php`
- **Каталог товаров** — `/catalog.php`
- **Корзина** — `/cart.php`
- **Избранное** — `/favorites.php`
- **Регистрация** — `/register.php`
- **Вход** — `/login.php`
- **Личный кабинет** — `/account.php`
- **Оформление заказа** — `/order.php`
- **Мои заказы** — `/orders.php`
- **Страница поддержки** — `/support.php`
- **Опрос/помощь проекту** — `/help_us.php`
- **Админ-панель** — `/admin/`
  - **Товары** — `/admin/products.php`
  - **Категории** — `/admin/categories.php`
  - **Добавить товар** — `/admin/add_product.php`
  - **Добавить категорию** — `/admin/add_category.php`
  - **Редактировать товар** — `/admin/edit_product.php`
  - **Редактировать категорию** — `/admin/edit_category.php`
  - **Удалить товар** — `/admin/delete_product.php`
  - **Удалить категорию** — `/admin/delete_category.php`
  - **Заказы** — `/admin/orders.php`
  - **Просмотр заказа** — `/admin/view_order.php`
  - **Опросы** — `/admin/surveys.php`
  - **Результаты опросов** — `/admin/survey_results.php`
  - **Панель управления** — `/admin/dashboard.php`
  - **Поддержка** — `/admin/support.php`

---

# Как работает сайт 

## 1. Главная страница (`index.php`)
- Показывает витрину популярных товаров и категорий.
- Данные берутся из базы данных MySQL.
- Есть ссылки на каталог, информацию о магазине, форму подписки и т.д.
- Пример кода:
```php
// Получение товаров
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
$featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Подключение компонентов
include __DIR__ . '/components/header.php';
include __DIR__ . '/components/footer.php';
```

## 2. Каталог товаров (`catalog.php`)
- Список всех товаров с возможностью фильтрации по категориям.
- Для каждого товара отображается картинка, название, цена, кнопка "В корзину".
- Можно перейти к подробному описанию товара (обычно через модальное окно).
- Пример кода:
```php
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Фильтрация по категории
if (isset($_GET['category'])) {
    $cat = $_GET['category'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$cat]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

## 3. Корзина (`cart.php`)
- Пользователь может добавлять товары в корзину, изменять количество, удалять товары.
- Оформление заказа — ввод контактных данных, подтверждение.
- Пример кода:
```php
// Добавление товара в корзину
$_SESSION['cart'][$product_id] = $quantity;
// Оформление заказа
$stmt = $pdo->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'pending')");
$stmt->execute([$user_id]);
```

## 4. Комментарии и отзывы
- На странице товара пользователи могут оставлять отзывы и оценки.
- Отзывы отображаются под описанием товара.
- Для добавления отзыва требуется авторизация.
- Пример кода:
```php
// Добавление комментария (add_comment.php)
require_once __DIR__ . '/config/db.php';
$stmt = $pdo->prepare("INSERT INTO comments (product_id, user_id, text, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$product_id, $user_id, $comment]);
// Получение комментариев
$stmt = $pdo->prepare("SELECT * FROM comments WHERE product_id = ?");
$stmt->execute([$product_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## 5. Избранное
- Авторизованные пользователи могут добавлять товары в "Избранное".
- Список избранных товаров доступен на отдельной странице.
- Пример кода:
```php
// Добавление в избранное
toggle_favorite.php
$stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
$stmt->execute([$user_id, $product_id]);
// Получение избранного
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ?");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## 6. Регистрация и вход
- Страницы `register.php` и `login.php` позволяют создать новый аккаунт или войти в существующий.
- После входа доступны дополнительные функции (оформление заказов, избранное, комментарии).
- Пример кода:
```php
// Регистрация
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
// Вход
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
}
```

## 7. Админ-панель (`/admin/`)
- Доступна только пользователям с правами администратора (`is_admin=1` в таблице `users`).
- Основные разделы:
  - **Товары** — добавление, редактирование, удаление товаров.
  - **Категории** — управление категориями товаров.
  - **Заказы** — просмотр и изменение статусов заказов.
  - **Комментарии** — модерация отзывов.
  - **Пользователи** — просмотр и управление пользователями (если реализовано).
  - **Поддержка** — ответы на сообщения пользователей.
  - **Опросы** — просмотр и анализ результатов опросов.
- Пример кода:
```php
// Проверка прав администратора
if (!isset($_SESSION['user_id']) || !$user['is_admin']) {
    header('Location: /login.php');
    exit;
}
// Добавление товара
$stmt = $pdo->prepare("INSERT INTO products (name, price, category_id) VALUES (?, ?, ?)");
$stmt->execute([$name, $price, $category_id]);
```

## 8. База данных
- Все данные сайта (товары, пользователи, заказы, комментарии, избранное) хранятся в MySQL.
- Структура базы описана в файлах из папки `sql/`.

## 9. Статические файлы
- Стили (`css`), скрипты (`js`), изображения — в папке `public/`.
- Используются современные библиотеки (Tailwind CSS, FontAwesome).

---

