"use client";
import { createContext, useContext, useEffect, useState, ReactNode } from "react";
import Cookies from "js-cookie";
import { login, getCurrentUser } from "@lib/api";

interface User {
  id: number;
  name: string;
  email: string;
}

interface AuthContextProps {
  user: User | null;
  loading: boolean;
  loginUser: (email: string, password: string) => Promise<void>;
}

const AuthContext = createContext<AuthContextProps>({} as AuthContextProps);

export const Authprovider = ({ children }: { children: ReactNode }) => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const savedToken = Cookies.get("token");

    if (savedToken) {
      getCurrentUser()
        .then(({ data }) => {
          setUser({
            ...data.user,
            id: Number(data.user.id),
          });
        })
        .catch(() => {
          Cookies.remove("token");
        })
        .finally(() => {
          setLoading(false);
        });
    } else {
      setLoading(false);
    }
  }, []);

  const loginUser = async (email: string, password: string) => {
    const { data } = await login({ email, password });

    // Expires in 7 days
    Cookies.set("token", data.token, { expires: 7 });

    setUser({
      ...data.user,
      id: Number(data.user.id),
    });
  };

  return (
    <AuthContext.Provider value={{ user, loading, loginUser }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
