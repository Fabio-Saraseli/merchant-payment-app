import { apiRequest } from "./apiClient";

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

type TransactionResult =
  | {
      success: true;
      transactions: Transaction[];
    }
  | {
      success: false;
      message: string;
    };

export async function getTransactions(
  fromDate = "",
  toDate = "",
): Promise<TransactionResult> {
  const params = new URLSearchParams();

  if (fromDate) {
    params.set("from", fromDate);
  }

  if (toDate) {
    params.set("to", toDate);
  }

  const query = params.toString();
  const path = `/transactions${query ? `?${query}` : ""}`;

  const result = await apiRequest(path, {
    authenticated: true,
  });

  if (!result.ok) {
    return {
      success: false,
      message: result.data.message || "Unable to load transactions",
    };
  }

  return {
    success: true,
    transactions: result.data.transactions,
  };
}
