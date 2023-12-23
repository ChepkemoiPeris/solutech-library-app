import axios from "axios";

let instance = axios.create({
  withCredentials: true,
});
axios.defaults.withCredentials = true;
instance.interceptors.request.use((request) => {  
  request.headers["Content-Type"] = "application/json";  
  const token = localStorage.getItem("token");
  if (token) {
    request.headers.Authorization = `Bearer ${token}`;
  }

  return request;
});

export default instance;
