import { API_BASE_URL } from "../constants";
import { clearSession, getToken } from "../helpers/authSession";

type ApiRequestOptions = RequestInit & {
  authenticated?: boolean;
};

export async function apiRequest(
  path: string,
  options: ApiRequestOptions = {},
) {
  const {
    authenticated = false,
    headers: requestHeaders,
    ...requestOptions
  } = options;

  const headers = new Headers(requestHeaders);

  if (requestOptions.body) {
    headers.set("Content-Type", "application/json");
  }

  if (authenticated) {
    const token = getToken();

    if (!token) {
      clearSession();
      window.location.href = "/login";

      return {
        ok: false,
        status: 401,
        data: {
          message: "Unauthorized",
        },
      };
    }

    headers.set("Authorization", `Bearer ${token}`);
  }

  try {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      ...requestOptions,
      headers,
    });

    const data = await response.json();

    if (authenticated && response.status === 401) {
      clearSession();
      window.location.href = "/login";
    }

    return {
      ok: response.ok,
      status: response.status,
      data,
    };
  } catch {
    return {
      ok: false,
      status: 0,
      data: {
        message: "Unable to connect to the server",
      },
    };
  }
}
