export const handleExpiryChange = (
  event: React.ChangeEvent<HTMLInputElement>,
  setExpiry: React.Dispatch<React.SetStateAction<string>>,
) => {
  const digits = event.target.value.replace(/\D/g, "").slice(0, 4);

  if (digits.length > 2) {
    setExpiry(`${digits.slice(0, 2)}/${digits.slice(2)}`);
    return;
  }

  setExpiry(digits);
};
