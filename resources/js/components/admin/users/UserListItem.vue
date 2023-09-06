<script setup>

    import axios from 'axios';
    import { ref } from 'vue';
    import { useToastr } from '../../../toastr.js';

    const props = defineProps({
        user: Object,
        index: Number,
        selectAll: Boolean,
    });

    const toastr = useToastr();
    const emit = defineEmits(['userDeleted','editUser','toggleSelection']);
    const userIdDeleted = ref(null);

    const roles = ref([
        {
            name: 'ADMIN',
            value: 1
        },
        {
            name: 'USER',
            value: 2,
        }
    ]);

    const deleteUser = (user) => {
        userIdDeleted.value = user.id;
        $('#deleteUserModal').modal('show');
    }

    const deleteConfirmUser = () => {
        axios.delete(`/api/users/${userIdDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            // users.value = users.value.filter(user => user.id !== userIdDeleted.value);
            toastr.success('User deleted successful');
            emit('userDeleted', userIdDeleted.value);
        })
    }

    const changeRole = (user, role) => {
        axios.patch(`/api/users/${user.id}/change_role`,{
            role: role
        }).then(()=>{
            toastr.success("Role changed successfully");
        });
    }

    const toggleSelection = () => {
         emit('toggleSelection', props.user);
    }

</script>

<template>

    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>
             <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="(user.role === role.name)">
                    {{ role.name }}
                </option>
            </select>
        </td>
        <td>{{ user.created_at }}</td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)">
                <i class="fa fa-edit"></i>
            </a>
            <a href="#" @click.prevent="deleteUser(user)">
                <i class="fa fa-trash text-danger ml-2"></i>
            </a>
        </td>
    </tr>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteConfirmUser" type="button" class="btn btn-primary">Delete User</button>
                </div>
            </div>
        </div>
    </div>

</template>