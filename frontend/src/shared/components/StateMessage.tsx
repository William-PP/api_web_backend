type StateMessageProps = {
  kind?: 'loading' | 'error' | 'empty'
  message: string
}

const styles: Record<NonNullable<StateMessageProps['kind']>, string> = {
  loading: 'text-slate-500',
  error: 'rounded bg-red-50 px-3 py-2 text-red-700',
  empty: 'text-slate-500',
}

export function StateMessage({ kind = 'empty', message }: StateMessageProps) {
  return <p className={`py-6 text-center text-sm ${styles[kind]}`}>{message}</p>
}
