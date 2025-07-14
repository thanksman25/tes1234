import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// Impor Layouts (Hanya satu layout utama yang baru)
import DefaultLayout from '@/layouts/DefaultLayout.vue';

// Impor Views (Halaman)
import LoginView from '@/views/auth/LoginView.vue';
import RegisterView from '@/views/auth/RegisterView.vue';
import VerificationHandler from '@/views/auth/VerificationHandler.vue';

// User Views
import UserDashboardView from '@/views/user/UserDashboardView.vue';
import ProfileView from '@/views/user/ProfileView.vue';
import CalculatorView from '@/views/user/CalculatorView.vue';
import RecapListView from '@/views/user/RecapListView.vue';
import RecapDetailView from '@/views/user/RecapDetailView.vue';
import FormulaSubmissionView from '@/views/user/FormulaSubmissionView.vue';

// Admin Views
import AdminDashboardView from '@/views/admin/AdminDashboardView.vue';
import SubmissionListView from '@/views/admin/SubmissionListView.vue';
import UserManagementView from '@/views/admin/UserManagementView.vue';


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Rute Publik & Otentikasi
    { path: '/login', name: 'login', component: LoginView },
    { path: '/register', name: 'register', component: RegisterView },
    { path: '/verification-success', name: 'verification-success', component: VerificationHandler, props: { success: true } },
    { path: '/verification-failure', name: 'verification-failure', component: VerificationHandler, props: { success: false } },


    // Semua rute yang butuh login sekarang menggunakan satu layout utama
    {
      path: '/',
      component: DefaultLayout, // Menggunakan satu layout utama
      meta: { requiresAuth: true },
      children: [
        // Rute Pengguna Biasa
        { path: '', name: 'dashboard', component: UserDashboardView },
        { path: 'profile', name: 'profile', component: ProfileView },
        { path: 'calculator', name: 'calculator', component: CalculatorView },
        { path: 'recapitulations', name: 'recap-list', component: RecapListView },
        { path: 'recapitulations/:id', name: 'recap-detail', component: RecapDetailView, props: true },
        { path: 'submit-formula', name: 'submit-formula', component: FormulaSubmissionView },

        // Rute Admin (dengan path 'admin/' di depannya)
        { path: 'admin/dashboard', name: 'admin-dashboard', component: AdminDashboardView, meta: { requiresAdmin: true } },
        { path: 'admin/submissions', name: 'admin-submissions', component: SubmissionListView, meta: { requiresAdmin: true } },
        { path: 'admin/users', name: 'admin-users', component: UserManagementView, meta: { requiresAdmin: true } },
      ],
    },

     // Fallback route - jika halaman tidak ditemukan
     {
      path: '/:pathMatch(.*)*',
      redirect: '/',
    }
  ],
});

// Navigation Guard (Petugas Keamanan)
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Coba ambil data user jika ada token di localStorage tapi belum ada di state
  // Ini penting untuk menjaga status login saat halaman di-refresh
  if (authStore.token && !authStore.isAuthenticated) {
    await authStore.fetchUser();
  }

  // Cek meta 'requiresAdmin' terlebih dahulu untuk rute admin
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    // Jika user bukan admin, lempar ke dashboard user
    next({ name: 'dashboard' });
  }
  // Cek meta 'requiresAuth'
  else if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Jika butuh login dan user belum login, lempar ke halaman login
    next({ name: 'login' });
  } 
  // Jika tidak ada masalah, izinkan akses
  else {
    next();
  }
});

export default router;