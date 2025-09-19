"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@context/AuthContext";
import { AxiosError } from "axios";

export default function LoginPage() {
  const { loginUser } = useAuth();
  const router = useRouter();
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [error, setError] = useState<string>("");

  React.useEffect(() => {
    console.log(email);
  }, [email]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      await loginUser(email, password);
      router.push("/feed");
    } catch (error: unknown) {
      const axiosError = error as AxiosError<{ message: string }>;
      setError(axiosError.response?.data?.message || "Erro ao efetuar login.");
    }

    router.push("/login");
  };

  return (
    <div className="container">
      <form className="form-container" onSubmit={handleSubmit}>
        <h1 className="title">Log in</h1>
        <input
          className="input-default"
          type="email"
          placeholder="Username or email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />

        <input
          className="input-default"
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
        
        <button className="btn-submit">Log in</button>
        <p className="register-cta">
          Não possui uma conta? <a href="/register">Cadastre-se</a>
        </p>
      </form>
    </div>
  );
}
