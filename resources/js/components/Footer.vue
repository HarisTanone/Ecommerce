<template>
    <footer class="bg-white py-4 mt-8 shadow-md">
      <div class="container mx-auto text-center text-gray-600">
        <p>© {{ new Date().getFullYear() }} Your Brand. All rights reserved.</p>
        <p class="mt-2">Follow us on:</p>
        <div class="flex justify-center space-x-4 mt-2">
          <a href="#" class="text-gray-600 hover:text-green-500">Facebook</a>
          <a href="#" class="text-gray-600 hover:text-green-500">Twitter</a>
          <a href="#" class="text-gray-600 hover:text-green-500">Instagram</a>
        </div>
      </div>
    </footer>
  </template>

  <script>
  export default {
    name: 'FooterComponent',
  };
  </script>

  <style scoped>
  /* Gaya tambahan jika diperlukan */
  </style>
