import Dashboard from "./components/admin/Dashboard.vue";
import AppointmentList from "./components/admin/appointments/AppointmentList.vue";
import AppointmentForm from "./components/admin/appointments/AppointmentForm.vue";
import UserList from "./components/admin/users/UserList.vue";
import UpdateProfile from "./components/admin/users/UpdateProfile.vue";
import UpdateSetting from "./components/admin/settings/UpdateSetting.vue";
import Login from './components/auth/Login.vue';

export default [
    {
        path: "/login",
        name: "admin.login",
        component: Login,
    },
    {
        path: "/admin/dashboard",
        name: "admin.dashboard",
        component: Dashboard,
    },
    {
        path: "/admin/appointments",
        name: "admin.appointments",
        component: AppointmentList,
    },
    {
        path: "/admin/appointments/create",
        name: "admin.appointments.create",
        component: AppointmentForm,
    },
    {
        path: "/admin/appointments/:id/edit",
        name: "admin.appointments.edit",
        component: AppointmentForm,
    },
    {
        path: "/admin/users",
        name: "admin.users",
        component: UserList,
    },
    {
        path: "/admin/settings",
        name: "admin.settings",
        component: UpdateSetting,
    },
    {
        path: "/admin/profile",
        name: "admin.profile",
        component: UpdateProfile,
    },
];
