import { apiClient } from '../../../shared/api/client'

export type Modalidad = {
  id: number
  nombre: string
  descripcion: string | null
  deportistas_count: number
  created_at: string | null
}

export type ModalidadPayload = {
  nombre: string
  descripcion: string
}

export async function getModalidades(): Promise<Modalidad[]> {
  const { data } = await apiClient.get<{ data: Modalidad[] }>('/modalidades')
  return data.data
}

export async function createModalidad(
  payload: ModalidadPayload,
): Promise<Modalidad> {
  const { data } = await apiClient.post<{ data: Modalidad }>(
    '/modalidades',
    payload,
  )
  return data.data
}

export async function updateModalidad(
  id: number,
  payload: ModalidadPayload,
): Promise<Modalidad> {
  const { data } = await apiClient.put<{ data: Modalidad }>(
    `/modalidades/${id}`,
    payload,
  )
  return data.data
}

export async function deleteModalidad(id: number): Promise<void> {
  await apiClient.delete(`/modalidades/${id}`)
}
