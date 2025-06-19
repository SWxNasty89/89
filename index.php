<?php
// === НАЧАЛО СКРИПТА ===

// 1. Включаем отображение ошибок (ЗАКОММЕНТИРОВАНО ПОСЛЕ ОТЛАДКИ)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// 1.1 Устанавливаем кодировку UTF-8 для вывода
header('Content-Type: text/html; charset=utf-8');

// 2. Запускаем сессию В САМОМ НАЧАЛЕ
session_start();

// 3. Подключаем файл конфигурации БД
require_once __DIR__ . '/config/db.php';

// 4. Инициализируем переменные
$featuredProducts = [];
$showcaseCategories = [];
$dbError = null;

// 5. Выполняем запросы к БД внутри try...catch
try {
    if (!isset($pdo)) {
        throw new Exception("Объект PDO не был создан. Проверьте db.php.");
    }

    // Get featured products
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p
                         LEFT JOIN categories c ON p.category_id = c.id
                         ORDER BY p.id DESC LIMIT 8");
    $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get categories for showcase
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name LIMIT 6");
    $showcaseCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
         $dbError = "Ошибка: Одна или несколько таблиц базы данных (products, categories) не найдены.";
    } else {
        $dbError = "Ошибка базы данных при загрузке данных страницы: " . $e->getMessage();
    }
    error_log("PDOException in index.php: " . $e->getMessage());
} catch (Exception $e) {
    $dbError = "Произошла ошибка: " . $e->getMessage();
    error_log("Exception in index.php: " . $e->getMessage());
}

// 6. Подключаем шапку сайта
try {
    include __DIR__ . '/components/header.php';
} catch (Throwable $e) {
    // Если header не подключится, сайт будет сломан, лучше прервать
    error_log("FATAL: Failed to include header.php: " . $e->getMessage());
    die("Критическая ошибка: Не удалось загрузить шапку сайта.");
}

ob_start('minify_output');
function minify_output($buffer) {
    $search = array('/\>[\s]+/s', '/[\s]+\</s', '/(\s)+/s');
    $replace = array('>','<','\\1');
    return preg_replace($search, $replace, $buffer);
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanAttire - Магазин мобильных аксессуаров</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // ... (Ваш конфиг Tailwind) ...
        tailwind.config = {
            theme: {
                extend: {
                     colors: {
                        primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#061927', 950: '#061927' },
                        secondary: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#3b5467', 800: '#334155', 900: '#1e293b', 950: '#0f172a' },
                        success: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                        background: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#182b39', 950: '#0f172a' }
                    },
                    screens: { 'xs': '375px', 'sm': '640px', 'md': '768px', 'lg': '1024px', 'xl': '1280px', '2xl': '1536px' }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.min.css">

    <style>
        @media (max-width: 640px) { .container { padding-left: 1rem; padding-right: 1rem; } }
        .db-error-message { background-color: #ffdddd; border: 1px solid #ffaaaa; color: #d8000c; padding: 15px; margin: 15px; text-align: center; font-family: sans-serif; }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-[#061927]">

    <?php // Отображаем сообщение об ошибке БД, если она была при ЗАГРУЗКЕ основных данных ?>
    <?php if ($dbError): ?>
        <div class="db-error-message">
            <strong>Внимание! Возникла проблема при загрузке данных:</strong><br>
            <?php echo htmlspecialchars($dbError); ?>
        </div>
    <?php endif; ?>

    <!-- Hero Banner Section -->
    <section class="bg-[#061927] text-white py-8 sm:py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12">
                <div class="w-full md:w-1/2 mb-6 md:mb-0 text-center md:text-left">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4">Стильные аксессуары для вашего смартфона</h1>
                    <p class="text-base sm:text-lg text-gray-200 mb-6 sm:mb-8">Защитите свой телефон с нашими премиальными чехлами, зарядными устройствами и другими аксессуарами</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="catalog.php" class="w-full sm:w-auto text-center bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] px-6 py-3 rounded-md font-medium transition duration-300">
                            Смотреть каталог
                        </a>
                        <a href="about.php" class="w-full sm:w-auto text-center bg-transparent border border-[#3b5467] hover:bg-[#3b5467]/20 text-white px-6 py-3 rounded-md font-medium transition duration-300">
                            О нас
                        </a>
                    </div>
                </div>
                <div class="w-full md:w-2/5">
                    <img src="/public/images/122.jpg" alt="Premium Mobile Accessories" class="rounded-xl shadow-2xl w-full max-w-md mx-auto h-[200px] sm:h-[250px] md:h-[300px] object-cover" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Showcase -->
    <section class="py-8 sm:py-12 md:py-16 bg-[#182b39] text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-8 sm:mb-12">Популярные категории</h2>
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6 md:gap-8">
                    <?php if (!empty($showcaseCategories)): ?>
                        <?php foreach ($showcaseCategories as $category): ?>
                            <a href="catalog.php?category=<?php echo htmlspecialchars(urlencode(strtolower($category['name']))); ?>" class="relative bg-[#061927] rounded-xl p-8 text-center group overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-b from-[#3b5467]/20 to-[#061927] opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                                <div class="relative z-10">
                                    <div class="w-24 h-24 mx-auto mb-6 bg-[#3b5467]/50 backdrop-blur-xl rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <i class="fas fa-tag text-4xl text-white group-hover:text-[#28eab8] transition-colors duration-500"></i>
                                    </div>
                                    <h3 class="font-medium text-white text-base group-hover:text-[#28eab8] transition-colors duration-500"><?php echo htmlspecialchars($category['name']); ?></h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                         <?php for ($i = count($showcaseCategories); $i < 5; $i++): ?>
                             <div class="relative bg-[#061927] rounded-xl p-8 text-center opacity-50">
                                <div class="w-24 h-24 mx-auto mb-6 bg-[#3b5467]/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-tag text-4xl text-gray-500"></i>
                                </div>
                                <h3 class="font-medium text-gray-400 text-base">Категория</h3>
                             </div>
                         <?php endfor; ?>
                    <?php else: ?>
                        <p class="col-span-full text-center text-gray-400">Категории не найдены.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-8 sm:py-12 md:py-16 bg-[#061927]">
         <div class="container mx-auto px-4">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 sm:mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4 sm:mb-0">Популярные товары</h2>
                <a href="catalog.php" class="text-[#28eab8] hover:text-[#28eab8]/80 font-medium flex items-center">
                    Все товары <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach ($featuredProducts as $product): ?>
                    <div class="relative bg-[#182b39] border border-[#3b5467] rounded-xl shadow-sm overflow-hidden group hover:shadow-[0_20px_50px_rgba(40,234,184,0.15)] hover:scale-[1.02] hover:rotate-[0.5deg] transition-all duration-500 flex flex-col h-full product-card cursor-pointer"
                         data-product-id="<?= $product['id'] ?>"
                         data-title="<?= htmlspecialchars($product['name']) ?>"
                         data-description="<?= htmlspecialchars($product['description']) ?>"
                         data-price="<?= $product['price'] ?>"
                         data-image="<?= $product['image'] ?>">
                        <div class="absolute inset-0 bg-gradient-to-tl from-[#28eab8]/5 via-[#3b5467]/10 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                        <div class="absolute inset-0 bg-[url('/public/images/noise.png')] opacity-[0.03] group-hover:opacity-[0.05] transition-opacity duration-700"></div>
                        <div class="h-64 overflow-hidden">
                             <?php
                                $imageWebPath = '/public/images/' . htmlspecialchars($product['image'] ?? 'default.jpg');
                                $imageFilePath = __DIR__ . '/public/images/' . ($product['image'] ?? '');
                                $imageSrc = (!empty($product['image']) && file_exists($imageFilePath))
                                    ? $imageWebPath
                                    : 'http://dummyimage.com/600x400/166534/ffffff.gif&text=' . urlencode($product['name'] ?? 'Product');
                             ?>
                            <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['name'] ?? 'Товар'); ?>" class="w-full h-full object-contain p-4 transition-all duration-700 group-hover:scale-105 group-hover:rotate-[-1deg]" loading="lazy">

                            <?php // === БЛОК С TRY...CATCH ДЛЯ ИЗБРАННОГО ===
                            $isFavorite = false;
                            if (isset($_SESSION['user_id'])) {
                                try {
                                    if (!isset($pdo)) {
                                         throw new Exception("Объект PDO недоступен для проверки избранного.");
                                    }
                                    $favStmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
                                    $currentProductId = $product['id'] ?? null;
                                    if ($currentProductId !== null) {
                                        $favStmt->execute(['user_id' => $_SESSION['user_id'], 'product_id' => $currentProductId]);
                                        $isFavorite = $favStmt->fetch() !== false;
                                    }
                                } catch (PDOException $e) {
                                    error_log("DB Error checking favorite in index.php loop for product ID " . ($currentProductId ?? 'N/A') . ": " . $e->getMessage());
                                } catch (Exception $e) {
                                    error_log("General Error checking favorite in index.php loop: " . $e->getMessage());
                                }
                            }
                            // === КОНЕЦ БЛОКА С TRY...CATCH ДЛЯ ИЗБРАННОГО ===
                            ?>
                            <button class="favorite-btn absolute top-4 right-4 w-10 h-10 bg-[#061927]/80 backdrop-blur-sm rounded-full flex items-center justify-center <?php echo $isFavorite ? 'text-[#28eab8] shadow-[0_0_15px_rgba(40,234,184,0.3)]' : 'text-white hover:text-[#28eab8]'; ?> hover:scale-110 hover:rotate-[360deg] transition-all duration-200 group/fav"
                                    data-product-id="<?php echo htmlspecialchars($product['id'] ?? 0); ?>">
                                <i class="fas fa-heart transform transition-all duration-200 group-hover/fav:scale-110 <?php echo $isFavorite ? 'text-[#28eab8]' : 'text-white'; ?>"></i>
                                <div class="absolute inset-0 bg-[#28eab8]/20 rounded-full opacity-0 group-hover/fav:opacity-100 transition-opacity duration-200"></div>
                            </button>
                        </div>
                        <div class="relative z-10 p-6 flex flex-col flex-grow">
                            <div class="flex items-start gap-2 mb-4">
                                <span class="inline-flex items-center text-sm font-medium text-[#061927] bg-[#28eab8] rounded-full px-3 py-1 shadow-lg transform group-hover:translate-y-[-2px] group-hover:shadow-[0_5px_15px_rgba(40,234,184,0.3)] transition-all duration-500">
                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-semibold text-white group-hover:text-[#28eab8] transition-all duration-500 group-hover:translate-x-1"><?php echo htmlspecialchars($product['name'] ?? 'Название товара'); ?></h3>
                            <p class="mt-2 text-sm text-gray-300 line-clamp-2 group-hover:text-gray-200 transition-colors duration-500"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 100)) . (strlen($product['description'] ?? '') > 100 ? '...' : ''); ?></p>
                            <div class="mt-auto pt-4 flex justify-between items-center">
                                <span class="text-xl font-bold text-[#28eab8] transition-all duration-500 group-hover:scale-110 group-hover:translate-x-1"><?php echo htmlspecialchars(number_format($product['price'] ?? 0, 2, '.', '')); ?> ₽</span>
                                <a href="cart.php?add=<?php echo htmlspecialchars($product['id'] ?? 0); ?>"
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
                <?php else: ?>
                     <p class="col-span-full text-center text-gray-400 py-10">
                        <?php if ($dbError): ?>
                            Не удалось загрузить товары из-за ошибки базы данных.
                        <?php else: ?>
                            Популярные товары не найдены.
                        <?php endif; ?>
                     </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php // РАСКОММЕНТИРУЕМ ПОДКЛЮЧЕНИЯ ?>
    <?php
    try {
        include __DIR__ . '/components/why_choose_us.php';
    } catch (Throwable $e) {
        error_log("Error including why_choose_us.php: " . $e->getMessage());
        // Не прерываем выполнение, просто блок не отобразится
    }
    ?>

    <!-- Newsletter -->
    <section class="py-8 sm:py-12 md:py-16 bg-[#061927] text-white">
         <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Подпишитесь на рассылку</h2>
                <p class="text-gray-300 mb-6 sm:mb-8">Получайте уведомления о новинках, акциях и специальных предложениях</p>
                <form action="subscribe.php" method="POST" class="flex flex-col sm:flex-row gap-4 justify-center">
                    <input type="email" name="email" placeholder="Ваш email" required class="w-full sm:w-auto px-4 py-3 rounded-md bg-[#182b39] border border-[#3b5467] text-white placeholder-gray-400 flex-grow max-w-md focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]">
                    <button type="submit" class="w-full sm:w-auto bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] px-6 py-3 rounded-md font-medium transition duration-300">
                        Подписаться
                    </button>
                </form>
            </div>
        </div>
    </section>

    <?php // РАСКОММЕНТИРУЕМ ПОДКЛЮЧЕНИЯ ?>
    <?php
    try {
        include __DIR__ . '/components/footer.php';
    } catch (Throwable $e) {
         error_log("Error including footer.php: " . $e->getMessage());
         // Если футер не загрузится, страница останется без него
    }
    ?>

    <!-- Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-[#182b39] rounded-xl shadow-xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="p-6 border-b border-[#3b5467]">
                    <div class="flex justify-between items-start">
                        <h2 id="modalTitle" class="text-2xl font-bold text-white" data-product-id=""></h2>
                        <button onclick="closeProductModal()" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column: Image -->
                        <div class="space-y-6">
                            <!-- Product Image -->
                            <div class="aspect-square rounded-xl overflow-hidden bg-[#1d3547]">
                                <img id="modalImage" src="" alt="" class="w-full h-full object-contain p-4">
                            </div>
                        </div>

                        <!-- Right Column: Details -->
                        <div class="space-y-6">
                            <!-- Product Description -->
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Описание товара</h3>
                                <p id="modalDescription" class="text-gray-300"></p>
                            </div>

                            <!-- Product Price -->
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Цена</h3>
                                <p id="modalPrice" class="text-[#28eab8] text-2xl font-bold"></p>
                            </div>

                            <!-- Comments Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4">Комментарии</h3>

                                <!-- Comment Form -->
                                <form id="commentForm" class="mb-6">
                                    <input type="hidden" id="commentProductId" name="product_id" value="">
                                    <div class="mb-4">
                                        <textarea id="commentText" 
                                                class="w-full bg-[#1d3547] border border-[#3b5467] rounded-md py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28eab8] focus:border-[#28eab8]"
                                                rows="3"
                                                placeholder="Оставьте ваш комментарий"></textarea>
                                    </div>
                                    <button type="submit" 
                                            class="bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] px-6 py-2 rounded-md font-medium transition duration-300">
                                        <i class="fas fa-paper-plane mr-1"></i> Отправить
                                    </button>
                                </form>

                                <!-- Comments List with Scroll -->
                                <div id="commentsList" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                    <!-- Comments will be added dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php // --- JAVASCRIPT --- ?>
    <script src="/public/js/script.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>