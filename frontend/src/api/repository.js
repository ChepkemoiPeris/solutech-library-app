import api from "./api";

const backendUrl = "http://localhost:8000";
 
export default {
  createSession() {
    return api.get(`${backendUrl}/sanctum/csrf-cookie`);
  },

  login(params) {
    const email = params.email;
    const password = params.password;
    return api.post(`${backendUrl}/api/auth/login`, {
      email,
      password,
    });
  },

  register(params) {
    const name = params.name;
    const email = params.email;
    const password = params.password;
    const password_confirmation = params.password_confirmation;
    return api.post(`${backendUrl}/api/auth/register`, {
      name,
      email,
      password,
      password_confirmation,
    });
  },

  logout() {
    return api.post(`${backendUrl}/api/auth/logout`);
  },

  getBooks(page, limit,name) {
    return api.get(`${backendUrl}/api/books?page=${page}&limit=${limit}&name=${name}`);
  },
  getIssuedBooks(page, limit,email){
    return api.get(`${backendUrl}/api/book_loans?page=${page}&limit=${limit}&email=${email}`);
  },
  addUser(params) { 
    const name = params.name;
    const email = params.email;
    const role = params.role;
    const password = params.password; 
    const token = localStorage.getItem("token");
    return api.post(`${backendUrl}/api/user/create`, {
          name,
          email,
          role,
          password
    });
  },
  editUser(params) { 
    const name = params.name;
    const email = params.email;
    const role = params.role;
    const password = params.password; 
    const token = localStorage.getItem("token");
    return api.post(`${backendUrl}/api/user/update`, {
          name,
          email,
          role,
          password
    });
  },

  getBook(id) {
    return api.get(`${backendUrl}/api/book/details/` + id);
  }, 
  getUser(id){
    return api.get(`${backendUrl}/api/user/` + id);
  },
  deleteBook(id) { 
    return api.delete(`${backendUrl}/api/book/delete/` + id);
  },

  borrowBook(book_id,user_id) { 
    const token = localStorage.getItem("token");
    return api.post(`${backendUrl}/api/book_loans/create/${book_id}`, {
      "user_id":user_id
      });
    
  },

  returnBook(id) {
    return api.post(`${backendUrl}/api/book_loans/return/` + id);
  },
  approveBook(id) {
    return api.post(`${backendUrl}/api/book_loans/approve/` + id);
  },
  getUsers(page, limit){
    return api.get(`${backendUrl}/api/users?page=${page}&limit=${limit}`);
  },

  searchBooks(property, value) {
    return api.get(`${backendUrl}/api/books/search/${property}/${value}`);
  },

  setToken(token) {
    localStorage.setItem("token", token);
  },
  setUser(user) {
    localStorage.setItem("user", JSON.stringify(user)); 
  },
  removeToken() {
    localStorage.removeItem("token");
  },

  isAuthenticated() { 
    if (localStorage.getItem("token")) {
      return true;
    }
    return false;
  },
  isAdmin() {
    let user = JSON.parse(localStorage.getItem("user"))  
    if (user && user.role == 'user') {
      return false;
    }
    return true;
  },
};
