import { ref, reactive, computed, watch } from 'vue'

export function usePlayer() {
  const lessons = ref([])
  const currentRecord = ref([])
  const isPlaying = ref(false)
  const pointer = ref(0)
  const playbackSpeed = ref(333) // 默认 3 倍速 (基于 1000ms)
  const chatLogs = ref([])
  
  // 预计算的状态快照
  const boardSnapshots = ref([])
  const chatSnapshots = ref([])

  // 当前显示的状态
  const boardState = reactive({
    stones: [],
    lastMove: null
  })

  // 计算属性
  const totalSteps = computed(() => currentRecord.value.length)

  // 解析坐标 hex -> {x, y}
  const parseCoord = (hex) => {
    if (!hex || hex.length !== 2) return null
    return {
      x: parseInt(hex.charAt(0), 16),
      y: parseInt(hex.charAt(1), 16)
    }
  }

  // 加载课程列表
  const fetchLessons = async () => {
    try {
      const res = await fetch('/trans.json')
      lessons.value = await res.json()
    } catch (e) {
      console.error('Failed to fetch lessons', e)
    }
  }

  // 加载具体课程数据并预计算
  const loadLesson = async (source) => {
    pause()
    try {
      const res = await fetch(`/json/${source}`)
      const data = await res.json()
      currentRecord.value = data
      
      // 预计算快照
      precomputeSnapshots(data)
      
      seekTo(0)
      play()
    } catch (e) {
      console.error('Failed to load lesson', e)
    }
  }

  const precomputeSnapshots = (record) => {
    const bSnapshots = []
    const cSnapshots = []
    let currentStones = []
    let currentLogs = []
    let currentStep = 1
    let currentColor = 'black'

    record.forEach((row, index) => {
      // 每一行动作后的状态
      if (row.action === 'RESET' || row.action === 'CLEAR') {
        currentStones = []
        currentStep = 1
        currentColor = 'black'
      } else if (row.action === 'MOVE') {
        const coord = parseCoord(row.content)
        if (coord) {
          currentStones.push({ ...coord, color: currentColor })
          currentColor = currentColor === 'black' ? 'white' : 'black'
          currentStep++
        }
      } else if (row.action === 'LOAD') {
        // LOAD 应该先清空当前棋盘，再根据 content 放置棋子
        currentStones = []
        currentStep = 1
        currentColor = 'black'
        
        const moves = row.content.match(/.{1,2}/g) || []
        moves.forEach(m => {
          const coord = parseCoord(m)
          if (coord) {
            currentStones.push({ ...coord, color: currentColor })
            currentColor = currentColor === 'black' ? 'white' : 'black'
            currentStep++
          }
        })
      } else if (row.action === 'BACK') {
        currentStones.pop()
        currentColor = currentColor === 'black' ? 'white' : 'black'
        currentStep--
      } else if (row.action === 'FIRST') {
        currentStones = currentStones.slice(0, 1)
        currentColor = 'white'
        currentStep = 2
      } else if (row.action === 'NEXT') {
        // NEXT usually refers to the next step in some context, 
        // but in the log it might be redundant if MOVE follows.
        // For snapshots, we just keep current state.
      } else if (row.action === 'GOTO') {
        const targetCount = parseInt(row.content)
        currentStones = currentStones.slice(0, targetCount)
        currentStep = targetCount + 1
        currentColor = currentStep % 2 === 1 ? 'black' : 'white'
      }

      // 聊天日志快照
      if (row.action === 'TALK' || row.action === 'MOVE' || row.action === 'EMOTE') {
        let content = row.content
        if (row.action === 'MOVE') {
            const coord = parseCoord(row.content)
            const showX = String.fromCharCode(coord.x + 96).toUpperCase()
            content = `落子: ${showX}${coord.y}`
        }
        currentLogs.push({
          user: row.user,
          content: content,
          type: row.action,
          time: row.time_int
        })
      }

      bSnapshots.push([...currentStones])
      cSnapshots.push([...currentLogs])
    })

    boardSnapshots.value = bSnapshots
    chatSnapshots.value = cSnapshots
  }

  const seekTo = (index) => {
    if (index < 0) index = 0
    if (index >= totalSteps.value) index = totalSteps.value - 1
    
    pointer.value = index
    boardState.stones = boardSnapshots.value[index] || []
    chatLogs.value = chatSnapshots.value[index] || []
  }

  let timer = null
  const play = () => {
    if (isPlaying.value) return
    isPlaying.value = true
    step()
  }

  const pause = () => {
    isPlaying.value = false
    if (timer) clearTimeout(timer)
  }

  const togglePlay = () => {
    isPlaying.value ? pause() : play()
  }

  const step = () => {
    if (!isPlaying.value) return
    if (pointer.value >= totalSteps.value - 1) {
      isPlaying.value = false
      return
    }

    const current = currentRecord.value[pointer.value]
    const next = currentRecord.value[pointer.value + 1]
    
    pointer.value++
    seekTo(pointer.value)

    const deltaTime = Math.min(next.time_int - current.time_int, 5) // 限制最大等待时间
    const timeout = 10 + deltaTime * playbackSpeed.value
    
    timer = setTimeout(step, timeout)
  }

  // 初始化加载
  fetchLessons()

  return {
    lessons,
    currentRecord,
    isPlaying,
    pointer,
    totalSteps,
    playbackSpeed,
    chatLogs,
    boardState,
    loadLesson,
    seekTo,
    play,
    pause,
    togglePlay
  }
}
