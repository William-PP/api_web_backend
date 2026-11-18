import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useEffect, useState } from 'react'
import { useForm } from 'react-hook-form'
import { getErrorMessage } from '../../../shared/api/errors'
import { Button } from '../../../shared/components/Button'
import { Card } from '../../../shared/components/Card'
import { Input } from '../../../shared/components/Input'
import { StateMessage } from '../../../shared/components/StateMessage'
import { useAuth } from '../../auth'
import {
  getPerfil,
  updateFoto,
  updatePerfil,
} from '../api/perfil.api'
import { perfilSchema } from '../schemas/perfil.schema'
import type { PerfilFormValues } from '../schemas/perfil.schema'

export function PerfilPage() {
  const { updateUser } = useAuth()
  const queryClient = useQueryClient()
  const [success, setSuccess] = useState<string | null>(null)
  const [photoError, setPhotoError] = useState<string | null>(null)

  const perfilQuery = useQuery({
    queryKey: ['perfil'],
    queryFn: getPerfil,
  })

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors, isSubmitting },
  } = useForm<PerfilFormValues>({
    resolver: zodResolver(perfilSchema),
    defaultValues: { name: '', email: '' },
  })

  useEffect(() => {
    if (perfilQuery.data) {
      reset({
        name: perfilQuery.data.name,
        email: perfilQuery.data.email,
      })
    }
  }, [perfilQuery.data, reset])

  const perfilMutation = useMutation({
    mutationFn: updatePerfil,
    onSuccess: (user) => {
      queryClient.setQueryData(['perfil'], user)
      updateUser(user)
      setSuccess('Datos actualizados correctamente.')
    },
  })

  const fotoMutation = useMutation({
    mutationFn: updateFoto,
    onSuccess: (user) => {
      queryClient.setQueryData(['perfil'], user)
      updateUser(user)
      setPhotoError(null)
      setSuccess('Foto actualizada correctamente.')
    },
    onError: (error) => setPhotoError(getErrorMessage(error)),
  })

  if (perfilQuery.isPending) {
    return <StateMessage kind="loading" message="Cargando perfil..." />
  }

  if (perfilQuery.isError) {
    return (
      <StateMessage kind="error" message={getErrorMessage(perfilQuery.error)} />
    )
  }

  const perfil = perfilQuery.data

  if (!perfil) {
    return <StateMessage kind="error" message="No se pudo cargar el perfil." />
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Perfil</h1>
        <p className="mt-1 text-sm text-slate-600">
          Datos y foto del usuario.
        </p>
      </div>

      {success && (
        <p className="rounded bg-green-50 px-3 py-2 text-sm text-green-700">
          {success}
        </p>
      )}

      <div className="grid gap-6 lg:grid-cols-3">
        <Card title="Foto" className="text-center">
          {perfil.foto_url ? (
            <img
              src={perfil.foto_url}
              alt="Foto de perfil"
              className="mx-auto size-32 rounded-full object-cover"
            />
          ) : (
            <div className="mx-auto flex size-32 items-center justify-center rounded-full bg-slate-100 text-4xl text-slate-400">
              {perfil.name.charAt(0).toUpperCase()}
            </div>
          )}

          <input
            id="foto-perfil"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            onChange={(event) => {
              const file = event.target.files?.[0]
              if (file) fotoMutation.mutate(file)
            }}
            className="mt-4 block w-full text-xs text-slate-600 file:mr-2 file:rounded file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-white"
          />
          {fotoMutation.isPending && (
            <p className="mt-2 text-sm text-slate-500">Subiendo foto...</p>
          )}
          {photoError && <p className="mt-2 text-sm text-red-600">{photoError}</p>}
          <p className="mt-2 text-xs text-slate-500">Máximo 2 MB (jpeg, png o webp).</p>
        </Card>

        <Card title="Datos personales" className="lg:col-span-2">
          <form
            onSubmit={handleSubmit((values) => perfilMutation.mutate(values))}
            className="space-y-4"
          >
            <Input
              label="Nombre"
              autoComplete="name"
              error={errors.name?.message}
              {...register('name')}
            />
            <Input
              label="Correo electrónico"
              type="email"
              autoComplete="email"
              error={errors.email?.message}
              {...register('email')}
            />

            {perfilMutation.isError && (
              <p className="rounded bg-red-50 px-3 py-2 text-sm text-red-700">
                {getErrorMessage(perfilMutation.error)}
              </p>
            )}

            <Button
              type="submit"
              disabled={isSubmitting || perfilMutation.isPending}
            >
              {isSubmitting || perfilMutation.isPending
                ? 'Guardando...'
                : 'Guardar cambios'}
            </Button>
          </form>

          <p className="mt-6 text-xs text-slate-500">
            Miembro desde {perfil.created_at ?? '—'}
          </p>
        </Card>
      </div>
    </div>
  )
}
