<template>
  <div class="home container">
    <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Add Book</h1>
    <div class="row">
      <p v-if="errorMessage" class="col-md-6 offset-md-3 text-danger">{{ errorMessage }}</p>
      <p v-if="success" class="col-md-6 offset-md-3 text-success">{{ success }}</p>
      <div class="col-md-6 offset-md-3 border p-4">
        <form @submit.prevent="submitForm">
          <div class="row g-3">
            <div v-for="field in formFields" :key="field.name" :class="['col-md-6', field.class]">
              <label :for="field.id" class="form-label">{{ field.label }}</label>
              <template v-if="field.type === 'file'">
                <input type="file" class="form-control" :id="field.id" @change="handleFileChange" />
              </template>
              <template v-else>
                <input :type="field.type" class="form-control" :id="field.id" :name="field.name" v-model="book[field.name]" />
              </template>
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
  name: "AddBookView",
  components: {},
  data() {
    return {
      errorMessage: "",
      success: "",
      book: {
        name: null,
        publisher: null,
        isbn: null,
        category: null,
        sub_category: null,
        description: null,
        pages: null,
        image: null,
      },
      formFields: [
        { name: "name", label: "Name", type: "text", id: "inputName", class: "" },
        { name: "publisher", label: "Publisher", type: "text", id: "publisher", class: "" },
        { name: "isbn", label: "ISBN", type: "text", id: "isbn", class: "" },
        { name: "description", label: "Description", type: "textarea", id: "description", class: "" },
        { name: "category", label: "Category", type: "text", id: "category", class: "" },
        { name: "sub_category", label: "Sub Category", type: "text", id: "sub_category", class: "" },
        { name: "pages", label: "Pages", type: "text", id: "pages", class: "" },
        { name: "image", label: "Image", type: "file", id: "image", class: "" },
      ],
    };
  },
  methods: {
    showMessage(message, type) {
      this[type] = message;
      setTimeout(() => {
        this[type] = "";
      }, 5000);
    },
    handleFileChange(event) {
      this.book.image = event.target.files[0];
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
    resetForm() {
      this.formFields.forEach((field) => {
        this.book[field.name] = null;
      });
    },
  },
};
</script>
