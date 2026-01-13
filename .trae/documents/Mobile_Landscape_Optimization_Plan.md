# 移动端横屏沉浸式优化计划

## 1. 屏幕方向处理
- **竖屏遮罩**: 增加一个仅在竖屏 (`orientation: portrait`) 且是移动端时显示的覆盖层，引导用户横屏。
- **视口适配**: 使用 `dvh` (Dynamic Viewport Height) 确保布局在移动端浏览器工具栏弹出时不会溢出。

## 2. 沉浸式控制交互
- **自动隐藏逻辑**:
    - 在 `App.vue` 维护 `controlsVisible` 状态。
    - 监听全局点击事件，点击时设为 `true` 并启动 3 秒定时器。
    - 3 秒无操作后，控制栏通过 `translate-y-full` 和 `opacity-0` 动效隐藏。
- **控制栏改造**: 将 `PlayerControls.vue` 设为底部浮动层，增加半透明磨砂背景 (Glassmorphism)。

## 3. 课程列表优化
- **抽屉模式**: 在移动端将课程列表改为侧边抽屉 (`daisyUI drawer`)。
- **触发按钮**: 在 Header 增加一个“课程”图标按钮用于开关抽屉。

## 4. 关键技术点
- 使用 Vue 的 `onMounted` 监听 `touchstart` 和 `mousedown` 事件以唤醒 UI。
- 使用 CSS `backdrop-filter: blur()` 提升控制栏的高级感。
