<template>
  <v-app>
    <v-app-bar>
      <v-toolbar-title>Vue ToDo List</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-chip class="counter-chip" outlined color="blue" dark>
        <span class="chip-text">Tasks</span>
        <span class="chip-number">{{ tasks_total }}</span>
      </v-chip>
      <v-chip class="counter-chip" outlined color="blue" dark>
        <span class="chip-text">Tasks Done</span>
        <span class="chip-number">{{ tasks_done }}</span>
      </v-chip>
      <v-btn 
        @click="taskDone" class="btn-style" v-if="selectedTasks.length > 0">
        <v-icon>mdi-delete</v-icon>
        Task Done
      </v-btn>
      <v-btn @click="taskAllDone" class="btn-style">
        <v-icon>mdi-delete</v-icon>
        Tasks
      </v-btn>
      <v-btn @click="logout">
        <v-icon>mdi-logout</v-icon>
        Logout
      </v-btn>
    </v-app-bar>
    
    <div class="list-container">
      <v-row>
        <v-col col="12">
          <v-list>
            <v-list-item v-for="(task, index) in tasks" :key="index">
              <v-list-item-content class="list-item-content">
                <v-checkbox 
                  @change="handleCheckboxChange(task)"
                  :model-value="getTaskStatus(task).done" 
                  :disabled="getTaskStatus(task).done" />
                <v-label>{{task.name}}</v-label>
                <v-btn icon @click="deleteTask(task)" class="delete-icon-btn">
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
    </div>
  </v-app>
</template>
<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

if (typeof localStorage !== 'undefined' && localStorage.getItem('token')) {
  const token = localStorage.getItem('token');
    
  if (token === null || token === "") {
    router.push('/');
  }
} else {
  router.push('/');
}
</script>

<script>
import gql from 'graphql-tag'
import { ref } from 'vue';

const tasks = ref([])

export default {
  async beforeCreate() {
    try {
      const LOGGED_USER_QUERY = gql`
        query {
          me {
            email
            tasks {
              id
              name
              status
            }
          }
        }
      `;

      const { data } = await this.$apollo.query({
        query: LOGGED_USER_QUERY,
        context: {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        }
      });
      tasks.value = data.me.tasks;

      this.tasks_total = tasks.value.length;
      this.tasks_done = tasks.value.filter(task => JSON.parse(task.status).done).length;

      console.log('Tasks fetched successfully:');
    } catch (error) {
      console.error('Error fetching tasks:', error);
    }
  },
  data() {
    return {
      tasks_total: 0,
      tasks_done: 0,
      selectedTasks: [],
    };
  },
  methods: {
    async taskDone() {
      try {
        for (const selectedTask of this.selectedTasks) {
          const taskId = selectedTask.id;
          const taskName = selectedTask.name;

          const UPDATE_TASK_MUTATION = gql`
            mutation UpdateTask($id: Int!, $name: String!, $status: JSON!) {
              updateTask(id: $id, name: $name, status: $status) {
                id
                name
                status
                user {
                  id
                  email
                }
              }
            }
          `;

          const { data } = await this.$apollo.mutate({
            mutation: UPDATE_TASK_MUTATION,
            variables: {
              id: taskId,
              name: taskName,
              status: JSON.stringify({ todo: false, done: true })
            },
            context: {
              headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
              }
            }
          });

          console.log(`Task "${taskName}" updated successfully!`);
          console.log('Updated Task:', data.updateTask);

          let tmpArray = [...tasks.value];
          const updatedTaskIndex = tmpArray.findIndex(task => task.id === taskId);
          if (updatedTaskIndex !== -1) {
            let updatedTask = {...tmpArray[updatedTaskIndex]};
            
            updatedTask.status = data.updateTask.status;
            tmpArray[updatedTaskIndex] = updatedTask;
            tasks.value = tmpArray;
          }
        }

        this.selectedTasks = [];
        this.updateTaskDoneCounter();
      } catch (error) {
        console.error('Error updating tasks:', error);
      }
    },
    taskAllDone() {
      // Handle Tasks button click
      console.log('Tasks button clicked');
    },
    handleCheckboxChange(task) {
      const taskId = task.id;
      const taskIndex = this.selectedTasks.findIndex(selectedTask => selectedTask.id === taskId);

      if (taskIndex === -1) {
        this.selectedTasks.push(task);
      } else {
        this.selectedTasks.splice(taskIndex, 1);
      }
    },
    updateTaskDoneCounter() {
      this.tasks_done = tasks.value.filter(task => this.getTaskStatus(task).done).length;
    },
    getTaskStatus(task) {
      return JSON.parse(task.status);
    },
    async logout () {
      try {
        const LOGOUT_MUTATION = gql`
          mutation {
            logout
          }
        `;

        const { data } = await this.$apollo.mutate({
          mutation: LOGOUT_MUTATION,
          context: {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          }
        });

        const isLogout = data.logout;

        if (isLogout) {
          localStorage.removeItem('token');

          this.$router.push("/");
          console.log('Logged out successfully!');
        }
      } catch (error) {
        console.error('Error logging out:', error);
      }
    },
    async deleteTask(task) {
      try {
        const DELETE_TASK_MUTATION = gql`
          mutation ($id: Int!) {
            deleteTask(id: $id) {
              id
              name
              status
              user {
                id
                email
              }
            }
          }
        `;

        const { data } = await this.$apollo.mutate({
          mutation: DELETE_TASK_MUTATION,
          variables: {
            id: task.id,
          },
          context: {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          }
        });

        console.log(`Task "${task.name}" deleted successfully!`);
        console.log('Deleted Task:', data.deleteTask);

        let tmpArray = [...tasks.value];
        const updatedTaskIndex = tmpArray.findIndex(task => task.id === data.deleteTask.id);
        if (updatedTaskIndex !== -1) {
          tmpArray.splice(updatedTaskIndex, 1);
          tasks.value = tmpArray;
        }

        this.tasks_total = tasks.value.length;

        this.updateTaskDoneCounter();
      } catch (error) {
        console.error('Error deleting tasks:', error);
      }
    }
  }
};
</script>

<style>
.counter-chip {
  margin-left: 10px;
  border-radius: 50px;
  background-color: blue !important;
  font-weight: 600;
}

.chip-text {
  margin-right: 5px;
  color: white;
}

.chip-number {
  border-radius: 50%;
  width: 28px;
  height: 28px;
  padding: 5px;
  background-color: white;
  text-align: center;
  color: blue;
}

.btn-style {
  background-color: red !important;
  color: white !important;
  margin-left: 10px;
  transition: background-color 0.3s ease !important;
}

.btn-style:hover {
  background-color: #ff6666 !important;
}

.btn-style:active {
  background-color: #cc0000 !important;
}

.list-container {
  margin-top: 70px;
  flex: 1; /* Fill remaining height */
  display: flex;
  flex-direction: column;
  overflow-y: auto; /* Add scrollbar if content exceeds height */
}

.list-item-content {
  display: flex;
  align-items: center; /* Align items vertically center */
  border: 1px solid #e0e0e0; /* Add border to each list item */
}

/* Center the checkbox */
.list-item-content .v-input--selection-controls {
  margin-right: auto;
}

.delete-icon-btn {
  margin-left: auto; /* Push the delete icon to the right */
}

.delete-icon-btn .v-btn__content {
  color: red; /* Set the color of the icon button content to red */
}

</style>
