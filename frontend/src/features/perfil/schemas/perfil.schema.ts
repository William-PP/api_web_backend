import { z } from 'zod'

export const perfilSchema = z.object({
  name: z
    .string()
    .trim()
    .min(2, 'El nombre debe tener al menos 2 caracteres')
    .max(255, 'El nombre es demasiado largo'),
  email: z.email('Ingresa un correo válido'),
})

export type PerfilFormValues = z.infer<typeof perfilSchema>
