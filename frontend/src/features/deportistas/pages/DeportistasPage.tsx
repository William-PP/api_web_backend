import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { Download, Pencil, Plus, Trash2 } from 'lucide-react'
import { getErrorMessage } from '../../../shared/api/errors'
import { Button } from '../../../shared/components/Button'
import { Card } from '../../../shared/components/Card'
import { Modal } from '../../../shared/components/Modal'
import { StateMessage } from '../../../shared/components/StateMessage'
import { getModalidades } from '../../modalidades/api/modalidades.api'
import {
  deleteDeportista,
  getDeportistas,
} from '../api/deportistas.api'
import type { Deportista } from '../api/deportistas.api'
import { downloadDeportistasPdf } from '../api/reportes.api'
import { DeportistaForm } from '../components/DeportistaForm'

export function DeportistasPage() {
  const queryClient = useQueryClient()
  const [search, setSearch] = useState('')
  const [editing, setEditing] = useState<Deportista | null>(null)
  const [isCreating, setIsCreating] = useState(false)
  const [actionError, setActionError] = useState<string | null>(null)

  const deportistasQuery = useQuery({
    queryKey: ['deportistas'],
    queryFn: getDeportistas,
  })

  const modalidadesQuery = useQuery({
    queryKey: ['modalidades'],
    queryFn: getModalidades,
  })

  const deleteMutation = useMutation({
    mutationFn: deleteDeportista,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['deportistas'] })
      queryClient.invalidateQueries({ queryKey: ['dashboard', 'resumen'] })
      queryClient.invalidateQueries({ queryKey: ['modalidades'] })
      setActionError(null)
    },
    onError: (error) => setActionError(getErrorMessage(error)),
  })

  const pdfMutation = useMutation({
    mutationFn: downloadDeportistasPdf,
    onError: (error) => setActionError(getErrorMessage(error)),
  })

  const deportistas = deportistasQuery.data ?? []
  const filtered = deportistas.filter((deportista) =>
    `${deportista.nombre_completo} ${deportista.modalidad?.nombre ?? ''}`
      .toLowerCase()
      .includes(search.toLowerCase()),
  )

  function closeForm() {
    setEditing(null)
    setIsCreating(false)
  }

  function handleDelete(deportista: Deportista) {
    if (window.confirm(`¿Eliminar a ${deportista.nombre_completo}?`)) {
      deleteMutation.mutate(deportista.id)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Deportistas</h1>
          <p className="mt-1 text-sm text-slate-600">
            Listado y gestión de deportistas.
          </p>
        </div>
        <div className="flex gap-2">
          <Button
            onClick={() => pdfMutation.mutate()}
            disabled={pdfMutation.isPending}
            className="bg-slate-200 text-slate-800 hover:bg-slate-300"
          >
            <Download className="mr-1 inline size-4" />
            {pdfMutation.isPending ? 'Generando...' : 'Exportar PDF'}
          </Button>
          <Button onClick={() => setIsCreating(true)}>
            <Plus className="mr-1 inline size-4" />
            Nuevo deportista
          </Button>
        </div>
      </div>

      {actionError && (
        <p className="rounded bg-red-50 px-3 py-2 text-sm text-red-700">
          {actionError}
        </p>
      )}

      <Card>
        <input
          type="search"
          value={search}
          onChange={(event) => setSearch(event.target.value)}
          placeholder="Buscar por nombre o modalidad..."
          className="mb-4 w-full max-w-sm rounded border border-slate-300 px-3 py-2 outline-none focus:border-slate-900"
        />

        {deportistasQuery.isPending && (
          <StateMessage kind="loading" message="Cargando deportistas..." />
        )}
        {deportistasQuery.isError && (
          <StateMessage
            kind="error"
            message={getErrorMessage(deportistasQuery.error)}
          />
        )}
        {!deportistasQuery.isPending &&
          !deportistasQuery.isError &&
          filtered.length === 0 && (
            <StateMessage message="No se encontraron deportistas." />
          )}

        {filtered.length > 0 && (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="border-b border-slate-200 text-xs uppercase text-slate-500">
                <tr>
                  <th className="py-2 pr-4">Deportista</th>
                  <th className="py-2 pr-4">Nacimiento</th>
                  <th className="py-2 pr-4">Modalidad</th>
                  <th className="py-2 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filtered.map((deportista) => (
                  <tr key={deportista.id}>
                    <td className="py-3 pr-4">
                      <div className="flex items-center gap-3">
                        {deportista.foto_url ? (
                          <img
                            src={deportista.foto_url}
                            alt=""
                            className="size-9 rounded-full object-cover"
                          />
                        ) : (
                          <span className="flex size-9 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-500">
                            {deportista.nombre.charAt(0)}
                            {deportista.apellido.charAt(0)}
                          </span>
                        )}
                        <span className="font-medium">
                          {deportista.nombre_completo}
                        </span>
                      </div>
                    </td>
                    <td className="py-3 pr-4 text-slate-600">
                      {deportista.fecha_nacimiento}
                    </td>
                    <td className="py-3 pr-4 text-slate-600">
                      {deportista.modalidad?.nombre ?? '—'}
                    </td>
                    <td className="py-3 text-right">
                      <button
                        type="button"
                        onClick={() => setEditing(deportista)}
                        aria-label={`Editar ${deportista.nombre_completo}`}
                        className="inline-flex rounded p-2 text-slate-600 hover:bg-slate-100"
                      >
                        <Pencil className="size-4" />
                      </button>
                      <button
                        type="button"
                        onClick={() => handleDelete(deportista)}
                        disabled={deleteMutation.isPending}
                        aria-label={`Eliminar ${deportista.nombre_completo}`}
                        className="inline-flex rounded p-2 text-red-600 hover:bg-red-50 disabled:opacity-50"
                      >
                        <Trash2 className="size-4" />
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Card>

      {(isCreating || editing) && (
        <Modal
          title={editing ? 'Editar deportista' : 'Nuevo deportista'}
          onClose={closeForm}
        >
          <DeportistaForm
            deportista={editing ?? undefined}
            modalidades={modalidadesQuery.data ?? []}
            onSuccess={closeForm}
            onCancel={closeForm}
          />
        </Modal>
      )}
    </div>
  )
}
