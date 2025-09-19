"use client";
import axios, { InternalAxiosRequestConfig } from "axios";
import Cookies from 'js-cookie'


const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || "http://localhost/api",
  withCredentials: true,
});

api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = Cookies.get("token");
  
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});


export default api;
