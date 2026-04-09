# 🛍️ Symfony Интернет-магазин

Интернет-магазин, разработанный с использованием Symfony. Реализована базовая e-commerce функциональность: каталог товаров, корзина, оформление заказов, пользовательская регистрация и административная панель для управления контентом.

---

## ⚙️ Стек технологий

- [Symfony 7.2](https://symfony.com/)
- Doctrine ORM
- Twig (шаблонизатор)
- MySQL
- PHPUnit + Foundry (для генерации фикстур и тестирования)
- Docker
- Vue

---

## 📦 Функциональные возможности

- Регистрация с подтверждением email и вход пользователей через google и vk
- Каталог товаров с пагинацией и фильтрацией
- Страница товара
- Корзина с возможностью редактирования количества
- Оформление заказа
- Уведомления пользователя через mercure
- Определение и выбор местоположения
- Админ-панель для управления товарами и заказами

---

## 🚀 Установка

Выполнить команды:

   ```bash
   git clone https://github.com/doctor2/onlineStore.git
   
   cd onlineStore
   
   docker-compose up -d
   
   docker exec -it php bash
   
   composer install
   
   php bin/console doctrine:database:create
   
   php bin/console doctrine:migrations:migrate
   
   php bin/console doctrine:fixtures:load
   
   npm install
   
   npm run dev
   
   ```

## 🧪 Tests

   ```bash
   php bin/console doctrine:database:create --env=test
   
   ./vendor/bin/phpunit --testsuite=acceptance
   ```
