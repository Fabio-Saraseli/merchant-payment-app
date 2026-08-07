export const handleCvvChange = (
  event: React.ChangeEvent<HTMLInputElement>,
  setCvv: React.Dispatch<React.SetStateAction<string>>,
) => {
  const digits = event.target.value.replace(/\D/g, "").slice(0, 4);

  setCvv(digits);
};
