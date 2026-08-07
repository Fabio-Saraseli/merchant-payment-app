export function clearSession() {
  localStorage.removeItem("merchant_token");
  localStorage.removeItem("token_expires_at");
  localStorage.removeItem("merchant");
}

export function isSessionExpired() {
  const expiresAt = localStorage.getItem("token_expires_at");

  if (!expiresAt) {
    return true;
  }

  return new Date(expiresAt).getTime() <= Date.now();
}
