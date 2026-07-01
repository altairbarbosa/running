import axios, { AxiosError } from 'axios'

export type User = {
  id: number
  name: string
  email: string
  role: 'admin' | 'trainer' | 'member'
  phone?: string | null
  birth_date?: string | null
  address?: string | null
  active: boolean
  must_change_password: boolean
  permission_group_id?: number | null
  permissions: string[]
  avatar_url?: string | null
  initials: string
}

export type ApiError = {
  message: string
  errors?: Record<string, string[]>
}

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || ''
const API_URL = import.meta.env.VITE_API_URL || `${API_BASE_URL}/api`

export const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

export async function csrf() {
  await axios.get(`${API_BASE_URL}/sanctum/csrf-cookie`, {
    withCredentials: true,
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
  })
}

export function apiMessage(error: unknown): string {
  const axiosError = error as AxiosError<ApiError>
  return axiosError.response?.data?.message ?? 'Não foi possível concluir a operação.'
}

export function fieldErrors(error: unknown): Record<string, string[]> {
  const axiosError = error as AxiosError<ApiError>
  return axiosError.response?.data?.errors ?? {}
}

export function storageUrl(url?: string | null) {
  if (!url) return null
  if (url.startsWith('http://web/')) return url.replace('http://web', '')
  if (url.startsWith('http')) return url
  return API_BASE_URL ? `${API_BASE_URL}${url.startsWith('/') ? '' : '/'}${url}` : url
}

export function hasPermission(user: User | null, permission: string) {
  if (!user) return false
  if (user.permissions.includes('*')) return true
  if (user.permissions.includes(permission)) return true
  const impliedBy: Record<string, string> = {
    'members.view': 'members.manage',
    'workouts.view': 'workouts.manage',
    'shop.view': 'shop.manage',
  }
  return Boolean(impliedBy[permission] && user.permissions.includes(impliedBy[permission]))
}
