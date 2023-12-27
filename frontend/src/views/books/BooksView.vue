<template>
  <div class="home container">
    <div class="row">
      <div class="col-md-4">
        <h4 class="text-3xl font-bold mt-5 mb-5 ms-4">Books</h4>
      </div>
      <div class="col-md-4">
        <RouterLink
          v-if="isAdmin"
          class="btn btn-primary me-2 rounded mt-5 text-center"
          to="/book/create"
          >Add book</RouterLink
        >
      </div>
      <div class="col-md-4 mt-5"> 
      <input
        v-model="value"
        @input="performSearch"
        class="form-control"
        type="text"
        placeholder="Search by name..."
      /> 
      </div>
    </div>
    <div v-if="successMessage" class="alert alert-success" role="alert">
      {{ successMessage }}
    </div>

    <div class="row row-cols-1 row-cols-md-4 g-4 mb-3 d-flex flex-wrap">
      <div v-for="(book, index) in books.data" :key="index" class="col ms-3">
        <div class="card">
          <img
            :src="`http://localhost:8000${book.image_url}`"
            class="card-img-top"
            style="max-width: 100%; height: 200px"
            alt="Book Image"
          />
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ book.name }}</h5>
            <p class="card-text">{{ truncateDescription(book.description) }}</p> 
            <p class="card-text"><strong>Status: </strong>
            <span v-if="book.status === 'pending'">
              {{ book.borrower ? "Pending approval" : "Unavailable" }}
            </span>
            <span v-else-if="book.status === 'returned'" class="text-success"
              >Available</span
            >
            <span v-else-if="book.status === 'borrowed'" class="text-danger"
              >Unavailable</span
            >
            <span v-else class="text-success"
              >Available</span
            >
            </p>
            <div class="mt-auto" v-if="isAdmin">
              <router-link
                class="btn btn-info me-2 rounded"
                :to="{ path: '/book/details/' + book.id }"
                >Details</router-link
              >
              <router-link
                class="btn btn-primary me-2 rounded"
                :to="{ path: '/book/edit/' + book.id }"
                >Edit</router-link
              >
              <button
                type="button"
                class="btn btn-danger rounded"
                data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal"
                @click="confirmDeleteBook(book.id)"
              >
                Delete
              </button>
            </div>
            <div class="mt-auto" v-if="!isAdmin">
              <router-link
                class="btn btn-info me-2 rounded"
                :to="{ path: '/book/details/' + book.id }"
                >Details</router-link
              >
               <button 
                    class="btn btn-primary"
                    @click="borrowBook(book.id)"
                    v-if="!book.isBorrowed"
                  >
                    Borrow
                  </button> 
                   <button 
                    class="btn btn-secondary"
                    @click="borrowBook(book.id)"
                    v-if="book.isBorrowed"
                    disabled
                  >
                    Unavailable
                  </button> 
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="confirmDeleteModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="confirmDeleteModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">
              Confirm Delete
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this book?
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              data-bs-dismiss="modal"
              class="btn btn-danger"
              @click="deleteBook"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: books.current_page === 1 }">
          <a class="page-link" @click="changePage(1)" aria-label="First"
            ><span aria-hidden="true">&laquo;&laquo;</span></a
          >
        </li>
        <li class="page-item" :class="{ disabled: books.current_page === 1 }">
          <a
            class="page-link"
            @click="changePage(books.current_page - 1)"
            aria-label="Previous"
            ><span aria-hidden="true">&laquo;</span></a
          >
        </li>
        <li
          v-for="page in books.last_page"
          :key="page"
          class="page-item"
          :class="{ active: page === books.current_page }"
        >
          <a class="page-link" @click="changePage(page)">{{ page }}</a>
        </li>
        <li
          class="page-item"
          :class="{ disabled: books.current_page === books.last_page }"
        >
          <a
            class="page-link"
            @click="changePage(books.current_page + 1)"
            aria-label="Next"
            ><span aria-hidden="true">&raquo;</span></a
          >
        </li>
        <li
          class="page-item"
          :class="{ disabled: books.current_page === books.last_page }"
        >
          <a
            class="page-link"
            @click="changePage(books.last_page)"
            aria-label="Last"
            ><span aria-hidden="true">&raquo;&raquo;</span></a
          >
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import repository from "@/api/repository";

export default {
  name: "books",
  components: {},
  data() {
    return {
      books: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      page: 1,
      limit: 10,
      property: "name", 
      isAdmin: false,
      bookToDelete: null,
      successMessage: "",
      status: "",
      borrower: false,
      value: "", 
    };
  },
  watch: {
    page: function () {
      this.getBooks();
    },
    limit: function () {
      this.getBooks();
    },
  },
  mounted() {
    this.getBooks();
    this.isAdmin = repository.isAdmin();
  },
  methods: {
    getBooks() {  
      repository.getBooks(this.page, this.limit,this.value).then((response) => {
        this.books = response.data;
         this.books.data.forEach((book) => { 
        if (book.book_loan && book.book_loan.length > 0) { 
          book.status = book.book_loan[0].status; 
          const loggedInUserId = JSON.parse(localStorage.getItem("user")).id;
          book.borrower = book.book_loan[0].user_id === loggedInUserId;
        } else { 
          book.status = "";
          book.borrower = false;
        }
      });
       
      });
    },
    
    truncateDescription(description) {
      if (description) {
        const words = description.split(" ").slice(0, 5).join(" ");
        return description.split(" ").length > 5 ? `${words} ...` : words;
      }
      return "";
    },
    changePage(page) {
      if (page >= 1 && page <= this.books.last_page) {
        this.page = page;
      }
    },
    deleteBook() {
      repository.deleteBook(this.bookToDelete).then((response) => {
        const deletedBookId = this.bookToDelete;
        this.books.data = this.books.data.filter(
          (book) => deletedBookId !== book.id
        );
        this.successMessage = "Book deleted successfully!";
        setTimeout(() => {
          this.successMessage = "";
        }, 5000);
      });
    }, 
    confirmDeleteBook(bookId) {
      this.bookToDelete = bookId;
    },
     performSearch() { 
      this.page = 1;  
      this.getBooks();
    },
    borrowBook(id) {
    let user = JSON.parse(localStorage.getItem("user"))   
    repository.borrowBook(id,user.id).then((response) => {
       this.successMessage = response.data.message; 
       this.getBooks();
       });
    },
  },
};
</script>
