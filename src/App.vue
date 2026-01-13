<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import GoBoard from './components/GoBoard.vue'
import LessonList from './components/LessonList.vue'
import ChatList from './components/ChatList.vue'
import PlayerControls from './components/PlayerControls.vue'
import AnalysisModal from './components/AnalysisModal.vue'
import OrientationOverlay from './components/OrientationOverlay.vue'
import { usePlayer } from './composables/usePlayer'

const { 
  currentRecord, 
  loadLesson, 
  lessons, 
  currentIndex,
  isPlaying, 
  pause,
  togglePlay, 
  pointer, 
  totalSteps,
  seekTo,
  playbackSpeed,
  chatLogs,
  boardState
} = usePlayer()

const showAnalysis = ref(false)
const showControls = ref(true)
let hideTimer = null

const openAnalysis = () => {
  pause()
  showAnalysis.value = true
}

const resetHideTimer = () => {
  showControls.value = true
  if (hideTimer) clearTimeout(hideTimer)
  hideTimer = setTimeout(() => {
    showControls.value = false
  }, 3000)
}

onMounted(() => {
  resetHideTimer()
  // 监听点击和触摸唤醒控制栏
  window.addEventListener('mousedown', resetHideTimer)
  window.addEventListener('touchstart', resetHideTimer)
})

onUnmounted(() => {
  window.removeEventListener('mousedown', resetHideTimer)
  window.removeEventListener('touchstart', resetHideTimer)
})
</script>

<template>
  <div class="drawer lg:drawer-open">
    <input id="lesson-drawer" type="checkbox" class="drawer-toggle" />
    
    <div class="drawer-content flex flex-col min-h-screen bg-base-200">
      <!-- Header -->
      <header class="bg-primary text-primary-content shadow-lg px-2 z-20 flex items-center h-10 min-h-[2.5rem]">
        <div class="flex-none lg:hidden">
          <label for="lesson-drawer" class="btn btn-xs btn-ghost gap-1 normal-case px-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            <span class="text-xs">课程列表</span>
          </label>
        </div>
        <div class="flex-1 flex justify-center lg:justify-start">
          <a class="btn btn-ghost btn-xs lg:btn-sm text-sm lg:text-base normal-case px-2">RenjuClass 现代连珠教室</a>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 container mx-auto p-1 lg:p-4 grid grid-cols-1 sm:grid-cols-12 gap-1 lg:gap-6 overflow-hidden relative">
        <!-- Left Column: Board -->
        <div class="sm:col-span-7 lg:col-span-8 flex flex-col relative h-[calc(100svh-2.5rem)] lg:h-auto">
          <div class="card bg-base-100 shadow-xl overflow-hidden flex-1">
            <div class="card-body p-0 items-center bg-neutral justify-center overflow-hidden">
              <GoBoard :state="boardState" />
            </div>
          </div>
          
          <!-- Floating Controls -->
          <Transition name="slide-up">
            <div v-show="showControls" class="fixed sm:absolute bottom-2 left-2 right-2 sm:bottom-4 sm:left-4 sm:right-4 z-30 pointer-events-none">
              <div class="card bg-base-100/80 backdrop-blur shadow-2xl pointer-events-auto max-w-2xl mx-auto border border-base-content/10">
                <div class="card-body p-2 lg:p-4">
                  <PlayerControls 
                    :is-playing="isPlaying"
                    :pointer="pointer"
                    :total-steps="totalSteps"
                    :speed="playbackSpeed"
                    @toggle="togglePlay"
                    @seek="seekTo"
                    @update:speed="s => playbackSpeed = s"
                    @analyze="openAnalysis"
                  />
                </div>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Right Column: Chat -->
        <div class="sm:col-span-5 lg:col-span-4 flex flex-col h-[calc(100svh-2.5rem)] lg:h-[calc(100svh-10rem)]">
          <div class="flex-1 card bg-base-100 shadow-xl overflow-hidden border border-base-200">
            <div class="card-header bg-base-300 p-2 font-bold flex justify-between items-center text-sm">
              <span>课堂对话</span>
            </div>
            <div class="card-body p-0 overflow-hidden">
              <ChatList :logs="chatLogs" />
            </div>
          </div>
        </div>
      </main>

      <!-- Footer (Desktop only) -->
      <footer class="hidden lg:flex footer footer-center p-4 bg-base-300 text-base-content">
        <div>
          <p>Copyright © 2026 - 重构自 xsir317@gmail.com 的 Ku10 项目</p>
        </div>
      </footer>
    </div>

    <!-- Sidebar / Drawer -->
    <div class="drawer-side z-40">
      <label for="lesson-drawer" class="drawer-overlay"></label>
      <div class="w-80 min-h-full bg-base-100 flex flex-col">
        <div class="p-4 bg-base-300 font-bold text-lg border-b border-base-200">课程列表</div>
        <div class="flex-1 overflow-hidden">
          <LessonList 
            :lessons="lessons" 
            :active-index="currentIndex"
            @select="(source, index) => { loadLesson(source, index); }" 
          />
        </div>
      </div>
    </div>

    <!-- Overlays -->
    <OrientationOverlay />
    <AnalysisModal 
      :show="showAnalysis" 
      :initial-stones="boardState.stones"
      @close="showAnalysis = false"
    />
  </div>
</template>

<style>
/* 可以在这里添加全局动画 */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>
