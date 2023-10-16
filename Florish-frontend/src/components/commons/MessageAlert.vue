<template>
    <div v-if="isVisible" class="alert">
      <div class="alert-content">
        {{ message }}
        <button @click="dismissAlert" class="close-button">Close</button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      message: String,
    },
    data() {
      return {
        isVisible: true,
      };
    },
    methods: {
      dismissAlert() {
        this.isVisible = false;
      },
    },
  };
  </script>
  
  <style scoped>
  .alert {
    position: fixed;
    top: 10px;
    right: 10px;
    background-color: #f44336;
    color: white;
    padding: 10px;
    border-radius: 5px;
    z-index: 9999;
  }
  
  .alert-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .close-button {
    background-color: transparent;
    color: white;
    border: none;
    cursor: pointer;
    font-weight: bold;
    margin-left: 10px;
  }
  </style>
  