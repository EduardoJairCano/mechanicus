<template>
  <div>
    <button
        type="submit"
        class="block w-full px-4 py-2 text-sm leading-5 text-white text-left hover:bg-blueMechanic800 focus:outline-none focus:bg-blueMechanic700 transition"
        v-if="as == 'button'">
      <slot></slot>
    </button>

    <a
        :href="href"
        class="block px-4 py-2 text-sm leading-5 text-white hover:bg-blueMechanic800 focus:outline-none focus:bg-blueMechanic700 transition"
        v-else-if="as =='a'">
      <slot></slot>
    </a>

    <inertia-link
        :href="href"
        class="block px-4 py-2 text-sm leading-5 text-white hover:bg-blueMechanic800 focus:outline-none focus:bg-blueMechanic700 transition"
        v-else>
      <slot></slot>
    </inertia-link>
  </div>
</template>

<script>
  export default {
    props: ['href', 'as']
  }
</script>
