<template>
    <div>
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
        </ul>
        <div class="order-section">
            <h2 v-if="totalAmount">Оформить заказ</h2>
            <h2 v-else>Пусто</h2>

            <div v-if="totalAmount" class="order-summary">
                <h2>Итого: {{ formatPrice(totalAmount) }}</h2>
                <a href="/order/new/shipping-address" class="order-btn">Оформить заказ</a>
            </div>
        </div>
        <AlertMessage ref="alert" />
    </div>
</template>

<script>
import AlertMessage from './AlertMessage.vue';

export default {
    data() {
        return {
            cartItems: [],
            totalAmount: 0,
        };
    },
    components: { AlertMessage },
    methods: {
        fetchProducts() {
            fetch('/api/v1/cart/products/')
                .then(res => res.json())
                .then(data => {
                    this.cartItems = data.cartItems;
                    this.totalAmount = data.totalAmount;
                })
                .catch(err => console.error('Ошибка загрузки корзины:', err));
        },
        formatPrice(price) {
            // форматирование цены, например, с ₽
            return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(price);
        },
        increaseQuantity(productId) {
            fetch(`/api/v1/cart/products/${productId}/increase`, {
                method: 'POST',
            })
                .then(res => res.json())
                .then(res => {
                    this.showMessage(res);
                    this.fetchProducts();
                })
                .catch(err => console.error('Ошибка при увеличении:', err));
        },
        decreaseQuantity(productId) {
            fetch(`/api/v1/cart/products/${productId}/decrease`, {
                method: 'POST',
            })
                .then(res => res.json())
                .then(res => {
                    this.showMessage(res);
                    this.fetchProducts();
                })
                .catch(err => console.error('Ошибка при уменьшении:', err));
        },
        showMessage(res) {
            if (res.success){
                this.$refs.alert.showMessage(res.success, 'success');
            } else {
                this.$refs.alert.showMessage(res.error, 'error');
            }
        }
    },
    mounted() {
        this.fetchProducts();
    }
};
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