<template>
<!--  <img alt="project logo" src="./assets/avatar.jpg">-->
  <HelloWorld msg="Go try that website to see if it has CSRF"/>
</template>

<script>
// import HelloWorld from './components/HelloWorld.vue'
import HelloWorld from './components/CSRF.vue'

export default {
  name: 'App',
  components: {
    HelloWorld
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
