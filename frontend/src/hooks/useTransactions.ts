import { useEffect, useState } from "react";
import { getTransactions } from "../api/transactions";
import type { Transaction } from "../api/payments";

export const useTransactions = () => {
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");
  const [errorMessage, setErrorMessage] = useState("");
  const [isLoading, setIsLoading] = useState(true);

  const loadTransactions = async (from = fromDate, to = toDate) => {
    setIsLoading(true);
    setErrorMessage("");

    const result = await getTransactions(from, to);

    setIsLoading(false);

    if (!result.success) {
      setErrorMessage(result.message);
      return;
    }

    setTransactions(result.transactions);
  };

  useEffect(() => {
    let isMounted = true;

    getTransactions("", "").then((result) => {
      if (!isMounted) {
        return;
      }

      setIsLoading(false);

      if (!result.success) {
        setErrorMessage(result.message);
        return;
      }

      setTransactions(result.transactions);
    });

    return () => {
      isMounted = false;
    };
  }, []);

  const handleFilter = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (fromDate && toDate && fromDate > toDate) {
      setErrorMessage("From date cannot be after to date");
      return;
    }

    await loadTransactions();
  };

  const handleClearFilters = async () => {
    setFromDate("");
    setToDate("");

    await loadTransactions("", "");
  };

  return {
    transactions,
    fromDate,
    setFromDate,
    toDate,
    setToDate,
    errorMessage,
    isLoading,
    handleFilter,
    handleClearFilters,
  };
};
