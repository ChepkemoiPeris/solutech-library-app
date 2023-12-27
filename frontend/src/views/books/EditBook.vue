<template>
  <div class="home container">
    <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Update Book</h1>
    <div class="row">
      <p v-if="errorMessage" class="col-md-6 offset-md-3 text-danger">{{ errorMessage }}</p>
      <p v-if="success" class="col-md-6 offset-md-3 text-success">{{ success }}</p>
      <div class="col-md-6 offset-md-3 border p-4">
        <form @submit.prevent="editBook" enctype="multipart/form-data">
          <div class="row g-3">
            <div v-for="field in formFields" :key="field.name" :class="['col-md-6', field.class]">
              <label :for="field.id" class="form-label">{{ field.label }}</label>
              <template v-if="field.type === 'file' && !showFileInput">
                <button class="btn btn-outline-primary p-0 ms-2" @click="toggleFileInput">Update {{ field.label.toLowerCase() }}</button>
                <input type="text" class="form-control" :id="field.id" v-model="book[field.name]" />
              </template>
              <template v-else-if="field.type === 'file' && showFileInput">
                <input type="file" class="form-control" :id="field.id" @change="handleFileChange" />
              </template>
              <template v-else>
                <input :type="field.type" class="form-control" :id="field.id" :name="field.name" v-model="book[field.name]" />
              </template>
            </div>
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import repository from "@/api/repository";

export default {
  name: "AddBookView",
  components: {},
  data() {
    return {
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
      showFileInput: false,
      success: "",
      errorMessage: "",
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

  mounted() { 
    let id = this.$route.params.id;
    repository.getBook(id).then((book) => {
      this.book = book.data;
    });
  },

  methods: {
    editBook() {
      const { id, name, publisher, isbn, category, sub_category, description, pages, image } = this.book;
      let formData = new FormData();
      formData.append("id", id);
      formData.append("name", name);
      formData.append("publisher", publisher);
      formData.append("isbn", isbn);
      formData.append("category", category);
      formData.append("sub_category", sub_category);
      formData.append("description", description);
      formData.append("pages", pages);
      if (this.showFileInput && image) {
        formData.append("image", image);
      }
      const token = localStorage.getItem("token");
      const config = {
        headers: {
          "content-type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
      };
      axios
        .post(`http://localhost:8000/api/book/update/` + id, formData, config)
        .then((res) => { 
          this.success = res.data.message;
          setTimeout(() => this.$router.push("/books"), 3000);
        })
        .catch((error) => { 
          this.errorMessage = error.response.data.message;
        });
    },
    reloadPage() {
      window.location.reload();
    },
    handleFileChange(event) {
      this.book.image = event.target.files[0];
    },
    toggleFileInput() {
      this.showFileInput = !this.showFileInput;
    },
  },
};
</script>
