import { createContext } from 'react'
import type { User } from './api/auth.api'

export type AuthContextValue = {
  token: string | null
  user: User | null
  isChecking: boolean
  setSession: (user: User, token: string) => void
  updateUser: (user: User) => void
  logout: () => Promise<void>
}

export const AuthContext = createContext<AuthContextValue | null>(null)
