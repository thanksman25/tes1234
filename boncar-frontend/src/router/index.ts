import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/store/auth';

const routes = [
  // Rute Otentikasi
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginPage.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/RegisterPage.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/verification-success',
    name: 'VerificationSuccess',
    component: () => import('@/views/VerificationStatusPage.vue'),
  },
  {
    path: '/verification-failure',
    name: 'VerificationFailure',
    component: () => import('@/views/VerificationStatusPage.vue'),
  },

  // Rute Utama (Membutuhkan Login)
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('@/views/DashboardPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/views/ProfilePage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/new-submission',
    name: 'NewSubmission',
    component: () => import('@/views/NewSubmissionPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/recapitulation',
    name: 'RecapList',
    component: () => import('@/views/RecapListPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/recapitulation/:id',
    name: 'RecapDetail',
    component: () => import('@/views/RecapDetailPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/calculator/form',
    name: 'CalculatorForm',
    component: () => import('@/views/CalculatorFormPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/calculator/tree-input',
    name: 'TreeInput',
    component: () => import('@/views/TreeInputPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/calculator/results',
    name: 'CalculatorResults',
    component: () => import('@/views/CalculatorResultsPage.vue'),
    meta: { requiresAuth: true }
  },

  // Rute Khusus Admin
  {
    path: '/users',
    name: 'Users',
    component: () => import('@/views/UsersPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/alometric-verification',
    name: 'AlometricVerification',
    component: () => import('@/views/AlometricVerificationPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/submission/:id',
    name: 'SubmissionDetail',
    component: () => import('@/views/SubmissionDetailPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation Guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Jika store belum terisi, coba fetch user dulu
  if (authStore.user === null) {
    await authStore.fetchUser();
  }

  const isAuthenticated = authStore.isAuthenticated;
  const userRole = authStore.userRole;

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Jika butuh login tapi belum login, redirect ke login
    next({ name: 'Login' });
  } else if (to.meta.guestOnly && isAuthenticated) {
    // Jika halaman hanya untuk tamu (login/register) tapi sudah login, redirect ke dashboard
    next({ name: 'Dashboard' });
  } else if (to.meta.requiresAdmin && userRole !== 'admin') {
    // Jika butuh admin tapi bukan admin, redirect ke dashboard
    next({ name: 'Dashboard' });
  } else {
    // Jika semua oke, lanjutkan
    next();
  }
});

export default router;
