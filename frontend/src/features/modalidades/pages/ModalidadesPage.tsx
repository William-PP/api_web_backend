import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { Pencil, Plus, Trash2 } from 'lucide-react'
import { getErrorMessage } from '../../../shared/api/errors'
import { Button } from '../../../shared/components/Button'
import { Card } from '../../../shared/components/Card'
import { Modal } from '../../../shared/components/Modal'
import { StateMessage } from '../../../shared/components/StateMessage'
import {
  deleteModalidad,
  getModalidades,
} from '../api/modalidades.api'
import type { Modalidad } from '../api/modalidades.api'
import { ModalidadForm } from '../components/ModalidadForm'

export function ModalidadesPage() {
  const queryClient = useQueryClient()
  const [search, setSearch] = useState('')
  const [editing, setEditing] = useState<Modalidad | null>(null)
  const [isCreating, setIsCreating] = useState(false)
  const [actionError, setActionError] = useState<string | null>(null)

  const modalidadesQuery = useQuery({
    queryKey: ['modalidades'],
    queryFn: getModalidades,
  })

  const deleteMutation = useMutation({
    mutationFn: deleteModalidad,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['modalidades'] })
      queryClient.invalidateQueries({ queryKey: ['dashboard', 'resumen'] })
      setActionError(null)
    },
    onError: (error) => setActionError(getErrorMessage(error)),
  })

  const modalidades = modalidadesQuery.data ?? []
  const filtered = modalidades.filter((modalidad) =>
    `${modalidad.nombre} ${modalidad.descripcion ?? ''}`
      .toLowerCase()
      .includes(search.toLowerCase()),
  )

  function closeForm() {
    setEditing(null)
    setIsCreating(false)
  }

  function handleDelete(modalidad: Modalidad) {
    if (
      window.confirm(`¿Eliminar la modalidad "${modalidad.nombre}"?`)
    ) {
      deleteMutation.mutate(modalidad.id)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Modalidades</h1>
          <p className="mt-1 text-sm text-slate-600">
            Listado y gestión de modalidades.
          </p>
        </div>
        <Button onClick={() => setIsCreating(true)}>
          <Plus className="mr-1 inline size-4" />
          Nueva modalidad
        </Button>
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
          placeholder="Buscar modalidad..."
          className="mb-4 w-full max-w-sm rounded border border-slate-300 px-3 py-2 outline-none focus:border-slate-900"
        />

        {modalidadesQuery.isPending && (
          <StateMessage kind="loading" message="Cargando modalidades..." />
        )}
        {modalidadesQuery.isError && (
          <StateMessage
            kind="error"
            message={getErrorMessage(modalidadesQuery.error)}
          />
        )}
        {!modalidadesQuery.isPending &&
          !modalidadesQuery.isError &&
          filtered.length === 0 && (
            <StateMessage message="No se encontraron modalidades." />
          )}

        {filtered.length > 0 && (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="border-b border-slate-200 text-xs uppercase text-slate-500">
                <tr>
                  <th className="py-2 pr-4">Nombre</th>
                  <th className="py-2 pr-4">Descripción</th>
                  <th className="py-2 pr-4 text-right">Deportistas</th>
                  <th className="py-2 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filtered.map((modalidad) => (
                  <tr key={modalidad.id}>
                    <td className="py-3 pr-4 font-medium">{modalidad.nombre}</td>
                    <td className="py-3 pr-4 text-slate-600">
                      {modalidad.descripcion ?? '—'}
                    </td>
                    <td className="py-3 pr-4 text-right">
                      {modalidad.deportistas_count}
                    </td>
                    <td className="py-3 text-right">
                      <button
                        type="button"
                        onClick={() => setEditing(modalidad)}
                        aria-label={`Editar ${modalidad.nombre}`}
                        className="inline-flex rounded p-2 text-slate-600 hover:bg-slate-100"
                      >
                        <Pencil className="size-4" />
                      </button>
                      <button
                        type="button"
                        onClick={() => handleDelete(modalidad)}
                        disabled={deleteMutation.isPending}
                        aria-label={`Eliminar ${modalidad.nombre}`}
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
          title={editing ? 'Editar modalidad' : 'Nueva modalidad'}
          onClose={closeForm}
        >
          <ModalidadForm
            modalidad={editing ?? undefined}
            onSuccess={closeForm}
            onCancel={closeForm}
          />
        </Modal>
      )}
    </div>
  )
}
