import { useSubmitPayment } from "../hooks/useSubmitPayment";
import Alert from "./ui/Alert";
import FormField from "./ui/FormField";
import Input from "./ui/Input";

function PaymentForm() {
  const {
    amount,
    setAmount,
    description,
    setDescription,
    cardNumber,
    expiry,
    cvv,
    errorMessage,
    isSubmitting,
    paymentMessage,
    handleCardNumberChange,
    handleExpiryChange,
    handleCvvChange,
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
          <FormField label="Card Number" htmlFor="cardNumber">
            <Input
              id="cardNumber"
              type="text"
              inputMode="numeric"
              autoComplete="cc-number"
              value={cardNumber}
              onChange={handleCardNumberChange}
              maxLength={19}
              placeholder="4242 4242 4242 4242"
            />
          </FormField>

          <FormField label="Expiry Date" htmlFor="expiry">
            <Input
              id="expiry"
              type="text"
              inputMode="numeric"
              autoComplete="cc-exp"
              value={expiry}
              onChange={handleExpiryChange}
              maxLength={5}
              placeholder="MM/YY"
            />
          </FormField>

          <FormField label="CVV" htmlFor="cvv">
            <Input
              id="cvv"
              type="password"
              inputMode="numeric"
              autoComplete="cc-csc"
              value={cvv}
              onChange={handleCvvChange}
              maxLength={4}
              placeholder="123"
            />
          </FormField>

          <FormField label="Amount" htmlFor="amount">
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">
                €
              </span>

              <Input
                id="amount"
                type="number"
                min="0.01"
                step="0.01"
                value={amount}
                onChange={(event) => setAmount(event.target.value)}
                placeholder="0.00"
                className="pl-8"
              />
            </div>
          </FormField>
        </div>

        <FormField label="Description" htmlFor="description">
          <Input
            id="description"
            type="text"
            value={description}
            onChange={(event) => setDescription(event.target.value)}
            placeholder="Payment description"
          />
        </FormField>

        {errorMessage && <Alert type="error" message={errorMessage} />}

        {paymentMessage && <Alert type="success" message={paymentMessage} />}

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
