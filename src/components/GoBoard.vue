<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'

const props = defineProps({
  state: {
    type: Object,
    default: () => ({ stones: [], lastMove: null })
  }
})

const canvasRef = ref(null)
const containerRef = ref(null)
let ctx = null

const BOARD_SIZE = 15
const CELL_SIZE = 37
const PADDING = 20

const drawBoard = () => {
  if (!ctx) return
  const width = canvasRef.value.width
  const height = canvasRef.value.height

  // 清放棋盘
  ctx.fillStyle = '#edd674'
  ctx.fillRect(0, 0, width, height)

  // 绘制网格
  ctx.strokeStyle = '#000'
  ctx.lineWidth = 1
  ctx.beginPath()

  for (let i = 0; i < BOARD_SIZE; i++) {
    // 横线
    ctx.moveTo(PADDING + CELL_SIZE / 2, PADDING + CELL_SIZE / 2 + i * CELL_SIZE)
    ctx.lineTo(PADDING + CELL_SIZE / 2 + (BOARD_SIZE - 1) * CELL_SIZE, PADDING + CELL_SIZE / 2 + i * CELL_SIZE)
    // 纵线
    ctx.moveTo(PADDING + CELL_SIZE / 2 + i * CELL_SIZE, PADDING + CELL_SIZE / 2)
    ctx.lineTo(PADDING + CELL_SIZE / 2 + i * CELL_SIZE, PADDING + CELL_SIZE / 2 + (BOARD_SIZE - 1) * CELL_SIZE)
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
  ctx.setTransform(scale, 0, 0, scale, 0, 0)
  drawBoard()
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
    <canvas ref="canvasRef" class="shadow-2xl rounded-sm"></canvas>
  </div>
</template>

<style scoped>
canvas {
  image-rendering: auto;
}
</style>
