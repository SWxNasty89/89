<?php
session_start();
require_once 'config/db.php';

// Handle "add to cart" from catalog
if (isset($_GET['add'])) {
    $productId = (int)$_GET['add'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
    header("Location: cart.php");
    exit();
}

// Handle actions (remove, decrease, increase)
if (isset($_GET['action'])) {
    $productId = (int)($_GET['id'] ?? 0);
    if ($_GET['action'] === 'remove') {
        unset($_SESSION['cart'][$productId]);
        header("Location: cart.php");
        exit();
    }
    if ($_GET['action'] === 'decrease') {
        if (isset($_SESSION['cart'][$productId]) && $_SESSION['cart'][$productId] > 1) {
            $_SESSION['cart'][$productId]--;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
        header("Location: cart.php");
        exit();
    }
    if ($_GET['action'] === 'increase') {
        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
        header("Location: cart.php");
        exit();
    }
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart = $_SESSION['cart'];

// Get product data
$items = [];
$total = 0;
if ($cart) {
    foreach ($cart as $id => $quantity) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $quantity;
            $product['subtotal'] = $quantity * $product['price'];
            $total += $product['subtotal'];
            $items[] = $product;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - UrbanAttire</title>
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
        <!-- Cart Header -->
        <section class="bg-[#182b39] text-white py-10">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold">Корзина</h1>
                <div class="flex items-center text-sm mt-2">
                    <a href="index.php" class="text-gray-300 hover:text-[#28eab8] transition-colors duration-300">Главная</a>
                    <span class="mx-2 text-[#3b5467]">›</span>
                    <span class="text-[#28eab8]">Корзина</span>
                </div>
            </div>
        </section>
        
        <div class="container mx-auto px-4 py-8">
            <?php if ($items): ?>
            <div class="bg-[#182b39] rounded-xl shadow-lg overflow-hidden border border-[#3b5467]">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#3b5467]">
                        <thead class="bg-[#061927]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Товар</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Цена</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Количество</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Сумма</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#182b39] divide-y divide-[#3b5467]">
                            <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-[#061927]/50 transition-colors duration-300">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-[#061927] p-2">
                                            <img src="<?= !empty($item['image']) && file_exists('public/images/' . $item['image']) 
                                                ? '/public/images/' . htmlspecialchars($item['image']) 
                                                : 'http://dummyimage.com/300x200/166534/ffffff.gif&text=' . urlencode($item['name']) ?>" 
                                                alt="<?= htmlspecialchars($item['name']) ?>" 
                                                class="w-full h-full object-contain transition duration-500 hover:scale-105">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white"><?= htmlspecialchars($item['name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-[#28eab8]"><?= htmlspecialchars($item['price']) ?> ₽</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <a href="cart.php?action=decrease&id=<?= $item['id'] ?>" 
                                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#061927] text-gray-300 hover:text-[#28eab8] hover:bg-[#3b5467] transition-all duration-300">
                                            <i class="fas fa-minus"></i>
                                        </a>
                                        <span class="mx-3 text-white w-8 text-center"><?= htmlspecialchars($item['quantity']) ?></span>
                                        <a href="cart.php?action=increase&id=<?= $item['id'] ?>" 
                                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#061927] text-gray-300 hover:text-[#28eab8] hover:bg-[#3b5467] transition-all duration-300">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-[#28eab8]"><?= htmlspecialchars($item['subtotal']) ?> ₽</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="cart.php?action=remove&id=<?= $item['id'] ?>" 
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-[#061927] text-red-400 hover:text-red-300 hover:bg-[#3b5467] transition-all duration-300">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="border-t border-[#3b5467] px-6 py-4 bg-[#061927]">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <a href="catalog.php" class="text-[#28eab8] hover:text-[#28eab8]/80 font-medium flex items-center transition-colors duration-300">
                                <i class="fas fa-arrow-left mr-2"></i> Продолжить покупки
                            </a>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-white mb-4">
                                Итого: <span class="text-2xl font-bold text-[#28eab8]"><?= htmlspecialchars($total) ?> ₽</span>
                            </div>
                            <form method="post" action="order.php">
                                <button type="submit" 
                                        class="relative inline-flex items-center bg-[#28eab8] text-[#061927] py-3 px-6 rounded-lg text-sm font-medium overflow-hidden transition-all duration-300 hover:shadow-[0_5px_15px_rgba(40,234,184,0.3)] group/btn">
                                    <span class="relative z-10 flex items-center transition-transform duration-300 group-hover/btn:translate-x-1">
                                        <i class="fas fa-credit-card mr-2 transition-transform duration-300 group-hover/btn:rotate-12"></i>
                                        Оформить заказ
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-[#28eab8] via-[#28eab8]/80 to-[#28eab8] translate-x-[-100%] group-hover/btn:translate-x-0 transition-transform duration-300"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-[#182b39] rounded-xl shadow-lg p-8 text-center max-w-2xl mx-auto border border-[#3b5467]">
                <div class="text-6xl text-[#3b5467] mb-4">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Ваша корзина пуста</h3>
                <p class="text-gray-300 mb-6">Добавьте товары в корзину, чтобы оформить заказ</p>
                <a href="catalog.php" 
                   class="relative inline-flex items-center bg-[#28eab8] text-[#061927] py-3 px-6 rounded-lg text-sm font-medium overflow-hidden transition-all duration-300 hover:shadow-[0_5px_15px_rgba(40,234,184,0.3)] group/btn">
                    <span class="relative z-10 flex items-center transition-transform duration-300 group-hover/btn:translate-x-1">
                        <i class="fas fa-shopping-bag mr-2 transition-transform duration-300 group-hover/btn:rotate-12"></i>
                        Перейти в каталог
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#28eab8] via-[#28eab8]/80 to-[#28eab8] translate-x-[-100%] group-hover/btn:translate-x-0 transition-transform duration-300"></div>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>
</body>
</html>