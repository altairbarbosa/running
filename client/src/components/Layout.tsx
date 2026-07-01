import {
  Activity,
  BadgeDollarSign,
  Boxes,
  ChevronDown,
  Dumbbell,
  Home,
  LogOut,
  Menu,
  Monitor,
  Moon,
  User,
  Receipt,
  ShieldCheck,
  ShoppingBag,
  Sun,
  UserCog,
  Users,
  X,
} from 'lucide-react'
import { useState } from 'react'
import { NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom'
import { hasPermission, storageUrl } from '../services/api'
import { useAuth } from '../services/auth'
import { useTheme } from '../services/theme'
import type { ThemePreference } from '../services/theme'

const links = [
  { to: '/dashboard', label: 'Visão geral', icon: Home, permission: 'dashboard.view' },
  { to: '/alunos', label: 'Alunos', icon: Users, permission: 'members.view' },
  { to: '/exercicios', label: 'Exercícios', icon: Activity, permission: 'exercises.manage' },
  { to: '/treinos', label: 'Treinos', icon: Dumbbell, permission: 'workouts.view' },
  { to: '/loja', label: 'Loja', icon: ShoppingBag, permission: 'shop.view' },
  { to: '/minhas-mensalidades', label: 'Mensalidades', icon: Receipt, permission: 'billing.view-own', memberOnly: true },
  { to: '/planos', label: 'Planos', icon: Boxes, permission: 'plans.manage' },
  { to: '/matriculas', label: 'Matrículas', icon: BadgeDollarSign, permission: 'memberships.manage' },
  { to: '/financeiro', label: 'Cobranças', icon: Receipt, permission: 'billing.manage' },
  { to: '/usuarios', label: 'Usuários', icon: UserCog, permission: 'users.manage' },
  { to: '/permissoes', label: 'Permissões', icon: ShieldCheck, permission: 'permissions.manage' },
]

const titles: Record<string, string> = {
  '/dashboard': 'Visão geral',
  '/alunos': 'Alunos',
  '/exercicios': 'Exercícios',
  '/treinos': 'Treinos',
  '/treinos/novo': 'Elaborar treino',
  '/loja': 'Loja',
  '/minhas-mensalidades': 'Minhas mensalidades',
  '/planos': 'Planos',
  '/matriculas': 'Matrículas',
  '/financeiro': 'Cobranças',
  '/usuarios': 'Usuários',
  '/permissoes': 'Permissões',
  '/perfil': 'Perfil',
}

export function Layout() {
  const { user, logout } = useAuth()
  const { theme, setTheme } = useTheme()
  const navigate = useNavigate()
  const location = useLocation()
  const [open, setOpen] = useState(false)
  const [accountMenu, setAccountMenu] = useState(false)

  const title = titles[location.pathname] ?? (location.pathname.startsWith('/treinos/') ? 'Detalhes do treino' : location.pathname.startsWith('/matriculas/') ? 'Detalhes da matrícula' : 'Running')

  async function handleLogout() {
    await logout()
    setAccountMenu(false)
    navigate('/login', { replace: true })
  }

  const avatar = user?.avatar_url ? (
    <img src={storageUrl(user.avatar_url) ?? ''} alt={user.name} className="size-10 rounded-full object-cover" />
  ) : (
    <span className="grid size-10 place-items-center rounded-full bg-brand-500 text-sm font-black text-ink-950">{user?.initials || 'R'}</span>
  )

  const themeOptions: { value: ThemePreference; label: string; icon: typeof Monitor }[] = [
    { value: 'system', label: 'Padrão', icon: Monitor },
    { value: 'light', label: 'Claro', icon: Sun },
    { value: 'dark', label: 'Escuro', icon: Moon },
  ]

  const sidebar = (
    <aside className={`fixed inset-y-0 left-0 z-40 flex w-[min(19rem,88vw)] flex-col bg-ink-950 text-white shadow-2xl transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:translate-x-0 lg:shadow-none ${open ? 'translate-x-0' : '-translate-x-full'}`}>
      <div className="flex h-20 items-center justify-between border-b border-white/10 px-5">
        <div className="flex items-center gap-3">
          <div className="grid size-10 place-items-center rounded-xl bg-brand-500 font-black text-ink-950">R</div>
          <div>
            <p className="text-sm font-black">Running</p>
            <p className="text-xs text-slate-500 dark:text-neutral-400">Studio</p>
          </div>
        </div>
        <button className="grid size-10 place-items-center rounded-xl text-slate-400 hover:bg-white/5 hover:text-white lg:hidden" onClick={() => setOpen(false)} aria-label="Fechar menu">
          <X className="size-5" />
        </button>
      </div>
      <nav className="flex-1 space-y-1 overflow-y-auto p-3 text-sm">
        {links
          .filter((link) => hasPermission(user, link.permission) && (!link.memberOnly || user?.role === 'member'))
          .map((link) => {
            const Icon = link.icon
            return (
              <NavLink key={link.to} to={link.to} onClick={() => setOpen(false)} className={({ isActive }) => `nav-link ${isActive ? 'nav-link-active' : 'nav-link-idle'}`}>
                <Icon className="size-5" />
                {link.label}
              </NavLink>
            )
          })}
      </nav>
    </aside>
  )

  return (
    <div className="min-h-screen lg:flex">
      {open && <div className="fixed inset-0 z-30 bg-ink-950/60 backdrop-blur-sm lg:hidden" onClick={() => setOpen(false)} />}
      {sidebar}
      <main className="min-w-0 flex-1 bg-slate-50 dark:bg-neutral-950">
        <header className="sticky top-0 z-20 border-b border-slate-200/80 bg-white/95 backdrop-blur dark:border-neutral-800 dark:bg-neutral-950/90">
          <div className="page-shell flex h-16 items-center justify-between gap-3 px-4 sm:h-20 sm:px-6 lg:px-8">
            <button className="grid size-10 shrink-0 place-items-center rounded-xl border border-slate-200 text-ink-950 dark:border-neutral-700 dark:text-white lg:hidden" onClick={() => setOpen(true)} aria-label="Abrir menu">
              <Menu className="size-5" />
            </button>
            <div className="min-w-0 flex-1">
              <p className="hidden text-[10px] font-bold uppercase tracking-[.2em] text-brand-600 sm:block">Painel Running</p>
              <h1 className="truncate text-lg font-black text-ink-950 dark:text-white sm:text-xl">{title}</h1>
            </div>
            <div className="relative shrink-0">
              <button
                type="button"
                className="flex items-center gap-2 rounded-full bg-brand-50 p-1 transition hover:bg-brand-100 focus:ring-4 focus:ring-brand-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:focus:ring-brand-500/20 sm:pr-3"
                onClick={() => setAccountMenu((current) => !current)}
                aria-expanded={accountMenu}
                aria-haspopup="menu"
                aria-label="Abrir menu da conta"
              >
                {avatar}
                <span className="hidden text-xs font-bold text-brand-700 dark:text-brand-100 sm:inline">
                  {user?.role === 'member' ? 'Aluno' : user?.role === 'trainer' ? 'Professor' : 'Administrador'}
                </span>
                <ChevronDown className={`hidden size-4 text-brand-700 transition dark:text-brand-100 sm:block ${accountMenu ? 'rotate-180' : ''}`} />
              </button>
              {accountMenu && (
                <>
                  <button className="fixed inset-0 z-30 cursor-default bg-transparent" aria-label="Fechar menu da conta" onClick={() => setAccountMenu(false)} />
                  <div role="menu" className="absolute right-0 top-full z-40 mt-2 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                    <div className="border-b border-slate-100 px-4 py-3 dark:border-neutral-800">
                      <p className="truncate text-sm font-bold text-ink-950 dark:text-white">{user?.name}</p>
                      <p className="truncate text-xs text-slate-500 dark:text-neutral-400">{user?.email}</p>
                    </div>
                    <div className="p-2">
                      <NavLink
                        to="/perfil"
                        role="menuitem"
                        className="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-brand-700 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:hover:text-brand-100"
                        onClick={() => setAccountMenu(false)}
                      >
                        <User className="size-4" />
                        Perfil
                      </NavLink>
                      <div className="my-2 border-t border-slate-100 pt-2 dark:border-neutral-800">
                        <p className="px-3 pb-2 text-[10px] font-bold uppercase tracking-[.18em] text-slate-400">Tema</p>
                        <div className="grid grid-cols-3 gap-1">
                          {themeOptions.map((option) => {
                            const Icon = option.icon
                            const active = theme === option.value
                            return (
                              <button
                                key={option.value}
                                type="button"
                                className={`flex min-h-16 flex-col items-center justify-center gap-1 rounded-xl border px-2 text-xs font-bold transition ${active ? 'border-brand-500 bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-100' : 'border-transparent text-slate-500 hover:bg-slate-50 hover:text-slate-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-slate-100'}`}
                                onClick={() => setTheme(option.value)}
                                aria-pressed={active}
                              >
                                <Icon className="size-4" />
                                {option.label}
                              </button>
                            )
                          })}
                        </div>
                      </div>
                      <button
                        role="menuitem"
                        className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                        onClick={handleLogout}
                      >
                        <LogOut className="size-4" />
                        Sair
                      </button>
                    </div>
                  </div>
                </>
              )}
            </div>
          </div>
        </header>
        <div className="page-shell p-4 pb-10 sm:p-6 lg:p-8 lg:pb-12">
          <Outlet />
        </div>
      </main>
    </div>
  )
}
