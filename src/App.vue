<template>
	<div id="content" class="app-myshares">
		<AppNavigation>
			<ul>
				<AppNavigationItem v-for="share in shares"
					:key="share.id"
					:title="share.file_target ? share.file_target : t('myshares', 'New share')"
					:class="{active: currentShareId === share.id}"
					@click="openShare(share)">
					<template slot="actions">
						<ActionButton v-if="share.id === -1"
							icon="icon-close"
							@click="cancelNewShare(share)">
							{{ t('myshares', 'Cancel share creation') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-delete"
							@click="deleteShare(share)">
							{{ t('myshares', 'Delete share') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>
		<AppContent><br /><br />
			<div v-if="currentShare" style="padding:10px;text-align:right">
				Type: <span v-html="currentShare.item_type"></span><br />
				Target: <a v-bind:href="'/index.php/s/' + currentShare.token" target="_blank"><span v-html="currentShare.file_target"></span></a><br />
				Token: <input ref="token"
					v-model="currentShare.token"
					:disabled="updating"
					style="width:200px" /><br />
				<input type="button"
					class="primary"
					:value="t('myshares', 'Save')"
					:disabled="updating || !savePossible"
					@click="saveShare">
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('myshares', '..........') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
	},
	data() {
		return {
			shares: [],
			currentShareId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected share object
		 * @returns {Object|null}
		 */
		currentShare() {
			if (this.currentShareId === null) {
				return null
			}
			return this.shares.find((share) => share.id === this.currentShareId)
		},

		/**
		 * Returns true if a share is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentShare && this.currentShare.title !== ''
		},
	},
	/**
	 * Fetch list of shares when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/myshares/shares'))
			this.shares = response.data
		} catch (e) {
			console.error(e)
			showError(t('myshares', 'Could not fetch shares'))
		}
		this.loading = false
	},

	methods: {
		/**
		 * Create a new share and focus the share content field automatically
		 * @param {Object} share Share object
		 */
		openShare(share) {
			if (this.updating) {
				return
			}
			this.currentShareId = share.id
			this.$nextTick(() => {
				this.$refs.token.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new share or save
		 */
		saveShare() {
			if (this.currentShareId === -1) {
				this.createShare(this.currentShare)
			} else {
				this.updateShare(this.currentShare)
			}
		},
		/**
		 * Create a new share and focus the share content field automatically
		 * The share is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newShare() {
			if (this.currentShareId !== -1) {
				this.currentShareId = -1
				this.shares.push({
					id: -1,
					title: '',
					content: '',
				})
				this.$nextTick(() => {
					this.$refs.title.focus()
				})
			}
		},
		/**
		 * Abort creating a new share
		 */
		cancelNewShare() {
			this.shares.splice(this.shares.findIndex((share) => share.id === -1), 1)
			this.currentShareId = null
		},
		/**
		 * Create a new share by sending the information to the server
		 * @param {Object} share Share object
		 */
		async createShare(share) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/myshares/shares'), share)
				const index = this.shares.findIndex((match) => match.id === this.currentShareId)
				this.$set(this.shares, index, response.data)
				this.currentShareId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('myshares', 'Could not create the share'))
			}
			this.updating = false
		},
		/**
		 * Update an existing share on the server
		 * @param {Object} share Share object
		 */
		async updateShare(share) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/myshares/shares/${share.id}`), share)
			} catch (e) {
				console.error(e)
				showError(t('myshares', 'Could not update the share'))
			}
			this.updating = false
		},
		/**
		 * Delete a share, remove it from the frontend and show a hint
		 * @param {Object} share Share object
		 */
		async deleteShare(share) {
			try {
				await axios.delete(generateUrl(`/apps/myshares/shares/${share.id}`))
				this.shares.splice(this.shares.indexOf(share), 1)
				if (this.currentShareId === share.id) {
					this.currentShareId = null
				}
				showSuccess(t('myshares', 'Share deleted'))
			} catch (e) {
				console.error(e)
				showError(t('myshares', 'Could not delete the share'))
			}
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}
</style>
