<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получаем избранные товары пользователя
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM favorites f 
    JOIN products p ON f.product_id = p.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE f.user_id = ? 
    ORDER BY f.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favoriteProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'components/header.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное - UrbanAttire</title>
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
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen bg-[#061927]">
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-white mb-8">Избранное</h1>
            
            <?php if ($favoriteProducts): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php foreach ($favoriteProducts as $product): ?>
                        <div class="relative bg-[#182b39] border border-[#3b5467] rounded-xl shadow-sm overflow-hidden group hover:shadow-[0_20px_50px_rgba(40,234,184,0.15)] hover:scale-[1.02] hover:rotate-[0.5deg] transition-all duration-500 flex flex-col h-full">
                            <div class="absolute inset-0 bg-gradient-to-tl from-[#28eab8]/5 via-[#3b5467]/10 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                            <div class="absolute inset-0 bg-[url('/public/images/noise.png')] opacity-[0.03] group-hover:opacity-[0.05] transition-opacity duration-700"></div>
                            <div class="h-64 overflow-hidden">
                                <img src="<?= !empty($product['image']) && file_exists('public/images/' . $product['image']) 
                                    ? '/public/images/' . htmlspecialchars($product['image']) 
                                    : 'http://dummyimage.com/300x200/166534/ffffff.gif&text=' . urlencode($product['name']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="w-full h-full object-contain p-4 transition-all duration-700 group-hover:scale-105 group-hover:rotate-[-1deg]">
                                <button class="favorite-btn absolute top-4 right-4 w-10 h-10 bg-[#061927]/80 backdrop-blur-sm rounded-full flex items-center justify-center text-[#28eab8] hover:scale-110 hover:rotate-[360deg] transition-all duration-500" 
                                        data-product-id="<?= $product['id'] ?>">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            <div class="relative z-10 p-6 flex flex-col flex-grow">
                                <div class="flex items-start gap-2 mb-4">
                                    <span class="inline-flex items-center text-sm font-medium text-[#061927] bg-[#28eab8] rounded-full px-3 py-1 shadow-lg transform group-hover:translate-y-[-2px] group-hover:shadow-[0_5px_15px_rgba(40,234,184,0.3)] transition-all duration-500">
                                        <?= htmlspecialchars($product['category_name'] ?? 'Без категории') ?>
                                    </span>
                                </div>
                                <h3 class="text-xl font-semibold text-white group-hover:text-[#28eab8] transition-all duration-500 group-hover:translate-x-1"><?= htmlspecialchars($product['name']) ?></h3>
                                <p class="mt-2 text-sm text-gray-300 line-clamp-2 group-hover:text-gray-200 transition-colors duration-500"><?= htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : '') ?></p>
                                <div class="mt-auto pt-4 flex justify-between items-center">
                                    <span class="text-xl font-bold text-[#28eab8] transition-all duration-500 group-hover:scale-110 group-hover:translate-x-1"><?= htmlspecialchars($product['price']) ?> ₽</span>
                                    <a href="cart.php?add=<?= htmlspecialchars($product['id']) ?>" 
                                       class="relative inline-flex items-center bg-[#28eab8] text-[#061927] py-2 px-4 rounded-lg text-sm font-medium overflow-hidden transition-all duration-300 hover:shadow-[0_5px_15px_rgba(40,234,184,0.3)] group/btn">
                                        <span class="relative z-10 flex items-center transition-transform duration-300 group-hover/btn:translate-x-1">
                                            <i class="fas fa-shopping-cart mr-2 transition-transform duration-300 group-hover/btn:rotate-12"></i>
                                            В корзину
                                        </span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-[#28eab8] via-[#28eab8]/80 to-[#28eab8] translate-x-[-100%] group-hover/btn:translate-x-0 transition-transform duration-300"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-[#182b39] text-white rounded-xl shadow-sm p-8 text-center">
                    <div class="text-6xl text-[#3b5467] mb-4">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Список избранного пуст</h3>
                    <p class="text-gray-300 mb-4">Добавьте товары в избранное, чтобы они отображались здесь.</p>
                    <a href="catalog.php" class="inline-block bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] px-4 py-2 rounded-lg transition duration-300">
                        Перейти в каталог
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>

    <script>
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const formData = new FormData();
                formData.append('product_id', productId);

                fetch('toggle_favorite.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.action === 'removed') {
                            this.closest('.grid > div').remove();
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при обновлении избранного');
                });
            });
        });
    </script>
</body>
</html> 