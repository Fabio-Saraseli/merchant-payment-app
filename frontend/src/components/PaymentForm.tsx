import { handleCvvChange } from "../helpers/handleCvvChange";
import { handleExpiryChange } from "../helpers/handleExpiryChange";
import { handleCardNumberChange } from "../helpers/handleCardNumberChange";
import { useSubmitPayment } from "../hooks/useSubmitPayment";

function PaymentForm() {
  const {
    amount,
    setAmount,
    description,
    setDescription,
    cardNumber,
    setCardNumber,
    expiry,
    setExpiry,
    cvv,
    setCvv,
    errorMessage,
    isSubmitting,
    paymentMessage,
    handleSubmit,
  } = useSubmitPayment();

  return (
    <section className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div className="text-left">
        <h2 className="text-lg font-semibold text-slate-900 sm:text-xl">
          Charge Card
        </h2>

        <p className="mt-1 text-sm text-slate-500">
          Create a new payment for this merchant.
        </p>
      </div>

      <form onSubmit={handleSubmit} className="mt-6 space-y-5">
        <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div className="text-left">
            <label
              htmlFor="cardNumber"
              className="mb-2 block text-sm font-medium text-slate-700"
            >
              Card Number
            </label>

            <input
              id="cardNumber"
              type="text"
              inputMode="numeric"
              autoComplete="cc-number"
              value={cardNumber}
              onChange={(event) => handleCardNumberChange(event, setCardNumber)}
              maxLength={19}
              placeholder="4242 4242 4242 4242"
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500"
            />
          </div>

          <div className="text-left">
            <label
              htmlFor="expiry"
              className="mb-2 block text-sm font-medium text-slate-700"
            >
              Expiry Date
            </label>

            <input
              id="expiry"
              type="text"
              inputMode="numeric"
              autoComplete="cc-exp"
              value={expiry}
              onChange={(event) => handleExpiryChange(event, setExpiry)}
              maxLength={5}
              placeholder="MM/YY"
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500"
            />
          </div>

          <div className="text-left">
            <label
              htmlFor="cvv"
              className="mb-2 block text-sm font-medium text-slate-700"
            >
              CVV
            </label>

            <input
              id="cvv"
              type="password"
              inputMode="numeric"
              autoComplete="cc-csc"
              value={cvv}
              onChange={(event) => handleCvvChange(event, setCvv)}
              maxLength={4}
              placeholder="123"
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500"
            />
          </div>

          <div className="text-left">
            <label
              htmlFor="amount"
              className="mb-2 block text-sm font-medium text-slate-700"
            >
              Amount
            </label>

            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">
                €
              </span>

              <input
                id="amount"
                type="number"
                min="0"
                step="0.01"
                value={amount}
                onChange={(event) => setAmount(event.target.value)}
                placeholder="0.00"
                className="w-full rounded-lg border border-slate-300 py-2.5 pl-8 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500"
              />
            </div>
          </div>
        </div>

        <div className="text-left">
          <label
            htmlFor="description"
            className="mb-2 block text-sm font-medium text-slate-700"
          >
            Description
          </label>

          <input
            id="description"
            type="text"
            value={description}
            onChange={(event) => setDescription(event.target.value)}
            placeholder="Payment description"
            className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500"
          />
        </div>

        {errorMessage && (
          <div className="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-left text-sm text-red-700">
            {errorMessage}
          </div>
        )}

        {paymentMessage && (
          <div className="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-left text-sm text-green-700">
            {paymentMessage}
          </div>
        )}

        <button
          type="submit"
          disabled={isSubmitting}
          className="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
        >
          {isSubmitting ? "Processing..." : "Charge Card"}
        </button>
      </form>
    </section>
  );
}

export default PaymentForm;
