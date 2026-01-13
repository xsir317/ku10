<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
  logs: {
    type: Array,
    default: () => []
  }
})

const listRef = ref(null)

// 自动滚动到底部
watch(() => props.logs.length, async () => {
  await nextTick()
  if (listRef.value) {
    listRef.value.scrollTop = listRef.value.scrollHeight
  }
})
</script>

<template>
  <div ref="listRef" class="overflow-y-auto h-full p-4 bg-gray-50 flex flex-col gap-2 scroll-smooth">
    <div v-for="(log, index) in logs" :key="index" class="chat" :class="log.user === 'RenjuTeacher' || log.user === 'GrandMaster' ? 'chat-start' : 'chat-start'">
      <div class="chat-header opacity-50 text-xs mb-1">
        {{ log.user }}
      </div>
      <div class="chat-bubble shadow-sm text-sm min-h-0 py-2 px-3" 
           :class="{
             'chat-bubble-primary': log.user === 'RenjuTeacher' || log.user === 'GrandMaster',
             'chat-bubble-info': log.type === 'MOVE',
             'bg-white text-gray-800 border border-gray-200': log.type === 'TALK' && log.user !== 'RenjuTeacher'
           }">
        <span v-html="log.content"></span>
      </div>
    </div>
    <div v-if="logs.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 gap-2">
      <div class="loading loading-dots loading-lg"></div>
      <p>等待课程加载...</p>
    </div>
  </div>
</template>

<style scoped>
.chat-bubble {
  max-width: 90%;
  word-break: break-word;
}
</style>
