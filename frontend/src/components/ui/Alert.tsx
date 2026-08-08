type AlertProps = {
  message: string;
  type: "error" | "success";
};

function Alert({
  message,
  type,
}: AlertProps) {
  const styles =
    type === "error"
      ? "border-red-200 bg-red-50 text-red-700"
      : "border-green-200 bg-green-50 text-green-700";

  return (
    <div
      className={`rounded-lg border px-3 py-2 text-left text-sm ${styles}`}
    >
      {message}
    </div>
  );
}

export default Alert;