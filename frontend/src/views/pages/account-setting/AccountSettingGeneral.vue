<template>
  <b-card>

    <!-- media -->
    <b-media no-body>
      <b-media-aside>
        <b-avatar
          ref="previewEl"
          size="80px"
          :src="userForm.avatar !== '' ? userForm.avatar : ''"
        ></b-avatar>
        <!--/ avatar -->
      </b-media-aside>

      <b-media-body class="mt-75 ml-75">
        <!-- upload button -->
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          variant="primary"
          size="sm"
          class="mb-75 mr-75"
          @click="$refs.refInputEl.$el.click()"
        >
          Upload
        </b-button>
        <b-form-file
          ref="refInputEl"
          v-model="profileFile"
          accept=".jpg, .png, .gif, .jpeg"
          :hidden="true"
          plain
          @input="imageRenderer"
        />
        <!--/ upload button -->

        <!-- reset -->
        <b-button
          v-ripple.400="'rgba(186, 191, 199, 0.15)'"
          variant="outline-secondary"
          size="sm"
          class="mb-75 mr-75"
          @click="resetAvatar"
        >
          Reset
        </b-button>
        <!--/ reset -->
        <b-card-text>Allowed JPG, JPEG, GIF or PNG. Max size of 800kB</b-card-text>
      </b-media-body>
    </b-media>
    <!--/ media -->

    <!-- form -->
    <b-form class="mt-2">
      <b-row>
        <b-col sm="6">
          <b-form-group
            label="Username: "
            label-for="account-username"
          >
            <b-form-input
              v-model="userForm.username"
              name="username"
              disabled
            />
          </b-form-group>
        </b-col>
        <b-col sm="6">
          <b-form-group
            label="Email: "
            label-for="account-email"
          >
            <b-form-input
              v-model="userForm.email"
              name="email"
              disabled
            />
          </b-form-group>
        </b-col>
        <b-col sm="6">
          <b-form-group
            label="Name: "
            label-for="account-name"
          >
            <b-form-input
              v-model="userForm.name"
              name="name"
              placeholder="Name"
            />

          </b-form-group>
        </b-col>
        <b-col sm="6">
          <b-form-group
            label="Contact: "
            label-for="account-contact"
          >
            <b-form-input
              v-model="userForm.phoneNumber"
              name="contact"
              placeholder="Contact"
            />
          </b-form-group>
        </b-col>
        <b-col sm="6">
          <b-form-group
            label="Gender: "
            label-for="account-gender"
          >
            <b-form-radio-group
              v-model="userForm.gender"
              :options="genderOptions"
              name="gender-radio"
            ></b-form-radio-group>
          </b-form-group>
        </b-col>
        <b-col sm="6">
          <b-form-group
            label="Birthday: "
            label-for="account-birthday"
          >
            <datepicker
              input-class="form-control"
              format="yyyy-MM-dd"
              v-model="userForm.birthday"
              placeholder="Choose a date"
              clear-button-icon
            >

            </datepicker>
          </b-form-group>
        </b-col>

        <b-col cols="12">
          <b-button
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            variant="primary"
            class="mt-2 mr-1"
            @click.prevent="submit"
          >
            Save changes
          </b-button>
          <b-button
            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
            variant="outline-secondary"
            type="reset"
            class="mt-2"
            @click.prevent="resetForm"
          >
            Reset
          </b-button>
        </b-col>
      </b-row>
    </b-form>
  </b-card>
</template>

<script>
import {
  BFormFile, BButton, BForm, BFormGroup, BFormInput, BRow, BCol, BAlert, BCard, BCardText, BMedia,
  BMediaAside, BMediaBody, BFormRadioGroup, BFormDatepicker, BAvatar, BLink,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import Datepicker from 'vuejs-datepicker'
import callApi from "@/axios"

export default {
  components: {
    BButton,
    BForm,
    BFormFile,
    BFormGroup,
    BFormInput,
    BRow,
    BCol,
    BAlert,
    BCard,
    BCardText,
    BMedia,
    BMediaAside,
    BMediaBody,
    BFormRadioGroup,
    BFormDatepicker,
    BAvatar,
    BLink,
    Datepicker,
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      profileFile: null,
      genderOptions: [
        { text: 'male', value: 1 },
        { text: 'female', value: 0 },
        { text: 'other', value: 9 }
      ],
      userAuth: {}
    }
  },
  computed: {
    userForm: {
      get() {
        this.userAuth = JSON.parse(JSON.stringify(this.userAuthenticate))
        return this.userAuth
      },
      set(val) {
        this.userAuth = val
      }
    }
  },
  methods: {
    resetForm() {
      this.userForm = this.userAuthenticate
    },
    resetAvatar() {
      this.userForm.avatar = this.userAuthenticate.avatar
      this.profileFile = null
    },
    imageRenderer(inputFile) {
      if (inputFile) {
        this.profileFile = inputFile
        this.userForm.avatar = URL.createObjectURL(inputFile)
      }
    },
    submit() {
      const formData = new FormData()
      const avatar = this.isNotEmpty(this.profileFile) ? this.profileFile : this.userForm.avatar
      formData.append('username', this.userForm.username)
      formData.append('email', this.userForm.email)
      formData.append('gender', this.userForm.gender)
      formData.append('name', this.userForm.name)
      formData.append('birthday', this.userForm.birthday)
      formData.append('avatar', avatar)
      formData.append('contact', this.userForm.phoneNumber)
      callApi.post('profile/update', formData).then((response) => {
        this.$store.dispatch('user/updateProfile', response)
      }).catch((error) => {})
    }
  },
}
</script>
