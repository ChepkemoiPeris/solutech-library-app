<template>
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-4">
          <h4 class="text-3xl font-bold mt-5 mb-4 ms-4">Users</h4>
        </div>
        <div class="col-md-4">
          <router-link class="btn btn-primary me-2 rounded mt-5 text-center" to="/add/user">Add
            user</router-link>
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
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th> 
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(user, index) in users.data" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{
                    user.name
                  }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.role }}</td>   
                  <td class="px-0 ps-0">
                    <router-link
                class="btn btn-info btn-sm"
                :to="{ path: '/edit/user/' + user.id }"
                >Edit</router-link
              >
                    <button
                      class="btn btn-danger btn-sm mx-1 d-inline" data-bs-toggle="modal"
                      data-bs-target="#confirmDeleteModal" @click="setUserToDelete(user.id)">
                      delete
                    </button> 
                  </td> 
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm delete</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete user?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteUser">Delete</button>
            </div>
          </div>
        </div>
      </div>
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-end">
          <li class="page-item" :class="{ disabled: users.current_page === 1 }">
            <a class="page-link" @click="changePage(1)" aria-label="First">
              <span aria-hidden="true">&laquo;&laquo;</span>
            </a>
          </li>
          <li class="page-item" :class="{ disabled: users.current_page === 1 }">
            <a class="page-link" @click="changePage(users.current_page - 1)" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <li v-for="page in users.last_page" :key="page" class="page-item"
            :class="{ active: page === users.current_page }">
            <a class="page-link" @click="changePage(page)">{{ page }}</a>
          </li>
          <li class="page-item" :class="{ disabled: users.current_page === users.last_page }">
            <a class="page-link" @click="changePage(users.current_page + 1)" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
          <li class="page-item" :class="{ disabled: users.current_page === users.last_page }">
            <a class="page-link" @click="changePage(users.last_page)" aria-label="Last">
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
    name: "Users",
    data() {
      return {
        users: {
          data: [],
          current_page: 1,
          last_page: 1,
        },
        page: 1,
        limit: 10,
        property: "email", 
        successMessage: "",
        value: "",
        userToDelete: ""
      };
    },
    watch: {
      page: "getUsers",
      limit: "getUsers",
    },
    mounted() {
      this.getUsers(); 
    },
    methods: {
        getUsers() {
        repository
          .getUsers(this.page, this.limit, this.value)
          .then((response) => { 
              this.users = response.data; 
          })
          .catch((error) => {
            console.error("Error fetching issued users:", error);
          });
      },
      changePage(page) {
        if (page >= 1 && page <= this.users.last_page) {
          this.page = page;
        }
      }, 
      setUserToDelete(id) {
        this.userToDelete = id;
      }, 
      deleteUser() {
      repository.deleteUser(this.userToDelete).then((response) => {
        const deletedUserId = this.userToDelete;
        this.users.data = this.users.data.filter(
          (user) => deletedUserId !== user.id
        );
        this.successMessage = "User deleted successfully!";
        setTimeout(() => {
          this.successMessage = "";
        }, 5000);
      });
    }, 
      performSearch() {
        this.page = 1;
        this.getUsers();
      },
    },
  };
  </script>
   
  