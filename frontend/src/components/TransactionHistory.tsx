import { useTransactions } from "../hooks/useTransactions";
import Alert from "./ui/Alert";
import Button from "./ui/Button";
import FormField from "./ui/FormField";
import Input from "./ui/Input";
import SectionHeader from "./ui/SectionHeader";

const formatTransactionDate = (createdAt: string) => {
  return new Date(createdAt).toLocaleString();
};

function TransactionHistory() {
  const {
    transactions,
    fromDate,
    setFromDate,
    toDate,
    setToDate,
    errorMessage,
    isLoading,
    handleFilter,
    handleClearFilters,
  } = useTransactions();

  return (
    <section>
      <SectionHeader
        title="Transaction History"
        description="Review and filter previous payment transactions."
      />

      <form
        onSubmit={handleFilter}
        className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto_auto]"
      >
        <FormField label="From" htmlFor="fromDate">
          <Input
            id="fromDate"
            type="date"
            value={fromDate}
            onChange={(event) => setFromDate(event.target.value)}
          />
        </FormField>

        <FormField label="To" htmlFor="toDate">
          <Input
            id="toDate"
            type="date"
            value={toDate}
            onChange={(event) => setToDate(event.target.value)}
          />
        </FormField>

        <Button
          type="submit"
          disabled={isLoading}
          className="w-full lg:self-end"
        >
          Filter
        </Button>

        <Button
          type="button"
          variant="secondary"
          onClick={handleClearFilters}
          disabled={isLoading}
          className="w-full lg:self-end"
        >
          Clear
        </Button>
      </form>

      {errorMessage && (
        <div className="mt-5">
          <Alert type="error" message={errorMessage} />
        </div>
      )}

      <div className="mt-6 overflow-x-auto">
        <table className="w-full min-w-187.5 text-left text-sm">
          <thead className="border-b border-slate-200 text-slate-500">
            <tr>
              <th className="px-3 py-3 font-medium">Date</th>
              <th className="px-3 py-3 font-medium">Description</th>
              <th className="px-3 py-3 font-medium">Card</th>
              <th className="px-3 py-3 font-medium">Amount</th>
              <th className="px-3 py-3 font-medium">Status</th>
            </tr>
          </thead>

          <tbody className="divide-y divide-slate-100">
            {transactions.map((transaction) => (
              <tr key={transaction.id}>
                <td className="whitespace-nowrap px-3 py-4 text-slate-600">
                  {formatTransactionDate(transaction.created_at)}
                </td>

                <td className="px-3 py-4 text-slate-900">
                  {transaction.description}
                </td>

                <td className="whitespace-nowrap px-3 py-4 text-slate-600">
                  •••• {transaction.card_last_four}
                </td>

                <td className="whitespace-nowrap px-3 py-4 font-medium text-slate-900">
                  €{(transaction.amount_cents / 100).toFixed(2)}
                </td>

                <td className="whitespace-nowrap px-3 py-4 capitalize text-slate-600">
                  {transaction.status}
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {!isLoading && transactions.length === 0 && (
          <p className="py-8 text-center text-sm text-slate-500">
            No transactions found.
          </p>
        )}

        {isLoading && (
          <p className="py-8 text-center text-sm text-slate-500">
            Loading transactions...
          </p>
        )}
      </div>
    </section>
  );
}

export default TransactionHistory;