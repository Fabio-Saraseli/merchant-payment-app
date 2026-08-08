import { apiRequest } from "./apiClient";

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

type ResponseFailure = {
  success: false;
  message: string;
};

type LoginResult = LoginSuccess | ResponseFailure;

export async function loginMerchant(
  credentials: LoginCredentials,
): Promise<LoginResult> {
  const result = await apiRequest("/auth/login", {
    method: "POST",
    body: JSON.stringify(credentials),
  });

  if (!result.ok) {
    return {
      success: false,
      message: result.data.message || "Unable to sign in",
    };
  }

  return {
    success: true,
    token: result.data.token,
    expires_at: result.data.expires_at,
    merchant: result.data.merchant,
  };
}
