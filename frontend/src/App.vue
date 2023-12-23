<script setup>
import { RouterLink, RouterView } from 'vue-router' 
import NavBarComponent from "./components/NavBarComponent.vue";
import LoginView from "./views/LoginView.vue";
import HomeView from "./components/HomeView.vue";

</script>

<template>

  <header>  
    <div class="wrapper">  
      <NavBarComponent />  
    </div>
  </header>
 
  <RouterView />
</template>

