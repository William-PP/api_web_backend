import { apiClient } from '../../../shared/api/client'
import type { Modalidad } from '../../modalidades/api/modalidades.api'

export type Deportista = {
  id: number
  nombre: string
  apellido: string
  nombre_completo: string
  fecha_nacimiento: string
  foto: string | null
  foto_url: string | null
  modalidad_id: number
  modalidad: Modalidad | null
  user: {
    id: number
    name: string
    email: string
  } | null
  created_at: string | null
  updated_at: string | null
}

export type DeportistaPayload = {
  nombre: string
  apellido: string
  fecha_nacimiento: string
  modalidad_id: number
  foto?: File | null
}

function toFormData(payload: DeportistaPayload): FormData {
  const formData = new FormData()

  formData.append('nombre', payload.nombre)
  formData.append('apellido', payload.apellido)
  formData.append('fecha_nacimiento', payload.fecha_nacimiento)
  formData.append('modalidad_id', String(payload.modalidad_id))

  if (payload.foto) {
    formData.append('foto', payload.foto)
  }

  return formData
}

export async function getDeportistas(): Promise<Deportista[]> {
  const { data } = await apiClient.get<{ data: Deportista[] }>('/deportistas')
  return data.data
}

export async function createDeportista(
  payload: DeportistaPayload,
): Promise<Deportista> {
  const { data } = await apiClient.post<{ data: Deportista }>(
    '/deportistas',
    toFormData(payload),
  )
  return data.data
}

export async function updateDeportista(
  id: number,
  payload: DeportistaPayload,
): Promise<Deportista> {
  if (payload.foto) {
    const formData = toFormData(payload)
    formData.append('_method', 'PUT')

    const { data } = await apiClient.post<{ data: Deportista }>(
      `/deportistas/${id}`,
      formData,
    )
    return data.data
  }

  const { data } = await apiClient.put<{ data: Deportista }>(
    `/deportistas/${id}`,
    {
      nombre: payload.nombre,
      apellido: payload.apellido,
      fecha_nacimiento: payload.fecha_nacimiento,
      modalidad_id: payload.modalidad_id,
    },
  )
  return data.data
}

export async function deleteDeportista(id: number): Promise<void> {
  await apiClient.delete(`/deportistas/${id}`)
}
