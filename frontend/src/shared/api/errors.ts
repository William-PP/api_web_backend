import axios from 'axios'

type ApiErrorPayload = {
  message?: string
  errors?: Record<string, string[] | string>
}

export function getErrorMessage(error: unknown): string {
  if (axios.isAxiosError<ApiErrorPayload>(error)) {
    const data = error.response?.data

    if (data?.errors) {
      const messages = Object.values(data.errors).flat()

      if (messages.length > 0) {
        return messages.join(' ')
      }
    }

    if (data?.message) {
      return data.message
    }

    if (error.code === 'ERR_NETWORK') {
      return 'No se pudo conectar con el servidor.'
    }

    return error.message
  }

  if (error instanceof Error) {
    return error.message
  }

  return 'Ocurrió un error inesperado.'
}
