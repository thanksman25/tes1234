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

// Navigation Guard yang disempurnakan
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Jika state user belum ada (misal: saat refresh halaman), coba ambil dari server
  if (authStore.user === null) {
    await authStore.fetchUser();
  }
  
  const isAuthenticated = authStore.isAuthenticated;

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Jika halaman butuh login tapi pengguna belum login, arahkan ke halaman Login.
    next({ name: 'Login' });
  } else if (to.meta.guestOnly && isAuthenticated) {
    // Jika halaman login/register diakses oleh pengguna yang sudah login, arahkan ke Dashboard.
    next({ name: 'Dashboard' });
  } else if (to.meta.requiresAdmin && authStore.userRole !== 'admin') {
    // Jika halaman butuh admin tapi pengguna bukan admin, arahkan ke Dashboard.
    next({ name: 'Dashboard' });
  }
  else {
    // Jika semua kondisi terpenuhi, lanjutkan navigasi.
    next();
  }
});

export default router;