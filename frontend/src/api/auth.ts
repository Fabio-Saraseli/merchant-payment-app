type LoginCredentials = {
  email: string;
  password: string;
};

type Merchant = {
  id: number;
  name: string;
  email: string;
};

type LoginSuccess = {
  success: true;
  token: string;
  merchant: Merchant;
};

type LoginFailure = {
  success: false;
  message: string;
};

export type LoginResult = LoginSuccess | LoginFailure;

export async function loginMerchant(
  credentials: LoginCredentials,
): Promise<LoginResult> {
  try {
    const response = await fetch("http://localhost:8080/api/auth/login", {
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
      merchant: data.merchant,
    };
  } catch {
    return {
      success: false,
      message: "Unable to connect to the server",
    };
  }
}
