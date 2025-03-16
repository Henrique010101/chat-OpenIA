<template>
  <main id="app">
    <ChatComponent />
  </main>
</template>

<script>
import ChatComponent from './components/ChatComponent.vue';

export default {
  name: 'App',
  components: {
    ChatComponent
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

#app {
  font-family: 'Roboto', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  margin-top: 30px;
  height: 90vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

html {
  height: 100%;
  background-color: #f0f0ff;
}
</style>