import React from "react";
import Link from "next/link";
import Image from "next/image";

export default function Header() {
  return (
    <header className="site-header">
      <div className="header-container">
        <Link href="/" className="header-logo">
          <Image
            className="header-image"
            alt="Microblog Logo"
            src="/logo.svg"
            height={16}
            width={16}
            priority
          />
          {process.env.NEXT_PUBLIC_APP_NAME &&
            `${process.env.NEXT_PUBLIC_APP_NAME}`}
        </Link>
        <nav className="header-nav">
          <Link href="/logout" className="logout-cta">
            Logout
          </Link>
          <Image
            src="https://randomuser.me/api/portraits/men/1.jpg"
            className="user-avatar"
            alt="avatar"
            height={40}
            width={40}
            priority
          />
        </nav>
      </div>
    </header>
  );
}
