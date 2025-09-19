import React from "react";
import Image from "next/image";

interface TweetProps {
  avatar: string;
  name: string;
  time: string;
  text: string;
}

const Tweet: React.FC<TweetProps> = ({ avatar, name, time, text }) => {
  return (
    <div className="tweet-container">
      <Image
        width={40}
        height={40}
        src={avatar}
        alt={name}
        className="tweet-avatar"
      />
      <div>
        <div className="tweet-header">
          <span className="tweet-name">{name}</span>
          <span className="tweet-time">{time}</span>
          <span className="tweet-feed-controls">
            <Image
              src="/icons/pencil.svg"
              alt="Edit tweet"
              className="mr-1"
              width={22}
              height={22}
            />
            <Image
              src="/icons/trash.svg"
              alt="Delete tweet"
              width={22}
              height={22}
            />
          </span>
        </div>
        <div className="tweet-text">{text}</div>
      </div>
    </div>
  );
};

export default Tweet;
