<template>
  <div class="navbar-container main-menu-content">
    <horizontal-nav-menu-items :items="navMenuItems" />
  </div>
</template>

<script>
import HorizontalNavMenuItems from './components/horizontal-nav-menu-items/HorizontalNavMenuItems.vue'

export default {
  components: {
    HorizontalNavMenuItems,
  },
  computed: {
    navMenuItems() {
      return this.sidebarMenu(true)
    }
  },
}
</script>

<style lang="scss">
@import "~@core/scss/base/core/menu/menu-types/horizontal-menu.scss";
</style>
