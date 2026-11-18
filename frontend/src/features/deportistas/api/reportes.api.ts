import { apiClient } from '../../../shared/api/client'

export async function downloadDeportistasPdf(): Promise<void> {
  const response = await apiClient.get<Blob>('/reportes/deportistas.pdf', {
    responseType: 'blob',
  })

  const url = URL.createObjectURL(response.data)
  const link = document.createElement('a')
  link.href = url
  link.download = 'deportistas.pdf'
  document.body.appendChild(link)
  link.click()
  link.remove()
  URL.revokeObjectURL(url)
}
