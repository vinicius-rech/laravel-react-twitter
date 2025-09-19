"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@context/AuthContext";
import { AxiosError } from "axios";
import Input from "../../components/Input";
import Button from "../../components/Button";
import Title from "../../components/Title";

export default function RegisterPage() {
  const { registerNewUser } = useAuth();
  const router = useRouter();
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [name, setName] = useState<string>("");
  const [password_confirmation, setPasswordConfirmation] = useState<string>("");
  const [error, setError] = useState<string>("");

  React.useEffect(() => {
    console.log(email);
  }, [email]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      await registerNewUser(name,email, password, password_confirmation);
      router.push("/feed");
    } catch (error: unknown) {
      const axiosError = error as AxiosError<{ message: string }>;
      setError(axiosError.response?.data?.message || "Erro ao efetuar cadastro.");
    }
  };

  return (
    <div className="container">
      <form className="form-container" onSubmit={handleSubmit}>
        <Title>Crie sua conta</Title>

        <Input
          type="text"
          placeholder="Nome completo"
          value={name}
          onChange={(e) => setName(e.target.value)}
        />

        <Input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />

        <Input
          type="password"
          placeholder="Senha"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />

        <Input
          type="password"
          placeholder="Confirmação de senha"
          value={password_confirmation}
          onChange={(e) => setPasswordConfirmation(e.target.value)}
        />

        <Button type="submit">Cadastrar</Button>
        <p className="register-cta">
          Já possui uma conta? <a href="/login">Faça login</a>
        </p>
      </form>
    </div>
  );
}
