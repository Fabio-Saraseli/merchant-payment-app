import { clearSession } from "../helpers/authSession";
import { API_BASE_URL } from "../constants";
import type { Transaction } from "./payments";

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
  const token = localStorage.getItem("merchant_token");

  if (!token) {
    return {
      success: false,
      message: "Unauthorized",
    };
  }

  const params = new URLSearchParams();

  if (fromDate) {
    params.set("from", fromDate);
  }

  if (toDate) {
    params.set("to", toDate);
  }

  const query = params.toString();
  const url = `${API_BASE_URL}/transactions${query ? `?${query}` : ""}`;

  try {
    const response = await fetch(url, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const data = await response.json();

    if (response.status === 401) {
      clearSession();
      window.location.href = "/login";

      return {
        success: false,
        message: "Unauthorized",
      };
    }

    if (!response.ok) {
      return {
        success: false,
        message: data.message || "Unable to load transactions",
      };
    }

    return {
      success: true,
      transactions: data.transactions,
    };
  } catch {
    return {
      success: false,
      message: "Unable to connect to the server",
    };
  }
}
