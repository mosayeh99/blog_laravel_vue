import Dashboard from "@/components/Dashboard.vue";
import Users from "@/pages/user/Users.vue";

export default [
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {
        path: '/',
        name: 'admin.users',
        component: Users,
    },
]
