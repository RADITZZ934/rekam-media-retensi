<template>
  <div class="h-full flex flex-col bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 overflow-hidden">
    
    <!-- Top Header Bar -->
    <div class="flex-shrink-0 bg-white/70 backdrop-blur-xl border-b border-white/50 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-200">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-700 to-purple-700 bg-clip-text text-transparent">
              Chat AI Assistant
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Powered by YuuLabs API • Tanya apa saja seputar rekam medis</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <!-- Character Selector -->
          <div class="relative" ref="charDropdownRef">
            <button 
              @click="showCharDropdown = !showCharDropdown"
              class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all text-sm"
            >
              <span class="text-lg">{{ selectedCharacter.avatar }}</span>
              <span class="font-medium text-gray-700">{{ selectedCharacter.name }}</span>
              <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': showCharDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <transition name="dropdown-menu">
              <div v-if="showCharDropdown" class="absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                <div class="p-3 border-b border-gray-100 bg-gray-50">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pilih Karakter AI</p>
                </div>
                <div class="p-2 max-h-64 overflow-y-auto">
                  <button 
                    v-for="char in characters" 
                    :key="char.id"
                    @click="selectCharacter(char)"
                    :class="[
                      'w-full flex items-center gap-3 p-3 rounded-xl text-left transition-all',
                      selectedCharacter.id === char.id 
                        ? 'bg-indigo-50 border border-indigo-200' 
                        : 'hover:bg-gray-50 border border-transparent'
                    ]"
                  >
                    <span class="text-2xl">{{ char.avatar }}</span>
                    <div>
                      <p class="font-medium text-sm text-gray-800">{{ char.name }}</p>
                      <p class="text-xs text-gray-500">{{ char.description }}</p>
                    </div>
                    <svg v-if="selectedCharacter.id === char.id" class="w-5 h-5 text-indigo-500 ml-auto flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- New Chat Button -->
          <button 
            @click="startNewChat"
            class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-200 transition-all text-sm font-medium"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Chat Baru
          </button>
        </div>
      </div>
    </div>

    <!-- Chat Content Area -->
    <div class="flex-1 flex overflow-hidden">
      
      <!-- Sidebar: Chat History -->
      <div class="w-72 bg-white/50 backdrop-blur-sm border-r border-gray-200/60 flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-gray-200/60">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input 
              v-model="searchHistory"
              type="text" 
              placeholder="Cari percakapan..." 
              class="w-full pl-10 pr-4 py-2.5 bg-gray-100/80 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none"
            />
          </div>
        </div>
        <div class="flex-1 overflow-y-auto p-3 space-y-1.5">
          <div v-if="filteredHistory.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
            <svg class="w-10 h-10 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-sm">Belum ada percakapan</p>
          </div>
          <button
            v-for="(chat, index) in filteredHistory"
            :key="chat.id"
            @click="loadChat(index)"
            :class="[
              'w-full p-3 rounded-xl text-left transition-all group',
              activeHistoryIndex === index 
                ? 'bg-indigo-50 border border-indigo-200 shadow-sm' 
                : 'hover:bg-gray-100/80 border border-transparent'
            ]"
          >
            <div class="flex items-start justify-between gap-2">
              <p class="text-sm font-medium text-gray-800 truncate flex-1">{{ chat.title }}</p>
              <button 
                @click.stop="deleteChat(index)"
                class="opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-lg transition-all"
              >
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ chat.time }}</p>
          </button>
        </div>
      </div>

      <!-- Main Chat Panel -->
      <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Messages Area -->
        <div ref="messageContainer" class="flex-1 overflow-y-auto px-6 py-6">
          
          <!-- Welcome State -->
          <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full">
            <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mb-6 shadow-2xl shadow-indigo-200 animate-float">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang! 👋</h2>
            <p class="text-gray-500 text-center max-w-md mb-8">
              Saya AI Assistant RSUK. Tanyakan apa saja seputar rekam medis, retensi, atau topik lainnya.
            </p>
            <div class="grid grid-cols-2 gap-3 max-w-lg w-full">
              <button 
                v-for="suggestion in suggestions" 
                :key="suggestion"
                @click="sendSuggestion(suggestion)"
                class="p-4 bg-white rounded-2xl border border-gray-200 hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-100 transition-all text-left group"
              >
                <p class="text-sm text-gray-700 group-hover:text-indigo-700 transition-colors">{{ suggestion }}</p>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 mt-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Messages -->
          <div v-else class="max-w-3xl mx-auto space-y-6">
            <div 
              v-for="(msg, index) in messages" 
              :key="index"
              :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']"
              class="animate-message-in"
            >
              <!-- AI Message -->
              <div v-if="msg.role === 'ai'" class="flex items-start gap-3 max-w-[85%]">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-md px-5 py-4 shadow-sm border border-gray-100">
                  <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap" v-html="formatMessage(msg.text)"></div>
                  <p class="text-[10px] text-gray-400 mt-2">{{ msg.time }}</p>
                </div>
              </div>
              
              <!-- User Message -->
              <div v-else class="max-w-[75%]">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl rounded-tr-md px-5 py-4 shadow-lg shadow-indigo-200">
                  <p class="text-sm leading-relaxed">{{ msg.text }}</p>
                  <p class="text-[10px] text-indigo-200 mt-2">{{ msg.time }}</p>
                </div>
              </div>
            </div>

            <!-- Typing Indicator -->
            <div v-if="isLoading" class="flex items-start gap-3 animate-message-in">
              <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
              <div class="bg-white rounded-2xl rounded-tl-md px-5 py-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-1.5">
                  <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></span>
                  <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                  <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="flex-shrink-0 bg-white/70 backdrop-blur-xl border-t border-gray-200/60 px-6 py-4">
          <div class="max-w-3xl mx-auto">
            <div class="relative flex items-end gap-3">
              <div class="flex-1 relative">
                <textarea 
                  ref="inputRef"
                  v-model="userInput"
                  @keydown="handleKeyDown"
                  rows="1"
                  placeholder="Ketik pesan Anda di sini..."
                  class="w-full px-5 py-3.5 pr-14 bg-gray-100/80 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white outline-none transition-all text-sm resize-none overflow-hidden leading-relaxed"
                  :style="{ height: textareaHeight }"
                ></textarea>
                <button
                  @click="sendMessage"
                  :disabled="!userInput.trim() || isLoading || isTyping"
                  class="absolute right-2 bottom-2 w-10 h-10 flex items-center justify-center bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-200 transition-all disabled:opacity-30 disabled:cursor-not-allowed disabled:shadow-none"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                  </svg>
                </button>
              </div>
            </div>
            <p class="text-[10px] text-center text-gray-400 mt-2">
              Tekan <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-gray-600 font-mono">Enter</kbd> untuk mengirim, 
              <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-gray-600 font-mono">Shift+Enter</kbd> untuk baris baru
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, computed, onMounted, onBeforeUnmount, watch } from 'vue'

// State
const messages = ref([])
const userInput = ref('')
const isLoading = ref(false)
const conversationId = ref('')
const messageContainer = ref(null)
const inputRef = ref(null)
const textareaHeight = ref('48px')
const searchHistory = ref('')
const showCharDropdown = ref(false)
const charDropdownRef = ref(null)
const activeHistoryIndex = ref(-1)
const isTyping = ref(false) // New state for stream effect

// Characters
const characters = ref([
  { id: 'unlimited', name: 'AI Assistant', description: 'Asisten pintar serba bisa', avatar: '🤖' },
])
const selectedCharacter = ref(characters.value[0])

// Chat History
const chatHistory = ref([])

// Suggestions
const suggestions = [
  '📋 Apa itu retensi rekam medis?',
  '🏥 Prosedur alih media dokumen',
  '📄 Syarat pemusnahan berkas medis',
  '⏰ Jangka waktu penyimpanan RM',
]

// Computed
const filteredHistory = computed(() => {
  if (!searchHistory.value.trim()) return chatHistory.value
  const q = searchHistory.value.toLowerCase()
  return chatHistory.value.filter(c => c.title.toLowerCase().includes(q))
})

// Methods
const formatTime = () => {
  return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

const formatMessage = (text) => {
  // Basic markdown-like formatting
  let formatted = text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code class="px-1.5 py-0.5 bg-gray-100 rounded text-indigo-600 text-xs font-mono">$1</code>')
    .replace(/\n/g, '<br>')
  return formatted
}

const scrollToBottom = async () => {
  await nextTick()
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight
  }
}

const autoResizeTextarea = () => {
  if (!inputRef.value) return
  inputRef.value.style.height = '48px'
  const newHeight = Math.min(inputRef.value.scrollHeight, 150)
  textareaHeight.value = newHeight + 'px'
}

const handleKeyDown = (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    sendMessage()
  }
}

const sendSuggestion = (text) => {
  userInput.value = text
  sendMessage()
}

const selectCharacter = (char) => {
  selectedCharacter.value = char
  showCharDropdown.value = false
}

const sendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return

  const userText = userInput.value.trim()
  messages.value.push({ role: 'user', text: userText, time: formatTime() })
  userInput.value = ''
  textareaHeight.value = '48px'
  isLoading.value = true
  scrollToBottom()

  // Auto-save to history
  if (messages.value.filter(m => m.role === 'user').length === 1) {
    chatHistory.value.unshift({
      id: Date.now(),
      title: userText.substring(0, 50) + (userText.length > 50 ? '...' : ''),
      time: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }),
      messages: [],
      conversationId: '',
      characterId: selectedCharacter.value.id,
    })
    activeHistoryIndex.value = 0
  }

  try {
    const response = await fetch('/api/chatai/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({
        message: userText,
      }),
    })

    if (!response.ok) throw new Error('Gagal terhubung ke AI')

    // REAL STREAMING (Read Reader)
    const reader = response.body.getReader()
    const decoder = new TextDecoder()
    let buffer = '' // Buffer for incomplete lines
    
    const messageIndex = messages.value.length
    messages.value.push({ role: 'ai', text: '', time: formatTime() })
    isTyping.value = true
    
    let fullText = ''

    while (true) {
      const { done, value } = await reader.read()
      if (done) break
      
      buffer += decoder.decode(value, { stream: true })
      const lines = buffer.split('\n')
      buffer = lines.pop() // Keep the last incomplete line
      
      for (const line of lines) {
        const cleanLine = line.trim()
        if (!cleanLine || !cleanLine.startsWith('data: ')) continue
        
        try {
          const jsonStr = cleanLine.replace('data: ', '').trim()
          if (!jsonStr) continue
          
          const data = JSON.parse(jsonStr)
          let newText = ''
          
          // Robust checking for Gemini response structure
          if (data.candidates?.[0]?.content?.parts?.[0]?.text) {
            newText = data.candidates[0].content.parts[0].text
          } else if (data.result && typeof data.result === 'string') {
            newText = data.result
          }
          
          if (newText) {
            fullText += newText
            messages.value[messageIndex].text = fullText
            scrollToBottom()
          }
        } catch (e) {
          // Log parsing error but don't break the loop
          console.warn("Stream parse error:", e, line)
        }
      }
    }
    isTyping.value = false

  } catch (error) {
    console.error(error)
    messages.value.push({ role: 'ai', text: '❌ Koneksi ke server AI gagal. Silakan coba lagi.', time: formatTime() })
  } finally {
    isLoading.value = false
    scrollToBottom()

    // Update history
    if (activeHistoryIndex.value >= 0 && chatHistory.value[activeHistoryIndex.value]) {
      chatHistory.value[activeHistoryIndex.value].messages = [...messages.value]
      chatHistory.value[activeHistoryIndex.value].conversationId = conversationId.value
    }
  }
}

const startNewChat = () => {
  messages.value = []
  conversationId.value = ''
  activeHistoryIndex.value = -1
  userInput.value = ''
  textareaHeight.value = '48px'
}

const loadChat = (index) => {
  const chat = chatHistory.value[index]
  if (chat) {
    messages.value = [...chat.messages]
    conversationId.value = chat.conversationId || ''
    activeHistoryIndex.value = index
    const char = characters.value.find(c => c.id === chat.characterId)
    if (char) selectedCharacter.value = char
    scrollToBottom()
  }
}

const deleteChat = (index) => {
  chatHistory.value.splice(index, 1)
  if (activeHistoryIndex.value === index) {
    startNewChat()
  } else if (activeHistoryIndex.value > index) {
    activeHistoryIndex.value--
  }
}

// Click outside to close dropdown
const handleClickOutside = (e) => {
  if (charDropdownRef.value && !charDropdownRef.value.contains(e.target)) {
    showCharDropdown.value = false
  }
}

// Watch input for auto-resize
watch(userInput, () => {
  nextTick(autoResizeTextarea)
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Float animation for welcome icon */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}
.animate-float {
  animation: float 4s ease-in-out infinite;
}

/* Message entrance animation */
@keyframes messageIn {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-message-in {
  animation: messageIn 0.3s ease-out;
}

/* Dropdown menu animation */
.dropdown-menu-enter-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.dropdown-menu-leave-active {
  transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}
.dropdown-menu-enter-from,
.dropdown-menu-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.98);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 4px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 8px;
}
::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Textarea styling */
textarea {
  scrollbar-width: thin;
  scrollbar-color: #d1d5db transparent;
}
</style>
