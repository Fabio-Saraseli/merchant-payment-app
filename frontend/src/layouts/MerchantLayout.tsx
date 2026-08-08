import { NavLink, Outlet, useNavigate } from "react-router-dom";
import { clearSession } from "../helpers/authSession";

function MerchantLayout() {
  const navigate = useNavigate();

  const storedMerchant = localStorage.getItem("merchant");
  const merchant = storedMerchant ? JSON.parse(storedMerchant) : null;

  const handleLogout = () => {
    clearSession();
    navigate("/login");
  };

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto w-full max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="text-left">
              <p className="text-sm font-medium text-slate-500">
                Merchant Portal
              </p>

              <h1 className="text-xl font-semibold text-slate-900 sm:text-2xl">
                {merchant?.name || "Dashboard"}
              </h1>

              {merchant?.email && (
                <p className="mt-1 text-sm text-slate-500">{merchant.email}</p>
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

          <nav className="mt-5 flex gap-2 overflow-x-auto">
            <NavLink
              to="/payments"
              className={({ isActive }) =>
                `whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition ${
                  isActive
                    ? "bg-slate-900 text-white"
                    : "text-slate-600 hover:bg-slate-100"
                }`
              }
            >
              Payments
            </NavLink>

            <NavLink
              to="/transactions"
              className={({ isActive }) =>
                `whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition ${
                  isActive
                    ? "bg-slate-900 text-white"
                    : "text-slate-600 hover:bg-slate-100"
                }`
              }
            >
              Transactions
            </NavLink>
          </nav>
        </div>
      </header>

      <div className="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <Outlet />
      </div>
    </main>
  );
}

export default MerchantLayout;
