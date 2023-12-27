<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-4">
        <h4 class="text-3xl font-bold mt-5 mb-4 ms-4">Issued Books</h4>
      </div>
      <div class="col-md-4">
        <router-link v-if="isAdmin" class="btn btn-primary me-2 rounded mt-5 text-center" to="/book/issue">Issue
          book</router-link>
      </div>
      <div class="col-md-4 mt-5">
        <input v-model="value" @input="performSearch" class="form-control" type="text" placeholder="Search by email..." />
      </div>
    </div>

    <div v-if="successMessage" class="alert alert-success mt-3" role="alert">
      {{ successMessage }}
    </div>

    <div class="row mt-3">
      <div class="col">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Book</th>
                <th>User Email</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Penalty</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(book, index) in issuedBooks.data" :key="index">
                <td>{{ index + 1 }}</td>
                <td data-bs-toggle="tooltip" data-bs-placement="top" :title="'ISBN: ' + book.book?.isbn">{{
                  book.book?.name
                }}</td>
                <td>{{ book.user?.email }}</td>
                <td>{{ book.loan_date }}</td>
                <td>
                  <span v-if="book.extended">
                    {{ book.extension_date }}
                    <span class="text-info"> Extended</span>
                  </span>
                  <span v-else>
                    {{ book.due_date }}
                  </span>
                </td>
                <td>{{ book.return_date ? book.return_date : '--' }}</td>
                <td>
                  <span v-if="book.penalty_amount">
                    {{ book.penalty_amount }}
                    <span v-if="book.penalty_status === 'paid'" class="text-success">{{ book.penalty_status }}</span>
                    <span v-if="book.penalty_status === 'unpaid'" class="text-danger">{{ book.penalty_status }}</span>
                  </span>
                  <span v-else>
                    --
                  </span>
                </td>
                <td>{{ book.status }}</td>

                <td v-if="isAdmin" class="px-0 ps-0">
                  <button v-if="book.status === 'borrowed'" class="btn btn-success btn-sm d-inline"
                    @click="returnBook(book.id)">Return</button>
                  <button v-if="book.status === 'pending'" class="btn btn-info btn-sm me-2 d-inline"
                    @click="approveBook(book.id)">Approve</button>
                  <button v-if="isAdmin && book.status === 'borrowed' && !book.penalty_amount"
                    class="btn btn-danger btn-sm mx-1 d-inline" data-bs-toggle="modal"
                    data-bs-target="#confirmPenaltyModal" @click="setBookToPenalty(book.id)">
                    Add Penalty
                  </button>
                  <button v-if="isAdmin && book.penalty_status === 'unpaid'" class="btn btn-danger btn-sm mx-1 d-inline"
                    @click="payPenalty(book.id)">
                    Pay Penalty
                  </button>
                </td>

                <td v-if="!isAdmin && book.status === 'borrowed'"> <button class="btn btn-info btn-sm me-2"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Extend by 1 week"
                    @click="extendDueDate(book.id)">Extend</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal fade" id="confirmPenaltyModal" tabindex="-1" role="dialog"
      aria-labelledby="confirmPenaltyModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmPenaltyModalLabel">Confirm Penalty</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to apply a penalty of KES 500 to this user?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="applyPenalty">Apply
              Penalty</button>
          </div>
        </div>
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
          <a class="page-link" @click="changePage(issuedBooks.current_page - 1)" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li v-for="page in issuedBooks.last_page" :key="page" class="page-item"
          :class="{ active: page === issuedBooks.current_page }">
          <a class="page-link" @click="changePage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: issuedBooks.current_page === issuedBooks.last_page }">
          <a class="page-link" @click="changePage(issuedBooks.current_page + 1)" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
        <li class="page-item" :class="{ disabled: issuedBooks.current_page === issuedBooks.last_page }">
          <a class="page-link" @click="changePage(issuedBooks.last_page)" aria-label="Last">
            <span aria-hidden="true">&raquo;&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>

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
      successMessage: "",
      value: "",
      bookToPenalty: ""
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
          let user = JSON.parse(localStorage.getItem("user")).id
          if (this.isAdmin) {
            this.issuedBooks = response.data;
          } else {
            this.issuedBooks.data = response.data.data.filter(book => book.user_id === user);
          }

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
    approveBook(id) {
      repository.approveBook(id).then((response) => {
        this.successMessage = response.data.message;
        this.getIssuedBooks();
      });
    },
    extendDueDate(id) {
      repository.extendDueDate(id).then((response) => {
        this.successMessage = response.data.message;
        this.getIssuedBooks();
      });
    },
    setBookToPenalty(id) {
      this.bookToPenalty = id;
    },

    applyPenalty() {
      if (this.bookToPenalty) {
        repository.applyPenalty(this.bookToPenalty).then((response) => {
          this.successMessage = 'Penalty set';
          this.getIssuedBooks();
          this.bookToPenalty = null;
        });
      }
    },
    payPenalty(id) {
      repository.payPenalty(id).then((response) => {
        this.successMessage = 'Penalty Paid';
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
 
