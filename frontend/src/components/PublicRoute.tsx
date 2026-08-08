import { Navigate } from "react-router-dom";
import { clearSession, isSessionExpired } from "../helpers/authSession";

type PublicRouteProps = {
  children: React.ReactNode;
};

function PublicRoute({ children }: PublicRouteProps) {
  const token = localStorage.getItem("merchant_token");

  if (token && !isSessionExpired()) {
    return <Navigate to="/dashboard" replace />;
  }

  if (token && isSessionExpired()) {
    clearSession();
  }

  return children;
}

export default PublicRoute;
