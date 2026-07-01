import { FormEvent, useState } from 'react'
import { Link, Navigate, useLocation, useNavigate, useSearchParams } from 'react-router-dom'
import { api, apiMessage, csrf, fieldErrors } from '../services/api'
import { useAuth } from '../services/auth'
import { Alert, Field } from '../components/ui'

function AuthShell({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="grid min-h-screen place-items-center bg-ink-950 px-4 py-10 dark:bg-neutral-950">
      <div className="w-full max-w-md">
        <div className="mb-8 flex items-center justify-center gap-3 text-white">
          <div className="grid size-12 place-items-center rounded-2xl bg-brand-500 text-xl font-black text-ink-950">R</div>
          <div>
            <p className="text-xl font-black">Running</p>
            <p className="text-sm text-slate-400">Studio</p>
          </div>
        </div>
        <div className="rounded-2xl bg-white p-6 shadow-2xl dark:border dark:border-neutral-800 dark:bg-neutral-900">
          <h1 className="mb-5 text-2xl font-black text-ink-950 dark:text-white">{title}</h1>
          {children}
        </div>
      </div>
    </div>
  )
}

export function LoginPage() {
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()
  const [email, setEmail] = useState('admin@running.test')
  const [password, setPassword] = useState('password')
  const [remember, setRemember] = useState(true)
  const [error, setError] = useState('')
  const [errors, setErrors] = useState<Record<string, string[]>>({})
  const [saving, setSaving] = useState(false)

  if (user) return <Navigate to={user.must_change_password ? '/primeiro-acesso' : '/dashboard'} replace />

  async function submit(event: FormEvent) {
    event.preventDefault()
    setSaving(true)
    setError('')
    setErrors({})
    try {
      const destination = await login(email, password, remember)
      navigate((location.state as { from?: string } | null)?.from ?? destination, { replace: true })
    } catch (err) {
      setError(apiMessage(err))
      setErrors(fieldErrors(err))
    } finally {
      setSaving(false)
    }
  }

  return (
    <AuthShell title="Entrar">
      {error && <Alert tone="error">{error}</Alert>}
      <form className="space-y-4" onSubmit={submit}>
        <Field label="E-mail" error={errors.email}><input className="field" value={email} onChange={(e) => setEmail(e.target.value)} type="email" /></Field>
        <Field label="Senha" error={errors.password}><input className="field" value={password} onChange={(e) => setPassword(e.target.value)} type="password" /></Field>
        <label className="flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-neutral-300"><input type="checkbox" checked={remember} onChange={(e) => setRemember(e.target.checked)} />Lembrar acesso</label>
        <button className="btn-primary w-full" disabled={saving}>{saving ? 'Entrando...' : 'Entrar'}</button>
      </form>
      <Link to="/esqueci-minha-senha" className="mt-4 block text-center text-sm font-bold text-brand-700">Esqueci minha senha</Link>
    </AuthShell>
  )
}

export function ForgotPasswordPage() {
  const [email, setEmail] = useState('')
  const [message, setMessage] = useState('')
  const [error, setError] = useState('')

  async function submit(event: FormEvent) {
    event.preventDefault()
    setMessage('')
    setError('')
    try {
      await csrf()
      const { data } = await api.post('/auth/forgot-password', { email })
      setMessage(data.message)
    } catch (err) {
      setError(apiMessage(err))
    }
  }

  return (
    <AuthShell title="Recuperar senha">
      {message && <Alert tone="success">{message}</Alert>}
      {error && <Alert tone="error">{error}</Alert>}
      <form className="space-y-4" onSubmit={submit}>
        <Field label="E-mail"><input className="field" value={email} onChange={(e) => setEmail(e.target.value)} type="email" /></Field>
        <button className="btn-primary w-full">Enviar link</button>
      </form>
      <Link to="/login" className="mt-4 block text-center text-sm font-bold text-brand-700">Voltar ao login</Link>
    </AuthShell>
  )
}

export function ResetPasswordPage() {
  const [params] = useSearchParams()
  const [email, setEmail] = useState(params.get('email') ?? '')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [message, setMessage] = useState('')
  const [error, setError] = useState('')
  const token = params.get('token') ?? ''

  async function submit(event: FormEvent) {
    event.preventDefault()
    setMessage('')
    setError('')
    try {
      await csrf()
      const { data } = await api.post('/auth/reset-password', { token, email, password, password_confirmation: passwordConfirmation })
      setMessage(data.message)
    } catch (err) {
      setError(apiMessage(err))
    }
  }

  return (
    <AuthShell title="Redefinir senha">
      {message && <Alert tone="success">{message}</Alert>}
      {error && <Alert tone="error">{error}</Alert>}
      <form className="space-y-4" onSubmit={submit}>
        <Field label="E-mail"><input className="field" value={email} onChange={(e) => setEmail(e.target.value)} type="email" /></Field>
        <Field label="Nova senha"><input className="field" value={password} onChange={(e) => setPassword(e.target.value)} type="password" /></Field>
        <Field label="Confirmar senha"><input className="field" value={passwordConfirmation} onChange={(e) => setPasswordConfirmation(e.target.value)} type="password" /></Field>
        <button className="btn-primary w-full">Salvar nova senha</button>
      </form>
    </AuthShell>
  )
}

export function FirstAccessPage() {
  const { user, setUser } = useAuth()
  const navigate = useNavigate()
  const [currentPassword, setCurrentPassword] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [error, setError] = useState('')

  if (user && !user.must_change_password) return <Navigate to="/dashboard" replace />

  async function submit(event: FormEvent) {
    event.preventDefault()
    setError('')
    try {
      await csrf()
      const { data } = await api.put('/auth/change-temporary-password', { current_password: currentPassword, password, password_confirmation: passwordConfirmation })
      setUser(data.user)
      navigate('/dashboard', { replace: true })
    } catch (err) {
      setError(apiMessage(err))
    }
  }

  return (
    <AuthShell title="Primeiro acesso">
      {error && <Alert tone="error">{error}</Alert>}
      <form className="space-y-4" onSubmit={submit}>
        <Field label="Senha temporária"><input className="field" value={currentPassword} onChange={(e) => setCurrentPassword(e.target.value)} type="password" /></Field>
        <Field label="Senha definitiva"><input className="field" value={password} onChange={(e) => setPassword(e.target.value)} type="password" /></Field>
        <Field label="Confirmar senha"><input className="field" value={passwordConfirmation} onChange={(e) => setPasswordConfirmation(e.target.value)} type="password" /></Field>
        <button className="btn-primary w-full">Criar senha definitiva</button>
      </form>
    </AuthShell>
  )
}
