import { useQuery } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import { getErrorMessage } from '../../../shared/api/errors'
import { StateMessage } from '../../../shared/components/StateMessage'
import { getDeportistasPublicos } from '../api/public.api'

export function LandingPage() {
  const deportistasQuery = useQuery({
    queryKey: ['public', 'deportistas'],
    queryFn: getDeportistasPublicos,
  })

  return (
    <main className="mx-auto max-w-5xl px-6 py-16">
      <section className="rounded-lg bg-slate-900 p-10 text-white">
        <h1 className="text-4xl font-bold">Gestión Deportiva</h1>
        <p className="mt-3 max-w-xl text-slate-300">
          Administra deportistas, modalidades y reportes del club desde un
          solo lugar.
        </p>
        <Link
          to="/login"
          className="mt-6 inline-block rounded bg-white px-5 py-2 font-medium text-slate-900"
        >
          Iniciar sesión
        </Link>
      </section>

      <section className="mt-12">
        <h2 className="text-2xl font-bold">Nuestros deportistas</h2>
        <p className="mt-1 text-sm text-slate-600">Listado público.</p>

        {deportistasQuery.isPending && (
          <StateMessage kind="loading" message="Cargando deportistas..." />
        )}
        {deportistasQuery.isError && (
          <StateMessage
            kind="error"
            message={getErrorMessage(deportistasQuery.error)}
          />
        )}

        {deportistasQuery.data && (
          <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {deportistasQuery.data.map((deportista) => (
              <article
                key={deportista.id}
                className="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4"
              >
                {deportista.foto_url ? (
                  <img
                    src={deportista.foto_url}
                    alt=""
                    className="size-12 rounded-full object-cover"
                  />
                ) : (
                  <span className="flex size-12 items-center justify-center rounded-full bg-slate-100 text-sm text-slate-500">
                    {deportista.nombre.charAt(0)}
                    {deportista.apellido.charAt(0)}
                  </span>
                )}
                <div>
                  <p className="font-medium">{deportista.nombre_completo}</p>
                  <p className="text-xs text-slate-500">
                    {deportista.modalidad ?? 'Sin modalidad'}
                  </p>
                </div>
              </article>
            ))}
          </div>
        )}
      </section>
    </main>
  )
}
