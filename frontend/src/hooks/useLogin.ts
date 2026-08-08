import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { loginMerchant } from "../api/auth";
import { saveSession } from "../helpers/authSession";

export const useLogin = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errorMessage, setErrorMessage] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const navigate = useNavigate();

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    setErrorMessage("");

    if (!email.trim() || !password.trim()) {
      setErrorMessage("Email and password required");
      return;
    }

    setIsSubmitting(true);

    const result = await loginMerchant({
      email,
      password,
    });

    setIsSubmitting(false);

    if (!result.success) {
      setErrorMessage(result.message);
      return;
    }

    saveSession(result.token, result.expires_at, result.merchant);

    navigate("/payments");
  };

  return {
    email,
    setEmail,
    password,
    setPassword,
    errorMessage,
    isSubmitting,
    handleSubmit,
  };
};
