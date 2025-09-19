"use client";
import axios, { InternalAxiosRequestConfig, AxiosResponse } from "axios";
import Cookies from "js-cookie";

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api",
  withCredentials: false,
});

api.interceptors.request.use((requestConfig: InternalAxiosRequestConfig) => {
  const token = Cookies.get("token");

  if (token && requestConfig.headers) {
    requestConfig.headers.Authorization = `Bearer ${token}`;
  }

  return requestConfig;
});

export interface LoginCredentials {
  email: string;
  password: string;
}

export type User = {
  id: string | number;
  email: string;
  name: string;
};

type AuthPayload = {
  token_type: string; // "Bearer"
  token: string;
  user: User;
};

function hasData(value: unknown): value is { data: unknown } {
  return typeof value === "object" && value !== null && "data" in value;
}

const unwrap = <T>(response: AxiosResponse<unknown>): { data: T } => {
  let payload: unknown = response;
  if (hasData(response)) {
    payload = response.data;
    if (hasData(payload)) {
      payload = payload.data;
    }
  }
  return { data: payload as T };
};

export const login = async (credentials: LoginCredentials) => {
  const response = await api.post("/login", credentials);
  return unwrap<AuthPayload>(response);
};

export const registerUser = async (credentials: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}) => {
  const response = await api.post("/register", credentials);
  return unwrap<AuthPayload>(response);
};

export const getCurrentUser = async () => {
  const response = await api.get("/user");
  return unwrap<{ user: User }>(response);
};

export const logout = async () => {
  // Sanctum expects POST /logout with Bearer token
  return api.post("/logout");
};

// ---------- Tweets ----------
export type Visibility = "public" | "private";

export type Tweet = {
  id: number;
  user_id: number;
  content: string;
  visibility: Visibility;
  created_at: string;
  updated_at: string;
  user?: User;
};

export type Paginated<T> = {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};

export const getTweets = async (page = 1) => {
  const response = await api.get("/tweets", { params: { page } });
  return { data: response.data as unknown as Paginated<Tweet> };
};

export const createTweet = async (payload: {
  content: string;
  visibility: Visibility;
}) => {
  const response = await api.post("/tweets", payload);
  return unwrap<Tweet>(response);
};

export const updateTweet = async (
  id: number,
  payload: { content: string; visibility: Visibility }
) => {
  const response = await api.put(`/tweets/${id}`, payload);
  return unwrap<Tweet>(response);
};

export const deleteTweet = async (id: number) => api.delete(`/tweets/${id}`);

export default api;
