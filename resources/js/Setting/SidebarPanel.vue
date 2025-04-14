<template>
  <div class="col-md-4 col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="list-group list-group-flush customizer-panel">
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.home' }" class="btn btn-border"><i class="fas fa-cube"></i>{{ $t('setting_general_page.general') }}</router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.misc' }" class="btn btn-border"><i class="fa-solid fa-screwdriver-wrench"></i>{{ $t('setting_general_page.misc_settings') }}</router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.quick-booking' }" class="btn btn-border"
              ><i class="fa-solid fa-paper-plane"></i>
              {{ $t('setting_general_page.quick_booking') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.invoice-setting' }" class="btn btn-border"
              ><i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
              {{ $t('setting_general_page.invoice_setting') }}
            </router-link>
          </div>
          <!-- <div class="mb-3" @click="toggle()"><router-link :to="{name: 'Settings.social-profile'}" class="btn btn-border"><i class="fas fa-users"></i>Social Profile</router-link></div>
          <div class="mb-3" @click="toggle()"><router-link :to="{name: 'Settings.meta'}" class="btn btn-border"><i class="fas fa-globe-asia"></i>Meta</router-link></div> -->
          <!-- <div class="mb-3" @click="toggle()"><router-link :to="{name: 'Settings.analitics'}" class="btn btn-border"><i class="fas fa-chart-line"></i>Analytics</router-link></div> -->
          <!-- <div class="mb-3" @click="toggle()"><router-link :to="{name: 'Settings.custom-code'}" class="btn btn-border"><i class="fa-solid fa-file-code"></i>Custom Code</router-link></div> -->
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.custom-code' }" class="btn btn-border"
              ><i class="fa-solid fa-file-code"></i>
              {{ $t('setting_general_page.custom_code') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.customization' }" class="btn btn-border"
              ><i class="fa-solid fa-swatchbook"></i>
              {{ $t('setting_general_page.customization') }}
            </router-link>
          </div>
          <!-- <div class="mb-3" @click="toggle()"><router-link :to="{name: 'Settings.mobile'}" class="btn btn-border"><i class="fa-solid fa-mobile-screen-button"></i>Mobile</router-link></div> -->
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.booking-configuration' }" class="btn btn-border"
              ><i class="fa-solid fa-paper-plane"></i>

              {{ $t('setting_general_page.booking_configuration') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.mail' }" class="btn btn-border"
              ><i class="fas fa-envelope"></i>
              {{ $t('setting_general_page.mail_setting') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.notificationsetting' }" class="btn btn-border"
              ><i class="fa-solid fa-bullhorn"></i>
              {{ $t('setting_general_page.notification_setting') }}
            </router-link>
          </div>
          <div v-if="role() == 'admin'" class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.integration' }" class="btn btn-border"
              ><i class="fa-solid fa-sliders"></i>
              {{ $t('setting_general_page.integration') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.other-settings' }" class="btn btn-border"
              ><i class="fa-solid fa-sliders"></i>

              {{ $t('setting_general_page.app_configuration_settings') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.custom-fields' }" class="btn btn-border"
              ><i class="fa-solid fa-bars"></i>
              {{ $t('setting_general_page.custom_fields') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.currency-settings' }" class="btn btn-border"
              ><i class="fa fa-dollar fa-lg mr-2"></i>
              {{ $t('setting_general_page.currency_setting') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.commission' }" class="btn btn-border"
              ><i class="fa fa-percent fa-lg mr-2"></i>
              {{ $t('setting_general_page.commission') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.holiday' }" class="btn btn-border"
              ><i class="fa-solid fa-calendar-check mr-2"></i>
              {{ $t('setting_general_page.holiday') }}
            </router-link>
          </div>
          <!-- <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.bussiness-hours' }" class="btn btn-border"
              ><i class="fa-solid fa-clock"></i>
              {{ $t('setting_general_page.business_hours') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.grooming-bussiness-hours' }" class="btn btn-border"
              ><i class="fa-solid fa-clock"></i>
              {{ $t('setting_general_page.grooming_business_hours') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.training-bussiness-hours' }" class="btn btn-border"
              ><i class="fa-solid fa-clock"></i>
              {{ $t('setting_general_page.training_business_hours') }}
            </router-link>
          </div> -->
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.payment-method' }" class="btn btn-border"
              ><i class="fa-solid fa-coins"></i>
              {{ $t('setting_general_page.payment_method') }}
            </router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.language-settings' }" class="btn btn-border"
              ><i class="fa fa-language" aria-hidden="true"></i>
              {{ $t('setting_general_page.language_settings') }}
            </router-link>
          </div>
          <!-- coin -->
          <!-- <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'Settings.fidocoin' }" class="btn btn-border"
              ><i class="fa fa-ils" aria-hidden="true"></i>
              {{ $t('setting_general_page.fidocoin') }}
            </router-link>
          </div> -->
          <!--end coin-->
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const toggle = () => {
  const formOffcanvas = document.getElementById('offcanvas')
  formOffcanvas.classList.add('show')
}
const hasPermissions = (name) => {
  return window.auth_permissions.includes(name)
}
const role = () => {
  return window.auth_role[0]
}
</script>
<style scoped>
.btn-border {
  text-align: left;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
</style>
