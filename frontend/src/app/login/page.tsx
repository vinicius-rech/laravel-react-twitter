"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@context/AuthContext";
import { AxiosError } from "axios";

import Input from "../../components/Input";
import Button from "../../components/Button";
import Title from "../../components/Title";

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
        <Title>Log in</Title>
        <Input
          onChange={(e) => setEmail(e.target.value)}
          placeholder="Email"
          value={email}
          type="email"
        />

        <Input
          onChange={(e) => setPassword(e.target.value)}
          placeholder="Password"
          value={password}
          type="password"
        />

        <Button type="submit">Log in</Button>
        <p className="register-cta">
          Não possui uma conta? <a href="/register">Cadastre-se</a>
        </p>
      </form>
    </div>
  );
}
