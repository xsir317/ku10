<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'

const props = defineProps({
  state: {
    type: Object,
    default: () => ({ stones: [], lastMove: null })
  },
  interactive: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['place-stone', 'undo'])

const canvasRef = ref(null)
const containerRef = ref(null)
let ctx = null

const BOARD_SIZE = 15
const CELL_SIZE = 37
const PADDING = 30 // 增加边距以容纳坐标

const drawBoard = () => {
  if (!ctx) return
  
  // 重置 transform 以便清空整个画布
  ctx.save()
  ctx.setTransform(1, 0, 0, 1, 0, 0)
  ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height)
  ctx.restore()

  // 绘制棋盘背景
  const totalSize = CELL_SIZE * BOARD_SIZE + PADDING * 2
  ctx.fillStyle = '#edd674'
  ctx.fillRect(0, 0, totalSize, totalSize)

  // 绘制坐标文字设置
  ctx.fillStyle = '#444'
  ctx.font = 'bold 14px Arial'
  ctx.textAlign = 'center'
  ctx.textBaseline = 'middle'

  // 绘制网格
  ctx.strokeStyle = '#000'
  ctx.lineWidth = 1
  ctx.beginPath()

  for (let i = 0; i < BOARD_SIZE; i++) {
    const offset = PADDING + CELL_SIZE / 2 + i * CELL_SIZE
    
    // 横线
    ctx.moveTo(PADDING + CELL_SIZE / 2, offset)
    ctx.lineTo(PADDING + CELL_SIZE / 2 + (BOARD_SIZE - 1) * CELL_SIZE, offset)
    
    // 纵线
    ctx.moveTo(offset, PADDING + CELL_SIZE / 2)
    ctx.lineTo(offset, PADDING + CELL_SIZE / 2 + (BOARD_SIZE - 1) * CELL_SIZE)

    // 绘制数字坐标 (右侧，从上到下 15-1)
    ctx.fillText(
      15 - i, 
      PADDING + CELL_SIZE / 2 + (BOARD_SIZE - 1) * CELL_SIZE + 20, 
      offset
    )

    // 绘制字母坐标 (下方，从左到右 A-O)
    const label = String.fromCharCode(65 + i) // A, B, C...
    ctx.fillText(
      label, 
      offset, 
      PADDING + CELL_SIZE / 2 + (BOARD_SIZE - 1) * CELL_SIZE + 20
    )
  }
  ctx.stroke()

  // 绘制星位 (4,4), (12,4), (4,12), (12,12), (8,8)
  const stars = [3, 7, 11]
  ctx.fillStyle = '#000'
  stars.forEach(r => {
    stars.forEach(c => {
      if ((r === 7 && c !== 7) || (c === 7 && r !== 7)) return // 仅中心和四个角
      if (r === 7 && c === 7) {
         // 中心
      } else if (![3, 11].includes(r) || ![3, 11].includes(c)) {
          return
      }
      
      ctx.beginPath()
      ctx.arc(
        PADDING + CELL_SIZE / 2 + c * CELL_SIZE,
        PADDING + CELL_SIZE / 2 + r * CELL_SIZE,
        3, 0, Math.PI * 2
      )
      ctx.fill()
    })
  })

  // 绘制棋子
  props.state.stones.forEach((stone, index) => {
    const x = PADDING + CELL_SIZE / 2 + (stone.x - 1) * CELL_SIZE
    const y = PADDING + CELL_SIZE / 2 + (BOARD_SIZE - stone.y) * CELL_SIZE
    
    // 阴影
    ctx.beginPath()
    ctx.arc(x + 2, y + 2, CELL_SIZE / 2 - 2, 0, Math.PI * 2)
    ctx.fillStyle = 'rgba(0,0,0,0.3)'
    ctx.fill()

    // 棋子主体
    const gradient = ctx.createRadialGradient(x - 5, y - 5, 2, x, y, CELL_SIZE / 2)
    if (stone.color === 'black') {
      gradient.addColorStop(0, '#666')
      gradient.addColorStop(1, '#000')
    } else {
      gradient.addColorStop(0, '#fff')
      gradient.addColorStop(1, '#ccc')
    }
    
    ctx.beginPath()
    ctx.arc(x, y, CELL_SIZE / 2 - 2, 0, Math.PI * 2)
    ctx.fillStyle = gradient
    ctx.fill()

    // 绘制序号
    ctx.fillStyle = stone.color === 'black' ? '#fff' : '#000'
    ctx.font = 'bold 14px Arial'
    ctx.textAlign = 'center'
    ctx.textBaseline = 'middle'
    ctx.fillText(index + 1, x, y)

    // 标记最后一手
    if (index === props.state.stones.length - 1) {
      ctx.strokeStyle = 'red'
      ctx.lineWidth = 2
      ctx.strokeRect(x - CELL_SIZE/2 + 5, y - CELL_SIZE/2 + 5, CELL_SIZE - 10, CELL_SIZE - 10)
    }
  })
}

const resizeCanvas = () => {
  if (!containerRef.value) return
  const size = Math.min(containerRef.value.clientWidth, containerRef.value.clientHeight) || 600
  canvasRef.value.width = size
  canvasRef.value.height = size
  // 根据实际大小缩放 context
  const scale = size / (CELL_SIZE * BOARD_SIZE + PADDING * 2)
  canvasRef.value.dataset.scale = scale // 存储缩放比例供点击逻辑使用
  ctx.setTransform(scale, 0, 0, scale, 0, 0)
  drawBoard()
}

const handleCanvasClick = (event) => {
  if (!props.interactive) return
  
  const rect = canvasRef.value.getBoundingClientRect()
  const scale = parseFloat(canvasRef.value.dataset.scale) || 1
  const x = (event.clientX - rect.left) / scale
  const y = (event.clientY - rect.top) / scale
  
  // 反查棋盘坐标
  const boardX = Math.round((x - PADDING - CELL_SIZE / 2) / CELL_SIZE) + 1
  const boardY = BOARD_SIZE - Math.round((y - PADDING - CELL_SIZE / 2) / CELL_SIZE)
  
  if (boardX >= 1 && boardX <= 15 && boardY >= 1 && boardY <= 15) {
    emit('place-stone', { x: boardX, y: boardY })
  }
}

const handleContextMenu = (event) => {
  if (!props.interactive) return
  event.preventDefault()
  emit('undo')
}

onMounted(() => {
  ctx = canvasRef.value.getContext('2d')
  window.addEventListener('resize', resizeCanvas)
  resizeCanvas()
})

onUnmounted(() => {
  window.removeEventListener('resize', resizeCanvas)
})

watch(() => props.state, drawBoard, { deep: true })
</script>

<template>
  <div ref="containerRef" class="w-full aspect-square flex items-center justify-center p-4">
    <canvas 
      ref="canvasRef" 
      class="shadow-2xl rounded-sm"
      :class="{ 'cursor-pointer': interactive }"
      @click="handleCanvasClick"
      @contextmenu="handleContextMenu"
    ></canvas>
  </div>
</template>

<style scoped>
canvas {
  image-rendering: auto;
}
</style>
