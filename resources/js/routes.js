import Dashboard from "./components/admin/Dashboard.vue";
import AppointmentList from "./components/admin/appointments/AppointmentList.vue";
import UserList from "./components/admin/users/UserList.vue";
import UpdateProfile from "./components/admin/users/UpdateProfile.vue";
import UpdateSetting from "./components/admin/settings/UpdateSetting.vue";

export default [
    {
        path: "/admin/dashboard",
        name: "admin.dashboard",
        component: Dashboard,
    },
    {
        path: "/admin/appointments",
        name: "admin.appointment",
        component: AppointmentList,
    },
    {
        path: "/admin/users",
        name: "admin.user",
        component: UserList,
    },
    {
        path: "/admin/settings",
        name: "admin.setting",
        component: UpdateSetting,
    },
    {
        path: "/admin/profile",
        name: "admin.profile",
        component: UpdateProfile,
    },
];
