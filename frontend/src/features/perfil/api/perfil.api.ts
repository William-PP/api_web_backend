import { apiClient } from '../../../shared/api/client'
import type { User } from '../../auth/api/auth.api'

export async function getPerfil(): Promise<User> {
  const { data } = await apiClient.get<{ data: User }>('/perfil')
  return data.data
}

export async function updatePerfil(payload: {
  name: string
  email: string
}): Promise<User> {
  const { data } = await apiClient.put<{ data: User }>('/perfil', payload)
  return data.data
}

export async function updateFoto(foto: File): Promise<User> {
  const formData = new FormData()
  formData.append('foto', foto)

  const { data } = await apiClient.post<{ data: User }>(
    '/perfil/foto',
    formData,
  )
  return data.data
}
