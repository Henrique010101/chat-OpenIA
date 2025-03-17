<template>
  <div class="chat_container">
    <header class="chat_header">
      Chat OpenIA
    </header>
    <section class="messages_container" ref="messagesContainer">
      <article v-for="(message, index) in chatMessages" :key="index" :class="['message', message.sender]">
        <strong>{{ message.sender === 'User' ? 'USER' : 'OpenIA' }}:</strong> {{ message.text }}
      </article>
    </section>
    <div class="input_container">
      <input v-model="userInput" @keyup.enter="sendMessage" placeholder="No que você está pensando?" class="chat_input" />
      <button @click="sendMessage" class="send_button">Enviar</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      userInput: '',
      chatMessages: [],
      isLoading: false,
    };
  },
  methods: {
    async sendMessage() {
      if (this.userInput.trim() === '' || this.isLoading) return;
      this.isLoading = true;

      const userMessage = { sender: 'User', text: this.userInput };
      this.chatMessages.push(userMessage);

      try {
        const response = await fetch('http://localhost/backend/api/chat.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message: this.userInput }),
        });

        const responseData = await response.json();

        const botMessage = { sender: 'Bot', text: responseData.reply };
        this.chatMessages.push(botMessage);
      } catch (error) {
        console.error('Erro na requisição:', error);
      } finally {
        this.userInput = '';
        this.isLoading = false;
        this.$nextTick(() => {
          const messagesContainer = this.$refs.messagesContainer;
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
      }
    },
  },
};
</script>

<style scoped>
.chat_container {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 70%;
  margin: 0 auto;
  background-color: #f7f7f8;
  border: 1px solid #d0d0d0ed;
  border-radius: 8px;
  overflow: hidden;
  font-size: 1rem;
  box-shadow: 10px 6px 4px #A9A9A9;
}

.chat_header {
  height: 120px;
  background-color: #616161;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  border: 1px solid #626262ed;
}

.messages_container {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background-color: #ffffff;
  display: flex;
  flex-direction: column;
}

.message {
  position: relative;
  margin-bottom: 8px;
  padding: 10px;
  border-radius: 8px;
  max-width: 80%;
  word-wrap: break-word;
}

.message.User {
  align-self: flex-end;
  background-color: #cefacf;
}

.message.User::before {
  content: '';
  position: absolute;
  right: -10px;
  bottom: 7px;
  width: 0;
  height: 0;
  border-left: 13px solid #cefacf;
  border-top: 7px solid transparent;
  border-bottom: 7px solid transparent;
}

.message.Bot {
  align-self: flex-start;
  background-color: #efd4f5;
}

.message.Bot::before {
  content: '';
  position: absolute;
  left: -10px;
  bottom: 7px;
  width: 0;
  height: 0;
  border-right: 13px solid #efd4f5;
  border-top: 7px solid transparent;
  border-bottom: 7px solid transparent;
}

.chat_input {
  padding: 10px;
  border: 1px solid #d0d0d0;
  border-radius: 8px;
  font-size: 1rem;
  margin: 10px;
  width: calc(100% - 20px);
  box-sizing: border-box;
}

.chat_input:focus {
  outline: none;
  border-color: #898989;
}

.input_container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
}

.send_button {
  background-color: #0095c6;
  color: white;
  padding: 10px 20px;
  font-size: 1rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.send_button:hover {
  background-color: #007095;
}

.send_button:active {
  background-color: #00afe9;
}
</style>
