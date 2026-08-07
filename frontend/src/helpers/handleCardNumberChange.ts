export const handleCardNumberChange = (
  event: React.ChangeEvent<HTMLInputElement>,
  setCardNumber: React.Dispatch<React.SetStateAction<string>>,
) => {
  const digits = event.target.value.replace(/\D/g, "").slice(0, 16);

  const formatted = digits.replace(/(.{4})/g, "$1 ").trim();

  setCardNumber(formatted);
};
