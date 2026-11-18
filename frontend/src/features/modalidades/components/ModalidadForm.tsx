import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { getErrorMessage } from '../../../shared/api/errors'
import { Button } from '../../../shared/components/Button'
import { Input } from '../../../shared/components/Input'
import {
  createModalidad,
  updateModalidad,
} from '../api/modalidades.api'
import type { Modalidad } from '../api/modalidades.api'
import { modalidadSchema } from '../schemas/modalidad.schema'
import type { ModalidadFormValues } from '../schemas/modalidad.schema'

type ModalidadFormProps = {
  modalidad?: Modalidad
  onSuccess: () => void
  onCancel: () => void
}

export function ModalidadForm({
  modalidad,
  onSuccess,
  onCancel,
}: ModalidadFormProps) {
  const queryClient = useQueryClient()
  const [serverError, setServerError] = useState<string | null>(null)

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<ModalidadFormValues>({
    resolver: zodResolver(modalidadSchema),
    defaultValues: {
      nombre: modalidad?.nombre ?? '',
      descripcion: modalidad?.descripcion ?? '',
    },
  })

  const mutation = useMutation({
    mutationFn: (values: ModalidadFormValues) =>
      modalidad
        ? updateModalidad(modalidad.id, values)
        : createModalidad(values),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['modalidades'] })
      queryClient.invalidateQueries({ queryKey: ['dashboard', 'resumen'] })
      onSuccess()
    },
    onError: (error) => setServerError(getErrorMessage(error)),
  })

  return (
    <form
      onSubmit={handleSubmit((values) => {
        setServerError(null)
        mutation.mutate(values)
      })}
      className="space-y-4"
    >
      <Input
        label="Nombre"
        placeholder="Ej. Fútbol"
        error={errors.nombre?.message}
        {...register('nombre')}
      />

      <div>
        <label
          htmlFor="descripcion"
          className="mb-1 block text-sm font-medium text-slate-700"
        >
          Descripción
        </label>
        <textarea
          id="descripcion"
          rows={3}
          className="w-full rounded border border-slate-300 px-3 py-2 outline-none focus:border-slate-900"
          placeholder="Descripción opcional"
          {...register('descripcion')}
        />
        {errors.descripcion?.message && (
          <p className="mt-1 text-sm text-red-600">
            {errors.descripcion.message}
          </p>
        )}
      </div>

      {serverError && (
        <p className="rounded bg-red-50 px-3 py-2 text-sm text-red-700">
          {serverError}
        </p>
      )}

      <div className="flex justify-end gap-2 pt-2">
        <Button type="button" onClick={onCancel} className="bg-slate-200 text-slate-800 hover:bg-slate-300">
          Cancelar
        </Button>
        <Button type="submit" disabled={isSubmitting || mutation.isPending}>
          {isSubmitting || mutation.isPending
            ? 'Guardando...'
            : modalidad
              ? 'Actualizar'
              : 'Crear'}
        </Button>
      </div>
    </form>
  )
}
