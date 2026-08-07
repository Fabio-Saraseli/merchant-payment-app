import { useNavigate } from "react-router-dom";
import PaymentForm from "../components/PaymentForm";

function DashboardPage() {
  const navigate = useNavigate();
  const storedMerchant = localStorage.getItem("merchant");
  const merchant = storedMerchant ? JSON.parse(storedMerchant) : null;

  const handleLogout = () => {
    localStorage.removeItem("merchant_token");
    localStorage.removeItem("merchant");
    navigate("/login");
  };

  return (
     <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
          <div className="text-left">
            <p className="text-sm font-medium text-slate-500">
              Merchant Portal
            </p>

            <h1 className="text-xl font-semibold text-slate-900 sm:text-2xl">
              {merchant?.name || 'Dashboard'}
            </h1>

            {merchant?.email && (
              <p className="mt-1 text-sm text-slate-500">
                {merchant.email}
              </p>
            )}
          </div>

          <button
            type="button"
            onClick={handleLogout}
            className="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 sm:w-auto"
          >
            Logout
          </button>
        </div>
      </header>

      <div className="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <PaymentForm />
      </div>
    </main>
  );
}

export default DashboardPage;
