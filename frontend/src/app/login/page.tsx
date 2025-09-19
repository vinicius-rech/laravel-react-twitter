"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@context/AuthContext";
import Router from "next/dist/shared/lib/router/router";

export default function LoginPage() {
  const { loginUser } = useAuth<RouterContext>();
  const router = useRouter<Router>();
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [error, setError] = useState<string>("");

  return (
    <div>
      <form action="">
        <h1>Log in</h1>
        <input type="text" />
        <input type="email" />
        <input type="password" />
        <button>Log in</button>
        <p>
          Não possui uma conta? <a href="/register">Cadastre-se</a>
        </p>
      </form>
    </div>
  );
}
