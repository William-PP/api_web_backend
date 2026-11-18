import {Link, Outlet} from 'react-router-dom'

export function PublicLayout() {
  return (
    <div className="min-h-screen bg-slate-50 text-slate-900">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
          <Link to="/" className="text-lg font-bold">
            Gestión Deportiva
          </Link>
          <nav className="flex gap-4 text-sm">
            <Link to="/login" className="hover:text-blue-600">
              Iniciar sesión
            </Link>
            <Link to="/registro" className="hover:text-blue-600">
              Crear cuenta
            </Link>
          </nav>
        </div>
      </header>
      <Outlet />
    </div>
  )
}
