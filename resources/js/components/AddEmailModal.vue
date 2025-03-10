<script setup>
import { ref, onMounted, watch } from "vue";
import { vAutoAnimate } from "@formkit/auto-animate";
import axios from "axios";

const baseUrl = import.meta.env.VITE_APP_URL;
const formModal = ref(false);
const emit = defineEmits(["close", "success"]);

const emails = ref({
    secret: "",
    mail: [],
});

onMounted(() => {
    const storedEmails = localStorage.getItem("emails");
    if (storedEmails) {
        emails.value = JSON.parse(storedEmails);
    }
});

watch(
    emails,
    (newEmails) => {
        localStorage.setItem("emails", JSON.stringify(newEmails));
    },
    { deep: true }
);

const successMessage = ref("");
const errorMessage = ref("");
const invalidKey = ref("");

async function handleSubmit() {
    invalidKey.value = "";
    try {
        const response = await axios.post(
            `${baseUrl}/api/email-queue/send`,
            emails.value
        );
        successMessage.value = response.data.message;
        emit("success", response.data.message);
        emit("close");
    } catch (error) {

        if (
            error.response &&
            error.response.data &&
            error.response.data.errors
        ) {
            errorMessage.value = error.response.data.errors;
        } else {
            invalidKey.value = error.response.data.error;
        }
        // console.log(errorMessage.value);
    }
}

function addEmail(newEmail) {
    emails.value.mail.push(newEmail);
}

const email = ref({
    subject: "",
    to: "",
    priority: "",
    content: "",
    attachment: [],
});

const detailErrors = ref({});

const errorsDetail = ref({});

const emailIndex = ref(null);

function detailEmail(index) {
    detailErrors.value =
        Object.keys(errorMessage.value)
            .filter(key => key.startsWith(`mail.${index}.`))
            .reduce((acc, key) => {
                acc[key] = errorMessage.value[key];
                return acc;
            }, {});
    console.log(detailErrors.value);
    email.value = JSON.parse(JSON.stringify(emails.value.mail[index]));
    emailIndex.value = index;
    console.log(emailIndex.value);
}

function newEmail() {
    detailErrors.value = {};
    email.value = {
        subject: "",
        to: "",
        priority: "low",
        content: "",
        attachment: [],
    };
    emails.value.secret = "";
    emailIndex.value = null;
}

function saveEdit() {
    emails.value.mail[emailIndex.value] = email.value;
    formModal.value = false;
}

function removeEmail(index) {
    emails.value.mail.splice(index, 1);
}

function addAttachment() {
    email.value.attachment.push("");
}

function removeAttachment(index) {
    email.value.attachment.splice(index, 1);
}
</script>

<template>
    <div>
        <div @click="emit('close')"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full max-h-full p-4 overflow-x-hidden overflow-y-auto bg-gray-700 bg-opacity-50 backdrop-blur-[2px] md:inset-0">
        </div>
        <!-- Extra Large Modal -->
        <div
            class="fixed z-50 w-full max-h-full -translate-x-1/2 -translate-y-1/2 bg-red-200 left-1/2 top-1/2 max-w-7xl">
            <!-- Modal content -->
            <form class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Add Email
                    </h3>
                    <button type="button" @click="emit('close')"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="extralarge-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Error message -->
                <div v-if="errorMessage && formModal === false"
                    class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                    role="alert">
                    An error occurred. Please check the card with red border for detail errors.
                </div>
                <!-- Error message invalid key -->
                <div v-if="invalidKey && formModal === false"
                    class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                    role="alert">
                    {{ invalidKey }}!
                </div>
                <!-- Modal body list card -->
                <!-- table -->
                <div v-show="!formModal" class="relative p-2 overflow-x-auto sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Subject
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    To
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Priority
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in emails.mail"
                                class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">
                                    {{ index + 1 }}
                                </td>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ item.subject }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ item.to }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ item.priority.charAt(0).toUpperCase() + item.priority.slice(1) }}
                                </td>
                                <td class="px-6 py-4">
                                    <a @click="
                                    (formModal = true),
                                    detailEmail(index),
                                    (emailIndex = index)
                                    "
                                        href="#"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                    <a @click="removeEmail(index)" href="#" class="font-medium text-red-700 dark:text-red-600 ms-3">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td @click="newEmail(), (formModal = true)" class="px-6 py-4 text-center text-green-700 border border-green-200 cursor-pointer" colspan="5">
                                    Add New Email
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table -->
                <!-- Modal body form -->
                <div v-show="formModal" class="flex flex-col p-4 md:p-5 gap-x-5 gap-y-4 max-h-[70vh] overflow-auto">
                    <div>
                        <label for="subject"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject</label>
                        <input type="text" id="subject" v-model="email.subject"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Example" required />
                        <div v-if="detailErrors['mail.' + emailIndex + '.subject']">
                            <p v-for="subjectErr in detailErrors['mail.' + emailIndex + '.subject']"
                                class="text-sm text-red-700 dark:text-red-800">
                                {{ subjectErr.replace(`mail.${emailIndex}.`, '') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To</label>
                        <input type="email" id="first_name" v-model="email.to"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="name@example.com" required />
                        <div v-if="detailErrors['mail.' + emailIndex + '.to']">
                            <p v-for="toErr in detailErrors['mail.' + emailIndex + '.to']"
                                class="text-sm text-red-700 dark:text-red-800">
                                {{ toErr.replace(`mail.${emailIndex}.`, '') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select an
                            Priority</label>
                        <select id="countries" v-model="email.priority"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label for="secret"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Secret</label>
                        <input type="text" id="secret" v-model="emails.secret"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter secret" required />
                    </div>
                    <div>
                        <label for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Attachment</label>
                        <div class="flex flex-col gap-y-2" v-auto-animate>
                            <div v-for="(item, index) in email.attachment" :key="index"
                                class="flex gap-x-2">
                                <div class="w-full">
                                    <input type="text" v-model="email.attachment[index]"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Attachment URL..." required />
                                    <div v-if="detailErrors['mail.' + emailIndex + '.attachment.' + index]">
                                        <p v-for="attachmentErr in detailErrors['mail.' + emailIndex + '.attachment.' + index]"
                                            class="text-sm text-red-700 dark:text-red-800">
                                            {{ attachmentErr.replace(`mail.${emailIndex}.`, '') }}
                                        </p>
                                    </div>
                                </div>
                                <button @click="removeAttachment(index)" type="button"
                                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2.5 dark:bg-red-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                            </div>
                            <button type="button" @click="addAttachment" class="text-blue-600 hover:text-blue-800">
                                Add Attachment
                            </button>
                        </div>

                    </div>
                    <div class="col-span-2">
                        <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Content</label>
                        <textarea id="message" rows="4" v-model="email.content"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Write your thoughts here..."></textarea>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center p-4 space-x-3 border-t border-gray-200 rounded-b md:p-5 rtl:space-x-reverse dark:border-gray-600">
                    <!-- add new -->
                    <button data-modal-hide="extralarge-modal" type="submit" v-if="formModal && emailIndex === null"
                        @click="addEmail(email), (formModal = false)"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Add
                    </button>
                    <!-- edit changes -->
                    <button data-modal-hide="extralarge-modal" type="button" @click="saveEdit"
                        v-if="formModal && emailIndex !== null"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Save
                    </button>
                    <!-- submit email -->
                    <button data-modal-hide="extralarge-modal" type="button" @click="handleSubmit" v-if="!formModal"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Submit
                    </button>
                    <!-- cancel -->
                    <button data-modal-hide="extralarge-modal" type="button" @click="formModal = false" v-if="formModal"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
