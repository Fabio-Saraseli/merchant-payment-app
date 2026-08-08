import type { Merchant } from "../api/auth";

const TOKEN_KEY = "merchant_token";
const TOKEN_EXPIRY_KEY = "token_expires_at";
const MERCHANT_KEY = "merchant";

export function saveSession(
  token: string,
  expiresAt: string,
  merchant: Merchant,
) {
  localStorage.setItem(TOKEN_KEY, token);
  localStorage.setItem(TOKEN_EXPIRY_KEY, expiresAt);
  localStorage.setItem(MERCHANT_KEY, JSON.stringify(merchant));
}

export function clearSession() {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(TOKEN_EXPIRY_KEY);
  localStorage.removeItem(MERCHANT_KEY);
}

export function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

export function isSessionExpired() {
  const expiresAt = localStorage.getItem(TOKEN_EXPIRY_KEY);

  if (!expiresAt) {
    return true;
  }

  const expiryTime = new Date(expiresAt).getTime();

  if (Number.isNaN(expiryTime)) {
    return true;
  }

  return expiryTime <= Date.now();
}

export function getMerchant() {
  const storedMerchant = localStorage.getItem(MERCHANT_KEY);

  if (!storedMerchant) {
    return null;
  }

  try {
    return JSON.parse(storedMerchant);
  } catch {
    return null;
  }
}
