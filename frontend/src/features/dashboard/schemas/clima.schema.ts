import { z } from 'zod'

export const climaSchema = z.object({
  ciudad: z
    .string()
    .trim()
    .min(1, 'Ingresa una ciudad')
    .max(100, 'La ciudad es demasiado larga'),
})

export type ClimaFormValues = z.infer<typeof climaSchema>
