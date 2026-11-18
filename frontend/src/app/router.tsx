import { createBrowserRouter } from 'react-router-dom'
import {
  ForgotPasswordPage,
  LoginPage,
  RegisterPage,
  ResetPasswordPage,
} from '../features/auth'
import { DashboardPage } from '../features/dashboard'
import { DeportistasPage } from '../features/deportistas'
import { LandingPage } from '../features/landing/pages/LandingPage'
import { ModalidadesPage } from '../features/modalidades'
import { PerfilPage } from '../features/perfil'
import { ErrorPage } from './pages/ErrorPage'
import { AppLayout } from './layouts/AppLayout'
import { ProtectedRoute } from './layouts/ProtectedRoute'
import { PublicLayout } from './layouts/PublicLayout'

export const router = createBrowserRouter([
  {
    element: <PublicLayout />,
    errorElement: <ErrorPage />,
    children: [
      { path: '/', element: <LandingPage /> },
      { path: '/login', element: <LoginPage /> },
      { path: '/registro', element: <RegisterPage /> },
      { path: '/olvide-contrasena', element: <ForgotPasswordPage /> },
      { path: '/restablecer-contrasena', element: <ResetPasswordPage /> },
    ],
  },
  {
    element: <ProtectedRoute />,
    errorElement: <ErrorPage />,
    children: [
      {
        element: <AppLayout />,
        children: [
          { path: '/app', element: <DashboardPage /> },
          { path: '/app/deportistas', element: <DeportistasPage /> },
          { path: '/app/modalidades', element: <ModalidadesPage /> },
          { path: '/app/perfil', element: <PerfilPage /> },
        ],
      },
    ],
  },
])