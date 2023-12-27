<template>
  <div class="home container">
    <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Add User</h1>
    <p v-if="errorMessage" class="col-md-6 offset-md-3 text-danger">
      {{ errorMessage }}
    </p>
    <p v-if="success" class="col-md-6 offset-md-3 text-success">
      {{ success }}
    </p>
    <div class="row">
      <div class="col-md-6 offset-md-3 border p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="inputName" class="form-label">Name</label>
            <input
              type="text"
              class="form-control"
              id="inputName"
              name="name"
              v-model="user.name"
              required
            />
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input
              type="email"
              class="form-control"
              id="email"
              v-model="user.email"
              required
            />
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="role">Role</label>
              <select
                v-model="user.role"
                class="form-control"
                name="role"
                id="role"
                required
              >
                <option value="">Select Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              class="form-control"
              id="password"
              v-model="user.password"
              required
            />
          </div>

          <div class="col-md-6">
            <button class="btn btn-primary" @click="editUser">Submit</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import repository from "@/api/repository";

export default {
  name: "EditUserView",
  components: {},

  data() {
    return {
      user: {
        name: null,
        email: null,
        role: "",
        password: null,
      },
      errorMessage: "",
      success: "",
    };
  },
    mounted() { 
    let id = this.$route.params.id;
    repository.getUser(id).then((user) => {
      this.user = user.data; 
    });
  },
  methods: {
    editUser() {
      repository
        .editUser(this.user)
        .then((response) => { 
          if (response.data) {
            this.success = "User updated successfully";
          } 
          setTimeout(() => (this.success = ""), 3000);
        })
        .catch((error) => {
          console.log(error)
          this.errorMessage = error.response.data.message;
          setTimeout(() => (this.errorMessage = ""), 3000);
        });
    },
  },
};
</script>
