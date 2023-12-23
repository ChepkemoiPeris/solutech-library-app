<template>
  <div class="home container">
    <!-- ... rest of your template ... -->
    <div class="col-md-6 offset-md-3 border p-4">
      <form @submit.prevent="submitForm">
        <div class="row g-3">
          <!-- ... rest of your form ... -->
          <div class="col-md-12">
            <div class="form-group">
              <label for="role">Book</label> 
              <select ref="bookSelect" class="form-control" v-select2="bookOptions"></select>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="role">User</label> 
              <select ref="userSelect" class="form-control" v-select2="userOptions"></select>
            </div>
          </div>
          <div class="col-md-6">
            <button class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

 <script>
import repository from "@/api/repository";
import axios from "axios";
import Select2 from 'select2';

export default {
  name: "IssueBook",
  components: {},
  mounted() { 
     const bookSelect = this.$refs.bookSelect;
    const userSelect = this.$refs.userSelect;

    if (bookSelect && userSelect) {
      new Select2(bookSelect);
      new Select2(userSelect);
    }
    this.getBooks();
    this.getUsers();
  },
  data() {
    return {
      errorMessage: "",
      success: "",
      bookOptions: [],
      userOptions: [],
      users: {}, // Removed unnecessary empty object
      books: {},
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
      repository.getBooks().then((response) => {
      console.log(response)
        this.bookOptions = response.data.data.map((book) => ({
          id: book.id,
          text: book.name, // Assuming the book object has a 'name' property
        })); 
         const bookSelect = this.$refs.bookSelect;

        // Trigger a change event
        if (bookSelect) {
          const event = new Event("change");
          bookSelect.dispatchEvent(event);
        }
      });
    },
    getUsers() {
      repository.getUsers().then((response) => {
      console.log(response.data)
        this.userOptions = response.data.data.map((user) => ({
          id: user.id,
          text: user.email,
        }));
        const userSelect = this.$refs.userSelect;
        if (userSelect) {
          const event = new Event("change");
          userSelect.dispatchEvent(event);
        }
      });
    }, 
  },
};
</script>

<style>
  @import "select2/dist/css/select2.min.css";
</style>