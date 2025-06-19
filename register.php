<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $_SESSION['user_id']   = $pdo->lastInsertId();
            $_SESSION['username']  = $username;
            header("Location: account.php");
            exit();
        } catch (PDOException $e) {
            $error = "Ошибка регистрации: " . $e->getMessage();
        }
    } else {
        $error = "Все поля обязательны!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - UrbanAttire</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#061927',
                            950: '#061927',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#3b5467',
                            800: '#334155',
                            900: '#1e293b',
                            950: '#0f172a',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        background: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#182b39',
                            950: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-50">
    <?php include 'components/header.php'; ?>
    
    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-[#061927] text-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-[#182b39] px-6 py-8 text-center">
                <h1 class="text-2xl font-bold mb-2">Создание аккаунта</h1>
                <p class="text-gray-300">Зарегистрируйтесь, чтобы совершать покупки и отслеживать заказы</p>
            </div>
            
            <div class="p-6">
                <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <form method="post" action="register.php">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-300 text-sm font-medium mb-2">
                            Имя пользователя
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-[#3b5467]"></i>
                            </div>
                            <input type="text" id="username" name="username" class="pl-10 w-full bg-[#182b39] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]" placeholder="Введите имя пользователя">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-300 text-sm font-medium mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-[#3b5467]"></i>
                            </div>
                            <input type="email" id="email" name="email" class="pl-10 w-full bg-[#182b39] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]" placeholder="Введите email">
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
                            <input type="password" id="password" name="password" class="pl-10 w-full bg-[#182b39] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]" placeholder="Создайте пароль">
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Пароль должен содержать не менее 8 символов</p>
                    </div>
                    
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input id="terms" type="checkbox" class="h-4 w-4 bg-[#182b39] border-[#3b5467] text-[#28eab8] focus:ring-[#28eab8] rounded">
                            <label for="terms" class="ml-2 block text-sm text-gray-300">
                                Я согласен с <a href="#" class="text-[#28eab8] hover:text-[#28eab8]/80">условиями использования</a> и <a href="#" class="text-[#28eab8] hover:text-[#28eab8]/80">политикой конфиденциальности</a>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] py-2 px-4 rounded-md font-medium transition duration-300">
                        Зарегистрироваться
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-300">
                        Уже есть аккаунт? <a href="login.php" class="text-[#28eab8] hover:text-[#28eab8]/80 font-medium">Войти</a>
                    </p>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>
</body>
</html>