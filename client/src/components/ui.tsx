import { Loader2 } from 'lucide-react'
import type { FormEvent, ReactNode } from 'react'

export function Loading() {
  return <div className="flex min-h-64 items-center justify-center text-slate-500 dark:text-neutral-400"><Loader2 className="mr-2 size-5 animate-spin" />Carregando...</div>
}

export function Alert({ children, tone = 'info' }: { children: ReactNode; tone?: 'info' | 'error' | 'success' }) {
  const classes = tone === 'error' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/70 dark:bg-red-950/40 dark:text-red-200' : tone === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/70 dark:bg-emerald-950/40 dark:text-emerald-200' : 'border-slate-200 bg-white text-slate-700 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200'
  return <div className={`mb-6 rounded-xl border px-4 py-3 text-sm font-medium ${classes}`}>{children}</div>
}

export function Field({ label, error, children }: { label: string; error?: string[]; children: ReactNode }) {
  return (
    <label className="block">
      <span className="label">{label}</span>
      {children}
      {error?.[0] && <span className="mt-1 block text-xs font-semibold text-red-600 dark:text-red-400">{error[0]}</span>}
    </label>
  )
}

export function Empty({ children }: { children: ReactNode }) {
  return <div className="card px-6 py-12 text-center text-sm font-semibold text-slate-500 dark:text-neutral-400">{children}</div>
}

export function PageActions({ children }: { children: ReactNode }) {
  return <div className="mb-5 flex flex-wrap items-center justify-between gap-3">{children}</div>
}

export function FormCard({ title, onSubmit, children }: { title: string; onSubmit: (event: FormEvent<HTMLFormElement>) => void; children: ReactNode }) {
  return (
    <form onSubmit={onSubmit} className="card mb-6 p-5">
      <h2 className="mb-4 text-lg font-black text-ink-950 dark:text-white">{title}</h2>
      <div className="grid gap-4 md:grid-cols-2">{children}</div>
    </form>
  )
}

export function money(value: unknown) {
  return Number(value ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

export function date(value?: string | null) {
  return value ? new Date(`${value}`.slice(0, 10) + 'T00:00:00').toLocaleDateString('pt-BR') : '-'
}
