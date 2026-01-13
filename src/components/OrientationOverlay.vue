<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isPortrait = ref(false)

const checkOrientation = () => {
  isPortrait.value = window.innerHeight > window.innerWidth && window.innerWidth < 768
}

onMounted(() => {
  checkOrientation()
  window.addEventListener('resize', checkOrientation)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkOrientation)
})
</script>

<template>
  <div v-if="isPortrait" class="fixed inset-0 z-[100] bg-neutral text-neutral-content flex flex-col items-center justify-center p-8 text-center animate-fade-in">
    <div class="mb-6 animate-bounce">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 3L13 3a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2V5a2 2 0 012-2h2z" class="origin-center rotate-90" />
      </svg>
    </div>
    <h2 class="text-2xl font-bold mb-2">请旋转您的设备</h2>
    <p class="text-base-content/70">为了获得最佳的棋盘观看体验，请将手机横屏使用。</p>
    
    <div class="mt-10 badge badge-outline badge-lg">仅支持横屏模式</div>
  </div>
</template>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}
</style>
