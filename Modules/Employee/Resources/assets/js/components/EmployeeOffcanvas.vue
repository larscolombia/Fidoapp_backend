<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="form-offcanvas"
      aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-md-8">
            <div class="row">
              <InputField class="col-md-6" :is-required="true" :label="$t('employee.lbl_first_name')" placeholder=""
                v-model="first_name" :error-message="errors['first_name']"
                :error-messages="errorMessages['first_name']"></InputField>
              <InputField class="col-md-6" :is-required="true" :label="$t('employee.lbl_last_name')" placeholder=""
                v-model="last_name" :error-message="errors['last_name']" :error-messages="errorMessages['last_name']">
              </InputField>

              <InputField class="col-md-6" :is-required="true" :label="$t('employee.lbl_Email')" placeholder=""
                v-model="email" :error-message="errors['email']" :error-messages="errorMessages['email']"></InputField>
              <div class="form-group col-md-6">
                <label class="form-label">{{ $t('employee.lbl_phone_number') }}<span class="text-danger">*</span>
                </label>
                <vue-tel-input :value="mobile" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15 }"
                  class="form-control"></vue-tel-input>
                <span class="text-danger">{{ errors['mobile'] }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 text-center">
            <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-120 avatar-rounded mb-2" />
            <div class="d-flex align-items-center justify-content-center gap-2">
              <input type="file" ref="logoInputRef" class="form-control d-none" id="logo" name="profile_image"
                accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
              <label class="btn btn-soft-primary" for="logo">{{ $t('employee.upload') }}</label>
              <input type="button" class="btn btn-soft-danger" name="remove" :value="$t('messages.remove')"
                @click="removeLogo()" v-if="ImageViewer" />
            </div>
            <span class="text-danger">{{ errors.profile_image }}</span>
          </div>
          <div class="row m-0 p-0" v-if="currentId === 0">
            <InputField type="password" class="col-md-6" :is-required="true" :label="$t('employee.lbl_password')"
              placeholder="" v-model="password" :error-message="errors['password']"
              :error-messages="errorMessages['password']"></InputField>

            <InputField type="password" class="col-md-6" :is-required="true"
              :label="$t('employee.lbl_confirm_password')" placeholder="" v-model="confirm_password"
              :error-message="errors['confirm_password']" :error-messages="errorMessages['confirm_password']">
            </InputField>
          </div>

          <div class="form-group col-md-4">
            <label for="" class="form-label w-100">{{ $t('employee.lbl_gender') }}</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male"
                :checked="gender == 'male'" />
              <label class="form-check-label" for="male"> {{ $t('customer.lbl_male') }} </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female"
                :checked="gender == 'female'" />
              <label class="form-check-label" for="female"> {{ $t('customer.lbl_female') }} </label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other"
                :checked="gender == 'other'" />
              <label class="form-check-label" for="other"> {{ $t('customer.lbl_other') }} </label>
            </div>
            <p class="mb-0 text-danger">{{ errors.gender }}</p>
          </div>

          <!-- <div class="form-group m-0 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" :true-value="1" :false-value="0"
                  v-model="show_in_calender" id="show-in-calender" :checked="show_in_calender">
                <label class="form-check-label" for="show-in-calender">
                  {{ $t('employee.lbl_show_in_calender') }}
                </label>
              </div>
            </div>

            <div class="form-group m-0 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" :true-value="1" :false-value="0" v-model="is_manager"
                  id="is-manager" :checked="is_manager">
                <label class="form-check-label" for="is-manager">
                  {{ $t('employee.lbl_is_manager') }}
                </label>
              </div>
            </div> -->

          <!-- <div class="form-group col-md-12">
              <label class="form-label" for="commission_id">{{ $t('branch.lbl_select_service') }}</label>
              <Multiselect v-model="commission_id" :value="commission_id" :options="commissions.options" :multiple="true" v-bind="multiselectOption" id="commission_id"></Multiselect>
            </div> -->

          <div class="form-group col-md-12">
            <label class="form-label" for="commission_id"> {{ $t('employee.lbl_select_commission') }} </label><span
              class="text-danger">*</span>
            <Multiselect id="commission_id" v-model="commission_id" :value="commission_id"
              :placeholder="$t('employee.lbl_select_commission')" v-bind="multiselectOption"
              :options="commissions.options" class="form-group"></Multiselect>
            <span v-if="errorMessages['commission_id']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['commission_id']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.commission_id }}</span>
          </div>

          <div class="col-md-6 form-group" v-if="type == 'staff'">
            <label class="form-label" for="user_type">{{ $t('employee.lbl_select_user_type') }} {{ type.value }}</label>
            <span class="text-danger">*</span>
            <select class="form-select" :placeholder="$t('employee.lbl_select_user_type')" v-model="user_type">
              <option value="vet">{{ $t('employee.vet') }}</option>
              <option value="trainer">{{ $t('employee.trainer') }}</option>
              <option value="groomer">{{ $t('employee.groomer') }}</option>
              <option value="walker">{{ $t('employee.walker') }}</option>
              <option value="boarder">{{ $t('employee.boarder') }}</option>
              <option value="day_taker">{{ $t('employee.day_taker') }}</option>
              <option value="pet_sitter">{{ $t('employee.pet_sitter') }}</option>
            </select>
            <span v-if="errorMessages['user_type']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['user_type']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.user_type }}</span>
          </div>

          <div class="col-md-6 form-group d-none" v-if="type != 'staff'">
            <label class="form-label" for="user_type">{{ $t('employee.lbl_select_user_type') }} </label>
            <select class="form-select" :placeholder="$t('employee.lbl_select_user_type')" v-model="user_type"
              :disabled="user_type != 'staff'">
              <option value="vet">{{ $t('employee.vet') }}</option>
              <option value="trainer">{{ $t('employee.trainer') }}</option>
              <option value="groomer">{{ $t('employee.groomer') }}</option>
              <option value="walker">{{ $t('employee.walker') }}</option>
              <option value="boarder">{{ $t('employee.boarder') }}</option>
              <option value="day_taker">{{ $t('employee.day_taker') }}</option>
              <option value="pet_sitter">{{ $t('employee.pet_sitter') }}</option>
            </select>
            <span v-if="errorMessages['user_type']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['user_type']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.user_type }}</span>
          </div>
          <div class="col-md-6 form-group">
            <label class="form-label" for="branch">{{ $t('employee.lbl_select_branch') }}</label><span
              class="text-danger">*</span>
            <Multiselect id="branch_id" v-model="branch_id" :value="branch_id"
              :placeholder="$t('employee.lbl_select_branch')" v-bind="singleSelectOption" :options="branch.options"
              @select="branchSelect" class="form-group"> </Multiselect>
            <span v-if="errorMessages['branch_id']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['branch_id']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.branch_id }}</span>
          </div>

          <!-- <div class="form-group">
              <label class="form-label" for="branch">{{ $t('employee.lbl_select_branch') }}</label><span class="text-danger">*</span>
              <Multiselect id="branch_id" v-model="branch_id" :value="branch_id" placeholder="Select Branch"
                v-bind="singleSelectOption" :options="branch.options" @select="branchSelect" class="form-group">
              </Multiselect>
              <span v-if="errorMessages['branch_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['branch_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.branch_id }}</span>
            </div> -->

          <div class="form-group" v-if="type == 'groomer' || type == 'vet'">
            <label class="form-label" for="service">{{ $t('employee.lbl_select_service') }}</label>
            <Multiselect id="service_id" v-model="service_id" :multiple="true" :value="service_id"
              :placeholder="$t('employee.lbl_select_service')" v-bind="multiSelectOption" :options="services.options"
              class="form-group"> </Multiselect>
            <span v-if="errorMessages['service_id']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['service_id']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.service_id }}</span>
          </div>
          <div v-for="field in customefield" :key="field.id">
            <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type"
              :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
          </div>

          <InputField class="col-md-6" :label="$t('employee.lbl_about_self')" placeholder="" v-model="about_self"
            :error-message="errors['about_self']" :error-messages="errorMessages['about_self']"></InputField>
          <InputField class="col-md-6" :label="$t('employee.lbl_expert')" placeholder="" v-model="expert"
            :error-message="errors['expert']" :error-messages="errorMessages['expert']"></InputField>
          <InputField class="col-md-6" :label="$t('employee.lbl_facebook_link')" placeholder="" v-model="facebook_link"
            :error-message="errors['facebook_link']" :error-messages="errorMessages['facebook_link']"></InputField>
          <InputField class="col-md-6" :label="$t('employee.lbl_instagram_link')" placeholder=""
            v-model="instagram_link" :error-message="errors['instagram_link']"
            :error-messages="errorMessages['instagram_link']"></InputField>
          <InputField class="col-md-6" :label="$t('employee.lbl_twitter_link')" placeholder="" v-model="twitter_link"
            :error-message="errors['twitter_link']" :error-messages="errorMessages['twitter_link']"></InputField>
          <InputField class="col-md-6" :label="$t('employee.lbl_dribbble_link')" placeholder="" v-model="dribbble_link"
            :error-message="errors['dribbble_link']" :error-messages="errorMessages['dribbble_link']"></InputField>
          <!-- nuevos campos -->
          <InputField class="col-md-6" :label="$t('Título profesional')" placeholder="" v-model="professional_title"
            :error-message="errors['professional_title']" :error-messages="errorMessages['professional_title']">
          </InputField>
          <InputField class="col-md-6" :label="$t('Número de validación')" placeholder="" v-model="validation_number"
            :error-message="errors['validation_number']" :error-messages="errorMessages['validation_number']">
          </InputField>
          <div class="col-md-6 form-group">
            <label for="speciality_id">Seleccione una Especialidad:</label>
            <select class="form-control" id="speciality_id" name="speciality_id" v-model="speciality_id">
              <option value="" disabled selected>Seleccione una especialidad</option>
            </select>

          </div>
          <!-- fin nuevos campos -->
          <div class="form-group col-md-12">
            <label class="form-label" for="address">{{ $t('booking.lbl_address') }}</label>
            <textarea class="form-control" v-model="address" id="address"></textarea>
            <span v-if="errorMessages['address']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['address']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <div class="text-danger">{{ errors.address }}</div>
          </div>

          <InputField class="col-md-6" :label="$t('employee.latitude')" placeholder="" v-model="latitude"
            :error-message="errors['latitude']" :error-messages="errorMessages['latitude']"></InputField>
          <InputField class="col-md-6" :label="$t('employee.longitude')" placeholder="" v-model="longitude"
            :error-message="errors['longitude']" :error-messages="errorMessages['longitude']"></InputField>

          <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label mb-0" for="category-status">{{ $t('employee.lbl_status') }}</label>
              <div class="form-check form-switch">
                <input class="form-check-input" :value="1" name="status" id="category-status" type="checkbox"
                  v-model="status" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <FormFooter></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, BRANCH_LIST, SERVICE_LIST, COMMISSION_LIST, SPECIALITIES_URL } from '../constant/employee'
import { useField, useForm } from 'vee-validate'

import { VueTelInput } from 'vue3-tel-input'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'
import axios from 'axios';
let translations = {};
const defaultTranslations = {
  required: 'Este campo es obligatorio.',
  string: 'Este campo debe ser una cadena.',
  email: 'Este campo debe ser un correo electrónico válido.',
  min: 'Este campo debe tener al menos :min caracteres.',
  confirmed: 'La confirmación no coincide.',
  not_especial: 'No se permiten caracteres especiales.',
  only_digits: 'El campo debe contener solo dígitos.',
  first_strings_are_allowed: 'Se permiten las primeras cadenas.',
};

// Función para cargar las traducciones
async function loadTranslations() {
  // Intentar cargar desde localStorage
  const storedTranslations = localStorage.getItem('translations');

  if (storedTranslations) {
    translations = JSON.parse(storedTranslations);
    console.log('Cargadas traducciones desde localStorage:', translations);
    return; // Salir si ya tenemos traducciones
  }

  try {
    const response = await axios.get('/api/translations');
    translations = response.data;

    // Almacenar en localStorage
    localStorage.setItem('translations', JSON.stringify(translations));
    console.log('Cargadas traducciones desde el servidor:', translations);
  } catch (error) {
    console.error('Error loading translations:', error);
    // Si hay un error, usar los mensajes por defecto
    translations = defaultTranslations;
  }
}

// Llamar a la función para cargar las traducciones
loadTranslations();
function getTranslation(key, default_min = null, default_max = null) {
  // Intenta obtener las traducciones del localStorage
  const storedTranslations = localStorage.getItem('translations');

  if (storedTranslations) {
    const translationsFromStorage = JSON.parse(storedTranslations);
    // Devuelve la traducción correspondiente si existe
    if (translationsFromStorage[key]) {
      if (default_min !== null) {
        translationsFromStorage[key].replace(':min', default_min);
      }
      if (default_max !== null) {
        translationsFromStorage[key].replace(':max', default_max);
      }
      return translationsFromStorage[key].replace(':attribute', '');
    }
  }

  // Si no se encuentra, devolvemos el mensaje por defecto
  return defaultTranslations[key] || `Missing translation for ${key}`;
}
// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  type: { type: String, default: () => 'staff' },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' },
  customefield: { type: Array, default: () => [] }
})

// Select Options
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  select: 1
})
const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true,
  createOption: true
})

const multiselectOption = ref({
  mode: 'tags',
  searchable: true,
  multiple: true
})

const branch = ref({ options: [], list: [] })
const commissions = ref({ options: [], list: [] })
const services = ref({ options: [], list: [] })

const { getRequest, storeRequest, updateRequest } = useRequest()

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  useSelect({ url: BRANCH_LIST }, { value: 'id', label: 'name' }).then((data) => (branch.value = data))
  useSelect({ url: COMMISSION_LIST }, { value: 'id', label: 'name' }).then((data) => (commissions.value = data))
  useSelect({ url: SPECIALITIES_URL }, { value: 'id', label: 'name' }).then((data) => (speciality_id.value = data))
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status && res.data) {
        setFormData(res.data)
        branchSelect()
      }
    })
  } else {
    setFormData(defaultData())
    branchSelect()
  }
})

onMounted(() => {
  setFormData(defaultData())
  loadSpecialities();
})
const loadSpecialities = async () => {
  try {
    const response = await fetch(SPECIALITIES_URL().path, {
      method: SPECIALITIES_URL().method,
    });
    if (!response.ok) throw new Error('Network response was not ok');

    const result = await response.json();
    console.log(result); // Verifica que los datos sean correctos

    // Asegúrate de que result.data sea un array
    if (!Array.isArray(result.data)) {
      console.error('La respuesta no contiene un array en data:', result);
      return; // Salir si no es un array
    }

    const select = document.getElementById('speciality_id');

    // Llenar el select con las especialidades
    result.data.forEach(speciality => {
      const option = document.createElement('option');
      option.value = speciality.id; // Asignar el ID como valor
      option.textContent = speciality.description; // Asignar la descripción como texto
      select.appendChild(option); // Agregar la opción al select
    });
  } catch (error) {
    console.error('Error fetching specialities:', error);
  }
};


const branchSelect = () => {
  useSelect({ url: SERVICE_LIST, data: { branch_id: branch_id.value } }, { value: 'id', label: 'name' }).then((data) => (services.value = data))
}

// File Upload Function
const ImageViewer = ref(null)

const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64

    logoInputRef.value.value = ''
  })
  changeFile.value = file
}
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

const changeLogo = (e) => fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: profile_image })
const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: profile_image })

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    id: '',
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    password: '',
    confirm_password: '',
    gender: 'male',
    password: '',
    profile_image: '',
    status: 1,
    branch_id: 1,
    service_id: [],
    commission_id: [],
    show_in_calender: 1,
    is_manager: 0,
    about_self: '',
    expert: '',
    facebook_link: '',
    instagram_link: '',
    twitter_link: '',
    dribbble_link: '',
    address: '',
    latitude: '',
    longitude: '',
    user_type: props.type,
    professional_title: '',
    validation_number: '',
    speciality_id: '',
    custom_fields_data: {}
  }
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.profile_image


  resetForm({
    values: {
      id: data.id,
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.mobile,
      password: data.password,
      confirm_password: data.confirm_password,
      gender: data.gender,
      profile_image: data.profile_image,
      branch_id: data.branch_id,
      service_id: data.service_id,
      commission_id: data.commission_id,
      status: data.status ? true : false,
      show_in_calender: data.show_in_calender,
      is_manager: data.is_manager,
      custom_fields_data: data.custom_field_data,
      about_self: data.about_self,
      expert: data.expert,
      facebook_link: data.facebook_link,
      instagram_link: data.instagram_link,
      twitter_link: data.twitter_link,
      dribbble_link: data.dribbble_link,
      user_type: data.user_type,
      address: data.address,
      latitude: data.latitude,
      longitude: data.longitude,
      professional_title: data.professional_title,
      validation_number: data.validation_number,
      speciality_id: data.speciality_id
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}
const numberRegex = /^\d+$/
const EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/
// Validations

const validationSchema = yup.object({
  first_name: yup
    .string()
    .required(getTranslation('required'))
    .test('is-string', getTranslation('not_special'), (value) => {
      // Regular expressions to disallow special characters and numbers
      const specialCharsRegex = /[!@#$%^&*(),?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  last_name: yup
    .string()
    .required(getTranslation('required'))
    .test('is-string', getTranslation('not_special'), (value) => {
      // Regular expressions to disallow special characters and numbers
      const specialCharsRegex = /[!@#$%^&*(),?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  email: yup
    .string()
    .required(getTranslation('required'))
    .test('is-string', getTranslation('first_strings_are_allowed'), (value) => !numberRegex.test(value))
    .matches(EMAIL_REGX, getTranslation('email')),
  mobile: yup
    .string()
    .required(getTranslation('required'))
    .matches(/^(\+?\d+)?(\s?\d+)*$/, getTranslation('only_digits')),
  password: yup
    .string()
    .test('password', getTranslation('required'), function (value) {
      if (currentId === 0 && !value) {
        return false
      }
      return true
    })
    .min(8, getTranslation('required', 8)),
  confirm_password: yup
    .string()
    .test('confirm_password', getTranslation('required'), function (value) {
      if (currentId === 0 && !value) {
        return false
      }
      return true
    })
    .oneOf([yup.ref('password')], getTranslation('confirmed')),
  commission_id: yup.array().required(getTranslation('required')),
  branch_id: yup.string().required(getTranslation('required')),

  user_type: yup.string().test('user_type', getTranslation('required'), function (value) {
    if (this.parent.user_type === 'staff') {
      return false
    }
    return true
  })

  // user_type: yup.string()
  // .required('User Type is a required field'),
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: id } = useField('id')
const { value: first_name } = useField('first_name')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const { value: gender } = useField('gender')
const { value: mobile } = useField('mobile')
const { value: branch_id } = useField('branch_id')
const { value: status } = useField('status')
const { value: service_id } = useField('service_id')
const { value: commission_id } = useField('commission_id')
const { value: profile_image } = useField('profile_image')
const { value: show_in_calender } = useField('show_in_calender')
const { value: is_manager } = useField('is_manager')
const { value: custom_fields_data } = useField('custom_fields_data')
const { value: about_self } = useField('about_self')
const { value: expert } = useField('expert')
const { value: facebook_link } = useField('facebook_link')
const { value: instagram_link } = useField('instagram_link')
const { value: twitter_link } = useField('twitter_link')
const { value: dribbble_link } = useField('dribbble_link')
const { value: user_type } = useField('user_type')
const { value: address } = useField('address')
const { value: latitude } = useField('latitude')
const { value: longitude } = useField('longitude')
const { value: professional_title } = useField('professional_title')
const { value: validation_number } = useField('validation_number')
const { value: speciality_id } = useField('speciality_id')

const errorMessages = ref({})

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted
  }
}

// Form Submit
const formSubmit = handleSubmit((values) => {
  values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>

<style scoped>
@media only screen and (min-width: 768px) {
  .offcanvas {
    width: 80%;
  }
}

@media only screen and (min-width: 1280px) {
  .offcanvas {
    width: 60%;
  }
}
</style>
