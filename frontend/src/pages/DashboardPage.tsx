function DashboardPage() {
  return (
    <main className="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
      <div className="mx-auto w-full max-w-7xl">
        <header className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
          <p className="text-sm font-medium text-slate-500">Merchant Portal</p>

          <h1 className="mt-1 text-2xl font-semibold text-slate-900 sm:text-3xl">
            Dashboard
          </h1>

          <p className="mt-2 text-sm text-slate-500 sm:text-base">
            Manage your payments and transactions.
          </p>
        </header>
      </div>
    </main>
  );
}

export default DashboardPage;
