export const formatCardNumber = (value: string) => {
  const digits = value.replace(/\D/g, "").slice(0, 16);

  return digits.replace(/(.{4})/g, "$1 ").trim();
};
