"use client";

import React, { useEffect, useState } from "react";
import Image from "next/image";
import Title from "@/components/Title";
import Tweet from "@/components/Tweet";
import {
  createTweet,
  getTweets,
  Tweet as TweetType,
  Visibility,
  updateTweet,
  deleteTweet,
} from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import { useRouter } from "next/navigation";

function Toggle({
  checked,
  onChange,
}: {
  checked: boolean;
  onChange: () => void;
}) {
  return (
    <button
      className={`toggle-switch ${checked ? "toggle-switch-on" : ""}`}
      aria-pressed={checked}
      onClick={onChange}
      type="button"
    >
      <span className={`toggle-knob ${checked ? "toggle-knob-on" : ""}`} />
    </button>
  );
}

export default function FeedPage() {
  const router = useRouter();
  const { user, loading } = useAuth();
  const [tweet, setTweet] = useState("");
  const [privacy, setPrivacy] = useState<Visibility>("public");
  const [loadingTweets, setLoadingTweets] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [tweets, setTweets] = useState<TweetType[]>([]);
  const isPublic = privacy === "public";

  const loadTweets = async () => {
    try {
      setLoadingTweets(true);
      const { data } = await getTweets();
      setTweets(data.data);
      setError(null);
    } catch {
      setError("Não foi possível carregar os tweets.");
    } finally {
      setLoadingTweets(false);
    }
  };

  useEffect(() => {
    if (!loading && !user) {
      router.replace("/login");
      return;
    }
    if (!loading && user) {
      loadTweets();
    }
  }, [loading, user, router]);

  const handlePost = async () => {
    if (!tweet.trim()) return;
    try {
      await createTweet({ content: tweet.trim(), visibility: privacy });
      setTweet("");
      await loadTweets();
    } catch {
      setError("Falha ao publicar o tweet.");
    }
  };

  const handleEdit = async (tweet: TweetType) => {
    const newContent = window.prompt("Editar tweet:", tweet.content);

    if (newContent === null) {
      return;
    }

    const trimmed = newContent.trim();
    if (!trimmed) {
      return;
    }

    try {
      await updateTweet(tweet.id, {
        content: trimmed,
        visibility: tweet.visibility,
      });
      await loadTweets();
    } catch {
      setError("Falha ao editar o tweet.");
    }
  };

  const handleDelete = async (tweet: TweetType) => {
    const confirmExclusion = window.confirm(
      "Deseja realmente excluir este tweet?"
    );

    if (!confirmExclusion) {
      return;
    }

    try {
      await deleteTweet(tweet.id);
      await loadTweets();
    } catch {
      setError("Falha ao excluir o tweet.");
    }
  };

  return (
    <div className="container">
      <div className="feed-container">
        <Title>Início</Title>
        {loading && <div>Verificando sessão...</div>}
        <div className="textarea-container">
          <div className="textarea-wrapper">
            <Image
              src={`https://randomuser.me/api/portraits/men/${(Number(user?.id) || 0) % 100}.jpg`}
              className="w-10 h-10 rounded-full"
              alt="avatar"
              height={40}
              width={40}
              priority
            />
            <textarea
              onChange={(e) => setTweet(e.target.value)}
              placeholder="O que está acontecendo?"
              className="textarea-tweet"
              value={tweet}
              rows={2}
            />
          </div>

          <div className="tweet-actions">
            <div className="tweet-privacy">
              <span>Postagem pública:</span>
              <Toggle
                checked={isPublic}
                onChange={() => setPrivacy(isPublic ? "private" : "public")}
              />
            </div>
            <button
              className="post-button"
              onClick={handlePost}
              disabled={!tweet.trim()}
            >
              Postar
            </button>
          </div>
        </div>

        <div className="tweet-feed">
          {loadingTweets && <div>Carregando…</div>}
          {error && <div className="text-red-500">{error}</div>}
          {!loadingTweets &&
            !error &&
            tweets.map((t) => (
              <Tweet
                key={t.id}
                avatar={`https://randomuser.me/api/portraits/men/${(Number(t.user?.id ?? t.user_id ?? t.id) || 0) % 100}.jpg`}
                name={t.user?.name || "Usuário"}
                time={new Date(t.created_at).toLocaleString()}
                text={t.content}
                canEdit={user ? t.user_id === user.id : false}
                onEdit={() => handleEdit(t)}
                onDelete={() => handleDelete(t)}
              />
            ))}
        </div>
      </div>
    </div>
  );
}
