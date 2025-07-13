<template>
    <transition name="fade">
        <div v-if="visible" :class="alertClass" class="alert" role="alert">
            {{ message }}
        </div>
    </transition>
</template>

<script>
export default {
    data() {
        return {
            visible: false,
            message: '',
            alertClass: ''
        }
    },
    methods: {
        showMessage(msg, type = 'success', duration = 3000) {
            this.message = msg;
            this.alertClass = `alert-${type}`;
            this.visible = true;

            setTimeout(() => {
                this.visible = false;
            }, duration);
        }
    }
}
</script>

<style>
/* Переход затухания */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active в Vue <2.1.8 */ {
    opacity: 0;
}
</style>