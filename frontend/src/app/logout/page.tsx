"use client";
import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/context/AuthContext";

export default function LogoutPage() {
  const { logout } = useAuth();
  const router = useRouter();

  useEffect(() => {
    (async () => {
      await logout();
      router.replace("/login");
    })();
  }, [logout, router]);

  return null;
}
