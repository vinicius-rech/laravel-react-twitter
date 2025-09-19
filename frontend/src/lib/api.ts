"use client";
import axios, { InternalAxiosRequestConfig } from "axios";
import Cookies from "js-cookie";

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

interface LoginCredentials {
  email: string;
  password: string;
}

type LoginResponse = {
  token: string;
  user: {
    id: string;
    email: string;
    name: string;
  };
};

export const login = (credentials: LoginCredentials) =>
  api.post<LoginResponse>("/login", credentials);

export const registerUser = (credentials: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}) => api.post<LoginResponse>("/register", credentials);

export const getCurrentUser = () =>
  api.get<Omit<LoginResponse, "token">>("/user");

export default api;
