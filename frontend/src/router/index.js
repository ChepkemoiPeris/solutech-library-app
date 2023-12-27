import { createRouter, createWebHistory } from 'vue-router' 
import LoginView from '../views/LoginView.vue';
import BooksView from '../views/books/BooksView.vue';
import BookDetails from '../views/books/BookDetailsView.vue';
import AddBook from '../views/books/AddBook.vue';
import EditBook from '../views/books/EditBook.vue'; 

import UsersView  from '../views/users/UsersView.vue';
import AddUser  from '../views/users/AddUser.vue';
import EditUser  from '../views/users/EditUser.vue';
import ChangePassword from '../views/users/ChangePassword.vue';

import IssuedBooks  from '../views/book_loans/IssuedBooks.vue';
import IssueBook  from '../views/book_loans/IssueBook.vue';

import middleware from "./middleware";
const router = createRouter({ 
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [ 
    {
      path: "/",
      name: "login",
      component: LoginView,
      beforeEnter: middleware.guest,
    }, 
    {
      path: "/logout",
      name: "logout",
      beforeEnter: middleware.user,
    },
    {
      path: "/books",
      name: "books",
      component: BooksView,
      beforeEnter: middleware.user,
    },
    ,
  {
    path: "/book/details/:id",
    name: "book",
    component: BookDetails,
    beforeEnter: middleware.user,
  },
  {
    path: "/book/create",
    name: "addBook",
    component: AddBook,
    beforeEnter: middleware.user,
  },
  {
    path: "/book/edit/:id",
    name: "editBook",
    component: EditBook,
    beforeEnter: middleware.user,
  },
  {
    path: "/users",
    name: "users",
    component: UsersView,
    beforeEnter: middleware.user,
  },
  {
    path: "/add/user",
    name: "addUser",
    component: AddUser,
    beforeEnter: middleware.user,
  },
  {
    path: "/edit/user/:id",
    name: "editUser",
    component: EditUser,
    beforeEnter: middleware.user,
  },
  {
    path: "/user/change_password/:id",
    name: "change_password",
    component: ChangePassword,
    beforeEnter: middleware.user,
    
  },
  {
    path: "/books/issued",
    name: "issuedBooks",
    component: IssuedBooks,
    beforeEnter: middleware.user,
  }, 
  {
    path: "/book/issue",
    name: "issueBook",
    component: IssueBook,
    beforeEnter: middleware.user,
  },
  ]
})

export default router
