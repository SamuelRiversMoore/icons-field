<template>
    <k-field :input="_uid" :counter="counterOptions" v-bind="$props" class="k-multiselect-field" @blur="blur">
        <icons-select ref="input" :id="_uid" v-bind="$props" theme="field" v-on="$listeners" type="multiselect" :value="value" />
    </k-field>
</template>

<script>
import IconsSelect from "../inputs/icons-select.vue";

export default {
    extends: "k-multiselect-field",
    components: { IconsSelect },
    props: {
        value: {
            type: String,
            default: ""
        }
    }
};
</script>

<style lang="scss">
@import "../assets/css/styles.scss";
@import "./samrm/icons-field/css/fork-awesome.min.css";
</style>
