import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    component: () => import('@/views/landing/LandingPage.vue'),
    meta: { layout: 'landing' }
  },
  {
    path: '/login',
    component: () => import('@/views/auth/LoginPage.vue'),
    meta: { layout: 'auth' }
  },
  {
    path: '/register',
    component: () => import('@/views/auth/RegisterPage.vue'),
    meta: { layout: 'auth' }
  },
  {
    path: '/client',
    meta: { layout: 'client', requiresAuth: true, role: 'client' },
    children: [
      { path: '', component: () => import('@/views/client/Dashboard.vue') },
      { path: 'contract', component: () => import('@/views/client/ContractPage.vue') },
      { path: 'menu', component: () => import('@/views/client/MenuPage.vue') },
      { path: 'bookings', component: () => import('@/views/client/BookingsList.vue') },
      { path: 'bookings/new', component: () => import('@/views/client/NewBooking.vue') },
      { path: 'bookings/:id', component: () => import('@/views/client/BookingDetail.vue') },
      { path: 'invoices', component: () => import('@/views/client/InvoicesList.vue') },
      { path: 'invoices/:id', component: () => import('@/views/client/InvoiceDetail.vue') },
      { path: 'payments', component: () => import('@/views/client/PaymentHistory.vue') },
    ]
  },
  {
    path: '/admin',
    meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    children: [
      { path: '', component: () => import('@/views/admin/Dashboard.vue') },
      { path: 'clients', component: () => import('@/views/admin/ClientsList.vue') },
      { path: 'clients/:id', component: () => import('@/views/admin/ClientDetail.vue') },
      { path: 'contracts', component: () => import('@/views/admin/ContractManage.vue') },
      { path: 'menu', component: () => import('@/views/admin/MenuManage.vue') },
      { path: 'services', component: () => import('@/views/admin/Services.vue') },
      { path: 'bookings', component: () => import('@/views/admin/BookingsList.vue') },
      { path: 'bookings/:id', component: () => import('@/views/admin/BookingDetail.vue') },
      { path: 'invoices', component: () => import('@/views/admin/InvoicesList.vue') },
      { path: 'payments', component: () => import('@/views/admin/PaymentsList.vue') },
      { path: 'reports', component: () => import('@/views/admin/Reports.vue') },
      { path: 'landing', component: () => import('@/views/admin/LandingSettings.vue') },
    ]
  },
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Child routelar parent meta sini to.matched orqali oladi
  const requiresAuth = to.matched.some(r => r.meta.requiresAuth)
  const routeRole = to.matched.find(r => r.meta.role)?.meta.role

  if (requiresAuth && !auth.isAuthenticated) {
    return '/login'
  }

  if (auth.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    return auth.isAdmin ? '/admin' : '/client'
  }

  if (routeRole && auth.user?.role !== routeRole) {
    return auth.isAdmin ? '/admin' : '/client'
  }

  if (routeRole === 'client' && auth.isAuthenticated) {
    if (!auth.user?.contract_agreed && to.path !== '/client/contract') {
      return '/client/contract'
    }
  }
})

export default router
