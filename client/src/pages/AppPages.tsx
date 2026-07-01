import { FormEvent, useEffect, useMemo, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { Plus, Save, Trash2 } from 'lucide-react'
import { Alert, Empty, Field, FormCard, Loading, PageActions, date, money } from '../components/ui'
import { api, apiMessage, csrf, fieldErrors, hasPermission, storageUrl } from '../services/api'
import { useAuth } from '../services/auth'

type AnyRecord = Record<string, any>

function useApi<T>(path: string) {
  const [data, setData] = useState<T | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const reload = async () => {
    setLoading(true)
    setError('')
    try {
      const response = await api.get(path)
      setData(response.data)
    } catch (err) {
      setError(apiMessage(err))
    } finally {
      setLoading(false)
    }
  }
  useEffect(() => { reload() }, [path])
  return { data, loading, error, reload }
}

function SubmitMessage({ message, error }: { message: string; error: string }) {
  return (
    <>
      {message && <Alert tone="success">{message}</Alert>}
      {error && <Alert tone="error">{error}</Alert>}
    </>
  )
}

function TextInput({ label, value, onChange, type = 'text', error }: { label: string; value: any; onChange: (value: any) => void; type?: string; error?: string[] }) {
  return <Field label={label} error={error}><input className="field" value={value ?? ''} onChange={(e) => onChange(e.target.value)} type={type} /></Field>
}

function SelectInput({ label, value, onChange, children, error }: { label: string; value: any; onChange: (value: any) => void; children: React.ReactNode; error?: string[] }) {
  return <Field label={label} error={error}><select className="field" value={value ?? ''} onChange={(e) => onChange(e.target.value)}>{children}</select></Field>
}

function CheckboxInput({ label, checked, onChange }: { label: string; checked: boolean; onChange: (checked: boolean) => void }) {
  return <label className="flex items-center gap-2 pt-8 text-sm font-bold text-slate-700 dark:text-neutral-200"><input type="checkbox" checked={checked} onChange={(e) => onChange(e.target.checked)} />{label}</label>
}

export function DashboardPage() {
  const { data, loading, error } = useApi<any>('/dashboard')
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  const stats = [
    ['Alunos', data?.stats.member_count],
    ['Exercícios', data?.stats.exercise_count],
    ['Treinos ativos', data?.stats.workout_count],
    ['Matrículas', data?.stats.membership_count],
    ['Inadimplência', data?.stats.overdue_amount != null ? money(data.stats.overdue_amount) : null],
    ['Recebido no mês', data?.stats.received_this_month != null ? money(data.stats.received_this_month) : null],
  ].filter(([, value]) => value !== null && value !== undefined)
  return (
    <div className="space-y-6">
      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">{stats.map(([label, value]) => <div className="card p-5" key={label}><p className="text-sm text-slate-500 dark:text-neutral-400">{label}</p><p className="mt-2 text-2xl font-black text-ink-950 dark:text-white">{value}</p></div>)}</div>
      <section className="card p-5">
        <h2 className="mb-4 text-lg font-black text-ink-950 dark:text-white">Treinos recentes</h2>
        <div className="divide-y divide-slate-100 dark:divide-neutral-800">{data?.workouts?.map((workout: AnyRecord) => <Link className="flex items-center justify-between py-3 text-sm hover:text-brand-700" to={`/treinos/${workout.id}`} key={workout.id}><span className="font-bold">{workout.name}</span><span>{date(workout.starts_at)}</span></Link>)}</div>
      </section>
    </div>
  )
}

export function ProfilePage() {
  const { user, setUser } = useAuth()
  const [form, setForm] = useState<AnyRecord>(() => ({ ...user }))
  const [password, setPassword] = useState({ current_password: '', password: '', password_confirmation: '' })
  const [message, setMessage] = useState('')
  const [error, setError] = useState('')
  const [errors, setErrors] = useState<Record<string, string[]>>({})

  async function saveProfile(event: FormEvent) {
    event.preventDefault()
    setMessage('')
    setError('')
    setErrors({})
    const payload = new FormData()
    Object.entries(form).forEach(([key, value]) => {
      if (value !== undefined && value !== null && !['permissions', 'initials', 'avatar_url'].includes(key)) payload.append(key, String(value))
    })
    const file = (event.currentTarget.querySelector('input[type=file]') as HTMLInputElement)?.files?.[0]
    if (file) payload.append('avatar', file)
    try {
      await csrf()
      const { data } = await api.post('/profile?_method=PUT', payload, { headers: { 'Content-Type': 'multipart/form-data' } })
      setUser(data.user)
      setMessage(data.message)
    } catch (err) {
      setError(apiMessage(err))
      setErrors(fieldErrors(err))
    }
  }

  async function savePassword(event: FormEvent) {
    event.preventDefault()
    setMessage('')
    setError('')
    try {
      await csrf()
      const { data } = await api.put('/profile/password', password)
      setMessage(data.message)
      setPassword({ current_password: '', password: '', password_confirmation: '' })
    } catch (err) {
      setError(apiMessage(err))
    }
  }

  return (
    <>
      <SubmitMessage message={message} error={error} />
      <FormCard title="Dados pessoais" onSubmit={saveProfile}>
        <TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} error={errors.name} />
        <TextInput label="E-mail" value={form.email} onChange={(email) => setForm({ ...form, email })} type="email" error={errors.email} />
        <TextInput label="Telefone" value={form.phone} onChange={(phone) => setForm({ ...form, phone })} error={errors.phone} />
        <TextInput label="Nascimento" value={form.birth_date} onChange={(birth_date) => setForm({ ...form, birth_date })} type="date" error={errors.birth_date} />
        <TextInput label="Endereço" value={form.address} onChange={(address) => setForm({ ...form, address })} error={errors.address} />
        <Field label="Avatar"><input className="field" type="file" accept="image/*" /></Field>
        <div className="md:col-span-2"><button className="btn-primary"><Save className="size-4" />Salvar perfil</button></div>
      </FormCard>
      <FormCard title="Alterar senha" onSubmit={savePassword}>
        <TextInput label="Senha atual" value={password.current_password} onChange={(value) => setPassword({ ...password, current_password: value })} type="password" />
        <TextInput label="Nova senha" value={password.password} onChange={(value) => setPassword({ ...password, password: value })} type="password" />
        <TextInput label="Confirmar senha" value={password.password_confirmation} onChange={(value) => setPassword({ ...password, password_confirmation: value })} type="password" />
        <div className="md:col-span-2"><button className="btn-primary">Alterar senha</button></div>
      </FormCard>
    </>
  )
}

function PeopleForm({ type, endpoint, defaults, groups, onSaved }: { type: 'member' | 'user'; endpoint: string; defaults?: AnyRecord; groups?: AnyRecord[]; onSaved: (message: string) => void }) {
  const [form, setForm] = useState<AnyRecord>(defaults ?? { name: '', email: '', phone: '', birth_date: '', address: '', active: true, role: 'trainer', permission_group_id: '' })
  const [errors, setErrors] = useState<Record<string, string[]>>({})
  async function submit(event: FormEvent) {
    event.preventDefault()
    setErrors({})
    try {
      await csrf()
      const { data } = await api.post(endpoint, form)
      onSaved(data.message)
      setForm(defaults ?? { name: '', email: '', phone: '', birth_date: '', address: '', active: true, role: 'trainer', permission_group_id: '' })
    } catch (err) {
      setErrors(fieldErrors(err))
      onSaved(apiMessage(err))
    }
  }
  return (
    <FormCard title={type === 'member' ? 'Novo aluno' : 'Novo usuário'} onSubmit={submit}>
      <TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} error={errors.name} />
      <TextInput label="E-mail" value={form.email} onChange={(email) => setForm({ ...form, email })} type="email" error={errors.email} />
      {type === 'user' && <SelectInput label="Perfil" value={form.role} onChange={(role) => setForm({ ...form, role })}><option value="admin">Administrador</option><option value="trainer">Professor</option><option value="member">Aluno</option></SelectInput>}
      {type === 'user' && <SelectInput label="Grupo de permissão" value={form.permission_group_id} onChange={(permission_group_id) => setForm({ ...form, permission_group_id })}><option value="">Padrão do perfil</option>{groups?.map((group) => <option key={group.id} value={group.id}>{group.name}</option>)}</SelectInput>}
      <TextInput label="Telefone" value={form.phone} onChange={(phone) => setForm({ ...form, phone })} />
      <TextInput label="Nascimento" value={form.birth_date} onChange={(birth_date) => setForm({ ...form, birth_date })} type="date" />
      <TextInput label="Endereço" value={form.address} onChange={(address) => setForm({ ...form, address })} />
      <CheckboxInput label="Ativo" checked={Boolean(form.active)} onChange={(active) => setForm({ ...form, active })} />
      <div className="md:col-span-2"><button className="btn-primary"><Plus className="size-4" />Salvar</button></div>
    </FormCard>
  )
}

export function MembersPage() {
  const { data, loading, error, reload } = useApi<any>('/members')
  const [message, setMessage] = useState('')
  async function deactivate(id: number) { await csrf(); await api.delete(`/members/${id}`); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <SubmitMessage message={message} error="" />
      <PeopleForm type="member" endpoint="/members" onSaved={(msg) => { setMessage(msg); reload() }} />
      <div className="grid gap-3 xl:grid-cols-2">{data.members.data.map((member: AnyRecord) => <article className="card p-5" key={member.id}><div className="flex items-start justify-between gap-4"><div><h2 className="font-black text-ink-950 dark:text-white">{member.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{member.email}</p><p className="mt-2 text-xs text-slate-500 dark:text-neutral-400">{member.phone ?? 'Sem telefone'}</p></div><button className="btn-secondary" onClick={() => deactivate(member.id)}><Trash2 className="size-4" />Desativar</button></div></article>)}</div>
    </>
  )
}

export function UsersPage() {
  const { data, loading, error, reload } = useApi<any>('/users')
  const [message, setMessage] = useState('')
  async function deactivate(id: number) { await csrf(); await api.delete(`/users/${id}`); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <SubmitMessage message={message} error="" />
      <PeopleForm type="user" endpoint="/users" groups={data.permission_groups} onSaved={(msg) => { setMessage(msg); reload() }} />
      <div className="card overflow-hidden"><table className="w-full text-left text-sm"><tbody>{data.users.data.map((user: AnyRecord) => <tr className="border-b border-slate-100 dark:border-neutral-800" key={user.id}><td className="p-4 font-bold">{user.name}</td><td className="p-4">{user.email}</td><td className="p-4">{user.role}</td><td className="p-4 text-right"><button className="btn-secondary" onClick={() => deactivate(user.id)}>Desativar</button></td></tr>)}</tbody></table></div>
    </>
  )
}

export function ExercisesPage() {
  const { data, loading, error, reload } = useApi<any>('/exercises')
  const [form, setForm] = useState<AnyRecord>({ name: '', muscle_group_id: '', instructions: '', active: true, video_url: '' })
  const [message, setMessage] = useState('')
  async function submit(event: FormEvent) {
    event.preventDefault()
    const payload = new FormData()
    Object.entries(form).forEach(([key, value]) => payload.append(key, String(value)))
    const files = (event.currentTarget.querySelector('input[type=file]') as HTMLInputElement)?.files
    Array.from(files ?? []).forEach((file) => payload.append('images[]', file))
    await csrf()
    const { data: saved } = await api.post('/exercises', payload)
    setMessage(saved.message)
    setForm({ name: '', muscle_group_id: '', instructions: '', active: true, video_url: '' })
    reload()
  }
  async function deactivate(id: number) { await csrf(); await api.delete(`/exercises/${id}`); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <SubmitMessage message={message} error="" />
      <FormCard title="Novo exercício" onSubmit={submit}>
        <TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} />
        <SelectInput label="Grupo muscular" value={form.muscle_group_id} onChange={(muscle_group_id) => setForm({ ...form, muscle_group_id })}><option value="">Selecione</option>{data.muscle_groups.map((group: AnyRecord) => <option key={group.id} value={group.id}>{group.name}</option>)}</SelectInput>
        <TextInput label="Vídeo" value={form.video_url} onChange={(video_url) => setForm({ ...form, video_url })} />
        <Field label="Imagens"><input className="field" type="file" multiple accept="image/*" /></Field>
        <label className="md:col-span-2"><span className="label">Instruções</span><textarea className="field" value={form.instructions} onChange={(e) => setForm({ ...form, instructions: e.target.value })} /></label>
        <div className="md:col-span-2"><button className="btn-primary">Salvar exercício</button></div>
      </FormCard>
      <div className="grid gap-3 xl:grid-cols-2">{data.exercises.data.map((exercise: AnyRecord) => <article className="card p-5" key={exercise.id}><div className="flex justify-between gap-4"><div><h2 className="font-black">{exercise.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{exercise.muscle_group?.name}</p></div><button className="btn-secondary" onClick={() => deactivate(exercise.id)}>Desativar</button></div></article>)}</div>
    </>
  )
}

export function WorkoutsPage() {
  const { user } = useAuth()
  const { data, loading, error } = useApi<any>('/workouts')
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <PageActions><div /><>{hasPermission(user, 'workouts.manage') && <Link className="btn-primary" to="/treinos/novo"><Plus className="size-4" />Novo treino</Link>}</></PageActions>
      <div className="grid gap-3 xl:grid-cols-2">{data.workouts.data.map((workout: AnyRecord) => <Link className="card block p-5 transition hover:border-brand-200" to={`/treinos/${workout.id}`} key={workout.id}><h2 className="font-black text-ink-950 dark:text-white">{workout.name}</h2><p className="mt-1 text-sm text-slate-500 dark:text-neutral-400">{workout.member?.name} · {date(workout.starts_at)}</p><p className="mt-4 text-sm font-bold text-brand-700">{workout.items?.length ?? 0} exercícios</p></Link>)}</div>
    </>
  )
}

export function WorkoutFormPage() {
  const navigate = useNavigate()
  const { data, loading, error } = useApi<any>('/workouts/form-data')
  const [form, setForm] = useState<AnyRecord>({ member_id: '', name: '', starts_at: new Date().toISOString().slice(0, 10), ends_at: '', notes: '', items: [{ exercise_id: '', sets: 3, repetitions: '10-12', weight: '', rest_seconds: 60, notes: '' }] })
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  function item(index: number, key: string, value: any) { setForm({ ...form, items: form.items.map((row: AnyRecord, i: number) => i === index ? { ...row, [key]: value } : row) }) }
  async function submit(event: FormEvent) {
    event.preventDefault()
    await csrf()
    const { data: saved } = await api.post('/workouts', form)
    navigate(`/treinos/${saved.workout.id}`)
  }
  return (
    <form className="space-y-5" onSubmit={submit}>
      <div className="card grid gap-4 p-5 md:grid-cols-2">
        <SelectInput label="Aluno" value={form.member_id} onChange={(member_id) => setForm({ ...form, member_id })}><option value="">Selecione</option>{data.members.map((member: AnyRecord) => <option key={member.id} value={member.id}>{member.name}</option>)}</SelectInput>
        <TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} />
        <TextInput label="Início" value={form.starts_at} onChange={(starts_at) => setForm({ ...form, starts_at })} type="date" />
        <TextInput label="Fim" value={form.ends_at} onChange={(ends_at) => setForm({ ...form, ends_at })} type="date" />
      </div>
      <div className="card p-5">
        <div className="mb-4 flex items-center justify-between"><h2 className="font-black">Exercícios</h2><button type="button" className="btn-secondary" onClick={() => setForm({ ...form, items: [...form.items, { exercise_id: '', sets: 3, repetitions: '10-12', weight: '', rest_seconds: 60, notes: '' }] })}>Adicionar</button></div>
        <div className="space-y-3">{form.items.map((row: AnyRecord, index: number) => <div className="grid gap-3 rounded-xl border border-slate-100 dark:border-neutral-800 p-3 md:grid-cols-5" key={index}><SelectInput label="Exercício" value={row.exercise_id} onChange={(value) => item(index, 'exercise_id', value)}><option value="">Selecione</option>{data.exercises.map((exercise: AnyRecord) => <option key={exercise.id} value={exercise.id}>{exercise.name}</option>)}</SelectInput><TextInput label="Séries" value={row.sets} onChange={(value) => item(index, 'sets', value)} type="number" /><TextInput label="Repetições" value={row.repetitions} onChange={(value) => item(index, 'repetitions', value)} /><TextInput label="Carga" value={row.weight} onChange={(value) => item(index, 'weight', value)} /><TextInput label="Descanso" value={row.rest_seconds} onChange={(value) => item(index, 'rest_seconds', value)} type="number" /></div>)}</div>
      </div>
      <button className="btn-primary">Salvar treino</button>
    </form>
  )
}

export function WorkoutDetailPage() {
  const { id } = useParams()
  const { data, loading, error } = useApi<any>(`/workouts/${id}`)
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  const workout = data.workout
  return <div className="space-y-4"><section className="card p-5"><h2 className="text-xl font-black">{workout.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{workout.member?.name} · {date(workout.starts_at)}</p></section>{workout.items.map((item: AnyRecord) => <article className="card p-5" key={item.id}><h3 className="font-black">{item.exercise?.name}</h3><p className="mt-2 text-sm text-slate-600 dark:text-neutral-300">{item.sets} séries · {item.repetitions} reps · {item.weight ?? '-'} kg · {item.rest_seconds ?? 0}s</p></article>)}</div>
}

export function ShopPage() {
  const { user } = useAuth()
  const { data, loading, error, reload } = useApi<any>('/shop')
  const [form, setForm] = useState<AnyRecord>({ name: '', description: '', price: '', stock: 0, active: true })
  const canManage = hasPermission(user, 'shop.manage')
  async function submit(event: FormEvent) {
    event.preventDefault()
    const payload = new FormData()
    Object.entries(form).forEach(([key, value]) => payload.append(key, String(value)))
    const file = (event.currentTarget.querySelector('input[type=file]') as HTMLInputElement)?.files?.[0]
    if (file) payload.append('image', file)
    await csrf(); await api.post('/shop/products', payload); reload()
  }
  async function order(id: number) { await csrf(); await api.post(`/shop/products/${id}/order`, { quantity: 1 }); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      {canManage && <FormCard title="Novo produto" onSubmit={submit}><TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} /><TextInput label="Preço" value={form.price} onChange={(price) => setForm({ ...form, price })} type="number" /><TextInput label="Estoque" value={form.stock} onChange={(stock) => setForm({ ...form, stock })} type="number" /><Field label="Imagem"><input className="field" type="file" accept="image/*" /></Field><div className="md:col-span-2"><button className="btn-primary">Salvar produto</button></div></FormCard>}
      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">{data.products.map((product: AnyRecord) => <article className="card overflow-hidden" key={product.id}>{product.image_url && <img className="h-40 w-full object-cover" src={storageUrl(product.image_url) ?? ''} />}<div className="p-5"><h2 className="font-black">{product.name}</h2><p className="mt-1 text-sm text-slate-500 dark:text-neutral-400">{product.description}</p><p className="mt-4 text-xl font-black">{money(product.price)}</p>{!canManage && <button className="btn-primary mt-4 w-full" onClick={() => order(product.id)}>Pedir</button>}</div></article>)}</div>
    </>
  )
}

export function PlansPage() {
  const { data, loading, error, reload } = useApi<any>('/plans')
  const [form, setForm] = useState<AnyRecord>({ name: '', price: '', enrollment_fee: 0, billing_interval_months: 1, description: '', active: true })
  async function submit(event: FormEvent) { event.preventDefault(); await csrf(); await api.post('/plans', form); reload() }
  async function deactivate(id: number) { await csrf(); await api.delete(`/plans/${id}`); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <FormCard title="Novo plano" onSubmit={submit}><TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} /><TextInput label="Preço" value={form.price} onChange={(price) => setForm({ ...form, price })} type="number" /><TextInput label="Matrícula" value={form.enrollment_fee} onChange={(enrollment_fee) => setForm({ ...form, enrollment_fee })} type="number" /><SelectInput label="Intervalo" value={form.billing_interval_months} onChange={(billing_interval_months) => setForm({ ...form, billing_interval_months })}><option value="1">Mensal</option><option value="3">Trimestral</option><option value="6">Semestral</option><option value="12">Anual</option></SelectInput><div className="md:col-span-2"><button className="btn-primary">Salvar plano</button></div></FormCard>
      <div className="grid gap-3 xl:grid-cols-2">{data.plans.map((plan: AnyRecord) => <article className="card p-5" key={plan.id}><div className="flex justify-between gap-4"><div><h2 className="font-black">{plan.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{money(plan.price)} · {plan.memberships_count} matrículas</p></div><button className="btn-secondary" onClick={() => deactivate(plan.id)}>Desativar</button></div></article>)}</div>
    </>
  )
}

export function MembershipsPage() {
  const { data, loading, error, reload } = useApi<any>('/memberships')
  const [form, setForm] = useState<AnyRecord>({ member_id: '', plan_id: '', starts_at: new Date().toISOString().slice(0, 10), billing_day: 10, notes: '' })
  async function submit(event: FormEvent) { event.preventDefault(); await csrf(); await api.post('/memberships', form); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <FormCard title="Nova matrícula" onSubmit={submit}><SelectInput label="Aluno" value={form.member_id} onChange={(member_id) => setForm({ ...form, member_id })}><option value="">Selecione</option>{data.members.map((member: AnyRecord) => <option key={member.id} value={member.id}>{member.name}</option>)}</SelectInput><SelectInput label="Plano" value={form.plan_id} onChange={(plan_id) => setForm({ ...form, plan_id })}><option value="">Selecione</option>{data.plans.map((plan: AnyRecord) => <option key={plan.id} value={plan.id}>{plan.name}</option>)}</SelectInput><TextInput label="Início" value={form.starts_at} onChange={(starts_at) => setForm({ ...form, starts_at })} type="date" /><TextInput label="Dia de vencimento" value={form.billing_day} onChange={(billing_day) => setForm({ ...form, billing_day })} type="number" /><div className="md:col-span-2"><button className="btn-primary">Criar matrícula</button></div></FormCard>
      <div className="grid gap-3 xl:grid-cols-2">{data.memberships.data.map((membership: AnyRecord) => <Link className="card block p-5" to={`/matriculas/${membership.id}`} key={membership.id}><h2 className="font-black">{membership.member?.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{membership.plan?.name} · {membership.status}</p></Link>)}</div>
    </>
  )
}

export function MembershipDetailPage() {
  const { id } = useParams()
  const { data, loading, error, reload } = useApi<any>(`/memberships/${id}`)
  async function cancel() { await csrf(); await api.patch(`/memberships/${id}/cancel`); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  const membership = data.membership
  return <div className="space-y-4"><section className="card p-5"><div className="flex justify-between gap-4"><div><h2 className="text-xl font-black">{membership.member?.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{membership.plan?.name} · vencimento dia {membership.billing_day}</p></div>{membership.status === 'active' && <button className="btn-danger" onClick={cancel}>Cancelar</button>}</div></section>{membership.charges.map((charge: AnyRecord) => <article className="card p-5" key={charge.id}><h3 className="font-black">{charge.description}</h3><p className="text-sm text-slate-500 dark:text-neutral-400">Vence em {date(charge.due_date)} · {charge.status}</p><p className="mt-3 text-xl font-black">{money(charge.total)}</p></article>)}</div>
}

export function FinancePage() {
  const { data, loading, error, reload } = useApi<any>('/finance')
  const [payment, setPayment] = useState<AnyRecord>({ amount: '', paid_at: new Date().toISOString().slice(0, 16), method: 'pix', notes: '' })
  async function pay(charge: AnyRecord) { await csrf(); await api.post(`/charges/${charge.id}/payments`, { ...payment, amount: payment.amount || charge.outstanding || charge.amount }); setPayment({ ...payment, amount: '' }); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <div className="space-y-5"><div className="grid gap-4 md:grid-cols-3"><div className="card p-5"><p className="text-sm">A receber</p><p className="text-2xl font-black">{money(data.summary.open_amount)}</p></div><div className="card p-5"><p className="text-sm">Em atraso</p><p className="text-2xl font-black">{money(data.summary.overdue_amount)}</p></div><div className="card p-5"><p className="text-sm">Recebido</p><p className="text-2xl font-black">{money(data.summary.received_this_month)}</p></div></div>{data.charges.data.map((charge: AnyRecord) => <article className="card p-5" key={charge.id}><div className="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between"><div><h2 className="font-black">{charge.membership?.member?.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{charge.description} · {date(charge.due_date)} · {charge.status}</p><p className="mt-2 text-lg font-black">{money(charge.outstanding ?? charge.amount)}</p></div>{charge.status !== 'paid' && charge.status !== 'cancelled' && <div className="grid gap-2 sm:grid-cols-4"><input className="field" placeholder="Valor" value={payment.amount} onChange={(e) => setPayment({ ...payment, amount: e.target.value })} /><input className="field" type="datetime-local" value={payment.paid_at} onChange={(e) => setPayment({ ...payment, paid_at: e.target.value })} /><select className="field" value={payment.method} onChange={(e) => setPayment({ ...payment, method: e.target.value })}><option value="pix">Pix</option><option value="cash">Dinheiro</option><option value="credit_card">Crédito</option><option value="debit_card">Débito</option><option value="bank_transfer">Transferência</option></select><button className="btn-primary" onClick={() => pay(charge)}>Receber</button></div>}</div></article>)}</div>
  )
}

export function BillingPage() {
  const { data, loading, error } = useApi<any>('/my-billing')
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return <div className="space-y-5"><section className="rounded-2xl bg-ink-950 p-6 text-white"><p className="text-sm text-brand-500">Resumo financeiro</p><h2 className="mt-2 text-3xl font-black">{money(data.open_amount)}</h2><p className="text-sm text-slate-400">Total pendente</p></section>{data.memberships.length === 0 ? <Empty>Você ainda não possui matrícula.</Empty> : data.memberships.map((membership: AnyRecord) => <section key={membership.id}><h2 className="mb-3 font-black">{membership.plan?.name}</h2><div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">{membership.charges.map((charge: AnyRecord) => <article className="card p-5" key={charge.id}><h3 className="font-black">{charge.description}</h3><p className="text-sm text-slate-500 dark:text-neutral-400">Vence em {date(charge.due_date)}</p><p className="mt-4 text-xl font-black">{money(charge.total)}</p><p className="text-sm font-bold text-red-600">Aberto: {money(charge.outstanding)}</p></article>)}</div></section>)}</div>
}

export function PermissionsPage() {
  const { data, loading, error, reload } = useApi<any>('/permission-groups')
  const [form, setForm] = useState<AnyRecord>({ name: '', description: '', permissions: [] })
  const allPermissions = useMemo(() => Object.entries(data?.catalog ?? {}).flatMap(([group, items]: [string, any]) => Object.entries(items).map(([key, label]) => ({ group, key, label }))), [data])
  async function submit(event: FormEvent) { event.preventDefault(); await csrf(); await api.post('/permission-groups', form); setForm({ name: '', description: '', permissions: [] }); reload() }
  if (loading) return <Loading />
  if (error) return <Alert tone="error">{error}</Alert>
  return (
    <>
      <form className="card mb-6 p-5" onSubmit={submit}><div className="grid gap-4 md:grid-cols-2"><TextInput label="Nome" value={form.name} onChange={(name) => setForm({ ...form, name })} /><TextInput label="Descrição" value={form.description} onChange={(description) => setForm({ ...form, description })} /></div><div className="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">{allPermissions.map((permission) => <label className="flex items-center gap-2 text-sm" key={permission.key}><input type="checkbox" checked={form.permissions.includes(permission.key)} onChange={(e) => setForm({ ...form, permissions: e.target.checked ? [...form.permissions, permission.key] : form.permissions.filter((key: string) => key !== permission.key) })} />{String(permission.label)}</label>)}</div><button className="btn-primary mt-4">Criar grupo</button></form>
      <div className="grid gap-3 xl:grid-cols-2">{data.groups.map((group: AnyRecord) => <article className="card p-5" key={group.id}><h2 className="font-black">{group.name}</h2><p className="text-sm text-slate-500 dark:text-neutral-400">{group.description}</p><p className="mt-2 text-xs font-bold text-brand-700">{group.permissions.length} permissões · {group.users_count} usuários</p></article>)}</div>
    </>
  )
}
