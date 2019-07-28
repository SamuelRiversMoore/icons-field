<template>
	<div element="k-dropdown" class="k-multiselect-input" @click.stop="onClick">
		<k-input theme="field" icon="angle-down" class="icons-input">
			<div v-if="state" @click.stop="onClick">
				<span v-if="state.value" class="dropdown-header-icon fa" :class="'fa-' + state.value"></span>
				<span>{{ state.value }}</span>
			</div>
		</k-input>

		<k-dropdown-content ref="dropdown" @open="onOpen" @close="q = null">
			<k-dropdown-item v-if="search" icon="search" class="k-multiselect-search">
				<input ref="search" v-model="q" />
			</k-dropdown-item>

			<div class="k-multiselect-options" @scroll="onScroll">
				<template v-if="sliced_options.length">
					<k-dropdown-item v-for="option in sliced_options" :key="option.value" @click.stop.native="select(option)" @keydown.native.enter.prevent="select(option)" @keydown.native.space.prevent="select(option)">
						<span v-if="option.value" class="dropdown-item-icon fa" :class="'fa-' + option.value"></span>
						<span v-html="option.display" />
						<span class="k-multiselect-value" v-html="option.info" />
					</k-dropdown-item>
				</template>
				<template v-else>
					<div class="k-dropdown-item no-result">
						<i>No result for "{{ q }}"</i>
					</div>
				</template>
			</div>
		</k-dropdown-content>
	</div>
</template>

<script>
import { required, minLength, maxLength } from "vuelidate/lib/validators";
export default {
	inheritAttrs: false,
	props: {
		disabled: Boolean,
		id: [Number, String],
		max: Number,
		min: Number,
		layout: String,
		options: {
			type: Array,
			default() {
				return [];
			}
		},
		required: Boolean,
		search: Boolean,
		separator: {
			type: String,
			default: ","
		},
		sort: Boolean,
		value: {
			type: String,
			required: true,
			default: ""
		}
	},
	data() {
		return {
			items_length: 100,
			state: null,
			q: null
		};
	},
	computed: {
		filtered_options() {
			if (this.q === null) {
				return this.options.map(option => ({
					...option,
					display: option.text,
					info: option.value
				}));
			}
			const regex = new RegExp(`(${RegExp.escape(this.q)})`, "ig");
			return this.options
				.filter(option => {
					return option.text.match(regex) || option.value.match(regex) || option.aliases.match(regex);
				})
				.map(option => {
					return {
						...option,
						display: option.text.replace(regex, "<b>$1</b>"),
						info: option.value.replace(regex, "<b>$1</b>")
					};
				});
		},
		sliced_options() {
			return this.filtered_options.slice(0, this.items_length);
		}
	},
	watch: {
		value: {
			handler(value) {
				this.state = value ? this.options.find(option => option.value == value) : null;
				//this.onInvalid();
			},
			immediate: true
		}
	},
	mounted() {
		//this.onInvalid();
		this.$events.$on("click", this.close);
		this.$events.$on("keydown.cmd.s", this.close);
		this.$events.$on("keydown.esc", this.escape);
	},
	destroyed() {
		this.$events.$off("click", this.close);
		this.$events.$off("keydown.cmd.s", this.close);
		this.$events.$off("keydown.esc", this.escape);
	},
	methods: {
		blur(e) {
			if (e.explicitOriginalTarget.classList && !e.explicitOriginalTarget.classList.contains("k-dropdown-item")) {
				this.close();
			}
		},
		close() {
			this.$refs.dropdown.close();
			this.q = null;
			this.$el.focus();
		},
		escape() {
			if (this.q) {
				this.q = null;
				return;
			}
			this.close();
		},
		focus() {
			this.$refs.dropdown.open();
		},
		isSelected(option) {
			return this.value == option;
		},
		navigate(direction) {
			let current = document.activeElement;
			switch (direction) {
				case "prev":
					if (current && current.previousSibling) {
						current.previousSibling.focus();
					}
					break;
				case "next":
					if (current && current.nextSibling) {
						current.nextSibling.focus();
					}
					break;
			}
		},
		onInput() {
			this.$emit("input", this.state.value);
		},
		onOpen() {
			this.$nextTick(() => {
				if (this.$refs.search) {
					this.$refs.search.focus();
				}
			});
		},
		onClick() {
			this.$refs.dropdown.toggle();
		},
		onScroll({ target: { scrollTop, clientHeight, scrollHeight } }) {
			if (scrollTop + clientHeight >= scrollHeight - 26 && this.items_length < this.filtered_options.length) {
				this.items_length += 50;
			}
		},
		select(option) {
			this.state = option;
			this.close();
			this.onInput();
		}
	}
};
</script>

<style lang="scss">
.icons-input {
	display: flex;
	flex-wrap: wrap;
	position: relative;
	padding: 0.25rem;
	padding-right: 0;
	min-height: 2.25rem;
	line-height: 1;
	width: 100%;
	cursor: pointer;
}
.dropdown-header-icon {
	margin: 0.5em;
}
.dropdown-item-icon {
	margin-right: 0.5em;
}
.k-dropdown-item.no-result {
	opacity: 0.5;
}
</style>
