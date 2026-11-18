import { Link } from 'react-router-dom'
import { ForgotPasswordForm } from '../components/ForgotPasswordForm'

export function ForgotPasswordPage() {
  return (
    <main className="mx-auto max-w-md px-6 py-16">
      <h1 className="text-2xl font-bold">Recuperar contraseña</h1>
      <p className="mt-2 text-sm text-slate-600">
        Ingresa tu correo y te enviaremos un enlace para restablecerla.
      </p>
      <div className="mt-6">
        <ForgotPasswordForm />
      </div>
      <p className="mt-4 text-sm text-slate-600">
        <Link to="/login" className="font-medium text-slate-900">
          Volver al inicio de sesión
        </Link>
      </p>
    </main>
  )
}
