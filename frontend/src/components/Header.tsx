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
            src="/logo.svg"
            alt="Microblog Logo"
            width={16}
            height={16}
            priority
          />
          Microblog
        </Link>
        <nav className="header-nav">
          <Link href="/logout" className="logout-cta">
            Logout
          </Link>
        </nav>
      </div>
    </header>
  );
}
