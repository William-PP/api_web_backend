import { Link } from 'react-router-dom'
import { LoginForm } from '../components/LoginForm'

export function LoginPage() {
  return (
    <main className="mx-auto max-w-md px-6 py-16">
      <h1 className="mb-6 text-2xl font-bold">Iniciar sesión</h1>
      <LoginForm />
      <div className="mt-4 flex items-center justify-between text-sm text-slate-600">
        <p>
          ¿No tienes cuenta?{' '}
          <Link to="/registro" className="font-medium text-slate-900">
            Crear cuenta
          </Link>
        </p>
        <Link to="/olvide-contrasena" className="font-medium text-slate-900">
          ¿Olvidaste tu contraseña?
        </Link>
      </div>
    </main>
  )
}
