import { Link } from 'react-router-dom'
import { RegisterForm } from '../components/RegisterForm'

export function RegisterPage() {
  return (
    <main className="mx-auto max-w-md px-6 py-16">
      <h1 className="text-2xl font-bold">Crear cuenta</h1>
      <div className="mt-6">
        <RegisterForm />
      </div>
      <p className="mt-4 text-sm text-slate-600">
        ¿Ya tienes cuenta?{' '}
        <Link to="/login" className="font-medium text-slate-900">
          Iniciar sesión
        </Link>
      </p>
    </main>
  )
}
