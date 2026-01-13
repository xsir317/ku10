<script setup>
import { computed } from 'vue'

const props = defineProps({
  isPlaying: Boolean,
  pointer: Number,
  totalSteps: Number,
  speed: Number
})

const emit = defineEmits(['toggle', 'seek', 'update:speed', 'analyze'])

const progress = computed({
  get: () => (props.totalSteps > 0 ? (props.pointer / (props.totalSteps - 1)) * 100 : 0),
  set: (val) => {
    const index = Math.floor((val / 100) * (props.totalSteps - 1))
    emit('seek', index)
  }
})

const speedOptions = [
  { label: '0.75x', value: 1333 },
  { label: '1x', value: 999 },
  { label: '1.5x', value: 667 },
  { label: '2x', value: 500 },
  { label: '3x', value: 333 },
  { label: '10x', value: 100 },
  { label: '100x', value: 10 }
]
</script>

<template>
  <div class="flex flex-col gap-4">
    <!-- Progress Bar -->
    <div class="flex items-center gap-4">
      <span class="text-xs font-mono w-12">{{ pointer + 1 }}/{{ totalSteps }}</span>
      <input 
        type="range" 
        min="0" 
        max="100" 
        v-model="progress" 
        class="range range-primary range-sm flex-1"
        :disabled="totalSteps === 0"
      />
      <span class="text-xs font-mono w-8">{{ Math.round(progress) }}%</span>
    </div>

    <!-- Controls -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button class="btn btn-circle btn-primary" @click="emit('toggle')" :disabled="totalSteps === 0">
          <span v-if="!isPlaying">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
            </svg>
          </span>
          <span v-else>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
        </button>
        
        <div class="divider divider-horizontal"></div>
        
        <div class="flex gap-1">
          <button class="btn btn-sm btn-outline" @click="emit('seek', pointer - 1)" :disabled="pointer <= 0">
            上一动
          </button>
          <button class="btn btn-sm btn-outline" @click="emit('seek', pointer + 1)" :disabled="pointer >= totalSteps - 1">
            下一动
          </button>
        </div>
        
        <div class="divider divider-horizontal"></div>

        <button class="btn btn-sm btn-accent" @click="emit('analyze')" :disabled="totalSteps === 0">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
          </svg>
          研究局面
        </button>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-xs text-gray-500">播放速度:</span>
        <select 
          class="select select-bordered select-sm" 
          :value="speed" 
          @change="e => emit('update:speed', parseInt(e.target.value))"
        >
          <option v-for="opt in speedOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>
