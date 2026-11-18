import { apiClient } from '../../../shared/api/client'

export type User = {
  id: number
  name: string
  email: string
  foto: string | null
  foto_url: string | null
  created_at: string | null
}

export type AuthResponse = {
  user: User
  token: string
}

export type LoginPayload = {
  email: string
  password: string
}

export type RegisterPayload = {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export type ForgotPasswordPayload = {
  email: string
}

export type ResetPasswordPayload = {
  token: string
  email: string
  password: string
  password_confirmation: string
}

export async function login(payload: LoginPayload): Promise<AuthResponse> {
  const { data } = await apiClient.post<AuthResponse>('/login', payload)
  return data
}

export async function register(payload: RegisterPayload): Promise<AuthResponse> {
  const { data } = await apiClient.post<AuthResponse>('/register', payload)
  return data
}

export async function logout(): Promise<void> {
  await apiClient.post('/logout')
}

export async function getMe(): Promise<User> {
  const { data } = await apiClient.get<{ data: User }>('/me')
  return data.data
}

export async function forgotPassword(
  payload: ForgotPasswordPayload,
): Promise<{ message: string }> {
  const { data } = await apiClient.post<{ message: string }>(
    '/forgot-password',
    payload,
  )
  return data
}

export async function resetPassword(
  payload: ResetPasswordPayload,
): Promise<{ message: string }> {
  const { data } = await apiClient.post<{ message: string }>(
    '/reset-password',
    payload,
  )
  return data
}