<template>
    <div class="home container">
      <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Change Password</h1>
      <div v-if="errorMessage.length > 0" class="alert alert-danger mt-3">
          <ul>
            <li v-for="(error, index) in errorMessage" :key="index">{{ error }}</li>
          </ul>
        </div>
 
        <div v-if="success" class="alert alert-success mt-3">
          {{ success }}
        </div>
      <div class="row">
        <div class="col-md-6 mx-auto">
          <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input v-model="currentPassword" type="password" class="form-control" id="current_password" aria-describedby="emailHelp">
          </div>
          <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input v-model="newPassword" type="password" class="form-control" id="new_password">
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm new Password</label>
            <input v-model="confirmPassword" type="password" class="form-control" id="confirm_password">
          </div>
          <button type="submit" class="btn btn-primary" @click="changePassword">Submit</button> 
        
        </div>
      </div>
    </div>
  </template>
  
  
  <script>
  import repository from "@/api/repository";
  
  export default {
    data() {
      return {
        currentPassword: "",
        newPassword: "",
        confirmPassword: "",
        errorMessage: [], // Change to an array
        success: "",
      };
    },
    methods: {
      changePassword() {
        const data = {
          current_password: this.currentPassword,
          new_password: this.newPassword,
          confirm_password: this.confirmPassword,
        };
  
        repository
          .changePassword(data)
          .then((response) => { 
            this.success = "Password changed successfully"; 
            this.clearFields();
            setTimeout(() => (this.success = ""), 3000);
          })
          .catch((error) => {
            console.log(error);
            if (error.response && error.response.data && error.response.data.message) {
              this.errorMessage = Object.values(error.response.data.message).flat();
            } else {
              this.errorMessage = ["An error occurred. Please try again."];
            }
            setTimeout(() => (this.errorMessage = []), 3000);
          });
      },
      clearFields() {
        this.currentPassword = "";
        this.newPassword = "";
        this.confirmPassword = "";
      },
    },
  };
  </script>
  