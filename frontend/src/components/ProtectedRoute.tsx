import { Navigate } from "react-router-dom";
import { clearSession, isSessionExpired } from "../helpers/authSession";

type ProtectedRouteProps = {
  children: React.ReactNode;
};

function ProtectedRoute({ children }: ProtectedRouteProps) {
  const token = localStorage.getItem("merchant_token");

  if (!token || isSessionExpired()) {
    clearSession();

    return <Navigate to="/login" replace />;
  }

  return children;
}

export default ProtectedRoute;
