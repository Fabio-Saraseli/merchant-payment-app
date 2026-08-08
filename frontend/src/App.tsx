import { Navigate, Route, Routes } from "react-router-dom";
import ProtectedRoute from "./components/ProtectedRoute";
import PublicRoute from "./components/PublicRoute";
import MerchantLayout from "./layouts/MerchantLayout";
import LoginPage from "./pages/LoginPage";
import PaymentsPage from "./pages/PaymentsPage";
import TransactionsPage from "./pages/TransactionsPage";

function App() {
  return (
    <Routes>
      <Route
        path="/login"
        element={
          <PublicRoute>
            <LoginPage />
          </PublicRoute>
        }
      />

      <Route
        element={
          <ProtectedRoute>
            <MerchantLayout />
          </ProtectedRoute>
        }
      >
        <Route path="/payments" element={<PaymentsPage />} />
        <Route path="/transactions" element={<TransactionsPage />} />
      </Route>

      <Route path="/dashboard" element={<Navigate to="/payments" replace />} />
      <Route path="/" element={<Navigate to="/payments" replace />} />

      <Route path="*" element={<Navigate to="/payments" replace />} />
    </Routes>
  );
}

export default App;
