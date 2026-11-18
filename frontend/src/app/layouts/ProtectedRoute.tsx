import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '../../features/auth'

export function ProtectedRoute() {
  const { token, isChecking } = useAuth()

  if (isChecking) {
    return (
      <div className="flex min-h-screen items-center justify-center text-slate-600">
        Verificando sesión...
      </div>
    )
  }

  return token ? <Outlet /> : <Navigate to="/login" replace />
}
