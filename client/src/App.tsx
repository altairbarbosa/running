import { Navigate, Route, Routes, useLocation } from 'react-router-dom'
import { Layout } from './components/Layout'
import { Loading } from './components/ui'
import { hasPermission } from './services/api'
import { useAuth } from './services/auth'
import { FirstAccessPage, ForgotPasswordPage, LoginPage, ResetPasswordPage } from './pages/AuthPages'
import {
  BillingPage,
  DashboardPage,
  ExercisesPage,
  FinancePage,
  MembersPage,
  MembershipDetailPage,
  MembershipsPage,
  PermissionsPage,
  PlansPage,
  ProfilePage,
  ShopPage,
  UsersPage,
  WorkoutDetailPage,
  WorkoutFormPage,
  WorkoutsPage,
} from './pages/AppPages'

function RequireAuth({ children, permission, memberOnly }: { children: React.ReactNode; permission?: string; memberOnly?: boolean }) {
  const { user, loading } = useAuth()
  const location = useLocation()

  if (loading) return <Loading />
  if (!user) return <Navigate to="/login" replace state={{ from: location.pathname }} />
  if (user.must_change_password && location.pathname !== '/primeiro-acesso') return <Navigate to="/primeiro-acesso" replace />
  if (memberOnly && user.role !== 'member') return <Navigate to="/financeiro" replace />
  if (permission && !hasPermission(user, permission)) return <Navigate to="/dashboard" replace />

  return children
}

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route path="/esqueci-minha-senha" element={<ForgotPasswordPage />} />
      <Route path="/redefinir-senha" element={<ResetPasswordPage />} />
      <Route path="/primeiro-acesso" element={<RequireAuth><FirstAccessPage /></RequireAuth>} />
      <Route path="/" element={<RequireAuth><Layout /></RequireAuth>}>
        <Route index element={<Navigate to="/dashboard" replace />} />
        <Route path="dashboard" element={<RequireAuth permission="dashboard.view"><DashboardPage /></RequireAuth>} />
        <Route path="perfil" element={<ProfilePage />} />
        <Route path="alunos" element={<RequireAuth permission="members.view"><MembersPage /></RequireAuth>} />
        <Route path="exercicios" element={<RequireAuth permission="exercises.manage"><ExercisesPage /></RequireAuth>} />
        <Route path="treinos" element={<RequireAuth permission="workouts.view"><WorkoutsPage /></RequireAuth>} />
        <Route path="treinos/novo" element={<RequireAuth permission="workouts.manage"><WorkoutFormPage /></RequireAuth>} />
        <Route path="treinos/:id" element={<RequireAuth permission="workouts.view"><WorkoutDetailPage /></RequireAuth>} />
        <Route path="loja" element={<RequireAuth permission="shop.view"><ShopPage /></RequireAuth>} />
        <Route path="minhas-mensalidades" element={<RequireAuth permission="billing.view-own" memberOnly><BillingPage /></RequireAuth>} />
        <Route path="planos" element={<RequireAuth permission="plans.manage"><PlansPage /></RequireAuth>} />
        <Route path="matriculas" element={<RequireAuth permission="memberships.manage"><MembershipsPage /></RequireAuth>} />
        <Route path="matriculas/:id" element={<RequireAuth permission="memberships.manage"><MembershipDetailPage /></RequireAuth>} />
        <Route path="financeiro" element={<RequireAuth permission="billing.manage"><FinancePage /></RequireAuth>} />
        <Route path="usuarios" element={<RequireAuth permission="users.manage"><UsersPage /></RequireAuth>} />
        <Route path="permissoes" element={<RequireAuth permission="permissions.manage"><PermissionsPage /></RequireAuth>} />
      </Route>
      <Route path="*" element={<Navigate to="/dashboard" replace />} />
    </Routes>
  )
}
