<template>
  <div class="home container">
    <div class="row">
      <div class="col-md-6">
        <h4 class="text-3xl font-bold mt-5 mb-5 ms-4">Users</h4>
      </div>
      <div class="col-md-6">
        <router-link
          v-if="isAdmin"
          class="btn btn-primary me-2 rounded mt-5 text-center"
          to="/add/user"
        >
          Add User
        </router-link>
      </div>
    </div>

    <DataTable
      :data="users"
      :columns="columns"
      class="table table-hover table-striped"
      width="100%"
      :options="options"
    >
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Action</th>
        </tr>
      </thead>
    </DataTable>
  </div> 
  <VTable :data="users">
    <template #head>
      <tr>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Address</th>
      </tr>
    </template>
    <template #body="{rows}">
      <tr v-for="row in rows" :key="row.id">
        <td>{{ row.name }}</td>
        <td>{{ row.age }}</td>
        <td>{{ row.email }}</td>
        <td>
          {{ row.address.street }},
          {{ row.address.city }}
          {{ row.address.state }}
        </td>
      </tr>
    </template>
  </VTable> 
</template>

<script>
import repository from "@/api/repository";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";

DataTable.use(DataTablesCore);

export default {
  name: "users",
  components: {
    DataTable
  },
  data() {
    return {
      users: [],
      page: 1,
      limit: 3,
      property: "title",
      value: "",
      isAdmin: false,
      columns: [
        { data: "name" },
        { data: "email" },
        { data: "role" },
        {
          data: "action",
          render: (data, type, row) => this.generateActionButtons(row),
        },
      ],
      options: {
        serverSide: true,
        processing: true,
        ajax: this.getUsers,  
      },
    };
  },
  mounted() {
    this.isAdmin = repository.isAdmin();
    
  },
  methods: {
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
    generateActionButtons(row) {  
      return `
      <router-link
                class="btn btn-primary btn-sm"
                :to="{ path: '/edit/user/' + row.id }"
                >Edit</router-link
              >
              <button class="btn btn-danger btn-sm">Delete</button>`;
    },
  },
};
</script>
