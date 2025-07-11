// boncar-frontend/src/shims-vue.d.ts

// File ini memberitahu TypeScript cara menginterpretasikan file .vue.
// Ini akan menyelesaikan error "Cannot find module './App.vue'".
declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}
