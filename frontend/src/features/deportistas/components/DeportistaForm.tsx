import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { getErrorMessage } from '../../../shared/api/errors'
import { Button } from '../../../shared/components/Button'
import { Input } from '../../../shared/components/Input'
import { Select } from '../../../shared/components/Select'
import type { Modalidad } from '../../modalidades/api/modalidades.api'
import {
  createDeportista,
  updateDeportista,
} from '../api/deportistas.api'
import type { Deportista } from '../api/deportistas.api'
import { deportistaSchema } from '../schemas/deportista.schema'
import type { DeportistaFormValues } from '../schemas/deportista.schema'

type DeportistaFormProps = {
  deportista?: Deportista
  modalidades: Modalidad[]
  onSuccess: () => void
  onCancel: () => void
}

export function DeportistaForm({
  deportista,
  modalidades,
  onSuccess,
  onCancel,
}: DeportistaFormProps) {
  const queryClient = useQueryClient()
  const [serverError, setServerError] = useState<string | null>(null)

  const {
    register,
    handleSubmit,
    setValue,
    formState: { errors, isSubmitting },
  } = useForm<DeportistaFormValues>({
    resolver: zodResolver(deportistaSchema),
    defaultValues: {
      nombre: deportista?.nombre ?? '',
      apellido: deportista?.apellido ?? '',
      fecha_nacimiento: deportista?.fecha_nacimiento ?? '',
      modalidad_id: deportista ? String(deportista.modalidad_id) : '',
      foto: null,
    },
  })

  const mutation = useMutation({
    mutationFn: (values: DeportistaFormValues) => {
      const payload = {
        nombre: values.nombre,
        apellido: values.apellido,
        fecha_nacimiento: values.fecha_nacimiento,
        modalidad_id: Number(values.modalidad_id),
        foto: values.foto,
      }

      return deportista
        ? updateDeportista(deportista.id, payload)
        : createDeportista(payload)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['deportistas'] })
      queryClient.invalidateQueries({ queryKey: ['dashboard', 'resumen'] })
      queryClient.invalidateQueries({ queryKey: ['modalidades'] })
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
      <div className="grid gap-4 sm:grid-cols-2">
        <Input
          label="Nombre"
          placeholder="Ej. Ana"
          error={errors.nombre?.message}
          {...register('nombre')}
        />
        <Input
          label="Apellido"
          placeholder="Ej. García"
          error={errors.apellido?.message}
          {...register('apellido')}
        />
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <Input
          label="Fecha de nacimiento"
          type="date"
          error={errors.fecha_nacimiento?.message}
          {...register('fecha_nacimiento')}
        />
        <Select
          label="Modalidad"
          placeholder="Selecciona una modalidad"
          options={modalidades.map((modalidad) => ({
            value: String(modalidad.id),
            label: modalidad.nombre,
          }))}
          error={errors.modalidad_id?.message}
          {...register('modalidad_id')}
        />
      </div>

      <div>
        <label
          htmlFor="foto"
          className="mb-1 block text-sm font-medium text-slate-700"
        >
          Foto (opcional)
        </label>
        <input
          id="foto"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          onChange={(event) =>
            setValue('foto', event.target.files?.[0] ?? null)
          }
          className="block w-full text-sm text-slate-600 file:mr-3 file:rounded file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-white"
        />
        {errors.foto?.message && (
          <p className="mt-1 text-sm text-red-600">{errors.foto.message}</p>
        )}
        {deportista?.foto_url && (
          <img
            src={deportista.foto_url}
            alt="Foto actual"
            className="mt-3 size-16 rounded object-cover"
          />
        )}
      </div>

      {serverError && (
        <p className="rounded bg-red-50 px-3 py-2 text-sm text-red-700">
          {serverError}
        </p>
      )}

      <div className="flex justify-end gap-2 pt-2">
        <Button
          type="button"
          onClick={onCancel}
          className="bg-slate-200 text-slate-800 hover:bg-slate-300"
        >
          Cancelar
        </Button>
        <Button type="submit" disabled={isSubmitting || mutation.isPending}>
          {isSubmitting || mutation.isPending
            ? 'Guardando...'
            : deportista
              ? 'Actualizar'
              : 'Crear'}
        </Button>
      </div>
    </form>
  )
}
