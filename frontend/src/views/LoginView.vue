<template> 
  <div id="login">
        <h3 class="text-center text-white pt-5">Login form</h3> 
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12"> 
                            <h3 class="text-center text-info">Login</h3>
                            <div class="form-group">
                                <label for="email" class="text-info">Email:</label><br>
                                <input type="text" name="email" id="email" class="form-control" v-model="user.email">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Password:</label><br>
                                <input type="password" name="password" id="password" class="form-control" v-model="user.password">
                            </div>
                            <div class="form-group mt-2">
                            <button
                            class="btn btn-info btn-md bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            @click="login"
                        >
                            Login
                        </button>
                         <p v-if="errorMessage" class="error-message text-danger">{{ errorMessage }}</p>
                            </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import repository from "@/api/repository";

export default {
  name: "LoginView",
  components: {},

  data() {
    return {
      user: {
        email: null,
        password: null,
      },
      errorMessage: '',
    };
  },
  methods: { 
    login() { 
      repository.createSession();
      repository.login(this.user).then((response) => {   
        repository.setUser(response.data.user)
        repository.setToken(response.data.token);
        this.reloadPage();
        this.$router.push("/books");
      }) .catch((error) => { 
      console.log(error)
      this.errorMessage = error.response.data.message;  
    });;
    },
    reloadPage() {
      window.location.reload();
    },
  },
};
</script>


