<template>
  <div>
    <h1 class="text-3xl font-bold mt-5 mb-5 text-center">Book Details</h1>
 <div v-if="successMessage" class="alert alert-success" role="alert">
      {{ successMessage }}
    </div>
    <div class="flex justify-center">
      <div class="w-full max-w-md m-5">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <img
            :src="`http://localhost:8000${book.image_url}`"
            alt="Book Image"
            class="mb-4 rounded-md w-full"
            style="max-height: 300px"
          />

          <div class="mb-4">
            <strong>Name/Title:</strong>
            <span
              class="inline-block bg-purple-200 rounded-full px-3 py-1 text-sm font-semibold text-purple-800 mr-2 mb-2"
              >{{ book.name }}</span
            >
          </div>
          <div class="mb-4">
            <strong>Publisher</strong>
            <span
              class="inline-block bg-purple-200 rounded-full px-3 py-1 text-sm font-semibold text-purple-800 mr-2 mb-2"
              >{{ book.publisher }}</span
            >
          </div>
          <div class="mb-4">
            <strong>Description:</strong>
            <p class="text-gray-700 text-base">{{ book.description }}</p>
          </div>

          <div class="mb-4"><strong>ISBN:</strong> {{ book.isbn }}</div>

          <div class="mb-4">
            <strong>Publisher:</strong> {{ book.publisher }}
          </div>

          <div class="mb-4"><strong>Category:</strong> {{ book.category }}</div>

          <div class="mb-4">
            <strong>Sub Category:</strong> {{ book.sub_category }}
          </div>

          <div class="mb-4"><strong>Pages:</strong> {{ book.pages }}</div>
          <div class="mb-4">
            <strong>Status: </strong>
            <span v-if="status === 'pending'">
              {{ borrower ? "Pending approval" : "Unavailable" }}
            </span>
            <span v-else-if="status === 'returned'" class="text-success"
              >Available</span
            >
            <span v-else-if="status === 'borrowed'" class="text-danger"
              >Unavailable</span
            >
            <span v-else class="text-success"
              >Available</span
            >
          </div>

          <div class="flex justify-between items-center"> 
            <div v-if="isAdmin">
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
            <div v-if="!isAdmin">  
              <button
                class="btn btn-secondary"
                v-if="status === 'pending' || status === 'borrowed'"
                disabled
              >
                Unavailable
              </button>
              <button
                class="btn btn-primary"
                @click="borrowBook(book.id)"
                v-else-if="status === 'returned'"
              >
                Borrow
              </button> 
               <button
                class="btn btn-primary"
                @click="borrowBook(book.id)"
                v-else
              >
                Borrow
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
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
              @click="deleteBook(book.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import repository from "@/api/repository";

export default {
  name: "BookView",
  data() {
    return {
      book: {},
      isAdmin: false,
      status: "",
      borrower: false,
      successMessage: "",
    };
  },
  mounted() {
    this.getBook();
    this.isAdmin = repository.isAdmin();
  },
  methods: {
    getBook() {
      repository.getBook(this.$route.params.id).then((response) => {
        this.book = response.data;
        if (response.data.book_loan && response.data.book_loan.length > 0) {
          this.status = response.data.book_loan[0].status; 
          if (
            response.data.book_loan[0].user_id ==
            JSON.parse(localStorage.getItem("user")).id
          ) {
            this.borrower = true;
          }
        }
      });
    },
    borrowBook(id) { 
        let user = JSON.parse(localStorage.getItem("user"))   
    repository.borrowBook(id,user.id).then((response) => {
       this.successMessage = response.data.message; 
       this.getBook();
       });
    },  
    deleteBook() {
      repository.deleteBook(this.bookToDelete).then((response) => {
        this.$router.push("/books");
      });
    },
    confirmDeleteBook(id) {
      this.bookToDelete = id;
    },
  },
};
</script>

<style scoped>
/* Add your scoped styles here */
</style>
