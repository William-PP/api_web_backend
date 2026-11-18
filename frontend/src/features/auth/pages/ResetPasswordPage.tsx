import { Link, useSearchParams } from 'react-router-dom'
import { ResetPasswordForm } from '../components/ResetPasswordForm'

export function ResetPasswordPage() {
  const [searchParams] = useSearchParams()
  const token = searchParams.get('token') ?? ''
  const email = searchParams.get('email') ?? ''

  return (
    <main className="mx-auto max-w-md px-6 py-16">
      <h1 className="text-2xl font-bold">Restablecer contraseña</h1>

      {token && email ? (
        <>
          <p className="mt-2 text-sm text-slate-600">
            Elige una nueva contraseña para tu cuenta.
          </p>
          <div className="mt-6">
            <ResetPasswordForm token={token} email={email} />
          </div>
        </>
      ) : (
        <p className="mt-4 rounded bg-red-50 px-3 py-2 text-sm text-red-700">
          El enlace de restablecimiento no es válido. Solicita uno nuevo desde{' '}
          <Link to="/olvide-contrasena" className="font-medium underline">
            recuperar contraseña
          </Link>
          .
        </p>
      )}
    </main>
  )
}
