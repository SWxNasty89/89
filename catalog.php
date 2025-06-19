<?php
require_once 'config/db.php';

// Инициализация переменных
$category_id = null;
$search_query = null;
$products = [];
$current_category = null;

// Получение параметров из GET-запроса
if (isset($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];
}

if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Получение категорий для фильтров
$categoriesStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Построение SQL-запроса
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id";

$conditions = [];
$params = [];

// Добавление условий поиска
if ($category_id) {
    $conditions[] = "p.category_id = :category_id";
    $params[':category_id'] = $category_id;
    
    // Получение имени текущей категории
    foreach ($categories as $cat) {
        if ($cat['id'] == $category_id) {
            $current_category = $cat['name'];
            break;
        }
    }
}

if ($search_query) {
    $conditions[] = "(p.name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = "%{$search_query}%";
}

// Добавление условий в запрос
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Добавление сортировки
$query .= " ORDER BY p.name";

// Выполнение запроса
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Логирование ошибки
    error_log("Database error: " . $e->getMessage());
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $current_category ? htmlspecialchars($current_category) : ($search_query ? "Поиск: " . htmlspecialchars($search_query) : 'Каталог товаров') ?> - UrbanAttire</title>
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
<body class="flex flex-col min-h-screen bg-[#061927]">
    <?php include 'components/header.php'; ?>
    
    <main class="flex-grow">
        <!-- Catalog Header -->
        <section class="bg-[#061927] text-white py-10">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold"><?= $current_category ? htmlspecialchars($current_category) : ($search_query ? "Поиск: " . htmlspecialchars($search_query) : 'Каталог товаров') ?></h1>
                <div class="flex items-center text-sm mt-2">
                    <a href="index.php" class="text-gray-300 hover:text-[#28eab8]">Главная</a>
                    <span class="mx-2">›</span>
                    <a href="catalog.php" class="text-gray-300 hover:text-[#28eab8]">Каталог</a>
                    <?php if ($current_category): ?>
                    <span class="mx-2">›</span>
                    <span class="text-[#28eab8]"><?= htmlspecialchars($current_category) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar/Filters -->
                <div class="md:w-1/4">
                    <div class="bg-[#182b39] text-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Категории</h2>
                        <ul class="space-y-2">
                            <li>
                                <a href="catalog.php" class="block py-2 px-3 rounded <?= !$category_id ? 'bg-[#28eab8] text-[#061927] font-medium' : 'text-white hover:bg-[#3b5467]' ?>">
                                    Все товары
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="catalog.php?category_id=<?= $cat['id'] ?>" 
                                   class="block py-2 px-3 rounded <?= $category_id == $cat['id'] ? 'bg-[#28eab8] text-[#061927] font-medium' : 'text-white hover:bg-[#3b5467]' ?>">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="md:w-3/4">
                    <?php if ($products): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($products as $product): ?>
                        <div class="relative bg-[#182b39] border border-[#3b5467] rounded-xl shadow-sm overflow-hidden group hover:shadow-[0_20px_50px_rgba(40,234,184,0.15)] hover:scale-[1.02] hover:rotate-[0.5deg] transition-all duration-500 flex flex-col h-full product-card"
                             data-product-id="<?= $product['id'] ?>"
                             data-title="<?= htmlspecialchars($product['name']) ?>"
                             data-description="<?= htmlspecialchars($product['description']) ?>"
                             data-price="<?= htmlspecialchars($product['price']) ?>"
                             data-image="<?= htmlspecialchars($product['image']) ?>">
                            <div class="absolute inset-0 bg-gradient-to-tl from-[#28eab8]/5 via-[#3b5467]/10 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                            <div class="absolute inset-0 bg-[url('/public/images/noise.png')] opacity-[0.03] group-hover:opacity-[0.05] transition-opacity duration-700"></div>
                            <div class="h-64 overflow-hidden">
                                <img src="<?= !empty($product['image']) && file_exists('public/images/' . $product['image']) 
                                    ? '/public/images/' . htmlspecialchars($product['image']) 
                                    : 'http://dummyimage.com/600x400/166534/ffffff.gif&text=' . urlencode($product['name']) ?>" 
                                    alt="<?= htmlspecialchars($product['name']) ?>" 
                                    class="w-full h-full object-contain p-4 transition-all duration-700 group-hover:scale-105 group-hover:rotate-[-1deg]">
                                <?php
                                $isFavorite = false;
                                if (isset($_SESSION['user_id'])) {
                                    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
                                    $stmt->execute([$_SESSION['user_id'], $product['id']]);
                                    $isFavorite = $stmt->fetch() !== false;
                                }
                                ?>
                                <button class="favorite-btn absolute top-4 right-4 w-10 h-10 bg-[#061927]/80 backdrop-blur-sm rounded-full flex items-center justify-center <?= $isFavorite ? 'text-[#28eab8] shadow-[0_0_15px_rgba(40,234,184,0.3)]' : 'text-white hover:text-[#28eab8]' ?> hover:scale-110 hover:rotate-[360deg] transition-all duration-200 group/fav" 
                                        data-product-id="<?= $product['id'] ?>">
                                    <i class="fas fa-heart transform transition-all duration-200 group-hover/fav:scale-110 <?= $isFavorite ? 'text-[#28eab8]' : 'text-white' ?>"></i>
                                    <div class="absolute inset-0 bg-[#28eab8]/20 rounded-full opacity-0 group-hover/fav:opacity-100 transition-opacity duration-200"></div>
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
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Товары не найдены</h3>
                        <p class="text-gray-300 mb-4">К сожалению, в этой категории пока нет товаров.</p>
                        <a href="catalog.php" class="inline-block bg-[#28eab8] hover:bg-[#28eab8]/90 text-[#061927] px-4 py-2 rounded-lg transition duration-300">
                            Смотреть все товары
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>

    <!-- Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-[#182b39] rounded-xl shadow-xl w-full max-w-7xl max-h-[90vh] overflow-y-auto">
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

    <script>
        // Debug logging
        console.log('Script loaded');

        // Добавляем ID текущего пользователя
        const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        console.log('Current user ID:', currentUserId);

        function openProductModal(productId, title, description, price, image) {
            console.log('openProductModal called with:', { productId, title, description, price, image });
            
            const modalTitle = document.getElementById('modalTitle');
            const modalDescription = document.getElementById('modalDescription');
            const modalPrice = document.getElementById('modalPrice');
            const modalImage = document.getElementById('modalImage');
            const commentProductId = document.getElementById('commentProductId');
            
            modalTitle.textContent = title;
            modalTitle.dataset.productId = productId;
            modalDescription.textContent = description;
            modalPrice.textContent = price + ' ₽';
            commentProductId.value = productId;
            
            const imagePath = image.startsWith('http') ? image : '/public/images/' + image;
            modalImage.src = imagePath;
            modalImage.alt = title;
            
            const modal = document.getElementById('productModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Загружаем комментарии
            loadComments(productId);
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function loadComments(productId) {
            console.log('Loading comments for product:', productId);
            const commentsList = document.getElementById('commentsList');
            
            if (!commentsList) {
                console.error('Comments list element not found');
                return;
            }

            // Показываем индикатор загрузки
            commentsList.innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-[#28eab8] border-t-transparent"></div></div>';

            // Загружаем комментарии
            fetch(`get_comments.php?product_id=${productId}`)
                .then(response => response.json())
                .then(comments => {
                    console.log('Received comments:', comments);
                    const commentsContainer = document.querySelector('.comments-container');
                    commentsContainer.innerHTML = '';

                    if (comments.length === 0) {
                        commentsContainer.innerHTML = '<p>Нет комментариев</p>';
                        return;
                    }

                    comments.forEach(comment => {
                        console.log('Processing comment:', comment);
                        const isAuthor = parseInt(comment.user_id) === parseInt(currentUserId);
                        const commentHtml = `
                            <div class="comment" data-comment-id="${comment.id}">
                                <div class="comment-header">
                                    <span class="username">${escapeHtml(comment.username)}</span>
                                    <span class="date">${comment.date}</span>
                                    ${isAuthor ? `
                                        <div class="comment-actions">
                                            <button type="button" class="edit-comment-btn" data-comment-id="${comment.id}">
                                                Редактировать
                                            </button>
                                            <button type="button" class="delete-comment-btn" data-comment-id="${comment.id}">
                                                Удалить
                                            </button>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="comment-text">${escapeHtml(comment.text)}</div>
                            </div>
                        `;
                        commentsContainer.insertAdjacentHTML('beforeend', commentHtml);
                    });

                    // Добавляем обработчики событий для кнопок
                    document.querySelectorAll('.delete-comment-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const commentId = this.dataset.commentId;
                            console.log('Delete button clicked for comment:', commentId);
                            deleteComment(commentId, productId);
                        });
                    });

                    document.querySelectorAll('.edit-comment-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const commentId = this.dataset.commentId;
                            console.log('Edit button clicked for comment:', commentId);
                            editComment(commentId);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    const commentsContainer = document.querySelector('.comments-container');
                    commentsContainer.innerHTML = `
                        <div class="error-message">
                            Ошибка при загрузке комментариев
                            <button onclick="loadComments(${productId})">Повторить</button>
                        </div>
                    `;
                });
        }

        function deleteComment(commentId, productId) {
            console.log('Attempting to delete comment:', commentId);
            if (confirm('Вы уверены, что хотите удалить этот комментарий?')) {
                fetch('delete_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ comment_id: commentId })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Delete response:', data);
                    if (data.success) {
                        showNotification('Комментарий успешно удален', 'success');
                        loadComments(productId);
                    } else {
                        showNotification(data.message || 'Ошибка при удалении комментария', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting comment:', error);
                    showNotification('Ошибка при удалении комментария', 'error');
                });
            }
        }

        // Добавляем обработчик кликов по карточкам товаров
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, adding click listeners');
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Не открываем модальное окно при клике на кнопку избранного или корзины
                    if (e.target.closest('.favorite-btn') || e.target.closest('a[href*="cart.php"]')) {
                        return;
                    }
                    
                    console.log('Card clicked');
                    const productId = this.dataset.productId;
                    const title = this.dataset.title;
                    const description = this.dataset.description;
                    const price = this.dataset.price;
                    const image = this.dataset.image;
                    
                    console.log('Product data:', { productId, title, description, price, image });
                    openProductModal(productId, title, description, price, image);
                });
            });
        });

        // Добавляем стили для кастомного скроллбара
        const style = document.createElement('style');
        style.textContent = `
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #1d3547;
                border-radius: 3px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #28eab8;
                border-radius: 3px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #28eab8/80;
            }
        `;
        document.head.appendChild(style);

        // Функции для работы с комментариями
        function editComment(commentId) {
            const newText = prompt('Введите новый текст комментария:');
            if (newText === null || newText === '') return;

            const modalTitle = document.getElementById('modalTitle');
            const productId = modalTitle.dataset.productId;

            fetch('edit_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    comment_id: commentId,
                    text: newText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadComments(productId);
                    showNotification('Комментарий успешно отредактирован', 'success');
                } else {
                    showNotification(data.message || 'Ошибка при редактировании комментария', 'error');
                }
            })
            .catch(error => {
                console.error('Error editing comment:', error);
                showNotification('Ошибка при редактировании комментария', 'error');
            });
        }

        function showNotification(message, type = 'success') {
            console.log('Showing notification:', message, type);
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-[#28eab8] text-[#061927]' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Обработчик отправки формы комментария
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const commentText = document.getElementById('commentText').value;
            const productId = document.getElementById('commentProductId').value;
            
            if (commentText.trim() && productId) {
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('comment', commentText);
                
                fetch('add_comment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.reset();
                        loadComments(productId);
                        showNotification('Комментарий успешно добавлен!', 'success');
                    } else {
                        showNotification(data.message || 'Ошибка при добавлении комментария', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error adding comment:', error);
                    showNotification('Ошибка при отправке комментария', 'error');
                });
            } else {
                showNotification('Пожалуйста, введите комментарий', 'error');
            }
        });

        // Закрытие модального окна при клике вне его
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });

        // Закрытие модального окна по клавише Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProductModal();
            }
        });
    </script>
</body>
</html>
