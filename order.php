<?php
session_start();
require_once 'config/db.php';

// Если корзина пуста, редирект на страницу корзины
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Если пользователь не авторизован, предложить регистрацию и оформление заказа
if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_submit'])) {
        // Обработка регистрации (аналогично register.php)
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if ($username && $email && $password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            try {
                $stmt->execute([
                    'username' => $username,
                    'email'    => $email,
                    'password' => $hashed
                ]);
                $_SESSION['user_id']  = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
            } catch (PDOException $e) {
                $error = "Ошибка регистрации: " . $e->getMessage();
            }
        } else {
            $error = "Все поля обязательны для регистрации!";
        }
        if (isset($error)) {
            echo "<p style='color:red;'>".htmlspecialchars($error)."</p>";
            // ...вывести форму регистрации заново...
            goto show_form;
        }
    } else {
        show_form:
        // Вывод формы регистрации для завершения заказа
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Регистрация для оформления заказа</title>
            <link rel="stylesheet" href="/public/css/style.css">
        </head>
        <body>
            <?php // ...включение шапки... ?>
            <main>
              <h1>Для оформления заказа необходимо зарегистрироваться</h1>
              <form method="post" action="order.php">
                <label>Имя пользователя:<br>
                  <input type="text" name="username">
                </label><br>
                <label>Email:<br>
                  <input type="email" name="email">
                </label><br>
                <label>Пароль:<br>
                  <input type="password" name="password">
                </label><br>
                <input type="hidden" name="reg_submit" value="1">
                <button type="submit">Зарегистрироваться и оформить заказ</button>
              </form>
            </main>
            <script src="/public/js/script.js"></script>
        </body>
        </html>
        <?php
        exit();
    }
}

// Проверяем, что пользователь всё-таки зарегистрирован
if (!isset($_SESSION['user_id'])) {
    die("Ошибка: Пользователь не зарегистрирован. Заказ не может быть оформлен.");
}

$total = 0;
foreach ($_SESSION['cart'] as $id => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $total += $quantity * $product['price'];
    }
}

// Убираем проверку и преобразование guest order, так как guest заказы не разрешены

// Insert order – user_id is guaranteed to be non-null
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (:user_id, :total)");
$stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'total'   => $total
]);

// Clear cart
unset($_SESSION['cart']);

// Redirect after processing order
header("Location: account.php");
exit();
?>
