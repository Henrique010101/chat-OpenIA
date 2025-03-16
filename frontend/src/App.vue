<template>
    <div id="app">
      <ChatComponent />
    </div>
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
  
  <style>
  #app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    margin-top: 30px;
    height: 90vh;
  }

  html {
    height: 100%;
    background-color: #f0f0ff;
  }
  </style>