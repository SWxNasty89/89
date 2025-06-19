<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - UrbanAttire</title>
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
    
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="bg-[#182b39] rounded-xl shadow-sm p-8 max-w-4xl mx-auto text-white">
            <h1 class="text-3xl font-bold text-white mb-6">О нашем магазине</h1>
            
            <div class="mb-8">
                <img src="/public/images/123.jpg" alt="UrbanAttire" class="w-full h-[400px] object-cover rounded-xl shadow-sm mb-6">
                
                <h2 class="text-2xl font-semibold text-[#28eab8] mb-4">Качество, стиль и надежность</h2>
                <p class="text-gray-300 mb-4 leading-relaxed">
                    UrbanAttire - ваш надежный магазин мобильных аксессуаров премиум-класса. Мы специализируемся на продаже высококачественных чехлов, защитных стекол, зарядных устройств и других аксессуаров для смартфонов и планшетов.
                </p>
                <p class="text-gray-300 mb-4 leading-relaxed">
                    Основанная в 2015 году, наша компания стремится предоставлять только лучшие продукты по конкурентоспособным ценам, обеспечивая при этом превосходное обслуживание клиентов.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div class="bg-[#061927] p-6 rounded-xl shadow-sm border border-[#3b5467] group hover:shadow-[0_10px_30px_rgba(40,234,184,0.15)] transition-all duration-300">
                    <div class="text-[#28eab8] text-4xl mb-3 group-hover:scale-110 transition-transform duration-300"><i class="fas fa-medal"></i></div>
                    <h3 class="text-xl font-semibold text-white mb-2 group-hover:text-[#28eab8] transition-colors duration-300">Качество</h3>
                    <p class="text-gray-300">Мы тщательно отбираем каждый товар, чтобы обеспечить наивысшее качество для наших клиентов.</p>
                </div>
                
                <div class="bg-[#061927] p-6 rounded-xl shadow-sm border border-[#3b5467] group hover:shadow-[0_10px_30px_rgba(40,234,184,0.15)] transition-all duration-300">
                    <div class="text-[#28eab8] text-4xl mb-3 group-hover:scale-110 transition-transform duration-300"><i class="fas fa-truck-fast"></i></div>
                    <h3 class="text-xl font-semibold text-white mb-2 group-hover:text-[#28eab8] transition-colors duration-300">Доставка</h3>
                    <p class="text-gray-300">Быстрая доставка по всей России. Отправляем заказы в течение 24 часов после оплаты.</p>
                </div>
                
                <div class="bg-[#061927] p-6 rounded-xl shadow-sm border border-[#3b5467] group hover:shadow-[0_10px_30px_rgba(40,234,184,0.15)] transition-all duration-300">
                    <div class="text-[#28eab8] text-4xl mb-3 group-hover:scale-110 transition-transform duration-300"><i class="fas fa-headset"></i></div>
                    <h3 class="text-xl font-semibold text-white mb-2 group-hover:text-[#28eab8] transition-colors duration-300">Поддержка</h3>
                    <p class="text-gray-300">Наша служба поддержки всегда готова помочь вам с любыми вопросами.</p>
                </div>
            </div>
            
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-[#28eab8] mb-4">Наша миссия</h2>
                <p class="text-gray-300 mb-4 leading-relaxed">
                    Наша миссия заключается в том, чтобы предоставлять современные, стильные и функциональные аксессуары, которые идеально дополнят ваши мобильные устройства, обеспечивая при этом надежную защиту и длительный срок службы.
                </p>
                <p class="text-gray-300 leading-relaxed">
                    Мы стремимся к постоянному совершенствованию нашего ассортимента, добавляя новые продукты, соответствующие последним технологическим тенденциям и потребностям наших клиентов.
                </p>
            </div>
            
            <div class="bg-[#061927] p-6 rounded-xl shadow-sm border border-[#3b5467] hover:shadow-[0_10px_30px_rgba(40,234,184,0.15)] transition-all duration-300">
                <h2 class="text-2xl font-semibold text-[#28eab8] mb-4">Свяжитесь с нами</h2>
                <div class="flex items-center mb-3 hover:translate-x-1 transition-transform duration-300">
                    <i class="fas fa-map-marker-alt text-[#28eab8] mr-3"></i>
                    <span class="text-gray-300">ул. Примерная, 123, Москва</span>
                </div>
                <div class="flex items-center mb-3 hover:translate-x-1 transition-transform duration-300">
                    <i class="fas fa-phone text-[#28eab8] mr-3"></i>
                    <span class="text-gray-300">+7 (999) 123-45-67</span>
                </div>
                <div class="flex items-center hover:translate-x-1 transition-transform duration-300">
                    <i class="fas fa-envelope text-[#28eab8] mr-3"></i>
                    <span class="text-gray-300">info@urbanattire.ru</span>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>
</body>
</html>
