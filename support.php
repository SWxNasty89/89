<?php
session_start();
require_once 'config/db.php';

// Включаем логирование ошибок
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log("=== Starting support.php script ===");

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Проверяем текущую базу данных
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbName = $stmt->fetchColumn();
    error_log("Current database: " . $dbName);

    // Проверяем существующие таблицы
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    error_log("Existing tables: " . print_r($tables, true));

    // Пробуем создать таблицу, если её нет
    $createTableSQL = file_get_contents('create_support_table.sql');
    error_log("Attempting to create table with SQL: " . $createTableSQL);
    $pdo->exec($createTableSQL);
    error_log("Table creation attempt completed");

    // Получаем историю сообщений пользователя
    $stmt = $pdo->prepare("SELECT * FROM support_messages WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Successfully retrieved messages for user_id: " . $_SESSION['user_id']);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    die("Произошла ошибка при работе с базой данных. Подробности в логах.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поддержка - UrbanAttire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#061927] text-white min-h-screen">
    <?php include 'components/header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Поддержка</h1>

            <!-- Форма отправки сообщения -->
            <div class="bg-[#182b39] rounded-xl p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Написать в поддержку</h2>
                <form id="supportForm" class="space-y-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium mb-2">Тема</label>
                        <input type="text" id="subject" name="subject" required
                               class="w-full bg-[#1d3547] border border-[#3b5467] rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8]">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium mb-2">Сообщение</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full bg-[#1d3547] border border-[#3b5467] rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8]"></textarea>
                    </div>
                    <button type="submit" 
                            class="bg-[#28eab8] text-[#061927] px-6 py-2 rounded-lg font-medium hover:bg-[#28eab8]/90 transition duration-300">
                        Отправить
                    </button>
                </form>
            </div>

            <!-- История сообщений -->
            <div class="bg-[#182b39] rounded-xl p-6">
                <h2 class="text-xl font-semibold mb-4">История сообщений</h2>
                <div class="space-y-4">
                    <?php if (empty($messages)): ?>
                        <p class="text-gray-400">У вас пока нет сообщений</p>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="border border-[#3b5467] rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium"><?= htmlspecialchars($message['subject']) ?></h3>
                                    <span class="text-sm text-gray-400">
                                        <?= date('d.m.Y H:i', strtotime($message['created_at'])) ?>
                                    </span>
                                </div>
                                <p class="text-gray-300 mb-4"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                                
                                <!-- Статус сообщения -->
                                <div class="mb-2">
                                    <span class="text-sm <?php
                                        switch($message['status']) {
                                            case 'new':
                                                echo 'text-yellow-400';
                                                break;
                                            case 'in_progress':
                                                echo 'text-blue-400';
                                                break;
                                            case 'resolved':
                                                echo 'text-green-400';
                                                break;
                                            default:
                                                echo 'text-gray-400';
                                        }
                                    ?>">
                                        <?php
                                        switch($message['status']) {
                                            case 'new':
                                                echo 'Новое';
                                                break;
                                            case 'in_progress':
                                                echo 'В обработке';
                                                break;
                                            case 'resolved':
                                                echo 'Решено';
                                                break;
                                            default:
                                                echo 'Статус неизвестен';
                                        }
                                        ?>
                                    </span>
                                </div>

                                <?php if ($message['admin_response']): ?>
                                    <div class="mt-4 border-t border-[#3b5467] pt-4">
                                        <p class="text-sm text-[#28eab8] mb-2">Ответ администратора:</p>
                                        <p class="text-gray-300"><?= nl2br(htmlspecialchars($message['admin_response'])) ?></p>
                                    </div>
                                    <div class="bg-[#1d3547] rounded-lg p-4 mt-2">
                                        <p class="text-sm font-medium text-[#28eab8] mb-2">Ответ поддержки:</p>
                                        <p class="text-gray-300"><?= nl2br(htmlspecialchars($message['admin_response'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="flex items-center mt-2">
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    
                                    switch($message['status']) {
                                        case 'new':
                                            $statusClass = 'bg-blue-500/20 text-blue-400';
                                            $statusText = 'Новое';
                                            break;
                                        case 'in_progress':
                                            $statusClass = 'bg-yellow-500/20 text-yellow-400';
                                            $statusText = 'В обработке';
                                            break;
                                        case 'resolved':
                                            $statusClass = 'bg-green-500/20 text-green-400';
                                            $statusText = 'Решено';
                                            break;
                                    }
                                    ?>
                                    <span class="text-sm px-2 py-1 rounded-full <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'components/footer.php'; ?>

    <script>
        document.getElementById('supportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('subject', document.getElementById('subject').value);
            formData.append('message', document.getElementById('message').value);
            
            fetch('send_support_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Сообщение успешно отправлено!');
                    location.reload();
                } else {
                    alert(data.message || 'Произошла ошибка при отправке сообщения');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при отправке сообщения');
            });
        });
    </script>
</body>
</html>