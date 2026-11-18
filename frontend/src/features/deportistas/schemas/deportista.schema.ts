import { z } from 'zod'

export const deportistaSchema = z.object({
  nombre: z
    .string()
    .trim()
    .min(2, 'El nombre debe tener al menos 2 caracteres')
    .max(100, 'El nombre no puede superar los 100 caracteres'),
  apellido: z
    .string()
    .trim()
    .min(2, 'El apellido debe tener al menos 2 caracteres')
    .max(100, 'El apellido no puede superar los 100 caracteres'),
  fecha_nacimiento: z
    .string()
    .regex(/^\d{4}-\d{2}-\d{2}$/, 'Selecciona una fecha válida')
    .refine(
      (value) => new Date(value) <= new Date(),
      'La fecha no puede ser futura',
    ),
  modalidad_id: z.string().min(1, 'Selecciona una modalidad'),
  foto: z
    .instanceof(File)
    .nullable()
    .optional()
    .refine(
      (file) => !file || file.size <= 2 * 1024 * 1024,
      'La imagen no puede superar los 2 MB',
    )
    .refine(
      (file) =>
        !file ||
        ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(
          file.type,
        ),
      'Formato no válido (jpeg, png o webp)',
    ),
})

export type DeportistaFormValues = z.infer<typeof deportistaSchema>
