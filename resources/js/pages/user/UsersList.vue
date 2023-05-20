<template>
    <!-- Table | Users List -->
    <table class="table table-striped table-light">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Created At</th>
            <th scope="col">Role</th>
            <th scope="col">Options</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(user, i) in users.data">
            <th scope="row">{{ ++i }}</th>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.created_at }}</td>
            <td>
                <select class="form-control" name="role" @change="changeRole(user.id, $event.target.value)">
                    <option v-for="role in roles" :value="role.value" :selected="user.role === role.name">{{ role.name }}</option>
                </select>
            </td>
            <td>
                <a href="#" @click="$emit('editUser', user)" type="button" data-toggle="modal" data-target="#userFormModal">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="#" @click="userIdBeingDeleted = user.id" type="button" data-toggle="modal" data-target="#deleteUserModal">
                    <i class="fa fa-trash text-danger ml-3"></i>
                </a>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Modal | Delete User -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form @submit="deleteUser">
                    <div class="modal-body">
                        <div class="form-group">
                            Are you sure want delete this user?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import {Form, Field} from "vee-validate";
import axios from "axios";

// --------------------------Setup------------------------
defineProps(['users']);
const emit = defineEmits([]);
const userIdBeingDeleted = ref(null);
const roles = [
    {name: 'ADMIN', value: 1},
    {name: 'USER', value: 2},
];

// --------------Api Connection Methods-------------------
const deleteUser = () => {
    axios.delete(`/api/users/${userIdBeingDeleted.value}`)
        .then(() => {
            emit('userDeleted', userIdBeingDeleted.value);
            $('#deleteUserModal').modal('hide');
        })
}

const changeRole = (userId, role) => {
    axios.patch(`/api/users/role/${userId}`, { role: role })
        .then(() => toastr.success('Role Updated Successfully'))
        .catch(err => console.log(err.data))
}

</script>

<style scoped>

</style>
