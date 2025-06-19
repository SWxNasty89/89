<?php
session_start();
// Используем __DIR__ для надежности пути
require_once __DIR__ . '/config/db.php';

// 1.1 Устанавливаем кодировку UTF-8 для вывода
header('Content-Type: text/html; charset=utf-8');

// Включаем отображение ошибок (на время отладки - можешь потом закомментировать)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = null; // Инициализируем переменную ошибки

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['username'] ?? '');
    $password   = trim($_POST['password'] ?? '');

    if ($identifier && $password) {
        try {
            if (!isset($pdo)) {
                 throw new Exception("Объект PDO не был создан. Проверьте db.php.");
            }

            // --- ИСПРАВЛЕННЫЙ ЗАПРОС ---
            // Используем два РАЗНЫХ плейсхолдера: :username_id и :email_id
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username_id OR email = :email_id");
            // Передаем значение $identifier для ОБОИХ плейсхолдеров
            $stmt->execute([
                'username_id' => $identifier,
                'email_id' => $identifier
            ]);
            // --- КОНЕЦ ИСПРАВЛЕНИЯ ---

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Проверяем пользователя и пароль
            if ($user && password_verify($password, $user['password'])) {
                // Успешный вход
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['admin'] = (isset($user['is_admin']) && $user['is_admin'] == 1);
                header("Location: account.php");
                exit();
            } else {
                // Неверные данные
                $error = "Неверное имя пользователя/email или пароль.";
            }
        } catch (PDOException $e) {
            error_log("Login DB Error: " . $e->getMessage());
            // Оставляем подробный вывод ошибки на время отладки
            $error = "Произошла ошибка при попытке входа. Пожалуйста, попробуйте позже.<br><strong>ДЕТАЛИ ОШИБКИ (для отладки):</strong> " . htmlspecialchars($e->getMessage());
        } catch (Exception $e) {
             error_log("Login General Error: " . $e->getMessage());
             $error = "Произошла системная ошибка. Пожалуйста, попробуйте позже.<br><strong>ДЕТАЛИ ОШИБКИ (для отладки):</strong> " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "Пожалуйста, заполните все поля.";
    }
}

// Включаем header
try {
    include __DIR__ . '/components/header.php';
} catch (Throwable $e) {
    error_log("FATAL: Failed to include header.php in login.php: " . $e->getMessage());
    echo "<p style='color:red; border: 1px solid red; padding: 10px;'>Критическая ошибка: Не удалось загрузить шапку сайта.<br>Сообщение: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - UrbanAttire</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // ... (Tailwind config) ...
        tailwind.config = {
            theme: {
                extend: {
                     colors: {
                        primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#061927', 950: '#061927' },
                        secondary: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#3b5467', 800: '#334155', 900: '#1e293b', 950: '#0f172a' },
                        success: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        background: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#182b39', 950: '#0f172a' }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        .login-error-box { background-color: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem; }
        .login-error-box strong { font-weight: 600; }
        .login-error-box .text-sm { margin-top: 0.5rem; } /* Добавил отступ для деталей */
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50">
    <?php // Шапка уже подключена выше ?>

    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-[#061927] text-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-[#182b39] px-6 py-8 text-center">
                <h1 class="text-2xl font-bold mb-2">Вход в аккаунт</h1>
                <p class="text-gray-300">Войдите, чтобы получить доступ к вашим заказам и персональным скидкам</p>
            </div>

            <div class="p-6">
                <?php if ($error !== null): ?>
                <div class="login-error-box" role="alert">
                    <div class="flex">
                        <div class="py-1"><i class="fas fa-exclamation-triangle mr-3 text-red-400"></i></div>
                        <div>
                            <p class="font-bold">Ошибка входа</p>
                            <div class="text-sm"><?php echo $error; ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <form method="post" action="login.php">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-300 text-sm font-medium mb-2">
                            Имя пользователя или Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-[#3b5467]"></i>
                            </div>
                            <input type="text" id="username" name="username" required class="pl-10 w-full bg-[#182b39] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]" placeholder="Введите имя пользователя или email">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-300 text-sm font-medium mb-2">
                            Пароль
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-[#3b5467]"></i>
                            </div>
                            <input type="password" id="password" name="password" required class="pl-10 w-full bg-[#182b39] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]" placeholder="Введите пароль">
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input id="remember" type="checkbox" class="h-4 w-4 bg-[#182b39] border-[#3b5467] text-[#28eab8] focus:ring-[#28eab8] rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-300">
                                Запомнить меня
                            </label>
                        </div>

                        <a href="#" class="text-sm text-[#28eab8] hover:text-[#28eab8]/80">
                            Забыли пароль?
                        </a>
                    </div>

                    <button type="submit" class="w-full bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] py-2 px-4 rounded-md font-medium transition duration-300">
                        Войти
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-300">
                        Нет аккаунта? <a href="register.php" class="text-[#28eab8] hover:text-[#28eab8]/80 font-medium">Зарегистрироваться</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php
    try {
        include __DIR__ . '/components/footer.php';
    } catch (Throwable $e) {
         error_log("Error including footer.php in login.php: " . $e->getMessage());
         echo "<p style='color:orange; text-align: center;'>Не удалось загрузить футер.</p>";
    }
    ?>
</body>
</html>