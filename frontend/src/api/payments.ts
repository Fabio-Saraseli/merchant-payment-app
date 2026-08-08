import { API_BASE_URL } from "../constants";

type PaymentData = {
  card_number: string;
  expiry: string;
  cvv: string;
  amount: string;
  description: string;
};

export type Transaction = {
  id: number;
  amount_cents: number;
  currency: string;
  description: string;
  card_last_four: string;
  status: string;
  provider_transaction_id: string | null;
  created_at: string;
};

export async function createPayment(paymentData: PaymentData) {
  const token = localStorage.getItem("merchant_token");

  if (!token) {
    return {
      success: false,
      message: "Unauthorized",
      status: 401,
    };
  }

  try {
    const response = await fetch(`${API_BASE_URL}/payments`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(paymentData),
    });

    const data = await response.json();

    if (!response.ok) {
      return {
        success: false,
        message: data.message || "Unable to process payment",
        status: response.status,
      };
    }

    return {
      success: true,
      transaction: data.transaction,
    };
  } catch {
    return {
      success: false,
      message: "Unable to connect to the server",
      status: 0,
    };
  }
}
