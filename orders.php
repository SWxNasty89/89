<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы - UrbanAttire</title>
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
<body class="flex flex-col min-h-screen bg-[#061927]">
    <?php include 'components/header.php'; ?>
    
    <main class="flex-grow">
        <!-- Orders Header -->
        <section class="bg-[#061927] text-white py-10">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold">Мои заказы</h1>
                <div class="flex items-center text-sm mt-2">
                    <a href="index.php" class="text-gray-300 hover:text-[#28eab8]">Главная</a>
                    <span class="mx-2">›</span>
                    <a href="account.php" class="text-gray-300 hover:text-[#28eab8]">Личный кабинет</a>
                    <span class="mx-2">›</span>
                    <span class="text-[#28eab8]">Мои заказы</span>
                </div>
            </div>
        </section>
        
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar -->
                <div class="md:w-1/4">
                    <div class="bg-[#182b39] rounded-xl shadow-sm p-6 mb-6">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 bg-[#28eab8]/10 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-[#28eab8] text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-white"><?= htmlspecialchars($user['username']) ?></h2>
                                <p class="text-gray-400"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                        </div>
                        
                        <ul class="space-y-2">
                            <li>
                                <a href="account.php" class="flex items-center py-2 px-3 rounded text-gray-300 hover:bg-[#28eab8]/10 hover:text-[#28eab8] transition duration-300">
                                    <i class="fas fa-user-circle w-5 mr-2"></i> Личные данные
                                </a>
                            </li>
                            <li>
                                <a href="orders.php" class="flex items-center py-2 px-3 rounded bg-[#28eab8]/10 text-[#28eab8] font-medium">
                                    <i class="fas fa-shopping-bag w-5 mr-2"></i> Мои заказы
                                </a>
                            </li>
                            <li>
                                <a href="favorites.php" class="flex items-center py-2 px-3 rounded text-gray-300 hover:bg-[#28eab8]/10 hover:text-[#28eab8] transition duration-300">
                                    <i class="fas fa-heart w-5 mr-2"></i> Избранное
                                </a>
                            </li>
                            <li>
                                <a href="support.php" class="flex items-center py-2 px-3 rounded text-gray-300 hover:bg-[#28eab8]/10 hover:text-[#28eab8] transition duration-300">
                                    <i class="fas fa-headset w-5 mr-2"></i> Поддержка
                                </a>
                            </li>
                            <li>
                                <a href="logout.php" class="flex items-center py-2 px-3 rounded text-red-400 hover:bg-red-400/10 transition duration-300">
                                    <i class="fas fa-sign-out-alt w-5 mr-2"></i> Выход
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="md:w-3/4">
                    <!-- Orders Section -->
                    <div class="bg-[#182b39] rounded-xl shadow-sm overflow-hidden">
                        <?php if (!empty($orders)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#3b5467]">
                                <thead class="bg-[#1d3547]">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">№ заказа</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Дата</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Сумма</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Статус</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[#182b39] divide-y divide-[#3b5467]">
                                    <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-[#1d3547] transition duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-[#28eab8]">#<?= htmlspecialchars($order['id']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-300"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($order['order_date']))) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-white"><?= htmlspecialchars($order['total']) ?> ₽</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-[#28eab8]/10 text-[#28eab8]">
                                                Выполнен
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="px-6 py-12 text-center">
                            <div class="text-5xl text-[#3b5467] mb-4">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-2">У вас пока нет заказов</h3>
                            <p class="text-gray-400 mb-6">Перейдите в каталог, чтобы найти интересующие вас товары</p>
                            <a href="catalog.php" class="inline-block bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] px-4 py-2 rounded-md transition duration-300">
                                Перейти в каталог
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>
</body>
</html> 