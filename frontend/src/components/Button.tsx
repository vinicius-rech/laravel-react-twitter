import React from "react";

interface ButtonProps {
  children: React.ReactNode;
  type?: "button" | "submit" | "reset";
  onClick?: () => void;
}

const Button: React.FC<ButtonProps> = ({
  children,
  type = "button",
  onClick,
}) => {
  return (
    <button className="btn-submit" type={type} onClick={onClick}>
      {children}
    </button>
  );
};

export default Button;
