import React from "react";
import Image from "next/image";

interface TweetProps {
  avatar: string;
  name: string;
  time: string;
  text: string;
  canEdit?: boolean;
  onEdit?: () => void;
  onDelete?: () => void;
}

const Tweet: React.FC<TweetProps> = ({
  avatar,
  name,
  time,
  text,
  canEdit = false,
  onEdit,
  onDelete,
}) => {
  return (
    <div className="tweet-container">
      <Image
        width={40}
        height={40}
        src={avatar}
        alt={name}
        className="tweet-avatar"
        unoptimized
      />
      <div>
        <div className="tweet-header">
          <span className="tweet-name">{name}</span>
          <span className="tweet-time">{time}</span>
          {canEdit && (
            <span className="tweet-feed-controls">
              <button
                type="button"
                aria-label="Editar tweet"
                onClick={onEdit}
                className="mr-1"
              >
                <Image
                  src="/icons/trash.svg"
                  alt="Edit tweet"
                  width={22}
                  height={22}
                />
              </button>
              <button
                type="button"
                aria-label="Excluir tweet"
                onClick={onDelete}
              >
                <Image
                  src="/icons/pencil.svg"
                  alt="Delete tweet"
                  width={22}
                  height={22}
                />
              </button>
            </span>
          )}
        </div>
        <div className="tweet-text">{text}</div>
      </div>
    </div>
  );
};

export default Tweet;
