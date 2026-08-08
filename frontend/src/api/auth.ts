import { API_BASE_URL } from "../constants";

type LoginCredentials = {
  email: string;
  password: string;
};

export type Merchant = {
  id: number;
  name: string;
  email: string;
};

type LoginSuccess = {
  expires_at: string;
  success: true;
  token: string;
  merchant: Merchant;
};

export type ResponseFailure = {
  success: false;
  message: string;
};

export type LoginResult = LoginSuccess | ResponseFailure;

export async function loginMerchant(
  credentials: LoginCredentials,
): Promise<LoginResult> {
  try {
    const response = await fetch(`${API_BASE_URL}/auth/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(credentials),
    });

    const data = await response.json();

    if (!response.ok) {
      return {
        success: false,
        message: data.message || "Unable to sign in",
      };
    }

    return {
      success: true,
      token: data.token,
      expires_at: data.expires_at,
      merchant: data.merchant,
    };
  } catch {
    return {
      success: false,
      message: "Unable to connect to the server",
    };
  }
}
