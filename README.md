# Ku10 (RenjuClass 在线教室) - 重构版

本项目是一个现代化的五子棋（连珠）在线课程播放与研究平台，重构自原有的 jQuery 版本。它提供了流畅的录像回放、高效的进度跳转以及内嵌式的棋局研究功能。

## 1. 技术栈

- **框架**: [Vue 3 (Composition API)](https://vuejs.org/) - 响应式状态管理与组件化开发。
- **构建工具**: [Vite](https://vitejs.dev/) - 极速的热重载与构建体验。
- **样式**: [Tailwind CSS](https://tailwindcss.com/) + [daisyUI](https://daisyui.com/) - 现代化的 UI 组件库。
- **渲染**: **HTML5 Canvas** - 用于高性能的棋盘渲染，支持无限缩放与流畅交互。

## 2. 核心架构与代码结构

项目采用典型的 Vue 3 单页应用架构，逻辑与 UI 高度解耦：

- **`src/composables/usePlayer.js`**: 核心播放引擎。负责 JSON 数据的异步加载、状态快照（Snapshots）预计算、播放逻辑调度以及 URL Hash 导航。
- **`src/components/`**:
  - `GoBoard.vue`: 高性能 Canvas 棋盘组件，支持显示模式与交互（研究）模式。
  - `AnalysisModal.vue`: 内嵌式研究推演组件。
  - `PlayerControls.vue`: 播放器控制栏，支持进度条跳转、倍速切换等。
  - `ChatList.vue` & `LessonList.vue`: 消息记录与课程导航列表。
- **`public/`**: 存放静态资源，包括 `json/`（课程数据）、`images/`（图标）和 `trans.json`（课程清单）。

## 3. 开发与构建步骤

### 运行环境要求
- Node.js (建议 v18+)
- npm 或 pnpm

### 安装依赖
```bash
npm install
```

### 本地开发
```bash
npm run dev
```
启动后访问 `http://localhost:3000` 即可查看。

### 编译生产环境 (Build)
```bash
npm run build
```
该命令会将项目编译并打包到根目录下的 `dist/` 文件夹中。

### 发布与部署
由于本项目是纯静态前端应用，发布非常简单：
1. 运行 `npm run build` 生成 `dist` 目录。
2. 将 `dist` 目录下的所有文件上传至你的 Web 服务器（如 Nginx、Apache）或静态托管平台（如 GitHub Pages、Vercel）。
3. 确保服务器正确托管 `index.html` 作为入口文件。

---

## 4. 关键改进点
- **零延迟跳转**: 通过预计算状态快照，解决了原版进度跳转需要重播的问题。
- **内嵌研究模式**: 告别弹出窗口，直接在教室内打开推演棋盘，支持右键悔棋。
- **现代 UI**: 采用 `emerald` 主题，适配移动端响应式布局。
