<script setup>
defineProps({
  lessons: {
    type: Array,
    default: () => []
  }
})
const emit = defineEmits(['select'])
</script>

<template>
  <div class="overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400">
    <ul class="menu p-2 bg-base-100 w-full">
      <li v-for="(lesson, index) in lessons" :key="index">
        <a @click="emit('select', lesson.data, index)" class="flex justify-between items-center hover:bg-primary hover:text-primary-content transition-colors duration-200">
          <span class="truncate pr-2">{{ lesson.show_name }}</span>
          <span v-if="lesson.origin" class="badge badge-sm badge-accent">原创</span>
        </a>
      </li>
    </ul>
  </div>
</template>
