import { Link, isRouteErrorResponse, useRouteError } from 'react-router-dom'

export function ErrorPage() {
  const error = useRouteError()

  let message = 'Ocurrió un error inesperado.'

  if (isRouteErrorResponse(error)) {
    message =
      error.status === 404
        ? 'La página que buscas no existe.'
        : `${error.status} ${error.statusText}`
  } else if (error instanceof Error) {
    message = error.message
  }

  return (
    <main className="flex min-h-screen flex-col items-center justify-center gap-4 px-6 text-center">
      <h1 className="text-3xl font-bold">Algo salió mal</h1>
      <p className="max-w-md text-sm text-slate-600">{message}</p>
      <Link
        to="/"
        className="rounded bg-slate-900 px-4 py-2 text-sm text-white"
      >
        Volver al inicio
      </Link>
    </main>
  )
}
