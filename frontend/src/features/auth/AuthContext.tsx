import { useCallback, useEffect, useMemo, useState } from 'react'
import type { PropsWithChildren } from 'react'
import { getMe, logout as logoutRequest } from './api/auth.api'
import type { User } from './api/auth.api'
import { AuthContext } from './auth-context'
import type { AuthContextValue } from './auth-context'

function readStoredUser(): User | null {
  const storedUser = localStorage.getItem('user')

  if (!storedUser) {
    return null
  }

  try {
    return JSON.parse(storedUser) as User
  } catch {
    return null
  }
}

export function AuthProvider({ children }: PropsWithChildren) {
  const [token, setTokenState] = useState<string | null>(() =>
    localStorage.getItem('token'),
  )
  const [user, setUserState] = useState<User | null>(readStoredUser)
  const [validatedToken, setValidatedToken] = useState<string | null>(null)

  const isChecking = token !== null && validatedToken !== token

  const clearSession = useCallback(() => {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    setTokenState(null)
    setUserState(null)
    setValidatedToken(null)
  }, [])

  useEffect(() => {
    if (!token) {
      return
    }

    let active = true

    getMe()
      .then((me) => {
        if (!active) return
        localStorage.setItem('user', JSON.stringify(me))
        setUserState(me)
        setValidatedToken(token)
      })
      .catch(() => {
        if (active) clearSession()
      })

    return () => {
      active = false
    }
  }, [token, clearSession])

  const logout = useCallback(async () => {
    try {
      await logoutRequest()
    } catch {
      // La sesión se cierra localmente aunque falle la petición.
    }

    clearSession()
  }, [clearSession])

  const setSession = useCallback((nextUser: User, nextToken: string) => {
    localStorage.setItem('token', nextToken)
    localStorage.setItem('user', JSON.stringify(nextUser))
    setTokenState(nextToken)
    setUserState(nextUser)
    setValidatedToken(nextToken)
  }, [])

  const updateUser = useCallback((nextUser: User) => {
    localStorage.setItem('user', JSON.stringify(nextUser))
    setUserState(nextUser)
  }, [])

  const value = useMemo<AuthContextValue>(
    () => ({
      token,
      user,
      isChecking,
      setSession,
      updateUser,
      logout,
    }),
    [token, user, isChecking, setSession, updateUser, logout],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}
