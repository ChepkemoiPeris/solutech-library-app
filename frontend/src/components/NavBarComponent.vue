<template>
  
  	<nav class="navbar navbar-expand-lg navbar-light bg-info p-3">
       <div class="container">
  <RouterLink class="navbar-brand" to="/">Bookhaven</RouterLink>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div
    v-if="!isAuthenticated"
    class="font-bold text-white flex items-center space-x-5 flex-wrap bg-blue-500 p-6"
  >
    <!-- Content for unauthenticated users -->
  </div>
  <div v-else class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <RouterLink class="nav-link mx-2 active" aria-current="page" to="/">Home</RouterLink>
      </li>
      <li class="nav-item">
        <RouterLink class="nav-link mx-2" to="/books">Books</RouterLink>
      </li>
      <li class="nav-item" v-if="isAdmin">
        <RouterLink class="nav-link mx-2" to="/books/issued">Issue Books</RouterLink>
      </li>
      <li class="nav-item" v-if="!isAdmin">
        <RouterLink class="nav-link mx-2" to="/books/issued">Issued Books</RouterLink>
      </li>
      <li class="nav-item" v-if="isAdmin">
        <RouterLink class="nav-link mx-2" to="/users">Users</RouterLink>
      </li>
      <li class="nav-item">
        <a class="nav-link mx-2 btn" @click="logout">Logout</a>
      </li>
    </ul>
  </div>
</div>

    </nav>
    
</template>


<script>
import repository from "@/api/repository";

export default {
  data() {
    return {
      isAuthenticated: false,
      isAdmin:false
    };
  },

  mounted() {
    this.isAuthenticated = repository.isAuthenticated();
    this.isAdmin = repository.isAdmin();
  },

  methods: {
    logout() {
      repository.logout();
      repository.removeToken();
      this.reloadPage();
      this.$router.push("/");
    },
    reloadPage() {
      window.location.reload();
    },
  },
};
</script> 
