import { useState } from "react";

function PaymentForm() {
  const [amount, setAmount] = useState("");
  const [description, setDescription] = useState("");
  const [cardNumber, setCardNumber] = useState("");
  const [expiry, setExpiry] = useState("");
  const [cvv, setCvv] = useState("");

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    // TODO: need to submit form data to BE
  };

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
              onChange={(event) => setCardNumber(event.target.value)}
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
              onChange={(event) => setExpiry(event.target.value)}
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
              onChange={(event) => setCvv(event.target.value)}
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

        <button
          type="submit"
          className="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800 sm:w-auto"
        >
          Charge Card
        </button>
      </form>
    </section>
  );
}

export default PaymentForm;
