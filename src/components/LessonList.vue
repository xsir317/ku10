<script setup>
defineProps({
  lessons: {
    type: Array,
    default: () => []
  },
  activeIndex: {
    type: Number,
    default: -1
  }
})
const emit = defineEmits(['select'])
</script>

<template>
  <div class="overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400">
    <ul class="menu p-2 bg-base-100 w-full">
      <li v-for="(lesson, index) in lessons" :key="index">
        <a 
          @click="emit('select', lesson.data, index)" 
          class="flex justify-between items-center transition-colors duration-200"
          :class="activeIndex === index ? 'active bg-primary text-primary-content font-bold' : 'hover:bg-base-200'"
        >
          <span class="truncate pr-2">{{ lesson.show_name }}</span>
          <div class="flex items-center gap-1">
            <span v-if="activeIndex === index" class="badge badge-xs badge-ghost">播放中</span>
            <span v-if="lesson.origin" class="badge badge-sm badge-accent">原创</span>
          </div>
        </a>
      </li>
    </ul>
  </div>
</template>
