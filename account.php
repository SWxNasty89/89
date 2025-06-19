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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - UrbanAttire</title>
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
        <!-- Account Header -->
        <section class="bg-[#061927] text-white py-10">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold">Личный кабинет</h1>
                <div class="flex items-center text-sm mt-2">
                    <a href="index.php" class="text-gray-300 hover:text-[#28eab8]">Главная</a>
                    <span class="mx-2">›</span>
                    <span class="text-[#28eab8]">Личный кабинет</span>
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
                                <a href="account.php" class="flex items-center py-2 px-3 rounded bg-[#28eab8]/10 text-[#28eab8] font-medium">
                                    <i class="fas fa-user-circle w-5 mr-2"></i> Личные данные
                                </a>
                            </li>
                            <li>
                                <a href="orders.php" class="flex items-center py-2 px-3 rounded text-gray-300 hover:bg-[#28eab8]/10 hover:text-[#28eab8] transition duration-300">
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
                                <a href="help_us.php" class="flex items-center py-2 px-3 rounded text-gray-300 hover:bg-[#28eab8]/10 hover:text-[#28eab8] transition duration-300">
                                    <i class="fas fa-hands-helping w-5 mr-2"></i> Помоги нам
                                </a>
                            </li>
                            <li class="pt-4">
                                <a href="logout.php" class="flex items-center py-2 px-3 rounded text-red-400 hover:text-red-300 transition duration-300">
                                    <i class="fas fa-sign-out-alt w-5 mr-2"></i> Выход
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="md:w-3/4">
                    <!-- Profile Section -->
                    <div class="bg-[#182b39] rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-[#3b5467]">
                            <h3 class="text-xl font-semibold text-white">Личные данные</h3>
                        </div>
                        
                        <div class="p-6">
                            <form id="profileForm" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="username" class="block text-sm font-medium text-gray-300 mb-1">
                                            Имя пользователя
                                        </label>
                                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full bg-[#1d3547] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                                            Email
                                        </label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full bg-[#1d3547] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]">
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">
                                            Текущий пароль
                                        </label>
                                        <input type="password" id="current_password" name="current_password" required class="w-full bg-[#1d3547] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]">
                                    </div>
                                    
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-300 mb-1">
                                            Новый пароль
                                        </label>
                                        <input type="password" id="new_password" name="new_password" class="w-full bg-[#1d3547] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]">
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <button type="submit" class="bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] py-2 px-4 rounded-md font-medium transition duration-300">
                                        Сохранить изменения
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>
    
    <!-- Notification Component -->
    <div id="notification" class="fixed top-4 right-4 max-w-sm w-full bg-[#182b39] rounded-xl shadow-lg transform transition-all duration-300 scale-0 opacity-0 z-50">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0" id="notification-icon">
                    <!-- Icon will be injected by JS -->
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-white" id="notification-title"></p>
                    <p class="mt-1 text-sm text-gray-400" id="notification-message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-[#182b39] rounded-md inline-flex text-gray-400 hover:text-gray-300 focus:outline-none" onclick="hideNotification()">
                        <span class="sr-only">Закрыть</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Progress bar -->
        <div class="absolute bottom-0 left-0 w-full h-1 bg-[#3b5467] rounded-b-xl">
            <div id="notification-progress" class="h-1 bg-[#28eab8] rounded-b-xl transition-all duration-300"></div>
        </div>
    </div>

    <script>
        function showNotification(type, title, message) {
            const notification = document.getElementById('notification');
            const notificationTitle = document.getElementById('notification-title');
            const notificationMessage = document.getElementById('notification-message');
            const notificationIcon = document.getElementById('notification-icon');
            const notificationProgress = document.getElementById('notification-progress');
            
            // Set icon based on type
            const iconColor = type === 'success' ? '#28eab8' : '#ef4444';
            const icon = type === 'success' 
                ? '<svg class="h-6 w-6 text-[#28eab8]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            
            notificationIcon.innerHTML = icon;
            notificationTitle.textContent = title;
            notificationMessage.textContent = message;
            
            // Show notification
            notification.classList.remove('scale-0', 'opacity-0');
            notification.classList.add('scale-100', 'opacity-100');
            
            // Animate progress bar
            notificationProgress.style.width = '100%';
            setTimeout(() => {
                notificationProgress.style.width = '0%';
            }, 100);
            
            // Auto hide after 5 seconds
            setTimeout(hideNotification, 5000);
        }
        
        function hideNotification() {
            const notification = document.getElementById('notification');
            notification.classList.remove('scale-100', 'opacity-100');
            notification.classList.add('scale-0', 'opacity-0');
        }

        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('success', 'Успешно!', data.message || 'Профиль успешно обновлен');
                    // Очищаем только поля пароля
                    this.current_password.value = '';
                    this.new_password.value = '';
                } else {
                    showNotification('error', 'Ошибка!', data.message || 'Произошла ошибка при обновлении профиля');
                }
            } catch (error) {
                showNotification('error', 'Ошибка!', 'Произошла ошибка при обновлении профиля');
            }
        });
    </script>
</body>
</html>