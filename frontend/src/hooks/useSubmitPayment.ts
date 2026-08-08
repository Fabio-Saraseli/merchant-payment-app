import { useState } from "react";
import { createPayment } from "../api/payments";
import {
  formatCardNumber,
  formatCvv,
  formatExpiry,
} from "../helpers/cardHelpers";

export function useSubmitPayment() {
  const [amount, setAmount] = useState("");
  const [description, setDescription] = useState("");
  const [cardNumber, setCardNumber] = useState("");
  const [expiry, setExpiry] = useState("");
  const [cvv, setCvv] = useState("");
  const [errorMessage, setErrorMessage] = useState("");
  const [paymentMessage, setPaymentMessage] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleCardNumberChange = (
    event: React.ChangeEvent<HTMLInputElement>,
  ) => {
    setCardNumber(formatCardNumber(event.target.value));
  };

  const handleExpiryChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setExpiry(formatExpiry(event.target.value));
  };

  const handleCvvChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setCvv(formatCvv(event.target.value));
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    setErrorMessage("");
    setPaymentMessage("");

    const cardDigits = cardNumber.replace(/\s/g, "");

    if (!cardDigits || !expiry || !cvv || !amount || !description.trim()) {
      setErrorMessage("All fields are required");
      return;
    }

    if (cardDigits.length !== 16) {
      setErrorMessage("Card number must contain 16 digits");
      return;
    }

    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
      setErrorMessage("Expiry date must use MM/YY format");
      return;
    }

    if (cvv.length < 3 || cvv.length > 4) {
      setErrorMessage("CVV must contain 3 or 4 digits");
      return;
    }

    if (Number(amount) <= 0) {
      setErrorMessage("Amount must be greater than 0");
      return;
    }

    setIsSubmitting(true);

    const result = await createPayment({
      card_number: cardDigits,
      expiry,
      cvv,
      amount,
      description: description.trim(),
    });

    setIsSubmitting(false);

    if (!result.success) {
      setErrorMessage(result.message);
      return;
    }

    setPaymentMessage(
      `Payment of €${(result.transaction.amount_cents / 100).toFixed(
        2,
      )} succeeded`,
    );

    setCardNumber("");
    setExpiry("");
    setCvv("");
    setAmount("");
    setDescription("");
  };

  return {
    amount,
    setAmount,
    description,
    setDescription,
    cardNumber,
    expiry,
    cvv,
    errorMessage,
    paymentMessage,
    isSubmitting,
    handleCardNumberChange,
    handleExpiryChange,
    handleCvvChange,
    handleSubmit,
  };
}
