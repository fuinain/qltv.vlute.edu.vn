<template>
    <div v-if="visible" class="overlay">
        <div class="loader"></div>
    </div>
</template>

<script>
export default {
    name: "LTELoader",
    props: {
        visible: {
            type: Boolean,
            default: true,
        },
    },
};
</script>

<style scoped>
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5); /* Nền tối mờ */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.loader {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 3px solid;
    border-color: #0d6efd #0d6efd transparent transparent;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
    position: relative;
}

.loader::after,
.loader::before {
    content: '';
    position: absolute;
    inset: 0;
    margin: auto;
    border: 3px solid;
    border-radius: 50%;
}

.loader::after {
    width: 40px;
    height: 40px;
    border-color: transparent transparent #FF3D00 #FF3D00;
    animation: rotationBack 0.5s linear infinite;
}

.loader::before {
    width: 32px;
    height: 32px;
    border-color: #198754 #198754 transparent transparent;
    animation: rotation 1.5s linear infinite;
}

@keyframes rotation {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes rotationBack {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(-360deg); }
}
</style>
