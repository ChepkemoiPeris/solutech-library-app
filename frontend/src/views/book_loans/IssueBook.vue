<template>
  <div class="home container">
    <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Issue Book</h1>
    <div class="row">
      <p v-if="errorMessage" class="col-md-6 offset-md-3 text-danger">{{ errorMessage }}</p>
      <p v-if="success" class="col-md-6 offset-md-3 text-success">{{ success }}</p>
      <div class="col-md-6 offset-md-3 border p-4">
        <form @submit.prevent="submitForm">
          <div class="row g-3">
             <div class="col-md-12">
            <div class="form-group">
              <label for="role">Book</label>
              <select 
                class="form-control"
                name="book_id"
                id="book_id"
                required
              >
                <option value="">Select Book</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="role">User</label>
              <select 
                class="form-control"
                name="user_id"
                id="user_id"
                required
              >
                <option value="">Select User</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
            <div class="col-md-6">
              <button class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import repository from "@/api/repository";
import axios from "axios";

export default {
  name: "IssueBook",
  components: {},
  data() {
    return {
      errorMessage: "",
      success: "",
      users: { 
      },
      books:{}
    };
  },
  methods: {
    showMessage(message, type) {
      this[type] = message;
      setTimeout(() => {
        this[type] = "";
      }, 5000);
    }, 
    submitForm() {
      let formData = new FormData();
      this.formFields.forEach((field) => {
        formData.append(field.name, this.book[field.name]);
      });
      const token = localStorage.getItem("token");
      const config = {
        headers: {
          "content-type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
      };
      axios
        .post("http://localhost:8000/api/book/create", formData, config)
        .then((res) => {
          this.showMessage(res.data.message, "success");
          this.resetForm();
        })
        .catch((error) => {
          console.log(error);
          this.showMessage(error.response.data.message, "errorMessage");
        });
    },
     getBooks() {  
      repository.getBooks(this.page, this.limit,this.value).then((response) => {
        this.books = response.data;
      });
    },
     getUsers(data, callback, settings) {
      repository
        .getUsers(data.start / data.length + 1, data.length)
        .then((response) => {
          callback({
            draw: data.draw,
            recordsTotal: response.data.recordsTotal,
            recordsFiltered: response.data.recordsFiltered,
            data: response.data.data,
          });
        });
    },
  },
};
</script>
