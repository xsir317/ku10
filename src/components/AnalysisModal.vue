<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import GoBoard from './GoBoard.vue'

const props = defineProps({
  show: Boolean,
  initialStones: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close'])

const analysisStones = ref([])
const currentColor = ref('black')

// 当模态框打开时，初始化棋盘
watch(() => props.show, (newVal) => {
  if (newVal) {
    analysisStones.value = [...props.initialStones]
    // 根据当前棋子数量决定颜色
    currentColor.value = analysisStones.value.length % 2 === 0 ? 'black' : 'white'
  }
})

const handlePlaceStone = (coord) => {
  // 检查是否已经有棋子
  const exists = analysisStones.value.some(s => s.x === coord.x && s.y === coord.y)
  if (exists) return

  analysisStones.value.push({
    ...coord,
    color: currentColor.value
  })
  
  // 切换颜色
  currentColor.value = currentColor.value === 'black' ? 'white' : 'black'
}

const handleUndo = () => {
  if (analysisStones.value.length > 0) {
    analysisStones.value.pop()
    currentColor.value = currentColor.value === 'black' ? 'white' : 'black'
  }
}

const reset = () => {
  analysisStones.value = [...props.initialStones]
  currentColor.value = analysisStones.value.length % 2 === 0 ? 'black' : 'white'
}

const clearAll = () => {
  analysisStones.value = []
  currentColor.value = 'black'
}
</script>

<template>
  <div class="modal" :class="{ 'modal-open': show }">
    <div class="modal-box max-w-4xl bg-base-100 p-0 overflow-hidden flex flex-col lg:flex-row h-[90vh]">
      <!-- Left: Board -->
      <div class="flex-1 bg-neutral flex items-center justify-center p-4">
        <GoBoard 
          :state="{ stones: analysisStones }" 
          :interactive="true"
          @place-stone="handlePlaceStone"
          @undo="handleUndo"
        />
      </div>

      <!-- Right: Controls -->
      <div class="w-full lg:w-64 p-6 flex flex-col gap-6 bg-base-100 border-l border-base-200">
        <div class="flex flex-col gap-2">
          <h3 class="font-bold text-lg">研究模式</h3>
          <p class="text-xs text-base-content/60">在此您可以自由推演局面，右键点击棋盘可快速悔棋。</p>
        </div>

        <div class="stats stats-vertical shadow bg-base-200">
          <div class="stat p-4">
            <div class="stat-title text-xs">当前手数</div>
            <div class="stat-value text-2xl">{{ analysisStones.length }}</div>
          </div>
          <div class="stat p-4">
            <div class="stat-title text-xs">下一手</div>
            <div class="stat-value text-lg flex items-center gap-2">
              <div class="w-4 h-4 rounded-full border border-base-content/20 shadow-sm"
                   :class="currentColor === 'black' ? 'bg-black' : 'bg-white'"></div>
              {{ currentColor === 'black' ? '黑方' : '白方' }}
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-2 mt-auto">
          <button class="btn btn-outline btn-sm" @click="handleUndo">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
            悔棋
          </button>
          <button class="btn btn-outline btn-sm" @click="reset">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            恢复初始
          </button>
          <button class="btn btn-outline btn-sm btn-error" @click="clearAll">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            清空棋盘
          </button>
          <div class="divider"></div>
          <button class="btn btn-primary" @click="emit('close')">退出研究</button>
        </div>
      </div>
    </div>
    <div class="modal-backdrop" @click="emit('close')"></div>
  </div>
</template>

<style scoped>
.modal-box {
  max-height: none;
}
</style>
