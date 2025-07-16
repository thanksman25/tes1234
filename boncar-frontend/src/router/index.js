// boncar-frontend/src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

import DefaultLayout from '@/layouts/DefaultLayout.vue';
import LoginView from '@/views/auth/LoginView.vue';
import RegisterView from '@/views/auth/RegisterView.vue';
import VerificationHandler from '@/views/auth/VerificationHandler.vue';

// User Views
import UserDashboardView from '@/views/user/UserDashboardView.vue';
import ProfileView from '@/views/user/ProfileView.vue';
import FormulaSubmissionView from '@/views/user/FormulaSubmissionView.vue';
import RecapListView from '@/views/user/RecapListView.vue';
import RecapDetailView from '@/views/user/RecapDetailView.vue';

// Calculator Views
import CalculatorView from '@/views/user/CalculatorView.vue';
import TreeInputView from '@/views/user/TreeInputView.vue'; // <-- BARU
import CalculatorResultsView from '@/views/user/CalculatorResultsView.vue'; // <-- BARU

// Admin Views
import AdminDashboardView from '@/views/admin/AdminDashboardView.vue';
import SubmissionListView from '@/views/admin/SubmissionListView.vue';
import UserManagementView from '@/views/admin/UserManagementView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'login', component: LoginView },
    { path: '/register', name: 'register', component: RegisterView },
    { path: '/verification-success', name: 'verification-success', component: VerificationHandler, props: { success: true } },
    { path: '/verification-failure', name: 'verification-failure', component: VerificationHandler, props: { success: false } },

    {
      path: '/',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'dashboard', component: UserDashboardView },
        { path: 'profile', name: 'profile', component: ProfileView },
        { path: 'submit-formula', name: 'submit-formula', component: FormulaSubmissionView },
        { path: 'recapitulations', name: 'recap-list', component: RecapListView },
        { path: 'recapitulations/:id', name: 'recap-detail', component: RecapDetailView, props: true },
        
        // Rute Kalkulator yang Baru
        { path: 'calculator', name: 'calculator', component: CalculatorView },
        { path: 'calculator/trees', name: 'tree-input', component: TreeInputView },
        { path: 'calculator/results', name: 'calculator-results', component: CalculatorResultsView },

        // Rute Admin
        { path: 'admin/dashboard', name: 'admin-dashboard', component: AdminDashboardView, meta: { requiresAdmin: true } },
        { path: 'admin/submissions', name: 'admin-submissions', component: SubmissionListView, meta: { requiresAdmin: true } },
        { path: 'admin/users', name: 'admin-users', component: UserManagementView, meta: { requiresAdmin: true } },
      ],
    },
     { path: '/:pathMatch(.*)*', redirect: '/' }
  ],
});

// Navigation Guard (tetap sama)
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  if (authStore.token && !authStore.isAuthenticated) {
    await authStore.fetchUser();
  }
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'dashboard' });
  } else if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
  } else {
    next();
  }
});

export default router;