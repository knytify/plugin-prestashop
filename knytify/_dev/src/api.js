import axios from "axios";

const instance = axios.create({
  baseURL: window.knytify.base_url,
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
  },
});

export default instance;
