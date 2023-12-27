<template>
    <div class="home container">
    <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Issue Book</h1>
    <div class="row">
      <p v-if="errorMessage" class="col-md-6 offset-md-3 text-danger">{{ errorMessage }}</p>
      <p v-if="successMessage" class="col-md-6 offset-md-3 text-success">{{ successMessage }}</p>
      <div class="col-md-6 offset-md-3 border p-4"> 
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-group">
                <label for="role">Book</label>
                <v-select v-model="selectedBook" :options="bookOptions" label="text"></v-select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="role">User</label>
                <v-select v-model="selectedUser" :options="userOptions" label="text"></v-select> 
              </div>
            </div> 
            <div class="col-md-6">
              <button class="btn btn-primary" @click="borrowBook(selectedBook.id, selectedUser.id)">Issue</button>
            </div>
          </div> 
      </div>
    </div>
  </div> 
  </template>

<script>
import repository from "@/api/repository"; 

export default { 
  mounted() {  
    this.getBooks();
    this.getUsers();
  },
  data() {
    return { 
      bookOptions: [],
      userOptions: [],
      users: {},  
      books: {},
      selectedBook: null,  
      selectedUser: null, 
      errorMessage: "",
      successMessage: "",
    };
  },
  methods: {
    getBooks() {
      repository.getBooks().then((response) => {
        this.books = response.data.data; 
       
        this.bookOptions = response.data.data
          .filter(book => !book.isBorrowed)
          .map(book => ({
            id: book.id,
            text: `${book.name}-${book.isbn}`,
          })); 
      });
    },
    getUsers() { 
      repository.getUsers().then((response) => {
        this.users = response.data.data;
        this.userOptions = response.data.data 
        .map((user) => ({
          id: user.id,
          text: user.email,
        })); 
      });
    },
    borrowBook(bookId, userId) { 
      repository.borrowBook(bookId, userId,'borrowed').then((response) => { 
        this.successMessage = 'Book issued successfully';
        this.selectedBook = null;  
        this.selectedUser = null; 
      });
    },
  },
};
</script>

 