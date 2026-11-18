import type { ButtonHTMLAttributes } from 'react'

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement>

export function Button({ className = '', ...props }: ButtonProps) {
  return (
    <button
      className={`rounded bg-slate-900 px-4 py-2 text-white disabled:cursor-not-allowed disabled:opacity-60 ${className}`}
      {...props}
    />
  )
}