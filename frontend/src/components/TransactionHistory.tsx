import { useTransactions } from "../hooks/useTransactions";

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
    <section className="rounded-xl border mt-5 border-slate-200 bg-white p-5 shadow-sm sm:p-6">
      <div className="text-left">
        <h2 className="text-lg font-semibold text-slate-900 sm:text-xl">
          Transaction History
        </h2>

        <p className="mt-1 text-sm text-slate-500">
          Review and filter previous payment transactions.
        </p>
      </div>

      <form
        onSubmit={handleFilter}
        className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto_auto]"
      >
        <div className="text-left">
          <label
            htmlFor="fromDate"
            className="mb-2 block text-sm font-medium text-slate-700"
          >
            From
          </label>

          <input
            id="fromDate"
            type="date"
            value={fromDate}
            onChange={(event) => setFromDate(event.target.value)}
            className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-500"
          />
        </div>

        <div className="text-left">
          <label
            htmlFor="toDate"
            className="mb-2 block text-sm font-medium text-slate-700"
          >
            To
          </label>

          <input
            id="toDate"
            type="date"
            value={toDate}
            onChange={(event) => setToDate(event.target.value)}
            className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-500"
          />
        </div>

        <button
          type="submit"
          disabled={isLoading}
          className="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800 disabled:opacity-60 lg:self-end"
        >
          Filter
        </button>

        <button
          type="button"
          onClick={handleClearFilters}
          disabled={isLoading}
          className="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:opacity-60 lg:self-end"
        >
          Clear
        </button>
      </form>

      {errorMessage && (
        <div className="mt-5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-left text-sm text-red-700">
          {errorMessage}
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
                  {transaction.created_at}
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
