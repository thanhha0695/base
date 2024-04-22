<template>
    <b-sidebar
        id="add-new-user-sidebar"
        :visible="isPopupActive"
        bg-variant="white"
        sidebar-class="sidebar-lg"
        shadow
        backdrop
        no-header
        right
        @hidden="resetForm"
        @change="(val) => { $emit('update:is-popup-active', val); }"
    >
        <template #default="{ hide }">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
                <h5 class="mb-0">
                    {{ text }} Role
                </h5>

                <feather-icon
                    class="ml-1 cursor-pointer"
                    icon="XIcon"
                    size="16"
                    @click="hide"
                />

            </div>

            <!-- BODY -->
            <validation-observer
                #default="{ handleSubmit }"
                ref="refFormObserver"
            >
                <!-- Form -->
                <b-form
                    class="p-2"
                    @submit.prevent="handleSubmit(onsubmit)"
                    @reset.prevent="resetForm"
                >

                    <!-- name -->
                    <validation-provider
                        #default="validationContext"
                        name="Name"
                        rules="required"
                    >
                        <b-form-group
                            label="Name"
                            label-for="full-name"
                        >
                            <b-form-input
                                id="full-name"
                                v-model="data.name"
                                autofocus
                                :state="getValidationState(validationContext)"
                                trim
                                placeholder="Administrator"
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Manage -->
                    <validation-provider
                        #default="validationContext"
                        name="Manage"
                        rules="required"
                    >
                        <b-form-group
                            label="Manage"
                            label-for="manage"
                            :state="getValidationState(validationContext)"
                        >
                            <v-select
                                v-model="data.manageId"
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="manageOptions"
                                :reduce="val => val.value"
                                :clearable="false"
                                input-id="manage"
                            />
                            <b-form-invalid-feedback :state="getValidationState(validationContext)">
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Parent Role -->
                    <validation-provider
                        v-if="!isUpdate && !isPrivilege"
                        #default="validationContext"
                        name="Parent Role"
                    >
                        <b-form-group
                            label="Parent Role"
                            label-for="parent-role"
                            :state="getValidationState(validationContext)"
                        >
                            <v-select
                                v-model="data.parentId"
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="parentRoleOptions"
                                :reduce="val => val.value"
                                :clearable="false"
                                input-id="parent-role"
                            />
                            <b-form-invalid-feedback :state="getValidationState(validationContext)">
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Description -->
                    <validation-provider
                        #default="validationContext"
                        name="Description"
                    >
                        <b-form-group
                            label="Description"
                            label-for="description"
                        >
                            <b-form-input
                                id="description"
                                v-model="data.description"
                                :state="getValidationState(validationContext)"
                                trim
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            variant="primary"
                            class="mr-2"
                            type="submit"
                        >
                            {{ text }}
                        </b-button>
                        <b-button
                            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            type="button"
                            variant="outline-secondary"
                            @click="hide"
                        >
                            Cancel
                        </b-button>
                    </div>

                </b-form>
            </validation-observer>
        </template>
    </b-sidebar>
</template>

<script>
import {
    BSidebar, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { ref } from '@vue/composition-api'
import { required } from '@validations'
import formValidation from '@core/comp-functions/forms/form-validation'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import callApi from '@/axios'

export default {
    name: 'Popup-Role',
    components: {
        BSidebar,
        BForm,
        BFormGroup,
        BFormInput,
        BFormInvalidFeedback, BButton,
        vSelect,
        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    props: {
        isPopupActive: {
            type: Boolean,
            required: true
        },
        parentRoleOptions: {
            type: Array,
            default: [],
        },
        manageOptions: {
            type: Array,
            required: true,
        },
        role: {
            type: Object,
            required: true
        },
        isUpdate: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            required,
        }
    },
    setup(props, { emit }) {
        let blankData = props.role
        let isUpdate = props.isUpdate
        const data = ref(JSON.parse(JSON.stringify(blankData)))
        const resetUserData = () => {
            data.value = JSON.parse(JSON.stringify(blankData))
        }
        let uri = 'manage/roles/store'
        let method = 'post'
        if (isUpdate) {
            const userId = blankData.id
            uri = `manage/roles/${ userId }/update`
            method = 'put'
        }
        const onsubmit = () => {
            callApi.request({
                method: method,
                url: uri,
                data: {
                    name: data.value.name,
                    manage_id: data.value.manageId,
                    description: data.value.description,
                    parent_id: data.value.parentId,
                }
            }).then((response) => {
                response.isUpdate = isUpdate
                emit('update:is-popup-active', false)
                emit('update-data', response)
            }).catch((error) => {})
        }
        const {
            refFormObserver,
            getValidationState,
            resetForm,
        } = formValidation(resetUserData)
        const text = isUpdate ? 'Update' : 'Add'
        return {
            refFormObserver,
            getValidationState,
            resetForm,
            data,
            onsubmit,
            text
        }
    },
}
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';

    #add-new-user-sidebar {
        .vs__dropdown-menu {
            max-height: 200px !important;
        }
    }
</style>
