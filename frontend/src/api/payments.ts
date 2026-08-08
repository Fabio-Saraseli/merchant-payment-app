import { apiRequest } from "./apiClient";

type PaymentData = {
  card_number: string;
  expiry: string;
  cvv: string;
  amount: string;
  description: string;
};



export async function createPayment(paymentData: PaymentData) {
  const result = await apiRequest("/payments", {
    method: "POST",
    authenticated: true,
    body: JSON.stringify(paymentData),
  });

  if (!result.ok) {
    return {
      success: false,
      message: result.data.message || "Unable to process payment",
      status: result.status,
    };
  }

  return {
    success: true,
    transaction: result.data.transaction,
  };
}
