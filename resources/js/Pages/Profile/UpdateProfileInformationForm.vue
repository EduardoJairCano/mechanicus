<template>
  <jet-form-section @submitted="updateProfileInformation">
    <template #title>
      Información de Perfil de Usuario
    </template>

    <template #description>
      Actualice la información de perfil y la dirección de correo electrónico de su cuenta.
    </template>

    <template #form>
      <!-- Profile Photo -->
      <div class="col-span-6 sm:col-span-4" v-if="$page.props.jetstream.managesProfilePhotos">
        <!-- Profile Photo File Input -->
        <input type="file" class="hidden" ref="photo" @change="updatePhotoPreview">

        <jet-label for="photo" value="Photo" />

        <!-- Current Profile Photo -->
        <div class="mt-2" v-show="! photoPreview">
          <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
        </div>

        <!-- New Profile Photo Preview -->
        <div class="mt-2" v-show="photoPreview">
          <span class="block rounded-full w-20 h-20"
                :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
          </span>
        </div>

        <jet-secondary-button class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
          Seleccionar Nueva Foto
        </jet-secondary-button>

        <jet-secondary-button type="button" class="mt-2" @click.prevent="deletePhoto" v-if="user.profile_photo_path">
          Eliminar Foto
        </jet-secondary-button>

        <jet-input-error :message="form.errors.photo" class="mt-2" />
      </div>

      <!-- Name -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="name" value="Nombre" />
        <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
        <jet-input-error :message="form.errors.name" class="mt-2" />
      </div>

      <!-- Email -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="email" value="Correo Electrónico" />
        <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
        <jet-input-error :message="form.errors.email" class="mt-2" />
      </div>

      <!-- Cell Phone Number -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="cell_phone_number" value="Teléfono Móvil" />
        <jet-input id="cell_phone_number" type="text" inputmode="numeric" class="mt-1 block w-full" v-model="form.cell_phone_number" />
        <jet-input-error :message="form.errors.email" class="mt-2" />
      </div>

      <!-- RFC -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="rfc" value="RFC" />
        <jet-input id="rfc" type="text" class="mt-1 block w-full" v-model="form.rfc" />
        <jet-input-error :message="form.errors.rfc" class="mt-2" />
      </div>
    </template>

    <template #actions>
      <jet-action-message :on="form.recentlySuccessful" class="mr-3">
        Actualizado...
      </jet-action-message>

      <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Actualizar
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
  import JetButton from '@/Jetstream/Button'
  import JetFormSection from '@/Jetstream/FormSection'
  import JetInput from '@/Jetstream/Input'
  import JetInputError from '@/Jetstream/InputError'
  import JetLabel from '@/Jetstream/Label'
  import JetActionMessage from '@/Jetstream/ActionMessage'
  import JetSecondaryButton from '@/Jetstream/SecondaryButton'

  export default {
    components: {
      JetActionMessage,
      JetButton,
      JetFormSection,
      JetInput,
      JetInputError,
      JetLabel,
      JetSecondaryButton,
    },

    props: ['user', 'userInfo'],

    data() {
      return {
        form: this.$inertia.form({
          _method: 'PUT',
          name: this.user.name,
          email: this.user.email,
          cell_phone_number: this.userInfo.cell_phone_number,
          rfc: this.userInfo.rfc,
          photo: null,
        }),

        photoPreview: null,
      }
    },

    methods: {
      updateProfileInformation() {
        if (this.$refs.photo) {
          this.form.photo = this.$refs.photo.files[0]
        }

        this.form.post(route('user-profile-information.update'), {
          errorBag: 'updateProfileInformation',
          preserveScroll: true,
          onSuccess: () => (this.clearPhotoFileInput()),
        });
      },

      selectNewPhoto() {
        this.$refs.photo.click();
      },

      updatePhotoPreview() {
        const photo = this.$refs.photo.files[0];

        if (! photo) return;

        const reader = new FileReader();

        reader.onload = (e) => {
          this.photoPreview = e.target.result;
        };

        reader.readAsDataURL(photo);
      },

      deletePhoto() {
        this.$inertia.delete(route('current-user-photo.destroy'), {
          preserveScroll: true,
          onSuccess: () => {
            this.photoPreview = null;
            this.clearPhotoFileInput();
          },
        });
      },

      clearPhotoFileInput() {
        if (this.$refs.photo?.value) {
          this.$refs.photo.value = null;
        }
      },
    },
  }
</script>
