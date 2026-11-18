import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useState } from 'react'
import { useAuth } from '../../features/auth'

const navigation: { to: string; label: string; end?: boolean }[] = [
  { to: '/app', label: 'Dashboard', end: true },
  { to: '/app/deportistas', label: 'Deportistas' },
  { to: '/app/modalidades', label: 'Modalidades' },
  { to: '/app/perfil', label: 'Perfil' },
]

export function AppLayout() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [isLoggingOut, setIsLoggingOut] = useState(false)

  async function handleLogout() {
    setIsLoggingOut(true)

    try {
      await logout()
      navigate('/login', { replace: true })
    } finally {
      setIsLoggingOut(false)
    }
  }

  return (
    <div className="min-h-screen bg-slate-100 text-slate-900">
      <aside className="fixed inset-y-0 left-0 w-56 border-r border-slate-200 bg-white p-4">
        <p className="mb-6 text-lg font-bold">Gestión Deportiva</p>
        <nav className="space-y-1">
          {navigation.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              end={item.end}
              className={({ isActive }) =>
                `block rounded px-3 py-2 text-sm ${
                  isActive
                    ? 'bg-slate-900 text-white'
                    : 'text-slate-700 hover:bg-slate-100'
                }`
              }
            >
              {item.label}
            </NavLink>
          ))}
        </nav>
        {user && (
          <div className="mt-6 border-t border-slate-200 pt-4">
            <p className="text-sm font-medium">{user.name}</p>
            <p className="truncate text-xs text-slate-500">{user.email}</p>
          </div>
        )}
        <button
          type="button"
          onClick={handleLogout}
          disabled={isLoggingOut}
          className="mt-4 w-full rounded bg-red-50 px-3 py-2 text-left text-sm text-red-700 hover:bg-red-100 disabled:opacity-60"
        >
          {isLoggingOut ? 'Cerrando sesión...' : 'Cerrar sesión'}
        </button>
      </aside>
      <main className="ml-56 p-8">
        <Outlet />
      </main>
    </div>
  )
}
