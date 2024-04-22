<template>
  <div class="login-form">
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="8" md="6">
          <v-card>
            <v-card-title class="text-center">Login</v-card-title>
            <v-card-text>
              <v-form @submit.prevent="login">
                <v-text-field v-model="username" label="Username or Email"></v-text-field>
                <v-text-field v-model="password" label="Password" type="password"></v-text-field>
                <v-btn type="submit" color="primary" class="mr-4">Login</v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script setup>
import { useRouter } from 'vue-router';
const router = useRouter(); 

if (typeof localStorage !== 'undefined' && localStorage.getItem('token')) {
  const token = localStorage.getItem('token');
    
  console.log(token);
  if (token || token !== "") {
    router.push('/tasks');
  }
}
</script>

<script>
import gql from 'graphql-tag'

export default {
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    async login() {
      try {
        const LOGIN_MUTATION = gql`
          mutation Login($username: String!, $password: String!) {
            login(email: $username, password: $password)
          }
        `;

        const { data } = await this.$apollo.mutate({
          mutation: LOGIN_MUTATION,
          variables: {
            username: this.username,
            password: this.password
          }
        });

        const token = data.login;

        localStorage.setItem('token', token);

        console.log('Logged in successfully!');
        this.$router.push('/tasks');
      } catch (error) {
        console.error('Error logging in:', error);
      }
    }
  }
};
</script>

<style scoped>
.login-form {
  margin-top: 50px;
}
</style>
