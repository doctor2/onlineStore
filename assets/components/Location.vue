<template>
    <div>
        <!-- Выбранный город на странице -->
        <div class="current-city" @click.stop="openPopup">
            {{ city ? city : '' }}
        </div>

        <!-- Попап -->
        <div v-if="show" class="city-popup">
            <div class="popup-content" ref="popup" @click.stop>
                <div v-if="!editing">
                    <p v-if="city && region">
                        Мы определили ваш город как:<br>
                        <strong>{{ city }}</strong> ({{ region }})
                    </p>

                    <p v-else>
                        Не удалось определить ваш город.<br>
                        Пожалуйста, выберите его вручную.
                    </p>

                    <div class="buttons">
                        <button class="button-link" v-if="city" @click="confirm">Подтвердить</button>
                        <button class="button-link" @click="toggleEdit">Изменить</button>
                    </div>
                </div>

                <!-- Выбор города -->
                <div v-if="editing" class="city-select">
                    <input
                        class="form-input"
                        type="text"
                        v-model="search"
                        placeholder="Поиск города..."
                    />

                    <ul class="city-list" @scroll="onScroll">
                        <li
                            v-for="c in cities"
                            :key="c.id"
                            @click="selectCity(c)"
                        >
                            {{ c.name }} ({{ c.region }})
                        </li>

                        <li v-if="loading">Загрузка...</li>
                        <li v-if="!loading && cities.length === 0">Ничего не найдено</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

const show = ref(false)
const editing = ref(false)

const city = ref(null)
const region = ref(null)
const cities = ref([])

const popup = ref(null)

async function loadInitialCity() {
    const res = await fetch('/api/v1/get-city')
    const data = await res.json()

    if (data.city) {
        city.value = data.city
        region.value = data.region
        return
    }

    const ipRes = await fetch('/api/v1/search-city')
    const ipData = await ipRes.json()

    if (ipData.city) {
        city.value = ipData.city
        region.value = ipData.region
    }

    show.value = true
}

onMounted(async () => {
    await loadInitialCity()

    if (!city.value) {
        show.value = true
    }

    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})

function handleClickOutside(event) {
    if (show.value && popup.value && !popup.value.contains(event.target)) {
        show.value = false
    }
}

function openPopup() {
    show.value = true
}

const page = ref(1)
const hasMore = ref(true)
const loading = ref(false)
const search = ref('')

async function loadCities(reset = false) {
    if (loading.value) return

    if (reset) {
        page.value = 1
        cities.value = []
        hasMore.value = true
    }

    if (!hasMore.value) return

    loading.value = true

    const res = await fetch(`/api/v1/get-cities?page=${page.value}&limit=30&search=${encodeURIComponent(search.value)}`)
    const data = await res.json()

    if (data.length < 30) {
        hasMore.value = false
    }

    cities.value.push(...data)
    page.value++
    loading.value = false
}

function onScroll(event) {
    const el = event.target

    const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 20

    if (nearBottom) {
        loadCities()
    }
}

watch(search, () => {
    loadCities(true)
})

async function toggleEdit() {
    editing.value = !editing.value

    if (editing.value) {
        await loadCities(true)
    }
}

async function confirm() {
    await saveCity(city.value, region.value)
    show.value = false
}

async function selectCity(c) {
    city.value = c.name
    region.value = c.region
    editing.value = false

    await saveCity(c.name, c.region)
    show.value = false
}

async function saveCity(cityName, regionName) {
    await fetch('/api/v1/set-city', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            city: cityName,
            region: regionName
        })
    })
}
</script>

<style scoped>
.current-city {
    cursor: pointer;
    font-weight: bold;
    padding: 5px 10px;
    display: inline-block;
}

.city-popup {
    position: fixed;
    top: 50px;
    left: 140px;
    z-index: 9999;
}

.popup-content {
    background: white;
    padding: 20px;
    width: 300px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

@keyframes slideIn {
    from { transform: translateX(50px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.city-list {
    max-height: 200px;
    overflow-y: auto;
    padding: 0;
    margin: 10px 0 0;
    list-style: none;
}

.city-list li {
    padding: 8px;
    cursor: pointer;
}

.city-list li:hover {
    background: #f0f0f0;
}

.buttons {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

button {
    font-size: 14px;
    text-decoration: none;
    color: #2c3e50;
}
</style>
