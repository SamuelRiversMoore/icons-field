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

			<div class="k-multiselect-options">
				<k-dropdown-item v-for="option in filtered" :key="option.value" @click.stop.native="select(option)" @keydown.native.enter.prevent="select(option)" @keydown.native.space.prevent="select(option)">
					<span v-if="option.value" class="dropdown-item-icon fa" :class="'fa-' + option.value"></span>
					<span v-html="option.display" />
					<span class="k-multiselect-value" v-html="option.info" />
				</k-dropdown-item>
			</div>
		</k-dropdown-content>
	</div>
</template>

<script>
// import "@/helpers/regex.js";
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
			state: null,
			q: null
		};
	},
	computed: {
		filtered() {
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
					return option.text.match(regex) || option.value.match(regex);
				})
				.map(option => {
					return {
						...option,
						display: option.text.replace(regex, "<b>$1</b>"),
						info: option.value.replace(regex, "<b>$1</b>")
					};
				});
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
			console.warn(this.state.value);
			this.$emit("input", this.state.value);
		},
		// onInvalid() {
		// 	this.$emit("invalid", this.$v.$invalid, this.$v);
		// },
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
		select(option) {
			if (this.isSelected(option)) {
				//this.remove(option);
			} else {
				this.state = option;
				this.close();
				this.onInput();
			}
		}
	}
	// validations() {
	// 	return {
	// 		state: {
	// 			required: this.required ? required : true
	// 		}
	// 	};
	// }
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
// .k-multiselect-input .k-sortable-ghost {
// 	background: $color-focus;
// }
// .k-multiselect-input .k-dropdown-content {
// 	width: 100%;
// }
// .k-multiselect-search {
// 	margin-top: 0 !important;
// 	color: $color-white;
// 	background: $color-dark;
// 	border-bottom: 1px dashed rgba($color-white, 0.2);
// 	> .k-button-text {
// 		flex: 1;
// 	}
// 	input {
// 		width: 100%;
// 		color: $color-white;
// 		background: none;
// 		border: none;
// 		outline: none;
// 		padding: 0.25rem 0;
// 		font: inherit;
// 	}
// }
// .k-multiselect-options {
// 	position: relative;
// 	max-height: 240px;
// 	overflow-y: scroll;
// 	padding: 0.5rem 0;
// }
// .k-multiselect-option {
// 	position: relative;
// 	&.selected {
// 		color: $color-positive-on-dark;
// 	}
// 	&.disabled:not(.selected) .k-icon {
// 		opacity: 0;
// 	}
// 	b {
// 		color: $color-focus-on-dark;
// 		font-weight: 700;
// 	}
// }
// .k-multiselect-value {
// 	color: $color-light-grey;
// 	margin-left: 0.25rem;
// 	&::before {
// 		content: " (";
// 	}
// 	&::after {
// 		content: ")";
// 	}
// }
// .k-multiselect-input[data-layout="list"] .k-tag {
// 	width: 100%;
// 	margin-right: 0 !important;
// }
</style>
