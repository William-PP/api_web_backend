import { apiClient } from '../../../shared/api/client'

export type Resumen = {
  totales: {
    deportistas: number
    modalidades: number
    usuarios: number
  }
  por_modalidad: {
    modalidad: string
    total: number
  }[]
}

export type Clima = {
  ciudad: string
  pais: string
  latitud: number
  longitud: number
  temperatura: number
  humedad: number
  viento: number
  codigo_clima: number
  actualizado_en: string
}

export async function getResumen(): Promise<Resumen> {
  const { data } = await apiClient.get<Resumen>('/dashboard/resumen')
  return data
}

export async function getClima(ciudad: string): Promise<Clima> {
  const { data } = await apiClient.get<Clima>('/dashboard/clima', {
    params: { ciudad },
  })
  return data
}
