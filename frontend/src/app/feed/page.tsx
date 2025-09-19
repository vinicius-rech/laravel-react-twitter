"use client";

import React, { useState } from "react";
import Image from "next/image";
import Title from "@/components/Title";
import Tweet from "@/components/Tweet";
import mockTweets from "@/mocks/mockTweets";

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
  const [tweet, setTweet] = useState("");
  const [privacy, setPrivacy] = useState("public");
  const isPublic = privacy === "public";

  return (
    <div className="container">
      <div className="feed-container">
        <Title>Início</Title>
        <div className="textarea-container">
          <div className="textarea-wrapper">
            <Image
              src="https://randomuser.me/api/portraits/men/1.jpg"
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
            <button className="post-button">Postar</button>
          </div>
        </div>

        <div className="tweet-feed">
          {mockTweets.map((tweet, index) => (
            <Tweet key={index} {...tweet} />
          ))}
        </div>
      </div>
    </div>
  );
}
