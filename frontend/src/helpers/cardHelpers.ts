export const formatCardNumber = (value: string) => {
  const digits = value.replace(/\D/g, "").slice(0, 16);

  return digits.replace(/(.{4})/g, "$1 ").trim();
};

export const formatCvv = (value: string) => {
  return value.replace(/\D/g, "").slice(0, 4);
};

export const formatExpiry = (value: string) => {
  const digits = value.replace(/\D/g, "").slice(0, 4);

  if (digits.length > 2) {
    return `${digits.slice(0, 2)}/${digits.slice(2)}`;
  }

  return digits;
};