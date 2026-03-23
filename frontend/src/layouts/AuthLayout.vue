<template>
  <v-main style="background: linear-gradient(135deg, #0F1C35 0%, #1A2744 50%, #2C3E6B 100%); min-height: 100vh;">
    <v-container fluid class="fill-height">
      <v-row justify="center" align="center">
        <v-col cols="12" sm="8" md="5" lg="4">
          <div class="text-center mb-8">
            <div class="text-h4 font-weight-bold text-white">OLDKHIVA</div>
            <div class="text-caption" style="color: #C8941A; letter-spacing: 3px;">RESTAURANT</div>
          </div>
          <slot />
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
