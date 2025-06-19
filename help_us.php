<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Помоги нам - UrbanAttire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Кастомные стили для чекбоксов */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #28eab8;
            border-radius: 4px;
            background-color: transparent;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .custom-checkbox:checked {
            background-color: #28eab8;
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            color: #061927;
            font-size: 14px;
            font-weight: bold;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-checkbox:hover {
            border-color: #28eab8;
            box-shadow: 0 0 5px rgba(40, 234, 184, 0.5);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox-label:hover {
            color: #28eab8;
        }
    </style>
</head>
<body class="bg-[#061927] text-white min-h-screen">
    <?php include 'components/header.php'; ?>

    <main class="flex-grow">
        <!-- Header -->
        <section class="bg-[#061927] text-white py-10">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold">Помоги нам стать лучше</h1>
                <div class="flex items-center text-sm mt-2">
                    <a href="index.php" class="text-gray-300 hover:text-[#28eab8]">Главная</a>
                    <span class="mx-2">›</span>
                    <a href="account.php" class="text-gray-300 hover:text-[#28eab8]">Личный кабинет</a>
                    <span class="mx-2">›</span>
                    <span class="text-[#28eab8]">Помоги нам</span>
                </div>
            </div>
        </section>

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-[#182b39] rounded-xl p-6">
                    <form id="surveyForm" class="space-y-8">
                        <!-- Вопрос 1 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">1. Что вам нравится больше всего в нашем сайте?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q1[]" value="navigation" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Удобная навигация</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q1[]" value="search" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Быстрый поиск товаров</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q1[]" value="design" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Красивый дизайн</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q1[]" value="selection" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Большой выбор аксессуаров</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q1[]" value="reviews" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Наличие отзывов</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 2 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">2. Какие функции сайта вы считаете самыми важными?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q2[]" value="cart" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Удобная корзина</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q2[]" value="filters" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Фильтры для поиска товаров</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q2[]" value="recommendations" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Рекомендации на основе покупок</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q2[]" value="tracking" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Отслеживание статуса заказа</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q2[]" value="support" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Возможность задать вопрос поддержке</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 3 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">3. Что вас раздражает при использовании нашего сайта?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q3[]" value="slow" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Медленная загрузка страниц</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q3[]" value="structure" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Запутанная структура</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q3[]" value="info" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Недостаточная информация о товарах</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q3[]" value="reviews" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Отсутствие отзывов или рейтинга</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q3[]" value="errors" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Ошибки при оформлении заказа</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 4 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">4. Как вы оцениваете удобство мобильной версии сайта?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q4[]" value="very_convenient" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Очень удобно</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q4[]" value="convenient" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Удобно, но есть нюансы</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q4[]" value="average" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Средне, можно улучшить</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q4[]" value="inconvenient" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Неудобно</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q4[]" value="not_used" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Не использую мобильную версию</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 5 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">5. Какая информация о товаре для вас важнее всего?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q5[]" value="compatibility" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Совместимость с устройствами</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q5[]" value="materials" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Материал и особенности</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q5[]" value="reviews" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Отзывы и рейтинги</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q5[]" value="video" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Видеообзор товара</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q5[]" value="warranty" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Гарантия и возврат</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 6 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">6. Что бы вы улучшили в дизайне сайта?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q6[]" value="modern" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Сделал(а) бы его более современным</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q6[]" value="visuals" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Добавил(а) больше визуальных элементов (фото, видео)</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q6[]" value="navigation" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Упростил(а) навигацию</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q6[]" value="dark_theme" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Добавил(а) темную тему</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q6[]" value="keep" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Оставил(а) всё, как есть</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 7 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">7. Какие скидки или акции вам наиболее интересны?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q7[]" value="first_order" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Скидки на первый заказ</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q7[]" value="free_shipping" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Бесплатная доставка</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q7[]" value="clearance" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Распродажи старых коллекций</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q7[]" value="gifts" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Подарки за покупки</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q7[]" value="loyalty" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Программа лояльности</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 8 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">8. Какую функцию поиска вы считаете самой полезной?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q8[]" value="name" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Поиск по названию</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q8[]" value="characteristics" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Поиск по характеристикам</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q8[]" value="categories" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Фильтрация по категориям</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q8[]" value="brands" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Поиск по брендам</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q8[]" value="recommendations" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Автоматические рекомендации</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 9 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">9. Какие методы оплаты для вас наиболее удобны?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q9[]" value="card" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Банковская карта</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q9[]" value="ewallet" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Электронные кошельки</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q9[]" value="cash" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Оплата при получении</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q9[]" value="mobile" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Apple Pay/Google Pay</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q9[]" value="installment" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Рассрочка</span>
                                </label>
                            </div>
                        </div>

                        <!-- Вопрос 10 -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold">10. Как бы вы описали наш сайт одним словом?</h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q10[]" value="convenient" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Удобный</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q10[]" value="beautiful" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Красивый</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q10[]" value="modern" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Современный</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q10[]" value="ordinary" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Обычный</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="q10[]" value="chaotic" class="form-checkbox h-5 w-5 text-[#28eab8]">
                                    <span>Хаотичный</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="bg-[#28eab8] text-[#061927] px-6 py-2 rounded-lg font-medium hover:bg-[#28eab8]/90 transition duration-300">
                                Отправить ответы
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'components/footer.php'; ?>

    <script>
        document.getElementById('surveyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                if (!data[key]) {
                    data[key] = [];
                }
                data[key].push(value);
            }
            
            fetch('save_survey.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Спасибо за ваши ответы! Мы учтем ваше мнение.');
                    window.location.href = 'account.php';
                } else {
                    alert('Произошла ошибка при сохранении ответов');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при отправке формы');
            });
        });
    </script>
</body>
</html> 