import { apiClient } from '../../../shared/api/client'

export type DeportistaPublico = {
  id: number
  nombre: string
  apellido: string
  nombre_completo: string
  fecha_nacimiento: string
  foto_url: string | null
  modalidad: string | null
}

export async function getDeportistasPublicos(): Promise<
  DeportistaPublico[]
> {
  const { data } = await apiClient.get<{ data: DeportistaPublico[] }>(
    '/public/deportistas',
  )
  return data.data
}
