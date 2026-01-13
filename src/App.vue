<script setup>
import { ref, onMounted } from 'vue'
import GoBoard from './components/GoBoard.vue'
import LessonList from './components/LessonList.vue'
import ChatList from './components/ChatList.vue'
import PlayerControls from './components/PlayerControls.vue'
import AnalysisModal from './components/AnalysisModal.vue'
import { usePlayer } from './composables/usePlayer'

const { 
  currentRecord, 
  loadLesson, 
  lessons, 
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

const openAnalysis = () => {
  pause()
  showAnalysis.value = true
}
</script>

<template>
  <div class="min-h-screen bg-base-200 flex flex-col">
    <!-- Header -->
    <header class="navbar bg-primary text-primary-content shadow-lg px-4">
      <div class="flex-1">
        <a class="btn btn-ghost text-xl normal-case">RenjuClass 现代连珠教室</a>
      </div>
      <div class="flex-none gap-2">
        <div class="badge badge-outline">Vue 3 + Canvas 版</div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto p-4 grid grid-cols-1 lg:grid-cols-12 gap-6 overflow-hidden">
      <!-- Left Column: Board and Controls -->
      <div class="lg:col-span-7 flex flex-col gap-4">
        <div class="card bg-base-100 shadow-xl overflow-hidden">
          <div class="card-body p-0 items-center bg-neutral">
            <GoBoard :state="boardState" />
          </div>
        </div>
        
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body p-4">
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

      <!-- Right Column: Chat and Lessons -->
      <div class="lg:col-span-5 flex flex-col gap-6 h-[calc(100vh-10rem)]">
        <!-- Chat Area -->
        <div class="flex-1 card bg-base-100 shadow-xl overflow-hidden">
          <div class="card-header bg-base-300 p-2 font-bold flex justify-between items-center">
            <span>课堂对话</span>
          </div>
          <div class="card-body p-0 overflow-hidden">
            <ChatList :logs="chatLogs" />
          </div>
        </div>

        <!-- Lesson List -->
        <div class="h-1/3 card bg-base-100 shadow-xl overflow-hidden">
          <div class="card-header bg-base-300 p-2 font-bold">课程列表</div>
          <div class="card-body p-0 overflow-hidden">
            <LessonList :lessons="lessons" @select="(source, index) => loadLesson(source, index)" />
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer footer-center p-4 bg-base-300 text-base-content">
      <div>
        <p>Copyright © 2026 - 重构自 xsir317@gmail.com 的 Ku10 项目</p>
      </div>
    </footer>

    <!-- Analysis Modal -->
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
</style>
