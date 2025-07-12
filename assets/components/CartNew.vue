<template>
    <ul class="cart-items">
        <li v-for="item in cartItems" :key="item.product.id">
            <div class="item-details">
                <span class="item-name">{{ item.product.name }}</span>
                <div class="item-controls">
                    <div class="item-price">{{ formatPrice(item.price) }}</div>
                    <div class="qty-controls">
                        <button
                            class="qty-btn decrease"
                            @click="decreaseQuantity(item.product.id)"
                            :disabled="item.quantity === 0"
                        >−</button>
                        <span class="quantity" :id="'quantity-' + item.product.id">{{ item.quantity }}</span>
                        <button
                            class="qty-btn increase"
                            @click="increaseQuantity(item.product.id)"
                        >+</button>
                    </div>
                </div>
            </div>
        </li>
        <li v-if="cartItems.length === 0">Корзина пуста</li>
    </ul>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// Состояние корзины
const cartItems = ref([])

// Форматирование цены, например, в рублях
function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(price)
}

// Загрузка корзины
async function loadCart() {
    try {
        const res = await fetch('/api/v1/cart/products/')
        if (!res.ok) throw new Error('Ошибка загрузки корзины')
        const data = await res.json()
        cartItems.value = data
    } catch (e) {
        console.error(e)
    }
}

// Увеличить количество
async function increaseQuantity(id) {
    try {
        const res = await fetch(`/api/v1/cart/products/${id}/increase`, { method: 'POST' })
        if (!res.ok) throw new Error('Ошибка увеличения количества')
        // Перезагрузить корзину
        await loadCart()
    } catch (e) {
        console.error(e)
    }
}

// Уменьшить количество (с удалением если 0)
async function decreaseQuantity(id) {
    try {
        const res = await fetch(`/api/v1/cart/products/${id}/decrease`, { method: 'POST' })
        if (!res.ok) throw new Error('Ошибка уменьшения количества')
        // Перезагрузить корзину
        await loadCart()
    } catch (e) {
        console.error(e)
    }
}

// Загрузка при монтировании компонента
onMounted(() => {
    loadCart()
})
</script>

<style scoped>
.cart-items {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
}

/* Стиль для каждого товара */
.cart-items li {
    background-color: #fff;
    padding: 10px 15px;
    margin-bottom: 8px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Контейнер деталей товара */
.item-details {
    display: flex;
    align-items: center;
    width: 100%;
    gap: 15px;
}
.item-controls{
    width: auto;
    display: flex;
    align-items: center;
    gap: 25px;
}
/* Название товара */
.item-name {
    flex: 2;
    font-weight: 600;
}

/* Контроль количества */
.qty-controls {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Кнопки увеличения/уменьшения */
.qty-btn {
    background-color: #3498db;
    color: #fff;
    border: none;
    border-radius: 3px;
    width: 25px;
    height: 25px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.qty-btn:hover {
    background-color: #2980b9;
}

/* Отображение количества */
.quantity {
    min-width: 20px;
    text-align: center;
    font-weight: 600;
}

/* Цена за товар */
.item-price {
    font-weight: 600;
    color: #2c3e50;
    flex: 1;
    text-align: right;
}
</style>
