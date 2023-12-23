<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-4">
        <h4 class="text-3xl font-bold mt-5 mb-4 ms-4">Issued Books</h4>
      </div>
      <div class="col-md-4">
        <router-link v-if="isAdmin" class="btn btn-primary me-2 rounded mt-5 text-center" to="/book/issue">Issue book</router-link>
      </div>
      <div class="col-md-4 mt-5">
        <input
          v-model="value"
          @input="performSearch"
          class="form-control"
          type="text"
          placeholder="Search by email..."
        />
      </div>
    </div>

    <div v-if="successMessage" class="alert alert-success mt-3" role="alert">
      {{ successMessage }}
    </div>

    <div class="row mt-3">
      <div class="col">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Book ISBN</th>
              <th>User Email</th>
              <th>Issue Date</th>
              <th>Due Date</th>
              <th>Return Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(book, index) in issuedBooks.data" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ book.book?.isbn }}</td>
              <td>{{ book.user?.email }}</td>
              <td>{{ book.loan_date }}</td>
              <td>{{ book.due_date }}</td>
              <td>{{ book.return_date ? book.return_date : 'Not returned' }}</td>
              <td>{{ book.status }}</td>
              <td>
              <button v-if="book.status === 'borrowed'" class="btn btn-success btn-sm " @click="returnBook(book.id)">Return</button>
              <button v-if="book.status === 'pending'" class="btn btn-info btn-sm me-2" @click="approveBook(book.id)">Approve</button>
 
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end">
        <li class="page-item" :class="{ disabled: issuedBooks.current_page === 1 }">
          <a class="page-link" @click="changePage(1)" aria-label="First">
            <span aria-hidden="true">&laquo;&laquo;</span>
          </a>
        </li>
        <li class="page-item" :class="{ disabled: issuedBooks.current_page === 1 }">
          <a
            class="page-link"
            @click="changePage(issuedBooks.current_page - 1)"
            aria-label="Previous"
          >
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li v-for="page in issuedBooks.last_page" :key="page" class="page-item" :class="{ active: page === issuedBooks.current_page }">
          <a class="page-link" @click="changePage(page)">{{ page }}</a>
        </li>
        <li
          class="page-item"
          :class="{ disabled: issuedBooks.current_page === issuedBooks.last_page }"
        >
          <a
            class="page-link"
            @click="changePage(issuedBooks.current_page + 1)"
            aria-label="Next"
          >
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
        <li
          class="page-item"
          :class="{ disabled: issuedBooks.current_page === issuedBooks.last_page }"
        >
          <a
            class="page-link"
            @click="changePage(issuedBooks.last_page)"
            aria-label="Last"
          >
            <span aria-hidden="true">&raquo;&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>

    <div
      class="modal fade"
      id="confirmDeleteModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="confirmDeleteModalLabel"
      aria-hidden="true"
    >
      <!-- ... Modal content ... -->
    </div>
  </div>
</template>

<script>
import repository from "@/api/repository";

export default {
  name: "IssuedBooks",
  data() {
    return {
      issuedBooks: {
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
      value: "",
    };
  },
  watch: {
    page: "getIssuedBooks",
    limit: "getIssuedBooks",
  },
  mounted() {
    this.getIssuedBooks();
    this.isAdmin = repository.isAdmin();
  },
  methods: {
    getIssuedBooks() {
      repository
        .getIssuedBooks(this.page, this.limit, this.value)
        .then((response) => { 
          this.issuedBooks = response.data;
        })
        .catch((error) => {
          console.error("Error fetching issued books:", error);
        });
    },
    changePage(page) {
      if (page >= 1 && page <= this.issuedBooks.last_page) {
        this.page = page;
      }
    }, 
    returnBook(id) {
      repository.returnBook(id).then((response) => {
       this.successMessage = response.data.message; 
       this.getIssuedBooks(); 
      });
    },
    approveBook(id){
    repository.approveBook(id).then((response) => {
       this.successMessage = response.data.message; 
       this.getIssuedBooks(); 
      });
    },
    performSearch() {
      this.page = 1;
      this.getIssuedBooks();
    }, 
  },
};
</script>

<style scoped>
/* Add any additional styling specific to this component */
</style>
